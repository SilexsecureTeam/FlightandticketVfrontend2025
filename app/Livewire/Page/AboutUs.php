<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\Page;

class AboutUs extends Component
{
    public $page;

    public function mount()
    {
        $this->page = Page::firstOrCreate(
            ['slug' => 'about-us'],
            ['title' => 'About Us', 'content' => '<p>About Us content goes here.</p>']
        );
    }

    public function render()
    {
        return view('livewire.page.about-us')->layout('components.layouts.app');
    }
}
