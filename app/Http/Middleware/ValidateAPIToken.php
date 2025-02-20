<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateAPIToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $apiToken = env('API_TOKEN');

        if (!$token || $token !== $apiToken) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}
