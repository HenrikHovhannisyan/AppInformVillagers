<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display the specified user.
     * @return JsonResponse
     */
    public function show()
    {
        $user = auth()->user();

        $responseUser = [
            'id' => $user->id,
            'name' => $user->name,
            'surname' => $user->surname,
            'avatar' => $user->avatar ? asset($user->avatar) : null,
            'role' => $user->role,
            'email' => $user->email,
            'phone' => $user->phone,
        ];

        return response()->json($responseUser, 200);
    }

    /**
     * Update the specified user in storage.
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(Request $request, User $user)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255',
                'surname' => 'nullable|string|max:255',
                'avatar' => 'nullable|file|image|max:2048',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|unique:users,phone,' . $user->id,
                'password' => 'nullable|confirmed|min:8',
            ]);

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');

                if (!$file->isValid()) {
                    return response()->json(['error' => 'File upload failed'], 400);
                }

                $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->move(public_path('avatars'), $filename);

                if ($user->avatar && file_exists(public_path($user->avatar))) {
                    unlink(public_path($user->avatar));
                }

                $validatedData['avatar'] = 'avatars/' . $filename;
            }

            if (isset($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            $user->update($validatedData);

            $responseUser = $user->only(['id', 'name', 'surname', 'avatar', 'role', 'email', 'phone']);
            if ($user->avatar) {
                $responseUser['avatar'] = url($user->avatar); // Генерируем полный URL
            }

            return response()->json([
                'message' => 'User information updated successfully',
                'user' => $responseUser,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user)
    {
        try {
            if (Auth::id() !== $user->id) {
                return response()->json(['error' => 'You are not authorized to delete this account'], 403);
            }

            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            $user->delete();

            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get statistics for the authenticated user.
     *
     * @return JsonResponse
     */
    public function getOwnStatistics()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized.',
            ], 401);
        }

        $statistics = $user->statistics;

        return response()->json([
            'statistics' => $statistics,
        ]);
    }

}
