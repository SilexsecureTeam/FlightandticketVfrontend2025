<div id="hideforAdmid" class="w-full sticky top-0 px-5 md:px-8 lg:px-24 flex justify-between bg-black items-center z-[9999] font-semibold"
x-data="{ 
    isOpen: false,
     phoneNumber: '(+234) 802-200-8793',
    phoneNumber2: '(+234) 802-288-8721',
    copyToClipboard(number) {
        if (number === 2) {
            navigator.clipboard.writeText(this.phoneNumber2.replace(/[^0-9]/g, ''));
        } else {
            navigator.clipboard.writeText(this.phoneNumber.replace(/[^0-9]/g, ''));
        }
    }
}">
    <a href="/">
        <img src="/images/logo.png" alt="MyMore logo" class="size-16"/>
    </a>

    <!-- Mobile Hamburger -->
    <button class="md:hidden text-white" @click="isOpen = !isOpen">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
    </button>

    <!-- Mobile Menu -->
    <div class="fixed inset-0 bg-black z-[999999] md:hidden"
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.away="isOpen = false"
        x-cloak>
        <div class="flex flex-col h-full pt-20 px-6 space-y-6 text-white">
            <button class="absolute top-6 right-6 text-white" @click="isOpen = false">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            @forelse ($topPages as $page)
                <a href="{{ $page->url }}" class="text-lg" @click="isOpen = false">{{ $page->title }}</a>
            @empty
                <a href="/" class="text-lg" @click="isOpen = false">Flights</a>
            @endforelse
            <a href="{{ route('blog.index') }}" class="text-lg" @click="isOpen = false">Blog</a>
            
            <!-- Phone Number Section -->
            <div class="pt-4">
                <div class="flex items-start gap-2 mb-2">
                    <img src="/images/about/phone.svg" alt="Phone icon" class="w-5 h-5">
                    <span class="text-sm">Phone Number</span>
                </div>
                <!-- First phone number -->
                <div class="flex items-center justify-between gap-3 mb-2">
                    <p class="text-sm" x-text="phoneNumber"></p>
                    <button @click="navigator.clipboard.writeText(phoneNumber.replace(/[^0-9]/g, ''))">
                        <img src="/images/about/copy.svg" alt="Copy" class="w-5 h-5">
                    </button>
                </div>
                <!-- Second phone number -->
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm" x-text="phoneNumber2"></p>
                    <button @click="navigator.clipboard.writeText(phoneNumber2.replace(/[^0-9]/g, ''))">
                        <img src="/images/about/copy.svg" alt="Copy" class="w-5 h-5">
                    </button>
                </div>
            </div>

            <!-- Cart Section -->
            <a href="#" class="flex items-center gap-2 pt-4">
                <img src="/images/shopping-cart-01.svg" alt="Shopping cart" class="w-5 h-5">
                <span class="text-sm">My Cart</span>
            </a>

            <!-- Language and Currency Section -->
            <div class="border-t border-gray-600 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <img src="/images/Nigeria.svg" alt="Nigeria Flag" class="w-5 h-5" />
                        <span>NG</span>
                    </div>
                    <span>ENG</span>
                    <span>Login</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Navigation -->
    <nav class="hidden md:flex items-end gap-1.5 xl:gap-2.5">
        @forelse ($topPages as $page)
            <a href="{{ $page->url }}" class="navLink">{{ $page->title }}</a>
        @empty
            <a href="/" class="navLink">Flights</a>
        @endforelse
        <a href="{{ route('blog.index') }}" class="navLink">Blog</a>
    </nav>

    <!-- Medium to Large Screen Right Side Menu -->
    <div class="hidden md:flex lg:flex items-end gap-4 lg:gap-8 xl:gap-10 relative md:bottom-1 lg:bottom-2 xl:bottom-2.5">
        <!-- Medium Screen Compact Menu -->
        <div class="flex md:flex lg:hidden items-center gap-3 text-white">
            @php
                $bookingsPage = $topPages->firstWhere('slug', 'bookings');
            @endphp
            <a href="{{ $bookingsPage?->url ?? route('cms.page', 'bookings') }}" class="text-sm whitespace-nowrap">Bookings</a>
            
            <!-- Phone Icon -->
            <div x-data="{ showPopup: false, timeout: null }" 
                @mouseenter="clearTimeout(timeout); showPopup = true" 
                @mouseleave="timeout = setTimeout(() => showPopup = false, 500)" 
                class="relative">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="cursor-pointer">
                    <path d="M17 11.8045C17 11.4588 17 11.286 17.052 11.132C17.2032 10.6844 17.6018 10.5108 18.0011 10.3289C18.45 10.1244 18.6744 10.0222 18.8968 10.0042C19.1493 9.98378 19.4022 10.0382 19.618 10.1593C19.9041 10.3198 20.1036 10.6249 20.3079 10.873C21.2513 12.0188 21.7229 12.5918 21.8955 13.2236C22.0348 13.7334 22.0348 14.2666 21.8955 14.7764C21.6438 15.6979 20.8485 16.4704 20.2598 17.1854C19.9587 17.5511 19.8081 17.734 19.618 17.8407C19.4022 17.9618 19.1493 18.0162 18.8968 17.9958C18.6744 17.9778 18.45 17.8756 18.0011 17.6711C17.6018 17.4892 17.2032 17.3156 17.052 16.868C17 16.714 17 16.5412 17 16.1955V11.8045Z" stroke="white" strokeWidth="1.5" />
                    <path d="M7 11.8046C7 11.3694 6.98778 10.9782 6.63591 10.6722C6.50793 10.5609 6.33825 10.4836 5.99891 10.329C5.55001 10.1246 5.32556 10.0224 5.10316 10.0044C4.43591 9.95037 4.07692 10.4058 3.69213 10.8732C2.74875 12.019 2.27706 12.5919 2.10446 13.2237C1.96518 13.7336 1.96518 14.2668 2.10446 14.7766C2.3562 15.6981 3.15152 16.4705 3.74021 17.1856C4.11129 17.6363 4.46577 18.0475 5.10316 17.996C5.32556 17.978 5.55001 17.8757 5.99891 17.6713C6.33825 17.5167 6.50793 17.4394 6.63591 17.3281C6.98778 17.0221 7 16.631 7 16.1957V11.8046Z" stroke="white" strokeWidth="1.5" />
                    <path d="M20 10.5V9C20 5.13401 16.4183 2 12 2C7.58172 2 4 5.13401 4 9V10.5" stroke="white" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                    <path d="M20 17.5C20 22 16 22 12 22" stroke="white" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                </svg>
                
                <!-- Phone Popup -->
                <div x-show="showPopup" x-transition 
                    @mouseenter="clearTimeout(timeout); showPopup = true" 
                    @mouseleave="timeout = setTimeout(() => showPopup = false, 500)"
                    class="absolute top-10 transform left-1/2 -translate-x-1/2 bg-white text-black rounded-[5px] shadow-lg px-4 py-2 min-w-[220px]"
                    style="display: none;">
                    
                    <img src="/images/about/Polygon1.png" alt="" class="absolute -top-3 transform left-1/2 -translate-x-1/2">
            
                    <div class="flex items-center mb-2 gap-1">
                        <img src="/images/about/phone.svg" class="size-4" alt="Phone icon">
                        <span class="text-xs font-semibold text-[#00000080]">Phone Number</span>
                    </div>
            
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold whitespace-nowrap">(+234) 802-200-8793</p>
                            <button @click="navigator.clipboard.writeText('08022008793')"
                                class="px-1.5 py-0.5 text-xs bg-primary text-white rounded">
                                Copy
                            </button>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-semibold whitespace-nowrap">(+234) 802-288-8721</p>
                            <button @click="navigator.clipboard.writeText('08022888721')"
                                class="px-1.5 py-0.5 text-xs bg-primary text-white rounded">
                                Copy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cart Icon -->
            <a href="#" class="flex items-center">
                <img src="/images/shopping-cart-01.svg" alt="Shopping cart" class="w-5 h-5">
            </a>
            
            <!-- Login Link -->
            <a href="/login" class="text-sm whitespace-nowrap">Login</a>
        </div>
        
        <!-- Large Screen Full Menu -->
        <div class="hidden lg:flex items-end gap-8 xl:gap-10 text-white">
            <div class="flex gap-3 xl:gap-5 items-end">
                <a href="#" class="">Bookings</a>
                <div class="border-r border-dashed border-r-white w-1 h-11"></div>
                
                <div x-data="{ showPopup: false, timeout: null }" 
                    @mouseenter="clearTimeout(timeout); showPopup = true" 
                    @mouseleave="timeout = setTimeout(() => showPopup = false, 500)" 
                    class="relative flex items-center gap-5">
                
                    <div class="relative cursor-pointer" id="phone-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="svg-icon">
                            <path d="M17 11.8045C17 11.4588 17 11.286 17.052 11.132C17.2032 10.6844 17.6018 10.5108 18.0011 10.3289C18.45 10.1244 18.6744 10.0222 18.8968 10.0042C19.1493 9.98378 19.4022 10.0382 19.618 10.1593C19.9041 10.3198 20.1036 10.6249 20.3079 10.873C21.2513 12.0188 21.7229 12.5918 21.8955 13.2236C22.0348 13.7334 22.0348 14.2666 21.8955 14.7764C21.6438 15.6979 20.8485 16.4704 20.2598 17.1854C19.9587 17.5511 19.8081 17.734 19.618 17.8407C19.4022 17.9618 19.1493 18.0162 18.8968 17.9958C18.6744 17.9778 18.45 17.8756 18.0011 17.6711C17.6018 17.4892 17.2032 17.3156 17.052 16.868C17 16.714 17 16.5412 17 16.1955V11.8045Z" stroke="white" strokeWidth="1.5" />
                            <path d="M7 11.8046C7 11.3694 6.98778 10.9782 6.63591 10.6722C6.50793 10.5609 6.33825 10.4836 5.99891 10.329C5.55001 10.1246 5.32556 10.0224 5.10316 10.0044C4.43591 9.95037 4.07692 10.4058 3.69213 10.8732C2.74875 12.019 2.27706 12.5919 2.10446 13.2237C1.96518 13.7336 1.96518 14.2668 2.10446 14.7766C2.3562 15.6981 3.15152 16.4705 3.74021 17.1856C4.11129 17.6363 4.46577 18.0475 5.10316 17.996C5.32556 17.978 5.55001 17.8757 5.99891 17.6713C6.33825 17.5167 6.50793 17.4394 6.63591 17.3281C6.98778 17.0221 7 16.631 7 16.1957V11.8046Z" stroke="white" strokeWidth="1.5" />
                            <path d="M20 10.5V9C20 5.13401 16.4183 2 12 2C7.58172 2 4 5.13401 4 9V10.5" stroke="white" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                            <path d="M20 17.5C20 22 16 22 12 22" stroke="white" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                    </div>
                
                    <!-- Popup -->
                    <div x-show="showPopup" x-transition 
                        @mouseenter="clearTimeout(timeout); showPopup = true" 
                        @mouseleave="timeout = setTimeout(() => showPopup = false, 500)"
                        class="absolute top-12 transform left-1/2 -translate-x-1/2 bg-white text-black rounded-[5px] shadow-lg px-6 py-3 min-w-[250px]"
                        style="display: none;">
                        
                        <img src="/images/about/Polygon1.png" alt="" class="absolute -top-3 transform left-1/2 -translate-x-1/2">
                
                        <div class="flex items-center mb-2 gap-1">
                            <img src="/images/about/phone.svg" class="size-5" alt="Phone icon">
                            <span class="text-xs font-semibold text-[#00000080]">Phone Number</span>
                        </div>
                
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-semibold whitespace-nowrap">(+234) 802-200-8793</p>
                                <button @click="navigator.clipboard.writeText('08022008793')"
                                    class="px-2 py-1 text-sm bg-primary text-white rounded">
                                    Copy
                                </button>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-semibold whitespace-nowrap">(+234) 802-288-8721</p>
                                <button @click="navigator.clipboard.writeText('08022888712')"
                                    class="px-2 py-1 text-sm bg-primary text-white rounded">
                                    Copy
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-r border-dashed border-r-white w-1 h-11"></div>
                <a href="#" class="flex items-end gap-1.5 xl:gap-2">
                    <img src="/images/shopping-cart-01.svg" alt="Shopping cart">
                    <span class="text-sm">My Cart</span>
                </a>
            </div>

            <!-- Language and Currency Section -->
            <div class="flex items-end text-sm text-white">
                <div class="flex gap-1.5 items-center">
                    <img src="/images/Nigeria.svg" alt="Nigeria Flag" class="w-5 h-5" />
                    <span>NG</span>
                </div>
                <div class="bg-white w-[1px] h-6 ml-2 mr-3.5"></div>
                <div class="relative">
                    <span>ENG</span>
                </div>
                <div class="bg-white w-[1px] h-6 ml-2 mr-3.5"></div>
                <a href="/login">Login</a>
            </div>
        </div>
        
        <!-- Medium Screen Language/Country Selector -->
        <div class="md:flex lg:hidden items-center gap-2 text-white">
            <div class="flex items-center gap-1">
                <img src="/images/Nigeria.svg" alt="Nigeria Flag" class="w-4 h-4" />
                <span class="text-xs">NG</span>
            </div>
            <div class="bg-white w-[1px] h-4 mx-1"></div>
            <span class="text-xs">ENG</span>
        </div>
    </div>
</div>