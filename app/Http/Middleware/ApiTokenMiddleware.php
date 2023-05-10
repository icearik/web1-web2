<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authKey = Cache::get('authKey');
	if (!$authKey) {
            return response('No key has been generated', 401);
        }
        if (!$request->header('X-AUTH-FINAL-TOKEN')) {
            return response()->json(['message' => 'No token supplied'], 401);
        }

        if ($request->header('X-AUTH-FINAL-TOKEN') !== $authKey) {
            return response()->json(['message' => 'Invalid token'], 401);
        }
	return $next($request);
    }
}
