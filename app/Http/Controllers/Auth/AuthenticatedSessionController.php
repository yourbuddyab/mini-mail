<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();
        $user = $request->user();
        $user->tokens()->delete();
        return response([
            'success' => true,
            'message' => "Register successfully",
            'data'    => [
                'user' => $user,
                'token' => $user->createToken('auth_token')->plainTextToken,
            ]
        ]);
    }
    public function destroy(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }
}
