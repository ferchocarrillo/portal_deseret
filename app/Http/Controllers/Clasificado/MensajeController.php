<?php

namespace App\Http\Controllers\Clasificado;

use App\Http\Controllers\Controller;
use App\Models\ClasificadoMensaje;
use App\Models\ClasificadoConversacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MensajeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ENVIAR MENSAJE
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        ClasificadoConversacion $conversacion
    ) {
        $userId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Verificar que el usuario pertenezca a la conversación
        |--------------------------------------------------------------------------
        */

        if (
            $conversacion->comprador_id !== $userId &&
            $conversacion->vendedor_id !== $userId
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Validar mensaje
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'mensaje' => [
                'required',
                'string',
                'min:1',
                'max:5000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Verificar que el clasificado siga existiendo
        |--------------------------------------------------------------------------
        */

        $clasificado = $conversacion->clasificado;

        if (!$clasificado) {
            return back()->with(
                'error',
                'El clasificado asociado a esta conversación ya no existe.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar estado del clasificado
        |--------------------------------------------------------------------------
        */

        if (!in_array($clasificado->estado, [
            'publicado',
        ])) {
            return back()->with(
                'error',
                'Esta publicación ya no está disponible para enviar mensajes.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Crear mensaje
        |--------------------------------------------------------------------------
        */

        ClasificadoMensaje::create([
            'conversacion_id' => $conversacion->id,
            'user_id' => $userId,
            'mensaje' => trim($validated['mensaje']),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Actualizar fecha del último mensaje
        |--------------------------------------------------------------------------
        */

        $conversacion->update([
            'ultimo_mensaje_at' => now(),
        ]);

        return back()->with(
            'success',
            'Mensaje enviado correctamente.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MARCAR MENSAJES COMO LEÍDOS
    |--------------------------------------------------------------------------
    */

    public function marcarLeidos(
        ClasificadoConversacion $conversacion
    ) {
        $userId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Verificar participante
        |--------------------------------------------------------------------------
        */

        if (
            $conversacion->comprador_id !== $userId &&
            $conversacion->vendedor_id !== $userId
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Marcar como leídos los mensajes del otro usuario
        |--------------------------------------------------------------------------
        */

        $conversacion->mensajes()
            ->where('user_id', '!=', $userId)
            ->whereNull('leido_at')
            ->update([
                'leido_at' => now(),
            ]);

        return response()->json([
            'success' => true,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR MENSAJE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        ClasificadoMensaje $mensaje
    ) {
        $userId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Solo el autor puede editar
        |--------------------------------------------------------------------------
        */

        if ($mensaje->user_id !== $userId) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Solo se pueden editar mensajes que aún no han sido leídos
        |--------------------------------------------------------------------------
        */

        if ($mensaje->leido_at !== null) {
            return back()->with(
                'error',
                'Este mensaje ya fue leído y no puede ser editado.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validar contenido
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'mensaje' => [
                'required',
                'string',
                'min:1',
                'max:5000',
            ],
        ]);

        $nuevoMensaje = trim($validated['mensaje']);

        /*
        |--------------------------------------------------------------------------
        | Evitar actualización innecesaria
        |--------------------------------------------------------------------------
        */

        if ($mensaje->mensaje === $nuevoMensaje) {
            return back();
        }

        /*
        |--------------------------------------------------------------------------
        | Actualizar mensaje
        |--------------------------------------------------------------------------
        */

        $mensaje->update([
            'mensaje' => $nuevoMensaje,
            'editado_at' => now(),
        ]);

        return back()->with(
            'success',
            'Mensaje actualizado correctamente.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR MENSAJE
    |--------------------------------------------------------------------------
    */

    public function destroy(
        ClasificadoMensaje $mensaje
    ) {
        $userId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Solo el autor puede eliminar
        |--------------------------------------------------------------------------
        */

        if ($mensaje->user_id !== $userId) {
            abort(403);
        }

        $mensaje->delete();

        return back()->with(
            'success',
            'Mensaje eliminado correctamente.'
        );
    }
}
