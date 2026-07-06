<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Class AuthController
 * @package App\Http\Controllers\Api
 *
 * Handles user authentication (login, logout, registration)
 */
class AuthController extends Controller
{
    /**
     * Authenticate user and generate API token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string|min:6'
        ]);

        // Rate limiting (prevent brute force)
        // Keyed on ip+email so one attacker can't lock out a whole office
        // behind shared NAT, and rotating IPs alone doesn't reset the counter
        // for a targeted account.
        $key = 'login-attempts:' . $request->ip() . '|' . strtolower((string) $request->input('email'));
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many login attempts. Please try again in ' .
                            RateLimiter::availableIn($key) . ' seconds.'
            ], 429);
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Verify credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key);
            return response()->json([
                'message' => 'Invalid credentials. Please check your email and password.'
            ], 401);
        }

        // Reset rate limiter on successful login
        RateLimiter::clear($key);

        // Revoke existing tokens (optional: single device only)
        // $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('crm-token')->plainTextToken;

        // Load user relationships
        $user->load('companies');

        return response()->json([
            'status'      => 'success',
            'message'     => 'Login successful',
            'token'       => $token,
            'token_type'  => 'Bearer',
            'user'        => [
                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => $user->email,
                'active_company_id' => $user->active_company_id
            ],
            'roles'       => $user->getRoleNames(),
            // Added: frontend stores this on login so it can gate UI without
            // a separate /me call. Matches what me() already returns.
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'companies'   => $user->companies ?? []
        ]);
    }

    /**
     * Logout user and revoke token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get authenticated user details
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('companies');

        return response()->json([
            'user'        => $user,
            'roles'       => $user->getRoleNames(),
            'companies'   => $user->companies,
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]);
    }
}