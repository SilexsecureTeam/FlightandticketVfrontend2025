<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'favicon',
    ];

    public static function current(): self
    {
        return Cache::rememberForever('site_settings.current', function () {
            $setting = static::query()->first();

            if ($setting) {
                return $setting;
            }

            return static::query()->create([
                'meta_title' => config('cms-seo.site.default_title'),
                'meta_description' => config('cms-seo.site.default_description'),
                'meta_keywords' => config('cms-seo.site.default_keywords'),
                'favicon' => null,
            ]);
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('site_settings.current');
    }

    protected static function booted(): void
    {
        static::saved(static function () {
            static::clearCache();
        });

        static::deleted(static function () {
            static::clearCache();
        });
    }

    public function title(): string
    {
        return $this->meta_title
            ?: (string) config('cms-seo.site.default_title');
    }

    public function description(): string
    {
        return $this->meta_description
            ?: (string) config('cms-seo.site.default_description');
    }

    public function keywords(): string
    {
        return $this->meta_keywords
            ?: (string) config('cms-seo.site.default_keywords');
    }

    public function faviconUrl(): ?string
    {
        if (empty($this->favicon)) {
            return null;
        }

        $path = (string) $this->favicon;

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $relative = '/' . ltrim($path, '/');
        $version = $this->updated_at?->timestamp ?? time();

        return $relative . '?v=' . $version;
    }

    public function faviconMimeType(): string
    {
        $extension = strtolower(pathinfo((string) $this->favicon, PATHINFO_EXTENSION));

        return match ($extension) {
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/png',
        };
    }

    /**
     * Publish a root /favicon.ico so browsers that ignore <link rel="icon"> still show the tab icon.
     */
    public function publishRootFavicon(): void
    {
        $rootIco = public_path('favicon.ico');
        $rootPng = public_path('favicon.png');

        if (empty($this->favicon)) {
            foreach ([$rootIco, $rootPng] as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }

            return;
        }

        $source = public_path(ltrim((string) $this->favicon, '/'));
        if (!is_file($source)) {
            return;
        }

        @copy($source, $rootIco);
        @copy($source, $rootPng);
    }
}
