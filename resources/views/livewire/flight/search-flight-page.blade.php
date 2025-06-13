

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
    },
   
}" 
class="max-w-7xl h-screen mx-auto p-6 items-center py-[10%] bg-white rounded-xl shadow-lg"
@keydown.escape="showPassengerDropdown = false; showCabinDropdown = false">

{{-- Display general error message --}}
@if (session('error'))
    <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500" role="alert">
        {{ session('error') }}
    </div>
@endif

{{-- Display validation error messages --}}
@if (session('errors'))
    <div class="bg-red-50 border border-red-200 text-sm text-red-800 rounded-lg p-4 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500" role="alert" tabindex="-1" aria-labelledby="hs-with-list-label">
        <div class="flex">
            <div class="shrink-0">
                <svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="m15 9-6 6"></path>
                    <path d="m9 9 6 6"></path>
                </svg>
            </div>
            <div class="ms-4">
                <h3 id="hs-with-list-label" class="text-sm font-semibold">
                    A problem has been occurred while submitting your data.
                </h3>
                <div class="mt-2 text-sm text-red-700 dark:text-red-400" wire:key="error-messages-{{ $this->id ?? 'default' }}">
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


     <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <div class="flex flex-wrap gap-4">
            <template x-for="(label, type) in {
                'one': { text: 'One Way', icon: 'M14 5l7 7m0 0l-7 7m7-7H3' },
                'round': { text: 'Round Trip', icon: 'M8 7l4-4m0 0l4 4m-4-4v18' },
                'multi': { text: 'Multi City', icon: 'M9 5l7 7-7 7' }
            }" :key="type">
                <label class="flex items-center cursor-pointer bg-white border border-gray-200 rounded-full px-4 py-2 transition-all duration-200 hover:bg-blue-50 hover:border-blue-200"
                       :class="{ 'bg-blue-100 border-blue-500': tripType === type }">
                    <input type="radio" x-model="tripType" :value="type" class="hidden" wire:click="updateTripType(type)" >
                    <span class="flex items-center justify-center w-5 h-5 mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="{ 'text-blue-500': tripType === type, 'text-gray-400': tripType !== type }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" :d="label.icon" />
                        </svg>
                    </span>
                    <span x-text="label.text" :class="{ 'font-medium': tripType === type }"></span>
                </label>
            </template>
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
        
            <!-- Currency Code -->
            <div x-data="{ showCurrencyDropdown: false }" class="relative">
              
                <button @click="showCurrencyDropdown = !showCurrencyDropdown"
                        class="w-full flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span x-text="currencyCode" class="text-sm font-medium text-gray-700"></span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div x-show="showCurrencyDropdown" 
                     @click.away="showCurrencyDropdown = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="absolute z-10 mt-1 w-full bg-white rounded-md shadow-lg">
                    <div class="py-1 max-h-60 overflow-auto">
                        <template x-for="currency in ['USD', 'EUR', 'GBP']">
                            <a href="#" 
                               @click.prevent="currencyCode = currency; showCurrencyDropdown = false"
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                               :class="{ 'bg-gray-100 text-gray-900': currency === currencyCode }"
                               x-text="currency"></a>
                        </template>
                    </div>
                </div>
            </div>
        
    
        <!-- Cabin Class Selector -->
        <div class="relative" >
            <button @click="showCabinDropdown = !showCabinDropdown"
                    class="flex items-center space-x-2 text-sm px-4 py-2 border border-gray-200 rounded-lg bg-white hover:bg-gray-50 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                <span x-text="cabinClass.replace('_', ' ')" class="font-medium"></span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            
            <div x-show="showCabinDropdown" 
                 @click.away="showCabinDropdown = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl z-50 border border-gray-200">
                <div class="py-1">
                    <template x-for="cabin in [
                        { value: 'ECONOMY', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
                        { value: 'PREMIUM_ECONOMY', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
                        { value: 'BUSINESS', icon: 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
                        { value: 'FIRST', icon: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z' }
                    ]">
                        <a href="#" 
                           @click.prevent="cabinClass = cabin.value; showCabinDropdown = false"
                           class="flex items-center px-4 py-2 text-sm hover:bg-gray-100 transition-colors duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="cabin.icon" />
                            </svg>
                            <span x-text="cabin.value.replace('_', ' ')"></span>
                        </a>
                    </template>
                </div>
            </div>
        </div>

    </div>
    
    

    <div class="mb-6 flex justify-between items-center">
        <div  
             class=" w-full max-w-md rounded-xl p-4 relative transition-shadow duration-300">
            <label class="block text-sm font-medium text-gray-700 mb-2">Passenger</label>
            <button @click="showPassengerDropdown = !showPassengerDropdown" class="w-full text-left border   text-black rounded-lg p-4 focus:outline-none transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex space-x-4">
                        <div class="flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span x-text="passengers.adult"></span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17l3-3 3 3m-3-3v11m0-14a4 4 0 110-8 4 4 0 010 8z" />
                            </svg>
                            <span x-text="passengers.child"></span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m-8-8h1m14 0h1m-6.4-6.4l.7-.7m-7.9 7.9l.7-.7m7.9 0l-.7-.7m-7.9-7.9l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span x-text="passengers.infant"></span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-lg font-semibold" x-text="`Total: ${passengers.adult + passengers.child + passengers.infant}`"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform duration-300" :class="{'rotate-180': showPassengerDropdown}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </button>
            
            <!-- Passenger Dropdown -->
            <div x-show="showPassengerDropdown" 
                 @click.away="showPassengerDropdown = false"
                 class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl z-50 border border-gray-200 max-h-96 overflow-y-auto">
                <div class="p-4 space-y-4">
                    <!-- Adult, Child, and Infant Count Section -->
                    <template x-for="(label, type) in {'adult': 'Adults', 'child': 'Children', 'infant': 'Infants'}">
                        <div class="flex items-center justify-between py-3 px-4 rounded-lg hover:bg-gray-50 transition-all duration-200">
                            <div class="flex items-center space-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" x-show="type === 'adult'" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17l3-3 3 3m-3-3v11m0-14a4 4 0 110-8 4 4 0 010 8z" x-show="type === 'child'" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m-8-8h1m14 0h1m-6.4-6.4l.7-.7m-7.9 7.9l.7-.7m7.9 0l-.7-.7m-7.9-7.9l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z" x-show="type === 'infant'" />
                                </svg>
                                <div>
                                    <div class="font-medium text-gray-800" x-text="label"></div>
                                    <div class="text-sm text-gray-500" 
                                         x-text="type === 'adult' ? '12+ years' : (type === 'child' ? '2-11 years' : 'Under 2 years')">
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button @click="if (type === 'adult' || passengers[type] > 0) { 
                                                    passengers[type] = Math.max(type === 'adult' ? 1 : 0, passengers[type] - 1) 
                                                }"
                                        class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 transition-all duration-200 flex items-center justify-center text-gray-600 hover:text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <span class="text-lg font-semibold text-gray-800 w-6 text-center" x-text="passengers[type]"></span>
                                <button @click="if (type !== 'infant' || passengers[type] < passengers.adult) {
                                                    passengers[type] = Math.min(9, passengers[type] + 1) 
                                                }"
                                        class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 transition-all duration-200 flex items-center justify-center text-gray-600 hover:text-gray-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
        
                    <!-- Display Total Passenger Count -->
                    <div class="mt-4 border-t pt-4">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-700">Total Passengers:</span>
                            <span x-text="passengers.adult + passengers.child + passengers.infant" class="font-semibold text-2xl text-primary"></span>
                        </div>
                    </div>
        
                    <!-- Flight Instruction: Infant can't be more than Adults -->
                    <div x-show="passengers.infant > passengers.adult" 
                         class="text-sm text-red-500 mt-2 bg-red-50 p-2 rounded-md flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Infants cannot exceed the number of adults.</span>
                    </div>
                </div>
            </div>
        </div>


       

            <!-- Max Price -->
            <div class="max-w-xl">
                <label for="maxPrice" class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                <div class=" relative"
                     >
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
        
          <!-- Max Results -->
    <div x-data="{ focused: false, hovered: false }" class="max-w-xl">
        <label for="maxResults" class="block text-sm font-medium text-gray-700 mb-1">Max Results</label>
        <div class="mt-1 relative rounded-md shadow-sm"
             :class="{ 'ring-2 ring-blue-500': focused }"
             @mouseenter="hovered = true"
             @mouseleave="hovered = false">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            <select 
                name="maxResults" 
                id="maxResults"
                x-model="max" 
                @focus="focused = true"
                @blur="focused = false"
                class="block w-full pl-10 pr-10 py-2 sm:text-sm border-gray-300 rounded-md focus:outline-none transition-colors duration-200 appearance-none"
                :class="{ 'border-blue-500': focused, 'border-gray-300': !focused }"
            >
                <option value="5">5 results</option>
                <option value="10">10 results</option>
                <option value="20">20 results</option>
                <option value="50">50 results</option>
                <option value="100">100 results</option>
                <option value="150">150 results</option>
                <option value="200">200 results</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>
    </div>

    </div>

    <template x-if="tripType !== 'multi'">  
        <div class="flex justify-evenly gap-4 mb-6">
      <!-- From City -->
        <div class=" w-full">
        <div class="relative border rounded-lg p-3">
        <label class="block text-xs text-gray-600 mb-1">From</label>
        <div class="absolute left-3 transform mr-2 pr-[15%] top-1/2 space-x-3">
            <!-- Animated SVG Icon -->
          <img src="/icons/from.svg" alt="from" class="size-7"/>
           </div>
          <input
            id="from"
            type="text"
            wire:model.live.debounce.250ms="searchQueryFrom"
            placeholder="Search City, Airport, or Country"
            autocomplete="off"
            class="pl-[20%] w-full text-xs font-semibold placeholder:text-xs focus:outline-none"
        >
        <!-- Airline search results -->
        <div
            x-show="$wire.showFromDropdown && Array.isArray($wire.fromAirlines) && $wire.fromAirlines.length > 0"
            x-cloak
            class="absolute z-10 w-full left-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg dark:bg-neutral-800 dark:border-neutral-700"
        >
          
                @forelse ($fromAirlines as $airline)
                    <li
                       wire:click="setAirportCode('{{ $airline['airport_code'] }}', '{{ $airline['airport'] }}', 'from')"
                        class="px-4 py-2 flex items-center justify-between hover:bg-blue-100 cursor-pointer dark:hover:bg-neutral-700"
                    >
                        <div class="flex flex-col">
                            <span class="font-semibold text-sm">{{ $airline['airport'] }} - {{ $airline['city'] }}, {{ $airline['country'] }}</span>
                        </div>
                        <span class="space-x-3 border rounded-md bg-primary text-white font-bold p-2 text-xs">{{ $airline['airport_code'] }}</span>
                    </li>
                @empty
                    <li class="px-4 py-2 text-gray-500">No results found</li>
                @endforelse
            </ul>
            
        </div>
         </div>
        </div>

        <!-- To City -->
            <div class=" w-full">
            <div class="relative border rounded-lg p-3">
        <label class="block text-xs text-gray-600 mb-1">To</label>
        <div class="absolute left-3 transform mr-2 pr-[15%] top-1/2 space-x-3">
            <!-- Animated SVG Icon -->
          <img src="/icons/from.svg" alt="from" class="size-7"/>
           </div>
        
        <input
            type="text"
            wire:model.live.debounce.250ms="searchQueryTo"
            placeholder="Search City, Airport, or Country"
            autocomplete="off"
           class="pl-[20%] w-full text-xs font-semibold placeholder:text-xs focus:outline-none"
        >
        <!-- Airline search results -->
        <div
            x-show="$wire.showToDropdown && Array.isArray($wire.toAirlines) && $wire.toAirlines.length > 0"
            x-cloak
            class="absolute z-10 w-full left-0 mt-1 bg-white border border-gray-300 rounded-md shadow-lg dark:bg-neutral-800 dark:border-neutral-700"
        >
        <ul>
            @forelse ($toAirlines as $airline)
                <li
                    wire:click="setAirportCode('{{ $airline['airport_code'] }}', '{{ $airline['airport'] }}', 'to')"
                    class="px-4 py-2 flex items-center justify-between hover:bg-blue-100 cursor-pointer dark:hover:bg-neutral-700"
                >
                    <div class="flex flex-col">
                        <span class="font-semibold text-sm">{{ $airline['airport'] }} - {{ $airline['city'] }}, {{ $airline['country'] }}</span>
                    </div>
                    <span class="space-x-3 border rounded-md bg-primary text-white font-bold p-2 text-xs">{{ $airline['airport_code'] }}</span>
                </li>
            @empty
                <li class="px-4 py-2 text-gray-500">No results found</li>
            @endforelse
        </ul>
        
       
        </div>
    </div>
</div>

        


        <!-- Departure Date -->
        <div x-data="datePickerComponent('{{ $departureDate }}', '{{ $label }}')" class="relative w-full">
            <div class="border relative rounded-lg p-3 mb-4">

                <label class="block text-xs text-gray-600 mb-1 font-medium" x-text="'Depature Date'"></label>
                <div class="absolute left-3 transform mr-2 pr-[15%] top-1/2 space-x-3">
                    <img src="/icons/date.svg" alt="from" class="size-7"/>
                </div>
                <input type="text" 
                    x-model="departureDateFormatted" 
            
                   @click="showDeparturePicker = !showDeparturePicker"
                   readonly
                   placeholder="Select Departure Date"
                   class="w-full pl-[20%] text-lg font-semibold bg-white rounded-md px-2  focus:outline-none cursor-pointer">
        </div>

        <!-- Departure Date Picker -->
        <div x-show="showDeparturePicker" @click.away="showDeparturePicker = false" 
             class="absolute top-full left-0 mt-2 bg-white rounded-lg shadow-lg z-50 w-full">
            <div class="px-4 py-2 bg-primary text-white rounded-t-lg">
                <h2 x-text="'Select Departure Date'" class="text-sm font-semibold"></h2>
            </div>
            <div class="p-4">
                <!-- Month Navigation -->
                <div class="flex justify-between items-center mb-4">
                    <button @click.prevent="prevMonth('departure')" type="button" class="hover:text-gray-800 bg-primary text-white px-2 py-1 rounded-md">&lt;</button>
                    <div x-text="months[currentMonth.getMonth()] + ' ' + currentMonth.getFullYear()" class="font-semibold text-lg text-gray-700"></div>
                    <button @click.prevent="nextMonth('departure')" type="button" class="text-white hover:text-gray-800 bg-primary px-2 py-1 rounded-md">&gt;</button>
                </div>
                <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-600 mb-2">
                    <template x-for="day in days" :key="day">
                        <div x-text="day" class="py-1"></div>
                    </template>
                </div>
                <div class="grid grid-cols-7 gap-1">
                    <template x-for="(date, index) in departureCalendarDates" :key="index">
                        <div>
                            <div 
                                @click="selectDate(date, 'departure')"
                                :class="{
                                    'text-gray-300': !date || isPastDate(date, 'departure'),
                                    'text-gray-800 hover:bg-blue-100 cursor-pointer': date && !isPastDate(date, 'departure'),
                                    'bg-primary text-white': date && isSelected(date, 'departure'),
                                    'border border-primary animate-bounce text-primary': date && isToday(date),
                                    'rounded-full flex items-center justify-center h-8 w-8': true
                                }"
                                x-text="date ? date.getDate() : ''">
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

        <!-- Return Date (only visible for round trips) -->
    <template x-if="tripType === 'round'" class="w-full">
        <div x-data="datePickerComponent('{{ $returnDate }}', 'Return Date')" class="relative w-full">
            <div class="border relative rounded-lg p-3">
                <label class="block text-xs text-gray-600 mb-1 font-medium" x-text="'Return Date'"></label>
                <div class="absolute left-3 transform mr-2 pr-[15%] top-1/2 space-x-3">
                    <img src="/icons/date.svg" alt="to" class="size-7"/>
                </div>
                <input type="text" 
                       x-model="returnDateFormatted" 
                       @click="showReturnPicker = !showReturnPicker"
                       readonly
                       placeholder="Select Return Date"
                       class="w-full pl-[20%] text-lg font-semibold bg-white rounded-md px-2 focus:outline-none cursor-pointer">
            </div>

            <!-- Return Date Picker -->
            <div x-show="showReturnPicker" @click.away="showReturnPicker = false" 
                 class="absolute top-full left-0 mt-2 bg-white rounded-lg shadow-lg z-50 w-full">
                <div class="px-4 py-2 bg-primary text-white rounded-t-lg">
                    <h2 x-text="'Select Return Date'" class="text-sm font-semibold"></h2>
                </div>
                <div class="p-4">
                    <!-- Month Navigation -->
                    <div class="flex justify-between items-center mb-4">
                        <button @click.prevent="prevMonth('return')" type="button" class="hover:text-gray-800 bg-primary text-white px-2 py-1 rounded-md">&lt;</button>
                        <div x-text="months[currentMonth.getMonth()] + ' ' + currentMonth.getFullYear()" class="font-semibold text-lg text-gray-700"></div>
                        <button @click.prevent="nextMonth('return')" type="button" class="text-white hover:text-gray-800 bg-primary px-2 py-1 rounded-md">&gt;</button>
                    </div>
                    <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-600 mb-2">
                        <template x-for="day in days" :key="day">
                            <div x-text="day" class="py-1"></div>
                        </template>
                    </div>
                    <div class="grid grid-cols-7 gap-1">
                        <template x-for="(date, index) in returnCalendarDates" :key="index">
                            <div>
                                <div 
                                    @click="selectDate(date, 'return')"
                                    :class="{
                                        'text-gray-300': !date || isPastDate(date, 'return'),
                                        'text-gray-800 hover:bg-blue-100 cursor-pointer': date && !isPastDate(date, 'return'),
                                        'bg-primary text-white': date && isSelected(date, 'return'),
                                        'border border-primary animate-bounce text-primary': date && isToday(date),
                                        'rounded-full flex items-center justify-center h-8 w-8': true
                                    }"
                                    x-text="date ? date.getDate() : ''">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </template>




     
        
            <!-- Return Date (for Round Trip) -->
       
        </div>
    </template>
    
{{-- 
    <template x-if="tripType === 'multi'">
        <div class="space-y-4">
            <!-- Iterate over the multiCityFlights array using Alpine.js -->
            <template x-for="(flight, index) in multiCityFlights" :key="index">
                <div class="grid grid-cols-4 gap-4 mb-4">
                    <!-- From City -->
                    <div class="col-span-1">
                        <div class="border rounded-lg p-3">
                            <label class="block text-xs text-gray-600 mb-1">From</label>
                            <input type="text" x-model="flight.from" placeholder="Select City" 
                                   class="w-full text-lg font-semibold focus:outline-none">
                        </div>
                    </div>
    
                    <!-- To City -->
                    <div class="col-span-1">
                        <div class="border rounded-lg p-3">
                            <label class="block text-xs text-gray-600 mb-1">To</label>
                            <input type="text" x-model="flight.to" placeholder="Select City" 
                                   class="w-full text-lg font-semibold focus:outline-none">
                        </div>
                    </div>
    
                    <!-- Departure Date Picker (without using index) -->
                    <div class="col-span-1">
                        <!-- Custom UI Date Picker without index in name -->
                        <div x-data="customDatePicker('Departure Date', '2024-01-01')" class="relative w-80 mx-auto">
                            <div class="border rounded-lg p-3 bg-gray-100">
                                <label class="block text-xs text-gray-600 mb-1 font-medium">Departure Date</label>
                                <input type="text" x-model="formattedDate" 
                                       @click="showPicker = !showPicker" readonly 
                                       placeholder="Select Date" class="w-full text-lg font-semibold bg-white rounded-md px-2 py-1 focus:outline-none cursor-pointer">
                            </div>
                            <div x-show="showPicker" @click.away="showPicker = false" 
                                 class="absolute top-full left-0 mt-2 bg-white rounded-lg shadow-lg z-50 w-full">
                                <div class="px-4 py-2 bg-blue-500 text-white rounded-t-lg">
                                    <h2 x-text="'Select a date for ' + label" class="text-sm font-semibold"></h2>
                                </div>
                                <div class="p-4">
                                    <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-600 mb-2">
                                        <template x-for="day in days">
                                            <div x-text="day" class="py-1"></div>
                                        </template>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1">
                                        <template x-for="(date, index) in calendarDates" :key="index">
                                            <div @click="date && selectDate(date)">
                                                <div x-text="date ? date.getDate() : ''" :class="{
                                                    'text-gray-300': !date || isPastDate(date),
                                                    'text-gray-800 hover:bg-blue-100 cursor-pointer': date && !isPastDate(date),
                                                    'bg-blue-500 text-white': date && isSelected(date),
                                                    'border border-blue-500 text-white': date && isToday(date),
                                                    'rounded-full flex items-center justify-center h-8 w-8': true
                                                }"></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <!-- Return Date Picker (without using index) -->
                    <div class="col-span-1">
                        <!-- Custom UI Date Picker without index in name -->
                        <div x-data="customDatePicker('Return Date', '2024-01-01')" class="relative w-80 mx-auto">
                            <div class="border rounded-lg p-3 bg-gray-100">
                                <label class="block text-xs text-gray-600 mb-1 font-medium">Return Date</label>
                                <input type="text" x-model="formattedDate" 
                                       @click="showPicker = !showPicker" readonly 
                                       placeholder="Select Date" class="w-full text-lg font-semibold bg-white rounded-md px-2 py-1 focus:outline-none cursor-pointer">
                            </div>
                            <div x-show="showPicker" @click.away="showPicker = false" 
                                 class="absolute top-full left-0 mt-2 bg-white rounded-lg shadow-lg z-50 w-full">
                                <div class="px-4 py-2 bg-blue-500 text-white rounded-t-lg">
                                    <h2 x-text="'Select a date for ' + label" class="text-sm font-semibold"></h2>
                                </div>
                                <div class="p-4">
                                    <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-600 mb-2">
                                        <template x-for="day in days">
                                            <div x-text="day" class="py-1"></div>
                                        </template>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1">
                                        <template x-for="(date, index) in calendarDates" :key="index">
                                            <div @click="date && selectDate(date)">
                                                <div x-text="date ? date.getDate() : ''" :class="{
                                                    'text-gray-300': !date || isPastDate(date),
                                                    'text-gray-800 hover:bg-blue-100 cursor-pointer': date && !isPastDate(date),
                                                    'bg-blue-500 text-white': date && isSelected(date),
                                                    'border border-blue-500 text-white': date && isToday(date),
                                                    'rounded-full flex items-center justify-center h-8 w-8': true
                                                }"></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <!-- Remove Flight Button for subsequent flights (not the first one) -->
                    <div class="col-span-4 flex justify-end" x-show="index > 0">
                        <button @click="removeFlight(index)" class="text-red-500 hover:text-red-700 text-sm mt-2">
                            Remove this flight
                        </button>
                    </div>
                </div>
            </template>
    
            <!-- Add Flight Button -->
            <button @click="addFlight()" 
                    x-show="multiCityFlights.length < 8"
                    class="text-blue-500 hover:text-blue-700 font-medium">
                + Add Another Flight
            </button>
        </div>
    </template>
     --}}
    <!-- Multi City Form -->
 

    <!-- Search Button -->
    <div class="mt-6 flex justify-center">
        <button wire:click="search" class="bg-primary hover:bg-black text-white font-bold py-3 px-12 rounded-full text-lg uppercase">
            Search
        </button>
    </div>
</div>



<script>
    function datePickerComponent(minDate, label) {
        return {
            showDeparturePicker: false,
            showReturnPicker: false,
            departureDateFormatted: '',
            returnDateFormatted: '',
            selectedDepartureDate: null,
            selectedReturnDate: null,
            currentMonth: new Date(),
            days: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            departureCalendarDates: [],
            returnCalendarDates: [],
            minDate: minDate ? new Date(minDate) : null,
            label: label,
            tripType: 'round', // Example: set based on your actual app state
    
            init() {
                this.$watch('currentMonth', () => {
                    this.generateCalendar('departure');
                    this.generateCalendar('return');
                });
                this.generateCalendar('departure');
                this.generateCalendar('return');
            },
    
            generateCalendar(type) {
                const year = this.currentMonth.getFullYear();
                const month = this.currentMonth.getMonth();
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
    
                const dates = [];
                for (let i = 0; i < firstDay; i++) dates.push(null);
                for (let day = 1; day <= daysInMonth; day++) {
                    dates.push(new Date(year, month, day));
                }
    
                if (type === 'departure') {
                    this.departureCalendarDates = dates;
                } else {
                    this.returnCalendarDates = dates;
                }
            },
    
            formatDate(date) {
                const day = this.days[date.getDay()];
                const month = this.months[date.getMonth()].slice(0, 3);
                return `${day}, ${date.getDate()} ${month} ${date.getFullYear()}`;
            },
    
            isSelected(date, type) {
                const selectedDate = type === 'departure' ? this.selectedDepartureDate : this.selectedReturnDate;
                return selectedDate && date.getDate() === selectedDate.getDate() &&
                    date.getMonth() === selectedDate.getMonth() && date.getFullYear() === selectedDate.getFullYear();
            },
    
            isToday(date) {
                const today = new Date();
                return date && date.getDate() === today.getDate() &&
                    date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear();
            },
    
            isPastDate(date, type) {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                return date < today || (type === 'return' && date < this.minDate);
            },
    
   
            selectDate(date, type) {
                if (!date || this.isPastDate(date, type)) return;
    
                if (type === 'departure') {
                    this.selectedDepartureDate = date;
                    this.departureDateFormatted = this.formatDate(date);
                    this.showDeparturePicker = false;
                    this.$wire.call('dateSelected', date.toISOString().split('T')[0], type); 
                
                } else {
                    this.selectedReturnDate = date;
                    this.returnDateFormatted = this.formatDate(date);
                    this.showReturnPicker = false;
                    this.$wire.call('dateSelected', date.toISOString().split('T')[0], type); 
                }
            },
    
            prevMonth(type) {
                const newDate = new Date(this.currentMonth);
                newDate.setMonth(newDate.getMonth() - 1);
                this.currentMonth = newDate;
            },
    
            nextMonth(type) {
                const newDate = new Date(this.currentMonth);
                newDate.setMonth(newDate.getMonth() + 1);
                this.currentMonth = newDate;
            }
        };
    }
    </script>