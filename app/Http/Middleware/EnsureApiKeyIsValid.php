<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiKeyIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->api_key !== md5('dntrademark.com')) {
            return response()->json([
                'status' => FALSE,
                'error' => 'Invalid API Key.'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
