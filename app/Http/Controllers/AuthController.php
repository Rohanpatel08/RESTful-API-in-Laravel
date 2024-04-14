<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {

            // dd($request);
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            // dd($credentials);
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return response()->json(['message' => 'Authenticated successfully'], 200);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
