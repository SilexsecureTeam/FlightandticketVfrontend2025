<div>
    @if($showToSource == "admin")
    @include('components.adminHead')
    <script>
        document.getElementById('hideforAdmid').style.display = 'none';
    </script>
    @endif
    <div x-data="{
    timer: 300,
    formatTime() {
        const minutes = Math.floor(this.timer / 60);
        const seconds = this.timer % 60;
        return `${minutes}:${seconds.toString().padStart(2, '0')}`;
    }
}" x-init="setInterval(() => {
    if (timer > 0) {
        timer--;
    } else {
        window.location.href = '/search';
    }
}, 1000);">
        <!-- Timer Component (Uncommented for better user experience) -->
        <div class="bg-gradient-to-r from-red-600 to-red-800 text-white py-2 px-4 z-50 shadow-lg">
            <div class="max-w-7xl mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-semibold text-xs">Session expires in</span>
                </div>
                <div class="text-lg font-bold" x-text="formatTime()"></div>
            </div>
        </div>

        <!-- Improved Stepper Component -->
        <div x-data="{
        currentStep: 'payment',
        steps: [
            { id: 'search-flight', label: 'Search Flight' },
            { id: 'select-flight', label: 'Select Flight' },
            { id: 'additional-info', label: 'Additional Info' },
            { id: 'payment', label: 'Payment' }
        ],
        isEditOpen: false
    }" class="bg-black text-white sticky w-full z-[99]">

            <div class="px-2 sm:px-3 md:px-6 py-5">
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
                        <a href="{{ url()->previous()}}" class="text-sm bg-white px-4 py-2 rounded-xl text-black shrink-0">Back</a>
                    </div>

                </div>
            </div>

        </div>


        <!-- Improved Main Content -->
        <main class="nanum-gothic-regular block md:flex justify-around px-4 md:px-8 py-6 md:py-10 bg-gray-50 min-h-screen">
            <!-- Left Column - Price Summary -->
            <div class="md:w-[48%] w-full mb-8 md:mb-0">
                <!-- Price Summary Card -->
                <div
                    class="bg-white rounded-3xl shadow-lg p-6 border border-gray-200/50 space-y-5 transition-all duration-300 hover:shadow-xl">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Price Summary
                        </h2>
                        <div class="p-2 rounded-full bg-red-100 text-red-600">
                            <svg width="40" height="40" viewBox="0 0 81 91" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M0 30.25H10.0833V0H20.1667L37.4092 30.25H60.5V0H70.5833V30.25H80.6667V40.3333H70.5833V50.4167H80.6667V60.5H70.5833V90.75H60.5L43.2071 60.5H20.1667V90.75H10.0833V60.5H0V50.4167H10.0833V40.3333H0V30.25ZM20.1667 30.25H25.8638L20.1667 20.3179V30.25ZM20.1667 40.3333V50.4167H37.4092L31.6617 40.3333H20.1667ZM60.5 70.5833V60.5H54.7021L60.5 70.5833ZM43.1567 40.3333L48.9546 50.4167H60.5V40.3333H43.1567Z" fill="#97060B" />
                            </svg>

                        </div>
                    </div>

                    <div class="space-y-3">
                        @php
                        $travelerTypes = [];
                        foreach ($bookingDetails['travelType'] as $traveler) {
                        $type =
                        strtolower($traveler['travelerType']) === 'held_infant'
                        ? 'Infant'
                        : ucfirst(strtolower($traveler['travelerType']));
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
                        <div class="flex justify-between items-center text-gray-700 bg-gray-50 p-3 rounded-xl">
                            <span class="font-medium flex items-center">
                                @if ($type == 'Adult')
                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                @elseif($type == 'Child')
                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                @else
                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                @endif
                                {{ $type }} x {{ $data['count'] }}
                            </span>
                            <span class="font-bold text-gray-800">{{ $data['currency'] }}
                                {{ number_format($data['price'] + $price['commission'], 2) }}</span>
                        </div>
                        @endforeach

                        {{-- <div class="flex justify-between items-center text-gray-700 bg-gray-50 p-3 rounded-xl">
                        <span class="font-medium flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Currency
                        </span>
                        <span class="font-bold text-gray-800">{{ $price['currency'] ?? 'NGN' }}</span>
                    </div> --}}
                    {{-- <div class="flex justify-between items-center text-gray-700 bg-gray-50 p-3 rounded-xl">
                        <span class="font-medium flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                            </svg>
                            Base Fee
                        </span>
                        <span class="font-bold text-gray-800">{{ $price['currency'] ?? 'NGN' }}
                    {{ $price['base'] ?? 00.0 }} </span>
                </div>
                <div class="flex justify-between items-center text-gray-700 bg-gray-50 p-3 rounded-xl">
                    <span class="font-medium flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                        </svg>
                        Taxes & Fees
                    </span>
                    <span class="font-bold text-gray-800">{{ $price['currency'] ?? 'NGN' }}
                        {{ number_format($price['grandTotal'] - $price['base'] ?? 0, 2) }}</span>
                </div> --}}
                {{-- <div class="flex justify-between items-center text-gray-700 bg-gray-50 p-3 rounded-xl">
                        <span class="font-medium flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Service charge
                        </span>
                        <span class="font-bold text-gray-800">{{ $price['currency'] ?? 'NGN' }} {{ number_format($price['commission'] ?? 0, 2) }}</span>
            </div> --}}

    </div>

    <!-- Total Amount Section -->
    <div class="flex justify-between items-center pt-4 mt-2 border-t border-gray-200 text-xl font-bold">
        <span class="text-gray-800">Total Amount</span>
        <span class="text-red-600 text-2xl">{{ $price['currency'] ?? 'NGN' }}
            {{ number_format($price['grandTotal'] + $price['commission'] ?? 0) }}</span>
    </div>

    <div class="space-y-4 mt-4">
        <div class="bg-red-50 border border-red-200 p-3 rounded-xl">
            <p class="text-primary text-sm flex items-start gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                        stroke-width="2" stroke-linecap="round" />
                </svg>
                <span>Book now! Price can change if you come back later.</span>
            </p>
        </div>

        <p class="text-center text-sm text-gray-500 pt-2">
            By clicking continue, you agree to our
            <a href="#" class="text-red-600 hover:underline font-medium">Terms</a>
            and
            <a href="#" class="text-red-600 hover:underline font-medium">Privacy Policy</a>
        </p>
    </div>
</div>
</div>

<!-- Right Column - Payment Options -->
<div class="md:w-[48%] font-sans">
    <div class="lg:sticky lg:top-32 h-fit">
        <!-- Payment Method Tabs - Improved -->
        <div class="flex gap-4 mb-6">
            <button
                class="w-1/2 py-3 text-center shadow-lg rounded-2xl font-semibold text-lg transition duration-300 ease-in-out flex items-center justify-center {{ $selectedPayment === 'Alat Pay' ? 'bg-primary text-white' : 'bg-white text-red-600 hover:bg-red-50' }}"
                wire:click="updatePaymentMethod('Alat Pay')">
                <img src="https://alatpay.ng/static/media/logo.8bdf3d450f27d18190ab.png" alt="Alat Pay"
                    class="h-6 mr-2" />
                Alat Pay
            </button>
            <button
                class="w-1/2 py-3 text-center shadow-lg rounded-2xl font-semibold text-lg transition duration-300 ease-in-out flex items-center justify-center {{ $selectedPayment === 'Paystack' ? 'bg-primary text-white' : 'bg-white text-red-600 hover:bg-red-50' }}"
                wire:click="updatePaymentMethod('Paystack')">
                <img src="https://website-v3-assets.s3.amazonaws.com/assets/img/hero/Paystack-mark-white-twitter.png"
                    alt="Paystack" class="h-6 mr-2" />
                Paystack
            </button>
        </div>

        <!-- Dynamic Payment Section - Improved -->
        @if ($selectedPayment === 'Alat Pay')
        <div class="py-8 p-6 bg-white rounded-3xl shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center justify-center mb-6">
                <img src="https://alatpay.ng/static/media/logo.8bdf3d450f27d18190ab.png"
                    alt="Alat Pay logo" class="h-16">
            </div>

            <div class="bg-gray-50 p-4 rounded-xl mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600 font-medium">Payment Method</span>
                    <span class="text-gray-800 font-bold">Alat Pay</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">Secure Transaction</span>
                    <span class="text-green-600 font-bold flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Protected
                    </span>
                </div>
            </div>

            <div class="flex flex-col gap-4 md:w-[90%] mx-auto">
                <button
                    class="w-full font-bold text-xl px-6 py-5 rounded-xl text-white bg-primary hover:bg-red-700 transition duration-300 ease-in-out shadow-lg flex justify-center items-center"
                    onclick="showPayment()" {{ $isLoading ? 'disabled' : '' }}>
                    @if ($isLoading)
                    <div
                        class="w-6 h-6 border-4 border-t-4 border-white rounded-full spinner-border animate-spin mr-3">
                    </div>
                    Processing...
                    @else
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Pay {{ $price['currency'] ?? 'NGN' }}
                    {{ number_format($price['grandTotal'] + $price['commission'] ?? '') }} Now
                    @endif
                </button>
            </div>
        </div>
        @elseif($selectedPayment === 'Paystack')
        <div class="py-8 p-6 bg-white rounded-3xl shadow-lg transition-all duration-300 hover:shadow-xl">
            <div class="flex items-center justify-center mb-6">
                <img src="https://website-v3-assets.s3.amazonaws.com/assets/img/hero/Paystack-mark-white-twitter.png"
                    alt="Paystack logo" class="h-16">
            </div>

            <div class="bg-gray-50 p-4 rounded-xl mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600 font-medium">Payment Method</span>
                    <span class="text-gray-800 font-bold">Paystack</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 font-medium">Secure Transaction</span>
                    <span class="text-green-600 font-bold flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Protected
                    </span>
                </div>
            </div>

            <div class="flex flex-col gap-4 md:w-[90%] mx-auto">
                <button
                    class="w-full font-bold text-xl px-6 py-5 rounded-xl text-white bg-primary hover:bg-red-700 transition duration-300 ease-in-out shadow-lg flex justify-center items-center"
                    wire:click="processPayment" {{ $isLoading ? 'disabled' : '' }}>
                    @if ($isLoading)
                    <div
                        class="w-6 h-6 border-4 border-t-4 border-white rounded-full spinner-border animate-spin mr-3">
                    </div>
                    Processing...
                    @else
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Pay {{ $price['currency'] ?? 'NGN' }}
                    {{ number_format($price['grandTotal'] + $price['commission'] ?? '') }} Now
                    @endif
                </button>
            </div>
        </div>
        @endif

        <!-- Reserve and Pay Later Section - Improved -->
        <div x-data="{ showModal: false }" class="mt-6">
            <!-- Reserve and Pay Later Button -->
            <button @click="showModal = true"
                class="w-full py-4 px-6 bg-gray-800 text-white font-semibold rounded-xl hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-300 shadow-md flex items-center justify-center text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Reserve and Pay Later
            </button>

            <!-- Modal Overlay - Improved -->
            <div x-cloak x-show="showModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-center z-[9999999] justify-center bg-black bg-opacity-70"
                @click.away="showModal = false">
                <!-- Modal Content -->
                <div class="bg-white w-11/12 sm:w-3/4 md:w-1/2 p-8 rounded-2xl shadow-2xl relative transform transition-all"
                    @click.stop>
                    <div class="flex items-center mb-4">
                        <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-2xl font-bold text-gray-800">Reserve and Pay Later</h3>
                    </div>
                    <div class="h-1 w-20 bg-blue-500 mb-6 rounded-full"></div>
                    <p class="text-gray-700 mb-4 text-lg leading-relaxed">
                        By selecting this option, you agree to complete the payment within <span
                            class="font-bold text-blue-600">24 hours</span>.
                        Failure to pay within the specified timeframe may result in the cancellation of your
                        reservation.
                    </p>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r">
                        <p class="text-blue-700 flex items-start">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Please note that prices are subject to change after the reservation period.
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4">
                        <button @click="showModal = false"
                            class="py-3 px-6 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 transition duration-300">
                            Cancel
                        </button>
                        <button type="button" wire:click='offlineBooking' wire:target='offlineBooking'
                            class="py-3 px-6 bg-primary text-white font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-300 shadow-md">
                            <div wire:loading.remove wire:target="offlineBooking">
                                Confirm Reservation
                            </div>
                            <div wire:loading wire:target="offlineBooking"
                                class="w-10 h-10 border-4 border-t-primary border-gray-300 rounded-full animate-spin">
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<!-- AlatPay Script -->
<script src="https://web.alatpay.ng/js/alatpay.js"></script>
<script>
    console.log("Last Name:", "{{ $lastName }}");
    console.log("Currency:", "{{ $currency }}");
    console.log("Amount:", {
        {
            $totalamount
        }
    });
    console.log("Email:", "{{ $email }}");
    console.log("Phone:", "{{ $phone }}");

    // Initialize AlatPay with dynamic data
    let popup = Alatpay.setup({
        apiKey: "3e0c19b031fe4e5bb9b51a200cb86440", // Replace with your AlatPay API key
        businessId: "99fe948e-bafe-4bd3-92be-08dcabf3e16e", // Replace with your AlatPay Business ID
        email: "{{ $email }}", // Customer email
        phone: "{{ $phone }}", // Customer phone number
        firstName: "{{ $firstName }}", // Customer first name
        lastName: "{{ $lastName }}", // Customer last name
        metaData: null, // Optional metadata
        currency: "{{ $currency }}", // Currency (e.g., NGN)
        amount: {
            {
                $totalamount
            }
        }, // Payment amount
        onTransaction: function(response) {
            console.log("API response is ", response);
            // Handle transaction success or failure here
            window.location.href = '/payment/success'; // Redirect to success page
        },
        onClose: function() {
            console.log("Payment gateway is closed");
        }
    });

    // Function to show the AlatPay payment modal
    function showPayment() {
        popup.show();
    }
</script>
</div>

</div>