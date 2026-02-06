<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Login extends Component
{
    public $email;
    public $password;
    public $otp;
    public $showOtp = false;
    public $user_id;

    public function save()
    {
        // Validate the input fields
        $this->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6',
        ]);

        try {
            $user = \App\Models\User::where('email', $this->email)->first();

            if ($user && Hash::check($this->password, $user->password)) {
                if ($user->enable_otp === 'yes') {
                    $otpCode = rand(100000, 999999);
                    $user->update([
                        'otp' => $otpCode,
                        'otp_expires_at' => now()->addMinutes(10),
                    ]);

                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\LoginOtp($otpCode));

                    $this->user_id = $user->id;
                    $this->showOtp = true;
                    return;
                }

                // Normal login
                if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
                    session()->regenerate();
                    return redirect()->route('flight.search');
                }
            } else {
                session()->flash('error', 'Invalid email or password');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function verifyOtp()
    {
        $this->validate([
            'otp' => 'required|numeric',
        ]);

        $user = \App\Models\User::find($this->user_id);

        if ($user && $user->otp == $this->otp && now()->lt($user->otp_expires_at)) {
            // Success - log in
            Auth::login($user);
            
            // Clear OTP
            $user->update([
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            session()->regenerate();
            return redirect()->route('flight.search');
        } else {
            session()->flash('error', 'Invalid or expired OTP');
        }
    }
    
    #[Layout('components.layouts.guestLayout')]
    
    public function render()
    {
        return view('livewire.auth.login');
    }
}
