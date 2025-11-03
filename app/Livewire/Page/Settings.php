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
        return view('livewire.page.settings');
    }
}
