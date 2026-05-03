<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\Page;

class TermsAndConditions extends Component
{
    public $page;

    public function mount()
    {
        $this->page = Page::firstOrCreate(
            ['slug' => 'terms-and-conditions'],
            ['title' => 'Terms and Conditions', 'content' => '<p>Terms and Conditions content goes here.</p>']
        );
    }

    public function render()
    {
        return view('livewire.page.terms-and-conditions')->layout('components.layouts.app');
    }
}
