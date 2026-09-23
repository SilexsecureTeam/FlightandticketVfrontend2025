<?php

namespace App\Livewire\Admin;

use App\Models\Payment\AlatPayPaymentEvent;
use App\Models\Payment\Payment;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class ManageAlatPayPayments extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $source = '';
    public ?int $selectedId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'source' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingSource(): void
    {
        $this->resetPage();
    }

    public function view(int $id): void
    {
        $this->selectedId = $id;
    }

    public function closeDetail(): void
    {
        $this->selectedId = null;
    }

    public function render()
    {
        $payments = Payment::query()
            ->with(['flightOrder:id,order_id,pnr,payment_status,order_status'])
            ->where('provider', 'alatpay')
            ->when($this->search, function ($query) {
                $term = '%' . $this->search . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('reference', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('transaction_id', 'like', $term)
                        ->orWhere('alatpay_order_id', 'like', $term)
                        ->orWhereHas('flightOrder', function ($fo) use ($term) {
                            $fo->where('pnr', 'like', $term)
                                ->orWhere('order_id', 'like', $term);
                        });
                });
            })
            ->when($this->status, fn ($q) => $q->where('payment_status', $this->status))
            ->when($this->source, fn ($q) => $q->where('source', $this->source))
            ->latest('id')
            ->paginate(15);

        $selectedPayment = null;
        $events = collect();

        if ($this->selectedId) {
            $selectedPayment = Payment::query()
                ->with('flightOrder')
                ->where('provider', 'alatpay')
                ->find($this->selectedId);

            if ($selectedPayment) {
                $events = AlatPayPaymentEvent::query()
                    ->where('payment_id', $selectedPayment->id)
                    ->orWhere('reference', $selectedPayment->reference)
                    ->latest('id')
                    ->limit(50)
                    ->get();
            }
        }

        return view('livewire.admin.manage-alatpay-payments', [
            'payments' => $payments,
            'selectedPayment' => $selectedPayment,
            'events' => $events,
            'webhookUrl' => config('services.alatpay.webhook_url') ?: url('/webhooks/alatpay'),
            'callbackUrl' => config('services.alatpay.callback_url') ?: url('/payment/alatpay/callback'),
        ]);
    }
}
