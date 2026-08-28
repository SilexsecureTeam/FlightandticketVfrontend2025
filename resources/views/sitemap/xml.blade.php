{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach ($urls as $url)
    <url>
        <loc>{{ $url['loc'] }}</loc>
@if (!empty($url['lastmod']))
        <lastmod>{{ $url['lastmod'] }}</lastmod>
@endif
@if (!empty($url['title']))
        <!-- seo:title: {{ str_replace(['--', "\n", "\r"], ['—', ' ', ' '], $url['title']) }} -->
@endif
@if (!empty($url['description']))
        <!-- seo:description: {{ str_replace(['--', "\n", "\r"], ['—', ' ', ' '], $url['description']) }} -->
@endif
@if (!empty($url['keywords']))
        <!-- seo:keywords: {{ str_replace(['--', "\n", "\r"], ['—', ' ', ' '], $url['keywords']) }} -->
@endif
@if (!empty($url['canonical']))
        <!-- seo:canonical: {{ $url['canonical'] }} -->
@endif
@if (!empty($url['image']))
        <image:image>
            <image:loc>{{ $url['image'] }}</image:loc>
@if (!empty($url['title']))
            <image:title>{{ $url['title'] }}</image:title>
@endif
@if (!empty($url['description']))
            <image:caption>{{ $url['description'] }}</image:caption>
@endif
        </image:image>
@endif
    </url>
@endforeach
</urlset>
