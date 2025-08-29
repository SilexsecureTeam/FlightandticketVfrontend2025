<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Success Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Top Success Banner -->
            <div class="bg-primary py-6 px-8 relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\" 20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23fff\" fill-opacity=\"0.1\" fill-rule=\"evenodd\"%3E%3Ccircle cx=\"3\" cy=\"3\" r=\"3\"/%3E%3Ccircle cx=\"13\" cy=\"13\" r=\"3\"/%3E%3C/g%3E%3C/svg%3E')] opacity-10"></div>
                <div class="relative flex items-center justify-center">
                    <div class="bg-white rounded-full p-2 mb-4">
                        <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-center text-3xl font-bold text-white font-sm14 line-height-sm">Offline Reservation Confirmed!</h1>
                <p class="text-center text-green-100 mt-2">Your Offline Reservation has been Confirmed</p>
            </div>

            <!-- Payment Details -->
            <div class="px-8 py-6 space-y-6 psm1">
                <!-- Transaction ID -->
                <div class="flex md:flex-row flex-col justify-between items-center p-4 bg-gray-50 rounded-xl">
                    <div class="text-gray-600">Order ID</div>
                    <div class="font-medium text-gray-900 font-sm10">{{ $payment->flightOrder->order_id ?? 'null'}}</div>
                </div>

                <!-- Amount Paid -->
                <div class="text-center line-height-sm5 moveUpMbhhh">
                    <span class="text-4xl font-bold text-gray-900 font-sm16">₦{{ number_format($payment->amount + $payment->commission ?? 0) }}</span>
                    <p class="text-gray-500 mt-1 font-sm10">Total Amount Paid</p>
                </div>

                <!-- Payment Instructions Alert -->
                <div class="bg-yellow-50 rounded-xl p-5 psm1 border border-yellow-200">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mobiledisplayNone">
                            <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3 mobilemarginLeft0 w-full">
                            <h3 class="text-sm font-medium text-yellow-800 mobileCenter">Important Payment Information</h3>
                            <div class="mt-2 text-sm text-yellow-700 font-sm10">
                                <p>Please make payment to one of the accounts below:</p>

                                <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- First Bank Account -->
                                    <div class="bg-white p-3 rounded-lg border border-yellow-100 shadow-sm">
                                        <p class="font-bold text-yellow-800">Primary Account</p>
                                        <p class="font-semibold mt-1">Wema Bank</p>
                                        <div class="flex items-center justify-between mt-1">
                                            <p>Account Number:</p>
                                            <p class="font-bold">0125048653</p>
                                        </div>
                                        <p class="mt-1">MYMORE TRAVELS SERVICES LTD</p>
                                    </div>

                                    <!-- Second Bank Account -->
                                    <div class="bg-white p-3 rounded-lg border border-yellow-100 shadow-sm">
                                        <p class="font-bold text-yellow-800">Alternative Account</p>
                                        <p class="font-semibold mt-1">Alternative Bank</p>
                                        <div class="flex items-center justify-between mt-1">
                                            <p>Account Number:</p>
                                            <p class="font-bold">0505820902</p>
                                        </div>
                                        <p class="mt-1">Mymore Travels Limited</p>
                                    </div>
                                </div>

                                <div class="mt-4 p-3 bg-red-50 rounded-lg border border-red-100">
                                    <p class="font-semibold text-red-600">⚠️ If payment is not confirmed within 48 hours, your flight reservation will be cancelled.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details Grid - IMPROVED -->
                <div class="rounded-xl overflow-hidden border border-gray-200">
                    <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                        <h3 class="text-base font-medium text-gray-700">Payment Details</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                        <!-- Payment Date -->
                        <div class="p-6 flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Payment Date</p>
                                <p class="mt-1 text-base font-medium text-gray-900 font-sm12">{{ now()->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        <!-- Payment Type -->
                        <div class="p-6 flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Payment Type</p>
                                <p class="mt-1 text-base font-medium text-gray-900 font-sm12">{{ $payment->provider ?? 'Offline Payment' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                        <!-- Email -->
                        <div class="p-6 flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Email</p>
                                <p class="mt-1 text-base font-medium text-gray-900 break-all font-sm10">{{ $payment->email ?? 'Email' }}</p>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="p-6 flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="mr-1.5 h-2 w-2 text-yellow-600" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Pending
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Notice -->
                <div class="bg-blue-50 rounded-xl p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <div class="flex-1 text-sm text-blue-700">
                        We've sent a confirmation email to your registered email address with all the transaction details.
                    </div>
                </div>

                <!-- Action Buttons -->
                {{-- <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/" 
                       class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Go to Dashboard
                    </a>
                </div> --}}
            </div>

            <!-- Support Section -->
            <div class="bg-gray-50 px-8 py-6">
                <div class="flex items-center justify-center gap-2 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Need help? <span class="text-blue-600 hover:text-blue-700 font-medium font-sm10">support@mymoretravels.com</span></span>
                </div>
            </div>
        </div>

        <!-- Download Button -->
        <div class="text-center mt-8">
            <button wire:click="download" wire:target="download" class="inline-flex items-center px-5 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>

                <div wire:loading.remove wire:target="download">
                    Download PDF
                </div>

                <div wire:loading wire:target="download"
                    class="w-10 h-10 border-4 border-t-black border-gray-300 rounded-full animate-spin"></div>
            </button>
        </div>
    </div>
</div>