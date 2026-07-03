<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
public function handle($request, Closure $next, $permission)
{
    $user = $request->user();

  
    if (!$user || !$user->role) abort(403);

    if ($user->role->nombre === 'administrador') return $next($request);

    if (!$user->role->permissions->contains('nombre', $permission)) {
        abort(403);
    }

    return $next($request);
}
}
