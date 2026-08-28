<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SitemapService
{
    public function build(): array
    {
        $baseUrl = $this->canonicalBaseUrl();
        $urls = [];

        $this->addUrl($urls, [
            'loc' => $baseUrl . '/',
            'lastmod' => Page::query()->published()->max('updated_at'),
            'title' => config('cms-seo.homepage.meta_title'),
            'description' => config('cms-seo.homepage.meta_description'),
            'keywords' => config('cms-seo.homepage.meta_keywords'),
            'canonical' => config('cms-seo.homepage.canonical_url'),
        ]);

        $blogIndexLastmod = BlogPost::query()->published()->max('updated_at');

        $this->addUrl($urls, [
            'loc' => $baseUrl . '/blog',
            'lastmod' => $blogIndexLastmod,
            'title' => config('cms-seo.blog_index.meta_title'),
            'description' => config('cms-seo.blog_index.meta_description'),
            'keywords' => config('cms-seo.blog_index.meta_keywords'),
            'canonical' => config('cms-seo.blog_index.canonical_url'),
        ]);

        $pages = Page::query()
            ->published()
            ->orderBy('slug')
            ->get();

        foreach ($pages as $page) {
            $loc = $this->resolvePageLoc($page, $baseUrl);

            if ($loc === null) {
                continue;
            }

            if (!empty($page->canonical_url) && $this->isAbsoluteUrl($page->canonical_url)) {
                $canonical = $this->normalizeAbsoluteUrl($page->canonical_url, $baseUrl);
                if ($canonical !== null) {
                    $loc = $canonical;
                }
            }

            $this->addUrl($urls, [
                'loc' => $loc,
                'lastmod' => $page->updated_at,
                'title' => $page->seoTitle(),
                'description' => $page->seoDescription(),
                'keywords' => $page->meta_keywords,
                'canonical' => $page->seoCanonical(),
                'image' => $this->resolveImageUrl($page->og_image, $baseUrl),
            ]);
        }

        $posts = BlogPost::query()
            ->published()
            ->orderBy('slug')
            ->get();

        foreach ($posts as $post) {
            $loc = $baseUrl . '/blog/' . rawurlencode($post->slug);

            if (!empty($post->canonical_url) && $this->isAbsoluteUrl($post->canonical_url)) {
                $canonical = $this->normalizeAbsoluteUrl($post->canonical_url, $baseUrl);
                if ($canonical !== null) {
                    $loc = $canonical;
                }
            }

            $this->addUrl($urls, [
                'loc' => $loc,
                'lastmod' => $post->updated_at ?: $post->published_at,
                'title' => $post->seoTitle(),
                'description' => $post->seoDescription(),
                'keywords' => $post->meta_keywords,
                'canonical' => $post->seoCanonical(),
                'image' => $post->seoImage() ?: $this->resolveImageUrl($post->featured_image, $baseUrl),
            ]);
        }

        return array_values($urls);
    }

    protected function addUrl(array &$urls, array $entry): void
    {
        $loc = $entry['loc'];

        $normalized = [
            'loc' => $loc,
            'lastmod' => $this->formatLastmod($entry['lastmod'] ?? null),
            'title' => $this->cleanSeoText($entry['title'] ?? null),
            'description' => $this->cleanSeoText($entry['description'] ?? null),
            'keywords' => $this->cleanSeoText($entry['keywords'] ?? null),
            'canonical' => $this->cleanSeoText($entry['canonical'] ?? $loc),
            'image' => $entry['image'] ?? null,
        ];

        if (isset($urls[$loc])) {
            $existing = $urls[$loc];
            if ($normalized['lastmod'] && (!$existing['lastmod'] || $normalized['lastmod'] > $existing['lastmod'])) {
                $urls[$loc]['lastmod'] = $normalized['lastmod'];
            }

            foreach (['title', 'description', 'keywords', 'canonical', 'image'] as $field) {
                if (empty($existing[$field]) && !empty($normalized[$field])) {
                    $urls[$loc][$field] = $normalized[$field];
                }
            }

            return;
        }

        $urls[$loc] = $normalized;
    }

    public function canonicalBaseUrl(): string
    {
        $configuredDomain = rtrim((string) config('cms-seo.site.domain'), '/');

        if ($configuredDomain !== '' && $this->isAbsoluteUrl($configuredDomain)) {
            return rtrim($configuredDomain, '/');
        }

        $configured = rtrim((string) config('app.url'), '/');
        $host = parse_url($configured, PHP_URL_HOST);

        $localHosts = ['localhost', '127.0.0.1', '::1'];

        if (
            empty($host)
            || in_array($host, $localHosts, true)
            || str_ends_with($host, '.test')
            || str_ends_with($host, '.local')
            || str_ends_with($host, '.localhost')
        ) {
            return 'https://flightandticketing.com';
        }

        return 'https://' . $host;
    }

    protected function resolvePageLoc(Page $page, string $baseUrl): ?string
    {
        if (!empty($page->custom_url)) {
            $custom = trim($page->custom_url);

            if ($this->isAbsoluteUrl($custom)) {
                return $this->normalizeAbsoluteUrl($custom, $baseUrl);
            }

            if ($custom === '/' || $custom === '') {
                return $baseUrl . '/';
            }

            return $baseUrl . '/' . ltrim($custom, '/');
        }

        return $baseUrl . '/page/' . rawurlencode($page->slug);
    }

    protected function resolveImageUrl(?string $image, string $baseUrl): ?string
    {
        if (empty($image)) {
            return null;
        }

        if ($this->isAbsoluteUrl($image)) {
            return $this->normalizeAbsoluteUrl($image, $baseUrl) ?? $image;
        }

        if (Str::startsWith($image, '/')) {
            return $baseUrl . $image;
        }

        return $baseUrl . '/' . ltrim($image, '/');
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

        if (strcasecmp($parts['host'], $baseParts['host']) !== 0) {
            return null;
        }

        $path = $parts['path'] ?? '/';
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

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

    protected function cleanSeoText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(preg_replace('/\s+/u', ' ', strip_tags($value)) ?? '');

        return $value !== '' ? $value : null;
    }
}
