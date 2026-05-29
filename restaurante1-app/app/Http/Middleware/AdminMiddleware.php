<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request);
        }

        // Si es una petición de Inertia o JSON, responder con error
        if ($request->wantsJson()) {
            return response()->json(['error' => 'No autorizado. Se requieren permisos de administrador.'], 403);
        }

        // Redirigir al menú público con mensaje de error
        return redirect()->route('public.menu')->with('error', 'No tienes permisos de administrador para acceder a esta sección.');
    }
}
