<?php

namespace App\Http\Controllers\Authcontroller;

use Exception;
use App\Models\User;
use App\Mail\VerifyEmail;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\Authentication\Otp;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class Authcontroller extends Controller
{
    use HttpResponses;

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    try {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error([], "Invalid credentials.", 401);
        }


        if (!$user->is_email_verified) {
            Auth::logout();
            return $this->error('Please verify your email before logging in.', [], 403);
        }

        // Generate and return token
        $token = $user->createToken('access_token')->plainTextToken;

        return $this->success(['user' =>$user,'token' => $token], 'Login successful.', 200);
    } catch (Exception $e) {
        return $this->error('Login failed. Please try again later.', ['error' => $e->getMessage()], 500);
    }
}

    public function register(Request $request)
    {
        try {

         $validated =   $request->validate([
                'username' => 'required|string|min:3|max:20|unique:users,username',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);



        $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            Otp::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(3),
            ]);

            Mail::to($request->email)->send(new VerifyEmail(
                $request->username,
                $request->email,
                $otp
            ));

            return $this->success(['otp' => $otp], ' Registered successfully. OTP sent to your email.', 201);
        
         } catch (ValidationException $e) {
                // Handle validation errors
                return $this->error( 'Validation failed.', $e->getMessage(), 422);
                
            }
         
        catch (Exception $e) {
            return $this->error('Registration failed. Please try again later.', ['error' => $e->getMessage()], 500);
        }
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:4',
        ]);

        try {
            $user = User::where('email', $request->email)->firstOrFail();
            $otp = Otp::where('user_id', $user->id)
                      ->where('otp', $request->otp)
                      ->where('expires_at', '>', now())
                      ->first();

            if (!$otp) {
                return $this->error( [], 'Invalid or expired OTP.', 400);
            }

            // Mark OTP as used or delete
            $otp->delete();

            $user->update([
                'email_verified_at' => now(),
                'is_email_verified' => true,
            ]);

            // Generate and return token
            $token = $user->createToken('access_token')->plainTextToken;

            return $this->success(['user' =>$user,'token' => $token], 'OTP verified successfully.', 200);
        } catch (Exception $e) {

            return $this->error('Verification failed. Please try again later.', ['error' => $e->getMessage()], 500);
        }
    }

    public function resendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    try {
        $user = User::where('email', $request->email)->firstOrFail();

        $key = 'otp-' . $user->id;
        // if (RateLimiter::remaining($key, 5) === 0) {
        //     return $this->error(' limit exceeded. Please try again later.', [], 429);
        // }


        // Check if an OTP already exists and hasn't expired
        $existingOtp = Otp::where('user_id', $user->id)
                           ->where('expires_at', '>', now())
                           ->first();

        if ($existingOtp) {
            return $this->error('An OTP is already valid. Please wait before requesting a new one.', [], 400);
        }

        $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        Otp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(3),
        ]);

            Mail::to($request->email)->send(new VerifyEmail(
                $request->username,
                $request->email,
                $otp
            ));

        return $this->success(['otp' => $otp], 'OTP has been sent to your email.', 200);
    } catch (Exception $e) {
        return $this->error('Failed to resend OTP. Please try again later.', ['error' => $e->getMessage()], 500);
    }
}


public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    try {
        $email = $request->email;

        // Retrieve user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->error('No user found with this email address.', [], 404);
        }

        $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        Otp::updateOrCreate(
            ['user_id' => $user->id],
            ['otp' => $otp, 'expires_at' => now()->addMinutes(3)]
        );

        // Generate password reset token
        $token = Password::createToken($user);
        $frontendUrl = env('FRONTEND_URL') . '/reset-password';
        $resetLink = $frontendUrl . '?token=' . $token . '&email=' . urlencode($email);

        // Commented out email sending for now
        // Mail::to($email)->send(new SendOtpAndResetLinkMail($otp, $resetLink));

        return $this->success([
            'otp' => $otp,
            'reset_link' => $resetLink
        ], 'OTP and password reset link generated successfully.', 200);

    } catch (\Exception $e) {
        return $this->error('Forgot password request failed. Please try again later.', ['error' => $e->getMessage()], 500);
    }
}



public function resetPassword(Request $request)
{
    $request->validate([
        'new_password' => 'required|string|confirmed',
        'otp' => 'nullable|integer',
    ]);

    $otp = $request->otp;
    $newPassword = $request->new_password;

    if ($otp) {
        // Validate OTP
        $otpRecord = Otp::where('user_id', User::where('email', Auth::user()->email)->value('id'))
                        ->where('otp', $otp)
                        ->where('expires_at', '>', now())
                        ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP'], 422);
        }

        // Reset password using OTP
        $user = User::where('email', Auth::user()->email)->first();
        $user->password = bcrypt($newPassword);
        $user->save();

        return response()->json(['message' => 'Password reset successfully using OTP'], 200);

    } else {
        return response()->json(['message' => 'OTP or token is required'], 422);
    }
}

public function resetForgetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|string',
        'new_password' => 'required|string|confirmed',
        'otp' => 'nullable|integer',
    ]);

    $otp = $request->otp;
    $newPassword = $request->new_password;

    if ($otp) {
        // Validate OTP
        $otpRecord = Otp::where('user_id', User::where('email', $request->email)->value('id'))
                        ->where('otp', $otp)
                        ->where('expires_at', '>', now())
                        ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP'], 422);
        }

        // Reset password using OTP
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($newPassword);
        $user->save();

        return response()->json(['message' => 'Password reset successfully using OTP'], 200);

    } else {
        return response()->json(['message' => 'OTP or token is required'], 422);
    }
}


public function logout(Request $request)
{
        $user = User::find(Auth::id());
        $user->tokens()->delete();
        // $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'logout successfully'], 200);
}

public function deleteAccount(Request $request)
{
        $user = User::find(Auth::id());
        $user->tokens()->delete();
        $user->delete();
        // $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Account deleted successfully'], 200);
}

public function updateUserOnboarding(Request $request)
{
        $user = User::find(Auth::id());
        $user->update([
            'onboarding_state' => $request->onboarding_state
        ]);

        return $this->success([User::find(Auth::id())], 'User onboarding state updated successfully', 200);
}

public function updateUserWidget(Request $request)
{
        $user = User::find(Auth::id());
        $user->update([
            'widget_state' => $request->widget_state
        ]);

        return $this->success([User::find(Auth::id())], 'User widget State updated successfully', 200);
}

public function getUserDetails()
{
    return $this->success([User::find(Auth::id())], 'User details', 200);
}


}

