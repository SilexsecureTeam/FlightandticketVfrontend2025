<?php

namespace App\Livewire\Page;

use App\Models\FlightSearch;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class FlightResult extends Component
{
    public $searchParams = [
        'from' => '',
        'to' => '',
        'fromDisplayName' => '',
        'toDisplayName' => '',
        'departureDate' => null,
        'returnDate' => null,
        'tripType' => 'round',
        'passengers' => ['adult' => 1, 'child' => 0, 'infant' => 0],
        'cabinClass' => 'ECONOMY',
        'multiCityFlights' => [],
    ];

    public $selectedIncludes = [];
    public $searchResultsId;
    public $searchId;
    public $status = 'processing';
    public $showToSource = 'web';
    public $loadMore = false;
    public $showAllResults = false;
    public $initialDisplayLimit = 50;
    public $moreResultsReady = false;
    public $allResultsLoaded = false;
    public $resultsHydrated = false;

    protected ?FlightSearch $flightSearchRecord = null;

    /** @var array<string, mixed>|null */
    protected ?array $viewSearchResults = null;

    public function mount(): void
    {
        $this->searchId = request()->get('id');

        if (request()->boolean('all')) {
            $this->showAllResults = true;
            $this->allResultsLoaded = true;
            $this->loadMore = 'loaded';
        }

        $this->checkStatus();
        $this->showToSource = session('showToSource') ?? 'web';
    }

    public function loadMoreFlights()
    {
        return $this->redirectRoute('search.results', [
            'id' => $this->searchId,
            'all' => 1,
        ]);
    }

    public function checkStatus()
    {
        $search = $this->flightSearchRecord();

        if (!$search) {
            session()->flash('error', 'No search ID provided.');
            return redirect()->route('landing');
        }

        $this->searchResultsId = $this->searchId;
        $previousStatus = $this->status;
        $previousMoreReady = $this->moreResultsReady;
        $this->status = $search->status;

        if ($search->status === 'failed') {
            session()->flash('error', '❌ Search failed, please try again later.');
            return redirect()->route('landing');
        }

        $moreResultsReady = ($search->meta['more_results_ready'] ?? false) || $search->status === 'view';

        if ($this->resultsHydrated && !$this->showAllResults && !$this->allResultsLoaded) {
            $this->moreResultsReady = $moreResultsReady;

            if ($previousStatus === $this->status && $previousMoreReady === $moreResultsReady) {
                return $this->skipRender();
            }

            return;
        }

        $shouldLoadResults = !in_array($search->status, ['processing', 'failed'], true)
            && !empty($search->flight_offers_path);

        if ($shouldLoadResults) {
            try {
                $this->hydrateFromSearchRecord($search);
                $this->moreResultsReady = $moreResultsReady;
                $this->viewSearchResults = null;
            } catch (\Exception $e) {
                session()->flash('error', 'Error retrieving search data: ' . $e->getMessage());
                return redirect()->route('landing');
            }
        }
    }

    protected function flightSearchRecord(): ?FlightSearch
    {
        if ($this->flightSearchRecord === null && $this->searchId) {
            $this->flightSearchRecord = FlightSearch::where('search_id', $this->searchId)->first();
        }

        return $this->flightSearchRecord;
    }

    protected function hydrateFromSearchRecord(FlightSearch $flightSearch): void
    {
        if ($flightSearch->expires_at && $flightSearch->expires_at < Carbon::now()) {
            throw new \RuntimeException('Search data expired or not found.');
        }

        $cheapestFlight = $flightSearch->cheapest;
        if (is_string($cheapestFlight)) {
            $cheapestFlight = json_decode($cheapestFlight, true);
        }
        $cheapestFlight = is_array($cheapestFlight) ? $cheapestFlight : [];

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
            'cabinClass' => $flightSearch->cabinClass,
            'multiCityFlights' => $flightSearch->multicity ? json_decode($flightSearch->multicity, true) : [],
        ];

        if (empty($this->searchParams['from']) || empty($this->searchParams['to'])) {
            $flightOffers = $this->readOffersFromStorage($flightSearch);
            if (!empty($flightOffers) && isset($flightOffers[0]['itineraries'][0]['segments'][0])) {
                $firstSegment = $flightOffers[0]['itineraries'][0]['segments'][0];
                if (empty($this->searchParams['from'])) {
                    $this->searchParams['from'] = $firstSegment['departure']['iataCode'] ?? '';
                }
                if (empty($this->searchParams['to'])) {
                    $this->searchParams['to'] = $firstSegment['arrival']['iataCode'] ?? '';
                }
            } else {
                if (empty($this->searchParams['from'])) {
                    $fromParts = explode('(', (string) $flightSearch->from_city_name);
                    if (count($fromParts) > 1) {
                        $this->searchParams['from'] = trim(str_replace(')', '', $fromParts[count($fromParts) - 1]));
                    }
                }

                if (empty($this->searchParams['to'])) {
                    $toParts = explode('(', (string) $flightSearch->to_city_name);
                    if (count($toParts) > 1) {
                        $this->searchParams['to'] = trim(str_replace(')', '', $toParts[count($toParts) - 1]));
                    }
                }
            }
        }

        $this->resultsHydrated = true;
    }

    protected function readOffersFromStorage(?FlightSearch $search = null): array
    {
        $search ??= $this->flightSearchRecord();

        if (!$search) {
            return [];
        }

        $meta = $search->meta ?? [];
        $path = ($this->showAllResults || $this->allResultsLoaded)
            ? ($meta['full_offers_path'] ?? null)
            : $search->flight_offers_path;

        if (empty($path) || !Storage::exists($path)) {
            if (!empty($search->flight_offers_path) && Storage::exists($search->flight_offers_path)) {
                $path = $search->flight_offers_path;
            } else {
                return [];
            }
        }

        return json_decode(Storage::get($path), true) ?? [];
    }

    protected function readDictionariesFromStorage(?FlightSearch $search = null): array
    {
        $search ??= $this->flightSearchRecord();

        if (!$search) {
            return [];
        }

        $meta = $search->meta ?? [];
        $path = ($this->showAllResults || $this->allResultsLoaded)
            ? ($meta['full_dictionaries_path'] ?? null)
            : $search->dictionaries_path;

        if (empty($path) || !Storage::exists($path)) {
            if (!empty($search->dictionaries_path) && Storage::exists($search->dictionaries_path)) {
                $path = $search->dictionaries_path;
            } else {
                return [];
            }
        }

        return json_decode(Storage::get($path), true) ?? [];
    }

    protected function buildSearchResultsForView(): array
    {
        if ($this->viewSearchResults !== null) {
            return $this->viewSearchResults;
        }

        $search = $this->flightSearchRecord();

        if (!$search) {
            return [
                'flightOffers' => [],
                'dictionaries' => [],
                'cheapestFlight' => [],
            ];
        }

        $cheapestFlight = $search->cheapest;
        if (is_string($cheapestFlight)) {
            $cheapestFlight = json_decode($cheapestFlight, true);
        }

        $this->viewSearchResults = [
            'tripType' => $search->trip_type,
            'meta' => $search->meta,
            'flightOffers' => $this->readOffersFromStorage($search),
            'dictionaries' => $this->readDictionariesFromStorage($search),
            'cheapestFlight' => is_array($cheapestFlight) ? $cheapestFlight : [],
            'passengers' => $search->passengers,
            'cabinClass' => $search->cabinClass,
            'fromCityName' => $search->from_city_name,
            'toCityName' => $search->to_city_name,
            'departureDate' => $search->departure_date,
            'returnDate' => $search->return_date,
            'multiCityFlights' => $search->multicity ? json_decode($search->multicity, true) : [],
        ];

        return $this->viewSearchResults;
    }

    protected function buildAirlinesWithNames(array $dictionaries): array
    {
        if (!isset($dictionaries['carriers']) || !is_array($dictionaries['carriers'])) {
            return [];
        }

        return collect($dictionaries['carriers'])
            ->map(fn ($name, $code) => ['code' => $code, 'name' => $name])
            ->values()
            ->all();
    }

    public function selectFlight($flightId)
    {
        $selectedFlight = collect($this->readOffersFromStorage())->firstWhere('id', $flightId);

        if ($selectedFlight) {
            session([
                'selected_flight' => $selectedFlight,
                'selected_includes' => implode(',', $this->selectedIncludes),
                'searchResultsId' => $this->searchResultsId,
            ]);
            session(['showToSource' => $this->showToSource]);

            return redirect()->route('booking.page');
        }
    }

    public function render()
    {
        $searchResults = $this->buildSearchResultsForView();

        return view('livewire.page.flight-result', [
            'searchResults' => $searchResults,
            'searchParams' => $this->searchParams,
            'airlinesWithNames' => $this->buildAirlinesWithNames($searchResults['dictionaries'] ?? []),
            'showAllResults' => $this->showAllResults,
            'initialDisplayLimit' => $this->initialDisplayLimit,
            'moreResultsReady' => $this->moreResultsReady,
            'allResultsLoaded' => $this->allResultsLoaded,
        ]);
    }
}
