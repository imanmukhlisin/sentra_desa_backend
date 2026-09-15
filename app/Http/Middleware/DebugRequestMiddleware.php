<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DebugRequestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $uri = $request->path();
        
        Log::channel('daily')->info('=== DEBUG REQUEST START ===', [
            'uri' => $uri,
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'full_url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'cookies' => $request->cookies->all(),
            'session_id' => $request->session()->getId(),
        ]);

        try {
            $response = $next($request);
            
            Log::channel('daily')->info('=== DEBUG RESPONSE ===', [
                'uri' => $uri,
                'status' => $response->getStatusCode(),
                'headers' => $response->headers->all(),
            ]);
            
            return $response;
        } catch (\Throwable $e) {
            Log::channel('daily')->error('=== DEBUG EXCEPTION ===', [
                'uri' => $uri,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }
}
