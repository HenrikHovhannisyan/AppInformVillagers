<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;

class PhoneVerificationController extends Controller
{
    public function sendVerificationCode(Request $request)
    {
        try {
            $request->validate(['phone' => 'required|unique:users,phone']);

            $verificationCode = rand(100000, 999999);

            $user = User::create([
                'phone' => $request->phone,
                'verification_code' => $verificationCode,
            ]);

            $this->sendSms($request->phone, "Your confirmation code: $verificationCode");

            return response()->json(['message' => 'Code sent'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => collect($e->errors())->flatten()->first(), // Берём только первое сообщение
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Internal server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function verifyCode(Request $request)
    {
        try {
            $request->validate([
                'verification_code' => 'required',
                'password' => 'required|confirmed|min:8',
            ]);

            $user = User::where('verification_code', $request->verification_code)->first();

            if (!$user) {
                return response()->json(['errors' => 'Invalid code'], 422);
            }

            $user->update([
                'is_verified' => true,
                'password' => Hash::make($request->password),
                'verification_code' => null,
            ]);

            return response()->json(['message' => 'User registered successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => collect($e->errors())->flatten()->first(), // Берём только первое сообщение
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Internal server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function sendSms($phone, $message)
    {
        try {
            $client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
            $client->messages->create($phone, [
                'from' => env('TWILIO_PHONE'),
                'body' => $message,
            ]);
        } catch (\Exception $e) {
            \Log::error('Twilio Error: ' . $e->getMessage());
            return response()->json(['errors' => 'Failed to send SMS'], 500);
        }
    }
}

