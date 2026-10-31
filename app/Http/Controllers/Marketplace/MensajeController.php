<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceConversacion;
use App\Models\MarketplaceMensaje;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MensajeController extends Controller
{
    /**
     * Enviar un mensaje dentro de una conversación.
     */
    public function store(
        Request $request,
        MarketplaceConversacion $conversacion
    ): RedirectResponse {
        $usuarioId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Verificar que el usuario participe en la conversación.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $conversacion->comprador_id === $usuarioId
                || $conversacion->vendedor_id === $usuarioId,
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Validar mensaje.
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
        | Verificar que la publicación todavía permita comunicación.
        |--------------------------------------------------------------------------
        */

        $publicacion = $conversacion->publicacion;

        if (!$publicacion) {
            return back()->with(
                'error',
                'La publicación asociada a esta conversación ya no existe.'
            );
        }

        if (
            !in_array(
                $publicacion->estado_publicacion,
                ['publicada', 'reservada'],
                true
            )
        ) {
            return back()->with(
                'error',
                'Esta publicación ya no está disponible para recibir mensajes.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Crear mensaje.
        |--------------------------------------------------------------------------
        */

        MarketplaceMensaje::create([
            'conversacion_id' => $conversacion->id,
            'user_id' => $usuarioId,
            'mensaje' => trim($validated['mensaje']),
            'leido_at' => null,
            'editado_at' => null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Actualizar fecha del último mensaje.
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

    /**
     * Marcar como leídos los mensajes recibidos.
     */
    public function marcarLeidos(
        MarketplaceConversacion $conversacion
    ): RedirectResponse {
        $usuarioId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Verificar participación.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $conversacion->comprador_id === $usuarioId
                || $conversacion->vendedor_id === $usuarioId,
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Marcar únicamente mensajes enviados por la otra persona.
        |--------------------------------------------------------------------------
        */

        MarketplaceMensaje::query()
            ->where('conversacion_id', $conversacion->id)
            ->where('user_id', '!=', $usuarioId)
            ->whereNull('leido_at')
            ->update([
                'leido_at' => now(),
            ]);

        return back();
    }

    /**
     * Editar un mensaje propio.
     *
     * Solo se permite mientras el interlocutor NO lo haya leído.
     */
    public function update(
        Request $request,
        MarketplaceMensaje $mensaje
    ): RedirectResponse {
        $usuarioId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Verificar que el mensaje pertenezca al usuario actual.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $mensaje->user_id === $usuarioId,
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Verificar que todavía no haya sido leído.
        |--------------------------------------------------------------------------
        |
        | Esta es la regla principal:
        |
        | leido_at = NULL  -> se puede editar.
        | leido_at != NULL -> NO se puede editar.
        |
        |--------------------------------------------------------------------------
        */

        if ($mensaje->leido_at !== null) {
            return back()->with(
                'error',
                'Este mensaje ya fue leído y no puede editarse.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validar nuevo contenido.
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
        | Evitar guardar una edición idéntica.
        |--------------------------------------------------------------------------
        */

        if ($nuevoMensaje === $mensaje->mensaje) {
            return back()->with(
                'error',
                'El mensaje no presenta cambios.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Actualizar mensaje.
        |--------------------------------------------------------------------------
        */

        $mensaje->update([
            'mensaje' => $nuevoMensaje,
            'editado_at' => now(),
        ]);

        return back()->with(
            'success',
            'Mensaje editado correctamente.'
        );
    }

    /**
     * Eliminar un mensaje.
     *
     * Solo permitimos eliminar mensajes propios.
     */
    public function destroy(
        MarketplaceMensaje $mensaje
    ): RedirectResponse {
        $usuarioId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Verificar que el mensaje pertenezca al usuario.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $mensaje->user_id === $usuarioId,
            403
        );

        $mensaje->delete();

        return back()->with(
            'success',
            'Mensaje eliminado correctamente.'
        );
    }
}
