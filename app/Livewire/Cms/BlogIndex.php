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
            'seoTitle' => 'Blog | Flightandticketing',
            'seoDescription' => 'Travel tips, deals, and news from Flightandticketing by Mymore Travels.',
            'seoKeywords' => 'travel blog, flight tips, travel deals',
            'seoCanonical' => url('/blog'),
        ]);
    }
}
