<?php

namespace App\Http\Controllers\Evento;

use App\Http\Controllers\Controller;
use App\Models\EventoConversacion;
use App\Models\EventoMensaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventoMensajeController extends Controller
{
    /**
     * Enviar mensaje dentro de la conversación.
     */
    public function store(Request $request, EventoConversacion $conversacion)
    {
        $userId = Auth::id();

        if ($conversacion->comprador_id !== $userId && $conversacion->organizador_id !== $userId) {
            abort(403);
        }

        $validated = $request->validate([
            'cuerpo' => 'required|string|max:2000',
        ]);

        EventoMensaje::create([
            'evento_conversacion_id' => $conversacion->id,
            'user_id'                => $userId,
            'cuerpo'                 => $validated['cuerpo'],
        ]);

        // 🔥 ESTA LÍNEA ES CLAVE: Actualiza la fecha de la conversación para que suba en la bandeja
        $conversacion->touch();

        return back();
    }


    public function marcarLeidos(Request $request, EventoConversacion $conversacion)
    {
        $userId = Auth::id();

        if ($conversacion->comprador_id !== $userId && $conversacion->organizador_id !== $userId) {
            abort(403);
        }

        $conversacion->mensajes()
            ->where('user_id', '!=', $userId)
            ->whereNull('leido_at')
            ->update(['leido_at' => now()]);

        return back();
    }


    /**
     * Eliminar mensaje propio.
     */
    public function destroy(EventoMensaje $mensaje)
    {
        if ($mensaje->user_id !== Auth::id()) {
            abort(403);
        }

        $mensaje->delete();

        return back();
    }
}
