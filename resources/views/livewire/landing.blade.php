<div class="relative min-h-screen w-full  ">

    <!-- HEADER SECTION -->
    <header class="horizontal-scroll-header text-white relative md:h-[80vh] w-full bg-cover bg-no-repeat"
        style="background-image: url('./images/hero-img.png');">
        <!-- Dark overlay for header background -->
        <div class="absolute inset-0 md:min-h-full bg-[#00000080]"></div>



        <div class="mx-auto max-w-[90%] pt-[2%] mb-[30px] flex flex-col gap-5 relative z-20 text-center">
            <h1 class="text-2xl md:text-4xl lg:text-5xl xl:text-[52px]" style="font-family: Caprasimo, serif;">Your Gateway to Seamless Travel.</h1>
            <p class="text-[#FFFFFFB2] text-sm md:text-lg lg:text-xl">Book flights, explore destinations, and plan your perfect trip—all in one trusted platform.</p>
        </div>

        <!-- Search Section Placeholder -->
        <div class="">
            @livewire('components.search')
        </div>
    </header>



    <!-- MAIN CONTENT -->
    <main class="">

        {{-- <!-- Explore Cheap Flights Section -->
        <section class="mx-auto py-10 lg:py-16 bg-[#9797970D] pl-5 md:pl-8 lg:pl-20 w-full">
            <div class="flex justify-between items-center mb-8 pr-5 md:pr-8 lg:pr-20">
                <h2 class="text-xl md:text-2xl lg:text-3xl font-semibold">Explore Cheap Flights in Nigeria</h2>
                <div class="flex gap-3 md:gap-5 items-center">
                    <img src="./images/arrow-side.svg" alt="left arrow" class="w-6 h-6 cursor-pointer hover:brightness-50 transition"
                        id="scroll-left">
                    <img src="./images/arrow-side.svg" alt="right arrow" class="w-6 h-6 rotate-180 cursor-pointer hover:brightness-50" id="scroll-right">
                </div>
            </div>
        
            <div class="flight-container hide-scrollbar grid grid-rows-2 gap-5 md:gap-7 grid-flow-col overflow-x-auto scroll-smooth">
                <!-- Flight cards dynamically injected here -->
                <!-- Dummy One -->
                <div class="bg-white rounded-[10px] shadow-md overflow-hidden min-w-[300px] max-w-[320px]">
                    <div class="border border-b-0 border-[#0000000F]">
                        <img src="./images/plane.png" alt="Flight Image" class="w-full h-36 object-cover">
                    </div>
                    <div class="p-3 border border-t-0 border-[#0000000F]">
                        <div class="flex gap-2 items-center mb-3">
                            <img src="./images/qatar.png" alt="Qatar airways logo" class="w-9 h-3.5">
                            <h3 class="font-semibold">Sky Vista Airlines</h3>
                        </div>
                        <div class="flex flex-col gap-2 text-[#00000080] font-medium">
                            <div class="flex items-center gap-2">
                                <img src="./images/calendar-01.svg" alt="calendar" class="w-4 h-4">
                                <p class="text-sm">Wed, 04 June, 2024</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <img src="./images/medal-07.svg" alt="calendar" class="w-4 h-4">
                                <p class="text-sm">First Class</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-semibold text-black">$300</span>
                                <span class="text-xs line-through">$700</span>
                            </div>
                            <p class="text-xs">Includes taxes & fees</p>
                        </div>
                    </div>
                </div>

                <!-- Dummy Two -->
                <div class="bg-white rounded-[10px] shadow-md overflow-hidden min-w-[300px] max-w-[320px]">
                    <div class="border border-b-0 border-[#0000000F]">
                        <img src="./images/plane.png" alt="Flight Image" class="w-full h-36 object-cover">
                    </div>
                    <div class="p-3 border border-t-0 border-[#0000000F]">
                        <div class="flex gap-2 items-center mb-3">
                            <img src="./images/qatar.png" alt="Qatar airways logo" class="w-9 h-3.5">
                            <h3 class="font-semibold">Sky Vista Airlines</h3>
                        </div>
                        <div class="flex flex-col gap-2 text-[#00000080] font-medium">
                            <div class="flex items-center gap-2">
                                <img src="./images/calendar-01.svg" alt="calendar" class="w-4 h-4">
                                <p class="text-sm">Wed, 04 June, 2024</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <img src="./images/medal-07.svg" alt="calendar" class="w-4 h-4">
                                <p class="text-sm">First Class</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-semibold text-black">$300</span>
                                <span class="text-xs line-through">$700</span>
                            </div>
                            <p class="text-xs">Includes taxes & fees</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Trending Destinations Section -->
        <section class="py-12 lg:py-16 bg-white px-5 md:pl-8 lg:pl-20 w-full">
            <h2 class="text-xl md:text-2xl lg:text-3xl font-semibold mb-5">Trending Destinations</h2>
            <div class="destination-container hide-scrollbar flex gap-5 md:gap-6 lg:gap-8 overflow-x-scroll">
                <!-- Destination card injected here too from the script -->
                <!-- Dummy One -->
                <div class="bg-white rounded-[15px] shadow-md overflow-hidden min-w-[300px] max-w-[350px]">
                    <img src="./images/destination.png" alt="Flight Image" class="w-full h-48 object-cover">
                    <div class="p-3 border border-t-0 border-[#0000000F]">                    
                        <h3 class="font-semibold text-sm mb-1">Canada</h3>
                        <p class="text-xs text-[#000000CC] mb-2">Toronto, Ontario</p>
                        <p class="text-[11px] text-[#00000080] mb-2">Jan12th - Jan20th, 2024</p>
                        <p class="font-bold mb-2" >$30.00</p>
                        <button class="px-2.5 py-[7px] font-bold text-[10px] text-white bg-[#000000CC]" >
                            Book Flight
                        </button>
                    </div>
                </div>        
                <!-- Dummy Two -->
                <div class="bg-white rounded-[15px] shadow-md overflow-hidden min-w-[300px] max-w-[350px]">
                    <img src="./images/destination.png" alt="Flight Image" class="w-full h-48 object-cover">
                    <div class="p-3 border border-t-0 border-[#0000000F]">
                        <h3 class="font-semibold text-sm mb-1">Canada</h3>
                        <p class="text-xs text-[#000000CC] mb-2">Toronto, Ontario</p>
                        <p class="text-[11px] text-[#00000080] mb-2">Jan12th - Jan20th, 2024</p>
                        <p class="font-bold mb-2">$30.00</p>
                        <button class="px-2.5 py-[7px] font-bold text-[10px] text-white bg-[#000000CC]">
                            Book Flight
                        </button>
                    </div>
                </div>
            </div>
        </section> --}}

        <!-- Brands Section -->
        <section class="py-12 lg:py-14 z-10 bg-white px-5 w-full">
            <div class="scroll-wrapper">
                <div class="scroll-container">
                    <div class="scroll-content">
                        <img src="./images/brands/amadeus.png" class="bg-blend-luminosity" alt="logo1">
                        <img src="./images/brands/paystack.png" class="bg-blend-luminosity" alt="logo2">
                        <img src="./images/brands/iata.png" class="bg-blend-luminosity" alt="logo3">
                        <img src="./images/brands/logo.svg" class="bg-blend-luminosity" alt="logo4">
                        <img src="./images/brands/tnanta.png" class="bg-blend-luminosity" alt="logo5">
                        <img src="https://alatpay.ng/static/media/logo.8bdf3d450f27d18190ab.png" class="bg-blend-luminosity" alt="logo5">
                        <!-- Duplicate for seamless effect -->
                        <img src="./images/brands/amadeus.png" class="bg-blend-luminosity" alt="logo1">
                        <img src="./images/brands/paystack.png" class="bg-blend-luminosity" alt="logo2">
                        <img src="./images/brands/iata.png" class="bg-blend-luminosity" alt="logo3">
                        <img src="./images/brands/logo.svg" class="bg-blend-luminosity" alt="logo4">
                        <img src="./images/brands/tnanta.png" class="bg-blend-luminosity" alt="logo5">
                        <img src="https://alatpay.ng/static/media/logo.8bdf3d450f27d18190ab.png" class="bg-blend-luminosity" alt="logo5">


                    </div>
                </div>
                <div class="w-10 md:w-24 lg:w-28 xl:w-32 gradient-fade-left rounded-r"></div>
                <div class="w-10 md:w-24 lg:w-28 xl:w-32 gradient-fade-right rounded-l"></div>
            </div>
        </section>

        <!-- Email Subscription Section -->
        <section class="py-10 lg:py-16 bg-[#9797970D] text-black   px-5 md:px-8 lg:px-24 w-full flex flex-col md:flex-row gap-10 items-center">
            <img src="./images/couple-pulling-luggage.png" alt="couple-pulling-luggage" class="w-auto h-auto md:flex-1 md:max-w-[50%]">
            <div class="mx-auto text-center md:max-w-[40%] flex-1 flex-shrink-0">
                <h3 class="text-2xl md:text-xl xl:text-3xl font-semibold mb-2 xl:mb-4">Don’t Miss Out a Great Deal</h3>
                <p class="text-[#00000080] mb-6 md:mb-5 xl:mb-11 md:text-sm xl:text-base">Stay one step ahead. Find the best value travel with the latest deals, tips,
                    and news.</p>
                <input type="email" name="email" id="email" placeholder="Enter your email address"
                    class="mb-3.5 xl:mb-5 py-3 xl:py-4 px-5 xl:px-8 bg-transparent border-[#00000033] border w-full" />
                <button class="bg-[#97060B] font-semibold text-white px-10 xl:px-[50px] py-2.5 xl:py-[13.5px]">
                    Subscribe
                </button>
            </div>
        </section>

    </main>

    <footer class="bg-[#262626] pt-10 pb-7 text-white">
        <section class="mb-10 px-5 md:px-8 lg:px-24 flex flex-col md:flex-row gap-7 justify-between">
            <img src="./images/logo.png" alt="" class="max-w-[156px] h-[74px]" />
            <div class="flex flex-wrap justify-between gap-10 ld:gap-20 text-xs">
                <div class="flex flex-col gap-3">
                    <h3 class="text-base">Company</h3>
                    <p>About Us</p>
                    <p>FAQ</p>
                    <p>Contact Us</p>
                </div>
                <div class="flex flex-col gap-3">
                    <h3 class="text-base">Legal</h3>
                    <p>Terms & Conditions</p>
                    <p>Privacy Policy</p>
                    <p>Cookies Policy</p>
                </div>
                <div class="flex flex-col gap-3">
                    <h3 class="text-base">Currency</h3>
                    <div class="relative flex items-center gap-2 border border-white px-2 py-1 w-fit min-w-[140px] pr-6">
                        <select class="bg-transparent text-white outline-none appearance-none w-full">
                            <option class="text-black" value="naira" selected>🇳🇬&emsp;Naira</option>
                            <option class="text-black" value="usd">🇺🇸&emsp;USD</option>
                            <option class="text-black" value="euro">🇪🇺&emsp;Euro</option>
                        </select>
                        <span class="absolute right-1 top-1/2 transform -translate-y-1/2">
                            <img src="./images/toggle.svg" alt="Dropdown" class="w-2 h-1.5" />
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <section class="">
            <div class="mb-5 pl-5 md:pl-8 lg:pl-20">
                <p class="mb-3">Connect with Us</p>
                <div class="flex gap-4">
                    <img src="./images/facebook.svg" alt="facebook icon">
                    <img src="./images/instagram.svg" alt="instagram icon">
                    <img src="./images/linkedin.svg" alt="linkedin icon">
                </div>
            </div>

            <div class="glow-line mb-10"></div>

            <div class="flex items-center justify-center text-center gap-1">
                <img src="./images/copyright.svg" alt="copyright icon" class="w-5 h-5">
                <p class="text-sm">2025 Mymore Inc.</p>
            </div>
        </section>
    </footer>



    <script>
        document.addEventListener("DOMContentLoaded", () => {
            initializeCustomerServicePopup();
            initializeFlightScrollButtons();
            initializeBackgroundTransition();
        });

        /* Initialize home backgroundimage transition */
        function initializeBackgroundTransition() {
            const images = [
                "./images/hero-img.png",
                "./images/hero-img2.png",
                "./images/hero-img3.png",
            ];

            let currentIndex = 0;

            function changeBackground() {

                const header = document.querySelector(".horizontal-scroll-header");
                currentIndex = (currentIndex + 1) % images.length;
                header.style.transition = "background-image 1s ease-in-out";
                header.style.backgroundImage = `url('${images[currentIndex]}')`;
            }

            setInterval(changeBackground, 15000);
        }

        /* Handle customer service popup */
        function initializeCustomerServicePopup() {
            const phoneIcon = document.getElementById("phone-icon");
            const popup = document.getElementById("popup");
            const copyBtn = document.getElementById("copy-btn");

            if (phoneIcon && popup && copyBtn) {
                phoneIcon.addEventListener("click", () => {
                    popup.classList.toggle("show");
                    phoneIcon.querySelector(".svg-icon").classList.toggle("active");
                });

                copyBtn.addEventListener("click", () => {
                    const phoneNumber = document.querySelector(".phone-number");
                    if (phoneNumber) {
                        navigator.clipboard.writeText(phoneNumber.innerText).then(() => {
                            alert("Phone number copied!");
                        });
                    }
                });
            }
        }

        /* Initialize scroll buttons for flight section */
        function initializeFlightScrollButtons() {
            const flightContainer = document.querySelector(".flight-container");
            const scrollLeft = document.getElementById("scroll-left");
            const scrollRight = document.getElementById("scroll-right");
            const scrollAmount = 350;

            if (flightContainer && scrollLeft && scrollRight) {
                scrollLeft.addEventListener("click", () => {
                    flightContainer.scrollBy({
                        left: -scrollAmount,
                        behavior: "smooth"
                    });
                });

                scrollRight.addEventListener("click", () => {
                    flightContainer.scrollBy({
                        left: scrollAmount,
                        behavior: "smooth"
                    });
                });
            }
        }
    </script>

</div>