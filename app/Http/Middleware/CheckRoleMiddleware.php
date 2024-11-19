<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user() || !$request->user()->hasRole($role)) {
            return response()->json(['message' => 'Acceso denegado: no tienes el rol necesario.'], 403);
        }

        return $next($request);
    }
}
