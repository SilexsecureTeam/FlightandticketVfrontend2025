<div class="h-full" x-data="{ editModal: @entangle('isEditModalOpen'), resetModal: @entangle('isResetModalOpen'), addModal: @entangle('isAddModalOpen') }">
    <style>
        input {
            background-color: #BEBEBE !important;
            height: 30px !important;
        }
    </style>
    <div class="min-h-full bg-gray-50">
        <main class="py-10">
            <div class="mx-auto md:max-w-[80%] w-[90%] px-4 sm:px-6 lg:px-8">
                <div class="mx-auto ">
                    <!-- Breadcrumb -->
                    <x-breadcrumb title="home" subtitle="Settings" />

                    <!-- Admin Users Section -->
                    <div class="mt-8">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold leading-6 text-gray-900">Admin Users</h2>
                            <button wire:click="openAddModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                Add Admin
                            </button>
                        </div>
                        
                        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($admins as $admin)
                            <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                                <div class="px-4 py-5 sm:p-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-primary rounded-full p-3 relative">
                                            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span class="absolute top-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white {{ $admin->status == 'active' ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 truncate flex items-center justify-between">
                                                    <span>{{ $admin->name }}</span>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $admin->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ ucfirst($admin->status) }}
                                                    </span>
                                                </dt>
                                                <dd>
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        {{ $admin->email }}
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $admin->phone }}
                                                    </div>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-between space-x-3">
                                    <button wire:click="editUser({{ $admin->id }})" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Edit
                                    </button>
                                    <button wire:click="openResetModal({{ $admin->id }})" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-full shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Reset Password
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Personal Information (Existing Section - Optional to keep or remove, but user asked to update "This" so I'll keep it separate or integrate) -->
                    <!-- The user asked to "Update this such that it get all user with type admin" -->
                    <!-- I will keep the existing "Your Password Change" section but adapt it if needed -->
                </div>
            </div>
        </main>
    </div>

    <!-- Edit Admin Modal -->
    <div x-show="editModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="editModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="editModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Admin Details</h3>
                    <form wire:submit.prevent="updateAdmin" class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" wire:model="name" class="mt-1 block w-full border-gray-300 @error('name') border-red-500 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" wire:model="email" disabled class="mt-1 block w-full border-gray-300 bg-gray-50 rounded-md shadow-sm sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="text" wire:model="phone" class="mt-1 block w-full border-gray-300 @error('phone') border-red-500 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Enable OTP</label>
                            <select wire:model="enable_otp" class="mt-1 block w-full border-gray-300 @error('enable_otp') border-red-500 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            @error('enable_otp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select wire:model="status" class="mt-1 block w-full border-gray-300 @error('status') border-red-500 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="active">Active</option>
                                <option value="block">Block</option>
                            </select>
                            @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mt-5 sm:mt-6 flex justify-end space-x-3">
                            <button type="button" @click="editModal = false" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:text-sm">
                                Cancel
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-blue-700 sm:text-sm">
                                <span wire:loading.remove wire:target="updateAdmin">Save Changes</span>
                                <span wire:loading wire:target="updateAdmin">Updating...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div x-show="resetModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="resetModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="resetModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Reset Admin Password</h3>
                    <p class="text-sm text-gray-500">Please enter your current password to authorize this action.</p>
                    <form wire:submit.prevent="resetAdminPassword" class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" wire:model="currentPassword" class="mt-1 block w-full border-gray-300 @error('currentPassword') border-red-500 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('currentPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" wire:model="newPassword" class="mt-1 block w-full border-gray-300 @error('newPassword') border-red-500 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('newPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" wire:model="confirmPassword" class="mt-1 block w-full border-gray-300 @error('confirmPassword') border-red-500 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('confirmPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mt-5 sm:mt-6 flex justify-end space-x-3">
                            <button type="button" @click="resetModal = false" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:text-sm">
                                Cancel
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-blue-700 sm:text-sm">
                                <span wire:loading.remove wire:target="resetAdminPassword">Reset Password</span>
                                <span wire:loading wire:target="resetAdminPassword">Resetting...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Admin Modal -->
    <div x-show="addModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="addModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="addModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Add New Admin User</h3>
                    <form wire:submit.prevent="storeAdmin" class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" wire:model="name" class="mt-1 block w-full border-gray-300 @error('name') border-red-500 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" wire:model="email" class="mt-1 block w-full border-gray-300 @error('email') border-red-500 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="text" wire:model="phone" class="mt-1 block w-full border-gray-300 @error('phone') border-red-500 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" wire:model="password" class="mt-1 block w-full border-gray-300 @error('password') border-red-500 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Enable OTP</label>
                            <select wire:model="enable_otp" class="mt-1 block w-full border-gray-300 @error('enable_otp') border-red-500 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                            @error('enable_otp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select wire:model="status" class="mt-1 block w-full border-gray-300 @error('status') border-red-500 @enderror rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="active">Active</option>
                                <option value="block">Block</option>
                            </select>
                            @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mt-5 sm:mt-6 flex justify-end space-x-3">
                            <button type="button" @click="addModal = false" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:text-sm">
                                Cancel
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-blue-700 sm:text-sm">
                                <span wire:loading.remove wire:target="storeAdmin">Create Admin</span>
                                <span wire:loading wire:target="storeAdmin">Creating...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

