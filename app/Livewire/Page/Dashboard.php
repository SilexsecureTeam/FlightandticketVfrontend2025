<?php

namespace App\Livewire\Page;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Airline;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\AmadeusApiTrait;
use Livewire\Attributes\Layout;
use App\Models\Flight\FlightOrder;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.admin')]
class Dashboard extends Component
{
    use WithPagination, AmadeusApiTrait;
    public $brand;
    public $number;
    public $expiryDate;
    public $securityCode;
    public $holder;

    public $selectedFlight; 
    public $order_id;
    public $search = '';
    public $dateFilter = '';

    public function selectFlight($flightId)
{
    // Fetch the flight details using the id
    $flightOrder = FlightOrder::find($flightId);

    $flightOffers = is_string($flightOrder->flight_offers) 
    ? json_decode($flightOrder->flight_offers, true) 
    : $flightOrder->flight_offers;

// Check if travelers is a string, then decode
$travelers = is_string($flightOrder->travelers) 
    ? json_decode($flightOrder->travelers, true) 
    : $flightOrder->travelers;

    // Assign to Livewire component
    $this->selectedFlight = [
        'id' => $flightOrder->id,
        'order_id' => $flightOrder->order_id,
        'Pnr' => $flightOrder->pnr,
        'flight_offers' => $flightOffers,
        'payment_status' => $flightOrder->payment_status,
        'order_status' => $flightOrder->order_status,
        'travelers' => $travelers
    ];
    $this->order_id = $flightOrder->order_id;

    // Debugging output to check the structure
    // dd($this->selectedFlight);
}

// protected $rules = [
//     'brand' => 'required|string|max:10',
//     'number' => 'required|digits:16',
//     'expiryDate' => 'required|date_format:Y-m',
//     'securityCode' => 'required|digits:3',
//     'holder' => 'required|string|max:50',
// ];

// protected $messages = [
//     'brand.required' => 'Card brand is required.',
//     'number.required' => 'Card number is required.',
//     'number.digits' => 'Card number must be 16 digits.',
//     'expiryDate.required' => 'Expiry date is required.',
//     'expiryDate.date_format' => 'Expiry date format should be YYYY-MM.',
//     'securityCode.required' => 'Security code is required.',
//     'securityCode.digits' => 'Security code must be 3 digits.',
//     'holder.required' => 'Cardholder name is required.',
// ];

public function mount()
{
        $this->dateFilter = Carbon::today();
}

public function processOrder()
{
    $this->number = str_replace(' ', '', $this->number);
    $this->validate([
        'brand' => 'required|string',
        'number' =>'required|digits:16',
        'expiryDate' => 'required|date_format:Y-m',
        'securityCode' => 'required|digits:3',
        'holder' => 'required|string',
    ]);

    // dd([
    //     'brand' => $this->brand,
    //     'number' => $this->number,
    //     'expiryDate' => $this->expiryDate,
    //     'securityCode' => $this->securityCode,
    //     'holder' => $this->holder,
    // ]);
    try {
        // Structure the request payload
        $payload = [
            'data' => [
                'formOfPayments' => [
                    [
                        'creditCard' => [
                            'brand' => $this->brand,
                            'number' => $this->number,
                            'expiryDate' => $this->expiryDate,
                            'securityCode' => $this->securityCode,
                            'holder' => $this->holder,
                        ]
                    ]
                ]
            ]
        ];
        //  dd( $payload);
        $clientRef = 'mymore-' . uniqid('', true);

        // Initialize the HTTP client
        $client = new Client();
        $baseUrl = config('services.amadeus.base_url');
    

        $response = $client->request('POST', "$baseUrl/v1/booking/flight-order/$this->order_id/issuance", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
                'Content-Type' => 'application/json',
                'ama-client-ref' => $clientRef,
            ],
            'json' => $payload,
        ]);



        // Decode and return the response as a success
        $data = json_decode($response->getBody()->getContents(), true);



    } catch (\GuzzleHttp\Exception\RequestException $e) {
        // Capture the full error response for more information

        $errorMessage = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
   

    }
}
    
    public function render()
    {
        $search = "%{$this->search}%";
        $flightOrders = FlightOrder::latest()
            ->where(function ($query) use ($search) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(travelers, '$[*].name.firstName')) LIKE ?", [$search])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(travelers, '$[*].name.lastName')) LIKE ?", [$search])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(travelers, '$[*].contact.emailAddress')) LIKE ?", [$search])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(travelers, '$[*].contact.phones[0].number')) LIKE ?", [$search])
                    ->orWhere('order_id', 'like', $search)
                    ->orWhere('payment_status', 'like', $search)
                    ->orWhere('order_status', 'like', $search)
                    ->orWhere('pnr', 'like', $search);
            })
            ->when($this->dateFilter, function ($query) {
                $query->whereDate('created_at', $this->dateFilter);
            })
            ->paginate(10);

        $flightcount = FlightOrder::count();
        return view('livewire.page.dashboard',[
            'flightOrders' => $flightOrders,
            'flightcount' => $flightcount,
          

        ]);
    }

    public function dateFilterFun()
    {
        $this->dateFilter = "";
        // return dd("Helli");
    }

    public function payment($id, $status)
    {
        $flightOrder = FlightOrder::findOrFail($id);
        $flightOrder->update(['payment_status' => $status == 'PENDING' ? 'APPROVED' : 'PENDING']);
    }

    public function order($id, $status)
    {
        $flightOrder = FlightOrder::findOrFail($id);
        $flightOrder->update(['order_status' => $status == 'PROCESSING' ? 'CONFIRMED' : 'PROCESSING']);
    }
}