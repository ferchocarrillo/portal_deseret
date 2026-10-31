<?php

namespace App\Http\Controllers\Emprendimiento;

use App\Http\Controllers\Controller;
use App\Models\Emprendimiento;
use App\Models\EmprendimientoDenuncia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmprendimientoDenunciaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REGISTRAR DENUNCIA
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
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
        | Validación
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'motivo' => [
                'required',
                'string',
                'max:255',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Evitar que el propietario denuncie su propio emprendimiento
        |--------------------------------------------------------------------------
        */

        if ($emprendimiento->user_id === Auth::id()) {
            return back()->with(
                'error',
                'No puedes denunciar tu propio emprendimiento.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Evitar denuncias duplicadas
        |--------------------------------------------------------------------------
        */

        $denunciaExistente = EmprendimientoDenuncia::query()
            ->where('emprendimiento_id', $emprendimiento->id)
            ->where('user_id', Auth::id())
            ->where('estado', 'pendiente')
            ->exists();

        if ($denunciaExistente) {
            return back()->with(
                'error',
                'Ya has realizado una denuncia sobre este emprendimiento.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Crear denuncia
        |--------------------------------------------------------------------------
        */

        EmprendimientoDenuncia::create([
            'emprendimiento_id' => $emprendimiento->id,
            'user_id' => Auth::id(),
            'motivo' => $validated['motivo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'estado' => 'pendiente',
        ]);

        return back()->with(
            'success',
            'La denuncia fue registrada correctamente.'
        );
    }
}
