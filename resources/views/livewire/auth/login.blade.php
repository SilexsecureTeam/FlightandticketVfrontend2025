<div
    class="flex items-center justify-center h-screen mx-auto p-3 text-black  bg-white shadow-sm ">

 
    <div class="p-4 sm:p-7 rounded-xl border border-gray-200  md:w-[30%] w-full max-w-full ">

        <div class="text-center p-3">
            <h1 class="block text-2xl font-medium text-black ">🎉 Welcome back !</h1>

        </div>

        @if (session()->has('error'))
        <div class="text-red-500 text-center">
            {{ session('error') }}
        </div>
        @endif

        <div class="mt-5">

            <!-- Form -->
            @if(!$showOtp)
            <form wire:submit.prevent='save'>
                <div class="grid gap-y-4 mb-4">
                    <!-- Form Group -->
                    <div>
                        <label for="email" class="block text-sm mb-2 ">Email address</label>
                        <div class="relative">
                            <input type="email" autocomplete="false" id="email" wire:model="email"
                                class="py-3 px-4 block w-full {{ $errors->has('email') ? 'border-red-500' : 'border-primary-100' }} border  rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  "
                                aria-describedby="email-error">
                            @error('email')
                            <div class="   absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                <svg class="h-5 w-5 text-red-500" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16" aria-hidden="true">
                                    <path
                                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                </svg>
                            </div>
                            @enderror

                        </div>
                        @error('email')
                        <p class="block text-xs text-red-600 mt-2" id="email-error">{{$message}}</p>
                        @enderror
                    </div>
                    <!-- End Form Group -->

                    <!-- Form Group -->
                    <div>
                        <label for="password" class="block text-sm mb-2 ">Password</label>
                        <div class="relative">
                            <input type="password" id="password" wire:model="password"
                                class="py-3 px-4 block w-full {{ $errors->has('password') ? 'border-red-500' : 'border-primary-100' }} border rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  "
                                aria-describedby="password-error">
                            @error('password')
                            <div class="   absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                <svg class="h-5 w-5 text-red-500" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16" aria-hidden="true">
                                    <path
                                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                </svg>
                            </div>
                            @enderror

                        </div>
                        @error('password')
                        <p class=" block text-xs text-red-600 mt-2" id="password-error">{{$message}}</p>
                        @enderror

                    </div>
                    <!-- End Form Group -->

                    <button type="submit" wire:loading.attr="disabled" class="w-full bg-primary py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm
                        font-medium rounded-lg border border-transparent bg-primary-100 text-white
 hover:text-black  focus:outline-none ">
                        <p wire:loading.remove wire:target="save">Login</p>

                        <div wire:loading wire:target="save" class="flex justify-center items-center">
                            <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>
                        </div>
                    </button>
                </div>
            </form>
            @else
            <form wire:submit.prevent='verifyOtp'>
                <div class="grid gap-y-4 mb-4">
                    <div class="text-center mb-4">
                        <p class="text-sm text-gray-600">Enter the 6-digit code sent to your email.</p>
                    </div>
                    <!-- Form Group -->
                    <div>
                        <label for="otp" class="block text-sm mb-2 ">OTP Code</label>
                        <div class="relative">
                            <input type="text" id="otp" wire:model="otp" maxlength="6"
                                class="py-3 px-4 block w-full {{ $errors->has('otp') ? 'border-red-500' : 'border-primary-100' }} border  rounded-lg text-sm text-center tracking-widest font-bold focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  "
                                placeholder="******">
                            @error('otp')
                            <div class="absolute inset-y-0 end-0 flex items-center pointer-events-none pe-3">
                                <svg class="h-5 w-5 text-red-500" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16" aria-hidden="true">
                                    <path
                                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                </svg>
                            </div>
                            @enderror
                        </div>
                        @error('otp')
                        <p class="block text-xs text-red-600 mt-2" id="otp-error">{{$message}}</p>
                        @enderror
                    </div>
                    <!-- End Form Group -->

                    <button type="submit" wire:loading.attr="disabled" class="w-full bg-primary py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm
                        font-medium rounded-lg border border-transparent bg-primary-100 text-white
 hover:text-black  focus:outline-none ">
                        <p wire:loading.remove wire:target="verifyOtp">Verify OTP</p>

                        <div wire:loading wire:target="verifyOtp" class="flex justify-center items-center">
                            <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                </path>
                            </svg>
                        </div>
                    </button>
                    
                    <button type="button" @click="$wire.set('showOtp', false)" class="text-sm text-center text-primary-100 hover:underline">
                        Back to Login
                    </button>
                </div>
            </form>
            @endif
            <!-- End Form -->
            <!-- End Form -->
          
            <div>

            </div>
          



        </div>
      
    </div>
</div>