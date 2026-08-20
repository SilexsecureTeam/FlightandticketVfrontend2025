<?php

namespace App\Livewire\Partials;

use App\Models\Page;
use Livewire\Component;

class SiteFooter extends Component
{
    public function render()
    {
        $footerPages = Page::inFooterMenu()->get();

        $companySlugs = ['about-us', 'faq', 'contact-us'];
        $legalSlugs = ['terms-and-conditions', 'privacy-policy', 'cookies-policy'];

        $companyPages = $footerPages->filter(fn (Page $page) => in_array($page->slug, $companySlugs, true))->values();
        $legalPages = $footerPages->filter(fn (Page $page) => in_array($page->slug, $legalSlugs, true))->values();
        $otherPages = $footerPages
            ->reject(fn (Page $page) => in_array($page->slug, array_merge($companySlugs, $legalSlugs), true))
            ->values();

        return view('livewire.partials.site-footer', [
            'companyPages' => $companyPages,
            'legalPages' => $legalPages,
            'otherPages' => $otherPages,
        ]);
    }
}
