<div x-data="{ sidebarIsOpen: false }"
    selectedFlight: @entangle('selectedFlight')
    x-on:click.outside="sidebarIsOpen = false"
    x-on:keydown.esc.window="sidebarIsOpen = false"
    class="bg-gray-50">

    <!-- toggle button -->
    <style>
        .bg-green-800 {
            background-color: #16a34a;
        }

        .toggle {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            gap: 8px;
            /* space between toggle and label */
        }

        .toggle input {
            display: none;
        }

        .slider {
            width: 50px;
            height: 24px;
            background-color: black;
            /* default OFF */
            border-radius: 50px;
            position: relative;
            transition: background-color 0.3s;
        }

        .slider::before {
            content: "";
            position: absolute;
            width: 18px;
            height: 18px;
            left: 3px;
            top: 3px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        /* When checked */
        .toggle input:checked+.slider {
            background-color: red;
            /* ON background */
        }

        .toggle input:checked+.slider::before {
            transform: translateX(26px);
            /* knob slides right */
        }

        .label-text {
            color: black;
            /* label color */
            font-size: 14px;
        }
    </style>
    <nav x-cloak x-show="sidebarIsOpen" x-trap="sidebarIsOpen" class="fixed right-0 z-[99999] overflow-y-auto flex h-[100vh] md:w-[50%] w-full pb-20 shrink-0 flex-col bg-gradient-to-br from-white via-white to-neutral-50/90 backdrop-blur-xl p-8 shadow-2xl transition-transform duration-300" aria-label="edit listing" x-transition:enter="transition duration-300 ease-out" x-transition:enter-end="translate-x-0" x-transition:enter-start="translate-x-80" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="translate-x-80" x-transition:leave-start="translate-x-0">
        <!-- Elegant header -->
        <div class="relative mb-8">
            <button class="absolute -left-4 top-1 p-2.5 hover:bg-black/5 rounded-full transition-colors duration-200" x-on:click="sidebarIsOpen = false">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.5" class="size-6 text-neutral-600 " aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="text-center">
                <h3 class="text-2xl font-semibold text-neutral-800 ">Flight Details</h3>
                <p class="text-sm text-neutral-500  mt-1">details</p>
            </div>
        </div>

        <div class="flex justify-between mb-6">
            @if($selectedFlight)
            <div>
                <label class="toggle">
                    <input wire:input.live.debounce.300ms="payment('{{$selectedFlight['id']}}', '{{$selectedFlight['payment_status']}}')" type="checkbox" {{   $selectedFlight['payment_status'] === 'APPROVED' ? 'checked' : '' }}>
                    <span class="slider"></span>
                    <span class="label-text">Approve Payment</span>
                </label>
            </div>
            <div>
                <label class="toggle">
                    <input wire:input.live.debounce.300ms="order('{{$selectedFlight['id']}}', '{{$selectedFlight['order_status']}}')" type="checkbox" {{   $selectedFlight['order_status'] === 'CONFIRMED' ||  $selectedFlight['order_status'] === 'COMPLETED'? 'checked' : '' }}>
                    <span class="slider"></span>
                    <span class="label-text">Mark as Done</span>
                </label>
            </div>
            @endif
        </div>

        <!-- Luxurious form -->
        <div class="py-4">
            @if ($selectedFlight)
            <div class="max-w-7xl mx-auto space-y-8">
                {{-- Header Section with Gradient --}}
                <div class="bg-gradient-to-r from-black to-purple-600 rounded-xl p-8 text-white shadow-xl">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-3xl font-bold mb-2">Flight Booking Details</h2>
                            <p class="text-indigo-100">PNR: {{ $selectedFlight['Pnr'] ?? 'N/A' }}</p>
                            <p class="text-indigo-100">Order ID: {{ $selectedFlight['order_id'] ?? 'N/A' }}</p>
                        </div>
                        <span class="px-6 py-2 rounded-full {{ $selectedFlight['payment_status'] === 'PENDING' ? 'bg-yellow-400 text-black' : 'bg-green-500' }} font-semibold shadow-lg">
                            {{ $selectedFlight['payment_status'] ?? 'STATUS UNKNOWN' }}
                        </span>
                    </div>
                </div>

                {{-- <div x-data="{ tab: 'flightOrderIssue' }" class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg p-6">
                            <!-- Improved Tab Navigation -->
                            <div class="flex space-x-2 border-b border-gray-200 mb-8">
                                <button 
                                    @click="tab = 'flightOrderIssue'" 
                                    :class="{'border-b-2 border-blue-500 text-blue-600': tab === 'flightOrderIssue',
                                            'text-gray-500 hover:text-gray-700': tab !== 'flightOrderIssue'}" 
                                    class="py-3 px-6 font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-t-lg">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        <span>Flight Issue</span>
                                    </div>
                                </button>
                                
                                <button 
                                    @click="tab = 'queueListing'" 
                                    :class="{'border-b-2 border-blue-500 text-blue-600': tab === 'queueListing',
                                            'text-gray-500 hover:text-gray-700': tab !== 'queueListing'}" 
                                    class="py-3 px-6 font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-t-lg">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                        <span>Queue Listing</span>
                                    </div>
                                </button>
                                
                                <button 
                                    @click="tab = 'flightManagement'" 
                                    :class="{'border-b-2 border-blue-500 text-blue-600': tab === 'flightManagement',
                                            'text-gray-500 hover:text-gray-700': tab !== 'flightManagement'}" 
                                    class="py-3 px-6 font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-t-lg">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Flight Management</span>
                                    </div>
                                </button>
                            </div>
                        
                            <!-- Flight Order Tab -->
                            <div x-show="tab === 'flightOrderIssue'" 
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 class="space-y-6">
                                <h3 class="text-2xl font-bold text-gray-800 mb-6">Flight Order Issue</h3>

                                @if (session()->has('success'))
                                <div class="bg-green-500 text-white p-2 mb-4 rounded">{{ session('success') }}
            </div>
            @endif
            @if (session()->has('error'))
            <div class="bg-red-500 text-white p-2 mb-4 rounded">{{ session('error') }}</div>
            @endif
            <form wire:submit.prevent="processOrder" class="max-w-2xl mx-auto bg-white p-6 rounded-xl shadow-sm">

                <div class="space-y-8">
                    <!-- Card Number and Brand Row -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2" x-data="{ 
                                                formatCardNumber(e) {
                                                    let input = e.target;
                                                    let value = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                                                    let formattedValue = '';
                                                    
                                                    // Add space after every 4 digits
                                                    for(let i = 0; i < value.length; i++) {
                                                        if(i > 0 && i % 4 === 0) {
                                                            formattedValue += ' ';
                                                        }
                                                        formattedValue += value[i];
                                                    }
                                                    
                                                    // Limit to 19 characters (16 digits + 3 spaces)
                                                    formattedValue = formattedValue.substring(0, 19);
                                                    
                                                    // Update the input value
                                                    input.value = formattedValue;
                                                    
                                                    // Update the Livewire model
                                                    @this.set('number', formattedValue);
                                                }
                                            }">
                            <label class="block">
                                <span class="text-gray-700 font-medium block mb-2">Card Number</span>
                                <div class="relative">
                                    <input type="text"
                                        x-on:input="formatCardNumber($event)"
                                        wire:model="number"
                                        placeholder="1234 5678 9012 3456"
                                        maxlength="19"
                                        class="pl-4 pr-10 py-3 w-full rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                    </span>
                                </div>
                            </label>
                            @error('number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block">
                                <span class="text-gray-700 font-medium block mb-2">Card Brand</span>
                                <select
                                    wire:model="brand"
                                    class="w-full rounded-lg border-2 border-gray-200 py-3 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all">
                                    <option value="">Select</option>
                                    <option value="visa">Visa</option>
                                    <option value="mastercard">Mastercard</option>
                                    <option value="amex">American Express</option>
                                </select>
                            </label>
                            @error('brand') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Cardholder Name -->
                    <div>
                        <label class="block">
                            <span class="text-gray-700 font-medium block mb-2">Card Holder Name</span>
                            <input type="text"
                                wire:model="holder"
                                placeholder="Name as shown on card"
                                class="w-full rounded-lg border-2 border-gray-200 py-3 px-4 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all">
                        </label>
                        @error('holder') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <!-- Expiry and CVV Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block">
                                <span class="text-gray-700 font-medium block mb-2">Expiry Date</span>
                                <input type="month"
                                    wire:model="expiryDate"
                                    class="w-full rounded-lg border-2 border-gray-200 py-3 px-4 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all">
                            </label>
                            @error('expiryDate') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror

                        </div>

                        <div>
                            <label class="block">
                                <span class="text-gray-700 font-medium block mb-2">Security Code (CVV)</span>
                                <div class="relative">
                                    <input type="text"
                                        wire:model="securityCode"
                                        placeholder="123"
                                        maxlength="3"
                                        class="w-full rounded-lg border-2 border-gray-200 py-3 pl-4 pr-10 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </span>
                                </div>
                            </label>
                            @error('securityCode') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="my-8 pt-6 border-t border-gray-100">
                    <button wire:loading.remove type="submit"
                        class="w-full md:w-auto float-right bg-primary cursor-pointer text-white font-medium px-8 py-3 rounded-lg 
                                                   shadow-sm transition-all duration-200 flex items-center justify-center space-x-2 
                                                   focus:outline-none focus:ring-2 ">
                        <span>Complete Payment</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </button>

                    <div wire:loading wire:target="processOrder" class="w-full md:w-auto float-right bg-primary cursor-pointer text-white font-medium px-8 py-3 rounded-lg 
                                        shadow-sm transition-all duration-200 flex items-center justify-center space-x-2 
                                        focus:outline-none focus:ring-2 ">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" class="size-10 text-center items-center flex mx-auto fill-white motion-safe:animate-spin">
                            <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
                            <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
                        </svg>
                    </div>
                </div>

            </form>
        </div>

        <!-- Queue Listing Tab -->
        <div x-show="tab === 'queueListing'"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            class="space-y-6">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Queue Listing</h3>
            <div class="bg-gray-50 p-6 rounded-lg">
                <p class="text-gray-600">Queue listing content goes here...</p>
            </div>
        </div>

        <!-- Flight Management Tab -->
        <div x-show="tab === 'flightManagement'"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            class="space-y-6">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Flight Management</h3>
            <div class="flex space-x-4">
                <button @click="deleteFlight"
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-md shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Delete Flight
                </button>
                <button @click="getFlight"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Get Flight
                </button>
            </div>
        </div>
</div> --}}
{{-- Price Summary Card --}}
<div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
    <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-100">
        <h3 class="text-xl font-bold text-gray-800">Price Summary</h3>
    </div>
    <div class="p-6 space-y-4">
        <div class="flex justify-between items-center py-2">
            <span class="text-gray-600">Base Price</span>
            <span class="text-lg font-semibold">NGN {{ number_format($selectedFlight['flight_offers']['price']['base'] ?? 0, 2) }}</span>
        </div>

        <div class="flex justify-between items-center pt-4 border-t border-gray-100">
            <span class="text-lg font-bold text-gray-800">Total Amount</span>
            <span class="text-2xl font-bold text-indigo-600">
                {{ $selectedFlight['flight_offers']['price']['currency'] ?? 'NGN' }} {{ number_format($selectedFlight['flight_offers']['price']['total'] ?? 0, 2) }}
            </span>
        </div>
    </div>
</div>

{{-- Flight Itinerary with Timeline --}}
<div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
    <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-100">
        <h3 class="text-xl font-bold text-gray-800">Flight Itinerary</h3>
    </div>
    <div class="p-6">
        @if(!empty($selectedFlight['flight_offers']['itineraries'][0]['segments']))
        @foreach($selectedFlight['flight_offers']['itineraries'][0]['segments'] as $index => $segment)
        <div class="relative pb-8">
            {{-- Timeline line --}}


            <div class="flex items-center mb-8">
                {{-- Timeline circle --}}
                <div class="absolute left-0 bg-primary rounded-full w-12 h-12 flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold">{{ $index + 1 }}</span>
                </div>

                {{-- Content (all the flight details) --}}
                <div class="ml-16 bg-gradient-to-r from-gray-50 to-white rounded-lg p-6 shadow-sm border border-gray-100 w-full">
                    <div class="grid grid-cols-3 gap-8">
                        {{-- Departure Details --}}
                        <div>
                            <div class="text-sm text-gray-500">Departure</div>
                            <div class="text-xl font-bold text-gray-800">{{ $segment['departure']['iataCode'] ?? 'N/A' }}</div>
                            <div class="text-indigo-600">
                                {{ \Carbon\Carbon::parse($segment['departure']['at'])->format('D, M d, Y') }}
                            </div>
                            <div class="text-gray-600">
                                {{ \Carbon\Carbon::parse($segment['departure']['at'])->format('H:i') }}
                            </div>
                        </div>

                        {{-- Flight Details --}}
                        <div class="text-center">
                            <div class="text-sm text-gray-500">Flight</div>
                            <div class="text-lg font-semibold text-indigo-600">
                                {{ $segment['carrierCode'] ?? '' }}{{ $segment['number'] ?? 'N/A' }}
                            </div>
                            <div class="text-gray-600">Duration: {{ $segment['duration'] ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">Aircraft: {{ $segment['aircraft']['code'] ?? 'N/A' }}</div>
                        </div>

                        {{-- Arrival Details --}}
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Arrival</div>
                            <div class="text-xl font-bold text-gray-800">{{ $segment['arrival']['iataCode'] ?? 'N/A' }}</div>
                            <div class="text-indigo-600">
                                {{ \Carbon\Carbon::parse($segment['arrival']['at'])->format('D, M d, Y') }}
                            </div>
                            <div class="text-gray-600">
                                {{ \Carbon\Carbon::parse($segment['arrival']['at'])->format('H:i') }}
                            </div>
                            @if(!empty($segment['arrival']['terminal']))
                            <div class="text-sm text-gray-500">Terminal {{ $segment['arrival']['terminal'] }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @else
        <p class="text-gray-500 text-center py-4">No itinerary information available</p>
        @endif
    </div>
</div>

{{-- Passenger Information --}}
<div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
    <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-100">
        <h3 class="text-xl font-bold text-gray-800">Passenger Details</h3>
    </div>
    <div class="p-6">
        @if(!empty($selectedFlight['travelers']))
        @foreach($selectedFlight['travelers'] as $traveler)
        <div class="bg-gray-50 rounded-lg p-6 mb-4 last:mb-0">
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <span class="text-sm text-gray-500">Full Name</span>
                        <div class="text-lg font-semibold text-gray-800">
                            {{ $traveler['name']['firstName'] ?? 'N/A' }} {{ $traveler['name']['lastName'] ?? '' }}
                        </div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Gender</span>
                        <div class="text-gray-800">{{ $traveler['gender'] ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Date of Birth</span>
                        <div class="text-gray-800">{{ $traveler['dateOfBirth'] ?? 'N/A' }}</div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <span class="text-sm text-gray-500">Email</span>
                        <div class="text-gray-800">{{ $traveler['contact']['emailAddress'] ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Phone</span>
                        <div class="text-gray-800">
                            @if(!empty($traveler['contact']['phones'][0]))
                            +{{ $traveler['contact']['phones'][0]['countryCallingCode'] ?? '' }}
                            {{ $traveler['contact']['phones'][0]['number'] ?? 'N/A' }}
                            @else
                            N/A
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($traveler['documents'][0]))
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Travel Document</h4>
                <div class="grid md:grid-cols-3 gap-6">
                    <div>
                        <span class="text-sm text-gray-500">Passport Number</span>
                        <div class="text-gray-800">{{ $traveler['documents'][0]['number'] ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Nationality</span>
                        <div class="text-gray-800">{{ $traveler['documents'][0]['nationality'] ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Expiry Date</span>
                        <div class="text-gray-800">{{ $traveler['documents'][0]['expiryDate'] ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endforeach
        @else
        <p class="text-gray-500 text-center py-4">No passenger information available</p>
        @endif
    </div>
</div>

{{-- Baggage and Additional Info --}}
{{-- <div class="grid md:grid-cols-2 gap-8">
                         
                            <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
                                <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-100">
                                    <h3 class="text-xl font-bold text-gray-800">Baggage Information</h3>
                                </div>
                                <div class="p-6">
                                    @if(!empty($selectedFlight['flight_offers']['travelerPricings'][0]['fareDetailsBySegment']))
                                        @foreach($selectedFlight['flight_offers']['travelerPricings'][0]['fareDetailsBySegment'] as $index => $segment)
                                            <div class="mb-4 last:mb-0">
                                                <div class="font-semibold text-gray-700">Segment {{ $index + 1 }}</div>
<div class="mt-2 space-y-2">
    <div class="flex justify-between">
        <span class="text-gray-600">Cabin Class</span>
        <span class="font-medium">{{ $segment['cabin'] ?? 'N/A' }}</span>
    </div>
    <div class="flex justify-between">
        <span class="text-gray-600">Checked Bags</span>
        <span class="font-medium">
            {{ $segment['includedCheckedBags']['quantity'] ?? '0' }} pieces
        </span>
    </div>
</div>
</div>
@endforeach
@else
<p class="text-gray-500 text-center py-4">No baggage information available</p>
@endif
</div>
</div>


<div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100">
    <div class="bg-gradient-to-r from-gray-50 to-white p-6 border-b border-gray-100">
        <h3 class="text-xl font-bold text-gray-800">Fare Information</h3>
    </div>
    <div class="p-6">
        @if(!empty($selectedFlight['flight_offers']))
        <div class="space-y-4">
            <div class="flex justify-between">
                <span class="text-gray-600">Fare Type</span>
                <span class="font-medium">
                    {{ $selectedFlight['flight_offers']['pricingOptions']['fareType'][0] ?? 'N/A' }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Last Ticketing Date</span>
                <span class="font-medium">
                    {{ $selectedFlight['flight_offers']['lastTicketingDate'] ?? 'N/A' }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Validating Airline</span>
                <span class="font-medium">
                    {{ $selectedFlight['flight_offers']['validatingAirlineCodes'][0] ?? 'N/A' }}
                </span>
            </div>
        </div>
        @else
        <p class="text-gray-500 text-center py-4">No fare information available</p>
        @endif
    </div>
</div>
</div> --}}
</div>
@else
<div class="text-center py-12">
    <div class="text-gray-400 text-xl">No flight selected</div>
    <p class="text-gray-500 mt-2">Please select a flight to view details</p>
</div>
@endif
</div>
</nav>

<div class="min-h-screen">
    <!-- Main Content -->
    <main class=" p-8">
        <!-- Header -->
        <header class="mb-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">Flight Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <a href="/flight/search" class="px-4 py-2 bg-primary text-white rounded-lg transition-colors duration-300">
                        + New Booking
                    </a>
                    <div class="relative">
                        <input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition-all duration-300">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Bookings</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{$flightcount}}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <span>12% from last month</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Revenue</p>
                        <h3 class="text-2xl font-bold text-gray-800">$0.00</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <span>8% from last month</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Active Customers</p>
                        <h3 class="text-2xl font-bold text-gray-800">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                    <span>5% from last month</span>
                </div>
            </div>

            {{-- <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">Satisfaction Rate</p>
                            <h3 class="text-2xl font-bold text-gray-800">94%</h3>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        </svg>
                        <span>2% from last month</span>
                    </div>
                </div> --}}
        </div>

        <!-- Recent Bookings Table -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Recent Bookings</h2>
                    <p>{{$dateFilter ? $dateFilter->toDayDateTimeString() : ""}}</p>
                </div>
                <div class="flex justify-end items-center mb-6">
                    <input type="date" wire:model.live.debounce.300ms="dateFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition-all duration-300" />
                    <button class="text-blue-600 hover:text-blue-700 transition-colors duration-300 btn btn-secondary ml-2" wire:click="dateFilterFun">
                        View All
                    </button>
                </div>
            </div>
            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 transition-all duration-300 shadow-sm"
                        placeholder="Search by Airport, code , City, Country...">
                    @if($search)
                    <button
                        wire:click="clearSearch"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">S/n</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Pnr </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Customer</th>
                            {{-- <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Flight</th> --}}
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Amount</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Date</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Payment Status</th>
                            {{-- <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Issue Order</th> --}}
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($flightOrders as $index => $flight)
                        @php

                        $travelers = is_string($flight->travelers) ? json_decode($flight->travelers, true) : $flight->travelers;

                        $flightOffers = is_string($flight->flight_offers) ? json_decode($flight->flight_offers, true) : $flight->flight_offers;

                        // Ensure there is at least one flight offer
                        if (!empty($flightOffers) && is_array($flightOffers)) {
                        $flightOffer = $flightOffers;

                        // Extract Grand Total
                        $grandTotal = $flightOffer['price']['grandTotal'] ?? 'N/A';
                        $currency = $flightOffer['price']['currency'] ?? '';


                        // Safely access itineraries
                        $itineraries = isset($flightOffer['itineraries']) && is_array($flightOffer['itineraries']) ? $flightOffer['itineraries'] : [];

                        $firstSegment = isset($itineraries[0]['segments'][0]) ? $itineraries[0]['segments'][0] : null;
                        $lastItineraryIndex = count($itineraries) - 1;
                        $lastSegment = null;
                        if ($lastItineraryIndex >= 0) {
                        $lastSegmentIndex = count($itineraries[$lastItineraryIndex]['segments']) - 1;
                        $lastSegment = $lastSegmentIndex >= 0 ? $itineraries[$lastItineraryIndex]['segments'][$lastSegmentIndex] : null;
                        }
                        // Extract departure and arrival details
                        $departureAirport = $firstSegment['departure']['iataCode'] ?? 'N/A';
                        $arrivalAirport = $lastSegment['arrival']['iataCode'] ?? 'N/A';



                        }
                        @endphp

                        <tr class="hover:bg-gray-50 transition-colors duration-300">
                            <td class="px-2 py-4 text-sm text-gray-800">
                                {{ ($flightOrders->currentPage() - 1) * $flightOrders->perPage() + $index + 1 }}
                            </td>
                            <td class="px-2 py-4  text-[10px] text-gray-800">{{$flight->pnr}}</td>
                            <td class="px-2 py-4">
                                <div class="flex items-center">
                                    {{-- <img src="/api/placeholder/32/32" class="w-8 h-8 rounded-full mr-3" alt="user"> --}}
                                    <div>
                                        <p class="text-xs font-medium text-gray-800">{{ $travelers[0]['name']['firstName'] }} {{ $travelers[0]['name']['lastName'] }}</p>
                                        <p class="text-[11px] text-gray-500">{{ $travelers[0]['contact']['emailAddress'] }}</p>
                                    </div>
                                </div>
                            </td>
                            {{-- <td class="px-2 py-4 text-sm text-gray-800">  {{ $departureAirport }} → {{ $arrivalAirport }}</td> --}}
                            <td class="px-2 py-4 text-sm text-gray-800">
                                <div class=" flex flex-col">
                                    <span>
                                        {{ $currency }}
                                    </span>
                                </div> {{ number_format((float) $grandTotal , 2) }}
                            </td>
                            <td class="px-2 py-4 flex flex-wrap text-[11px] text-gray-800">{{ \Carbon\Carbon::parse($flight->created_at)->format('M j, Y g:i A') }}

                            </td>
                            <td class="px-2 py-4">
                                <span class="px-3 py-1 text-[12px] font-medium rounded-full {{ $flight->payment_status === 'APPROVED' ? 'bg-green-800' : 'bg-red-800' }} text-white">{{$flight->payment_status}}</span>
                            </td>
                            {{-- <td class="px-2 py-4">
                                    <span class="p-2 font-bold text-xs shrink-0  rounded-full bg-yellow-600 text-white">Issue Order</span>
                                </td> --}}
                            <td class="px-2 py-4">
                                <button wire:click="selectFlight({{ $flight->id }})" x-on:click="sidebarIsOpen = ! sidebarIsOpen" class="{{ $flight->order_status === 'CONFIRMED' || $flight->order_status === 'COMPLETED' ? 'text-success' : 'text-primary' }} text-sm transition-colors duration-300">View Details</button>
                            </td>
                        </tr>


                        @endforeach


                    </tbody>

                    {{--<div class="mt-4">
                            {{$flightOrders->links()}}
            </div>--}}
            </table>
            <div class="mt-4">
                {{$flightOrders->links()}}
            </div>
        </div>
</div>
</main>
</div>
</div>