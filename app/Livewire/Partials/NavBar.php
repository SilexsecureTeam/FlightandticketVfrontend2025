<?php

namespace App\Livewire\Partials;

use App\Models\Page;
use Livewire\Component;

class NavBar extends Component
{
    public function render()
    {
        $topPages = Page::inTopMenu()->get();

        return view('livewire.partials.nav-bar', [
            'topPages' => $topPages,
        ]);
    }
}
