<?php

namespace App\Http\Controllers;

use Exception;
use App\Mail\LoginOtp;
use App\Mail\WelcomeOtp;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\Authentication\Otp;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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

            if ($user->status === 'deleted') {
                return $this->error([], "Your account does not exist. Please contact support.", 401);
            }

            if ($user->status === 'block') {
                return $this->error([], "Your account is blocked. Please contact support.", 401);
            }

            $newOtp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $user->update([
                'otp' => $newOtp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);


            if (!$user->email_verified_at) {
                Auth::logout();
                Mail::to($user->email)->send(new WelcomeOtp($newOtp, $user->name));
                return $this->error([], 'Please verify your email before logging in. Verification OTP sent to your email.', 403);
            }

            if ($user->enable_otp === 'yes') {
                Auth::logout();
                Mail::to($user->email)->send(new LoginOtp($newOtp));
                return $this->error([], 'Please verify your email before logging in. Verification OTP sent to your email.', 403);
            } else {
                $user->update([
                    'otp' => null,
                    'otp_expires_at' => null,
                ]);

                $token = $user->createToken('access_token')->plainTextToken;

                return $this->success(['user' => $user, 'token' => $token], 'Login successfully.', 200);
            }

            // Generate and return token
            // $token = $user->createToken('access_token')->plainTextToken;

        } catch (Exception $e) {
            return $this->error('Login failed. Please try again later.', ['error' => $e->getMessage()], 500);
        }
    }

    public function register(Request $request)
    {
        try {

            $validated =   $request->validate([
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'address' => 'nullable|string|max:500',
                'avatar' => 'nullable|string|max:500',
                'zipcode' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'daily_report' => 'nullable|in:yes,no',
                'weekly_report' => 'nullable|in:yes,no',
                'monthly_report' => 'nullable|in:yes,no',
                'yearly_report' => 'nullable|in:yes,no',
            ]);

            $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $user = User::create([
                'name' => $validated['name'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'address' => $validated['address'] ?? null,
                'avatar' => $validated['avatar'] ?? null,
                'zipcode' => $validated['zipcode'] ?? null,
                'country' => $validated['country'] ?? null,
                'state' => $validated['state'] ?? null,
                'city' => $validated['city'] ?? null,
                'daily_report' => $validated['daily_report'] ?? 'no',
                'weekly_report' => $validated['weekly_report'] ?? 'no',
                'monthly_report' => $validated['monthly_report'] ?? 'no',
                'yearly_report' => $validated['yearly_report'] ?? 'no',
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10)
            ]);


            Mail::to($user->email)->send(new WelcomeOtp($otp, $user->name));

            return $this->success(' Registered successfully. OTP sent to your email.', 201);
        } catch (ValidationException $e) {
            // Handle validation errors
            return $this->error('Validation failed.', $e->getMessage(), 422);
        } catch (Exception $e) {
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
            if (!$user->otp || $user->otp !== $request->otp) {
                return $this->error([], 'Invalid OTP.', 400);
            }

            if (!$user->otp_expires_at || now()->greaterThan($user->otp_expires_at)) {

                if (!$user->is_email_verified) {
                    $newOtp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
                    $user->update([
                        'otp' => $newOtp,
                        'otp_expires_at' => now()->addMinutes(10),
                    ]);

                    Mail::to($user->email)->send(new WelcomeOtp($newOtp, $user->name));

                    return $this->error(
                        [],
                        'Your OTP has expired. Use the new OTP sent to your mail to verify your account.',
                        200
                    );
                }

                return $this->error([], 'OTP is expired.', 400);
            }

            // Mark OTP as used
            $user->update([
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            if (!$user->is_email_verified) {
                $user->update([
                    'email_verified_at' => now(),
                ]);
            }

            // Generate and return token
            $token = $user->createToken('access_token')->plainTextToken;

            return $this->success(['user' => $user, 'token' => $token], 'OTP verified successfully.', 200);
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

            // $key = 'otp-' . $user->id;
            // if (RateLimiter::remaining($key, 5) === 0) {
            //     return $this->error(' limit exceeded. Please try again later.', [], 429);
            // }


            // Check if an OTP already exists and hasn't expired
            if ($user->otp && $user->otp_expires_at && now()->lessThanOrEqualTo($user->otp_expires_at)) {
                return $this->error('An OTP is already valid. Please wait before requesting a new one.', [], 400);
            }

            $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $user->update([
                'otp' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            if (!$user->is_email_verified) {
                Mail::to($user->email)->send(new WelcomeOtp($otp, $user->name));
            } else {
                Mail::to($user->email)->send(new LoginOtp($otp));
            }

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
        $user->update([
            'status' => 'deleted',
        ]);
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

    public function getProfile()
    {
        $user = Auth::user();

        $profile = [
            'name' => $user->name,
            'phone' => $user->phone,
            'address' => $user->address,
            'avatar' => $user->avatar ? url($user->avatar) : null,
            'zipcode' => $user->zipcode,
            'country' => $user->country,
            'state' => $user->state,
            'city' => $user->city,
            'daily_report' => $user->daily_report,
            'weekly_report' => $user->weekly_report,
            'monthly_report' => $user->monthly_report,
            'yearly_report' => $user->yearly_report,
            'enable_otp' => $user->enable_otp,
        ];

        return $this->success($profile, 'User profile details.', 200);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'zipcode' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'daily_report' => 'nullable|in:yes,no',
            'weekly_report' => 'nullable|in:yes,no',
            'monthly_report' => 'nullable|in:yes,no',
            'yearly_report' => 'nullable|in:yes,no',
            'enable_otp' => 'nullable|in:yes,no',
        ]);

        try {
            if ($request->hasFile('avatar')) {
                // Delete old avatar if it exists (public/avatars or legacy storage)
                if ($user->avatar) {
                    $publicAvatar = public_path($user->avatar);
                    if (File::exists($publicAvatar)) {
                        File::delete($publicAvatar);
                    } elseif (Storage::disk('public')->exists($user->avatar)) {
                        Storage::disk('public')->delete($user->avatar);
                    }
                }

                $destination = public_path('avatars');
                if (!File::isDirectory($destination)) {
                    File::makeDirectory($destination, 0755, true);
                }

                $filename = $request->file('avatar')->hashName();
                $request->file('avatar')->move($destination, $filename);
                $validated['avatar'] = 'avatars/' . $filename;
            }

            $user->update($validated);

            return $this->success($user, 'Profile updated successfully.', 200);
        } catch (Exception $e) {
            return $this->error('Profile update failed. Please try again later.', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Change password while authenticated (requires previous password).
     */
    public function changePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'previous_password' => 'nullable|string',
                'password' => 'required|string|min:8',
                'confirm_password' => 'required|same:password',
            ]);

            $user = Auth::user();
            if($request->request_type !== "forgot_password") {
                if (!Hash::check($validated['previous_password'], $user->password)) {
                    return $this->error([], 'The previous password is incorrect.', 422);
                }
            }

            $user->update([
                'password' => $validated['password'],
            ]);

            return $this->success([], 'Password updated successfully.', 200);
        } catch (ValidationException $e) {
            return $this->error('Validation failed.', $e->getMessage(), 422);
        } catch (Exception $e) {
            return $this->error('Password update failed. Please try again later.', ['error' => $e->getMessage()], 500);
        }
    }
}


