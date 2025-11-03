<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class Customers extends Component
{
    public function render()
    {
        return view('livewire.page.customers');
    }
}
