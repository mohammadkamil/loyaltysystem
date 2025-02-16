<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Authentication
 *
 * APIs for user authentication
 */
class AuthController extends Controller
{
    /**
     * User Login
     *
     * Authenticate user and return a token.
     * 
     * @bodyParam email string required The email of the user. Example: user@example.com
     * @bodyParam password string required The password of the user. Example: password123
     *
     * @response 200 {
     *  "user": {
     *    "id": 1,
     *    "name": "John Doe",
     *    "email": "user@example.com"
     *  },
     *  "token": "1|asfghjklqwertyui"
     * }
     * 
     * @response 422 {
     *  "message": "The provided credentials are incorrect."
     * }
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * User Logout
     *
     * Revoke user token and logout.
     * 
     * @authenticated
     *
     * @response 200 {
     *  "message": "Logged out successfully"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Get Authenticated User
     *
     * Returns the currently authenticated user.
     * 
     * @authenticated
     *
     * @response 200 {
     *  "id": 1,
     *  "name": "John Doe",
     *  "email": "user@example.com"
     * }
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
