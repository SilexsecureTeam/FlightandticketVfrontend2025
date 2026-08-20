<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'content',
        'status',
        'menu_location',
        'menu_order',
        'custom_url',
        'is_system',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'canonical_url',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'menu_order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeInTopMenu(Builder $query): Builder
    {
        return $query->published()
            ->whereIn('menu_location', ['top', 'both'])
            ->orderBy('menu_order')
            ->orderBy('title');
    }

    public function scopeInFooterMenu(Builder $query): Builder
    {
        return $query->published()
            ->whereIn('menu_location', ['footer', 'both'])
            ->orderBy('menu_order')
            ->orderBy('title');
    }

    public function getUrlAttribute(): string
    {
        if (!empty($this->custom_url)) {
            return $this->custom_url;
        }

        return route('cms.page', $this->slug);
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

        return Str::limit(strip_tags((string) $this->content), 160);
    }

    public function seoCanonical(): string
    {
        return $this->canonical_url ?: url($this->url);
    }
}
