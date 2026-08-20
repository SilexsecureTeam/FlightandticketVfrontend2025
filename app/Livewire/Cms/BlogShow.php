<?php

namespace App\Livewire\Cms;

use App\Models\BlogPost;
use Livewire\Component;

class BlogShow extends Component
{
    public BlogPost $post;

    public function mount(string $slug)
    {
        $this->post = BlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.cms.blog-show')
            ->layout('components.layouts.app', [
                'seoTitle' => $this->post->seoTitle(),
                'seoDescription' => $this->post->seoDescription(),
                'seoKeywords' => $this->post->meta_keywords,
                'seoCanonical' => $this->post->seoCanonical(),
                'seoImage' => $this->post->seoImage(),
            ]);
    }
}
