<div x-data="{ sidebarIsOpen: false }" class="h-full">


    <nav x-cloak x-show="sidebarIsOpen" x-trap="sidebarIsOpen" class="fixed right-0 z-[99999] overflow-y-auto flex h-[100vh] md:w-[50%] w-full pb-20 shrink-0 flex-col bg-gradient-to-br from-white via-white to-neutral-50/90 backdrop-blur-xl p-8 shadow-2xl transition-transform duration-300" aria-label="edit listing" x-transition:enter="transition duration-300 ease-out" x-transition:enter-end="translate-x-0" x-transition:enter-start="translate-x-80" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="translate-x-80" x-transition:leave-start="translate-x-0">
        <!-- Elegant header -->
        <div class="relative mb-8">
            <button class="absolute -left-4 top-1 p-2.5 hover:bg-black/5 rounded-full transition-colors duration-200" x-on:click="sidebarIsOpen = false">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="1.5" class="size-6 text-neutral-600 " aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="text-center">
                <h3 class="text-2xl font-semibold text-neutral-800 ">Edit Details</h3>
                <p class="text-sm text-neutral-500  mt-1">details</p>
            </div>
        </div>

        <!-- Luxurious form -->
        <div class="py-4">
            <div class="lg:col-span-2">
                <div class="bg-white shadow-lg ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
                    <!-- Header Section -->
                    <div class="px-6 py-8 bg-primary">
                        <h2 class="text-xl font-bold leading-6 text-white">Airport Settings</h2>
                        <p class="mt-2 text-sm text-blue-100">Manage your Airport details and branding</p>
                    </div>
                    <!-- Form Body -->
                    <div class="px-6 py-8">
                        <form wire:submit.prevent="save" x-on:submit.prevent class="space-y-8">
                            <!-- Input Fields Section -->
                            <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                <!-- Airline Code -->
                                <div class="relative">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">
                                        Iata Code
                                    </label>
                                    <div class="mt-2 relative rounded-md shadow-sm">

                                        <input type="text" id="airport_code" wire:model="airport_code"

                                            class="block w-full rounded-md border-0 p-2.5  text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition duration-200" />
                                    </div>

                                    @error('airport_code')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">Enter the IATA Airline</p>
                                </div>

                                <!-- Airline Name -->
                                <div class="relative">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">
                                        Airport Name
                                    </label>
                                    <div class="mt-2 relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        </div>
                                        <input type="text" wire:model="airport"
                                            placeholder="e.g. Abu Simbel Airport"
                                            class="block w-full rounded-md border-0 p-2.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition duration-200" />
                                    </div>
                                    @error('airport')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror

                                    <p class="mt-1 text-xs text-gray-500">Enter the full name of your airline</p>
                                </div>

                                <div class="relative">
                                    <label class="block text-sm font-medium leading-6 text-gray-900">
                                        City
                                    </label>
                                    <div class="mt-2 relative rounded-md shadow-sm">

                                        <input type="text" id="city" wire:model="city"
                                            placeholder=" Abuja, London, Paris"
                                            class="block w-full rounded-md border-0 p-2.5  text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition duration-200" />
                                    </div>
                                    @error('city_country')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror


                                </div>
                                <div class="relative">
                                    <label for="airlineName" class="block text-sm font-medium leading-6 text-gray-900">
                                        Country
                                    </label>
                                    <div class="mt-2 relative rounded-md shadow-sm">

                                        <input type="text" id="country" wire:model="country"
                                            placeholder="e.g. Nigeria, United State"
                                            class="block w-full rounded-md border-0 p-2.5  text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition duration-200" />
                                    </div>
                                    @error('country')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror


                                </div>


                            </div>

                            <div class="relative">
                                <label for="airlineName" class="block text-sm font-medium leading-6 text-gray-900">
                                    Location Served
                                </label>
                                <div class="mt-2 relative rounded-md shadow-sm">

                                    <input type="text" id="country" wire:model="city_country"
                                        placeholder="e.g. United Airlines, Delta, British Airways"
                                        class="block w-full rounded-md border-0 py-2.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition duration-200" />
                                </div>
                                @error('country')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror


                            </div>

                            <!-- Form Actions -->
                            <div class="mt-10 flex justify-end space-x-4 items-center">

                                <button type="submit" class="relative inline-flex items-center rounded-md bg-primary  px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <span wire:loading.remove wire:target="save">
                                        Update
                                    </span>
                                    <span wire:loading wire:target="save" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>

                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="min-h-full bg-gray-50">
        <main class="py-10">
            <div class="mx-auto md:max-w-[95%] w-[90%] px-4 sm:px-6 lg:px-3">
                <div class="mx-auto ">
                    <!-- Breadcrumb -->
                    <x-breadcrumb title="home" subtitle="Settings" />



                    <!-- Recent Bookings Table -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mt-5 border border-gray-100">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                            <!-- Header Section -->
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 border-b border-gray-200 dark:border-gray-700">
                                <h2 class="text-2xl font-bold text-primary flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600 dark:text-blue-500text-primarye-width=" 2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Airport List
                                </h2>
                            </div>

                            <!-- Content Section -->
                            <div class="p-6">
                                <div class="grid gap-6 md:grid-cols-2">
                                    <!-- Import Form -->
                                    <div>
                                        <form wire:submit.prevent="import" class="space-y-4">
                                            <div class="space-y-2">
                                                <label for="file" class="text-sm font-medium text-gray-700 dark:text-gray-300">Import Airports from CSV</label>

                                                <div class="flex items-center gap-2">
                                                    <div class="relative flex-1">
                                                        <input
                                                            type="file"
                                                            wire:model="file"
                                                            class="absolute inset-0 opacity-0 w-full h-full cursor-pointer z-10" />
                                                        <div class="flex items-center gap-2 p-3 border border-dashed rounded-md bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 dark:border-gray-600 transition-colors">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            <span class="text-sm text-gray-500 dark:text-gray-400 truncate" x-text="fileName"></span>
                                                        </div>
                                                    </div>

                                                    <button
                                                        type="submit"
                                                        class="px-4 py-2 bg-primary text-white font-medium rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 flex items-center gap-2"
                                                        wire:loading.class="opacity-75 cursor-wait"
                                                        wire:loading.attr="disabled"
                                                        wire:target="import">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" wire:loading.class="animate-spin" wire:target="import">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                        </svg>
                                                        <span wire:loading.remove wire:target="import">Import</span>
                                                        <span wire:loading wire:target="import">Importing... Please wait.</span>
                                                    </button>
                                                </div>

                                                @error('file')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </form>
                                    </div>


                                </div>
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
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gradient-to-r from-blue-50 to-indigo-50">
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">S/N</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">

                                            Iata Code


                                        </th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">

                                            Airport Name

                                        </th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">Location Served</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">city</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">Country</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">Visibility</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($airports as $index => $airport)
                                    <tr class="hover:bg-blue-50 border-b border-gray-100 text-black transition-colors duration-300">
                                        <td class="px-4 py-4 text-sm text-gray-800">
                                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full">
                                                {{ ($airports->currentPage() - 1) * $airports->perPage() + $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="px-3 py-1 text-xs font-bold  rounded-full   shadow-sm">
                                                {{$airport->airport_code}}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class=" py-1 text-xs text-center font-bold  rounded-full  shadow-sm">
                                                {{$airport->airport}}
                                            </span>
                                        </td>

                                        <td class="px-4 py-4 text-xs font-bold">
                                            {{$airport->city_country}}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="px-3 py-1 text-sm font-bold  rounded-full  shadow-sm">
                                                {{$airport->city}}
                                            </span>
                                        </td>

                                        <td class="px-4 py-4">
                                            <span class="px-3 py-1 text-sm font-bold  rounded-full  shadow-sm">
                                                {{$airport->country}}
                                            </span>
                                        </td>

                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <button type="button"
                                                    wire:click="toggleVisibility({{ $airport->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="toggleVisibility({{ $airport->id }})"
                                                    role="switch"
                                                    aria-checked="{{ $airport->visible ? 'true' : 'false' }}"
                                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300 {{ $airport->visible ? 'bg-primary' : 'bg-gray-200' }}">
                                                    <span class="inline-block h-5 w-5 transform rounded-full border border-gray-300 bg-white transition duration-200 {{ $airport->visible ? 'translate-x-5' : 'translate-x-0.5' }}"></span>
                                                </button>
                                                <span class="ml-2 text-sm font-medium text-gray-700">
                                                    {{ $airport->visible ? 'Visible' : 'Hidden' }}
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-4 py-4">
                                            <div class="flex space-x-2">
                                                <button wire:click="selectAirport({{ $airport->id }})" x-on:click="sidebarIsOpen = !sidebarIsOpen"
                                                    class="px-3 py-1 text-xs font-medium bg-primary text-white rounded-md shadow-sm transition-colors duration-300 flex items-center">
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
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="text-lg font-medium">No Airport found</p>
                                                <p class="text-sm text-gray-500 mt-1">Try adjusting your search criteria</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="mt-6 text-xs flex justify-center">
                                <div class="text-[10px] space-x-1">
                                    {{ $airports->links() }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>