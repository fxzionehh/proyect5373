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

    // Seguridad: Si no hay usuario o rol, bloquea
    if (!$user || !$user->role) abort(403);

    // Si es administrador, pasa
    if ($user->role->nombre === 'administrador') return $next($request);

    // Carga los permisos y verifica
    // Asegúrate de que la relación sea 'permissions' y el campo 'nombre'
    if (!$user->role->permissions->contains('nombre', $permission)) {
        abort(403);
    }

    return $next($request);
}
}
