<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request): Response
    {

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'success' => false,
                'message' => "Email and Password incorrect",
            ]);
        }
        $user = Auth::user();
        return response([
            'success' => true,
            'message' => "Register successfully",
            'data'    => [
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken,
            ]
        ]);
    }
    public function logout(Request $request): Response
    {
        // Get the authenticated user's token
        $request->user()->currentAccessToken()->delete();

        return response([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }
}
