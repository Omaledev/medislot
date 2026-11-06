<?php

namespace App\Http\Middleware;

use App\Models\HospitalApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateHospitalApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Hospital-Key');

        if (! $token && $bearer = $request->bearerToken()) {
            $token = $bearer;
        }

        if (! $token) {
            $token = $request->query('api_key');
        }

        if (! $token || ! is_string($token)) {
            return response()->json([
                'message' => 'Hospital API key is missing. Please provide via X-Hospital-Key or Authorization Bearer header.',
            ], 401);
        }

        $apiKey = HospitalApiKey::findToken($token);

        if (! $apiKey || ! $apiKey->hospital) {
            return response()->json([
                'message' => 'Invalid or expired hospital API key.',
            ], 401);
        }

        $apiKey->recordUsage();

        $request->attributes->set('hospital', $apiKey->hospital);
        $request->attributes->set('hospital_api_key', $apiKey);

        return $next($request);
    }
}
