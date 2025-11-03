<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email;
    public $password;

    public function save()
    {
        // Validate the input fields
        $this->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6',
        ]);

        try {
            // Attempt to authenticate the user
            if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
                session()->regenerate();

                return redirect()->route('flight.search');
            } else {
                session()->flash('error', 'Invalid email or password');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
    
    #[Layout('components.layouts.guestLayout')]
    
    public function render()
    {
        return view('livewire.auth.login');
    }
}
