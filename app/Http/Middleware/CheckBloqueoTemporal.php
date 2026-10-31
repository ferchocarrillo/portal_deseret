<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBloqueoTemporal
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Los administradores pasan sin restricciones
        if ($user && method_exists($user, 'esAdministrador') && $user->esAdministrador()) {
            return $next($request);
        }

        // Verificar si tiene bloqueo temporal vigente
        if ($user && $user->bloqueado_hasta) {
            if (\Carbon\Carbon::parse($user->bloqueado_hasta)->isFuture()) {
                $diasRestantes = now()->diffInDays(\Carbon\Carbon::parse($user->bloqueado_hasta)) + 1;
                $mensaje = "Tu cuenta tiene un bloqueo temporal para crear publicaciones vigente por {$diasRestantes} día(s) más.";

                // Si es una petición POST (al enviar datos), devolvemos error
                if ($request->isMethod('post')) {
                    return back()->withErrors(['general' => $mensaje]);
                }

                // Si intentan entrar por GET (abrir la vista), los sacamos al dashboard con una alerta
                return redirect()->route('dashboard')->with('error', $mensaje);
            }
        }

        return $next($request);
    }
}
