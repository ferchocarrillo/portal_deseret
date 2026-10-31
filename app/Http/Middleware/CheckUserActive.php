<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Refrescamos desde la base de datos por si el admin lo bloqueó hace un segundo
            $user->refresh();

            // Comparamos si está inactivo (0 o false)
            if ($user->activo == 0 || $user->activo === false) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Tu cuenta ha sido desactivada por los administradores.',
                ]);
            }
        }

        return $next($request);
    }
}
