<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $rol)
    {
        if ($request->user()->role?->nombre !== $rol) {
            abort(403, 'Error');
        }

        return $next($request);
    }
}