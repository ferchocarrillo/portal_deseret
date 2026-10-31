<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        // VALIDACIÓN DE USUARIO ACTIVO: Si está inactivo, se le deniega el acceso de inmediato
        if ($user && ($user->activo == 0 || $user->activo === false)) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Tu cuenta ha sido desactivada por los administradores.',
            ]);
        }

        $request->session()->regenerate();

        // Guardar la conexión anterior como última visita
        if ($user->ultima_conexion) {
            $user->ultima_visita = $user->ultima_conexion;
        }

        // Registrar la conexión actual
        $user->ultima_conexion = now();

        // Incrementar número de accesos
        $user->numero_accesos = ($user->numero_accesos ?? 0) + 1;

        $user->save();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
