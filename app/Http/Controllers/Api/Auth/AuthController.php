<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::guard('web')->attempt($credentials)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $user = Auth::guard('web')->user();
        return response()->json([
            'role' => $user->getRoleNames()->first(),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logout realizado']);
    }

    public function me(Request $request)
    {
        return response()->json([
            'role' => $request->user()->getRoleNames()->first(),
        ]);
    }
}