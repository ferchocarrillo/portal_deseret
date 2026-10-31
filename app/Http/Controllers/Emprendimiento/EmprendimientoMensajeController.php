<?php

namespace App\Http\Controllers\Emprendimiento;

use App\Http\Controllers\Controller;
use App\Models\EmprendimientoConversacion;
use App\Models\EmprendimientoMensaje;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmprendimientoMensajeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ENVIAR MENSAJE
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        EmprendimientoConversacion $conversacion
    ): RedirectResponse {
        $this->verificarParticipacion($conversacion);

        /*
        |--------------------------------------------------------------------------
        | Validación
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'mensaje' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Crear mensaje
        |--------------------------------------------------------------------------
        */

        $conversacion->mensajes()->create([
            'user_id' => Auth::id(),
            'mensaje' => trim($validated['mensaje']),
        ]);

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | MARCAR MENSAJES COMO LEÍDOS
    |--------------------------------------------------------------------------
    */

    public function marcarLeidos(
        EmprendimientoConversacion $conversacion
    ): RedirectResponse {
        $this->verificarParticipacion($conversacion);

        $conversacion->mensajes()
            ->whereNull('leido_at')
            ->where('user_id', '!=', Auth::id())
            ->update([
                'leido_at' => now(),
            ]);

        return back();
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR MENSAJE
    |--------------------------------------------------------------------------
    */

    public function destroy(
        EmprendimientoMensaje $mensaje
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Solo el propietario puede eliminar su mensaje
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $mensaje->user_id === Auth::id(),
            403
        );

        $mensaje->delete();

        return back()->with(
            'success',
            'Mensaje eliminado correctamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFICAR PARTICIPACIÓN
    |--------------------------------------------------------------------------
    */

    private function verificarParticipacion(
        EmprendimientoConversacion $conversacion
    ): void {
        $usuarioActual = Auth::id();

        abort_unless(
            $conversacion->usuario_iniciador_id === $usuarioActual ||
                $conversacion->usuario_receptor_id === $usuarioActual,
            403
        );
    }
}
