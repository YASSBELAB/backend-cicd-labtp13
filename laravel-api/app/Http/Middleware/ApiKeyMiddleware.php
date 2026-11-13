<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request by validating Authorization: Bearer <API_KEY>
     * or an authenticated Sanctum user token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = config('services.api.key');
        $providedKey = $request->bearerToken();

        if ($expectedKey && $providedKey && hash_equals($expectedKey, $providedKey)) {
            return $next($request);
        }

        if ($request->user('sanctum')) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Clé API invalide ou manquante',
        ], 401);
    }
}
