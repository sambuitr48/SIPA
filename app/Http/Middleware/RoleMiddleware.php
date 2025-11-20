<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Verifica si el usuario autenticado tiene uno de los roles permitidos.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Verifica si el usuario no está autenticado
        if (! $user) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        // Verifica si su rol coincide con los permitidos
        if (! in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Forbidden. Insufficient permissions.',
            ], 403);
        }

        return $next($request);
    }
}
