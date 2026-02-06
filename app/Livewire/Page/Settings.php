<?php

namespace App\Livewire\Page;


use App\Models\Markup;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.admin')]
class Settings extends Component
{
    use LivewireAlert;

  
    public $currentPassword;
    public $newPassword;
    public $confirmPassword;

    // Admin Management Properties
    public $adminId;
    public $name;
    public $email;
    public $phone;
    public $status = 'active';
    public $enable_otp = 'no';

    public $isEditModalOpen = false;
    public $isResetModalOpen = false;
    public $isAddModalOpen = false;

    public $password;

    public function mount()
    {
        // No mount logic needed for now as we fetch admins in render or a dedicated property
    }

    public function openAddModal()
    {
        $this->reset(['adminId', 'name', 'email', 'phone', 'password', 'status']);
        $this->status = 'active';
        $this->isAddModalOpen = true;
    }

    public function storeAdmin()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:6',
            'status' => 'required|in:active,block',
        ]);

        try {
            \App\Models\User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'type' => 'admin',
                'enable_otp' => $this->enable_otp,
                'status' => $this->status,
            ]);

            $this->isAddModalOpen = false;
            $this->alert('success', 'Admin created successfully', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function editUser($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $this->adminId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->status = $user->status;
        $this->enable_otp = $user->enable_otp;
        $this->isEditModalOpen = true;
    }

    public function updateAdmin()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,block',
        ]);

        try {
            $user = \App\Models\User::findOrFail($this->adminId);
            $user->update([
                'name' => $this->name,
                'phone' => $this->phone,
                'status' => $this->status,
                'enable_otp' => $this->enable_otp,
            ]);

            $this->isEditModalOpen = false;
            $this->alert('success', 'Admin updated successfully', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function openResetModal($id)
    {
        $this->adminId = $id;
        $this->reset(['currentPassword', 'newPassword', 'confirmPassword']);
        $this->isResetModalOpen = true;
    }

    public function resetAdminPassword()
    {
        $this->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|min:6',
            'confirmPassword' => 'required|same:newPassword'
        ]);

        try {
            // Verify current auth user's password to authorize the reset
            if (!Hash::check($this->currentPassword, auth()->user()->password)) {
                $this->alert('error', 'Current password is incorrect', [
                    'position' => 'top-end',
                    'timer' => 3000,
                    'toast' => true,
                ]);
                return;
            }

            $user = \App\Models\User::findOrFail($this->adminId);
            $user->update([
                'password' => Hash::make($this->newPassword)
            ]);

            $this->isResetModalOpen = false;
            $this->alert('success', 'Password reset successfully', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function updatePassword()
    {
        try {
            $this->validate([
                'currentPassword' => 'required',
                'newPassword' => 'required|min:6',
                'confirmPassword' => 'required|same:newPassword'
            ]);

            if (!Hash::check($this->currentPassword, auth()->user()->password)) {
                $this->alert('error', 'Current password is incorrect', [
                    'position' => 'top-end',
                    'timer' => 5000,
                    'toast' => true,
                ]);
                return;
            }

            auth()->user()->update([
                'password' => Hash::make($this->newPassword)
            ]);

            $this->alert('success', 'Password updated successfully', [
                'position' => 'top-end',
                'timer' => 5000,
                'toast' => true,
            ]);


            $this->reset(['currentPassword', 'newPassword', 'confirmPassword']);
        } catch (\Exception $e) {
            $this->alert('error',   $e->getMessage(), [
                'position' => 'top-end',
                'timer' => 5000,
                'toast' => true,
            ]);

        }
    }

    public function render()
    {
        return view('livewire.page.settings', [
            'admins' => \App\Models\User::where('type', 'admin')->get()
        ]);
    }
}
