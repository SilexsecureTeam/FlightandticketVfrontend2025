<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Page;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = $this->canonicalBaseUrl();
        $urls = [];

        $urls[$baseUrl . '/'] = [
            'loc' => $baseUrl . '/',
            'lastmod' => null,
        ];

        $blogIndexLastmod = BlogPost::query()
            ->published()
            ->max('updated_at');

        $urls[$baseUrl . '/blog'] = [
            'loc' => $baseUrl . '/blog',
            'lastmod' => $this->formatLastmod($blogIndexLastmod),
        ];

        $pages = Page::query()
            ->published()
            ->orderBy('slug')
            ->get(['slug', 'custom_url', 'canonical_url', 'updated_at']);

        foreach ($pages as $page) {
            $loc = $this->resolvePageLoc($page, $baseUrl);

            if ($loc === null) {
                continue;
            }

            // Prefer explicit canonical when it is an absolute URL on this host
            if (!empty($page->canonical_url) && $this->isAbsoluteUrl($page->canonical_url)) {
                $canonical = $this->normalizeAbsoluteUrl($page->canonical_url, $baseUrl);
                if ($canonical !== null) {
                    $loc = $canonical;
                }
            }

            if (isset($urls[$loc])) {
                // Keep the newer lastmod if we already have this URL (e.g. homepage)
                $existing = $urls[$loc]['lastmod'];
                $incoming = $this->formatLastmod($page->updated_at);
                if ($incoming && (!$existing || $incoming > $existing)) {
                    $urls[$loc]['lastmod'] = $incoming;
                }
                continue;
            }

            $urls[$loc] = [
                'loc' => $loc,
                'lastmod' => $this->formatLastmod($page->updated_at),
            ];
        }

        $posts = BlogPost::query()
            ->published()
            ->orderBy('slug')
            ->get(['slug', 'canonical_url', 'updated_at', 'published_at']);

        foreach ($posts as $post) {
            $loc = $baseUrl . '/blog/' . rawurlencode($post->slug);

            if (!empty($post->canonical_url) && $this->isAbsoluteUrl($post->canonical_url)) {
                $canonical = $this->normalizeAbsoluteUrl($post->canonical_url, $baseUrl);
                if ($canonical !== null) {
                    $loc = $canonical;
                }
            }

            if (isset($urls[$loc])) {
                continue;
            }

            $lastmodSource = $post->updated_at ?: $post->published_at;

            $urls[$loc] = [
                'loc' => $loc,
                'lastmod' => $this->formatLastmod($lastmodSource),
            ];
        }

        $xml = view('sitemap.xml', [
            'urls' => array_values($urls),
        ])->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Canonical production host from SEO settings in the codebase.
     * Uses APP_URL when it is a real non-local host; otherwise the established production domain.
     */
    protected function canonicalBaseUrl(): string
    {
        $configured = rtrim((string) config('app.url'), '/');
        $host = parse_url($configured, PHP_URL_HOST);
        $scheme = parse_url($configured, PHP_URL_SCHEME) ?: 'https';

        $localHosts = ['localhost', '127.0.0.1', '::1'];

        if (
            empty($host)
            || in_array($host, $localHosts, true)
            || str_ends_with($host, '.test')
            || str_ends_with($host, '.local')
            || str_ends_with($host, '.localhost')
        ) {
            // Established production canonical across layouts and transactional emails
            return 'https://flightandticketing.com';
        }

        // Sitemap locs should use HTTPS when that is the site's canonical protocol
        return 'https://' . $host;
    }

    protected function resolvePageLoc(Page $page, string $baseUrl): ?string
    {
        // Menu-only custom paths that are the homepage should not create a second entry
        if (!empty($page->custom_url)) {
            $custom = trim($page->custom_url);

            // External absolute custom URLs are not this site's indexable CMS URLs
            if ($this->isAbsoluteUrl($custom)) {
                $normalized = $this->normalizeAbsoluteUrl($custom, $baseUrl);
                // Only include if same host; otherwise skip (off-site)
                return $normalized;
            }

            // Internal custom path (e.g. "/") — map to absolute path on this site
            if ($custom === '/' || $custom === '') {
                return $baseUrl . '/';
            }

            // Other internal custom paths: use them as the public URL
            return $baseUrl . '/' . ltrim($custom, '/');
        }

        return $baseUrl . '/page/' . rawurlencode($page->slug);
    }

    protected function isAbsoluteUrl(string $url): bool
    {
        return (bool) preg_match('#^https?://#i', $url);
    }

    protected function normalizeAbsoluteUrl(string $url, string $baseUrl): ?string
    {
        $parts = parse_url($url);
        $baseParts = parse_url($baseUrl);

        if (empty($parts['host']) || empty($baseParts['host'])) {
            return null;
        }

        // Only keep URLs on the canonical host
        if (strcasecmp($parts['host'], $baseParts['host']) !== 0) {
            return null;
        }

        $path = $parts['path'] ?? '/';
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        // Drop query strings and fragments from sitemap locs
        return 'https://' . $baseParts['host'] . ($path === '' ? '/' : $path);
    }

    protected function formatLastmod(mixed $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
