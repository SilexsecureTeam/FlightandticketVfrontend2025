<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 text-gray-800  font-sans">

    <div x-data="{
        currentStep: 'select-flight',
        steps: [
            { id: 'search-flight', label: 'Search Flight' },
            { id: 'select-flight', label: 'Select Flight' },
            { id: 'additional-info', label: 'Additional Info' },
            { id: 'payment', label: 'Payment' }
        ],
        isEditOpen: false
    }" class="bg-black text-white sticky mt-1 md:top-[2%] w-full z-[99]">

        <div class="px-2 sm:px-3 md:px-6 py-1">
            <div class="flex items-center justify-between space-x-2 ">
                <!-- Title -->
                <div class="text-xs md:text-sm font-bold text-white shrink-0">Flight Details</div>

                <!-- Stepper container -->
                <div class="flex-1 hidden md:inline relative py-3  max-w-md mx-auto">
                    <div class="absolute inset-0 flex items-center justify-between px-1">
                        <div class="absolute h-[2px] bg-gray-700 w-full"></div>
                        <div class="absolute h-[2px] bg-primary transition-all duration-300"
                            :style="'width: ' + (steps.findIndex(step => step.id === currentStep) / (steps.length - 1) * 100) +
                            '%'">
                        </div>

                        <div class="relative flex justify-between w-full">
                            <template x-for="(step, index) in steps" :key="step.id">
                                <div class="relative flex flex-col items-center w-16">
                                    <!-- Step Circle -->
                                    <div class="w-3 h-3 rounded-full flex items-center justify-center transition-all duration-300 z-10 text-[10px] font-medium"
                                        :class="{
                                            'bg-primary text-white': currentStep === step.id,
                                            'bg-white text-black': steps.findIndex(s => s.id === currentStep) > index,
                                            'bg-white text-gray-400': steps.findIndex(s => s.id === currentStep) < index
                                        }">
                                        <span x-text="index + 1"></span>
                                    </div>
                                    <!-- Step Label -->
                                    <div class="absolute -bottom-3 text-[8px] text-center w-full transition-all duration-300"
                                        :class="{
                                            'text-primary': currentStep === step.id,
                                            'text-white': steps.findIndex(s => s.id === currentStep) > index,
                                            'text-white': steps.findIndex(s => s.id === currentStep) < index
                                        }">
                                        <span x-text="step.label" class="inline-block"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div wire:ignore>
                    <a href="{{ url('search?id='.$searchResultsId)}}" class="text-sm bg-white px-4 py-2 rounded-xl text-black shrink-0">Back</a>
                </div>


            </div>
        </div>

    </div>



    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-2" x-data="{
        travelRulesOpen: false,
        fareRulesOpen: false,
        travelerInfoOpen: true,
    
    }">




        <div class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden border border-gray-100">
            <div x-data="{ isOpen: false }">

                <!-- Main Flight Information -->
                <div class="p-6">
                    <div class="grid grid-cols-12 gap-4">
                        <!-- Left Section: Flight Route -->
                        <div class="col-span-12 lg:col-span-7">
                            @foreach ($selectedFlight['itineraries'] as $index => $itinerary)
                            <div class="mb-6 last:mb-0">
                                <!-- Mobile Flight Header - Logo on left side -->
                                <div class="flex md:hidden items-center mb-4">
                                    <!-- Logo on left -->
                                    <div class="flex-shrink-0 mr-3">
                                        <img src="{{ !empty($itinerary['segments'][0]['airline']['logo'])
                                                ? 'https://main.mymoretravels.com/storage/' . $itinerary['segments'][0]['airline']['logo']
                                                : asset('images/default-airline-logo.png') }}"
                                            alt="{{ $itinerary['segments'][0]['airline']['name'] }}"
                                            class="object-contain w-12 h-12">
                                    </div>

                                    <!-- Airline name only -->
                                    <div class="flex-1">
                                        <div class="text-sm font-medium">
                                            {{ $itinerary['segments'][0]['airline']['name'] ?? 'Unknown Airline' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Flight details - Improved mobile layout -->
                                <div class="flex flex-row md:items-center justify-between md:gap-4">
                                    <!-- Airline logo - Desktop only -->
                                    <div class="hidden md:flex flex-col items-center justify-center">
                                        <img src="{{ !empty($itinerary['segments'][0]['airline']['logo'])
                                                ? 'https://main.mymoretravels.com/storage/' . $itinerary['segments'][0]['airline']['logo']
                                                : asset('images/default-airline-logo.png') }}"
                                            alt="{{ $itinerary['segments'][0]['airline']['name'] }}"
                                            class="object-contain w-12 h-12">
                                        <div class="text-xs font-medium text-center">
                                            {{ $itinerary['segments'][0]['airline']['name'] ?? 'Unknown Airline' }}
                                        </div>
                                    </div>

                                    <!-- Departure -->
                                    <div class="flex-shrink-0 text-left w-[45%] md:w-auto">
                                        <div class="text-xs font-bold line-clamp-2 md:line-clamp-none">
                                            {{ $itinerary['segments'][0]['departure']['city_country'] }}
                                            ({{ $itinerary['segments'][0]['departure']['iataCode'] }})
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($itinerary['segments'][0]['departure']['at'])->format('H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($itinerary['segments'][0]['departure']['at'])->format('D, M d') }}
                                        </div>
                                    </div>

                                    <!-- Arrow for mobile -->
                                    <div class="flex items-center self-center text-gray-400 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="2"
                                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </div>

                                    <!-- Flight Path - Desktop only -->
                                    <div class="hidden md:block flex-1 px-4">
                                        <div class="relative">
                                            <div class="h-px bg-gray-300 w-full"></div>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <span class="bg-white px-2 text-xs text-gray-500">
                                                    {{ str_replace(['PT', 'H', 'M'], ['', 'h ', 'm'], $itinerary['duration']) }}
                                                </span>
                                            </div>
                                        </div>
                                        <!-- Stops Indicator -->
                                        <div class="text-center flex flex-col items-center gap-3 mt-3">
                                            @if (count($itinerary['segments']) > 1)
                                            <span class="text-xs flex-shrink-0 text-orange-600">
                                                {{ count($itinerary['segments']) - 1 }}
                                                stop(s)
                                            </span>
                                            <span class="text-xs">
                                                {{ $selectedFlight['travelerPricings'][0]['fareDetailsBySegment'][0]['cabin'] }}
                                            </span>
                                            @else
                                            <span class="text-xs text-green-600">Direct</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Arrival -->
                                    <div class="flex-shrink-0 text-right w-[45%] md:w-auto">
                                        @php
                                        $lastSegment = end($itinerary['segments']);
                                        @endphp
                                        <div class="text-xs font-bold line-clamp-2 md:line-clamp-none">
                                            {{ $lastSegment['arrival']['city_country'] }}
                                            ({{ $lastSegment['arrival']['iataCode'] }})
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($lastSegment['arrival']['at'])->format('H:i') }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($lastSegment['arrival']['at'])->format('D, M d') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Flight details below arrow - Mobile only -->
                                <div class="flex md:hidden justify-center items-center mt-3 mb-2">
                                    <div class="text-center">
                                        <div class="text-sm font-medium">
                                            {{ str_replace(['PT', 'H', 'M'], ['', 'h ', 'm'], $itinerary['duration']) }}
                                            •
                                            @if (count($itinerary['segments']) > 1)
                                            <span class="text-orange-600">
                                                {{ count($itinerary['segments']) - 1 }}
                                                stop
                                            </span>
                                            @else
                                            <span class="text-green-600">Direct</span>
                                            @endif
                                        </div>
                                        <!-- Cabin class - visible on mobile -->
                                        <div class="text-xs text-gray-600 mt-1">
                                            {{ $selectedFlight['travelerPricings'][0]['fareDetailsBySegment'][0]['cabin'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Right Section: Price & Booking -->
                        <div class="col-span-12 lg:col-span-5 flex flex-col justify-between mt-4 lg:mt-0">
                            <div class="flex flex-col justify-end items-end">
                                <!-- Price Information -->
                                <div>
                                    <div class="text-base md:text-xl font-bold text-gray-900">
                                        {{ $selectedFlight['price']['currency'] }}
                                        {{ number_format((float) $selectedFlight['price']['total'] + $selectedFlight['price']['commission'], 2) }}
                                    </div>
                                </div>

                                <!-- Flight Details -->
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">
                                        {{ $selectedFlight['numberOfBookableSeats'] }} seats left
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap justify-end items-end gap-3 mt-4">
                                <button @click="isOpen = !isOpen"
                                    class="inline-flex items-center text-xs md:text-sm px-3 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50">
                                    Flight Details
                                    <svg class="ml-2 h-4 w-4 transition-transform duration-200"
                                        :class="{ 'transform rotate-180': isOpen }"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                {{--
                                <div>
                                    <button wire:loading.remove
                                        wire:target='selectFlight({{ $selectedFlight['id'] }})'
                                class="px-4 md:px-6 py-2 text-xs md:text-sm bg-primary text-white rounded-lg hover:bg-red-700"
                                wire:click="selectFlight({{ $selectedFlight['id'] }})">
                                Select Flight
                                </button>
                                <div wire:loading
                                    wire:target='selectFlight({{ $flight['id'] }})'>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" aria-hidden="true"
                                        class="size-5 fill-neutral-600 motion-safe:animate-spin dark:fill-neutral-300">
                                        <path
                                            d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                            opacity=".25" />
                                        <path
                                            d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                                    </svg>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>


            <!-- Expanded Flight Details -->
            <div x-cloak x-show="isOpen" x-collapse>
                <div class="border-t border-gray-200 bg-gray-50">
                    <div class="p-6 space-y-6">
                        <!-- Tabs Navigation -->
                        <div x-data="{ activeTab: 'flight-info' }" class="mb-6">
                            <div class="border-b border-gray-200">
                                <nav class="flex -mb-px space-x-8">
                                    <button @click="activeTab = 'flight-info'"
                                        :class="{ 'border-primary text-primary': activeTab === 'flight-info', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'flight-info' }"
                                        class="py-4 px-1 border-b-2 font-medium text-sm">
                                        Flight Info
                                    </button>
                                    <button @click="activeTab = 'flight-breakdown'"
                                        :class="{ 'border-primary text-primary': activeTab === 'flight-breakdown', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'flight-breakdown' }"
                                        class="py-4 px-1 border-b-2 font-medium text-sm">
                                        Flight Breakdown
                                    </button>
                                    <button @click="activeTab = 'fare-rules'"
                                        :class="{ 'border-primary text-primary': activeTab === 'fare-rules', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'fare-rules' }"
                                        class="py-4 px-1 border-b-2 font-medium text-sm">
                                        Fare Rules
                                    </button>
                                </nav>
                            </div>

                            <!-- Flight Info Tab Content -->
                            <div x-show="activeTab === 'flight-info'" class="mt-6">
                                <!-- Flight Details Section -->
                                @foreach ($selectedFlight['itineraries'] as $index => $itinerary)
                                <div class="space-y-4">
                                    <h3 class="font-semibold text-gray-900">
                                        {{ $index === 0 ? 'Depature' : 'Return' }} Flight
                                        Details
                                    </h3>

                                    @foreach ($itinerary['segments'] as $segIndex => $segment)
                                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                                        <!-- Segment Header -->
                                        <div class="flex items-center justify-start mb-3 md:mb-0 md:mr-4">
                                            <img src="{{ !empty($itinerary['segments'][0]['airline']['logo'])
                                                            ? 'https://main.mymoretravels.com/storage/' . $itinerary['segments'][0]['airline']['logo']
                                                            : asset('images/default-airline-logo.png') }}"
                                                alt="{{ $segment['airline']['name'] }}"
                                                class="object-contain w-16 h-16 md:w-20 md:h-20">
                                            <div class="text-base md:text-lg font-medium ml-3">
                                                {{ $segment['airline']['name'] ?? 'Unknown Airline' }}
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between mb-4 mt-4">
                                            <div class="flex items-center space-x-4">
                                                <span class="text-sm  font-medium">
                                                    Flight {{ $segment['carrierCode'] }}
                                                    {{ $segment['number'] }}
                                                </span>
                                                <span class="text-xs text-gray-500">
                                                    Aircraft:
                                                    {{ $segment['aircraft']['code'] }}
                                                </span>
                                            </div>
                                            <span class="text-sm text-gray-500">
                                                Duration:
                                                {{ str_replace(['PT', 'H', 'M'], ['', 'h ', 'm'], $segment['duration']) }}
                                            </span>
                                        </div>

                                        <!-- Flight Details -->
                                        <div class="grid grid-cols-2 gap-4">
                                            <!-- Departure -->
                                            <div>
                                                <div class="text-sm text-gray-500">
                                                    Departure</div>
                                                {{-- <div class="font-medium text-xs md:text-base">{{ $segment['departure']['city'] }} --}}
                                                <div class="font-medium text-xs md:text-base">
                                                    {{ $segment['departure']['city_country'] }}
                                                    ({{ $segment['departure']['iataCode'] }})
                                                </div>
                                                <div class="text-sm">Terminal
                                                    {{ $segment['departure']['terminal'] ?? '' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($segment['departure']['at'])->format('D, M d, Y H:i') }}
                                                </div>
                                            </div>

                                            <!-- Arrival -->
                                            <div class="text-right">
                                                <div class="text-sm text-gray-500">Arrival
                                                </div>
                                                <div class="font-medium text-xs md:text-base">
                                                    {{ $segment['arrival']['city_country'] }}
                                                    {{-- {{ $segment['arrival']['city'] }} --}}
                                                    ({{ $segment['arrival']['iataCode'] }})
                                                </div>
                                                <div class="text-sm">Terminal
                                                    {{ $segment['arrival']['terminal'] ?? '' }}
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
                                    $currentArrival = \Carbon\Carbon::parse(
                                    $segment['arrival']['at'],
                                    );
                                    $nextDeparture = \Carbon\Carbon::parse(
                                    $itinerary['segments'][$segIndex + 1]['departure']['at'],
                                    );
                                    $layoverDiff = $currentArrival->diff($nextDeparture);
                                    $layoverHours = $layoverDiff->h;
                                    $layoverMinutes = $layoverDiff->i;
                                    $layoverDuration =
                                    ($layoverHours > 0 ? $layoverHours . 'h ' : '') .
                                    ($layoverMinutes > 0 ? $layoverMinutes . 'm' : '');

                                    $isNightLayover =
                                    $currentArrival->hour >= 18 || $nextDeparture->hour <= 6;
                                        $totalMinutes=$layoverHours * 60 + $layoverMinutes;

                                        if ($layoverHours>= 8) {
                                        $layoverType = $isNightLayover
                                        ? 'Overnight Stop'
                                        : 'Very Long Stopover';
                                        $severityClass = 'bg-red-50 border-red-100';
                                        $textClass = 'text-red-700';
                                        } elseif ($layoverHours >= 6) {
                                        $layoverType = $isNightLayover
                                        ? 'Night Stopover'
                                        : 'Long Stopover';
                                        $severityClass = 'bg-orange-50 border-orange-100';
                                        $textClass = 'text-orange-700';
                                        } elseif ($layoverHours >= 4) {
                                        $layoverType = $isNightLayover
                                        ? 'Evening Break'
                                        : 'Extended Break';
                                        $severityClass = 'bg-blue-50 border-blue-100';
                                        $textClass = 'text-blue-700';
                                        } elseif ($totalMinutes >= 90) {
                                        $layoverType = 'Comfortable Break';
                                        $severityClass = 'bg-green-50 border-green-100';
                                        $textClass = 'text-green-700';
                                        } elseif ($totalMinutes >= 45) {
                                        $layoverType = '45 Min Break - Move Quickly';
                                        $severityClass = 'bg-yellow-50 border-yellow-100';
                                        $textClass = 'text-yellow-700';
                                        } else {
                                        $layoverType = 'Rush Transfer - Very Tight';
                                        $severityClass = 'bg-red-50 border-red-100';
                                        $textClass = 'text-red-700';
                                        }

                                        // Add a warning message for tight transfers
                                        $warningMessage = '';
                                        if ($totalMinutes < 90) {
                                            $warningMessage=$totalMinutes < 45
                                            ? 'This is an extremely tight transfer. Check with your airline if this is feasible.'
                                            : 'Pack light and be prepared to move quickly between gates.' ;
                                            }
                                            @endphp


                                            <div
                                            class="flex items-center gap-3 my-4 px-4 py-3 rounded-lg border {{ $severityClass }}">
                                            <div class="flex-shrink-0">
                                                @if ($isNightLayover)
                                                <svg class="w-5 h-5 {{ $textClass }}"
                                                    fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                                </svg>
                                                @else
                                                <svg class="w-5 h-5 {{ $textClass }}"
                                                    fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                                @endif
                                            </div>

                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="font-medium {{ $textClass }}">{{ $layoverType }}</span>
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $severityClass }} {{ $textClass }}">
                                                        {{ $layoverDuration }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-1 text-sm text-gray-600">
                                                    <svg class="w-4 h-4" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span>{{ $segment['arrival']['iataCode'] }}</span>
                                                    <span class="text-xs text-gray-500">
                                                        {{ $currentArrival->format('H:i') }}
                                                        -
                                                        {{ $nextDeparture->format('H:i') }}
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
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <!-- Price Details -->
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-3">Price Breakdown</h4>
                                    <div class="space-y-4">
                                        <!-- Group travelers by type and count -->
                                        @php
                                        $travelerTypes = [];
                                        foreach ($selectedFlight['travelerPricings'] as $traveler) {
                                        $type = $traveler['travelerType'];
                                        if (!isset($travelerTypes[$type])) {
                                        $travelerTypes[$type] = [
                                        'count' => 0,
                                        'price' => 0,
                                        'currency' => $traveler['price']['currency'],
                                        ];
                                        }
                                        $travelerTypes[$type]['count']++;
                                        $travelerTypes[$type]['price'] += floatval(
                                        $traveler['price']['total'],
                                        );
                                        }
                                        @endphp

                                        <!-- Display each traveler type with count -->
                                        @foreach ($travelerTypes as $type => $data)
                                        <div class="flex justify-between text-sm border-b pb-2">
                                            <span class="text-gray-700 font-medium">
                                                {{ ucfirst(strtolower($type) === 'held_infant' ? 'Infant' : strtolower($type)) }}
                                                {{ $data['count'] }} x
                                            </span>
                                            <span class="font-medium">
                                                {{ $data['currency'] }}
                                                {{ number_format($data['price'], 2) }}
                                            </span>
                                        </div>
                                        @endforeach

                                        <!-- Base fare -->

                                        {{-- <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">Base
                                                        Fare</span>
                                                    <span>{{ $selectedFlight['price']['currency'] }}
                                        {{ number_format((float) $selectedFlight['price']['base'], 2) }}</span>
                                    </div> --}}

                                    {{-- Tax and Fee --}}
                                    {{-- <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">Tax & Fees</span>
                                                    <span>{{ $selectedFlight['price']['currency'] }}
                                    {{ number_format((float) ($selectedFlight['price']['grandTotal'] - $selectedFlight['price']['base']), 2) }}

                                    </span>
                                </div> --}}
                                {{-- <div class="flex justify-between text-sm">
                                                    <span class="text-gray-500">Service charge</span>
                                                    <span>{{ $selectedFlight['price']['currency'] }}

                                {{ number_format($selectedFlight['price']['commission'], 2) }}

                                </span>
                            </div> --}}

                            <!-- Total Price -->
                            <div
                                class="flex justify-between font-medium pt-2 border-t border-gray-200">
                                <span>Total Price</span>
                                <span>{{ $selectedFlight['price']['currency'] }}
                                    {{ number_format((float) $selectedFlight['price']['grandTotal'] + $selectedFlight['price']['commission'], 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Baggage Information -->
                    <div>
                        <h4 class="font-medium text-gray-900 mb-3">Baggage Allowance</h4>

                        <!-- Show baggage allowance by traveler type -->
                        <div class="space-y-4">
                            @foreach ($selectedFlight['travelerPricings'] as $index => $traveler)
                            @if ($index === 0 || $traveler['travelerType'] !== $selectedFlight['travelerPricings'][$index - 1]['travelerType'])
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <h5 class="font-medium text-sm mb-2">
                                    {{ ucfirst(strtolower($traveler['travelerType']) === 'held_infant' ? 'Infant' : strtolower($traveler['travelerType'])) }}
                                </h5>

                                @if (isset($traveler['fareDetailsBySegment'][0]['includedCheckedBags']['quantity']))
                                <div
                                    class="flex items-center space-x-2 text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-green-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>
                                        {{ $traveler['fareDetailsBySegment'][0]['includedCheckedBags']['quantity'] }}
                                        checked bags included
                                    </span>
                                </div>
                                @else
                                <div
                                    class="flex items-center space-x-2 text-sm text-gray-500">
                                    <svg class="w-5 h-5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span>No checked bags included</span>
                                </div>
                                @endif

                                @if (isset($traveler['fareDetailsBySegment'][0]['includedCabinBags']['quantity']))
                                <div
                                    class="flex items-center space-x-2 mt-1 text-sm text-gray-600">
                                    <svg class="w-5 h-5 text-green-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2"
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
                    <h4 class="text-red-600 font-medium mb-4">Important Information</h4>
                    <ul class="space-y-4 text-gray-700">
                        <li class="flex gap-2">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <span class="font-medium">Penalty Applies:</span>
                                <p class="mt-1 text-sm">On some airlines, changes are not permitted
                                    on a ticket in case of no-show and the ticket(s) have no value
                                    and cannot be refunded.</p>
                            </div>
                        </li>
                        <li class="flex gap-2">
                            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <div>
                                <span class="font-medium">Visa Requirements:</span>
                                <p class="mt-1 text-sm">Please ensure that you have a valid visa
                                    before your international travel. 247 travels will not be held
                                    liable by airport authorities if you have not fulfilled your
                                    visa requirements.</p>
                            </div>
                        </li>
                        <li class="flex gap-2">
                            <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <span class="font-medium">Kuala Lumpur Restrictions:</span>
                                <p class="mt-1 text-sm">Tickets to Kuala Lumpur must not exceed 14
                                    days, this simply means all tickets booked to Kuala Lumpur may
                                    be less than 14 days but must not be more than 14 days.</p>
                            </div>
                        </li>
                        <li class="flex gap-2">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            <div>
                                <span class="font-medium">Egypt Air Policy:</span>
                                <p class="mt-1 text-sm">Egypt Air tickets cannot be booked for one
                                    way routes, there should be a RESTRICTION on bookings, NO ONE
                                    WAY on Egypt Air.</p>
                            </div>
                        </li>
                        <li class="flex gap-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <div>
                                <span class="font-medium">Health Concerns:</span>
                                <p class="mt-1 text-sm">If you or anyone accompanying you has a
                                    serious health issue, please call us before booking.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

</div>





<form wire:submit.prevent="createFlightOrder">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Passenger Forms -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Collapsible Rules Sections -->
            <div class="space-y-4">

            </div>
            {{-- Session Error --}}

            <!-- Passenger Information Forms -->
            <div>
                @if (!empty($selectedFlight))
                @foreach ($travelers as $index => $traveler)
                <div
                    class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 transition-all duration-300 hover:shadow-xl">
                    <div class="p-8">
                        <div class="flex justify-between items-center mb-8">
                            <div class="flex items-center gap-4">
                                <div class="p-3 rounded-full bg-primary/10 text-primary">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                </div>
                                <h2 class="md:text-xl text-base font-bold text-gray-800">Traveler -
                                    {{ ucfirst(strtolower($traveler['type']) === 'held_infant' ? 'Infant' : strtolower($traveler['type'])) }}
                                </h2>

                            </div>
                            <button type="button" wire:click="toggleTraveler({{ $index }})"
                                class="px-4 py-2 bg-primary text-white rounded-xl hover:scale-125 text-sm font-medium hover:bg-primary/80 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all duration-200">
                                {{ $traveler['isOpen'] ? 'Hide ' : 'Show ' }}
                            </button>
                        </div>


                        {{-- resources/views/livewire/error-alert.blade.php --}}
                        <div>
                            @if (session('error_details'))
                            @php
                            $errorDetails = session('error_details');
                            @endphp
                            <div x-data="{ show: true }" x-init="setTimeout(() => {
                                                    show = false;
                                                    $wire.clearError();
                                                }, 9000)" x-show="show"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="bg-red-50 border border-red-200 text-sm text-red-800 mt-3 rounded-lg p-4 mb-2 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500"
                                role="alert" tabindex="-1"
                                aria-labelledby="hs-with-list-label">
                                <div class="flex">
                                    <div class="shrink-0">
                                        <svg class="shrink-0 size-4 mt-0.5"
                                            xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="m15 9-6 6"></path>
                                            <path d="m9 9 6 6"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <strong id="hs-with-list-label">Error:</strong>
                                        <ul class="mt-1 space-y-1 list-disc pl-5">
                                            @if (isset($errorDetails['errors']))
                                            @foreach ($errorDetails['errors'] as $error)
                                            <li>{{ $error['detail'] ?? 'An error occurred.' }}
                                            </li>
                                            @endforeach
                                            @else
                                            <li>{{ $errorDetails['error_message'] ?? 'An error occurred.' }}
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>


                        @if ($traveler['isOpen'])
                        <div class="space-y-8 animate-fadeIn">
                            <!-- Personal Information -->
                            <div class="bg-gray-50 rounded-xl p-6 space-y-6 shadow-inner">
                                <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">
                                    Personal Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-2">First
                                            Name</label>
                                        <input type="text"
                                            class="w-full p-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            wire:model.defer="travelers.{{ $index }}.name.firstName"
                                            placeholder="Enter first name">
                                        @error("travelers.{$index}.name.firstName")
                                        <span
                                            class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-2">Middle
                                            Name </label>
                                        <input type="text"
                                            class="w-full p-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            wire:model.defer="travelers.{{ $index }}.name.middleName" placeholder="Enter Middle name">
                                        @error("travelers.{$index}.name.middleName")
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-2">Last
                                            Name</label>
                                        <input type="text"
                                            class="w-full p-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            wire:model.defer="travelers.{{ $index }}.name.lastName" placeholder="Enter Last name">
                                        @error("travelers.{$index}.name.lastName")
                                        <span
                                            class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-2">Date
                                            of Birth</label>
                                        <input type="date"
                                            class="w-full p-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            wire:model.defer="travelers.{{ $index }}.dateOfBirth">
                                        @error("travelers.{$index}.dateOfBirth")
                                        <span
                                            class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                        <select
                                            class="w-full p-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            wire:model.defer="travelers.{{ $index }}.gender">
                                            <option value="">Select Gender</option>
                                            <option value="MALE">Male</option>
                                            <option value="FEMALE">Female</option>

                                        </select>
                                        @error("travelers.{$index}.gender")
                                        <span
                                            class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            @if ($index === 0)
                            <!-- Contact Information -->
                            <div class="bg-gray-50 rounded-xl p-6 space-y-6 shadow-inner">
                                <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">
                                    Contact Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input type="email"
                                            class="w-full p-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            wire:model.defer="travelers.{{ $index }}.contact.emailAddress"
                                            placeholder="Enter email address"
                                            wire:change="$dispatch('syncContactInfo')">
                                        @error("travelers.{$index}.contact.emailAddress")
                                        <span
                                            class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-2">Phone
                                            Number</label>
                                        <input type="text"
                                            class="w-full p-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            wire:model.defer="travelers.{{ $index }}.contact.phones.0.number"
                                            placeholder="Enter phone number"
                                            wire:change="$dispatch('syncContactInfo')">
                                        @error("travelers.{$index}.contact.phones.0.number")
                                        <span
                                            class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @else
                            <!-- Hidden Contact Information for Other Travelers -->
                            <input type="hidden"
                                wire:model="travelers.{{ $index }}.contact.emailAddress">
                            <input type="hidden"
                                wire:model="travelers.{{ $index }}.contact.phones.0.number">
                            @endif
                            <!-- Document Information -->
                            <div class="bg-gray-50 rounded-xl p-6 space-y-6 shadow-inner">
                                <h3 class="text-xl font-semibold text-gray-800 border-b pb-2">
                                    Document Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-2">Passport
                                            Number</label>
                                        <input type="text"
                                            class="w-full p-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            wire:model.defer="travelers.{{ $index }}.documents.0.number"
                                            placeholder="Enter passport number">
                                        @error("travelers.{$index}.documents.0.number")
                                        <span
                                            class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-2">Issuance
                                            Location</label>
                                        <select
                                            class="w-full p-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            wire:model.defer="travelers.{{ $index }}.documents.0.issuanceLocation">
                                            <option value="">Select Country</option>
                                            <option value="US">United States</option>
                                            <option value="NG">Nigeria</option>
                                            <option value="UK">United Kingdom</option>
                                        </select>
                                        @error("travelers.{$index}.documents.0.issuanceLocation")
                                        <span
                                            class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-2">Issuance
                                            Date</label>
                                        <input type="date"
                                            class="w-full p-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            wire:model.defer="travelers.{{ $index }}.documents.0.issuanceDate">
                                        @error("travelers.{$index}.documents.0.issuanceDate")
                                        <span
                                            class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 mb-2">Expiry
                                            Date</label>
                                        <input type="date"
                                            class="w-full p-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                            wire:model.defer="travelers.{{ $index }}.documents.0.expiryDate">
                                        @error("travelers.{$index}.documents.0.expiryDate")
                                        <span
                                            class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @if ($loop->last)
                            <button type="submit"
                                class="w-full bg-primary md:hidden text-white rounded-xl py-4 text-lg font-semibold hover:bg-opacity-90 transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow-lg shadow-primary/25">
                                <span wire:loading.remove>
                                    Continue to Payment
                                </span>
                                <div class="flex items-center justify-center text-center mx-auto"
                                    wire:loading.flex wire:target='createFlightOrder'>
                                    <span>Please Wait</span>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24" aria-hidden="true"
                                        class="size-10 pl-3 fill-white motion-safe:animate-spin">
                                        <path
                                            d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                            opacity=".25" />
                                        <path
                                            d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                                    </svg>
                                </div>
                            </button>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
                @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-8">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Please select a flight first.
                            </p>
                        </div>
                    </div>
                </div>
                @endif


            </div>

        </div>


        <div class="lg:sticky lg:top-8 h-fit">
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200/50 space-y-8">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold">Price Summary</h2>
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Currency -->
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Currency</span>
                        @if (isset($flight_offer_price['currency']))
                        <span class="font-medium">{{ $flight_offer_price['currency'] }}</span>
                        @else
                        <span class="w-16 h-6 bg-gray-300 rounded animate-pulse"></span>
                        @endif
                    </div>

                    <!-- Traveler Breakdown -->
                    @php
                    $travelerTypes = [];
                    $hasTravelerData = false;

                    // Check if $flight_offer_booking and travelerPricings exist
                    if (
                    isset($flight_offer_booking['travelerPricings']) &&
                    is_array($flight_offer_booking['travelerPricings'])
                    ) {
                    foreach ($flight_offer_booking['travelerPricings'] as $traveler) {
                    // Ensure required keys exist
                    if (
                    isset($traveler['travelerType'], $traveler['price']) &&
                    isset($traveler['price']['currency'], $traveler['price']['total'])
                    ) {
                    $type = $traveler['travelerType'];
                    $hasTravelerData = true;

                    // Initialize traveler type if not already set
                    if (!isset($travelerTypes[$type])) {
                    $travelerTypes[$type] = [
                    'count' => 0,
                    'price' => 0,
                    'currency' => $traveler['price']['currency'],
                    ];
                    }

                    // Increment count and add price
                    $travelerTypes[$type]['count']++;
                    $travelerTypes[$type]['price'] += floatval($traveler['price']['total']);
                    }
                    }
                    }
                    @endphp

                    @if ($hasTravelerData)
                    @foreach ($travelerTypes as $type => $data)
                    <div class="flex justify-between text-sm border-b pb-2">
                        <span class="text-gray-700 font-medium">
                            {{ ucfirst(strtolower($type) === 'held_infant' ? 'Infant' : strtolower($type)) }}
                            x {{ $data['count'] }}
                        </span>
                        <span class="font-medium">
                            {{ $data['currency'] }} {{ number_format($data['price'], 2) }}
                        </span>
                    </div>
                    @endforeach
                    @else
                    <!-- Skeleton Loading for Traveler Breakdown -->
                    @for ($i = 0; $i < 3; $i++)
                        <!-- Assuming max 3 traveler types -->
                        <div class="flex justify-between text-sm border-b pb-2">
                            <span class="w-24 h-5 bg-gray-300 rounded animate-pulse"></span>
                            <span class="w-20 h-5 bg-gray-300 rounded animate-pulse"></span>
                        </div>
                        @endfor
                        @endif

                        <!-- Taxes & Fees -->
                        {{-- <div class="flex justify-between items-center text-gray-600">
                                <span>Base Fare</span>
                                @if (isset($flight_offer_price['base']))
                                    <span class="font-medium">

                                        {{ $flight_offer_price['currency'] }}
                        {{ number_format((float) $flight_offer_price['base'], 2) }}
                        </span>
                        @else
                        <span class="w-20 h-6 bg-gray-300 rounded animate-pulse"></span>
                        @endif
                </div> --}}
                {{-- <div class="flex justify-between items-center text-gray-600">
                                <span>Taxes & Fees</span>
                                @if (isset($flight_offer_price['currency']))
                                    <span class="font-medium">

                                        {{ $flight_offer_price['currency'] }}
                {{ number_format((float) ($flight_offer_price['grandTotal'] - $flight_offer_price['base']), 2) }}
                </span>
                @else
                <span class="w-20 h-6 bg-gray-300 rounded animate-pulse"></span>
                @endif
            </div> --}}



            <!-- Commission -->
            {{-- <div class="flex justify-between items-center text-gray-600">
                                <span>Service charge</span>
                                @if (isset($flight_offer_price['currency']) && isset($flight_offer_price['commission']))
                                    <span class="font-medium">
                                        {{ $flight_offer_price['currency'] }}
            {{ number_format($flight_offer_price['commission'], 2) }}
            </span>
            @else
            <span class="w-20 h-6 bg-gray-300 rounded animate-pulse"></span>
            @endif
        </div> --}}

        <!-- Total Amount -->
        <div
            class="flex justify-between items-center pt-4 border-t border-gray-200 text-xl font-bold text-gray-800">
            <span class=" text-xs md:text-xl">Total Amount</span>
            @if (isset($flight_offer_price['currency']) &&
            isset($flight_offer_price['grandTotal']) &&
            isset($flight_offer_price['commission']))
            <span class="text-primary  text-xs md:text-xl">
                {{ $flight_offer_price['currency'] }}
                {{ number_format((float) $flight_offer_price['grandTotal'] + $flight_offer_price['commission'], 2) }}
            </span>
            @else
            <span class="w-32 h-8 bg-gray-300 rounded animate-pulse"></span>
            @endif
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-red-50 border-red-400 p-4 rounded-xl">
            <p class="text-red-700 text-sm flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Book now! Price can change if you come back later.</span>
            </p>
        </div>

        <button type="submit"
            class="w-full hidden md:inline bg-primary text-white rounded-xl py-4 text-lg font-semibold hover:bg-opacity-90 transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow-lg shadow-primary/25">
            <span wire:loading.remove>Continue to Payment</span>
            <div class="flex items-center justify-center text-center mx-auto" wire:loading.flex
                wire:target='createFlightOrder'>
                <span>Please Wait</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"
                    class="size-10 pl-3 fill-white motion-safe:animate-spin">
                    <path
                        d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                        opacity=".25" />
                    <path
                        d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                </svg>
            </div>
        </button>

        <p class="text-center text-sm text-gray-500">
            By clicking continue, you agree to our
            <a href="#" class="text-primary hover:underline">Terms</a>
            and
            <a href="#" class="text-primary hover:underline">Privacy Policy</a>
        </p>
    </div>
    </div>

    @if (session('error_details'))
    @php
    $errorDetails = session('error_details');
    @endphp
    <div class="bg-red-50 border border-red-200 text-sm text-red-800 mt-3 rounded-lg p-4 mb-2 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500"
        role="alert" tabindex="-1" aria-labelledby="hs-with-list-label">
        <div class="flex">
            <div class="shrink-0">
                <svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg"
                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="m15 9-6 6"></path>
                    <path d="m9 9 6 6"></path>
                </svg>
            </div>
            <div class="ml-3">
                <strong id="hs-with-list-label">Error:</strong>
                <ul class="mt-1 space-y-1 list-disc pl-5">
                    @if (isset($errorDetails['errors']))
                    @foreach ($errorDetails['errors'] as $error)
                    <li>{{ $error['detail'] ?? 'An error occurred.' }}</li>
                    @endforeach
                    @else
                    <li>{{ $errorDetails['error_message'] ?? 'An error occurred.' }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    @endif
    </div>

    </div>
</form>

</div>
</div>