<?php

namespace App\Http\Controllers;

use App\Models\ClasificadoConversacion;
use App\Models\EmprendimientoConversacion;
use App\Models\MarketplaceConversacion;
use App\Models\ServicioConversacion;
use App\Models\EventoConversacion; // 🔥 Importante: Añadido el modelo de Eventos
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CentroMensajesController extends Controller
{
    /**
     * Centro general de mensajes.
     */
    public function index(): Response
    {
        $usuarioId = (int) Auth::id();

        /*
        |--------------------------------------------------------------------------
        | MARKETPLACE
        |--------------------------------------------------------------------------
        */

        $marketplace = MarketplaceConversacion::query()
            ->with([
                'publicacion:id,user_id,titulo,slug,estado_publicacion',
                'publicacion.imagenes' => function ($query) {
                    $query
                        ->orderByDesc('es_principal')
                        ->orderBy('orden')
                        ->limit(1);
                },
                'comprador:id,name,foto',
                'vendedor:id,name,foto',
                'mensajes' => function ($query) {
                    $query
                        ->latest()
                        ->limit(1);
                },
            ])
            ->withCount([
                'mensajes as mensajes_no_leidos' => function ($query) use ($usuarioId) {
                    $query
                        ->where('user_id', '!=', $usuarioId)
                        ->whereNull('leido_at');
                },
            ])
            ->where(function ($query) use ($usuarioId) {
                $query
                    ->where('comprador_id', $usuarioId)
                    ->orWhere('vendedor_id', $usuarioId);
            })
            ->get();

        $marketplace->each(function ($conversacion) {
            $conversacion->setAttribute(
                'tipo',
                'marketplace'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | CLASIFICADOS
        |--------------------------------------------------------------------------
        */

        $clasificados = ClasificadoConversacion::query()
            ->with([
                'clasificado:id,titulo,slug,estado',
                'clasificado.imagenes' => function ($query) {
                    $query
                        ->select([
                            'id',
                            'clasificado_id',
                            'ruta',
                            'orden',
                        ])
                        ->orderBy('orden')
                        ->limit(1);
                },
                'comprador:id,name,foto',
                'vendedor:id,name,foto',
                'ultimoMensaje',
            ])
            ->withCount([
                'mensajes as mensajes_no_leidos' => function ($query) use ($usuarioId) {
                    $query
                        ->where('user_id', '!=', $usuarioId)
                        ->whereNull('leido_at');
                },
            ])
            ->where(function ($query) use ($usuarioId) {
                $query
                    ->where('comprador_id', $usuarioId)
                    ->orWhere('vendedor_id', $usuarioId);
            })
            ->get();

        $clasificados->each(function ($conversacion) {
            $conversacion->setAttribute(
                'tipo',
                'clasificado'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | EMPRENDIMIENTOS
        |--------------------------------------------------------------------------
        */

        $emprendimientos = EmprendimientoConversacion::query()
            ->with([
                'emprendimiento:id,user_id,nombre,activo',
                'usuarioIniciador:id,name,foto',
                'usuarioReceptor:id,name,foto',
                'mensajes' => function ($query) {
                    $query
                        ->latest()
                        ->limit(1);
                },
            ])
            ->withCount([
                'mensajes as mensajes_no_leidos' => function ($query) use ($usuarioId) {
                    $query
                        ->where('user_id', '!=', $usuarioId)
                        ->whereNull('leido_at');
                },
            ])
            ->where(function ($query) use ($usuarioId) {
                $query
                    ->where(
                        'usuario_iniciador_id',
                        $usuarioId
                    )
                    ->orWhere(
                        'usuario_receptor_id',
                        $usuarioId
                    );
            })
            ->get();

        $emprendimientos->each(function ($conversacion) {
            $conversacion->setAttribute(
                'tipo',
                'emprendimiento'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | SERVICIOS
        |--------------------------------------------------------------------------
        */

        $servicios = ServicioConversacion::query()
            ->with([
                'servicio:id,user_id,titulo,imagen,imagen_tipo,estado',
                'interesado:id,name,foto',
                'propietario:id,name,foto',
                'mensajes' => function ($query) {
                    $query
                        ->latest()
                        ->limit(1);
                },
            ])
            ->withCount([
                'mensajes as mensajes_no_leidos' => function ($query) use ($usuarioId) {
                    $query
                        ->where('user_id', '!=', $usuarioId)
                        ->whereNull('leido_at');
                },
            ])
            ->where(function ($query) use ($usuarioId) {
                $query
                    ->where('usuario_interesado_id', $usuarioId)
                    ->orWhere('propietario_id', $usuarioId);
            })
            ->get();

        $servicios->each(function ($conversacion) {
            $conversacion->setAttribute(
                'tipo',
                'servicio'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | EVENTOS (NUEVO BLOQUE)
        |--------------------------------------------------------------------------
        */

        $eventos = EventoConversacion::query()
            ->with([
                'evento:id,user_id,titulo,estado',
                'comprador:id,name,foto',
                'organizador:id,name,foto',
                'mensajes' => function ($query) {
                    $query->latest()->limit(1);
                },
            ])
            ->withCount([
                'mensajes as mensajes_no_leidos' => function ($query) use ($usuarioId) {
                    $query
                        ->where('user_id', '!=', $usuarioId)
                        ->whereNull('leido_at');
                },
            ])
            ->where(function ($query) use ($usuarioId) {
                $query
                    ->where('comprador_id', $usuarioId)
                    ->orWhere('organizador_id', $usuarioId);
            })
            ->get();

        $eventos->each(function ($conversacion) {
            $conversacion->setAttribute(
                'tipo',
                'evento'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | UNIFICAR
        |--------------------------------------------------------------------------
        */

        $conversaciones = $marketplace
            ->concat($clasificados)
            ->concat($emprendimientos)
            ->concat($servicios)
            ->concat($eventos) // 🔥 Añadido aquí a la unificación
            ->sortByDesc(function ($conversacion) {
                return $conversacion->ultimo_mensaje_at
                    ?? $conversacion->mensajes?->first()?->created_at
                    ?? $conversacion->ultimoMensaje?->created_at
                    ?? $conversacion->created_at;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | VISTA
        |--------------------------------------------------------------------------
        */

        return Inertia::render(
            'Marketplace/Mensajes',
            [
                'conversaciones' => $conversaciones,
            ]
        );
    }
}
