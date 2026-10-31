<?php

namespace App\Http\Controllers\Emprendimiento;

use App\Http\Controllers\Controller;
use App\Models\Emprendimiento;
use App\Models\EmprendimientoConversacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EmprendimientoConversacionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INICIAR / OBTENER CONVERSACIÓN
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

        $usuarioActual = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | El propietario no puede contactarse a sí mismo
        |--------------------------------------------------------------------------
        */

        if ($emprendimiento->user_id === $usuarioActual) {
            return back()->with(
                'error',
                'No puedes iniciar una conversación con tu propio emprendimiento.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Buscar conversación existente
        |--------------------------------------------------------------------------
        |
        | La conversación debe ser única entre los dos usuarios
        | para este emprendimiento, independientemente de quién
        | la haya iniciado.
        |
        */

        $conversacion = EmprendimientoConversacion::query()
            ->where('emprendimiento_id', $emprendimiento->id)
            ->where(function ($query) use (
                $usuarioActual,
                $emprendimiento
            ) {
                $query
                    ->where(function ($q) use (
                        $usuarioActual,
                        $emprendimiento
                    ) {
                        $q->where(
                            'usuario_iniciador_id',
                            $usuarioActual
                        )->where(
                            'usuario_receptor_id',
                            $emprendimiento->user_id
                        );
                    })
                    ->orWhere(function ($q) use (
                        $usuarioActual,
                        $emprendimiento
                    ) {
                        $q->where(
                            'usuario_iniciador_id',
                            $emprendimiento->user_id
                        )->where(
                            'usuario_receptor_id',
                            $usuarioActual
                        );
                    });
            })
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Crear conversación si no existe
        |--------------------------------------------------------------------------
        */

        if (!$conversacion) {
            $conversacion = EmprendimientoConversacion::create([
                'emprendimiento_id' => $emprendimiento->id,
                'usuario_iniciador_id' => $usuarioActual,
                'usuario_receptor_id' => $emprendimiento->user_id,
            ]);
        }

        return redirect()->route(
            'emprendimientos.mensajes.show',
            $conversacion
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MOSTRAR CONVERSACIÓN
    |--------------------------------------------------------------------------
    */

    public function show(
        EmprendimientoConversacion $conversacion
    ): Response {
        $usuarioActual = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Verificar participación
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $conversacion->usuario_iniciador_id === $usuarioActual ||
                $conversacion->usuario_receptor_id === $usuarioActual,
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Cargar conversación y relaciones
        |--------------------------------------------------------------------------
        */

        $conversacion->load([
            'emprendimiento',
            'usuarioIniciador',
            'usuarioReceptor',
            'mensajes' => function ($query) {
                $query
                    ->with('usuario')
                    ->orderBy('created_at');
            },
        ]);

        /*
        |--------------------------------------------------------------------------
        | Marcar como leídos los mensajes recibidos
        |--------------------------------------------------------------------------
        */

        $conversacion->mensajes()
            ->whereNull('leido_at')
            ->where('user_id', '!=', $usuarioActual)
            ->update([
                'leido_at' => now(),
            ]);

        /*
        |--------------------------------------------------------------------------
        | Vista
        |--------------------------------------------------------------------------
        */

        return Inertia::render(
            'Emprendimientos/Mensajes',
            [
                'conversacion' => $conversacion,
                'mensajes' => $conversacion->mensajes,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR MIS CONVERSACIONES
    |--------------------------------------------------------------------------
    */

    public function index(): Response
    {
        $usuarioActual = Auth::id();

        $conversaciones = EmprendimientoConversacion::query()
            ->with([
                'emprendimiento',
                'usuarioIniciador',
                'usuarioReceptor',
                'mensajes' => function ($query) {
                    $query
                        ->latest()
                        ->limit(1);
                },
            ])
            ->where(function ($query) use ($usuarioActual) {
                $query
                    ->where(
                        'usuario_iniciador_id',
                        $usuarioActual
                    )
                    ->orWhere(
                        'usuario_receptor_id',
                        $usuarioActual
                    );
            })
            ->latest()
            ->get();

        return Inertia::render(
            'Emprendimientos/Conversaciones',
            [
                'conversaciones' => $conversaciones,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR CONVERSACIÓN
    |--------------------------------------------------------------------------
    */

    public function destroy(
        EmprendimientoConversacion $conversacion
    ): RedirectResponse {
        $usuarioActual = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Verificar participación
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $conversacion->usuario_iniciador_id === $usuarioActual ||
                $conversacion->usuario_receptor_id === $usuarioActual,
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Eliminar conversación
        |--------------------------------------------------------------------------
        |
        | Los mensajes se eliminan automáticamente debido al
        | cascadeOnDelete() de la migración.
        |
        */

        $conversacion->delete();

        return redirect()
            ->route('emprendimientos.conversaciones')
            ->with(
                'success',
                'Conversación eliminada correctamente.'
            );
    }
}
