<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (is_null($user->email_verified_at)) {
            return response()->json(['message' => 'Email not verified'], 403);
        }

        return $next($request);
    }
}