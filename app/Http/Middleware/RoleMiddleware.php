<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
public function handle(Request $request, Closure $next, string $rol)
{
    $user = $request->user();

    if (!$user || !$user->role) abort(403);

    if ($user->role->nombre === 'administrador' || $user->role->nombre === $rol) {
        return $next($request);
    }

    abort(403);
}
}