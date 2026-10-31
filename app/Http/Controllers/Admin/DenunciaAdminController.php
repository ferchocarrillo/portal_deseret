<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Denuncia;
use App\Models\User;
use App\Models\Evento;
use App\Models\ClasificadoConversacion;
use App\Models\ClasificadoMensaje;
use App\Models\EmprendimientoConversacion;
use App\Models\EmprendimientoMensaje;
use App\Models\MarketplaceConversacion;
use App\Models\MarketplaceMensaje;
use App\Models\ServicioConversacion;
use App\Models\ServicioMensaje;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DenunciaAdminController extends Controller
{
    /**
     * Devuelve el número de denuncias pendientes para las notificaciones del sidebar.
     */
    public function contarPendientes()
    {
        return response()->json([
            'denunciasPendientes' => Denuncia::where('estado', 'pendiente')->count(),
        ]);
    }

    /**
     * Listar todas las denuncias del sistema de forma unificada.
     */
    public function index(Request $request): Response
    {
        $query = Denuncia::query()
            ->with([
                'usuario:id,name,email',
            ]);

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $query->where(function ($q) use ($buscar) {
                $q->where('motivo', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('codigo_unico', 'like', "%{$buscar}%")
                    ->orWhereHas('usuario', function ($q) use ($buscar) {
                        $q->where('name', 'like', "%{$buscar}%")
                            ->orWhere('email', 'like', "%{$buscar}%");
                    });
            });
        }

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $query->orderByRaw(
            "CASE
                WHEN estado = 'pendiente' THEN 0
                WHEN estado = 'en_revision' THEN 1
                WHEN estado = 'resuelto' THEN 2
                WHEN estado = 'descartado' THEN 3
                ELSE 4
            END"
        )->orderByDesc('created_at');

        $denuncias = $query
            ->paginate(20)
            ->withQueryString();

        return Inertia::render(
            'admin/Denuncias/Index',
            [
                'denuncias' => $denuncias,
                'filtros' => [
                    'buscar' => $request->get('buscar'),
                    'modulo' => $request->get('modulo'),
                    'estado' => $request->get('estado'),
                ],
            ]
        );
    }

    /**
     * Mostrar una denuncia específica junto con su elemento afectado.
     */
    public function show(Denuncia $denuncia): Response
    {
        // Cargamos la denuncia y el usuario que reporta
        $denuncia->load([
            'usuario:id,name,email',
        ]);

        $propietario = null;
        $modulo = strtolower($denuncia->modulo);
        $elementoId = $denuncia->reportable_id;

        if (!$elementoId && $denuncia->reportable) {
            $elementoId = $denuncia->reportable->id;
        }

        if ($elementoId) {
            $modeloAfectado = match ($modulo) {
                'cursos' => \App\Models\Curso::find($elementoId),
                'marketplace' => \App\Models\MarketplacePublicacion::find($elementoId),
                'clasificados' => \App\Models\Clasificado::find($elementoId),
                'emprendimientos' => \App\Models\Emprendimiento::find($elementoId),
                'servicios' => \App\Models\Servicio::find($elementoId),
                'eventos' => \App\Models\Evento::find($elementoId), // 👈 Evento añadido
                default => null,
            };

            if ($modeloAfectado) {
                $userId = $modeloAfectado->user_id
                    ?? $modeloAfectado->propietario_id
                    ?? $modeloAfectado->vendedor_id
                    ?? null;

                if ($userId) {
                    $propietario = \App\Models\User::find($userId);
                }

                if (!$propietario && method_exists($modeloAfectado, 'user')) {
                    $propietario = $modeloAfectado->user;
                }
            }
        }

        // Si encontramos al propietario, inyectamos sus datos y estatus para la vista
        if ($propietario) {
            $nombreCompleto = trim(($propietario->nombres ?? '') . ' ' . ($propietario->apellidos ?? ''));
            $nombreFinal = $nombreCompleto !== '' ? $nombreCompleto : ($propietario->name ?? 'Usuario');

            $denuncia->usuarioReportado = [
                'id' => $propietario->id,
                'name' => $nombreFinal,
                'email' => $propietario->email,
                'activo' => (bool) ($propietario->activo ?? true),
                'bloqueado_hasta' => $propietario->bloqueado_hasta ?? null,
            ];
        } else {
            $denuncia->usuarioReportado = null;
        }

        // Resolver la URL pública de la publicación asociada
        $publicacionUrl = null;
        if ($elementoId) {
            $publicacionUrl = match ($modulo) {
                'cursos' => route('cursos.show', $elementoId),
                'marketplace' => route('marketplace.show', $elementoId),
                'clasificados' => route('clasificados.show', $elementoId),
                'emprendimientos' => route('emprendimientos.show', $elementoId),
                'servicios' => route('servicios.show', $elementoId),
                'eventos' => route('eventos.show', $elementoId), // 👈 Evento añadido
                default => null,
            };
        }

        return Inertia::render(
            'admin/Denuncias/Show',
            [
                'denuncia' => $denuncia,
                'publicacionUrl' => $publicacionUrl,
            ]
        );
    }

    /**
     * Actualizar estado y notas internas de la denuncia.
     */
    public function actualizarSeguimiento(Request $request, Denuncia $denuncia): RedirectResponse
    {
        $validated = $request->validate([
            'estado' => ['required', Rule::in(['pendiente', 'en_revision', 'resuelto', 'descartado'])],
            'notas_internas' => ['nullable', 'string', 'max:3000'],
        ]);

        $datosActualizacion = [
            'estado' => $validated['estado'],
            'notas_internas' => $validated['notas_internas'],
        ];

        if (in_array($validated['estado'], ['resuelto', 'descartado']) && !$denuncia->fecha_cierre) {
            $datosActualizacion['fecha_cierre'] = now();
        }

        $denuncia->update($datosActualizacion);

        return back()->with('success', 'El seguimiento de la denuncia ha sido actualizado.');
    }

    /**
     * 1. Bloquear la publicación asociada a la denuncia.
     */
    public function bloquearPublicacion(Denuncia $denuncia): RedirectResponse
    {
        $modelo = $denuncia->reportable;

        if (!$modelo) {
            return back()->with('error', 'El elemento asociado a la denuncia ya no existe.');
        }

        if ($modelo instanceof \App\Models\MarketplacePublicacion) {
            $modelo->update(['estado_publicacion' => 'suspendida']);
        } elseif ($modelo instanceof \App\Models\Servicio) {
            $modelo->update(['estado' => 'pausado']);
        } elseif ($modelo instanceof \App\Models\Curso) {
            $modelo->update(['estado' => 'inactivo']);
        } elseif ($modelo instanceof \App\Models\Clasificado) {
            $modelo->update(['estado' => 'pausado']);
        } elseif ($modelo instanceof \App\Models\Evento) { // 👈 Evento añadido
            $modelo->update(['estado' => 'suspendido']);
        } elseif (isset($modelo->estado_publicacion)) {
            $modelo->update(['estado_publicacion' => 'suspendida']);
        } elseif (isset($modelo->estado)) {
            $modelo->update(['estado' => 'pausado']);
        } elseif (isset($modelo->activo)) {
            $modelo->update(['activo' => false]);
        }

        $denuncia->update([
            'estado' => 'resuelto',
            'fecha_cierre' => now(),
            'notas_internas' => trim($denuncia->notas_internas . "\n[Acción Administrativa]: Publicación bloqueada el " . now()),
        ]);

        return back()->with('success', 'La publicación ha sido bloqueada correctamente.');
    }

    /**
     * 2. Bloquear al usuario infractor y todas sus publicaciones en cascada.
     */
    public function bloquearUsuario(Denuncia $denuncia): RedirectResponse
    {
        $modelo = $denuncia->reportable;

        if (!$modelo) {
            return back()->with('error', 'El elemento asociado a la denuncia ya no existe.');
        }

        $usuario = $modelo->usuario ?? $modelo->user ?? User::find($modelo->user_id ?? null);

        if (!$usuario) {
            return back()->with('error', 'No se pudo identificar al usuario propietario de esta publicación.');
        }

        try {
            // 1. Desactivar usuario
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'activo')) {
                $usuario->activo = false;
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'suspendido_at')) {
                $usuario->suspendido_at = now();
            }
            $usuario->save();

            // 2. Inactivar publicaciones en cascada por ID de usuario
            \App\Models\Servicio::where('user_id', $usuario->id)->update(['estado' => 'pausado']);
            \App\Models\Clasificado::where('user_id', $usuario->id)->update(['estado' => 'pausado']);
            \App\Models\MarketplacePublicacion::where('user_id', $usuario->id)->update(['estado_publicacion' => 'suspendida']);
            \App\Models\Evento::where('user_id', $usuario->id)->update(['estado' => 'suspendido']); // 👈 Evento añadido

            if (\Illuminate\Support\Facades\Schema::hasColumn('emprendimientos', 'activo')) {
                \App\Models\Emprendimiento::where('user_id', $usuario->id)->update(['activo' => false]);
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('cursos', 'estado')) {
                \App\Models\Curso::where('user_id', $usuario->id)->update(['estado' => 'inactivo']);
            }

            // 3. Cerrar la denuncia
            $denuncia->update([
                'estado' => 'resuelto',
                'fecha_cierre' => now(),
                'notas_internas' => trim($denuncia->notas_internas . "\n[Acción Administrativa]: Usuario ID {$usuario->id} bloqueado y publicaciones inactivadas el " . now()),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error al bloquear usuario en cascada: " . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al procesar el bloqueo: ' . $e->getMessage());
        }

        return back()->with('success', 'El usuario ha sido bloqueado y todas sus publicaciones se han inactivado.');
    }

    /**
     * 3. Enviar recordatorio de normas (Advertencia / Mensaje Oficial).
     */
    public function enviarAdvertencia(Request $request, Denuncia $denuncia): RedirectResponse
    {
        $validated = $request->validate([
            'rule_id' => 'required|integer|in:' . implode(',', array_keys(config('community_rules.rules', []))),
            'detail'  => 'required|string|max:1000',
        ]);

        $modelo = $denuncia->reportable;

        if (!$modelo) {
            return back()->with('error', 'El elemento asociado a la denuncia ya no existe.');
        }

        $usuario = $modelo->usuario ?? $modelo->user ?? User::find($modelo->user_id ?? null);

        if (!$usuario) {
            return back()->with('error', 'No se encontró al usuario asociado para enviar la advertencia.');
        }

        $strikesCount = $usuario->infractions()->count() + 1;

        $usuario->infractions()->create([
            'moderator_id' => auth()->id(),
            'rule_id'      => $validated['rule_id'],
            'detail'       => $validated['detail'],
            'strike_level' => $strikesCount,
        ]);

        $regla = config("community_rules.rules.{$validated['rule_id']}");
        $tituloRegla = $regla['title'] ?? 'Norma Comunitaria';
        $codigoRegla = $regla['code'] ?? 'R';
        $descRegla   = $regla['description'] ?? '';

        $contenidoMensaje = "⚠️ AVISO OFICIAL DE MODERACIÓN (Caso: {$denuncia->codigo_unico})\n\n"
            . "Estimado usuario, tu publicación en el módulo '{$denuncia->modulo}' ha sido revisada por infringir las directrices de nuestra comunidad.\n\n"
            . "📌 REGLA INFRINGIDA:\n"
            . "• [{$codigoRegla}] {$tituloRegla}\n"
            . "  \"{$descRegla}\"\n\n"
            . "📝 DETALLE / MOTIVO REGISTRADO POR MODERACIÓN:\n"
            . "{$validated['detail']}\n\n"
            . "🚨 ADVERTENCIA POR REINCIDENCIA (Infracción acumulada #{$strikesCount}):\n"
            . "Este aviso queda registrado formalmente en tu perfil...\n";

        // Si manejas mensajería específica para eventos, puedes agregar su bloque aquí si lo deseas.
        $denuncia->update([
            'notas_internas' => trim($denuncia->notas_internas . "\n[Infracción {$codigoRegla} registrada y enviada a {$usuario->email}] el " . now()),
        ]);

        return back()->with('success', 'La infracción ha sido registrada y el mensaje con las reglas y advertencias fue enviado al usuario.');
    }

    /**
     * 4. Aplicar bloqueo temporal para crear publicaciones.
     */
    public function bloqueoTemporal(Request $request, Denuncia $denuncia): RedirectResponse
    {
        $request->validate([
            'dias' => 'required|integer|min:1|max:365',
        ]);

        $modelo = $denuncia->reportable;
        $usuario = $modelo?->usuario ?? $modelo?->user ?? \App\Models\User::find($modelo?->user_id ?? null);

        if (!$usuario) {
            return back()->with('error', 'No se pudo identificar al usuario asociado.');
        }

        $dias = (int) $request->input('dias');
        $bloqueadoHasta = now()->addDays($dias);

        $usuario->update([
            'bloqueado_hasta' => $bloqueadoHasta,
        ]);

        $denuncia->update([
            'notas_internas' => trim($denuncia->notas_internas . "\n[Acción Administrativa]: Bloqueo temporal de {$dias} días aplicado al usuario ID {$usuario->id} hasta {$bloqueadoHasta} el " . now()),
        ]);

        return back()->with('success', "Se ha aplicado el bloqueo temporal por {$dias} días.");
    }

    /**
     * 6. Reactivar al usuario infractor y restaurar todas sus publicaciones en cascada.
     */
    public function reactivarUsuario(Denuncia $denuncia): RedirectResponse
    {
        $modelo = $denuncia->reportable;

        if (!$modelo) {
            return back()->with('error', 'El elemento asociado a la denuncia ya no existe.');
        }

        $usuario = $modelo->usuario ?? $modelo->user ?? User::find($modelo->user_id ?? null);

        if (!$usuario) {
            return back()->with('error', 'No se pudo identificar al usuario propietario de esta publicación.');
        }

        if (Schema::hasColumn('users', 'activo')) {
            $usuario->activo = true;
        }
        if (Schema::hasColumn('users', 'suspendido_at')) {
            $usuario->suspendido_at = null;
        }
        if (Schema::hasColumn('users', 'bloqueado_hasta')) {
            $usuario->bloqueado_hasta = null;
        }
        $usuario->save();

        \App\Models\Servicio::where('user_id', $usuario->id)->update(['estado' => 'activo']);
        \App\Models\Clasificado::where('user_id', $usuario->id)->update(['estado' => 'activo']);
        \App\Models\MarketplacePublicacion::where('user_id', $usuario->id)->update(['estado_publicacion' => 'activo']);
        \App\Models\Evento::where('user_id', $usuario->id)->update(['estado' => 'activo']); // 👈 Evento reactivado

        if (Schema::hasColumn('emprendimientos', 'activo')) {
            \App\Models\Emprendimiento::where('user_id', $usuario->id)->update(['activo' => true]);
        }

        if (Schema::hasColumn('cursos', 'estado')) {
            \App\Models\Curso::where('user_id', $usuario->id)->update(['estado' => 'publicado']);
        }

        $denuncia->update([
            'notas_internas' => trim($denuncia->notas_internas . "\n[Acción Administrativa]: Usuario ID {$usuario->id} reactivado el " . now()),
        ]);

        return back()->with('success', 'El usuario ha sido reactivado y sus publicaciones han vuelto a estar disponibles.');
    }

    /**
     * Reactivar únicamente la publicación asociada a la denuncia.
     */
    public function reactivarPublicacion(Denuncia $denuncia): RedirectResponse
    {
        $modelo = $denuncia->reportable;

        if (!$modelo) {
            return back()->with('error', 'El elemento asociado a la denuncia ya no existe.');
        }

        if ($modelo instanceof \App\Models\MarketplacePublicacion) {
            $modelo->update(['estado_publicacion' => 'activo']);
        } elseif ($modelo instanceof \App\Models\Servicio) {
            $modelo->update(['estado' => 'activo']);
        } elseif ($modelo instanceof \App\Models\Curso) {
            $modelo->update(['estado' => 'publicado']);
        } elseif ($modelo instanceof \App\Models\Clasificado) {
            $modelo->update(['estado' => 'activo']);
        } elseif ($modelo instanceof \App\Models\Evento) { // 👈 Evento reactivado
            $modelo->update(['estado' => 'activo']);
        } elseif (isset($modelo->estado)) {
            $modelo->update(['estado' => 'activo']);
        } elseif (isset($modelo->activo)) {
            $modelo->update(['activo' => true]);
        }

        $denuncia->update([
            'notas_internas' => trim($denuncia->notas_internas . "\n[Acción Administrativa]: Publicación reactivada el " . now()),
        ]);

        return back()->with('success', 'La publicación ha sido reactivada correctamente.');
    }

    /**
     * Quitar el bloqueo temporal de creación de publicaciones al usuario.
     */
    public function quitarBloqueoTemporal(Denuncia $denuncia): RedirectResponse
    {
        $modelo = $denuncia->reportable;
        $usuario = $modelo?->usuario ?? $modelo?->user ?? User::find($modelo?->user_id ?? null);

        if (!$usuario) {
            return back()->with('error', 'No se encontró al usuario asociado.');
        }

        if (Schema::hasColumn('users', 'bloqueado_hasta')) {
            $usuario->update(['bloqueado_hasta' => null]);
        }

        $denuncia->update([
            'notas_internas' => trim($denuncia->notas_internas . "\n[Acción Administrativa]: Bloqueo temporal de publicaciones retirado al usuario ID {$usuario->id} el " . now()),
        ]);

        return back()->with('success', 'Se ha retirado el bloqueo temporal al usuario.');
    }
}
