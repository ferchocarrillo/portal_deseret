<?php

namespace App\Http\Controllers\Emprendimiento;

use App\Http\Controllers\Controller;
use App\Models\Emprendimiento;
use App\Models\EmprendimientoFavorito;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EmprendimientoFavoritoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | AGREGAR A FAVORITOS
    |--------------------------------------------------------------------------
    */

    public function store(
        Emprendimiento $emprendimiento
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Verificar que el emprendimiento esté activo
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $emprendimiento->activo,
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Verificar si ya está en favoritos
        |--------------------------------------------------------------------------
        */

        $existe = EmprendimientoFavorito::query()
            ->where('user_id', Auth::id())
            ->where(
                'emprendimiento_id',
                $emprendimiento->id
            )
            ->exists();

        if ($existe) {
            return back()->with(
                'info',
                'Este emprendimiento ya está en tus favoritos.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Crear favorito
        |--------------------------------------------------------------------------
        */

        EmprendimientoFavorito::create([
            'user_id' => Auth::id(),
            'emprendimiento_id' => $emprendimiento->id,
        ]);

        return back()->with(
            'success',
            'Emprendimiento agregado a favoritos.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | QUITAR DE FAVORITOS
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Emprendimiento $emprendimiento
    ): RedirectResponse {
        EmprendimientoFavorito::query()
            ->where('user_id', Auth::id())
            ->where(
                'emprendimiento_id',
                $emprendimiento->id
            )
            ->delete();

        return back()->with(
            'success',
            'Emprendimiento eliminado de favoritos.'
        );
    }
}
