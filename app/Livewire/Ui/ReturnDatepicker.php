<?php

namespace App\Livewire\Ui;

use Carbon\Carbon;
use Livewire\Component;

class ReturnDatepicker extends Component
{
    public $label;
    public $selectedDate;
    public $formattedDate;
    public $minDate;

    // #[On('dateUpdated')]

    public function mount($label, $minDate = null)
    {
        $this->label = $label;
        $this->minDate = $minDate ? Carbon::parse($minDate)->format('Y-m-d') : null;
    }
    public function dateSelected($date)
    {
        // Ensure the date is formatted as YYYY-MM-DD
        $this->selectedDate = Carbon::parse($date)->format('Y-m-d'); // Format to YYYY-MM-DD
        $this->formattedDate = Carbon::parse($date)->format('D, d M Y'); // Optional formatted display for UI

        // Dispatch the event to the parent component
        $this->dispatch('dateUpdated', [
            'label' => $this->label,
            'selectedDate' => $this->selectedDate
        ]);
    }
    public function render()
    {
        return view('livewire.ui.return-datepicker');
    }
}
