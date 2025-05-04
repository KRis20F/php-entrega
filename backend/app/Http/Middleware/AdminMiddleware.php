<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Middleware to check if user is admin
// Middleware para verificar si el usuario es administrador
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized - Not authenticated'], 401);
        }

        if (!$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized - Admin role required'], 403);
        }

        return $next($request);
    }
}