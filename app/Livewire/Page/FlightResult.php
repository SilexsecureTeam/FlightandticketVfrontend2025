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
    public int $currentPage = 1;
    public int $perPage = 20;
    public array $filterAirlines = [];
    public array $filterStops = [];
    public string $sortBy = 'cheapest';

    protected ?FlightSearch $flightSearchRecord = null;

    /** @var array<string, mixed>|null */
    protected ?array $viewSearchResults = null;

    /** @var array<int, array>|null */
    protected ?array $filteredFlightOffersCache = null;

    /** @var array<int, array>|null */
    protected ?array $allOffersCache = null;

    public function mount(): void
    {
        $this->searchId = request()->get('id');

        if (request()->boolean('all')) {
            $this->showAllResults = true;
            $this->allResultsLoaded = true;
            $this->loadMore = 'loaded';
            $this->currentPage = 1;
            $this->filteredFlightOffersCache = null;
            $this->allOffersCache = null;
        }

        $this->checkStatus(initialLoad: true);
        $this->showToSource = session('showToSource') ?? 'web';
    }

    public function loadMoreFlights()
    {
        $search = $this->flightSearchRecord(fresh: true);

        if ($search && $search->status === 'completed') {
            $search->update(['status' => 'view']);
            $this->status = 'view';
            $this->flightSearchRecord = $search->fresh();
        }

        $this->allResultsLoaded = true;
        $this->showAllResults = true;
        $this->currentPage = 1;
        $this->filteredFlightOffersCache = null;
        $this->allOffersCache = null;

        return $this->redirectRoute('search.results', [
            'id' => $this->searchId,
            'all' => 1,
        ]);
    }

    public function checkStatus(bool $initialLoad = false)
    {
        $search = $this->flightSearchRecord(fresh: !$initialLoad);

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

        $moreResultsReady = in_array($search->status, ['completed', 'view'], true)
            || ($search->meta['more_results_ready'] ?? false);

        if ($this->resultsHydrated && !$this->showAllResults && !$this->allResultsLoaded) {
            $this->moreResultsReady = $moreResultsReady;

            if (!$initialLoad && $previousStatus === $this->status && $previousMoreReady === $moreResultsReady) {
                return $this->skipRender();
            }

            return;
        }

        $shouldLoadResults = $search->status !== 'failed'
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

    public function applySearchStatusUpdate(string $status, bool $moreResultsReady = false)
    {
        if ($this->status === $status && $this->moreResultsReady === $moreResultsReady) {
            return $this->skipRender();
        }

        $this->status = $status;
        $this->moreResultsReady = $moreResultsReady;

        if ($status === 'failed') {
            session()->flash('error', '❌ Search failed, please try again later.');
            return $this->redirectRoute('landing');
        }
    }

    protected function shouldPollStatus(): bool
    {
        return $this->status === 'processing' && !$this->allResultsLoaded;
    }

    protected function flightSearchRecord(bool $fresh = false): ?FlightSearch
    {
        if ($fresh) {
            $this->flightSearchRecord = null;
        }

        if ($this->flightSearchRecord === null && $this->searchId) {
            $this->flightSearchRecord = FlightSearch::query()->firstWhere('search_id', $this->searchId);
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

    protected function readAllOffersCached(): array
    {
        if ($this->allOffersCache === null) {
            $this->allOffersCache = $this->readOffersFromStorage();
        }

        return $this->allOffersCache;
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
            'flightOffers' => $this->readAllOffersCached(),
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

    public function updateFlightFilters(array $airlines, array $stops): void
    {
        $this->filterAirlines = array_values(array_filter($airlines));
        $this->filterStops = array_values(array_map('intval', $stops));
        $this->currentPage = 1;
        $this->filteredFlightOffersCache = null;
    }

    public function setFlightSort(string $sort): void
    {
        if (!in_array($sort, ['cheapest', 'fastest'], true)) {
            return;
        }

        $this->sortBy = $sort;
        $this->currentPage = 1;
        $this->filteredFlightOffersCache = null;
    }

    public function goToFlightPage(int $page): void
    {
        $totalPages = $this->getTotalPages();
        $this->currentPage = max(1, min($page, $totalPages));
    }

    public function previousFlightPage(): void
    {
        $this->goToFlightPage($this->currentPage - 1);
    }

    public function nextFlightPage(): void
    {
        $this->goToFlightPage($this->currentPage + 1);
    }

    protected function enrichFlightOffer(array $flight): array
    {
        $flight['totalPrice'] = floatval($flight['price']['grandTotal'] ?? $flight['price']['total'] ?? 0);

        $totalDurationInSeconds = 0;
        $stopNm = 0;

        foreach ($flight['itineraries'] as $itinerary) {
            if ($stopNm) {
                $stopNm = $stopNm == 10 ? 0 : $stopNm;
                $stopNm = count($itinerary['segments']) - 1 == $stopNm ? $stopNm : 700;
            } else {
                $stopNm = count($itinerary['segments']) - 1 > 0 ? count($itinerary['segments']) - 1 : 10;
            }

            foreach ($itinerary['segments'] as $segment) {
                $durationParts = explode('PT', $segment['duration'] ?? '');
                if (isset($durationParts[1])) {
                    $timeParts = explode('H', $durationParts[1]);
                    $hours = isset($timeParts[0]) ? intval($timeParts[0]) : 0;
                    $minutes = isset($timeParts[1]) ? intval(str_replace('M', '', $timeParts[1])) : 0;
                    $totalDurationInSeconds += $hours * 3600 + $minutes * 60;
                }
            }
        }

        $flight['totalDuration'] = $totalDurationInSeconds;
        $flight['totalStops'] = $stopNm == 10 ? 0 : $stopNm;

        return $flight;
    }

    protected function flightMatchesCarrierFilters(array $flight): bool
    {
        if (empty($this->filterAirlines)) {
            return true;
        }

        $validatingAirlineCode = $flight['validatingAirlineCodes'][0] ?? '';
        $carrierCodes = $this->extractCarrierCodes($flight);

        foreach ($this->filterAirlines as $code) {
            if ($code === $validatingAirlineCode || in_array($code, $carrierCodes, true)) {
                return true;
            }
        }

        return false;
    }

    protected function flightMatchesStopFilters(array $flight): bool
    {
        if (empty($this->filterStops)) {
            return true;
        }

        return in_array((int) ($flight['totalStops'] ?? 0), $this->filterStops, true);
    }

    protected function extractCarrierCodes(array $flight): array
    {
        $codes = [];

        foreach ($flight['itineraries'] ?? [] as $itinerary) {
            foreach ($itinerary['segments'] ?? [] as $segment) {
                if (!empty($segment['carrierCode'])) {
                    $codes[] = $segment['carrierCode'];
                }
            }
        }

        return array_values(array_unique($codes));
    }

    protected function getFilteredFlightOffers(): array
    {
        if ($this->filteredFlightOffersCache !== null) {
            return $this->filteredFlightOffersCache;
        }

        $offers = array_map(
            fn (array $flight) => $this->enrichFlightOffer($flight),
            $this->readAllOffersCached()
        );

        $offers = array_values(array_filter(
            $offers,
            fn (array $flight) => $this->flightMatchesCarrierFilters($flight)
                && $this->flightMatchesStopFilters($flight)
        ));

        usort($offers, function (array $a, array $b) {
            if ($this->sortBy === 'fastest') {
                return ($a['totalDuration'] ?? 0) <=> ($b['totalDuration'] ?? 0);
            }

            return ($a['totalPrice'] ?? 0) <=> ($b['totalPrice'] ?? 0);
        });

        $this->filteredFlightOffersCache = $offers;

        return $offers;
    }

    protected function getEffectivePage(): int
    {
        return min($this->currentPage, $this->getTotalPages());
    }

    protected function getPaginatedFlightOffers(): array
    {
        $filtered = $this->getFilteredFlightOffers();
        $offset = ($this->getEffectivePage() - 1) * $this->perPage;

        return array_slice($filtered, $offset, $this->perPage);
    }

    protected function getTotalFilteredCount(): int
    {
        return count($this->getFilteredFlightOffers());
    }

    protected function getTotalPages(): int
    {
        $total = $this->getTotalFilteredCount();

        return max(1, (int) ceil($total / $this->perPage));
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
        $totalFilteredCount = $this->getTotalFilteredCount();
        $totalPages = $this->getTotalPages();
        $currentPage = $this->getEffectivePage();

        return view('livewire.page.flight-result', [
            'searchResults' => $searchResults,
            'searchParams' => $this->searchParams,
            'airlinesWithNames' => $this->buildAirlinesWithNames($searchResults['dictionaries'] ?? []),
            'paginatedFlightOffers' => $this->getPaginatedFlightOffers(),
            'totalFilteredCount' => $totalFilteredCount,
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'showAllResults' => $this->showAllResults,
            'initialDisplayLimit' => $this->initialDisplayLimit,
            'moreResultsReady' => $this->moreResultsReady,
            'allResultsLoaded' => $this->allResultsLoaded,
            'shouldPoll' => $this->shouldPollStatus(),
        ]);
    }
}
