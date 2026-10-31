<?php

namespace App\Http\Controllers\Servicio;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use App\Models\ContactoServicio;
use App\Models\ResenaServicio;
use App\Models\ServicioRequerimiento;
use App\Models\ServicioConversacion;
use App\Models\Favorito;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ServiciosController extends Controller
{
    /**
     * Mostrar el catálogo de servicios.
     */
    public function index(Request $request): Response
    {
        $tipo = in_array($request->input('tipo'), ['ofrecidos', 'requeridos'])
            ? $request->input('tipo')
            : 'ofrecidos';

        $buscar = $request->input('buscar');
        $modalidad = $request->input('modalidad');
        $usuarioId = Auth::id();

        // Obtener favoritos del usuario para servicios y requerimientos
        $favoritosServiciosIds = Auth::check()
            ? Favorito::query()
            ->where('user_id', $usuarioId)
            ->where('modulo', 'servicios')
            ->pluck('publicacion_id')
            ->map(fn($id) => (int) $id)
            ->toArray()
            : [];

        $favoritosRequerimientosIds = Auth::check()
            ? Favorito::query()
            ->where('user_id', $usuarioId)
            ->where('modulo', 'servicios_requerimientos')
            ->pluck('publicacion_id')
            ->map(fn($id) => (int) $id)
            ->toArray()
            : [];

        if ($tipo === 'requeridos') {
            // Catálogo principal: Solicitudes de la comunidad
            $items = ServicioRequerimiento::query()
                ->with('usuario')
                ->abiertos()
                ->when($buscar, function ($query, $buscar) {
                    $query->where(function ($q) use ($buscar) {
                        $q->where('titulo', 'like', "%{$buscar}%")
                            ->orWhere('descripcion', 'like', "%{$buscar}%")
                            ->orWhere('categoria', 'like', "%{$buscar}%");
                    });
                })
                ->when($modalidad, fn($q) => $q->where('modalidad', $modalidad))
                ->latest()
                ->paginate(12)
                ->withQueryString()
                ->through(function ($req) use ($favoritosRequerimientosIds) {
                    $req->es_favorito = in_array((int) $req->id, $favoritosRequerimientosIds, true);
                    return $req;
                });
        } else {
            // Catálogo principal: Servicios ofrecidos
            $query = Servicio::query()
                ->with('user')
                ->where('estado', 'activo');

            // ---------------------------------------------------------
            // FILTRO GEOESPACIAL POR ALCANCE (RADIO)
            // ---------------------------------------------------------
            $usuarioLogueado = Auth::user();

            if ($usuarioLogueado) {
                $usuarioLogueado->load('informacionRecepcion');
            }

            $infoResidencia = $usuarioLogueado ? $usuarioLogueado->informacionRecepcion : null;

            $query->select('servicios.*');

            if ($infoResidencia && $infoResidencia->latitud && $infoResidencia->longitud) {
                $lat = number_format((float) $infoResidencia->latitud, 8, '.', '');
                $lng = number_format((float) $infoResidencia->longitud, 8, '.', '');

                $driver = DB::connection()->getDriverName();

                if ($driver === 'sqlite') {
                    $query->selectRaw("0 AS distancia_km");
                } else {
                    $haversine = "(6371 * acos(least(1.0, cos(radians({$lat})) * cos(radians(latitud)) * cos(radians(longitud) - radians({$lng})) + sin(radians({$lat})) * sin(radians(latitud)))))";

                    $query->selectRaw("{$haversine} AS distancia_km")
                        ->where(function ($q) use ($haversine) {
                            $q->whereNull('radio_alcance_km')
                                ->orWhereRaw("{$haversine} <= radio_alcance_km");
                        });
                }
            } else {
                $query->selectRaw("NULL AS distancia_km")
                    ->whereNull('radio_alcance_km');
            }
            // ---------------------------------------------------------

            $query->when($buscar, function ($q, $buscar) {
                $q->where(function ($sq) use ($buscar) {
                    $sq->where('titulo', 'like', "%{$buscar}%")
                        ->orWhere('descripcion', 'like', "%{$buscar}%")
                        ->orWhere('categoria', 'like', "%{$buscar}%");
                });
            })
                ->when($modalidad, fn($q) => $q->where('modalidad', $modalidad))
                ->latest();

            $items = $query->paginate(12)
                ->withQueryString()
                ->through(function ($servicio) use ($favoritosServiciosIds) {
                    $servicio->es_favorito = in_array((int) $servicio->id, $favoritosServiciosIds, true);
                    return $servicio;
                });
        }

        // Franja superior fija: solicitudes abiertas recientes
        $requerimientosDestacados = ServicioRequerimiento::query()
            ->with('usuario')
            ->abiertos()
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($req) use ($favoritosRequerimientosIds) {
                $req->setAttribute('es_favorito', in_array((int) $req->id, $favoritosRequerimientosIds, true));
                // 🔥 ESTO ES LO QUE FALTABA: Le inyectamos el 'tipo' para que Vue sepa qué carpeta buscar
                $req->setAttribute('tipo', 'Requerimiento');
                return $req;
            });

        return Inertia::render('Servicios/Index', [
            'items' => $items,
            'tipoActivo' => $tipo,
            'requerimientosDestacados' => $requerimientosDestacados,
            'filtros' => [
                'buscar' => $buscar ?? '',
                'modalidad' => $modalidad ?? '',
                'tipo' => $tipo,
            ],
        ]);
    }

    /**
     * Mostrar el formulario para publicar un servicio.
     */
    public function create(Request $request): Response
    {
        $usuario = $request->user();
        $usuario->load('informacionRecepcion'); // Cargar Ancla Territorial

        return Inertia::render('Servicios/Create', [
            'informacion_recepcion' => $usuario->informacionRecepcion,
        ]);
    }

    /**
     * Guardar una nueva publicación.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'categoria' => ['required', 'string', 'max:100'],

            'modalidad' => [
                'required',
                'in:presencial,remoto,hibrido',
            ],

            'precio_tipo' => [
                'required',
                'in:hora,dia,servicio,proyecto,fijo,a_convenir',
            ],

            'precio' => [
                'nullable',
                'numeric',
                'min:0',
                'required_unless:precio_tipo,a_convenir',
            ],

            'moneda' => ['required', 'string', 'size:3'],

            'imagen_tipo' => [
                'required',
                'in:perfil,archivo',
            ],

            'imagen' => [
                'nullable',
                'image',
                'max:5120',
                'required_if:imagen_tipo,archivo',
            ],

            'pais' => ['nullable', 'string', 'max:100'],
            'departamento' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],

            // NUEVAS REGLAS GEO
            'direccion' => ['nullable', 'string', 'max:255'],
            'mostrar_direccion' => ['nullable', 'boolean'],
            'radio_alcance_km' => ['nullable', 'numeric', 'min:1', 'max:100'],
        ]);

        $usuario = $request->user();
        $usuario->load('informacionRecepcion');
        $infoResidencia = $usuario->informacionRecepcion;

        $latitud = null;
        $longitud = null;
        $radioAlcance = null;
        $mostrarDireccion = $validated['mostrar_direccion'] ?? false;

        // ---------------------------------------------------------
        // VALIDACIÓN GEOESPACIAL SEGÚN MODALIDAD
        // ---------------------------------------------------------
        if (in_array($validated['modalidad'], ['presencial', 'hibrido'])) {
            if (!$infoResidencia || !$infoResidencia->latitud || !$infoResidencia->longitud) {
                return back()->withErrors([
                    'modalidad' => 'Debes registrar tu ubicación exacta en el mapa antes de publicar servicios presenciales o híbridos.'
                ]);
            }

            $latitud = $infoResidencia->latitud;
            $longitud = $infoResidencia->longitud;
            $radioAlcance = $validated['radio_alcance_km'] ?? 15;
        } else {
            $validated['direccion'] = null;
            $mostrarDireccion = false;
        }

        $imagenPath = null;

        if (
            $validated['imagen_tipo'] === 'archivo' &&
            $request->hasFile('imagen')
        ) {
            $imagenPath = $request->file('imagen')->store('servicios', 'public');
        }

        Servicio::create([
            'user_id' => $usuario->id,

            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'categoria' => $validated['categoria'],

            'modalidad' => $validated['modalidad'],

            'precio_tipo' => $validated['precio_tipo'],
            'precio' => $validated['precio_tipo'] === 'a_convenir'
                ? null
                : $validated['precio'],
            'moneda' => $validated['moneda'],

            'imagen_tipo' => $validated['imagen_tipo'],
            'imagen' => $imagenPath,

            'pais' => $validated['pais'] ?? null,
            'departamento' => $validated['departamento'] ?? null,
            'ciudad' => $validated['ciudad'] ?? null,
            'direccion' => $validated['direccion'] ?? null,

            // CAMPOS GEO
            'mostrar_direccion' => $mostrarDireccion,
            'latitud' => $latitud,
            'longitud' => $longitud,
            'radio_alcance_km' => $radioAlcance,

            'estado' => 'activo',
        ]);

        return redirect()
            ->route('servicios.index')
            ->with('success', 'Tu servicio se publicó correctamente.');
    }

    /**
     * Mostrar el detalle de un servicio.
     */
    public function show(
        Request $request,
        Servicio $servicio
    ): Response {
        abort_unless($servicio->estado === 'activo', 404);

        $servicio->load('user');

        $esFavorito = Auth::check()
            ? Favorito::query()
            ->where('user_id', Auth::id())
            ->where('publicacion_id', (int) $servicio->id)
            ->where('modulo', 'servicios')
            ->exists()
            : false;

        $servicio->es_favorito = (bool) $esFavorito;

        $contactosUsuario = collect();

        if (
            $request->user() &&
            $request->user()->id !== $servicio->user_id
        ) {
            $tieneConversacion = ServicioConversacion::where('servicio_id', $servicio->id)
                ->where('usuario_interesado_id', $request->user()->id)
                ->exists();

            if ($tieneConversacion) {
                ContactoServicio::firstOrCreate(
                    [
                        'servicio_id' => $servicio->id,
                        'user_id'     => $request->user()->id,
                    ],
                    [
                        'canal'       => 'chat',
                    ]
                );
            }

            $contactosUsuario = ContactoServicio::query()
                ->where('servicio_id', $servicio->id)
                ->where('user_id', $request->user()->id)
                ->latest()
                ->get();

            $resenasUsuario = ResenaServicio::query()
                ->where('servicio_id', $servicio->id)
                ->where('user_id', $request->user()->id)
                ->whereIn(
                    'contacto_servicio_id',
                    $contactosUsuario->pluck('id')
                )
                ->get()
                ->groupBy('contacto_servicio_id');

            $contactosUsuario->transform(
                function ($contacto) use ($resenasUsuario) {
                    $contacto->resenas = $resenasUsuario
                        ->get($contacto->id, collect())
                        ->values();

                    return $contacto;
                }
            );
        }

        $resenasPublicas = ResenaServicio::query()
            ->with('user')
            ->where('servicio_id', $servicio->id)
            ->where('estado', 'publicada')
            ->latest()
            ->get()
            ->map(function ($resena) {
                $usuario = $resena->user;

                $nombre = trim(collect([
                    $usuario?->nombres,
                    $usuario?->apellidos,
                ])->filter()->implode(' '));

                return [
                    'id' => $resena->id,
                    'tipo' => $resena->tipo,
                    'calificacion' => (int) $resena->calificacion,
                    'comentario' => $resena->comentario,
                    'created_at' => $resena->created_at,
                    'resenador_nombre' => $nombre
                        ?: ($usuario?->name ?? 'Usuario'),
                    'respuesta_propietario' => $resena->respuesta_propietario,
                    'respuesta_at' => $resena->respuesta_at,
                ];
            })
            ->values();

        $resumenCalificaciones = [];

        foreach (['atencion', 'servicio'] as $tipo) {
            $resenasTipo = $resenasPublicas->where('tipo', $tipo);

            $resumenCalificaciones[$tipo] = [
                'total' => $resenasTipo->count(),
                'promedio' => $resenasTipo->isNotEmpty()
                    ? round($resenasTipo->avg('calificacion'), 1)
                    : 0,
            ];
        }

        return Inertia::render('Servicios/Show', [
            'servicio' => $servicio,
            'contactosUsuario' => $contactosUsuario,
            'resenasPublicas' => $resenasPublicas,
            'resumenCalificaciones' => $resumenCalificaciones,
        ]);
    }

    /**
     * Mostrar el formulario de edición.
     */
    public function edit(Servicio $servicio): Response
    {
        $this->autorizarPropietario($servicio);

        return Inertia::render('Servicios/Edit', [
            'servicio' => $servicio,
        ]);
    }

    /**
     * Actualizar una publicación propia.
     */
    public function update(
        Request $request,
        Servicio $servicio
    ): RedirectResponse {
        $this->autorizarPropietario($servicio);

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'categoria' => ['required', 'string', 'max:100'],

            'modalidad' => [
                'required',
                'in:presencial,remoto,hibrido',
            ],

            'precio_tipo' => [
                'required',
                'in:hora,dia,servicio,proyecto,fijo,a_convenir',
            ],

            'precio' => [
                'nullable',
                'numeric',
                'min:0',
                'required_unless:precio_tipo,a_convenir',
            ],

            'moneda' => ['required', 'string', 'size:3'],

            'imagen_tipo' => [
                'required',
                'in:perfil,archivo',
            ],

            'imagen' => [
                'nullable',
                'image',
                'max:5120',
            ],

            'pais' => ['nullable', 'string', 'max:100'],
            'departamento' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],

            // NUEVAS REGLAS GEO PARA UPDATE
            'direccion' => ['nullable', 'string', 'max:255'],
            'mostrar_direccion' => ['nullable', 'boolean'],
            'radio_alcance_km' => ['nullable', 'numeric', 'min:1', 'max:100'],
        ]);

        // Bloque de ubicación
        if (in_array($validated['modalidad'], ['presencial', 'hibrido'])) {
            $usuario = Auth::user();
            $usuario->load('informacionRecepcion');
            if ($usuario->informacionRecepcion && $usuario->informacionRecepcion->latitud) {
                $validated['latitud'] = $usuario->informacionRecepcion->latitud;
                $validated['longitud'] = $usuario->informacionRecepcion->longitud;
                $validated['radio_alcance_km'] = $validated['radio_alcance_km'] ?? 15;
            }
        } else {
            $validated['latitud'] = null;
            $validated['longitud'] = null;
            $validated['radio_alcance_km'] = null;
            $validated['mostrar_direccion'] = false;
        }

        if (
            $validated['imagen_tipo'] === 'archivo' &&
            $request->hasFile('imagen')
        ) {
            if ($servicio->imagen) {
                Storage::disk('public')->delete($servicio->imagen);
            }

            $validated['imagen'] = $request->file('imagen')->store('servicios', 'public');
        } elseif ($validated['imagen_tipo'] === 'perfil') {
            if ($servicio->imagen) {
                Storage::disk('public')->delete($servicio->imagen);
            }

            $validated['imagen'] = null;
        } else {
            unset($validated['imagen']);
        }

        $servicio->update([
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'categoria' => $validated['categoria'],

            'modalidad' => $validated['modalidad'],

            'precio_tipo' => $validated['precio_tipo'],
            'precio' => $validated['precio_tipo'] === 'a_convenir'
                ? null
                : $validated['precio'],
            'moneda' => $validated['moneda'],

            'imagen_tipo' => $validated['imagen_tipo'],
            'imagen' => $validated['imagen'] ?? $servicio->imagen,

            'pais' => $validated['pais'] ?? null,
            'departamento' => $validated['departamento'] ?? null,
            'ciudad' => $validated['ciudad'] ?? null,
            'direccion' => $validated['direccion'] ?? null,

            // CAMPOS GEO
            'mostrar_direccion' => $validated['mostrar_direccion'] ?? false,
            'latitud' => $validated['latitud'] ?? null,
            'longitud' => $validated['longitud'] ?? null,
            'radio_alcance_km' => $validated['radio_alcance_km'] ?? null,
        ]);

        return redirect()
            ->route('servicios.show', $servicio)
            ->with('success', 'Tu servicio se actualizó correctamente.');
    }

    /**
     * Activar o pausar una publicación.
     */
    public function cambiarEstado(
        Request $request,
        Servicio $servicio
    ): RedirectResponse {
        $this->autorizarPropietario($servicio);

        $validated = $request->validate([
            'estado' => ['required', 'in:activo,pausado'],
        ]);

        $servicio->update([
            'estado' => $validated['estado'],
        ]);

        return back()->with(
            'success',
            'El estado del servicio se actualizó correctamente.'
        );
    }

    /**
     * Eliminar una publicación propia.
     */
    public function destroy(Servicio $servicio): RedirectResponse
    {
        $this->autorizarPropietario($servicio);

        if ($servicio->imagen) {
            Storage::disk('public')->delete($servicio->imagen);
        }

        $servicio->delete();

        return redirect()
            ->route('servicios.index')
            ->with('success', 'El servicio se eliminó correctamente.');
    }

    /**
     * Verificar que el usuario autenticado sea el propietario.
     */
    private function autorizarPropietario(Servicio $servicio): void
    {
        abort_unless(
            $servicio->user_id === auth()->id(),
            403,
            'No tienes permiso para modificar este servicio.'
        );
    }

    /**
     * Registrar que un usuario inició contacto por el chat de la plataforma.
     */
    public function registrarContacto(
        Request $request,
        Servicio $servicio
    ): JsonResponse {
        abort_unless($servicio->estado === 'activo', 404);

        abort_if(
            $request->user()->id === $servicio->user_id,
            403,
            'No puedes registrar un contacto con tu propia publicación.'
        );

        $contacto = ContactoServicio::firstOrCreate(
            [
                'servicio_id' => $servicio->id,
                'user_id' => $request->user()->id,
            ],
            [
                'canal' => $request->input('canal', 'chat'),
            ]
        );

        return response()->json([
            'message' => 'Contacto registrado exitosamente.',
            'contacto_id' => $contacto->id,
        ]);
    }

    /**
     * Guardar una reseña vinculada a un contacto previo.
     */
    public function guardarResena(
        Request $request,
        Servicio $servicio
    ): RedirectResponse {
        $validated = $request->validate([
            'contacto_servicio_id' => [
                'required',
                'integer',
                'exists:contactos_servicios,id',
            ],
            'tipo' => [
                'required',
                'Rule' => Rule::in(['atencion', 'servicio']),
            ],
            'calificacion' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'comentario' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'recibio_servicio' => [
                'required',
                'boolean',
            ],
        ]);

        $contacto = ContactoServicio::query()
            ->whereKey($validated['contacto_servicio_id'])
            ->where('servicio_id', $servicio->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        abort_if(
            $servicio->user_id === $request->user()->id,
            403,
            'No puedes reseñar tu propia publicación.'
        );

        if (
            $validated['tipo'] === 'servicio' &&
            !$validated['recibio_servicio']
        ) {
            return back()->withErrors([
                'recibio_servicio' => 'Debes confirmar que recibiste el servicio para calificarlo.',
            ]);
        }

        $yaExiste = ResenaServicio::query()
            ->where('contacto_servicio_id', $contacto->id)
            ->where('tipo', $validated['tipo'])
            ->exists();

        if ($yaExiste) {
            return back()->withErrors([
                'tipo' => 'Ya registraste una reseña de este tipo para este contacto.',
            ]);
        }

        ResenaServicio::create([
            'contacto_servicio_id' => $contacto->id,
            'servicio_id' => $servicio->id,
            'user_id' => $request->user()->id,
            'tipo' => $validated['tipo'],
            'calificacion' => $validated['calificacion'],
            'comentario' => $validated['comentario'] ?? null,
            'estado' => 'publicada',
        ]);

        return back()->with(
            'success',
            'Tu reseña se guardó correctamente.'
        );
    }

    /**
     * Permitir al propietario responder una reseña pública.
     */
    public function responderResena(
        Request $request,
        Servicio $servicio,
        int $resena
    ): RedirectResponse {
        $this->autorizarPropietario($servicio);

        $validated = $request->validate([
            'respuesta_propietario' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $resenaServicio = ResenaServicio::query()
            ->where('servicio_id', $servicio->id)
            ->where('estado', 'publicada')
            ->whereKey($resena)
            ->firstOrFail();

        if ($resenaServicio->respuesta_propietario) {
            return back()->withErrors([
                'respuesta_propietario' => 'Esta reseña ya tiene una respuesta.',
            ]);
        }

        $resenaServicio->update([
            'respuesta_propietario' => trim(
                $validated['respuesta_propietario']
            ),
            'respuesta_at' => now(),
        ]);

        return back()->with(
            'success',
            'Tu respuesta se publicó correctamente.'
        );
    }
}
