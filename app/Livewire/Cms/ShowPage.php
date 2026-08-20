<?php

namespace App\Livewire\Cms;

use App\Models\Page;
use Livewire\Component;

class ShowPage extends Component
{
    public Page $page;

    public function mount(string $slug)
    {
        $this->page = Page::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        if (!empty($this->page->custom_url) && $this->page->custom_url !== request()->getRequestUri()) {
            // Keep content pages reachable even if custom_url is set for menus.
        }
    }

    public function render()
    {
        $content = (string) ($this->page->content ?? '');
        $content = preg_replace('#https?://[^"\']+/storage/cms/#i', '/uploads/cms/', $content) ?? $content;
        $content = str_replace('/storage/cms/', '/uploads/cms/', $content);

        return view('livewire.cms.show-page', [
            'pageContent' => $content,
        ])
            ->layout('components.layouts.app', [
                'seoTitle' => $this->page->seoTitle(),
                'seoDescription' => $this->page->seoDescription(),
                'seoKeywords' => $this->page->meta_keywords,
                'seoCanonical' => $this->page->seoCanonical(),
                'seoImage' => $this->page->og_image,
            ]);
    }
}
