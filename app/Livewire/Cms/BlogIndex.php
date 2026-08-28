<?php

namespace App\Livewire\Cms;

use App\Models\BlogPost;
use Livewire\Component;
use Livewire\WithPagination;

class BlogIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $posts = BlogPost::query()
            ->published()
            ->latest('published_at')
            ->paginate(9);

        return view('livewire.cms.blog-index', [
            'posts' => $posts,
        ])->layout('components.layouts.app', [
            'seoTitle' => config('cms-seo.blog_index.meta_title'),
            'seoDescription' => config('cms-seo.blog_index.meta_description'),
            'seoKeywords' => config('cms-seo.blog_index.meta_keywords'),
            'seoCanonical' => config('cms-seo.blog_index.canonical_url'),
        ]);
    }
}
