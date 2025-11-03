<?php

namespace App\Livewire\Page;

use App\Models\Payment\Payment as PaymentModel;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
// use App\Models\Payment as PaymentModel;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class Payment extends Component
{
    public $search;
    public $payment_status;
    public $payment_id;

    use WithPagination, LivewireAlert;
    public function selectPayment($paymentId)
    {
        // Find payment and store it
        $payment = PaymentModel::select('id', 'payment_status')->findOrFail($paymentId);

        $this->payment_id = $payment->id;
        $this->payment_status = $payment->payment_status;
    }
    public function updateStatus()
    {
        try {
            $this->validate([
                'payment_status' => 'required|string|in:PENDING,SUCCESS,FAILED,CANCELED',
            ]);
        
    
            if ($this->payment_id) {
                $payment = PaymentModel::find($this->payment_id);
            
                if ($payment) {

                    $payment->update(['payment_status' => $this->payment_status]);
             

                } 

                $this->alert('success', 'Payment status updated successfully!', [
                    'position' => 'top-right',
                    'timer' => 3000,
                    'toast' => true,
                ]);
            } else {
                throw new \Exception("Payment ID is missing.");
            }
        } catch (\Exception $e) {
            $this->alert('error', "Error: " . $e->getMessage(), [
                'position' => 'top-right',
                'timer' => 5000,
                'toast' => true,
            ]);
        }
    }
    
    public function render()
    {
        $payments = PaymentModel::with(['flightOrder:id,order_id'])
        ->when($this->search, function ($query) {
            $query->where('provider', 'like', "%{$this->search}%")
                  ->orWhereHas('flightOrder', function ($q) {
                      $q->where('order_id', 'like', "%{$this->search}%");
                  });
        })
        ->latest()
        ->paginate(12);
        return view('livewire.page.payment', compact('payments'));
    }
}
