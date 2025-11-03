<div class="h-full">
    <div class="min-h-full bg-gray-50">
        <main class="py-10">
            <div class="mx-auto md:max-w-[80%] w-[90%] px-4 sm:px-6 lg:px-8">
                <div class="mx-auto ">
                    <!-- Breadcrumb -->
                    <x-breadcrumb title="home" subtitle="Settings" />

                
                    <!-- Settings Section -->
                    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <!-- Personal Information -->
                        <div class="lg:col-span-2">
                       


                            <div class="bg-white shadow-sm ring-1 mt-5 ring-gray-900/5 sm:rounded-xl">
                                <div class="px-4 py-6 sm:px-6">
                                    <h2 class="text-lg font-semibold leading-6 text-gray-900">Personal Imformation</h2>
                                    <p class="mt-1 text-sm text-gray-500">Update your Personal Detaiils here.</p>

                                </div>
                                <div class="border-t border-gray-100 px-4 py-6 sm:px-6">
                                    <form wire:submit.prevent="updateProfile" x-on:submit.prevent class="space-y-6">
                                        <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                                            <div>
                                                <label for="fullName" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                                                <div class="mt-2 relative rounded-md shadow-sm">
                                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <input type="text" wire:model="first_name"
                                                        class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                                    />
                                                </div>
                                            </div>
                                           
                                            <div>
                                                <label for="fullName" class="block text-sm font-medium leading-6 text-gray-900">Phone Number</label>
                                                <div class="mt-2 relative rounded-md shadow-sm">
                                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <input type="text" wire:model="phone"
                                                        class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                                    />
                                                </div>
                                            </div>
                                       
                                        </div>

                                        <div>
                                            <label for="emailAddress" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                                            <div class="mt-2 relative rounded-md shadow-sm">
                                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                                    </svg>
                                                </div>
                                                <input type="email"  disabled wire:model="email"
                                                    class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                                />
                                            </div>
                                        </div>



                                        <div class="flex justify-end space-x-3">
                                           
                                            <button type="submit"
                                                class="rounded-md py-2 text-sm font-semibold text-white shadow-sm "
                                            >
                                              

                                                
                                            <div wire:loading.remove     wire:target="updateProfile" type="submit" class="rounded-md w-full bg-primary hover:bg-black px-3 py-2 text-sm font-semibold text-white shadow-sm ">
                                              
                                                Update 
                                                
                                            </div>
                                    
                                            <div wire:loading     wire:target="updateProfile"
  class="w-10 h-10 mr-3 border-4 border-t-primary-200 border-gray-300 rounded-full animate-spin"
  ></div>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Photo Upload -->
                        <div class="lg:col-span-1">
                            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                                <div class="px-4 py-6 sm:px-6">
                                    <h2 class="text-lg font-semibold leading-6 text-gray-900">Change Password</h2>

                                </div>
                                <div class="border-t border-gray-100 px-4 py-6 sm:px-6">
                                    <form wire:submit.prevent="updatePassword" x-on:submit.prevent class="space-y-6">
                                        <div class="grid grid-cols-1 gap-x-6 gap-y-6 ">
                                            <div>
                                                <label for="fullName" class="block text-sm font-medium leading-6 text-gray-900">Current Password</label>
                                                <div class="mt-2 relative rounded-md shadow-sm">
                                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <input type="text" wire:model="currentPassword"
                                                        class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                                    />
                                                </div>
                                            </div>
                                            <div>
                                                <label for="fullName" class="block text-sm font-medium leading-6 text-gray-900">New Password</label>
                                                <div class="mt-2 relative rounded-md shadow-sm">
                                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <input type="text" wire:model="newPassword"
                                                        class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                                    />
                                                </div>
                                            </div>
                                            <div>
                                                <label for="fullName" class="block text-sm font-medium leading-6 text-gray-900">Confirm New Password</label>
                                                <div class="mt-2 relative rounded-md shadow-sm">
                                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <input type="text" wire:model="confirmPassword"
                                                        class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                                    />
                                                </div>
                                            </div>
                                          
                                        </div>



                                        <div class="flex justify-end space-x-3">
                                           
                                            <button type="submit"
                                                class="rounded-md py-2 text-sm font-semibold text-white shadow-sm "
                                            >
                                              

                                                
                                            <div wire:loading.remove  wire:target="updatePassword"     type="submit" class="rounded-md w-full bg-primary hover:bg-black px-3 py-2 text-sm font-semibold text-white shadow-sm ">
                                              
                                                Update 
                                                
                                            </div>
                                    
                                            <div wire:loading     wire:target="updatePassword"
  class="w-10 h-10 mr-3 border-4 border-t-primary border-gray-300 rounded-full animate-spin"
  ></div>
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

