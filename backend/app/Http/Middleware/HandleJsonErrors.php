<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleJsonErrors
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $response = $next($request);
            
            if (!$response instanceof Response) {
                $response = response()->json(['message' => 'Invalid response'], 500);
            }
            
            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error processing request',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
