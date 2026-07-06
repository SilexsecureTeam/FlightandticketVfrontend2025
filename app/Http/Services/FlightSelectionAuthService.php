<?php

namespace App\Http\Services;

use App\Mail\LoginOtp;
use App\Mail\WelcomeOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class FlightSelectionAuthService
{
    public function sendOtp(string $name, string $email): array
    {
        $user = User::query()->where('email', $email)->first();
        $otpCode = (string) random_int(100000, 999999);
        $isNewUser = false;

        if (!$user) {
            $isNewUser = true;
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(32)),
                'phone' => '',
                'type' => 'user',
                'status' => 'active',
                'enable_otp' => 'no',
                'otp' => $otpCode,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            Mail::to($user->email)->send(new WelcomeOtp($otpCode, $user->name));
        } else {
            $this->ensureUserIsActive($user);

            $user->update([
                'name' => $name,
                'otp' => $otpCode,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            Mail::to($user->email)->send(new LoginOtp($otpCode));
        }

        return [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'is_new_user' => $isNewUser,
        ];
    }

    public function resendOtp(string $email): array
    {
        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            throw new \RuntimeException('No account found for this email. Please submit your name and email first.', 404);
        }

        $this->ensureUserIsActive($user);

        $otpCode = (string) random_int(100000, 999999);
        $user->update([
            'otp' => $otpCode,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new LoginOtp($otpCode));

        return [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ];
    }

    public function verifyOtp(string $email, string $otp): array
    {
        $user = User::query()->where('email', $email)->first();

        if (
            !$user
            || (string) $user->otp !== (string) $otp
            || !$user->otp_expires_at
            || now()->gte($user->otp_expires_at)
        ) {
            throw new \RuntimeException('Invalid or expired OTP. Please try again or resend a new code.', 400);
        }

        $this->ensureUserIsActive($user);

        $user->update([
            'otp' => null,
            'otp_expires_at' => null,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        $token = $user->createToken('access_token')->plainTextToken;

        return [
            'user' => $user->fresh(),
            'token' => $token,
        ];
    }

    protected function ensureUserIsActive(User $user): void
    {
        if ($user->status === 'block') {
            throw new \RuntimeException('This account is blocked. Please contact support.', 403);
        }
    }
}
