<?php

namespace App\Livewire\Booking;

use App\Mail\OfflineLineMail;
use App\Models\Airline;
use App\Models\Markup;
use App\Models\Payment\Payment;
use Illuminate\Support\Str;
use Livewire\Component;
use Mail;

class PaymentBooking extends Component
{
    public $amount = 0;
    public $currency = 'NGN';
    public $paystackUrl;
    public $price;
    public $selectedPayment = 'Alat Pay';
    public $cardNumber = '';
    public $expiryDate = '';
    public $cvv = '';
    public $isLoading = false;
    public $bookingDetails = false;
    public $email = false;
    public $markUp;
    public $data = [

    ];
    public $orderResponse;


    public function mount()
    {
        // Retrieve data from session
        $this->paystackUrl = session('paystackUrl', "");
        $this->orderResponse = session('orderResponse', "");
        $this->price = session('payment_information', []);
    
        // Retrieve booking details from session
        $bookingDetails = session('booking_details');
        $this->bookingDetails = $bookingDetails['data'];

    

    
        // Process itineraries and segments
        if (isset($this->bookingDetails['itineraries']) && is_array($this->bookingDetails['itineraries'])) {
            foreach ($this->bookingDetails['itineraries'] as &$itinerary) {
                if (isset($itinerary['segments']) && is_array($itinerary['segments'])) {
                    foreach ($itinerary['segments'] as &$segment) { // Use & to pass by reference
                        // Get airline details including markup
                        $airlineDetails = $this->getAirlineDetailsByCode($segment['carrierCode']);
                        $segment['airline'] = [
                            'name' => $airlineDetails['name'],
                            'logo' => $airlineDetails['logo'],
                            'markup' => $airlineDetails['markup'],
                        ];
    
                    }
                }
            }
        }
    
        // Debugging: Uncomment the line below to inspect the updated structure
     
    
        // Extract traveler and contact details
        $this->data = [
            'firstName' => $this->bookingDetails['travelers'][0]['name']['firstName'] ?? '',
            'lastName' => $this->bookingDetails['travelers'][0]['name']['lastName'] ?? '',
            'middleName' => $this->bookingDetails['travelers'][0]['name']['middleName'] ?? '',
            'phone' => $this->bookingDetails['travelers'][0]['contact']['phones'][0]['number'] ?? '',
            'email' => $bookingDetails['email'] ?? '',
        ];
        $this->email = $bookingDetails['email'] ?? '';
    }

    // public function mount()
    // {
  
    //     // Retrieve data from session
    //     $this->paystackUrl = session('paystackUrl', "");
    //     $this->orderResponse = session('orderResponse', "");
    //     $this->price = session('payment_information', []);
  
 



    //     $bookingDetails = session('booking_details');
    //     $this->bookingDetails = $bookingDetails['data'];

    //     if (isset($this->bookingDetails['itineraries']) && is_array($this->bookingDetails['itineraries'])) {
    //         foreach ($this->bookingDetails['itineraries'] as &$itinerary) {
    //             if (isset($itinerary['segments']) && is_array($itinerary['segments'])) {
    //                 foreach ($itinerary['segments'] as $segment) {
    //                     // Get airline details including markup
    //                     $airlineDetails = $this->getAirlineDetailsByCode($segment['carrierCode']);
    //                     $segment['airline'] = [
    //                         'name' => $airlineDetails['name'],
    //                         'logo' => $airlineDetails['logo'],
    //                         'markup' => $airlineDetails['markup'],
    //                     ];
    //                 }
    //             }
    //         }
    //     }
    //     dd($this->bookingDetails);
    //     // dd($this->bookingDetails['travelers'][0]['contact']['phones'][0]['number']);

    //     $this->data = [
    //         'firstName' => $this->bookingDetails['travelers'][0]['name']['firstName'] ?? '',
    //         'lastName' => $this->bookingDetails['travelers'][0]['name']['lastName'] ?? '',
    //         'phone' => $this->bookingDetails['travelers'][0]['contact']['phones'][0]['number'] ?? '',
    //         'email' => $bookingDetails['email']?? '',
    //     ];
    //     $this->email = $bookingDetails['email'];
      
    // }
    public function updatePaymentMethod($method)
    {
        $this->selectedPayment = $method;
    }

    public function processPayment()
    {
        $this->isLoading = true;

        if ($this->selectedPayment === 'Paystack') {
            $this->initiatePaystackPayment();
        } else {
            $this->processAlatPayment();
        }
    }

    protected function initiatePaystackPayment()
    {
        if (!empty($this->paystackUrl)) {
            return redirect($this->paystackUrl);
        }
    }



    public function offlineBooking()
    {
// dd($this->bookingDetails);
        $payment = Payment::where('flight_order_id', $this->orderResponse)->firstOrFail(); 
    
        $payment->update([
            'provider' => 'Offline',
            'payment_date' => now()->format('Y-m-d H:i:s'),
            'reference' => Str::upper(Str::random(12)),
            'commission'=>$this->bookingDetails['commission'] ?? 0
        ]);
    
        // dd($this->bookingDetails);
        Mail::to($this->email)->send(new OfflineLineMail(bookingData: $this->bookingDetails));
        $hashedOrder = base64_encode($this->orderResponse);
      
        return redirect()->route('payment.offline', ['orderResponse' => $hashedOrder]);

    }

     

    protected function processAlatPayment()
    {
        // Alat Pay processing logic
    }


    public function getAirlineDetailsByCode($carrierCode) {
        // Fetch airline details from the database
        $airline = Airline::where('airline_code', $carrierCode)->first();
        // dd( $airline);
        if ($airline) {
            return [
                'name' => $airline->airline_name,
                'logo' => $airline->logo ?? null, 
                'markup' => $airline->markup ?? 0,
            ];
        }
    
        return [
            'name' => 'Unknown',
            'logo' => null,
            'markup' => 0,
        ];
    }


    public function render()
    {
        $price = $this->price['grandTotal'] + $this->markUp;
        return view('livewire.booking.payment-booking', [
            'totalamount' => $price ?? 0,
            'currency' => $this->price['currency'] ?? 'NGN',
            'email' => $this->data['email'] ?? '',
            'firstName' => $this->data['firstName'] ?? '',
            'middleName' => $this->data['middleName'] ?? '',
            'lastName' => $this->data['lastName'] ?? '',
            'phone' => $this->data['phone'] ?? '',
        ]);
    }

 
}

