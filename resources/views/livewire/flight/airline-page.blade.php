<div x-data="{ sidebarIsOpen: false }" class="h-full">


    <nav style="z-index: 20000;" x-cloak x-show="sidebarIsOpen" x-trap="sidebarIsOpen" class="fixed right-0 z-[999999999999999999999999]  flex h-[100vh] md:w-[50%] w-full pb-20 shrink-0 flex-col bg-gradient-to-br from-white via-white to-neutral-50/90 backdrop-blur-xl p-8 shadow-2xl transition-transform duration-300" aria-label="edit listing" x-transition:enter="transition duration-300 ease-out" x-transition:enter-end="translate-x-0" x-transition:enter-start="translate-x-80" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="translate-x-80" x-transition:leave-start="translate-x-0">
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
        <div class="py-4 overflow-y-auto">
            <div class="lg:col-span-2">
                <div class="bg-white shadow-lg ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
                    <!-- Header Section -->
                    {{-- <div class="px-6 py-8 bg-primary">
                        <h2 class="text-xl font-bold leading-6 text-white">Airline Settings</h2>
                        <p class="mt-2 text-sm text-blue-100">Manage your airline details and branding</p>
                    </div> --}}
                    <!-- Form Body -->
                    <div class="px-6 py-8">
                        <form wire:submit.prevent="save" x-on:submit.prevent class="space-y-8">
                            <!-- Input Fields Section -->
                            <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-5">
                                <!-- Airline Code -->
                                <div class="relative md:col-span-1 ">
                                    <label for="airlineCode" class="block text-sm font-medium leading-6 text-gray-900">
                                        Airline Code
                                    </label>
                                    <div class="mt-2 relative rounded-md shadow-sm">

                                        <input type="text" id="airlineCode" wire:model="e_airline_code"
                                            placeholder="e.g. UAL, DL, BA"
                                            class="block w-full rounded-md border-0 py-2.5 px-2 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition duration-200" />
                                    </div>

                                    @error('e_airline_code')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Airline Name -->
                                <div class="relative md:col-span-3 ">
                                    <label for="airlineName" class="block text-sm font-medium leading-6 text-gray-900">
                                        Airline Name
                                    </label>
                                    <div class="mt-2 relative rounded-md shadow-sm">

                                        <input type="text" id="airlineName" wire:model="e_airline_name"
                                            placeholder="e.g. United Airlines, Delta, British Airways"
                                            class="block w-full rounded-md border-0 py-2.5 px-2 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition duration-200" />
                                    </div>
                                    @error('e_airline_name')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror


                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-5">
                                <div class="relative  md:col-span-1">
                                    <label for="airlineName" class="block text-sm font-medium leading-6 text-gray-900">
                                        Mark price
                                    </label>
                                    <div class="mt-2 relative rounded-md shadow-sm">

                                        <input type="text" id="airlineName" wire:model="e_markup"
                                            placeholder="00.00"
                                            class="block w-full rounded-md border-0 py-2.5 px-2  text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition duration-200" />
                                    </div>
                                    @error('markprice')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror

                                </div>
                                <div class="relative  md:col-span-2">
                                    <label for="airlineName" class="block text-sm font-medium leading-6 text-gray-900">
                                        Mark State
                                    </label>
                                    <div class="mt-2 relative rounded-md shadow-sm">
                                        <select id="airlineName" wire:model="e_markstatus"
                                            placeholder="00.00" class="block w-full rounded-md border-0 py-2.5 px-2  text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition duration-200">
                                            <option value="null">Select option</option>
                                            <option value="markup" {{$e_markstatus == "markup" ? 'selected' : ''}}>Markup</option>
                                            <option value="markdown" {{$e_markstatus == "markdown" ? 'selected' : ''}}>Markdown</option>
                                        </select>
                                    </div>
                                    @error('e_markstatus')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror

                                </div>
                            </div>

                            <!-- Logo Upload Section -->
                            <div class="mt-8">
                                <label class="block text-sm font-medium leading-6 text-gray-900 mb-3">
                                    Airline Logo
                                </label>

                                <!-- Preview Section -->
                                @if ($e_logo && is_object($e_logo))
                                <div class="mt-6 flex items-center space-x-4">
                                    <div class="bg-gray-100 p-4 rounded-lg">
                                        <img src="{{ $e_logo->temporaryUrl() }}" alt="Selected Logo" class="h-16 w-auto">
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">New Logo</p>
                                        <button type="button" wire:click="$set('e_logo', null)" class="mt-1 text-sm text-red-600 hover:text-red-700">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                                @elseif ($e_logo && is_string($e_logo))
                                <div class="mt-6 flex items-center space-x-4">
                                    <div class="bg-gray-100 p-4 rounded-lg">
                                        <img src="{{ Storage::url($e_logo) }}" alt="Current Logo" class="h-16 w-auto">
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Current Logo</p>
                                        <button type="button" wire:click="$set('e_logo', null)" class="mt-1 text-sm text-red-600 hover:text-red-700">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                                @endif
                                <div
                                    x-data="{
                                        isDropping: false,
                                        uploading: false,
                                        progress: 0,
                                        activeType: 'Airline Logo'
                                    }"
                                    @dragover.prevent="isDropping = true"
                                    @dragleave.prevent="isDropping = false"
                                    @drop.prevent="isDropping = false"
                                    class="border-2 border-dashed rounded-xl p-5 text-center transition-all duration-300"
                                    :class="isDropping ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-gray-50'">
                                    <input
                                        type="file"
                                        wire:model="e_logo"
                                        x-on:livewire-upload-start="uploading = true"
                                        x-on:livewire-upload-finish="uploading = false"
                                        x-on:livewire-upload-error="uploading = false"
                                        x-on:livewire-upload-progress="progress = $event.detail.progress"
                                        accept="image/*"
                                        class="hidden"
                                        id="logoInput" />
                                    <label for="logoInput" class="cursor-pointer" x-show="!$wire.logo">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-14 h-14 text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                            <p class="text-lg font-medium text-gray-700 mb-2">Drag & Drop or Click to Upload</p>
                                            <p class="text-sm text-gray-500">Supported formats: PNG, JPG, </p>
                                            <p class="text-sm text-gray-500 mt-1">Maximum file size: 2MB</p>
                                        </div>
                                    </label>
                                </div>

                                <!-- Upload progress indicator -->
                                {{-- <div x-show="uploading" class="mt-4">
                                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div 
                                            class="h-full bg-primary transition-all duration-300 ease-in-out"
                                            :style="{ width: `${progress}%` }"
                                        ></div>
                                    </div>
                                    <p class="text-center mt-2 text-sm text-gray-600" x-text="`Uploading... ${progress}%`"></p>
                                </div> --}}

                                <!-- Preview Section -->



                            </div>




                            <!-- Preview Section (if logo is already uploaded) -->
                            <div class="mt-6" wire:ignore>
                                <!-- You can add conditional rendering based on whether a logo is uploaded -->
                                <template x-if="false">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-gray-100 p-4 rounded-lg">
                                            <img src="" alt="Current logo" class="h-16 w-auto" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Current Logo</p>
                                            <button type="button" class="mt-1 text-sm text-red-600 hover:text-red-700">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Form Actions -->
                            <div class="mt-10 flex justify-end space-x-4 items-center">

                                <button wire:click="editAirline" class="relative inline-flex items-center rounded-md bg-primary hover:bg-primary px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
            <div class="mx-auto md:max-w-[90%] w-[90%] px-4 sm:px-6 lg:px-8">
                <div class="mx-auto ">
                    <!-- Breadcrumb -->
                    <x-breadcrumb title="home" subtitle="Settings" />




                    <!-- Recent Bookings Table -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mt-5 border border-gray-100">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Airline List
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
                                    placeholder="Search by airline code or name...">
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
                                            <button wire:click="sortBy('airline_code')" class="flex items-center">
                                                Airport Code

                                            </button>
                                        </th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">
                                            <button wire:click="sortBy('airline_name')" class="flex items-center">
                                                Airport Name

                                            </button>
                                        </th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">Visibility</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">Mark Price</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">Mark State</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">Logo</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-blue-200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($airlines as $index => $airline)
                                    <tr class="hover:bg-blue-50 border-b border-gray-100 transition-colors duration-300">
                                        <td class="px-4 py-4 text-sm text-gray-800">
                                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full">
                                                {{ ($airlines->currentPage() - 1) * $airlines->perPage() + $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="px-3 py-1 text-base md:text-xl font-medium rounded-full bg-black text-white shadow-sm">
                                                {{$airline->airline_code}}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class=" py-1 text-base md:text-xs rounded-full  text-black font-bold ">
                                                {{$airline->airline_name}}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox"
                                                        class="sr-only peer"
                                                        wire:click="toggleVisibility({{ $airline->id }})"
                                                        {{ $airline->visible ? 'checked' : '' }}>
                                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                                    <span class="ml-2 text-sm font-medium text-gray-700">
                                                        {{ $airline->visible ? 'Visible' : 'Hidden' }}
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="px-3 py-1 text-base md:text-sm font-medium rounded-full shadow-sm">
                                                ₦{{ number_format($airline->markup ?? 0, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="px-3 py-1 text-base md:text-sm font-medium rounded-full shadow-sm">
                                                {{ $airline->markstatus ?? 'null' }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-4">
                                            <div class="relative w-18 h-18 flex items-center justify-center rounded-full overflow-hidden bg-gray-100 border border-gray-200 shadow-sm group">
                                                <img
                                                    src="{{ Storage::url($airline->logo) }}"
                                                    alt="{{ $airline->airline_name }} Logo"
                                                    class="w-16 h-16 object-contain rounded-full"
                                                    onerror="this.onerror=null; this.src='/images/default-airline-logo.png'; this.classList.add('p-2');">
                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300"></div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex space-x-2">
                                                <button wire:click="selectAirline({{ $airline->id }})" x-on:click="sidebarIsOpen = !sidebarIsOpen"
                                                    class="px-3 py-1 text-xs font-medium bg-primary hover:bg-primary text-white rounded-md shadow-sm transition-colors duration-300 flex items-center">
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
                                                <p class="text-lg font-medium">No airlines found</p>
                                                <p class="text-sm text-gray-500 mt-1">Try adjusting your search criteria</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div class="mt-6 flex justify-center">
                                {{$airlines->links()}}
                            </div>
                        </div>
                    </div>


                    <!-- Settings Section -->
                    <div class="mt-8 grid grid-cols-1 gap-6 ">
                        <!-- Personal Information -->
                        <div class="lg:col-span-2">
                            <div class="bg-white shadow-lg ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
                                <!-- Header Section -->
                                <div class="px-6 py-8 bg-primary">
                                    <h2 class="text-xl font-bold leading-6 text-white">Airline Settings</h2>
                                    <p class="mt-2 text-sm text-blue-100">Manage your airline details and branding</p>
                                </div>
                                <!-- Form Body -->
                                <div class="px-6 py-8">
                                    <form wire:submit.prevent="save" x-on:submit.prevent class="space-y-8">
                                        <!-- Input Fields Section -->
                                        <div class="grid grid-cols-1 gap-x-8 gap-y-6 sm:grid-cols-2">
                                            <!-- Airline Code -->
                                            <div class="relative">
                                                <label for="airlineCode" class="block text-sm font-medium leading-6 text-gray-900">
                                                    Airline Code
                                                </label>
                                                <div class="mt-2 relative rounded-md shadow-sm">
                                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                                        </svg>
                                                    </div>
                                                    <input type="text" id="airlineCode" wire:model="airline_code"
                                                        placeholder="e.g. UAL, DL, BA"
                                                        class="block w-full rounded-md border-0 py-2.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition duration-200" />
                                                </div>

                                                @error('airline_code')
                                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                                @enderror
                                                <p class="mt-1 text-xs text-gray-500">Enter the IATA or ICAO code for your airline</p>
                                            </div>

                                            <!-- Airline Name -->
                                            <div class="relative">
                                                <label for="airlineName" class="block text-sm font-medium leading-6 text-gray-900">
                                                    Airline Name
                                                </label>
                                                <div class="mt-2 relative rounded-md shadow-sm">
                                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                        </svg>
                                                    </div>
                                                    <input type="text" id="airlineName" wire:model="airline_name"
                                                        placeholder="e.g. United Airlines, Delta, British Airways"
                                                        class="block w-full rounded-md border-0 py-2.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition duration-200" />
                                                </div>
                                                @error('airline_code')
                                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                                @enderror

                                                <p class="mt-1 text-xs text-gray-500">Enter the full name of your airline</p>
                                            </div>
                                        </div>

                                        <!-- Logo Upload Section -->
                                        <div class="mt-8">
                                            <label class="block text-sm font-medium leading-6 text-gray-900 mb-3">
                                                Airline Logo
                                            </label>
                                            <div
                                                x-data="{
                                                    isDropping: false,
                                                    uploading: false,
                                                    progress: 0,
                                                    activeType: 'Airline Logo'
                                                }"
                                                @dragover.prevent="isDropping = true"
                                                @dragleave.prevent="isDropping = false"
                                                @drop.prevent="isDropping = false"
                                                class="border-2 border-dashed rounded-xl p-10 text-center transition-all duration-300"
                                                :class="isDropping ? 'border-blue-500 bg-blue-50' : 'border-gray-300 bg-gray-50'">
                                                <input
                                                    type="file"
                                                    wire:model="logo"
                                                    x-on:livewire-upload-start="uploading = true"
                                                    x-on:livewire-upload-finish="uploading = false"
                                                    x-on:livewire-upload-error="uploading = false"
                                                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                                                    accept="image/*"
                                                    class="hidden"
                                                    id="logoInput" />
                                                <label for="logoInput" class="cursor-pointer" x-show="!$wire.logo">
                                                    <div class="flex flex-col items-center">
                                                        <svg class="w-16 h-16 text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                        </svg>
                                                        <p class="text-lg font-medium text-gray-700 mb-2">Drag & Drop or Click to Upload</p>
                                                        <p class="text-sm text-gray-500">Supported formats: PNG, JPG, SVG</p>
                                                        <p class="text-sm text-gray-500 mt-1">Maximum file size: 10MB</p>
                                                    </div>
                                                </label>
                                            </div>

                                            <!-- Upload progress indicator -->
                                            <div x-show="uploading" class="mt-4">
                                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                                    <div
                                                        class="h-full bg-gradient-to-r from-blue-400 to-primary transition-all duration-300 ease-in-out"
                                                        :style="{ width: `${progress}%` }"></div>
                                                </div>
                                                <p class="text-center mt-2 text-sm text-gray-600" x-text="`Uploading... ${progress}%`"></p>
                                            </div>

                                            <!-- Preview Section -->
                                            @if ($logo)
                                            <div class="mt-6 flex items-center space-x-4">
                                                <div class="bg-gray-100 p-4 rounded-lg">
                                                    <img src="{{ $logo->temporaryUrl() }}" alt="Selected Logo" class="h-16 w-auto">
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">Selected Logo</p>
                                                    <button type="button" wire:click="$set('logo', null)" class="mt-1 text-sm text-red-600 hover:text-red-700">
                                                        Remove
                                                    </button>
                                                </div>
                                            </div>
                                            @endif
                                        </div>


                                        <!-- Preview Section (if logo is already uploaded) -->
                                        <div class="mt-6" wire:ignore>
                                            <!-- You can add conditional rendering based on whether a logo is uploaded -->
                                            <template x-if="false">
                                                <div class="flex items-center space-x-4">
                                                    <div class="bg-gray-100 p-4 rounded-lg">
                                                        <img src="" alt="Current logo" class="h-16 w-auto" />
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">Current Logo</p>
                                                        <button type="button" class="mt-1 text-sm text-red-600 hover:text-red-700">
                                                            Remove
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Form Actions -->
                                        <div class="mt-10 flex justify-end space-x-4 items-center">

                                            <button type="submit" class="relative inline-flex items-center rounded-md bg-primary hover:bg-primary px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <span wire:loading.remove wire:target="save">
                                                    Save Changes
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
                </div>
            </div>
        </main>
    </div>
</div>