<div id="flight-result-page">
    @if($showToSource == "admin")
    @include('components.adminHead')
    <script>
        document.getElementById('hideforAdmid').style.display = 'none';
    </script>
    @endif
    <style>
        .support-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #000000;
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .support-card-icon {
            /* width: 50px;
            height: 50px;
            background: #0d6efd; */
            color: #97050C;
            /* border-radius: 50%;
            align-items: center;
            justify-content: center; */
            font-size: 22px;
        }

        .support-card .text {
            line-height: 1.4;
            color: #fff;
        }

        .support-card .line-one {
            margin: 0;
            font-size: 15px;
            color: #ffffff;
        }

        .support-card .line-two a {
            margin: 4px 0 0;
            font-size: 14px;
            color: #ffffff;
            text-decoration: underline;
            text-decoration-color: #97050C;
            /* underline color */
            text-decoration-thickness: 2px;
            /* optional */
            text-underline-offset: 3px;
            /* optional */
        }

        @media(max-width: 768px) {

            .support-card .line-two a,
            .support-card .line-one,
            .support-card-icon {
                font-size: 12px;
            }

            .displayNoneMobile {
                display: none !important;
            }
        }
    </style>
    @if ($status === 'process')
    <style>
        /* Shimmer effect */
        .skeleton {
            background: linear-gradient(90deg, #e0e0e0 25%, #f0f0f0 50%, #e0e0e0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 10px;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        /* Layout styling */
        .layout {
            display: grid;
            grid-template-columns: 220px 1fr;
            grid-template-rows: 120px 1fr;
            grid-template-areas:
                "sidebar header"
                "sidebar body";
            height: 100vh;
        }

        .sidebar {
            grid-area: sidebar;
            background-color: #fafafa;
            border-right: 1px solid #ddd;
            padding: 1rem;
        }

        .header {
            grid-area: header;
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            padding: 1rem;
        }

        .body {
            grid-area: body;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        /* Full-width skeleton cards */
        .skeleton.card {
            height: 120px;
            width: 100%;
        }

        /* Modal backdrop */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Modal card */
        .modal {
            background: #fff;
            border-radius: 12px;
            width: 400px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .modal.skeleton {
            animation: shimmer 1.5s infinite;
        }

        /* Message fade animation */
        .modal-message {
            font-size: 16px;
            font-weight: 800;
            color: #97050C;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            position: absolute;
            left: 0;
            right: 0;
        }

        .modal-message.active {
            opacity: 1;
            position: relative;
        }

        /* Cancel button */
        .cancel-btn {
            margin-top: 2rem;
            background-color: #97050C;
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 14px;
            border-radius: 8px;
            cursor: pointer;
        }

        .cancel-btn:hover {
            opacity: 0.9;
        }
    </style>

    <style>
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-card {
            background: #fff;
            border-radius: 12px;
            width: 420px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .modal-card h3 {
            font-size: 18px;
            margin-bottom: 1rem;
            color: #444;
            display: block;
        }

        .modal-card h3 span {
            font-weight: bold;
            color: #97050C;
        }

        .flight-image {
            margin: 1.5rem 0;
            text-align: center;
        }

        .route {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin: 1rem 0;
            font-weight: bold;
            font-size: 16px;
        }

        .route small {
            display: block;
            font-size: 13px;
            font-weight: normal;
            color: #666;
        }

        .cancel-btn {
            margin-top: 1.5rem;
            background-color: #97050C;
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 14px;
            border-radius: 8px;
            cursor: pointer;
        }

        .cancel-btn:hover {
            opacity: 0.9;
        }

        .text-white {
            color: unset !important;
        }
    </style>



    <div class="layout" wire:ignore>
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="skeleton" style="height:20px; margin-bottom:15px;"></div>
            <div class="skeleton" style="height:20px; margin-bottom:15px;"></div>
            <div class="skeleton" style="height:20px; margin-bottom:15px;"></div>
            <div class="skeleton" style="height:20px; margin-bottom:15px;"></div>
        </div>

        <!-- Header -->
        <div class="header">
            <h3>Flight Search</h3>
        </div>

        <!-- Body with cards -->
        <div class="body" id="skeleton-container"></div>
    </div>

    <!-- Modal -->
{{--<div class="modal-backdrop" wire:ignore>
	<div class="modal-card">
		<h6>Do not <span>leave</span> this page</h6>
		<!-- Replace src with generated GIF -->
		<div class="flight-image"> <img src="{{asset('images/plane-flying.png')}}" alt="Flying plane" width="200" height="200"> </div> 
		<div id="modal-messages">
			<div class="modal-message active">🔄 Searching for flights...</div>
			<div class="modal-message">🔄 Checking availability...</div>
			<div class="modal-message">🔄 searching for the best flights...</div>
			<div class="modal-message">🔄 Verifying flight details...</div>
			<div class="modal-message">🔄 Comparing ticket prices...</div>
			<div class="modal-message">🔄 Checking seat availability...</div>
			<div class="modal-message">🔄 Looking for the fastest routes...</div>
			<div class="modal-message">🔄 Finding the most convenient options...</div>
			<div class="modal-message">🔄 Matching your travel dates...</div>
			<div class="modal-message">🔄 Optimizing for best value...</div>
			<div class="modal-message">🔄 Confirming airline schedules...</div>
			<div class="modal-message">🔄 Cross-checking with partners...</div>
			<div class="modal-message">🔄 Preparing your flight results...</div>
			<div class="modal-message">🔄 Almost done, please wait...</div>
			<div class="modal-message">🔄 Finalizing your search...</div>
			<div class="modal-message">🔄 Connection lost, retrying please wait...</div>
		</div>
		<div class="route">
			<div> {{$seachNow->from_city_name}}
				<br> <small>{{\Carbon\Carbon::parse($seachNow->departure_date)->format('D, M d')}}</small> </div>
			<div>✈️</div>
			<div> {{$seachNow->to_city_name}}
				<br> <small>{{\Carbon\Carbon::parse($seachNow->return_date)->format('D, M d')}}</small> </div>
		</div>
		<p> {{array_sum($seachNow->passengers)}} passenger, <b style="text-transform: uppercase;">{{$seachNow->cabinClass}} class</b> </p>
		<a href="{{route('landing')}}">
			<button class="cancel-btn">Cancel Search</button>
		</a>
	</div>
</div> --}} 

<script>
    // Populate skeleton cards
    const skeletonContainer = document.getElementById("skeleton-container");
    for (let i = 0; i < 5; i++) {
        const skeletonCard = document.createElement("div");
        skeletonCard.classList.add("skeleton", "card");
        skeletonContainer.appendChild(skeletonCard);
    }

    // Cycle modal messages
    const messages1 = document.querySelectorAll(".modal-message");
    let current = 0;
    setInterval(() => {
        messages1[current].classList.remove("active");
        current = (current + 1) % messages1.length;
        messages1[current].classList.add("active");
    }, 3000);
</script>
@else
<div x-data="{
    showFilters: false
}">
    <!-- Mobile Filter Modal -->
    <div x-cloak x-show="showFilters" class="fixed inset-0 bg-black bg-opacity-50 z-50 md:hidden"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white h-full w-full overflow-y-auto" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full">
            <div class="p-6 space-y-2">
                <!-- Filter content (copy from sidebar) -->
                <!-- Header with close button -->
                <div class="flex justify-between items-center border-b pb-3">
                    <h2 class="text-lg font-semibold">Filters</h2>
                    <button @click="showFilters = false"
                        class="bg-gray-200 text-black rounded-full w-8 h-8 flex items-center justify-center text-xl font-bold">
                        &times;
                    </button>
                </div>
                <div class="block md:hidden w-full    text-black h-screen p-1 border flex-shrink-0">
                    <div class="">
                        <!-- Filter content (unchanged) -->
                        <h2 class="text-lg font-semibold">Filters</h2>
                        @if (isset($searchResults['flightOffers']) &&
                        is_array($searchResults['flightOffers']) &&
                        count($searchResults['flightOffers']))
                        @php
                        // Convert flight offers to a collection
                        $flightOffersCollection = collect($searchResults['flightOffers']);

                        // Extract unique stop options from flight data
                        $stopOptions = $flightOffersCollection
                        ->flatMap(function ($flight) {
                        return collect($flight['itineraries'] ?? []) // Handle missing itineraries
                        ->map(function ($itinerary) {
                        // Calculate total stops for the itinerary
                        return count($itinerary['segments'] ?? []) - 1;
                        })
                        ->unique();
                        })
                        ->unique()
                        ->values();

                        // Convert stop numbers to descriptive strings
                        $stopLabels = [
                        0 => 'Non-stop',
                        1 => '1 Stop',
                        2 => '2 Stops',
                        // Add more as needed
                        ];

                        // Map stop options to their labels
                        $stopOptionsWithLabels = $stopOptions->map(function ($stops) use ($stopLabels) {
                        return ['value' => $stops, 'label' => $stopLabels[$stops] ?? "$stops Stops"];
                        });

                        // Extract unique validating airline codes from flight offers
                        $uniqueAirlines = $flightOffersCollection
                        ->pluck('validatingAirlineCodes')
                        ->flatten()
                        ->unique()
                        ->values();

                        $airlinesWithNames = $uniqueAirlines->map(function ($code) use ($searchResults) {
                        $name = ($searchResults['dictionaries']['carriers'] ?? [])[$code] ?? '';
                        return ['code' => $code, 'name' => $name];
                        });
                        @endphp

                        <!-- Filter by Airlines -->
                        <h3 class="text-base font-medium mb-4">Filter by Airlines</h3>

                        <div class="mb-6 overflow-y-auto border" style="height: 45vh;">
                            @if (isset($searchResults['dictionaries']['carriers']))
                            <ul class="space-y-3">
                                @foreach ($searchResults['dictionaries']['carriers'] as $code => $name)
                                <li>
                                    <label
                                        class="flex items-center space-x-2 text-sm cursor-pointer hover:bg-gray-50 p-2 rounded">
                                        <input type="checkbox" value="{{ $code }}"
                                            class="airline-filter rounded text-primary focus:ring-primary"
                                            onclick="toggleAirline(this)" {{ in_array($code, $selectedAirlines ?? []) ? 'checked' : '' }}>
                                        <span class="text-gray-700">{{ $name }}</span>
                                    </label>
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <p class="text-gray-500 text-sm">No airlines found.</p>
                            @endif
                        </div>

                        <!-- Filter by Number of Stops -->
                        <div class="mb-6">
                            <h3 class="text-base font-medium mb-4">Filter by Number of Stops</h3>
                            <ul class="space-y-3">
                                @foreach ($stopOptionsWithLabels as $option)
                                <li>
                                    <label class=" flex items-center text-[16px] gap-5">
                                        <input type="checkbox" value="{{ $option['value'] }}"
                                            class="stop-filter rounded text-primary focus:ring-primary"
                                            onclick="toggleStopFilter(this)">
                                        {{ $option['label'] }}
                                    </label>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @else
                        <p>No flights found.</p>
                        @endif
                        <div class="mt-6 text-center">
                            <button @click="showFilters = false"
                                class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-red-700 w-full">
                                Close Filters
                            </button>
                        </div>



                        <!-- Price -->
                        {{-- <div class="space-y-2">
                            <label class="block text-sm">Price</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="text" value="₦500,000" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                                <input type="text" value="₦50,000,000" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                            </div>
                        </div> --}}

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Mobile Filter Button (Fixed at bottom left) -->
    <div class="fixed bottom-4 left-4 md:hidden z-[99999999]">
        <button @click="showFilters = !showFilters"
            class="flex items-center justify-center bg-primary text-white rounded-full p-3 shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
        </button>
    </div>

    <div x-data="{
        currentStep: 'additional-info',
        steps: [
            { id: 'search-flight', label: 'Search Flight' },
            { id: 'select-flight', label: 'Select Flight' },
            { id: 'additional-info', label: 'Additional Info' },
            { id: 'payment', label: 'Payment' }
        ],
        isEditOpen: false
    }" class="bg-red sticky overflow-y-auto-rm h-fit md:top-[2%] w-full z-[99]">

        <div class="px-2 sm:px-3 md:px-6 py-4">
            <div class="flex items-center justify-between space-x-2 md:px-6">
                <!-- Title -->
                <div :class="{ 'displaynoe': isEditOpen }" class="text-xs md:text-sm font-bold text-white shrink-0 hidden">
                    Flight Details
                </div>

                <!-- Stepper container -->
                <div x-show="isEditOpen" style="display: none" class="flex-1 hidden md:inline relative py-3  max-w-md mx-auto">
                    <div class="absolute inset-0 flex items-center justify-between px-1">
                        <div class="absolute h-[2px] bg-gray-700 w-full"></div>
                        <div class="absolute h-[2px] bg-black transition-all duration-300"
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

                @if(!empty($searchParams['fromDisplayName']))
                <div class="form-box" x-show="!isEditOpen">
                    <label>Flying from</label>
                    <div class="location">
                        <span>{{$searchParams['fromDisplayName']}}</span>
                    </div>
                </div>
                @endif
                @if(!empty($searchParams['toDisplayName']))
                <div class="form-box" x-show="!isEditOpen">
                    <label>Flying to</label>
                    <div class="location">
                        <span>{{$searchParams['toDisplayName']}}</span>
                    </div>
                </div>
                @endif
                @if(!empty($searchParams['departureDate']))
                <div class="dispNonme form-box" x-show="!isEditOpen">
                    <label>Departure</label>
                    <div class="location">
                        {{--<i class="fa-solid fa-calendar-days calendar-icon"></i>--}}
                        {{\Carbon\Carbon::parse($searchParams['departureDate'])->format('D, M d')}}
                    </div>
                </div>
                @endif
                @if(!empty($searchParams['returnDate']))
                <div class="dispNonme form-box" x-show="!isEditOpen">
                    <label>Return</label>
                    <div class="location">
                        {{--<i class="fa-solid fa-calendar-days calendar-icon"></i>--}}
                        {{\Carbon\Carbon::parse($searchParams['returnDate'])->format('D, M d')}}
                    </div>
                </div>
                @endif
                @if(!empty($searchParams['passengers']))
                <div class="passenger-box dispNonme" x-show="!isEditOpen">
                    Adult {{$searchParams['passengers']['adult'] ?? 0}} &nbsp;&nbsp; Child {{$searchParams['passengers']['child'] ?? 0}} &nbsp;&nbsp; Infant {{$searchParams['passengers']['infant'] ?? 0}} &nbsp; | &nbsp; <strong>Total: {{($searchParams['passengers']['adult'] ?? 0) + ($searchParams['passengers']['child'] ?? 0) + ($searchParams['passengers']['infant'] ?? 0)}}</strong>
                </div>
                @endif

                @if(!empty($searchParams['cabinClass']))
                <div class="btn-group dispNonme" x-show="!isEditOpen">
                    <button class="btn active">Cabin: {{$searchParams['cabinClass']}}</button>
                </div>
                @endif

                <!-- Edit/Cancel Button -->
                <div class="flex items-center justify-end">
                    <button @click="isEditOpen = !isEditOpen"
                        class="px-5 py-3 md:py-3 bg-black text-white rounded-lg flex items-center text-[10px] md:text-xs">
                        <template x-if="!isEditOpen">
                            <span>Edit</span>
                        </template>
                        <template x-if="isEditOpen">
                            <span class="text-lg">&times;</span> <!-- Cancel Icon -->
                        </template>
                    </button>
                </div>
            </div>

            <!-- Edit Section -->
            <div x-show="isEditOpen" x-cloak class="mt-2 heightFindCsna">
                @if (isset($searchParams))
                <livewire:components.search :editMode="true" :from="$searchParams['from'] ?? ''" :to="$searchParams['to'] ?? ''" :fromDisplayName="$searchParams['fromDisplayName'] ?? ''"
                    :toDisplayName="$searchParams['toDisplayName'] ?? ''" :departureDate="$searchParams['departureDate'] ?? null" :returnDate="$searchParams['returnDate'] ?? null" :tripType="$searchParams['tripType'] ?? 'round'" :initialPassengers="$searchParams['passengers'] ?? null"
                    :cabinClass="$searchParams['cabinClass'] ?? 'ECONOMY'" :multiCityFlights="$searchParams['multiCityFlights'] ?? []" />
                @else
                <livewire:components.search />
                @endif
            </div>

        </div>
    </div>



    <!-- Main Container -->
    <div class=" relative w-full  flex flex-col md:flex-row min-h-screen">

        <!-- Sidebar (hidden on mobile) -->
        <div class="hidden md:block w-[20%] bg-white text-black p-1  flex-shrink-0">
            <div class="p-6 space-y-6">
                <!-- Filter content (unchanged) -->
                <h2 class="text-lg font-semibold">Filters</h2>
                @if (isset($searchResults['flightOffers']) &&
                is_array($searchResults['flightOffers']) &&
                count($searchResults['flightOffers']))
                @php
                // Convert flight offers to a collection
                $flightOffersCollection = collect($searchResults['flightOffers']);

                // Extract unique stop options from flight data
                $stopOptions = $flightOffersCollection
                ->flatMap(function ($flight) {
                return collect($flight['itineraries'] ?? []) // Handle missing itineraries
                ->map(function ($itinerary) {
                // Calculate total stops for the itinerary
                return count($itinerary['segments'] ?? []) - 1;
                })
                ->unique();
                })
                ->unique()
                ->values();

                // Convert stop numbers to descriptive strings
                $stopLabels = [
                0 => 'Non-stop',
                1 => '1 Stop',
                2 => '2 Stops',
                // Add more as needed
                ];

                // Map stop options to their labels
                $stopOptionsWithLabels = $stopOptions->map(function ($stops) use ($stopLabels) {
                return ['value' => $stops, 'label' => $stopLabels[$stops] ?? "$stops Stops"];
                });

                // Extract unique validating airline codes from flight offers
                $uniqueAirlines = $flightOffersCollection
                ->pluck('validatingAirlineCodes')
                ->flatten()
                ->unique()
                ->values();

                $airlinesWithNames = $uniqueAirlines->map(function ($code) use ($searchResults) {
                $name = ($searchResults['dictionaries']['carriers'] ?? [])[$code] ?? '';
                return ['code' => $code, 'name' => $name];
                });
                @endphp

                <!-- Filter by Airlines -->
                <h3 class="text-base font-medium mb-4">Filter by Airlines</h3>
                <div class="mb-6 overflow-y-auto border" style="height: 45vh;">
                    @if (isset($searchResults['dictionaries']['carriers']))
                    <ul class="space-y-3">
                        @foreach ($searchResults['dictionaries']['carriers'] as $code => $name)
                        <li>
                            <label
                                class="flex items-center space-x-2 text-sm cursor-pointer hover:bg-gray-50 p-2 rounded">
                                <input type="checkbox" value="{{ $code }}"
                                    class="airline-filter rounded text-primary focus:ring-primary"
                                    onclick="toggleAirline(this)" {{ in_array($code, $selectedAirlines ?? []) ? 'checked' : '' }}>
                                <span class="text-gray-700">{{ $name }}</span>
                            </label>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-gray-500 text-sm">No airlines found.</p>
                    @endif
                </div>

                <!-- Filter by Number of Stops -->
                <div class="mb-6">
                    <h3 class="text-base font-medium mb-4">Filter by Number of Stops</h3>
                    <ul class="space-y-3">
                        @foreach ($stopOptionsWithLabels as $option)
                        <li>
                            <label class=" flex items-center text-[16px] gap-5">
                                <input type="checkbox" value="{{ $option['value'] }}"
                                    class="stop-filter rounded text-primary focus:ring-primary"
                                    onclick="toggleStopFilter(this)">
                                {{ $option['label'] }}
                            </label>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @else
                <p>No flights found.</p>
                @endif



                <!-- Price -->
                {{-- <div class="space-y-2">
                    <label class="block text-sm">Price</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="text" value="₦500,000" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                        <input type="text" value="₦50,000,000" class="rounded-lg border border-gray-200 px-3 py-2 text-sm">
                    </div>
                </div> --}}




            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 w-full justify-center items-center">
            <!-- Header -->

            <!-- Property Grid -->
            <!-- Property Grid -->
            <div class="md:p-3 p-2  text-black">
                <!-- Sort by Cheapest and Fastest -->

                <!-- Modal Structure -->
                <div class="modal-overlay" id="modalOverlay"></div>

                <div class="modal" id="modal">
                    <div class="modal-header">
                        <h3>Edit Panel</h3>
                        <button class="modal-close" id="modalCloseBtn">&times;</button>
                    </div>
                    <div class="modal-body" id="modalBody">
                        <!-- Dynamic content goes here -->
                    </div>
                </div>
                <div class="w-full mb-4">
                    <div>
                        <h3 class="text-base font-medium mb-4">Sort By</h3>
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
                            <div class="flex space-x-4">
                                <!-- Cheapest Button -->
                                <!-- Cheapest Button -->
                                <button onclick="sortFlights('cheapest')" id="cheapest-btn"
                                    class="px-4 py-2  rounded-md bg-primary text-white">
                                    Cheapest
                                </button>

                                <!-- Fastest Button -->
                                <button onclick="sortFlights('fastest')" id="fastest-btn"
                                    class="px-4 py-2 rounded-md  text-black">
                                    Fastest
                                </button>
                            </div>

                            <div class="support-card">

                                <div class="text">
                                    <p class="line-one">
                                        <strong>Our customer service team is happy to help you get the best deal</strong>
                                    </p>
                                    <p class="line-two">
                                        <span class="support-card-icon displayNoneMobile"><i class="fa-solid fa-headset"></i></span>
                                        <a href="https://wa.me/2348022008793" target="_blank"><strong>WhatsApp: 08022008793</strong></a> |
                                        <a href="tel:08022888721" target="_blank"><strong>Call: 08022888721</strong></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if(isset($searchResults['cheapestFlight']) && is_array($searchResults['cheapestFlight']) && count($searchResults['cheapestFlight']))
                <div class="table-wrapper">
                    <!-- Fixed Left Column -->
                    <div class="fixed-column">
                        <div class="cell header-cell" style="color: #F0F8FF;">-</div>
                        <div class="cell">Non-stop</div>
                        <div class="cell">1 Stop</div>
                        <div class="cell">2 Stops</div>
                    </div>

                    <!-- Scrollable Airline Columns -->
                    <div class="scrollable-columns" id="scrollableTable">
                        <div class="column" data-airline-code="" style="cursor: pointer;" onclick="toggleAirlineCheap('')">
                            <div class="cell header-cell">All Airlines</div>
                            <div class="cell">—</div>
                            <div class="cell">—</div>
                            <div class="cell">—</div>
                        </div>
                        @if(is_array($searchResults['cheapestFlight']))
                        @foreach($searchResults['cheapestFlight'] as $cheapflight)
                        <div class="column" data-airline-code="{{ $cheapflight['code'] }}" style="cursor: pointer;">
                            <div class="cell header-cell" onclick="toggleAirlineCheap('{{ $cheapflight['code'] }}')">
                                <img src="{{ !empty($cheapflight['logo'])
                                                                        ? url('/').'/storage/' . $cheapflight['logo']
                                                                        : asset('images/default-airline-logo.png') }}"
                                    alt="{{ $cheapflight['name'] }}" style="height: 20px; width: 20px; display:unset;">
                                {{$cheapflight['name']}}
                            </div>
                            @if(is_array($cheapflight['price']))
                            <div class="cell" onclick="handleCellClick(this, '{{ $cheapflight['code'] }}')">
                                @if(isset($cheapflight['price'][0]))
                                <label class=" flex items-center text-[16px] gap-5">
                                    <input type="checkbox" value="0" class="table-stop-filter" style="display: none;">
                                </label>
                                {{
                                 $cheapflight['price'][0] ? $cheapflight['currency']. " ". number_format((float) $cheapflight['price'][0], 2) : '-'
                                }}
                                @else
                                -
                                @endif
                            </div>

                            <div class="cell" onclick="handleCellClick(this, '{{ $cheapflight['code'] }}')">
                                @if(isset($cheapflight['price'][1]))
                                <label class=" flex items-center text-[16px] gap-5">
                                    <input type="checkbox" value="1" class="table-stop-filter" style="display: none;">
                                </label>
                                {{
                                 $cheapflight['price'][1] ? $cheapflight['currency']. " ". number_format((float) $cheapflight['price'][1], 2) : '-'
                                }}
                                @else
                                -
                                @endif
                            </div>
                            <div class="cell" onclick="handleCellClick(this, '{{ $cheapflight['code'] }}')">
                                @if(isset($cheapflight['price'][2]))
                                <label class=" flex items-center text-[16px] gap-5">
                                    <input type="checkbox" value="2" class="table-stop-filter" style="display: none;">
                                </label>
                                {{
                                 $cheapflight['price'][2] ? $cheapflight['currency']. " ". number_format((float) $cheapflight['price'][2], 2) : '-'
                                }}
                                @else
                                -
                                @endif
                            </div>
                            {{--<div class="cell">{{isset($cheapflight['price'][1]) ? $cheapflight['currency']. " ".number_format((float) $cheapflight['price'][1], 2) : '-'}}
                        </div>
                        <div class="cell">{{isset($cheapflight['price'][2]) ? $cheapflight['currency']. " ".number_format((float) $cheapflight['price'][2], 2) : '-'}}</div>--}}
                        @else
                        <div class="cell">—</div>
                        <div class="cell">—</div>
                        @endif
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            <!-- Navigation -->
            <div class="nav-buttons">
                <button type="button" class="btn" id="prevBtn" disabled>⬅ Show less airlines</button>
                <button type="button" class="btn" id="nextBtn">Show more airlines ➡</button>
            </div>
            @endif

            <div style="margin-top: 20px; margin-bottom: 20px">
                @if ($status === 'processing' && !$allResultsLoaded)
                <div>
                    <div style="color:#000; text-align:center;">Please wait!</div>
                    <button
                        class="loading-message block mx-auto px-4 py-2 bg-primary text-white rounded-md mt-4"
                        disabled
                        style="opacity:0.5; cursor:not-allowed;">
                        🔄 searching for more flights..
                    </button>
                </div>
                @endif
            </div>

            <div class=" space-y-4" id="flight-list">
                @if (!empty($paginatedFlightOffers))
                <div id="flight-offers-container">
                    @foreach ($paginatedFlightOffers as $index => $flight)
                    @php
                    $stopNm = $flight['totalStops'] ?? 0;
                    @endphp
                    <div id="flightcard{{ $flight['id'] ?? ($index + 1) }}" wire:key="flight-offer-{{ $flight['id'] ?? $index }}"
                        data-airline="{{ $flight['validatingAirlineCodes'][0] }}"
                        data-total-stops="{{ $stopNm }}"
                        data-price="{{ $flight['totalPrice'] ?? floatval($flight['price']['grandTotal'] ?? $flight['price']['total'] ?? 0) }}"
                        data-duration="{{ $flight['totalDuration'] ?? 0 }}"
                        class="flight-offer bg-white rounded-xl shadow-2xl border-b border-t border-dotted-2 mb-6 overflow-hidden  border-primary">
                        <div x-data="{ isOpen: false }">

                            <!-- Main Flight Information -->
                            <div class="p-6">
                                <div class="grid grid-cols-12 deskotGap">
                                    <!-- Left Section: Flight Route -->
                                    <div class="col-span-12 lg:col-span-7">
                                        @foreach ($flight['itineraries'] as $index => $itinerary)
                                        <div class="mb-6 last:mb-0">
                                            <!-- Mobile Flight Header - Logo on left side -->
                                            <div class="flex md:hidden items-center mb-4">
                                                <!-- Logo on left -->
                                                <div class="flex-shrink-0 mr-3">
                                                    <img src="{{ !empty($itinerary['segments'][0]['airline']['logo'])
                                                                        ? url('/').'/storage/' . $itinerary['segments'][0]['airline']['logo']
                                                                        : asset('images/plane-flying.png') }}"
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
                                            <div
                                                class="flex flex-row md:items-center justify-between md:gap-4">
                                                <!-- Airline logo - Desktop only -->
                                                <div
                                                    class="hidden md:flex flex-col items-center justify-center">
                                                    <img src="{{ !empty($itinerary['segments'][0]['airline']['logo'])
                                                                        ? url('/').'/storage/' . $itinerary['segments'][0]['airline']['logo']
                                                                        : asset('images/default-airline-logo.png') }}"
                                                        alt="{{ $itinerary['segments'][0]['airline']['name'] }}"
                                                        class="object-contain w-12 h-12">
                                                    <div class="text-xs font-medium text-center">
                                                        {{ $itinerary['segments'][0]['airline']['name'] ?? 'Unknown Airline' }}
                                                    </div>
                                                </div>

                                                <!-- Departure -->
                                                <div class="flex-shrink-0 text-left mdwauto md:w-auto">
                                                    <div
                                                        class="text-xs font-bold line-clamp-2 md:line-clamp-none">
                                                        {{ $itinerary['segments'][0]['departure']['city'] }}
                                                        ({{ $itinerary['segments'][0]['departure']['iataCode'] }})
                                                    </div>
                                                    <div class="font-sm10 text-gray-500">
                                                        {{ \Carbon\Carbon::parse($itinerary['segments'][0]['departure']['at'])->format('H:i') }}
                                                        {{--</div>
                                                        <div class="text-xs text-gray-400">--}}
                                                        {{ \Carbon\Carbon::parse($itinerary['segments'][0]['departure']['at'])->format('D, M d') }}
                                                    </div>
                                                </div>

                                                <!-- Arrow for mobile -->
                                                {{--<div
                                                        class="flex items-center self-center text-gray-400 flex-shrink-0 mobiledisplayNone">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-6 w-6" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                                        </svg>
                                                    </div>--}}

                                                <!-- Flight Path - Desktop only -->
                                                <div class="flex-1 px-4">
                                                    <div class="relative">
                                                        <div class="h-px bg-gray-300 w-full"></div>
                                                        <div
                                                            class="absolute inset-0 flex items-center justify-center">
                                                            <span
                                                                class="bg-white px-2 text-xs text-gray-500 font-sm10">
                                                                {{ str_replace(['PT', 'H', 'M'], ['', 'h ', 'm'], $itinerary['duration']) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <!-- Stops Indicator -->
                                                    <div
                                                        class="text-center flex flex-col items-center mt-1">
                                                        @if (count($itinerary['segments']) > 1)
                                                        <span
                                                            class="text-xs flex-shrink-0 text-orange-600 font-sm8">
                                                            {{ count($itinerary['segments']) - 1 }}
                                                            stop(s)
                                                        </span>
                                                        <span class="text-xs font-sm10 font-bold">
                                                            {{ $flight['travelerPricings'][0]['fareDetailsBySegment'][0]['cabin'] }}
                                                        </span>
                                                        @else
                                                        <span
                                                            class="text-xs text-green-600 font-sm10">Direct</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Arrival -->
                                                <div
                                                    class="flex-shrink-0 text-right mdwauto md:w-auto">
                                                    @php
                                                    $lastSegment = end($itinerary['segments']);
                                                    @endphp
                                                    <div
                                                        class="text-xs font-bold line-clamp-2 md:line-clamp-none">
                                                        {{ $lastSegment['arrival']['city'] }}
                                                        ({{ $lastSegment['arrival']['iataCode'] }})
                                                    </div>
                                                    <div class="font-sm10 text-gray-500">
                                                        {{ \Carbon\Carbon::parse($lastSegment['arrival']['at'])->format('H:i') }}
                                                        {{--</div>
                                                        <div class="text-xs text-gray-400">--}}
                                                        {{ \Carbon\Carbon::parse($lastSegment['arrival']['at'])->format('D, M d') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Flight details below arrow - Mobile only -->
                                            <div
                                                class="flex md:hidden justify-center items-center mt-3 mb-2">
                                                {{--<div class="text-center">
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
                                                <span class="text-xs text-gray-600 mt-1">
                                                    {{ $flight['travelerPricings'][0]['fareDetailsBySegment'][0]['cabin'] }}
                                                </span>
                                            </div>
                                        </div>--}}
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Right Section: Price & Booking -->
                            <div
                                class="col-span-12 lg:col-span-5 flex flex-col justify-between deskotMaerp lg:mt-0">

                                <div class="flex flexdcolmrow justify-end items-end">
                                    <!-- Price Information -->
                                    <div>
                                        <div class="text-base md:text-xl font-bold text-gray-900">
                                            {{ $flight['price']['currency'] }}
                                            @php
                                            $main = $flight['price']['total'];
                                            $mark = $flight['price']['total'] + $flight['price']['commission'];
                                            $ismark = $main - $mark;
                                            @endphp
                                            {{ number_format((float) $mark, 2) }}
                                            @if($showToSource == "admin" && $ismark < 0)
                                                <span style="font-size: 10px;">markup</span>
                                                @endif
                                                @if($showToSource == "admin" && $ismark > 0)
                                                <span style="font-size: 10px;">markdown</span>
                                                @endif
                                        </div>
                                        @if($showToSource == "admin" && $ismark != 0)
                                        <div class="text-base md:text-xl font-bold text-gray-900">
                                            {{ $flight['price']['currency'] }}
                                            {{ number_format((float) $main, 2) }}
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Flight Details -->
                                    <div class="text-right ml-2">
                                        <div class="text-sm text-gray-500">
                                            {{ $flight['numberOfBookableSeats'] }} seats left
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-wrap justify-end items-end gap-3 mt-4">
                                    {{--@click="isOpen = !isOpen"--}}
                                    <button
                                        class="inline-flex items-center text-xs md:text-sm px-3 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 details-btn" data-modal="modal-<?= $flight['id'] ?>">
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

                                    <div>
                                        <button wire:loading.remove
                                            wire:target='selectFlight({{ $flight['id'] }})'
                                            class="px-4 md:px-6 py-2 text-xs md:text-sm bg-primary text-white rounded-lg hover:bg-red-700"
                                            wire:click="selectFlight({{ $flight['id'] }})">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @include('livewire.page.flight-modal', ['flight' => $flight, 'index' => $index, 'selectedFlightNow' => true])
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
                                        @foreach ($flight['itineraries'] as $index => $itinerary)
                                        <div class="space-y-4">
                                            <h3 class="font-semibold text-gray-900">
                                                {{ $index === 0 ? 'Depature' : 'Return' }}
                                                Flight
                                                Details
                                            </h3>

                                            @foreach ($itinerary['segments'] as $segIndex => $segment)
                                            <div class="bg-white rounded-lg p-4 border border-gray-200"
                                                data-carrier-code="{{ $segment['carrierCode'] }}">
                                                <!-- Segment Header -->
                                                <div
                                                    class="flex items-center justify-start mb-3 md:mb-0 md:mr-4">
                                                    <img src="{{ !empty($itinerary['segments'][0]['airline']['logo'])
                                                                                    ? url('/').'/storage/' . $itinerary['segments'][0]['airline']['logo']
                                                                                    : asset('images/default-airline-logo.png') }}"
                                                        alt="{{ $segment['airline']['name'] }}"
                                                        class="object-contain w-16 h-16 md:w-20 md:h-20">
                                                    <div
                                                        class="text-base md:text-lg font-medium ml-3">
                                                        {{ $segment['airline']['name'] ?? 'Unknown Airline' }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex items-center justify-between mb-4 mt-4">
                                                    <div
                                                        class="flex items-center space-x-4">
                                                        <span class="text-sm  font-medium">
                                                            Flight
                                                            {{ $segment['carrierCode'] }}
                                                            {{ $segment['number'] }}
                                                        </span>
                                                        <span
                                                            class="text-xs text-gray-500">
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
                                                        <div
                                                            class="font-medium text-xs md:text-base">
                                                            {{ $segment['departure']['city'] }}
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
                                                        <div class="text-sm text-gray-500">
                                                            Arrival
                                                        </div>
                                                        <div
                                                            class="font-medium text-xs md:text-base">
                                                            {{ $segment['arrival']['city'] }}
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
                                            $itinerary['segments'][
                                            $segIndex + 1
                                            ]['departure']['at'],
                                            );
                                            $layoverDiff = $currentArrival->diff(
                                            $nextDeparture,
                                            );
                                            $layoverHours = $layoverDiff->h;
                                            $layoverMinutes = $layoverDiff->i;
                                            $layoverDuration =
                                            ($layoverHours > 0
                                            ? $layoverHours . 'h '
                                            : '') .
                                            ($layoverMinutes > 0
                                            ? $layoverMinutes . 'm'
                                            : '');

                                            $isNightLayover =
                                            $currentArrival->hour >= 18 ||
                                            $nextDeparture->hour <= 6;
                                                $totalMinutes=$layoverHours * 60 +
                                                $layoverMinutes;

                                                if ($layoverHours>= 8) {
                                                $layoverType = $isNightLayover
                                                ? 'Overnight Stop'
                                                : 'Very Long Stopover';
                                                $severityClass =
                                                'bg-red-50 border-red-100';
                                                $textClass = 'text-red-700';
                                                } elseif ($layoverHours >= 6) {
                                                $layoverType = $isNightLayover
                                                ? 'Night Stopover'
                                                : 'Long Stopover';
                                                $severityClass =
                                                'bg-orange-50 border-orange-100';
                                                $textClass = 'text-orange-700';
                                                } elseif ($layoverHours >= 4) {
                                                $layoverType = $isNightLayover
                                                ? 'Evening Break'
                                                : 'Extended Break';
                                                $severityClass =
                                                'bg-blue-50 border-blue-100';
                                                $textClass = 'text-blue-700';
                                                } elseif ($totalMinutes >= 90) {
                                                $layoverType = 'Comfortable Break';
                                                $severityClass =
                                                'bg-green-400 border-green-100';
                                                $textClass = 'text-black';
                                                } elseif ($totalMinutes >= 45) {
                                                $layoverType =
                                                '45 Min Break - Move Quickly';
                                                $severityClass =
                                                'bg-yellow-50 border-yellow-100';
                                                $textClass = 'text-yellow-700';
                                                } else {
                                                $layoverType =
                                                'Rush Transfer - Very Tight';
                                                $severityClass =
                                                'bg-red-50 border-red-100';
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
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                                        </svg>
                                                        @else
                                                        <svg class="w-5 h-5 {{ $textClass }}"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                                        </svg>
                                                        @endif
                                                    </div>

                                                    <div class="flex flex-col">
                                                        <div
                                                            class="flex items-center gap-2 font-bold">
                                                            <span
                                                                class="font-medium {{ $textClass }}">{{ $layoverType }}</span>
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $severityClass }} {{ $textClass }}">
                                                                {{ $layoverDuration }}
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="flex items-center gap-1 text-sm text-gray-600">
                                                            <svg class="w-4 h-4"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            </svg>
                                                            <span
                                                                class="text-black font-bold">{{ $segment['arrival']['iataCode'] }}</span>
                                                            <span
                                                                class="text-xs text-black font-bold">
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
                                    <div class="grid grid-cols-1 lg:grid-cols-2 w-full gap-6">
                                        <!-- Price Details -->
                                        <div>
                                            <h4 class="font-medium text-gray-900 mb-3">Price
                                                Breakdown</h4>
                                            <div class="space-y-4">
                                                <!-- Group travelers by type and count -->
                                                @php
                                                $travelerTypes = [];
                                                foreach (
                                                $flight['travelerPricings']
                                                as $traveler
                                                ) {
                                                $type = $traveler['travelerType'];
                                                if (!isset($travelerTypes[$type])) {
                                                $travelerTypes[$type] = [
                                                'count' => 0,
                                                'price' => 0,
                                                'currency' =>
                                                $traveler['price'][
                                                'currency'
                                                ],
                                                ];
                                                }
                                                $travelerTypes[$type]['count']++;
                                                $travelerTypes[$type][
                                                'price'
                                                ] += floatval(
                                                $traveler['price']['total'],
                                                );
                                                }
                                                @endphp

                                                <!-- Display each traveler type with count -->
                                                @foreach ($travelerTypes as $type => $data)
                                                <div
                                                    class="flex justify-between text-sm border-b pb-2">

                                                    <span
                                                        class="text-gray-700 font-medium">
                                                        {{ ucfirst(strtolower($type) === 'held_infant' ? 'Infant' : strtolower($type)) }}
                                                        x
                                                        {{ $data['count'] }}
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
                                                                            <span>{{ $flight['price']['currency'] }}
                                                {{ number_format((float) $flight['price']['base'], 2) }}</span>
                                            </div> --}}

                                            {{-- Tax and Fee --}}
                                            {{-- <div class="flex justify-between text-sm">
                                                                            <span class="text-gray-500">Tax &
                                                                                Fees</span>
                                                                            <span>{{ $flight['price']['currency'] }}
                                            {{ number_format((float) ($flight['price']['grandTotal'] - $flight['price']['base']), 2) }}
                                            </span>
                                        </div> --}}

                                        {{-- Service Charge --}}
                                        {{-- <div class="flex justify-between text-sm">
                                                                            <span class="text-gray-500">Service
                                                                                charge</span>
                                                                            <span>
                                                                                {{ $data['currency'] }}
                                        {{ number_format($flight['price']['commission'], 2) }}


                                        </span>
                                    </div> --}}

                                    <!-- Fees -->
                                    {{-- @foreach ($flight['price']['fees'] as $fee)
                                                                            <div class="flex justify-between text-sm">
                                                                                <span class="text-gray-500">{{ ucfirst(strtolower($fee['type'])) }}</span>
                                    <span>{{ $flight['price']['currency'] }}
                                        {{ number_format((float) $fee['amount'], 2) }}</span>
                                </div>
                                @endforeach --}}

                                <!-- Total Price -->
                                <div
                                    class="flex justify-between font-medium pt-2 border-t border-gray-200">
                                    <span>Total Price</span>
                                    <span>{{ $flight['price']['currency'] }}
                                        {{ number_format((float) $flight['price']['grandTotal'] + $flight['price']['commission'], 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Baggage Information -->
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">Baggage
                                Allowance</h4>

                            <!-- Show baggage allowance by traveler type -->
                            <div class="space-y-4">
                                @foreach ($flight['travelerPricings'] as $index => $traveler)
                                @if ($index === 0 || $traveler['travelerType'] !== $flight['travelerPricings'][$index - 1]['travelerType'])
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <h5
                                        class="font-medium text-sm mb-2">
                                        {{ ucfirst(strtolower($traveler['travelerType']) === 'held_infant' ? 'Infant' : strtolower($traveler['travelerType'])) }}
                                    </h5>



                                    @if (isset($traveler['fareDetailsBySegment'][0]['includedCheckedBags']['quantity']))
                                    <div
                                        class="flex items-center space-x-2 text-sm text-gray-600">
                                        <svg class="w-5 h-5 text-green-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
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
                                        <svg class="w-5 h-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>No checked bags
                                            included</span>
                                    </div>
                                    @endif

                                    @if (isset($traveler['fareDetailsBySegment'][0]['includedCabinBags']['quantity']))
                                    <div
                                        class="flex items-center space-x-2 mt-1 text-sm text-gray-600">
                                        <svg class="w-5 h-5 text-green-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
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

                        <!-- Additional Information -->

                    </div>

                </div>

                <!-- Fare Rules Tab Content -->
                <div x-show="activeTab === 'fare-rules'" class="mt-6">
                    <div
                        class="bg-white rounded-lg p-6 border border-gray-200">
                        <h4 class="text-red-600 font-medium mb-4">Important
                            Information</h4>
                        <ul class="space-y-4 text-gray-700">
                            <li class="flex gap-2">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"
                                    fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <span class="font-medium">Penalty
                                        Applies:</span>
                                    <p class="mt-1 text-sm">On some airlines,
                                        changes are not permitted on a ticket in
                                        case of no-show and the ticket(s) have
                                        no value and cannot be refunded.</p>
                                </div>
                            </li>
                            <li class="flex gap-2">
                                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5"
                                    fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <div>
                                    <span class="font-medium">Visa
                                        Requirements:</span>
                                    <p class="mt-1 text-sm">Please ensure that
                                        you have a valid visa before your
                                        international travel. 247 travels will
                                        not be held liable by airport
                                        authorities if you have not fulfilled
                                        your visa requirements.</p>
                                </div>
                            </li>
                            <li class="flex gap-2">
                                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5"
                                    fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <span class="font-medium">Kuala Lumpur
                                        Restrictions:</span>
                                    <p class="mt-1 text-sm">Tickets to Kuala
                                        Lumpur must not exceed 14 days, this
                                        simply means all tickets booked to Kuala
                                        Lumpur may be less than 14 days but must
                                        not be more than 14 days.</p>
                                </div>
                            </li>
                            <li class="flex gap-2">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"
                                    fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                <div>
                                    <span class="font-medium">Egypt Air
                                        Policy:</span>
                                    <p class="mt-1 text-sm">Egypt Air tickets
                                        cannot be booked for one way routes,
                                        there should be a RESTRICTION on
                                        bookings, NO ONE WAY on Egypt Air.</p>
                                </div>
                            </li>
                            <li class="flex gap-2">
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5"
                                    fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <div>
                                    <span class="font-medium">Health
                                        Concerns:</span>
                                    <p class="mt-1 text-sm">If you or anyone
                                        accompanying you has a serious health
                                        issue, please call us before booking.
                                    </p>
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
            <button wire:loading.remove
                wire:target='selectFlight({{ $flight['id'] }})'
                class="px-6 py-2 md:text-base text-xs bg-primary text-white rounded-lg hover:bg-red-700"
                wire:click="selectFlight({{ $flight['id'] }})">
                Select Flight
            </button>
            <div wire:loading wire:target='selectFlight({{ $flight['id'] }})'>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                    aria-hidden="true"
                    class="size-5 fill-neutral-600 motion-safe:animate-spin dark:fill-neutral-300">
                    <path
                        d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                        opacity=".25" />
                    <path
                        d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                </svg>
            </div>


        </div>
        <button class=" bg-primary rounded-lg p-2 text-white"
            @click="isOpen = !isOpen"> close</button>
    </div>


</div>

</div>

</div>
@endforeach
</div>
</div>

@if (($totalPages ?? 1) > 1)
<div class="nav-buttons" style="margin-top: 20px; margin-bottom: 20px;">
    <button type="button" class="btn" wire:click="previousFlightPage" @disabled(($currentPage ?? 1) <= 1)>⬅ Previous</button>
    <span style="align-self: center;">Page {{ $currentPage ?? 1 }} of {{ $totalPages ?? 1 }}</span>
    <button type="button" class="btn" wire:click="nextFlightPage" @disabled(($currentPage ?? 1) >= ($totalPages ?? 1))>Next ➡</button>
</div>
@endif

<!-- Load More Button -->
@else
<p>No flights found.</p>
@endif

</div>
@if ($status === 'processing' && !$allResultsLoaded)
<div style="margin-top: 20px; margin-bottom: 20px">
    <div style="color:#000; text-align:center;">Please wait!</div>
    <button
        class="loading-message block mx-auto px-4 py-2 bg-primary text-white rounded-md mt-4"
        disabled
        style="opacity:0.5; cursor:not-allowed;">
        🔄 searching for more flights..
    </button>
</div>
@endif
@if ($status === 'completed' && !$allResultsLoaded)
<button wire:click="loadMoreFlights" wire:loading.attr="disabled" id="load-more-btn" class="block mx-auto px-4 py-2 bg-primary text-white rounded-md mt-4 ">
    <b wire:loading.remove wire:target="loadMoreFlights">Load More</b>
    <span wire:loading wire:target="loadMoreFlights">Loading...</span>
</button>
@endif
</div>
</div>

<script>
    // All your messages
    const messages = [
        "🔄 Generating more flight options, please wait...",
        "🔄 Still checking availability, hang tight...",
        "🔄 Searching for additional flights...",
        "🔄 Verifying more flight details...",
        "🔄 Comparing extra ticket prices...",
        "🔄 Checking more seat availability...",
        "🔄 Looking for faster routes...",
        "🔄 Finding more convenient options...",
        "🔄 Matching your travel dates with more flights...",
        "🔄 Optimizing results for best value...",
        "🔄 Confirming airline schedules...",
        "🔄 Cross-checking with more partners...",
        "🔄 Preparing additional flight results...",
        "🔄 Almost done, generating final results...",
        "🔄 Finalizing your flight search...",
        "🔄 Connection interrupted, retrying — please wait..."
    ];

    // Shuffle helper
    function shuffle(array) {
        return array.sort(() => Math.random() - 0.5);
    }

    // Rotate waiting messages only while the loading button is visible
    const loadingMessageIntervals = [];

    function startLoadingMessageRotation() {
        stopLoadingMessageRotation();

        document.querySelectorAll('.loading-message').forEach((messageBox) => {
            const shuffledMessages = shuffle([...messages]);
            let index = 0;

            const intervalId = setInterval(() => {
                if (!document.body.contains(messageBox)) {
                    clearInterval(intervalId);
                    return;
                }

                messageBox.textContent = shuffledMessages[index];
                index = (index + 1) % shuffledMessages.length;
            }, 3000);

            loadingMessageIntervals.push(intervalId);
        });
    }

    function stopLoadingMessageRotation() {
        loadingMessageIntervals.forEach((intervalId) => clearInterval(intervalId));
        loadingMessageIntervals.length = 0;
    }

    function initLoadingMessages() {
        if (document.querySelector('.loading-message')) {
            startLoadingMessageRotation();
        } else {
            stopLoadingMessageRotation();
        }
    }

    document.addEventListener('DOMContentLoaded', initLoadingMessages);
    document.addEventListener('livewire:navigated', initLoadingMessages);

    document.addEventListener('livewire:init', () => {
        if (document.querySelector('.loading-message')) {
            initLoadingMessages();
        }
    });
</script>

<script>
    let selectedAirlines = [];
    let selectedStops = [];
    let sortBy = 'cheapest';
    let flightResultWire = null;

    function resolveFlightResultWire() {
        if (flightResultWire) {
            return flightResultWire;
        }

        const root = document.getElementById('flight-result-page');
        const wireId = root?.getAttribute('wire:id');

        if (wireId && typeof Livewire !== 'undefined') {
            flightResultWire = Livewire.find(wireId);
        }

        if (!flightResultWire && typeof Livewire !== 'undefined') {
            flightResultWire = Livewire.all().find(
                (component) => component.name === 'page.flight-result'
            ) ?? null;
        }

        return flightResultWire;
    }

    function pushFiltersToLivewire() {
        const component = resolveFlightResultWire();
        if (!component) {
            return;
        }

        component.call('updateFlightFilters', selectedAirlines, selectedStops);
    }

    function syncSidebarAirlineCheckboxes() {
        document.querySelectorAll('.airline-filter').forEach((checkbox) => {
            checkbox.checked = selectedAirlines.includes(checkbox.value);
        });
    }

    function syncSidebarStopCheckboxes() {
        document.querySelectorAll('.stop-filter').forEach((checkbox) => {
            const stopValue = parseInt(checkbox.value, 10);
            checkbox.checked = selectedStops.includes(stopValue);
        });
    }

    function syncTableColumnHighlight() {
        document.querySelectorAll('#scrollableTable .column[data-airline-code]').forEach((column) => {
            const code = column.getAttribute('data-airline-code');
            const isSelected = code === ''
                ? selectedAirlines.length === 0
                : selectedAirlines.includes(code);

            column.classList.toggle('airline-column-selected', isSelected);
        });
    }

    function syncFilterUi() {
        syncSidebarAirlineCheckboxes();
        syncSidebarStopCheckboxes();
        syncTableColumnHighlight();
    }

    // Toggle airline filter from sidebar
    function toggleAirline(checkbox) {
        const airlineCode = checkbox.value;

        if (checkbox.checked) {
            if (!selectedAirlines.includes(airlineCode)) {
                selectedAirlines.push(airlineCode);
            }
        } else {
            selectedAirlines = selectedAirlines.filter((code) => code !== airlineCode);
        }

        syncTableColumnHighlight();
        pushFiltersToLivewire();
    }

    function toggleAirlineCheap(airlineCode) {
        const loadMoreBtn = document.getElementById('load-more-btn');
        if (loadMoreBtn) {
            loadMoreBtn.style.display = 'none';
        }

        selectedStops = [];
        selectedAirlines = airlineCode ? [airlineCode] : [];

        syncFilterUi();
        pushFiltersToLivewire();
    }

    function handleCellClick(cell, airlineCode) {
        const checkbox = cell.querySelector('input[type="checkbox"]');
        if (!checkbox) {
            return;
        }

        const stopValue = parseInt(checkbox.value, 10);
        selectedAirlines = [airlineCode];
        selectedStops = [stopValue];

        syncFilterUi();
        pushFiltersToLivewire();
    }

    // Toggle stop filter
    function toggleStopFilter(checkbox) {
        const stopValue = parseInt(checkbox.value, 10);

        if (checkbox.checked) {
            if (!selectedStops.includes(stopValue)) {
                selectedStops.push(stopValue);
            }
        } else {
            selectedStops = selectedStops.filter((stop) => stop !== stopValue);
        }

        syncSidebarStopCheckboxes();
        pushFiltersToLivewire();
    }

    // Sort flights by cheapest or fastest
    function sortFlights(option) {
        sortBy = option;
        updateButtonClasses();

        const component = resolveFlightResultWire();
        if (component) {
            component.call('setFlightSort', option);
        }
    }

    function updateButtonClasses() {
        const cheapestBtn = document.getElementById('cheapest-btn');
        const fastestBtn = document.getElementById('fastest-btn');

        if (!cheapestBtn || !fastestBtn) {
            return;
        }

        // Reset both buttons to default state
        cheapestBtn.classList.remove('bg-primary', 'text-white');
        cheapestBtn.classList.add('text-black');

        fastestBtn.classList.remove('bg-primary', 'text-white');
        fastestBtn.classList.add('text-black');

        // Apply active state to the selected button
        if (sortBy === 'cheapest') {
            cheapestBtn.classList.add('bg-primary', 'text-white');
            cheapestBtn.classList.remove('text-black');
        } else if (sortBy === 'fastest') {
            fastestBtn.classList.add('bg-primary', 'text-white');
            fastestBtn.classList.remove('text-black');
        }
    }

    function initFlightFilters() {
        updateButtonClasses();
        syncFilterUi();
    }

    document.addEventListener('DOMContentLoaded', initFlightFilters);
    document.addEventListener('livewire:navigated', initFlightFilters);

    document.addEventListener('livewire:init', () => {
        resolveFlightResultWire();

        Livewire.hook('morph.updated', ({ component }) => {
            if (component.name !== 'page.flight-result') {
                return;
            }

            flightResultWire = null;
            resolveFlightResultWire();
            syncFilterUi();

            if (document.querySelector('.loading-message')) {
                initLoadingMessages();
            } else {
                stopLoadingMessageRotation();
            }
        });

        startSearchStatusPoll();
    });

    const searchStatusPoll = {
        timer: null,
        searchId: @json($searchId ?? null),
        shouldPoll: @json($shouldPoll ?? false),
        lastStatus: @json($status ?? 'processing'),
        statusUrl: @json($searchId ? route('search.status', ['searchId' => $searchId]) : null),
    };

    function stopSearchStatusPoll() {
        if (searchStatusPoll.timer) {
            clearInterval(searchStatusPoll.timer);
            searchStatusPoll.timer = null;
        }
    }

    async function pollSearchStatusOnce() {
        if (!searchStatusPoll.shouldPoll || !searchStatusPoll.statusUrl) {
            stopSearchStatusPoll();
            return;
        }

        if (searchStatusPoll.lastStatus !== 'processing' || document.hidden) {
            stopSearchStatusPoll();
            return;
        }

        try {
            const response = await fetch(searchStatusPoll.statusUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                cache: 'no-store',
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            const newStatus = data.status ?? searchStatusPoll.lastStatus;
            const moreResultsReady = Boolean(data.more_results_ready);

            if (newStatus === searchStatusPoll.lastStatus) {
                return;
            }

            searchStatusPoll.lastStatus = newStatus;

            if (newStatus === 'failed') {
                stopSearchStatusPoll();
                window.location.href = @json(route('landing'));
                return;
            }

            const component = resolveFlightResultWire();
            if (component) {
                component.call('applySearchStatusUpdate', newStatus, moreResultsReady);
            }

            if (newStatus !== 'processing') {
                searchStatusPoll.shouldPoll = false;
                stopSearchStatusPoll();
            }
        } catch (error) {
            // Ignore transient network errors during background polling.
        }
    }

    function startSearchStatusPoll() {
        if (searchStatusPoll.timer || !searchStatusPoll.shouldPoll || !searchStatusPoll.statusUrl) {
            return;
        }

        pollSearchStatusOnce();
        searchStatusPoll.timer = setInterval(pollSearchStatusOnce, 5000);
    }

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopSearchStatusPoll();
            return;
        }

        if (searchStatusPoll.shouldPoll && searchStatusPoll.lastStatus === 'processing') {
            startSearchStatusPoll();
        }
    });
</script>

{{--<script>
    // document.addEventListener("livewire:load", () => {
    let scrollable = null;
    let prevBtn = null;
    let nextBtn = null;

    // handler references so we can remove them later
    let handlers = {
        onPrev: null,
        onNext: null,
        onScroll: null,
        onMouseDown: null,
        onMouseMove: null,
        onMouseUp: null,
        onMouseLeave: null,
        onTouchStart: null,
        onTouchMove: null,
        onTouchEnd: null
    };

    let isDown = false;
    let startX = 0;
    let scrollLeft = 0;

    function cleanup() {
        if (!scrollable) return;
        // remove scroll/drag handlers
        if (handlers.onScroll) scrollable.removeEventListener('scroll', handlers.onScroll);
        if (handlers.onMouseDown) scrollable.removeEventListener('mousedown', handlers.onMouseDown);
        if (handlers.onMouseMove) scrollable.removeEventListener('mousemove', handlers.onMouseMove);
        if (handlers.onMouseUp) scrollable.removeEventListener('mouseup', handlers.onMouseUp);
        if (handlers.onMouseLeave) scrollable.removeEventListener('mouseleave', handlers.onMouseLeave);
        if (handlers.onTouchStart) scrollable.removeEventListener('touchstart', handlers.onTouchStart);
        if (handlers.onTouchMove) scrollable.removeEventListener('touchmove', handlers.onTouchMove);
        if (handlers.onTouchEnd) scrollable.removeEventListener('touchend', handlers.onTouchEnd);

        // remove button clicks
        if (prevBtn && handlers.onPrev) prevBtn.removeEventListener('click', handlers.onPrev);
        if (nextBtn && handlers.onNext) nextBtn.removeEventListener('click', handlers.onNext);

        // reset
        handlers = {
            onPrev: null,
            onNext: null,
            onScroll: null,
            onMouseDown: null,
            onMouseMove: null,
            onMouseUp: null,
            onMouseLeave: null,
            onTouchStart: null,
            onTouchMove: null,
            onTouchEnd: null
        };
        isDown = false;
    }

    function scrollByColumns(direction) {
        if (!scrollable) return;
        const col = scrollable.querySelector('.column');
        const columnWidth = (col && col.offsetWidth) ? col.offsetWidth : Math.min(220, scrollable.clientWidth);
        scrollable.scrollBy({
            left: direction * columnWidth,
            behavior: 'smooth'
        });
    }

    function updateButtons() {
        if (!scrollable || !prevBtn || !nextBtn) return;
        prevBtn.disabled = scrollable.scrollLeft <= 0;
        nextBtn.disabled = (scrollable.scrollLeft + scrollable.clientWidth) >= (scrollable.scrollWidth - 5);
    }

    function bindSlider() {
        // remove previous listeners (if any)
        cleanup();

        scrollable = document.getElementById("scrollableTable");
        prevBtn = document.getElementById("prevBtn");
        nextBtn = document.getElementById("nextBtn");

        if (!scrollable) {
            // nothing to bind
            return;
        }

        // Backwards-compatible global function (if you still have inline onclick)
        window.scrollTable = (dir) => scrollByColumns(dir);

        // Button handlers
        handlers.onPrev = () => scrollByColumns(-1);
        handlers.onNext = () => scrollByColumns(1);
        if (prevBtn) prevBtn.addEventListener('click', handlers.onPrev);
        if (nextBtn) nextBtn.addEventListener('click', handlers.onNext);

        // Scroll event - update buttons state
        handlers.onScroll = () => updateButtons();
        scrollable.addEventListener('scroll', handlers.onScroll);

        // Mouse drag
        handlers.onMouseDown = function(e) {
            isDown = true;
            scrollable.classList.add('dragging');
            startX = e.pageX - scrollable.offsetLeft;
            scrollLeft = scrollable.scrollLeft;
        };
        handlers.onMouseMove = function(e) {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - scrollable.offsetLeft;
            const walk = (x - startX) * 1.5; // adjust speed
            scrollable.scrollLeft = scrollLeft - walk;
        };
        handlers.onMouseUp = function() {
            isDown = false;
            scrollable.classList.remove('dragging');
        };
        handlers.onMouseLeave = function() {
            isDown = false;
            scrollable.classList.remove('dragging');
        };

        scrollable.addEventListener('mousedown', handlers.onMouseDown);
        scrollable.addEventListener('mousemove', handlers.onMouseMove);
        scrollable.addEventListener('mouseup', handlers.onMouseUp);
        scrollable.addEventListener('mouseleave', handlers.onMouseLeave);

        // Touch drag
        handlers.onTouchStart = function(e) {
            isDown = true;
            startX = e.touches[0].pageX - scrollable.offsetLeft;
            scrollLeft = scrollable.scrollLeft;
        };
        handlers.onTouchMove = function(e) {
            if (!isDown) return;
            const x = e.touches[0].pageX - scrollable.offsetLeft;
            const walk = (x - startX) * 1.5;
            scrollable.scrollLeft = scrollLeft - walk;
        };
        handlers.onTouchEnd = function() {
            isDown = false;
        };

        scrollable.addEventListener('touchstart', handlers.onTouchStart, {
            passive: true
        });
        scrollable.addEventListener('touchmove', handlers.onTouchMove, {
            passive: true
        });
        scrollable.addEventListener('touchend', handlers.onTouchEnd);

        // Initial button state
        updateButtons();
    }

    // Initial bind
    bindSlider();

    // Re-bind after Livewire updates (give DOM a beat)
    //     Livewire.hook('message.processed', () => {
    //         setTimeout(bindSlider, 50);
    //     });
    // });
</script>--}}

<script>
    (function() {
        // keep state & handler refs so we can clean up between re-binds
        let state = {
            scrollable: null,
            prevBtn: null,
            nextBtn: null,
            handlers: {},
            livewireHooked: false
        };

        function cleanup() {
            const s = state;
            if (!s.scrollable) return;

            // remove scroll/drag handlers
            const h = s.handlers;
            if (h.scroll) s.scrollable.removeEventListener('scroll', h.scroll);
            if (h.mousedown) s.scrollable.removeEventListener('mousedown', h.mousedown);
            if (h.mousemove) document.removeEventListener('mousemove', h.mousemove);
            if (h.mouseup) document.removeEventListener('mouseup', h.mouseup);
            if (h.mouseleave) s.scrollable.removeEventListener('mouseleave', h.mouseleave);
            if (h.touchstart) s.scrollable.removeEventListener('touchstart', h.touchstart);
            if (h.touchmove) s.scrollable.removeEventListener('touchmove', h.touchmove);
            if (h.touchend) s.scrollable.removeEventListener('touchend', h.touchend);

            // remove button clicks
            if (s.prevBtn && h.prev) s.prevBtn.removeEventListener('click', h.prev);
            if (s.nextBtn && h.next) s.nextBtn.removeEventListener('click', h.next);

            // reset state handlers
            s.handlers = {};
        }

        function bindSlider() {
            cleanup();

            const s = state;
            s.scrollable = document.getElementById('scrollableTable');
            s.prevBtn = document.getElementById('prevBtn');
            s.nextBtn = document.getElementById('nextBtn');

            if (!s.scrollable) return; // nothing to do

            // ensure buttons don't submit forms
            if (s.prevBtn) s.prevBtn.type = 'button';
            if (s.nextBtn) s.nextBtn.type = 'button';

            // helper: scroll one column at a time
            const scrollByColumns = (direction) => {
                const col = s.scrollable.querySelector('.column');
                const columnWidth = col ? col.offsetWidth : Math.min(220, s.scrollable.clientWidth);
                s.scrollable.scrollBy({
                    left: direction * columnWidth,
                    behavior: 'smooth'
                });
            };

            // update next/prev disabled states
            const updateButtons = () => {
                if (!s.prevBtn || !s.nextBtn) return;
                s.prevBtn.disabled = s.scrollable.scrollLeft <= 0;
                s.nextBtn.disabled = (s.scrollable.scrollLeft + s.scrollable.clientWidth) >= (s.scrollable.scrollWidth - 5);
            };

            // store handlers so we can remove them later
            s.handlers.prev = () => scrollByColumns(-1);
            s.handlers.next = () => scrollByColumns(1);
            if (s.prevBtn) s.prevBtn.addEventListener('click', s.handlers.prev);
            if (s.nextBtn) s.nextBtn.addEventListener('click', s.handlers.next);

            // scroll event
            s.handlers.scroll = () => updateButtons();
            s.scrollable.addEventListener('scroll', s.handlers.scroll);

            // ----- mouse drag (desktop) -----
            let isDown = false;
            let startX = 0;
            let scrollLeft = 0;

            s.handlers.mousedown = function(e) {
                // only left mouse button
                if (e.button !== 0) return;
                isDown = true;
                s.scrollable.classList.add('dragging');
                startX = e.pageX - s.scrollable.offsetLeft;
                scrollLeft = s.scrollable.scrollLeft;
            };
            s.handlers.mousemove = function(e) {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - s.scrollable.offsetLeft;
                const walk = (x - startX) * 1.5; // adjust scroll speed if needed
                s.scrollable.scrollLeft = scrollLeft - walk;
            };
            s.handlers.mouseup = function() {
                if (!isDown) return;
                isDown = false;
                s.scrollable.classList.remove('dragging');
            };
            s.handlers.mouseleave = function() {
                isDown = false;
                s.scrollable.classList.remove('dragging');
            };

            s.scrollable.addEventListener('mousedown', s.handlers.mousedown);
            document.addEventListener('mousemove', s.handlers.mousemove);
            document.addEventListener('mouseup', s.handlers.mouseup);
            s.scrollable.addEventListener('mouseleave', s.handlers.mouseleave);

            // ----- touch drag (mobile) -----
            s.handlers.touchstart = function(e) {
                isDown = true;
                startX = e.touches[0].pageX - s.scrollable.offsetLeft;
                scrollLeft = s.scrollable.scrollLeft;
            };
            s.handlers.touchmove = function(e) {
                if (!isDown) return;
                const x = e.touches[0].pageX - s.scrollable.offsetLeft;
                const walk = (x - startX) * 1.5;
                s.scrollable.scrollLeft = scrollLeft - walk;
            };
            s.handlers.touchend = function() {
                isDown = false;
            };

            s.scrollable.addEventListener('touchstart', s.handlers.touchstart, {
                passive: true
            });
            s.scrollable.addEventListener('touchmove', s.handlers.touchmove, {
                passive: true
            });
            s.scrollable.addEventListener('touchend', s.handlers.touchend);

            // initial state
            updateButtons();
        }

        // Attempt to bind now (if DOM already ready)
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            bindSlider();
        } else {
            document.addEventListener('DOMContentLoaded', bindSlider, {
                once: true
            });
        }

        // If Livewire is present, bind on livewire:load and re-bind after each update
        function ensureLivewireHooks() {
            if (!window.Livewire || state.livewireHooked) return;
            state.livewireHooked = true;

            // initial Livewire load (fires once)
            document.addEventListener('livewire:load', () => {
                bindSlider();
            }, {
                once: true
            });

            // re-bind after each Livewire DOM update
            Livewire.hook('message.processed', (message, component) => {
                // small delay to allow DOM replace to finish
                setTimeout(bindSlider, 40);
            });
        }

        // Run immediately if Livewire exists, otherwise wait a bit and check again
        if (window.Livewire) {
            ensureLivewireHooks();
        } else {
            // check for Livewire presence in case it loads after this script
            const checkInterval = setInterval(() => {
                if (window.Livewire) {
                    ensureLivewireHooks();
                    clearInterval(checkInterval);
                }
            }, 200);

            // stop checking after 5s
            setTimeout(() => clearInterval(checkInterval), 5000);
        }
    })();
</script>

</div>
</div>
@endif
</div>