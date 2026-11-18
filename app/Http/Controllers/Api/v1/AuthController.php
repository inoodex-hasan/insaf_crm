<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\v1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
{
    /**
     * Authenticate user and issue Sanctum token
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required_without:username|string',
            'username' => 'required_without:email|string',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:100',
        ]);

        $loginField = $request->filled('email') ? 'email' : 'username';
        $user = User::where($loginField, $request->input($loginField))->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Invalid credentials provided.', [
                'auth' => ['The provided credentials do not match our records.'],
            ], 401);
        }

        // Generate Sanctum Bearer Token
        $deviceName = $request->input('device_name', 'web-client');
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->sendResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
        ], 'Login successful.');
    }

    /**
     * Return authenticated user profile and roles
     */
    public function me(Request $request): JsonResponse
    {
        return $this->sendResponse(
            new UserResource($request->user()),
            'User profile retrieved successfully.'
        );
    }

    /**
     * Revoke current access token
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->sendResponse(null, 'Successfully logged out.');
    }

    /**
     * Update user profile information
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'designation' => 'nullable|string|max:255',
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($validated['password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return $this->sendError('Current password does not match.', [
                    'current_password' => ['The provided current password is incorrect.'],
                ], 422);
            }
            $user->password = Hash::make($validated['password']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (isset($validated['designation'])) {
            $user->designation = $validated['designation'];
        }
        $user->save();

        return $this->sendResponse(
            new UserResource($user),
            'Profile updated successfully.'
        );
    }
}
