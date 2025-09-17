<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // di dalam variabel $user kan dia bentuknya array assoc, nahh dsini kta tambahin lagi key yg namanya token ke dalam variabel $user
        $user['token'] = $request->user()->createToken('auth')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login Successful',
            'data' => $user,
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    // logout
    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout Successful',
        ]);
    }
}
