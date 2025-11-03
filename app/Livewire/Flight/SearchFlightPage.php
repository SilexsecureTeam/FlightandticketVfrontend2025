<?php

namespace App\Livewire\Flight;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class SearchFlightPage extends Component
{
    public function render()
    {
        return view('livewire.flight.search-flight-page');
    }
}