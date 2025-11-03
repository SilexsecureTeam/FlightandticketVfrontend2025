<?php

namespace App\Livewire\Page;

use App\Models\Markup;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class PaymentSettings extends Component
{
    use LivewireAlert;
    public $mark_up;

    public function mount()
    {
        $mark_up = Markup::first();
        $this->mark_up = $mark_up->mark_up;
    }

    public function updatemarkup()
    {
        try {
            $this->validate([
                'mark_up' => 'required|string',
            ]);
    
            Markup::updateOrCreate(
                ['id' => 1], 
                ['mark_up' => $this->mark_up]
            );
    
            $this->alert('success', 'Mark Up updated successfully', [
                'position' => 'top-end',
                'timer' => 5000,
                'toast' => true,
            ]);
        } catch (\Exception $e) {
            $this->alert('error',  $e->getMessage(), [
                'position' => 'top-end',
                'timer' => 5000,
                'toast' => true,
            ]);
        }
    }
    public function render()
    {
        return view('livewire.page.payment-settings');
    }
}
