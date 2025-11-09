<div x-data="{
    tripType: @entangle('tripType'),
    showPassengerDropdown: false,
    showCabinDropdown: false,
    cabinClass: @entangle('cabinClass'),
    isFlexible: @entangle('isFlexible'),
    multiCityFlights: @entangle('multiCityFlights'),
    passengers: @entangle('passengers'),
    nonStop: @entangle('nonStop'),
    currencyCode: @entangle('currencyCode'),
    maxPrice: @entangle('maxPrice'),
    max: @entangle('max'),
    addFlight() {
        const newFlight = { from: '', to: '', departureDate: '', returnDate: '' };
        this.multiCityFlights.push(newFlight);
        this.$wire.set('multiCityFlights', this.multiCityFlights);
    },
    removeFlight(index) {
        this.multiCityFlights.splice(index, 1);
        this.$wire.set('multiCityFlights', this.multiCityFlights); // Sync with Livewire
    }

}" @keydown.escape="showPassengerDropdown = false; showCabinDropdown = false" class="{{$showToSource == 'web' ? 'p-4' : ''}}">

    <style>
        @keyframes steam {
            0% {
                background-position: -100%;
            }

            100% {
                background-position: 100%;
            }
        }

        /* Apply the animation to the steam effect */
        .animate-steam {
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.7), transparent);
            background-size: 200% 100%;
            /* Ensures the gradient is large enough to animate */
            animation: steam 4s ease-in-out infinite;
            /* The gradient will move smoothly from left to right */
        }
    </style>

    <div class="{{$showToSource == 'web' ? 'lg:mx-10' : ''}} relative bg-black bg-opacity-80 z-20 rounded-3xl p-4 md:p-7 ">
        @if (session('error'))
        <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4  " role="alert">
            {{ session('error') }}
        </div>
        @endif

        {{-- Display validation error messages --}}
        @if (session('errors'))
        <div class="bg-red-50 border border-red-200 text-sm text-red-800 rounded-lg p-4 mb-2  " role="alert"
            tabindex="-1" aria-labelledby="hs-with-list-label">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="m15 9-6 6"></path>
                        <path d="m9 9 6 6"></path>
                    </svg>
                </div>
                <div class="ms-4">
                    <h3 id="hs-with-list-label" class="text-sm font-semibold">
                        A problem has been occurred while submitting your data.
                    </h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-400"
                        wire:key="error-messages-{{ $this->id ?? 'default' }}">
                        <ul class="list-disc space-y-1 ps-5">
                            {{-- Check if session('errors') is an array --}}
                            @if (is_array(session('errors')))
                            {{-- Loop through and display each error --}}
                            @foreach (session('errors') as $field => $messages)
                            @foreach ($messages as $message)
                            <li>{{ $message }}</li>
                            @endforeach
                            @endforeach
                            @else
                            {{-- If it's a MessageBag, use all() --}}
                            @foreach (session('errors')->all() as $message)
                            <li>{{ $message }}</li>
                            @endforeach
                            @endif
                        </ul>
                    </div>

                </div>
            </div>
        </div>
        @endif
        <!-- Trip Type Selection -->
        <div class="flex relative justify-between flex-wrap gap-5 mb-3 md:gap-3 ">
            <div
                class="flex flex-wrap gap-1 md:gap-3 justify-center">
                <template
                    x-for="(label, type) in {
                    'one': { text: 'ONE WAY', icon: 'M14 5l7 7m0 0l-7 7m7-7H3' },
                    'round': { text: 'ROUND TRIP', icon: 'M8 7l4-4m0 0l4 4m-4-4v18' },
                    'multi': { text: 'MULTI CITY', icon: 'M9 5l7 7-7 7' }
                }"
                    :key="type">
                    <label @click="$dispatch('updateTrip', { type: type })" {{-- @click="tripType = type; $dispatch('updateTrip', type)" --}}
                        class="flex items-center cursor-pointer border h-fit p-1 border-gray-300 rounded-full px-3 
                                  transition-all duration-200 text-xs sm:text-sm"
                        :class="{ 'bg-white text-primary': tripType === type, 'text-gray-700 bg-gray-100': tripType !== type }">

                        <input type="radio" x-model="tripType" :value="type" class="hidden">

                        <!-- Icon -->
                        <span class="md:flex items-center hidden justify-center w-4 h-4 sm:w-5 sm:h-5 mr-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5"
                                :class="{ 'text-primary': tripType === type, 'text-gray-400': tripType !== type }"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="label.icon" />
                            </svg>
                        </span>
                        <!-- Text -->
                        <span x-text="label.text" class="font-medium"></span>
                    </label>
                </template>
            </div>

            <!-- Dropdown Buttons -->
            <div class="relative">
                <button @click="showCabinDropdown = !showCabinDropdown"
                    class="flex items-center space-x-2 text-sm px-4 py-2 border border-gray-200 text-black rounded-lg bg-white hover:bg-gray-50 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <span x-text="cabinClass.replace('_', ' ')" class="font-medium"></span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="showCabinDropdown" @click.away="showCabinDropdown = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="absolute right-0 mt-2 w-56 bg-white text-black  rounded-lg shadow-xl z-50 border border-gray-200">
                    <div class="py-1">
                        <template
                            x-for="cabin in [
                            { value: 'ECONOMY', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
                            { value: 'PREMIUM_ECONOMY', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
                            { value: 'BUSINESS', icon: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
                            { value: 'FIRST', icon: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z' }
                        ]">
                            <a href="#" @click.prevent="cabinClass = cabin.value; showCabinDropdown = false"
                                class="flex items-center px-4 py-2 text-sm hover:bg-primary text-black hover:text-wh transition-colors duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="cabin.icon" />
                                </svg>
                                <span x-text="cabin.value.replace('_', ' ')"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            <div class=" w-full {{$showToSource =='web' ? 'md:max-w-sm flex-end' : '' }} rounded-xl relative transition-shadow duration-300">
                <div class="flex justify-between w-full">
                    <button @click="showPassengerDropdown = !showPassengerDropdown"
                        class="w-full text-left border text-white {{$showToSource =='admin' ? 'md:max-w-sm' : '' }} rounded-lg p-2 focus:outline-none transition-all duration-300">
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-4">
                                <div class="flex items-center space-x-1">
                                    <span class="text-[11px]">Adult</span>
                                    <span x-text="passengers.adult"></span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <span class="text-[11px]">Child</span>
                                    <span x-text="passengers.child"></span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <span class="text-[11px]">Infant</span>
                                    <span x-text="passengers.infant"></span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-lg font-semibold"
                                    x-text="`Total: ${passengers.adult + passengers.child + passengers.infant}`"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-300"
                                    :class="{ 'rotate-180': showPassengerDropdown }" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </button>
                    @if($showToSource == "admin")
                    <div class="max-w-xl">
                        <div class=" relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <input type="number"
                                name="maxPrice"
                                id="maxPrice"
                                x-model="maxPrice"
                                min="1"
                                class="block rounded-xl pl-10 pr-20 p-5 border w-full sm:text-sm outline-none  focus:outline-none"

                                placeholder="0.00">
                            <div class="absolute inset-y-0 right-0 flex items-center">
                                <label for="currency" class="sr-only">Currency</label>
                                <select id="currency"
                                    name="currency"
                                    x-model="currencyCode"
                                    class="h-full py-0 pl-2 pr-7 border-transparent bg-transparent text-gray-500 sm:text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option>USD</option>
                                    <option>EUR</option>
                                    <option>GBP</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Non-stop Flights -->
                    <label class="flex items-center space-x-3 p-3 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50 transition-colors duration-200 cursor-pointer group">
                        <input type="checkbox"
                            x-model="nonStop"
                            class="rounded border-gray-300 text-blue-500 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 h-5 w-5">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 group-hover:text-blue-500 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">Non-stop flights only</span>
                        </div>
                    </label>

                    @endif
                </div>
                <!-- Passenger Dropdown -->
                <div x-cloak x-show="showPassengerDropdown" @click.away="showPassengerDropdown = false"
                    class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl z-50 border border-gray-200 max-h-72 overflow-y-auto">
                    <div class="p-4 ">
                        <!-- Adult, Child, and Infant Count Section -->
                        <template
                            x-for="(label, type) in {'adult': 'Adults', 'child': 'Children', 'infant': 'Infants'}">
                            <div
                                class="flex items-center justify-between py-3 px-4 rounded-lg hover:bg-gray-50 transition-all duration-200">
                                <div class="flex items-center space-x-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            x-show="type === 'adult'" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 17l3-3 3 3m-3-3v11m0-14a4 4 0 110-8 4 4 0 010 8z"
                                            x-show="type === 'child'" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 3v1m0 16v1m-8-8h1m14 0h1m-6.4-6.4l.7-.7m-7.9 7.9l.7-.7m7.9 0l-.7-.7m-7.9-7.9l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                                            x-show="type === 'infant'" />
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-800" x-text="label"></div>
                                        <div class="text-sm text-gray-500"
                                            x-text="type === 'adult' ? '12+ years' : (type === 'child' ? '2-11 years' : 'Under 2 years')">
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <button
                                        @click="if (type === 'adult' || passengers[type] > 0) { 
                                                   passengers[type] = Math.max(type === 'adult' ? 1 : 0, passengers[type] - 1) 
                                               }"
                                        class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 transition-all duration-200 flex items-center justify-center text-gray-600 hover:text-gray-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <span class="text-lg font-semibold text-gray-800 w-6 text-center"
                                        x-text="passengers[type]"></span>
                                    <button
                                        @click="if (type !== 'infant' || passengers[type] < passengers.adult) {
                                                   passengers[type] = Math.min(9, passengers[type] + 1) 
                                               }"
                                        class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 transition-all duration-200 flex items-center justify-center text-gray-600 hover:text-gray-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <!-- Flight Instruction: Infant can't be more than Adults -->
                        <div x-show="passengers.infant > passengers.adult"
                            class="text-sm text-red-500 mt-2 bg-red-50 p-2 rounded-md flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Infants cannot exceed the number of adults.</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="relative w-full  mx-auto h-1 overflow-hidden my-4">
            <div
                class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-transparent via-white to-transparent animate-steam">
            </div>
        </div>

        <!-- Search Form -->
        <div class="relative flex md:justify-between flex-wrap md:flex-nowrap">
            <!-- One Way and Round Trip Form -->
            <template x-if="tripType !== 'multi'">
                <div class="md:flex w-full justify-evenly gap-4 mb-2">
                    <!-- From City -->
                    <div x-data="{
                        fromInput: @entangle('searchQueryFrom').live,
                        toInput: @entangle('searchQueryTo').live,
                        showPopularFrom: false,
                        showPopularTo: false,
                        clearFromInput() {
                            this.fromInput = '';
                            $wire.searchQueryFrom = '';
                            this.showPopularFrom = true;
                            $wire.showFromDropdown = false;
                        },
                        clearToInput() {
                            this.toInput = '';
                            $wire.searchQueryTo = '';
                            this.showPopularTo = true;
                            $wire.showToDropdown = false;
                        }
                    }" @click.away="showPopularFrom = false; showPopularTo = false"
                        class="relative flex flex-col lg:flex-row justify-center items-start gap-6 w-full  mx-auto p-2 bg-gradient-to-br">
                        <!-- From Section -->
                        <div class="w-full lg:w-1/2 relative">
                            <div class="rounded-lg p-2 border border-white/10 shadow-lg">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2 text-white mb-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="font-medium text-xs lg:text-xl ">Flying from</span>
                                    </div>
                                    <div class="relative">
                                        <input id="from" type="text" x-model="fromInput"
                                            wire:model.live.debounce.500ms="searchQueryFrom"
                                            placeholder="Search City, Airport, or Country" autocomplete="off"
                                            @click="showPopularFrom = true; showPopularTo = false; $wire.showPopularFromCities = true; $wire.showPopularToCities = false;"
                                            class="w-full bg-transparent placeholder:text-xs  text-white border-b border-white/20 pb-2 text-sm lg:text-base placeholder:text-white/50 focus:outline-none focus:border-primary transition-colors">

                                        <!-- Clear button -->
                                        <button x-show="fromInput.length > 0" x-cloak @click.stop="clearFromInput()"
                                            class="absolute right-0 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition-colors"
                                            type="button">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <span class="sr-only">Clear input</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Popular Cities (shown on click when input is empty) -->
                            <div x-show="showPopularFrom && (!fromInput || fromInput.length === 0)" x-cloak
                                @click.away="showPopularFrom = false"
                                class="absolute hidden lg:block z-10 w-[160%] mt-2  bg-white text-black border border-gray-200 rounded-xl p-3 shadow-2xl">
                                <div class="mb-2 pb-2 border-b border-gray-100">
                                    <h3 class="text-sm font-semibold text-gray-500 mb-2">Popular Cities</h3>
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach ($popularCities as $city)
                                        <button
                                            wire:click="setAirportCode('{{ $city['airport_code'] }}', '{{ $city['city_country'] }} ({{ $city['airport_code'] }})', '{{ $city['city'] }}', 'from')"
                                            @click="showPopularFrom = false"
                                            class="flex items-center gap-2 p-2 hover:bg-primary/10 rounded-lg transition-colors text-left">
                                            <span class="text-primary">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                                    </path>
                                                </svg>
                                            </span>
                                            <div class="flex flex-col">
                                                <span class="font-medium text-[10px]">{{ $city['city'] }}</span>
                                                <span
                                                    class="text-[8px] text-gray-500">{{ $city['country'] }}</span>
                                            </div>
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- From Dropdown (shown when typing) -->
                            <div x-show="$wire.showFromDropdown && Array.isArray($wire.fromAirlines) && $wire.fromAirlines.length > 0"
                                x-cloak
                                class="absolute z-10 w-full mt-2 bg-white text-black border border-gray-200 rounded-xl p-3 shadow-2xl">
                                <ul class="max-h-60 overflow-y-auto custom-scrollbar">
                                    @forelse ($fromAirlines as $airline)
                                    <li wire:click="setAirportCode('{{ $airline['airport_code'] }}', '{{ $airline['city_country'] }} ({{ $airline['airport_code'] }})', '{{ $airline['city'] }}',  'from')"
                                        @click="showPopularFrom = false"
                                        class="px-3 py-2.5 flex items-center justify-between hover:bg-primary/10 rounded-lg cursor-pointer transition-colors duration-150 mb-1">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-sm">{{ $airline['airport'] }}</span>
                                            <span
                                                class="text-gray-500 text-xs">{{ $airline['city_country'] }}</span>
                                        </div>
                                        <span
                                            class="bg-primary text-white font-medium px-2.5 py-1.5 rounded-md text-xs">{{ $airline['airport_code'] }}</span>
                                    </li>
                                    @empty
                                    <li class="px-4 py-3 text-gray-500 text-center">No results found</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <!-- To Section -->
                        <div class="w-full lg:w-1/2 relative">
                            <div class="rounded-lg p-2 border border-white/10 shadow-lg">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2 text-white mb-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="font-medium text-xs lg:text-xl">Flying to</span>
                                    </div>
                                    <div class="relative">
                                        <input type="text" x-model="toInput"
                                            wire:model.live.debounce.500ms="searchQueryTo"
                                            placeholder="Search City, Airport, or Country" autocomplete="off" or
                                            Country" autocomplete="off"
                                            @click="showPopularTo = true; showPopularFrom = false; $wire.showPopularToCities = true; $wire.showPopularFromCities = false;"
                                            class="w-full placeholder:text-xs bg-transparent text-white border-b border-white/20 pb-2 text-sm md:text-base placeholder:text-white/50 focus:outline-none focus:border-primary transition-colors">

                                        <!-- Clear button -->
                                        <button x-show="toInput.length > 0" x-cloak @click.stop="clearToInput()"
                                            class="absolute right-0 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition-colors"
                                            type="button">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <span class="sr-only">Clear input</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Popular Cities (shown on click when input is empty) -->
                            <div x-show="showPopularTo && (!toInput || toInput.length === 0)" x-cloak
                                @click.away="showPopularTo = false"
                                class="absolute hidden lg:block z-10 w-[160%] right-0 mt-2 bg-white text-black border border-gray-200 rounded-xl p-3 shadow-2xl">
                                <div class="mb-2 pb-2 border-b border-gray-100">
                                    <h3 class="text-sm font-semibold text-gray-500 mb-2">Popular Cities</h3>
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach ($popularCities as $city)
                                        <button
                                            wire:click="setAirportCode('{{ $city['airport_code'] }}', '{{ $city['city_country'] }} ({{ $city['airport_code'] }})', '{{ $city['city'] }}', 'to')"
                                            @click="showPopularTo = false"
                                            class="flex items-center gap-2 p-2 hover:bg-primary/10 rounded-lg transition-colors text-left">
                                            <span class="text-primary">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </span>
                                            <div class="flex flex-col">
                                                <span class="font-medium text-[10px]">{{ $city['city'] }}</span>
                                                <span
                                                    class="text-[8px] text-gray-500">{{ $city['country'] }}</span>
                                            </div>
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- To Dropdown (shown when typing) -->
                            <div x-show="$wire.showToDropdown && Array.isArray($wire.toAirlines) && $wire.toAirlines.length > 0"
                                x-cloak
                                class="absolute z-10 w-full mt-2 bg-white text-black border border-gray-200 rounded-xl p-3 shadow-2xl">
                                <ul class="max-h-60 overflow-y-auto custom-scrollbar">
                                    @forelse ($toAirlines as $airline)
                                    <li wire:click="setAirportCode('{{ $airline['airport_code'] }}', '{{ $airline['city_country'] }} ({{ $airline['airport_code'] }})', '{{ $airline['city'] }}', 'to')"
                                        @click="showPopularTo = false"
                                        class="px-3 py-2.5 flex items-center justify-between hover:bg-primary/10 rounded-lg cursor-pointer transition-colors duration-150 mb-1">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-sm">{{ $airline['airport'] }}</span>
                                            <span
                                                class="text-gray-500 text-xs">{{ $airline['city_country'] }}</span>
                                        </div>
                                        <span
                                            class="bg-primary text-white font-medium px-2.5 py-1.5 rounded-md text-xs">{{ $airline['airport_code'] }}</span>
                                    </li>
                                    @empty
                                    <li class="px-4 py-3 text-gray-500 text-center">No results found</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    <style>
                        /* Custom scrollbar styles */
                        .custom-scrollbar {
                            scrollbar-width: thin;
                            scrollbar-color: #cbd5e1 #f1f5f9;
                        }

                        .custom-scrollbar::-webkit-scrollbar {
                            width: 6px;
                        }

                        .custom-scrollbar::-webkit-scrollbar-track {
                            background: #f1f5f9;
                            border-radius: 10px;
                        }

                        .custom-scrollbar::-webkit-scrollbar-thumb {
                            background-color: #cbd5e1;
                            border-radius: 10px;
                        }

                        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                            background-color: #94a3b8;
                        }
                    </style>

                    <div class="grid grid-cols-1 lg:grid-cols-2 lg:flex justify-between items-center w-full">
                        <!-- Departure Date -->
                        <x-datepicker label="Departure" name="departure_date" placeholder="Pick Departure Date"
                            :initial-date="$departureDate ?? null" wire="true" />

                        <!-- Return Date (only visible for round trips) -->

                        <template x-if="tripType === 'round'" class="w-full">
                            <x-datepicker label="Return" name="return_date" placeholder="Pick Return Date"
                                :initial-date="$returnDate ?? null" :min-date="$departureDate ?? null" type="return" wire="true" />
                        </template>
                    </div>
                </div>
            </template>

            <!-- Multi-City Form -->
            <div x-show="tripType === 'multi'" class="w-full mb-4 max-h-screen overflow-y-auto">
                @foreach ($multiCityFlights as $index => $flight)
                <div class="mb-6 p-4 border border-white/10 rounded-lg bg-black/30">
                    <!-- Flight Number Label -->
                    <div class="mb-3 flex justify-between items-center">
                        <h3 class="text-white font-medium text-lg">Flight {{ $index + 1 }}</h3>

                        <!-- Remove Flight Button (don't show for first flight) -->
                        @if ($index > 0)
                        <button wire:click="removeFlight({{ $index }})" type="button"
                            class="text-red-500 hover:text-red-700 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                        @endif
                    </div>

                    <!-- From/To Cities and Date in a single row -->
                    <div class="flex flex-col lg:flex-row gap-4 mb-4">
                        <!-- From City -->
                        <div class="w-full lg:w-1/3 relative">
                            <div class="rounded-lg p-2 border border-white/10 shadow-lg">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2 text-white mb-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="font-medium text-xs lg:text-base">Flying from</span>
                                    </div>
                                    <div class="relative">
                                        <input type="text"
                                            wire:model.live.debounce.500ms="multiCityFlights.{{ $index }}.fromQuery"
                                            placeholder="Search City, Airport, or Country" autocomplete="off"
                                            @click="$wire.showFromDropdown = {{ $index }}"
                                            class="w-full bg-transparent placeholder:text-xs text-white border-b border-white/20 pb-2 text-sm lg:text-base placeholder:text-white/50 focus:outline-none focus:border-primary transition-colors">

                                        <!-- Clear button -->
                                        <button
                                            x-show="$wire.multiCityFlights[{{ $index }}].fromQuery.length > 0"
                                            wire:click="clearMultiCityInput('from', {{ $index }})"
                                            class="absolute right-0 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition-colors"
                                            type="button">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <span class="sr-only">Clear input</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Popular Cities (shown when input is empty) -->
                            <div x-show="$wire.showFromDropdown === {{ $index }} && !$wire.multiCityFlights[{{ $index }}].fromQuery"
                                x-cloak
                                class="absolute z-10 w-full mt-2 bg-white text-black border border-gray-200 rounded-xl p-3 shadow-2xl">
                                <div class="mb-2 pb-2 border-b border-gray-100">
                                    <h3 class="text-sm font-semibold text-gray-500 mb-2">Popular Cities</h3>
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach ($popularCities as $city)
                                        <button
                                            wire:click="setMultiCityAirportCode('{{ $city['airport_code'] }}', '{{ $city['city_country'] }} ({{ $city['airport_code'] }})', '{{ $city['city'] }}', 'from', {{ $index }})"
                                            class="flex items-center gap-2 p-2 hover:bg-primary/10 rounded-lg transition-colors text-left">
                                            <span class="text-primary">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                    </path>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                                    </path>
                                                </svg>
                                            </span>
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-medium text-[10px]">{{ $city['city'] }}</span>
                                                <span
                                                    class="text-[8px] text-gray-500">{{ $city['country'] }}</span>
                                            </div>
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- From Dropdown (shown when typing) -->
                            <div x-show="$wire.showFromDropdown === {{ $index }} && $wire.multiCityFlights[{{ $index }}].fromQuery"
                                x-cloak
                                class="absolute z-10 w-full mt-2 bg-white text-black border border-gray-200 rounded-xl p-3 shadow-2xl">
                                <ul class="max-h-60 overflow-y-auto custom-scrollbar">
                                    @forelse ($fromAirlines as $airline)
                                    <li wire:click="setMultiCityAirportCode('{{ $airline['airport_code'] }}', '{{ $airline['city_country'] }} ({{ $airline['airport_code'] }})', '{{ $airline['city'] }}', 'from', {{ $index }})"
                                        class="px-3 py-2.5 flex items-center justify-between hover:bg-primary/10 rounded-lg cursor-pointer transition-colors duration-150 mb-1">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-sm">{{ $airline['airport'] }}</span>
                                            <span
                                                class="text-gray-500 text-xs">{{ $airline['city_country'] }}</span>
                                        </div>
                                        <span
                                            class="bg-primary text-white font-medium px-2.5 py-1.5 rounded-md text-xs">{{ $airline['airport_code'] }}</span>
                                    </li>
                                    @empty
                                    <li class="px-4 py-3 text-gray-500 text-center">
                                        @if (strlen($multiCityFlights[$index]['fromQuery']) < 2)
                                            Type at least 2 characters to search
                                            @else
                                            No results found
                                            @endif
                                            </li>
                                            @endforelse
                                </ul>
                            </div>
                        </div>

                        <!-- To City -->
                        <div class="w-full lg:w-1/3 relative">
                            <div class="rounded-lg p-2 border border-white/10 shadow-lg">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2 text-white mb-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="font-medium text-xs lg:text-base">Flying to</span>
                                    </div>
                                    <div class="relative">
                                        <input type="text"
                                            wire:model.live.debounce.500ms="multiCityFlights.{{ $index }}.toQuery"
                                            placeholder="Search City, Airport, or Country" autocomplete="off"
                                            @click="$wire.showToDropdown = {{ $index }}"
                                            class="w-full placeholder:text-xs bg-transparent text-white border-b border-white/20 pb-2 text-sm md:text-base placeholder:text-white/50 focus:outline-none focus:border-primary transition-colors">

                                        <!-- Clear button -->
                                        <button
                                            x-show="$wire.multiCityFlights[{{ $index }}].toQuery.length > 0"
                                            wire:click="clearMultiCityInput('to', {{ $index }})"
                                            class="absolute right-0 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition-colors"
                                            type="button">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <span class="sr-only">Clear input</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Popular Cities (shown when input is empty) -->
                            <div x-show="$wire.showToDropdown === {{ $index }} && !$wire.multiCityFlights[{{ $index }}].toQuery"
                                x-cloak
                                class="absolute z-10 w-full mt-2 bg-white text-black border border-gray-200 rounded-xl p-3 shadow-2xl">
                                <div class="mb-2 pb-2 border-b border-gray-100">
                                    <h3 class="text-sm font-semibold text-gray-500 mb-2">Popular Cities</h3>
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach ($popularCities as $city)
                                        <button
                                            wire:click="setMultiCityAirportCode('{{ $city['airport_code'] }}', '{{ $city['city_country'] }} ({{ $city['airport_code'] }})', '{{ $city['city'] }}', 'to', {{ $index }})"
                                            class="flex items-center gap-2 p-2 hover:bg-primary/10 rounded-lg transition-colors text-left">
                                            <span class="text-primary">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </span>
                                            <div class="flex flex-col">
                                                <span
                                                    class="font-medium text-[10px]">{{ $city['city'] }}</span>
                                                <span
                                                    class="text-[8px] text-gray-500">{{ $city['country'] }}</span>
                                            </div>
                                        </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- To Dropdown (shown when typing) -->
                            <div x-show="$wire.showToDropdown === {{ $index }} && $wire.multiCityFlights[{{ $index }}].toQuery"
                                x-cloak
                                class="absolute z-10 w-full mt-2 bg-white text-black border border-gray-200 rounded-xl p-3 shadow-2xl">
                                <ul class="max-h-60 overflow-y-auto custom-scrollbar">
                                    @forelse ($toAirlines as $airline)
                                    <li wire:click="setMultiCityAirportCode('{{ $airline['airport_code'] }}', '{{ $airline['city_country'] }} ({{ $airline['airport_code'] }})', '{{ $airline['city'] }}', 'to', {{ $index }})"
                                        class="px-3 py-2.5 flex items-center justify-between hover:bg-primary/10 rounded-lg cursor-pointer transition-colors duration-150 mb-1">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-sm">{{ $airline['airport'] }}</span>
                                            <span
                                                class="text-gray-500 text-xs">{{ $airline['city_country'] }}</span>
                                        </div>
                                        <span
                                            class="bg-primary text-white font-medium px-2.5 py-1.5 rounded-md text-xs">{{ $airline['airport_code'] }}</span>
                                    </li>
                                    @empty
                                    <li class="px-4 py-3 text-gray-500 text-center">
                                        @if (strlen($multiCityFlights[$index]['toQuery']) < 2)
                                            Type at least 2 characters to search
                                            @else
                                            No results found
                                            @endif
                                            </li>
                                            @endforelse
                                </ul>
                            </div>
                        </div>

                        <!-- Date Picker -->
                        <div class="w-full lg:w-1/3">
                            <x-datepicker label="Flight {{ $index + 1 }} Date"
                                name="flight_{{ $index }}_date" placeholder="Pick Departure Date"
                                :initial-date="$flight['departureDate'] ?? null" :wire="$index" type="departure" />
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Add Flight Button -->
                <button wire:click="addFlight" type="button"
                    class="mt-2 flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary/90 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add Another Flight</span>
                </button>
            </div>
            <!-- Search Button -->
            <button wire:click="search" wire:target="search"
                class="lg:bg-red-800 lg:flex md:relative bottom-0 lg:right-0 
                         flex items-center justify-center  p-2 size-10 md:size-16 
                         rounded-full transition-colors sm:ml-auto relative w-full">

                <!-- Normal State -->
                <div wire:loading.remove wire:target="search" class="flex items-center justify-between w-full">
                    <!-- Search Icon for Large Screens -->
                    <svg class="size-7 hidden lg:flex text-white mx-auto" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z">
                        </path>
                    </svg>
                    <!-- Search Text for Small Screens (Right-Aligned) -->
                    <span class="lg:hidden absolute right-3">Search</span>
                </div>

                <!-- Loading State: Spinner Animation -->
                <div wire:loading wire:target="search" class="flex items-center justify-center w-full">
                    {{--<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"
                        style="animation:animate-spin"
                        class="size-10 fill-white motion-safe:animate-spin">
                        <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                            opacity=".25" />
                        <path
                            d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                    </svg>--}}
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"
                        style="width:40px; height:40px; fill:#fff; animation:spinvne 1s linear infinite;" class="">
                        <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
                        <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                    </svg>
                </div>
            </button>
        </div>
    </div>
    <style>
        /* Custom scrollbar styles */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }
    </style>
</div>

{{-- <!-- Modified initialization script -->
<script>
    document.addEventListener('livewire:load', function() {
        @if (isset($editMode) && $editMode && !empty($from) && !empty($to))
            // This script is now just for debugging purposes
            console.log("Search component initialized in edit mode");
            console.log("From: {{ $from }}");
console.log("To: {{ $to }}");
console.log("From Display: {{ $fromDisplayName }}");
console.log("To Display: {{ $toDisplayName }}");
@endif
});
</script> --}}