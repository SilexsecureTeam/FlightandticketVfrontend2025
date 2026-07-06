<?php

namespace App\Http\Controllers;

use Exception;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use App\Http\Services\FlightSelectionAuthService;
use Illuminate\Validation\ValidationException;

class FlightSelectionAuthController extends Controller
{
    use HttpResponses;

    public function __construct(
        protected FlightSelectionAuthService $flightSelectionAuthService
    ) {
    }

    /**
     * Create user if new, or send OTP to existing user email.
     */
    public function sendOtp(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);

            $data = $this->flightSelectionAuthService->sendOtp(
                $validated['name'],
                $validated['email']
            );

            $message = $data['is_new_user']
                ? 'Account created successfully. OTP sent to your email.'
                : 'OTP sent to your email.';

            return $this->success($data, $message, 200);
        } catch (ValidationException $e) {
            return $this->error($e->errors(), 'Validation failed.', 422);
        } catch (\RuntimeException $e) {
            return $this->error([], $e->getMessage(), $e->getCode() >= 400 ? $e->getCode() : 400);
        } catch (Exception $e) {
            return $this->error(['error' => $e->getMessage()], 'Failed to send OTP. Please try again later.', 500);
        }
    }

    /**
     * Resend OTP to an existing user email.
     */
    public function resendOtp(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
            ]);

            $data = $this->flightSelectionAuthService->resendOtp($validated['email']);

            return $this->success($data, 'A new OTP has been sent to your email.', 200);
        } catch (ValidationException $e) {
            return $this->error($e->errors(), 'Validation failed.', 422);
        } catch (\RuntimeException $e) {
            return $this->error([], $e->getMessage(), $e->getCode() >= 400 ? $e->getCode() : 400);
        } catch (Exception $e) {
            return $this->error(['error' => $e->getMessage()], 'Failed to resend OTP. Please try again later.', 500);
        }
    }

    /**
     * Verify OTP and return Sanctum access token.
     */
    public function verifyOtp(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
                'otp' => 'required|digits:6',
            ]);

            $data = $this->flightSelectionAuthService->verifyOtp(
                $validated['email'],
                $validated['otp']
            );

            return $this->success($data, 'OTP verified successfully.', 200);
        } catch (ValidationException $e) {
            return $this->error($e->errors(), 'Validation failed.', 422);
        } catch (\RuntimeException $e) {
            return $this->error([], $e->getMessage(), $e->getCode() >= 400 ? $e->getCode() : 400);
        } catch (Exception $e) {
            return $this->error(['error' => $e->getMessage()], 'OTP verification failed. Please try again later.', 500);
        }
    }
}
