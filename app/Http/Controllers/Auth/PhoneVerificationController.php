<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;

class PhoneVerificationController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sendVerificationCode(Request $request)
    {
        try {
            $request->validate(['phone' => 'required|unique:users,phone']);

//            $verificationCode = rand(100000, 999999);
            $verificationCode = 123456;

            $user = User::create([
                'avatar' => '/images/default-avatar.png',
                'phone' => $request->phone,
                'verification_code' => $verificationCode,
            ]);

            $this->sendSms($request->phone, "Your confirmation code: $verificationCode");

            return response()->json(['message' => 'Code sent'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Internal server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
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

            $user->account()->create([
                'field_size' => null,
                'tree_count' => null,
                'olive_type' => null,
                'age_of_trees' => null,
                'location_of_field' => null,
                'continuous_season_count' => null,
                'total_harvested_olives' => null,
                'total_gained_oil' => null,
                'account_creation_date' => now(),
                'edit_request' => false,
                'admin_approval' => false,
            ]);

            $currentYear = now()->year;
            for ($i = 0; $i < 5; $i++) {
                $user->statistics()->create([
                    'year' => $currentYear - $i,
                    'olive_amount' => null,
                    'oil_amount' => null,
                ]);
            }

            return response()->json(['message' => 'User registered successfully'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Internal server error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param $phone
     * @param $message
     * @return JsonResponse
     */
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|exists:users,phone',
                'password' => 'required',
            ]);

            $user = User::where('phone', $request->phone)->first();

            if (!$user->is_verified) {
                return response()->json(['errors' => 'Phone number not verified'], 403);
            }

            if (!$user->status) {
                return response()->json(['errors' => 'Your account is inactive.'], 403);
            }

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['errors' => 'Invalid phone number or password'], 422);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $avatarUrl = $user->avatar ? asset($user->avatar) : null;

            $userData = $user->only(['id', 'name', 'surname', 'email', 'phone', 'role']);
            $userData['avatar'] = $avatarUrl;

            $response = [
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $userData,
            ];

            return response()->json($response, 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => collect($e->errors())->flatten()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => 'Internal server error: ' . $e->getMessage(),
            ], 500);
        }
    }

}

