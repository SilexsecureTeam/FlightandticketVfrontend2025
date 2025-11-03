       <!-- Settings Section -->
       <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Personal Information -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <div class="px-4 py-6 sm:px-6">
                    <h2 class="text-lg font-semibold leading-6 text-gray-900">Payment Settings</h2>
                    <p class="mt-1 text-sm text-gray-500">Update Mark up Price   here.</p>
                </div>
                <div class="border-t border-gray-100 px-4 py-6 sm:px-6">
                    <form wire:submit.prevent="updatemarkup" x-on:submit.prevent class="space-y-6">
                        <div class="grid grid-cols-1 gap-x-6 gap-y-6 ">
                            <div>
                                <label for="fullName" class="block text-sm font-medium leading-6 text-gray-900">Mark Up Price</label>
                                <div class="mt-2 relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        ₦
                                    </div>
                                    <input type="number" wire:model="mark_up"
                                        class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6"
                                    />
                                </div>
                            </div>
                        
                        
                        </div>


                        <div class="flex justify-end space-x-3">
                           
                            <button type="submit"
                                class="rounded-md py-2 text-sm font-semibold text-white shadow-sm "
                            >
                              

                                
                            <div wire:loading.remove     wire:target="updatemarkup" type="submit" class="rounded-md w-full bg-primary hover:bg-black px-3 py-2 text-sm font-semibold text-white shadow-sm ">
                              
                                Update 
                                
                            </div>
                    
                            <div wire:loading     wire:target="updatemarkup"
class="w-10 h-10 mr-3 border-4 border-t-primary border-gray-300 rounded-full animate-spin"
></div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>


        </div>

       
    </div>