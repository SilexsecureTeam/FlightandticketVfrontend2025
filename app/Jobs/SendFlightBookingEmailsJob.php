<?php

namespace App\Jobs;

use App\Http\Services\FlightBookingService;
use App\Models\Payment\Payment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Sends the standard booking confirmation emails, unless the customer has
 * since chosen "Reserve and Pay Later" (which sends its own offline emails).
 */
class SendFlightBookingEmailsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public array $bookingData,
        public string $email,
        public int $flightOrderId,
    ) {
    }

    public function handle(FlightBookingService $bookingService): void
    {
        $reservedOffline = Payment::query()
            ->where('flight_order_id', $this->flightOrderId)
            ->where('provider', 'Offline')
            ->exists();

        if ($reservedOffline) {
            return;
        }

        $bookingService->sendConfirmationEmails($this->bookingData, $this->email);
    }
}
