<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isStaff()) {
            return $next($request);
        }

        if ($request->wantsJson()) {
            return response()->json(['error' => 'No autorizado. Se requieren permisos de personal (cocina/administración).'], 403);
        }

        return redirect()->route('public.menu')->with('error', 'No tienes permisos de personal para acceder a esta sección.');
    }
}
