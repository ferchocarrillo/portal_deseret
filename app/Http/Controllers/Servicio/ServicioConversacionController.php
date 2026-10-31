<?php

namespace App\Http\Controllers\Servicio;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use App\Models\ServicioConversacion;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ServicioConversacionController extends Controller
{
    /**
     * Iniciar o abrir una conversación vinculada a un servicio.
     */
    public function store(Request $request, Servicio $servicio): RedirectResponse
    {
        abort_unless($servicio->estado === 'activo', 404);

        abort_if(
            (int) $request->user()->id === (int) $servicio->user_id,
            403,
            'No puedes iniciar una conversación con tu propia publicación.'
        );

        $conversacion = ServicioConversacion::where('servicio_id', $servicio->id)
            ->where('usuario_interesado_id', $request->user()->id)
            ->where('propietario_id', $servicio->user_id)
            ->first();

        if (! $conversacion) {
            $conversacion = new ServicioConversacion();
            $conversacion->servicio_id = $servicio->id;
            $conversacion->usuario_interesado_id = $request->user()->id;
            $conversacion->propietario_id = $servicio->user_id;
            $conversacion->ultimo_mensaje_at = now();
            $conversacion->save();
        }

        return redirect()->route('servicios.conversacion.show', [
            'servicio' => $servicio->id,
            'conversacion' => $conversacion->id,
        ]);
    }

    /**
     * Mostrar la conversación y el historial de mensajes.
     */
    public function show(
        Request $request,
        ServicioConversacion $conversacion
    ): Response {
        $userId = (int) $request->user()->id;

        $esParticipante = (int) $conversacion->usuario_interesado_id === $userId
            || (int) $conversacion->propietario_id === $userId;

        abort_unless($esParticipante, 403, 'No tienes acceso a esta conversación.');

        // Marcar como leídos los mensajes que envió la otra persona
        $conversacion->mensajes()
            ->where('user_id', '!=', $userId)
            ->whereNull('leido_at')
            ->update(['leido_at' => now()]);

        // Cargar relaciones ordenando mensajes de más antiguo a más reciente
        $conversacion->load([
            'servicio.user',
            'interesado',
            'propietario',
            'mensajes' => function ($query) {
                $query->orderBy('created_at', 'asc')->with('user');
            },
        ]);

        return Inertia::render('Servicios/Conversacion', [
            'servicio'     => $conversacion->servicio,
            'conversacion' => $conversacion,
        ]);
    }

    /**
     * Enviar un mensaje dentro de la conversación.
     */
    public function enviarMensaje(
        Request $request,
        Servicio $servicio,
        ServicioConversacion $conversacion
    ): RedirectResponse {
        $userId = (int) $request->user()->id;

        $esParticipante = (int) $conversacion->usuario_interesado_id === $userId
            || (int) $conversacion->propietario_id === $userId;

        abort_unless($esParticipante, 403, 'No puedes enviar mensajes en esta conversación.');

        // Se valida que el mensaje no esté vacío
        $validated = $request->validate([
            'mensaje' => ['required', 'string', 'max:2000'],
        ]);

        $conversacion->mensajes()->create([
            'user_id' => $userId,
            'mensaje' => trim($validated['mensaje']),
        ]);

        $conversacion->update([
            'ultimo_mensaje_at' => now(),
        ]);

        return back();
    }
}
