<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Landing extends Component
{
    public function render()
    {
        return view('livewire.landing')->layout('components.layouts.app', [
            'seoTitle' => config('cms-seo.homepage.meta_title'),
            'seoDescription' => config('cms-seo.homepage.meta_description'),
            'seoKeywords' => config('cms-seo.homepage.meta_keywords'),
            'seoCanonical' => config('cms-seo.homepage.canonical_url'),
        ]);
    }
}
