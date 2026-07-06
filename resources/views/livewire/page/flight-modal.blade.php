<div class="modal-overlay" id="overlay-<?= $flight['id'] ?>"></div>
<div class="modal" id="modal-<?= $flight['id'] ?>" wire:ignore>
    <div class="modal-header">
        <h3>Flight details</h3>
        <button class="modal-close modal-closeBtn" data-close="modal-<?= $flight['id'] ?>">&times;</button>
    </div>
    <div class="modal-body">
        <div style="height: auto;">
            <div class="border-t border-gray-200 bg-gray-50">
                <div class="p-6 space-y-6">
                    <!-- Tabs Navigation -->
                    <div x-data="{ activeTab: 'flight-info' }" class="mb-6">
                        <div class="border-b border-gray-200">
                            <nav class="flex -mb-px space-x-8">
                                <button @click="activeTab = 'flight-info'" :class="{ 'border-primary text-primary': activeTab === 'flight-info', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'flight-info' }" class="py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"> Flight Info </button>
                                <button @click="activeTab = 'flight-breakdown'" :class="{ 'border-primary text-primary': activeTab === 'flight-breakdown', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'flight-breakdown' }" class="py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"> Flight Breakdown </button>
                                <button @click="activeTab = 'fare-rules'" :class="{ 'border-primary text-primary': activeTab === 'fare-rules', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'fare-rules' }" class="py-4 px-1 border-b-2 font-medium text-sm border-primary text-primary"> Fare Rules </button>
                            </nav>
                        </div>
                        <!-- Flight Info Tab Content -->
                        <div x-show="activeTab === 'flight-info'" class="mt-6">
                            <!-- Flight Details Section -->
                            @foreach ($flight['itineraries'] as $index => $itinerary)
                            <div class="space-y-4">
                                <h3 class="font-semibold text-gray-900">
                                    {{ $index === 0 ? 'Depature' : 'Return' }} Flight Details
                                </h3>

                                @foreach ($itinerary['segments'] as $segIndex => $segment)
                                <div class="bg-white rounded-lg p-4 border border-gray-200"
                                    data-carrier-code="{{ $segment['carrierCode'] }}">

                                    <!-- Segment Header -->
                                    <div class="flex items-center justify-start mb-3 md:mb-0 md:mr-4">
                                        <img src="{{ !empty($itinerary['segments'][0]['airline']['logo'])
                                        ? url('/').'/storage/' . $itinerary['segments'][0]['airline']['logo']
                                        : asset('images/default-airline-logo.png') }}"
                                            alt="{{ $segment['airline']['name'] }}"
                                            class="object-contain w-16 h-16 md:w-20 md:h-20">
                                        <div class="text-base md:text-lg font-medium ml-3">
                                            {{ $segment['airline']['name'] ?? 'Unknown Airline' }}
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between mb-4 mt-4">
                                        <div class="flex items-center space-x-4">
                                            <span class="text-sm font-medium">
                                                Flight {{ $segment['carrierCode'] }} {{ $segment['number'] }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                Aircraft: {{ $segment['aircraft']['code'] }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-500">
                                            Duration: {{ str_replace(['PT', 'H', 'M'], ['', 'h ', 'm'], $segment['duration']) }}
                                        </span>
                                    </div>

                                    <!-- Flight Details -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <!-- Departure -->
                                        <div>
                                            <div class="text-sm text-gray-500">Departure</div>
                                            <div class="font-medium text-xs md:text-base">
                                                {{ $segment['departure']['city'] }} ({{ $segment['departure']['iataCode'] }})
                                            </div>
                                            <div class="text-sm">
                                                Terminal {{ $segment['departure']['terminal'] ?? '' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($segment['departure']['at'])->format('D, M d, Y H:i') }}
                                            </div>
                                        </div>

                                        <!-- Arrival -->
                                        <div class="text-right">
                                            <div class="text-sm text-gray-500">Arrival</div>
                                            <div class="font-medium text-xs md:text-base">
                                                {{ $segment['arrival']['city'] }} ({{ $segment['arrival']['iataCode'] }})
                                            </div>
                                            <div class="text-sm">
                                                Terminal {{ $segment['arrival']['terminal'] ?? '' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($segment['arrival']['at'])->format('D, M d, Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Layover Calculation -->
                                @if (isset($itinerary['segments'][$segIndex + 1]))
                                @php
                                $currentArrival = \Carbon\Carbon::parse($segment['arrival']['at']);
                                $nextDeparture = \Carbon\Carbon::parse($itinerary['segments'][$segIndex + 1]['departure']['at']);
                                $layoverDiff = $currentArrival->diff($nextDeparture);
                                $layoverHours = $layoverDiff->h;
                                $layoverMinutes = $layoverDiff->i;
                                $layoverDuration = ($layoverHours > 0 ? $layoverHours . 'h ' : '') .
                                ($layoverMinutes > 0 ? $layoverMinutes . 'm' : '');

                                $isNightLayover = $currentArrival->hour >= 18 || $nextDeparture->hour <= 6;
                                    $totalMinutes=$layoverHours * 60 + $layoverMinutes;

                                    if ($layoverHours>= 8) {
                                    $layoverType = $isNightLayover ? 'Overnight Stop' : 'Very Long Stopover';
                                    $severityClass = 'bg-red-50 border-red-100';
                                    $textClass = 'text-red-700';
                                    } elseif ($layoverHours >= 6) {
                                    $layoverType = $isNightLayover ? 'Night Stopover' : 'Long Stopover';
                                    $severityClass = 'bg-orange-50 border-orange-100';
                                    $textClass = 'text-orange-700';
                                    } elseif ($layoverHours >= 4) {
                                    $layoverType = $isNightLayover ? 'Evening Break' : 'Extended Break';
                                    $severityClass = 'bg-blue-50 border-blue-100';
                                    $textClass = 'text-blue-700';
                                    } elseif ($totalMinutes >= 90) {
                                    $layoverType = 'Comfortable Break';
                                    $severityClass = 'bg-green-400 border-green-100';
                                    $textClass = 'text-black';
                                    } elseif ($totalMinutes >= 45) {
                                    $layoverType = '45 Min Break - Move Quickly';
                                    $severityClass = 'bg-yellow-50 border-yellow-100';
                                    $textClass = 'text-yellow-700';
                                    } else {
                                    $layoverType = 'Rush Transfer - Very Tight';
                                    $severityClass = 'bg-red-50 border-red-100';
                                    $textClass = 'text-red-700';
                                    }

                                    $warningMessage = '';
                                    if ($totalMinutes < 90) {
                                        $warningMessage=$totalMinutes < 45
                                        ? 'This is an extremely tight transfer. Check with your airline if this is feasible.'
                                        : 'Pack light and be prepared to move quickly between gates.' ;
                                        }
                                        @endphp

                                        <div class="flex items-center gap-3 my-4 px-4 py-3 rounded-lg border {{ $severityClass }}">
                                        <div class="flex-shrink-0">
                                            @if ($isNightLayover)
                                            <svg class="w-5 h-5 {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                            </svg>
                                            @else
                                            <svg class="w-5 h-5 {{ $textClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            @endif
                                        </div>

                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2 font-bold">
                                                <span class="font-medium {{ $textClass }}">{{ $layoverType }}</span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $severityClass }} {{ $textClass }}">
                                                    {{ $layoverDuration }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-1 text-sm text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="text-black font-bold">{{ $segment['arrival']['iataCode'] }}</span>
                                                <span class="text-xs text-black font-bold">
                                                    {{ $currentArrival->format('H:i') }} - {{ $nextDeparture->format('H:i') }}
                                                </span>
                                            </div>
                                        </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                    <!-- Flight Breakdown Tab Content -->
                    <div x-show="activeTab === 'flight-breakdown'" class="mt-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 w-full gap-6">

                            <!-- Price Details -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-3">Price Breakdown</h4>
                                <div class="space-y-4">
                                    @php
                                    $travelerTypes = [];
                                    foreach ($flight['travelerPricings'] as $traveler) {
                                    $type = $traveler['travelerType'];
                                    if (!isset($travelerTypes[$type])) {
                                    $travelerTypes[$type] = [
                                    'count' => 0,
                                    'price' => 0,
                                    'currency' => $traveler['price']['currency'],
                                    ];
                                    }
                                    $travelerTypes[$type]['count']++;
                                    $travelerTypes[$type]['price'] += floatval($traveler['price']['total']);
                                    }
                                    @endphp

                                    @foreach ($travelerTypes as $type => $data)
                                    <div class="flex justify-between text-sm border-b pb-2">
                                        <span class="text-gray-700 font-medium">
                                            {{ ucfirst(strtolower($type) === 'held_infant' ? 'Infant' : strtolower($type)) }} x
                                            {{ $data['count'] }}
                                        </span>
                                        <span class="font-medium">
                                            {{ $data['currency'] }} {{ number_format($data['price'] + $flight['price']['commission'], 2) }}
                                        </span>
                                    </div>
                                    @endforeach

                                    <!-- Total Price -->
                                    <div class="flex justify-between font-medium pt-2 border-t border-gray-200">
                                        <span>Total Price</span>
                                        <span>
                                            {{ $flight['price']['currency'] }}
                                            {{ number_format((float) $flight['price']['grandTotal'] + $flight['price']['commission'], 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Baggage Information -->
                            <div>
                                <h4 class="font-medium text-gray-900 mb-3">Baggage Allowance</h4>
                                <div class="space-y-4">
                                    @foreach ($flight['travelerPricings'] as $index => $traveler)
                                    @if ($index === 0 || $traveler['travelerType'] !== $flight['travelerPricings'][$index - 1]['travelerType'])
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <h5 class="font-medium text-sm mb-2">
                                            {{ ucfirst(strtolower($traveler['travelerType']) === 'held_infant' ? 'Infant' : strtolower($traveler['travelerType'])) }}
                                        </h5>

                                        @if (isset($traveler['fareDetailsBySegment'][0]['includedCheckedBags']['weight']))
                                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>
                                                {{ $traveler['fareDetailsBySegment'][0]['includedCheckedBags']['weight'] }} {{ $traveler['fareDetailsBySegment'][0]['includedCheckedBags']['weightUnit'] }}
                                            </span>
                                        </div>
                                        @else
                                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span>No checked bags Weight included</span>
                                        </div>
                                        @endif
                                        @if (isset($traveler['fareDetailsBySegment'][0]['includedCheckedBags']['quantity']))
                                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>
                                                {{ $traveler['fareDetailsBySegment'][0]['includedCheckedBags']['quantity'] }}
                                                checked bags included
                                            </span>
                                        </div>
                                        @else
                                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            <span>No checked bags included</span>
                                        </div>
                                        @endif

                                        @if (isset($traveler['fareDetailsBySegment'][0]['includedCabinBags']['quantity']))
                                        <div class="flex items-center space-x-2 mt-1 text-sm text-gray-600">
                                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span>
                                                {{ $traveler['fareDetailsBySegment'][0]['includedCabinBags']['quantity'] }}
                                                cabin bag included
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Fare Rules Tab Content -->
                    <div x-show="activeTab === 'fare-rules'" class="mt-6">
                        <div class="bg-white rounded-lg p-6 border border-gray-200">
                            <h4 class="text-red-600 font-medium mb-4">Important
                                Information</h4>
                            <ul class="space-y-4 text-gray-700">
                                <li class="flex gap-2">
                                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"> </path>
                                    </svg>
                                    <div> <span class="font-medium">Penalty
                                            Applies:</span>
                                        <p class="mt-1 text-sm">On some airlines, changes are not permitted on a ticket in case of no-show and the ticket(s) have no value and cannot be refunded.</p>
                                    </div>
                                </li>
                                <li class="flex gap-2">
                                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"> </path>
                                    </svg>
                                    <div> <span class="font-medium">Visa
                                            Requirements:</span>
                                        <p class="mt-1 text-sm">Please ensure that you have a valid visa before your international travel. 247 travels will not be held liable by airport authorities if you have not fulfilled your visa requirements.</p>
                                    </div>
                                </li>
                                <li class="flex gap-2">
                                    <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div> <span class="font-medium">Kuala Lumpur
                                            Restrictions:</span>
                                        <p class="mt-1 text-sm">Tickets to Kuala Lumpur must not exceed 14 days, this simply means all tickets booked to Kuala Lumpur may be less than 14 days but must not be more than 14 days.</p>
                                    </div>
                                </li>
                                <li class="flex gap-2">
                                    <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"> </path>
                                    </svg>
                                    <div> <span class="font-medium">Egypt Air
                                            Policy:</span>
                                        <p class="mt-1 text-sm">Egypt Air tickets cannot be booked for one way routes, there should be a RESTRICTION on bookings, NO ONE WAY on Egypt Air.</p>
                                    </div>
                                </li>
                                <li class="flex gap-2">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"> </path>
                                    </svg>
                                    <div> <span class="font-medium">Health
                                            Concerns:</span>
                                        <p class="mt-1 text-sm">If you or anyone accompanying you has a serious health issue, please call us before booking. </p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class=" flex gap-3 justify-end items-end m-3">
            <div>
                @if($selectedFlightNow)
                <button wire:loading.remove wire:target="requestSelectFlight(<?= $flight['id'] ?>)" class="px-6 py-2 md:text-base text-xs bg-primary text-white rounded-lg hover:bg-red-700" wire:click="requestSelectFlight(<?= $flight['id'] ?>)"> Select Flight</button>
                @endif
                <div wire:loading wire:target="requestSelectFlight(<?= $flight['id'] ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" class="size-5 fill-neutral-600 motion-safe:animate-spin dark:fill-neutral-300">
                        <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25"></path>
                        <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z"> </path>
                    </svg>
                </div>
            </div>
            <button class=" bg-primary rounded-lg p-2 text-white modal-close" data-close="modal-<?= $flight['id'] ?>"> close</button>
        </div>
    </div>
</div>
</div>
<script>
    document.addEventListener('click', (e) => {
        // Open modal
        if (e.target.matches('.details-btn')) {
            const modalId = e.target.dataset.modal;
            document.querySelector("body").classList.add("bodyModalPrevent");
            document.getElementById(modalId).classList.add('active');
            document.getElementById('overlay-' + modalId.replace('modal-', '')).classList.add('active');
        }
        // Close modal
        if (e.target.matches('.modal-close')) {
            const modalId = e.target.dataset.close;
            document.getElementById(modalId).classList.remove('active');
            document.querySelector("body").classList.remove("bodyModalPrevent");
            document.getElementById('overlay-' + modalId.replace('modal-', '')).classList.remove('active');
        }
        // Close on overlay click
        if (e.target.classList.contains('modal-overlay')) {
            e.target.classList.remove('active');
            const id = e.target.id.replace('overlay-', '');
            document.getElementById('modal-' + id).classList.remove('active');
            document.querySelector("body").classList.remove("bodyModalPrevent");
        }
    });
</script>