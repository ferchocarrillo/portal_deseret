<?php

namespace App\Http\Controllers\Servicio;

use App\Http\Controllers\Controller;
use App\Models\ServicioConversacion;
use App\Models\ServicioMensaje;
use Illuminate\Http\Request;

class ServicioMensajeController extends Controller
{
    /**
     * Enviar un mensaje.
     */
    public function store(
        Request $request,
        ServicioConversacion $conversacion
    ) {
        $usuario = $request->user();

        $this->autorizarParticipante(
            $conversacion,
            $usuario->id
        );

        $datos = $request->validate([
            'mensaje' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        $mensaje = ServicioMensaje::create([
            'conversacion_id' => $conversacion->id,
            'user_id' => $usuario->id,
            'mensaje' => trim($datos['mensaje']),
        ]);

        $conversacion->update([
            'ultimo_mensaje_at' => $mensaje->created_at,
        ]);

        return back();
    }

    /**
     * Marcar mensajes como leídos.
     */
    public function marcarLeidos(
        Request $request,
        ServicioConversacion $conversacion
    ) {
        $usuario = $request->user();

        $this->autorizarParticipante(
            $conversacion,
            $usuario->id
        );

        ServicioMensaje::query()
            ->where('conversacion_id', $conversacion->id)
            ->where('user_id', '!=', $usuario->id)
            ->whereNull('leido_at')
            ->update([
                'leido_at' => now(),
            ]);

        return back();
    }

    /**
     * Editar un mensaje propio.
     */
    public function update(
        Request $request,
        ServicioConversacion $conversacion,
        ServicioMensaje $mensaje
    ) {
        $usuario = $request->user();

        $this->autorizarParticipante(
            $conversacion,
            $usuario->id
        );

        abort_unless(
            $mensaje->conversacion_id === $conversacion->id,
            404
        );

        abort_unless(
            $mensaje->user_id === $usuario->id,
            403
        );

        $datos = $request->validate([
            'mensaje' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);

        $mensaje->update([
            'mensaje' => trim($datos['mensaje']),
            'editado_at' => now(),
        ]);

        return back();
    }

    /**
     * Eliminar un mensaje propio.
     */
    public function destroy(
        Request $request,
        ServicioConversacion $conversacion,
        ServicioMensaje $mensaje
    ) {
        $usuario = $request->user();

        $this->autorizarParticipante(
            $conversacion,
            $usuario->id
        );

        abort_unless(
            $mensaje->conversacion_id === $conversacion->id,
            404
        );

        abort_unless(
            $mensaje->user_id === $usuario->id,
            403
        );

        $mensaje->delete();

        return back();
    }

    /**
     * Verificar que el usuario pertenezca a la conversación.
     */
    private function autorizarParticipante(
        ServicioConversacion $conversacion,
        int $usuarioId
    ): void {
        abort_unless(
            $conversacion->usuario_interesado_id === $usuarioId ||
                $conversacion->propietario_id === $usuarioId,
            403
        );
    }
}
