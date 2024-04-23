<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasHeader('username')) {
            $user = User::where('username', $request->header('username'))->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not Found']);
            }

            $request->attributes->add(['user' => $user]);
            return $next($request);
        }
        return response()->json(['success' => false, 'message' => 'Provide username in header to proceed further.']);
    }
}