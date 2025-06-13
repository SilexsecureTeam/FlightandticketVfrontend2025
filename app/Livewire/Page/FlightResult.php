<?php

namespace App\Livewire\Page;

use App\Models\FlightSearch;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\Attributes\On;

class FlightResult extends Component
{
    public $searchResults;
    public $selectedIncludes = [];
    public $markUp;
    public $sortBy = 'cheapest';
    public $searchParams; // For passing to edit component
    public $airlinesWithNames = [];
    public $selectedAirlines = [];
    public $filteredFlightOffers = [];

    public function mount()
    {
        // Get search ID from URL parameter
        $searchId = request()->get('id');
        
        if ($searchId) {
            try {
                // Get data from database
                $flightSearch = FlightSearch::where('search_id', $searchId)
                    ->where('expires_at', '>', Carbon::now())
                    ->first();
                
                if (!$flightSearch) {
                    session()->flash('error', 'Search data expired or not found.');
                    return redirect()->route('landing');
                }

                // Handle flight offers as either JSON string or array
                $flightOffers = $flightSearch->flight_offers;
                if (is_string($flightOffers)) {
                    $flightOffers = json_decode($flightOffers, true);
                }
                
                // Handle dictionaries as either JSON string or array
                $dictionaries = $flightSearch->dictionaries;
                if (is_string($dictionaries)) {
                    $dictionaries = json_decode($dictionaries, true);
                }

                // Process airlines from dictionaries
                $this->processAirlines($dictionaries);

                // Transform the data back to the expected format
                $this->searchResults = [
                    'tripType' => $flightSearch->trip_type,
                    'meta' => $flightSearch->meta,
                    'flightOffers' => $flightOffers,
                    'dictionaries' => $dictionaries,
                    'passengers' => $flightSearch->passengers,
                    'fromCityName' => $flightSearch->from_city_name,
                    'toCityName' => $flightSearch->to_city_name,
                    'departureDate' => $flightSearch->departure_date,
                    'returnDate' => $flightSearch->return_date,
                ];
                
                // Extract search data for edit form
                $this->searchParams = [
                    'editMode' => true,
                    'from' => $flightSearch->from_code,
                    'to' => $flightSearch->to_code,
                    'fromDisplayName' => $flightSearch->from_city_name,
                    'toDisplayName' => $flightSearch->to_city_name,
                    'departureDate' => $flightSearch->departure_date,
                    'returnDate' => $flightSearch->return_date,
                    'tripType' => $flightSearch->trip_type,
                    'passengers' => $flightSearch->passengers,
                    'cabinClass' => 'ECONOMY', // Default if not stored
                ];
                
                // If codes are missing, try to extract them from itineraries
                if (empty($this->searchParams['from']) || empty($this->searchParams['to'])) {
                    if (!empty($flightOffers) && isset($flightOffers[0]['itineraries'][0]['segments'][0])) {
                        $firstSegment = $flightOffers[0]['itineraries'][0]['segments'][0];
                        if (empty($this->searchParams['from'])) {
                            $this->searchParams['from'] = $firstSegment['departure']['iataCode'] ?? '';
                        }
                        if (empty($this->searchParams['to'])) {
                            $this->searchParams['to'] = $firstSegment['arrival']['iataCode'] ?? '';
                        }
                    } else {
                        // If no flight offers, try to extract codes from city names
                        if (empty($this->searchParams['from'])) {
                            $fromParts = explode('(', $flightSearch->from_city_name);
                            if (count($fromParts) > 1) {
                                $this->searchParams['from'] = trim(str_replace(')', '', $fromParts[count($fromParts) - 1]));
                            }
                        }
                        
                        if (empty($this->searchParams['to'])) {
                            $toParts = explode('(', $flightSearch->to_city_name);
                            if (count($toParts) > 1) {
                                $this->searchParams['to'] = trim(str_replace(')', '', $toParts[count($toParts) - 1]));
                            }
                        }
                    }
                }
                
            } catch (\Exception $e) {
                session()->flash('error', 'Error retrieving search data: ' . $e->getMessage());
                return redirect()->route('landing');
            }
        } else {
            session()->flash('error', 'No search ID provided.');
            return redirect()->route('landing');
        }
    }

    protected function processAirlines($dictionaries)
    {
        if (isset($dictionaries['carriers']) && is_array($dictionaries['carriers'])) {
            $this->airlinesWithNames = collect($dictionaries['carriers'])
                ->map(function ($name, $code) {
                    return [
                        'code' => $code,
                        'name' => $name
                    ];
                })
                ->values()
                ->all();
        }
    }

    public function updateresult()
    {
        $this->sortBy = 'cheapest';
    }

    public function selectFlight($flightId)
    {
        // Find the selected flight using the ID
        $selectedFlight = collect($this->searchResults['flightOffers'])->firstWhere('id', $flightId);

        if ($selectedFlight) {
            // Store only the selected flight in session, not the entire search result
            session([
                'selected_flight' => $selectedFlight,
                'selected_includes' => implode(',', $this->selectedIncludes)
            ]);

            return redirect()->route('booking.page');
        }
    }

    #[On('filterAirlines')]
    public function filterAirlines($data)
    {
        $this->selectedAirlines = $data['airlines'];
        
        if (empty($this->selectedAirlines)) {
            // If no airlines selected, show all flights
            $this->filteredFlightOffers = $this->searchResults['flightOffers'];
        } else {
            // Filter flights based on selected airlines
            $this->filteredFlightOffers = collect($this->searchResults['flightOffers'])
                ->filter(function ($offer) {
                    // Check if any of the validating airline codes match the selected airlines
                    $validatingAirlines = collect($offer['validatingAirlineCodes'] ?? []);
                    if ($validatingAirlines->intersect($this->selectedAirlines)->isNotEmpty()) {
                        return true;
                    }
                    
                    // Also check all segments for matching airlines
                    return collect($offer['itineraries'])->some(function ($itinerary) {
                        return collect($itinerary['segments'])->some(function ($segment) {
                            return in_array($segment['carrierCode'], $this->selectedAirlines);
                        });
                    });
                })
                ->values()
                ->all();
        }
    }

    public function render()
    {
        // If no filtering has been applied yet, use all flight offers
        if (empty($this->filteredFlightOffers)) {
            $this->filteredFlightOffers = $this->searchResults['flightOffers'];
        }

        return view('livewire.page.flight-result', [
            'searchResults' => $this->searchResults,
            'searchParams' => $this->searchParams,
            'airlinesWithNames' => $this->airlinesWithNames,
            'filteredFlightOffers' => $this->filteredFlightOffers,
        ]);
    }
}
