<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
        'user_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'canonical_url',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function getUrlAttribute(): string
    {
        return route('blog.show', $this->slug);
    }

    public function featuredImageUrl(): ?string
    {
        if (empty($this->featured_image)) {
            return null;
        }

        $path = (string) $this->featured_image;

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, '/')) {
            return $path;
        }

        // New public uploads path: uploads/blog/filename.jpg
        if (Str::startsWith($path, 'uploads/')) {
            return '/' . ltrim($path, '/');
        }

        // Legacy storage path (blog/filename.jpg) — prefer public/uploads copy
        $filename = basename($path);
        if (is_file(public_path('uploads/blog/' . $filename))) {
            return '/uploads/blog/' . $filename;
        }

        return '/storage/' . ltrim($path, '/');
    }

    public function seoTitle(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function seoDescription(): string
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }

        if ($this->excerpt) {
            return $this->excerpt;
        }

        return Str::limit(strip_tags((string) $this->content), 160);
    }

    public function seoCanonical(): string
    {
        return $this->canonical_url ?: url($this->url);
    }

    public function seoImage(): ?string
    {
        if (!empty($this->og_image)) {
            $image = (string) $this->og_image;

            return Str::startsWith($image, ['http://', 'https://', '/'])
                ? $image
                : '/' . ltrim($image, '/');
        }

        return $this->featuredImageUrl();
    }
}
