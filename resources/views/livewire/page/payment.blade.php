        <!-- Recent Bookings Table -->
        <div x-data="{ isEditOpen: false }" class="bg-white rounded-lg shadow-lg p-6 mt-5 border border-gray-100">


            <nav x-cloak x-show="sidebarIsOpen" x-trap="sidebarIsOpen" class="fixed right-0 z-[99999] overflow-y-auto flex h-[100vh] md:w-[50%] w-full pb-20 shrink-0 flex-col bg-gradient-to-br from-white via-white to-neutral-50/90 backdrop-blur-xl p-8 shadow-2xl transition-transform duration-300" aria-label="edit listing" x-transition:enter="transition duration-300 ease-out" x-transition:enter-end="translate-x-0" x-transition:enter-start="translate-x-80" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="translate-x-80" x-transition:leave-start="translate-x-0">
                <!-- Elegant header -->
                <div class="relative mb-8">
                    <button class="absolute -left-4 top-1 p-2.5 hover:bg-black/5 rounded-full transition-colors duration-200" x-on:click="sidebarIsOpen = false">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.5" class="size-6 text-neutral-600 " aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <div class="text-center">
                        <h3 class="text-2xl font-semibold text-neutral-800 ">Payment Details</h3>
                        <p class="text-sm text-neutral-500  mt-1">details</p>
                    </div>
                </div>
            
                <!-- Luxurious form -->
                <div class="py-4">
                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                        <div class="px-4 py-6 sm:px-6">
                            <h2 class="text-lg font-semibold leading-6 text-gray-900">Update Payment Status</h2>
                            <p class="mt-1 text-sm text-gray-500">Update Flight  details here.</p>
                        </div>
                        <div class="border-t border-gray-100 px-4 py-6 sm:px-6">
                            <div  class="space-y-6">
                               <div>
        
                            </div> 
                            <div x-data="{
                                radioGroupOptions: [
                                    { title: 'Pending', description: 'Payment Not Confirmed Yet', value: 'PENDING' },
                                    { title: 'Success', description: 'Payment has been confirmed', value: 'SUCCESS' },
                                    { title: 'Failed', description: 'Payment Failed.', value: 'FAILED' },
                                    { title: 'Canceled', description: 'Payment has been Canceled.', value: 'CANCELED' }
                                ]
                            }" class="space-y-3">
                                <template x-for="(option, index) in radioGroupOptions" :key="index">
                                    <label @click="$wire.payment_status = option.value" class="flex items-start p-5 space-x-3 bg-white border rounded-md shadow-sm hover:bg-gray-50 border-neutral-200/70">
                                        <input 
                                            type="radio" 
                                            wire:model="payment_status" 
                                            :value="option.value" 
                                            :checked="$wire.payment_status === option.value" 
                                            class="text-gray-900 translate-y-px focus:ring-gray-700"
                                        />
                                        <span class="relative flex flex-col text-left space-y-1.5 leading-none">
                                            <span x-text="option.title" class="font-semibold"></span>
                                            <span x-text="option.description" class="text-sm opacity-50"></span>
                                        </span>
                                    </label>
                                </template>
                            </div>
                        
                                <div class="flex justify-end space-x-3">
                                    <button type="submit" wire:click='updateStatus' wire:target="updateStatus class="rounded-md w-full bg-primary hover:bg-black px-3 py-2 text-sm font-semibold text-white shadow-sm">
                                       <div wire:loading.remove wire:target="updateStatus">
                                        
                                           Update
                                    </div>
                                        <div wire:loading wire:target="updateStatus" class="w-10 h-10 mr-3 border-4 border-t-primary border-gray-300 rounded-full animate-spin"></div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>


            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
          Payment
                </h2>
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
                        placeholder="Search by airline code or name..."
                    >
                    @if($search)
                        <button 
                            wire:click="clearSearch" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
            

            <div class="overflow-x-auto text-black bg-white rounded-lg shadow-md">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-100 to-indigo-100">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">S/N</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">
                                <button wire:click="sortBy('order_id')" class="flex items-center">
                                    Order ID
                                    <!-- Add sort icon here if needed -->
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">
                                <button wire:click="sortBy('amount')" class="flex items-center">
                                    Amount
                                    <!-- Add sort icon here if needed -->
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">Payment Type</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">Payment Date</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">Payment Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $index => $payment)
                        <tr class="hover:bg-blue-50 border-b border-gray-100 transition-colors duration-300">
                            <td class="px-4 py-4 text-sm text-gray-800">
                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full">
                                    {{ ($payments->currentPage() - 1) * $payments->perPage() + $index + 1 }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm font-medium text-gray-900">
                                    {{$payment->flightOrder->order_id ?? 'N/A'}}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm font-medium text-gray-900">
                                    NGN{{ number_format($payment->amount ?? 0) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($payment->provider == 'Paystack') bg-green-100 text-green-800
                                    @elseif($payment->provider == 'Offline') bg-yellow-100 text-yellow-800
                                    @elseif($payment->provider == 'Alat Pay') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $payment->provider ?? 'Unknown' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-gray-500">
                                    {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y H:i') : 'N/A' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($payment->payment_status == 'SUCCESS') bg-green-100 text-green-800
                                    @elseif($payment->payment_status == 'PENDING') bg-yellow-100 text-yellow-800
                                    @elseif($payment->payment_status == 'FAILED') bg-red-100 text-red-800
                                    @elseif($payment->payment_status == 'CANCELED') bg-gray-100 text-gray-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ $payment->payment_status ?? 'Unknown' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex space-x-2">
                                    <button 
                                    wire:click="selectPayment({{ $payment->id }})"
                                        x-on:click="sidebarIsOpen = !sidebarIsOpen" 
                                        class="px-3 py-1 text-xs font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-sm transition-colors duration-300 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        Edit
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-lg font-medium">No Payments found</p>
                                    <p class="text-sm text-gray-500 mt-1">Try adjusting your search criteria</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-6 flex justify-center">
                    {{$payments->links()}}
                </div>
            </div>
          
          
        </div>