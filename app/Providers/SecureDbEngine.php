<?php

namespace App\Providers;

use Illuminate\Database\Connection;
use Illuminate\Database\MariaDbConnection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\ServiceProvider;

/**
 * Register once in bootstrap/providers.php.
 * Reads decrypt any Secure DB ciphertext. Writes encrypt a column only when that table is encrypted.
 */
class SecureDbEngine extends ServiceProvider
{
    public static ?self $current = null;

    private string $dataKey = '';

    private string $appKey = '';

    private string $gatewayUrl = '';

    private ?array $tables = null;

    private float $catalogLoadedAt = 0;

    private bool $catalogWarned = false;

    public function register(): void
    {
        self::$current = $this;
        $this->dataKey = self::envValue('SECURE_DB_DATA_KEY');
        $this->appKey = self::envValue('SECURE_DB_APP_KEY');
        $this->gatewayUrl = rtrim(self::envValue('SECURE_DB_GATEWAY_URL'), '/');

        Connection::resolverFor('mysql', function ($connection, $database, $prefix, $config) {
            return new SecureDbMySqlConnection($connection, $database, $prefix, $config);
        });

        Connection::resolverFor('mariadb', function ($connection, $database, $prefix, $config) {
            return new SecureDbMariaDbConnection($connection, $database, $prefix, $config);
        });
    }

    public function decryptRecord(object|array $row): object|array
    {
        if ($this->dataKey === '') {
            return $row;
        }

        foreach ($this->recordValues($row) as $field => $value) {
            if (! is_string($value) || ! $this->isEncrypted($value)) {
                continue;
            }
            $plain = $this->decrypt($value);
            if (is_array($row)) {
                $row[$field] = $plain;
            } else {
                $row->{$field} = $plain;
            }
        }

        return $row;
    }

    public function encryptBindings(string $query, array $bindings): array
    {
        if ($this->dataKey === '') {
            return $bindings;
        }

        $table = $this->tableFromWrite($query);
        $fields = $table ? $this->encryptedFields($table) : [];
        if ($fields === []) {
            return $bindings;
        }

        $columns = $this->columnsForWrite($query);
        foreach ($bindings as $index => $value) {
            $column = $columns[$index] ?? null;
            if (! is_int($index) || $column === null || ! isset($fields[strtolower($column)])) {
                continue;
            }
            if (! is_string($value) || $value === '' || $this->isEncrypted($value)) {
                continue;
            }
            $bindings[$index] = $this->encrypt($value);
        }

        return $bindings;
    }

    private function recordValues(object|array $row): array
    {
        return is_array($row) ? $row : get_object_vars($row);
    }

    private function encryptedFields(string $table): array
    {
        $this->loadCatalog();
        $meta = $this->tables[strtolower($table)] ?? null;
        if (! $meta || empty($meta['encrypted'])) {
            return [];
        }

        $fields = [];
        foreach ($meta['encrypted_fields'] ?? [] as $field) {
            $fields[strtolower((string) $field)] = true;
        }

        return $fields;
    }

    private function loadCatalog(): void
    {
        if ($this->tables !== null && (microtime(true) - $this->catalogLoadedAt) < 30) {
            return;
        }

        $this->catalogLoadedAt = microtime(true);
        $this->tables = [];
        if ($this->gatewayUrl === '' || $this->appKey === '') {
            return;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 3,
                'header' => "Accept: application/json\r\nX-Secure-DB-App-Key: {$this->appKey}\r\n",
                'ignore_errors' => true,
            ],
        ]);
        $body = @file_get_contents($this->gatewayUrl . '/api/secure-db/app/tables', false, $context);
        $payload = json_decode($body ?: '', true);
        if (! is_array($payload) || empty($payload['success'])) {
            $this->warnCatalog(is_array($payload) ? (string) ($payload['message'] ?? 'Secure DB could not load the encryption catalog.') : 'Secure DB could not load the encryption catalog.');

            return;
        }

        foreach ($payload['data']['tables'] ?? [] as $table) {
            if (! empty($table['name'])) {
                $this->tables[strtolower((string) $table['name'])] = $table;
            }
        }
    }

    private function warnCatalog(string $message): void
    {
        if ($this->catalogWarned) {
            return;
        }
        $this->catalogWarned = true;
        if (function_exists('logger')) {
            logger()->warning($message);
        }
    }

    private function tableFromWrite(string $query): ?string
    {
        if (! preg_match('/\b(?:insert(?:\s+ignore)?\s+into|update)\s+(?:[`"]?[A-Za-z0-9_]+[`"]?\.)?[`"]?([A-Za-z0-9_]+)[`"]?/i', $query, $match)) {
            return null;
        }

        return $match[1];
    }

    private function columnsForWrite(string $query): array
    {
        if (preg_match('/\binsert(?:\s+ignore)?\s+into\b.+\(([^)]+)\)\s*values\s*(.+)$/is', $query, $match)) {
            $columns = $this->columnNames($match[1]);
            $mapped = [];
            preg_match_all('/\(([^)]*)\)/', $match[2], $groups);
            foreach ($groups[1] as $group) {
                $count = substr_count($group, '?');
                for ($i = 0; $i < $count; $i++) {
                    $mapped[] = $columns[$i] ?? null;
                }
            }
            if (preg_match('/\bon\s+duplicate\s+key\s+update\s+(.+)$/is', $query, $duplicate)) {
                foreach ($this->assignmentColumns($duplicate[1]) as $column) {
                    $mapped[] = $column;
                }
            }

            return $mapped;
        }

        if (preg_match('/\bupdate\b.+\bset\s+(.*?)(?:\s+where\s+|\s+order\s+|\s+limit\s+|$)/is', $query, $match)) {
            return $this->assignmentColumns($match[1]);
        }

        return [];
    }

    private function columnNames(string $list): array
    {
        preg_match_all('/[`"]?([A-Za-z0-9_]+)[`"]?/', $list, $match);

        return $match[1];
    }

    private function assignmentColumns(string $clause): array
    {
        preg_match_all('/[`"]?([A-Za-z0-9_]+)[`"]?\s*=\s*\?/i', $clause, $match);

        return $match[1];
    }

    private function encrypt(string $plaintext): string
    {
        $key = $this->normalizeKey($this->dataKey);
        $iv = random_bytes(12);
        $tag = '';
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
        if ($ciphertext === false) {
            return $plaintext;
        }

        return base64_encode((string) json_encode([
            'algorithm' => 'aes-256-gcm',
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
            'ciphertext' => base64_encode($ciphertext),
        ]));
    }

    private function decrypt(string $encrypted): string
    {
        $payload = json_decode(base64_decode($encrypted, true) ?: '', true);
        if (! is_array($payload) || ! isset($payload['ciphertext'], $payload['iv'], $payload['tag'])) {
            return $encrypted;
        }

        $plaintext = openssl_decrypt(
            base64_decode((string) $payload['ciphertext']),
            'aes-256-gcm',
            $this->normalizeKey($this->dataKey),
            OPENSSL_RAW_DATA,
            base64_decode((string) $payload['iv']),
            base64_decode((string) $payload['tag'])
        );

        return $plaintext === false ? $encrypted : $plaintext;
    }

    private function isEncrypted(string $value): bool
    {
        if (strlen($value) < 16 || ! str_starts_with($value, 'eyJ')) {
            return false;
        }
        $payload = json_decode(base64_decode($value, true) ?: '', true);

        return is_array($payload) && isset($payload['ciphertext'], $payload['algorithm']);
    }

    private function normalizeKey(string $key): string
    {
        $decoded = base64_decode($key, true);
        $raw = $decoded !== false ? $decoded : $key;
        if (strlen($raw) < 32) {
            $raw = hash('sha256', $raw, true);
        }

        return substr($raw, 0, 32);
    }

    private static function envValue(string $name): string
    {
        foreach ([$_ENV[$name] ?? null, $_SERVER[$name] ?? null, getenv($name)] as $value) {
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }
        if (function_exists('env')) {
            $value = env($name);
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        self::loadDotEnv();

        return self::$dotenv[$name] ?? '';
    }

    private static array $dotenv = [];

    private static bool $dotenvLoaded = false;

    private static function loadDotEnv(): void
    {
        if (self::$dotenvLoaded) {
            return;
        }
        self::$dotenvLoaded = true;
        $path = function_exists('base_path') ? base_path('.env') : null;
        if (! is_string($path) || ! is_file($path)) {
            return;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            self::$dotenv[trim($key)] = trim($value, " \t\"'");
        }
    }
}

trait SecureDbConnectionBehavior
{
    public function select($query, $bindings = [], $useReadPdo = true)
    {
        $rows = parent::select($query, $bindings, $useReadPdo);
        $engine = SecureDbEngine::$current;
        if (! $engine) {
            return $rows;
        }
        foreach ($rows as $index => $row) {
            $rows[$index] = $engine->decryptRecord($row);
        }

        return $rows;
    }

    public function cursor($query, $bindings = [], $useReadPdo = true)
    {
        $engine = SecureDbEngine::$current;
        foreach (parent::cursor($query, $bindings, $useReadPdo) as $row) {
            yield $engine ? $engine->decryptRecord($row) : $row;
        }
    }

    public function insert($query, $bindings = [], $sequence = null)
    {
        $engine = SecureDbEngine::$current;
        if ($engine) {
            $bindings = $engine->encryptBindings($query, $bindings);
        }

        return parent::insert($query, $bindings, $sequence);
    }

    public function update($query, $bindings = [])
    {
        $engine = SecureDbEngine::$current;
        if ($engine) {
            $bindings = $engine->encryptBindings($query, $bindings);
        }

        return parent::update($query, $bindings);
    }
}

class SecureDbMySqlConnection extends MySqlConnection
{
    use SecureDbConnectionBehavior;
}

class SecureDbMariaDbConnection extends MariaDbConnection
{
    use SecureDbConnectionBehavior;
}
