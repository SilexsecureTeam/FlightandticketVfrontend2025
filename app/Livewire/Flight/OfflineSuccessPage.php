<?php

namespace App\Livewire\Flight;

use App\Models\Payment\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class OfflineSuccessPage extends Component
{
    public $orderResponse;
    public $payment;
    public $showToSource = 'web';



    public function mount($orderResponse)
    {
        $this->showToSource = session('showToSource') ?? "web";
        // Decode the orderResponse
        $decodedOrderResponse = base64_decode($orderResponse, true);

        // Check if decoding was successful
        if (!$decodedOrderResponse) {
            abort(404);
        }

        $this->orderResponse = $decodedOrderResponse;

        $this->payment = Payment::where('flight_order_id', $this->orderResponse)
            ->with(['flightOrder:id,order_id,amount'])
            ->first();
    }


    public function download()
    {
        if (!$this->payment || !$this->payment->flightOrder) {
            abort(404, 'Payment record not found.');
        }

        $payment = $this->payment;

        // Generate PDF
        $pdf = Pdf::loadView('pdf.offline-pdf', ['payment' => $payment]);

        // Stream the PDF for download
        return response()->streamDownload(
            fn() => print($pdf->output()),
            "Receipt_{$payment->flightOrder->order_id}.pdf"
        );
    }

    public function render()
    {
        $payment =   $this->payment;
        return view('livewire.flight.offline-success-page', compact('payment'));
    }
}
