<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\ClasificadoConversacion;
use App\Models\ClasificadoMensaje;
use App\Models\EmprendimientoConversacion;
use App\Models\EmprendimientoMensaje;
use App\Models\EventoConversacion;
use App\Models\EventoMensaje;
use App\Models\MarketplaceConversacion;
use App\Models\MarketplaceMensaje;
use App\Models\MarketplacePublicacion;
use App\Models\ServicioConversacion;
use App\Models\ServicioMensaje;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ConversacionController extends Controller
{
    /**
     * Listar en una sola bandeja las conversaciones de
     * Marketplace, Clasificados, Emprendimientos, Servicios y Eventos.
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
                'comprador:id,name',
                'vendedor:id,name',
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
            $conversacion->setAttribute('tipo', 'marketplace');
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
                'comprador:id,name',
                'vendedor:id,name',
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
            $conversacion->setAttribute('tipo', 'clasificado');
        });

        /*
        |--------------------------------------------------------------------------
        | EMPRENDIMIENTOS
        |--------------------------------------------------------------------------
        */

        $emprendimientos = EmprendimientoConversacion::query()
            ->with([
                'emprendimiento:id,user_id,nombre,activo',
                'usuarioIniciador:id,name',
                'usuarioReceptor:id,name',
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
                    ->where('usuario_iniciador_id', $usuarioId)
                    ->orWhere('usuario_receptor_id', $usuarioId);
            })
            ->get();

        $emprendimientos->each(function ($conversacion) {
            $conversacion->setAttribute('tipo', 'emprendimiento');
        });

        /*
        |--------------------------------------------------------------------------
        | SERVICIOS
        |--------------------------------------------------------------------------
        */

        $servicios = ServicioConversacion::query()
            ->with([
                'servicio:id,user_id,titulo,imagen,imagen_tipo,estado',
                'interesado:id,name',
                'propietario:id,name',
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
            $conversacion->setAttribute('tipo', 'servicio');
        });

        /*
        |--------------------------------------------------------------------------
        | EVENTOS
        |--------------------------------------------------------------------------
        */
        $eventos = EventoConversacion::query()
            ->with([
                'evento',      // <-- Déjalo solo así
                'comprador',   // <-- Déjalo solo así
                'organizador', // <-- Déjalo solo así
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
        dd('Eventos encontrados:', $eventos->toArray());
        $eventos->each(function ($conversacion) {
            $conversacion->setAttribute('tipo', 'evento');
        });

        /*
        |--------------------------------------------------------------------------
        | UNIFICAR TODAS LAS CONVERSACIONES
        |--------------------------------------------------------------------------
        */

        $conversaciones = $marketplace
            ->concat($clasificados)
            ->concat($emprendimientos)
            ->concat($servicios)
            ->concat($eventos) // 🔥 NUEVO: Concatenar los eventos
            ->sortByDesc(function ($conversacion) {
                return $conversacion->ultimo_mensaje_at
                    ?? $conversacion->mensajes?->first()?->created_at
                    ?? $conversacion->ultimoMensaje?->created_at
                    ?? $conversacion->created_at;
            })
            ->values();

        return Inertia::render('Marketplace/Mensajes', [
            'conversaciones' => $conversaciones,
        ]);
    }

    /**
     * Crear o recuperar una conversación con el vendedor.
     */
    public function store(
        MarketplacePublicacion $publicacion
    ): RedirectResponse {
        $usuarioId = (int) Auth::id();

        if (
            !in_array(
                $publicacion->estado_publicacion,
                ['publicada', 'reservada'],
                true
            )
        ) {
            return back()->with(
                'error',
                'Esta publicación ya no está disponible para contacto.'
            );
        }

        if ((int) $publicacion->user_id === $usuarioId) {
            return back()->with(
                'error',
                'No puedes iniciar una conversación contigo mismo.'
            );
        }

        $conversacion = MarketplaceConversacion::firstOrCreate(
            [
                'publicacion_id' => $publicacion->id,
                'comprador_id' => $usuarioId,
                'vendedor_id' => (int) $publicacion->user_id,
            ],
            [
                'ultimo_mensaje_at' => null,
            ]
        );

        return redirect()->route(
            'marketplace.mensajes.show',
            $conversacion->id
        );
    }

    /**
     * Mostrar una conversación de Marketplace.
     */
    public function show(
        MarketplaceConversacion $conversacion
    ): Response {
        $usuarioId = (int) Auth::id();

        abort_unless(
            (int) $conversacion->comprador_id === $usuarioId
                || (int) $conversacion->vendedor_id === $usuarioId,
            403
        );

        $conversacion->load([
            'publicacion:id,user_id,categoria_id,subcategoria_id,titulo,slug,precio,es_gratis,estado_producto,estado_publicacion',
            'publicacion.imagenes' => function ($query) {
                $query
                    ->orderByDesc('es_principal')
                    ->orderBy('orden');
            },
            'publicacion.usuario:id,name,foto',
            'comprador:id,name,foto',
            'vendedor:id,name,foto',
            'mensajes.usuario:id,name,foto',
        ]);

        $conversacion->mensajes()
            ->where('user_id', '!=', $usuarioId)
            ->whereNull('leido_at')
            ->update([
                'leido_at' => now(),
            ]);

        return Inertia::render('Marketplace/Conversacion', [
            'conversacion' => $conversacion,
            'usuarioActual' => $usuarioId,
        ]);
    }

    /**
     * Obtener la cantidad total de mensajes no leídos.
     *
     * Incluye Marketplace, Clasificados, Emprendimientos, Servicios y Eventos.
     */
    public function noLeidos(): JsonResponse
    {
        $usuarioId = Auth::id();

        if (!$usuarioId) {
            return response()->json([
                'mensajesNoLeidos' => 0,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | MARKETPLACE
        |--------------------------------------------------------------------------
        */

        $marketplace = MarketplaceMensaje::query()
            ->where('user_id', '!=', $usuarioId)
            ->whereNull('leido_at')
            ->whereHas('conversacion', function ($query) use ($usuarioId) {
                $query
                    ->where('comprador_id', $usuarioId)
                    ->orWhere('vendedor_id', $usuarioId);
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | CLASIFICADOS
        |--------------------------------------------------------------------------
        */

        $clasificados = ClasificadoMensaje::query()
            ->where('user_id', '!=', $usuarioId)
            ->whereNull('leido_at')
            ->whereHas('conversacion', function ($query) use ($usuarioId) {
                $query
                    ->where('comprador_id', $usuarioId)
                    ->orWhere('vendedor_id', $usuarioId);
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | EMPRENDIMIENTOS
        |--------------------------------------------------------------------------
        */

        $emprendimientos = EmprendimientoMensaje::query()
            ->where('user_id', '!=', $usuarioId)
            ->whereNull('leido_at')
            ->whereHas('conversacion', function ($query) use ($usuarioId) {
                $query
                    ->where('usuario_iniciador_id', $usuarioId)
                    ->orWhere('usuario_receptor_id', $usuarioId);
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | SERVICIOS
        |--------------------------------------------------------------------------
        */

        $servicios = ServicioMensaje::query()
            ->where('user_id', '!=', $usuarioId)
            ->whereNull('leido_at')
            ->whereHas('conversacion', function ($query) use ($usuarioId) {
                $query
                    ->where('usuario_interesado_id', $usuarioId)
                    ->orWhere('propietario_id', $usuarioId);
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | EVENTOS
        |--------------------------------------------------------------------------
        */

        $eventos = EventoMensaje::query()
            ->where('user_id', '!=', $usuarioId)
            ->whereNull('leido_at')
            ->whereHas('conversacion', function ($query) use ($usuarioId) {
                $query
                    ->where('comprador_id', $usuarioId)
                    ->orWhere('organizador_id', $usuarioId);
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | TOTAL
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'mensajesNoLeidos' =>
            $marketplace
                + $clasificados
                + $emprendimientos
                + $servicios
                + $eventos, // 🔥 NUEVO: Sumar los eventos al total
        ]);
    }
}
