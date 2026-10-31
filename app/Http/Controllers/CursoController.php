<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Favorito;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CursoController extends Controller
{
    /**
     * Listado general de cursos publicados por la comunidad.
     */
    public function index(Request $request): Response
    {
        $usuarioId = $request->user()?->id;

        // Obtener los IDs de favoritos del usuario autenticado para cursos
        $favoritosIds = Auth::check()
            ? Favorito::query()
            ->where('user_id', $usuarioId)
            ->where('modulo', 'cursos')
            ->pluck('publicacion_id')
            ->toArray()
            : [];

        $query = Curso::query()
            ->select('cursos.*')
            ->where('estado', 'publicado')
            ->whereHas('usuario', function ($q) {
                $q->where('activo', true);
            })
            ->where(function ($q) {
                $q->whereNull('publicado_desde')
                    ->orWhere('publicado_desde', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('publicado_hasta')
                    ->orWhere('publicado_hasta', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('fecha_limite_inscripcion')
                    ->orWhere('fecha_limite_inscripcion', '>=', now());
            });

        // ---------------------------------------------------------
        // FILTRO GEOESPACIAL POR ALCANCE (RADIO)
        // ---------------------------------------------------------
        $usuarioLogueado = Auth::user();

        if ($usuarioLogueado) {
            $usuarioLogueado->load('informacionRecepcion');
        }

        $infoResidencia = $usuarioLogueado ? $usuarioLogueado->informacionRecepcion : null;

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

        $cursos = $query
            ->with('usuario:id,name')
            ->with([
                'inscripciones' => function ($q) use ($usuarioId) {
                    $q->select('id', 'curso_id', 'user_id', 'estado')
                        ->where('user_id', $usuarioId);
                },
            ])
            ->withCount([
                'inscripciones as inscripciones_activas_count' => function ($q) {
                    $q->where('estado', 'activa');
                },
            ])
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->input('buscar');
                $q->where(function ($subquery) use ($buscar) {
                    $subquery->where('titulo', 'like', "%{$buscar}%")
                        ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            })
            ->when($request->filled('modalidad'), function ($q) use ($request) {
                $q->where('modalidad', $request->input('modalidad'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(function (Curso $curso) use ($usuarioId, $favoritosIds) {
                $inscripcion = $curso->inscripciones->first();

                if ($curso->cupos_ilimitados) {
                    $cuposDisponibles = null;
                } elseif ($curso->cantidad_cupos !== null) {
                    $cuposDisponibles = max(
                        0,
                        (int) $curso->cantidad_cupos - (int) $curso->inscripciones_activas_count
                    );
                } else {
                    $cuposDisponibles = null;
                }

                return [
                    'id' => $curso->id,
                    'user_id' => $curso->user_id,
                    'es_mi_curso' => (int) $curso->user_id === (int) $usuarioId,
                    'es_favorito' => in_array($curso->id, $favoritosIds),
                    'titulo' => $curso->titulo,
                    'slug' => $curso->slug,
                    'descripcion' => $curso->descripcion,
                    'imagen' => $curso->imagen ? Storage::disk('public')->url($curso->imagen) : null,
                    'tipo' => $curso->tipo,
                    'es_gratuito' => $curso->es_gratuito,
                    'precio' => $curso->precio,
                    'modalidad' => $curso->modalidad,
                    'ciudad' => $curso->ciudad,
                    'fecha_inicio' => $curso->fecha_inicio?->format('Y-m-d'),
                    'fecha_fin' => $curso->fecha_fin?->format('Y-m-d'),
                    'estado' => $curso->estado,
                    'cupos_ilimitados' => (bool) $curso->cupos_ilimitados,
                    'cantidad_cupos' => $curso->cantidad_cupos,
                    'cupos_disponibles' => $cuposDisponibles,
                    'inscripciones_activas_count' => (int) $curso->inscripciones_activas_count,
                    'ya_inscrito' => $inscripcion !== null,
                    'estado_inscripcion' => $inscripcion?->estado,
                    'inscripcion_id' => $inscripcion?->id,
                    'intensidad_horaria' => $curso->intensidad_horaria,
                    'url_externa' => $curso->url_externa,
                    'usuario' => $curso->usuario ? ['name' => $curso->usuario->name] : null,
                    'created_at' => $curso->created_at?->format('Y-m-d H:i:s'),
                ];
            });

        return Inertia::render('Cursos/Index', [
            'cursos' => $cursos,
            'filtros' => $request->only(['buscar', 'modalidad']),
        ]);
    }

    /**
     * Formulario para crear un curso.
     */
    public function create(Request $request): Response
    {
        $usuario = $request->user();
        $usuario->load('informacionRecepcion');

        return Inertia::render('Cursos/Create', [
            'informacion_recepcion' => $usuario->informacionRecepcion,
        ]);
    }

    /**
     * Guardar un nuevo curso.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $usuario = $request->user();

        // VALIDACIÓN GEOESPACIAL SEGÚN MODALIDAD
        if (in_array($validated['modalidad'], ['presencial', 'hibrido'])) {
            $usuario->load('informacionRecepcion');
            $infoResidencia = $usuario->informacionRecepcion;

            if (!$infoResidencia || !$infoResidencia->latitud || !$infoResidencia->longitud) {
                return back()->withErrors([
                    'modalidad' => 'Debes registrar tu ubicación exacta en el mapa antes de publicar cursos presenciales o híbridos.'
                ]);
            }

            $validated['latitud'] = $infoResidencia->latitud;
            $validated['longitud'] = $infoResidencia->longitud;
            $validated['radio_alcance_km'] = $request->input('radio_alcance_km', 15);
            $validated['mostrar_direccion'] = $request->input('mostrar_direccion', false);
        } else {
            $validated['latitud'] = null;
            $validated['longitud'] = null;
            $validated['radio_alcance_km'] = null;
            $validated['direccion'] = null;
            $validated['mostrar_direccion'] = false;
        }

        $validated['user_id'] = $usuario->id;
        $validated['slug'] = $this->generarSlug($validated['titulo']);
        $validated['estado'] = $validated['estado'] ?? 'publicado';

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('cursos', 'public');
        }

        Curso::create($validated);

        return redirect()
            ->route('cursos.index')
            ->with('success', 'El curso se creó correctamente.');
    }

    /**
     * Mostrar el formulario de edición.
     */
    public function edit(Request $request, int $id): Response
    {
        $curso = $this->cursoDelUsuario($request, $id);
        $usuario = $request->user();
        $usuario->load('informacionRecepcion');

        return Inertia::render('Cursos/Edit', [
            'curso' => $this->datosFormulario($curso),
            'informacion_recepcion' => $usuario->informacionRecepcion,
        ]);
    }

    /**
     * Actualizar un curso existente.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $curso = $this->cursoDelUsuario($request, $id);
        $validated = $request->validate($this->rules($curso));

        // VALIDACIÓN GEOESPACIAL SEGÚN MODALIDAD
        if (in_array($validated['modalidad'], ['presencial', 'hibrido'])) {
            $usuario = $request->user();
            $usuario->load('informacionRecepcion');
            $infoResidencia = $usuario->informacionRecepcion;

            if ($infoResidencia && $infoResidencia->latitud) {
                $validated['latitud'] = $infoResidencia->latitud;
                $validated['longitud'] = $infoResidencia->longitud;
                $validated['radio_alcance_km'] = $request->input('radio_alcance_km', 15);
            }
            $validated['mostrar_direccion'] = $request->input('mostrar_direccion', false);
        } else {
            $validated['latitud'] = null;
            $validated['longitud'] = null;
            $validated['radio_alcance_km'] = null;
            $validated['direccion'] = null;
            $validated['mostrar_direccion'] = false;
        }

        $validated['slug'] = $curso->slug;

        if ($curso->estado === 'finalizado') {
            unset($validated['estado']);
        }

        if ($request->hasFile('imagen')) {
            $imagenAnterior = $curso->imagen;
            $validated['imagen'] = $request->file('imagen')->store('cursos', 'public');

            if ($imagenAnterior) {
                Storage::disk('public')->delete($imagenAnterior);
            }
        }

        $curso->update($validated);

        return redirect()
            ->route('cursos.index')
            ->with('success', 'El curso se actualizó correctamente.');
    }

    public function show(Curso $curso)
    {
        $usuario = auth()->user();

        $curso->load([
            'usuario:id,name,email',
        ]);

        $miInscripcion = $curso->inscripciones()
            ->where('user_id', $usuario->id)
            ->first();

        $inscritosActivos = $curso->inscripciones()
            ->where('estado', 'activa')
            ->count();

        $esFavorito = Favorito::query()
            ->where('user_id', $usuario->id)
            ->where('publicacion_id', $curso->id)
            ->where('modulo', 'cursos')
            ->exists();

        $curso->inscritos_activos = $inscritosActivos;
        $curso->mi_inscripcion = $miInscripcion;
        $curso->es_propietario = $curso->user_id === $usuario->id;
        $curso->es_favorito = $esFavorito;

        return Inertia::render('Cursos/Show', [
            'curso' => [
                'id' => $curso->id,
                'titulo' => $curso->titulo,
                'slug' => $curso->slug,
                'descripcion' => $curso->descripcion,
                'imagen' => $curso->imagen,
                'tipo' => $curso->tipo,
                'modalidad' => $curso->modalidad,
                'precio' => $curso->precio,
                'intensidad_horaria' => $curso->intensidad_horaria,
                'estado' => $curso->estado,
                'publicado_desde' => $curso->publicado_desde,
                'publicado_hasta' => $curso->publicado_hasta,
                'fecha_limite_inscripcion' => $curso->fecha_limite_inscripcion,
                'url_externa' => $curso->url_externa,
                'inscritos_activos' => $inscritosActivos,
                'es_propietario' => $curso->es_propietario,
                'es_favorito' => $esFavorito,
                'mi_inscripcion' => $miInscripcion ? [
                    'id' => $miInscripcion->id,
                    'estado' => $miInscripcion->estado,
                    'created_at' => $miInscripcion->created_at,
                ] : null,
                'usuario' => $curso->usuario ? [
                    'id' => $curso->usuario->id,
                    'name' => $curso->usuario->name,
                    'email' => $curso->usuario->email,
                ] : null,
            ],
        ]);
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $curso = $this->cursoDelUsuario($request, $id);

        if ($curso->imagen) {
            Storage::disk('public')->delete($curso->imagen);
        }

        $curso->delete();

        return redirect()
            ->route('cursos.index')
            ->with('success', 'El curso se eliminó correctamente.');
    }

    private function rules(?Curso $curso = null): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'imagen' => [
                $curso ? 'nullable' : 'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'tipo' => ['required', Rule::in(['propio', 'externo'])],
            'url_externa' => ['nullable', 'required_if:tipo,externo', 'url', 'max:2048'],
            'es_gratuito' => ['required', 'boolean'],
            'precio' => [
                'nullable',
                'required_if:es_gratuito,0',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],
            'modalidad' => ['required', Rule::in(['virtual', 'presencial', 'hibrido'])],
            'pais' => ['nullable', 'string', 'max:255'],
            'departamento' => ['nullable', 'string', 'max:255'],
            'ciudad' => ['nullable', 'string', 'max:255'],

            // NUEVAS REGLAS GEO
            'direccion' => ['nullable', 'string', 'max:255'],
            'mostrar_direccion' => ['nullable', 'boolean'],
            'radio_alcance_km' => ['nullable', 'numeric', 'min:1', 'max:100'],

            'enlace_virtual' => ['nullable', 'url', 'max:2048'],
            'frecuencia_clases' => ['nullable', 'integer', 'min:1', 'max:255'],
            'duracion_sesion_minutos' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'intensidad_horaria' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'fecha_limite_inscripcion' => ['nullable', 'date'],
            'publicado_desde' => ['nullable', 'date'],
            'publicado_hasta' => ['nullable', 'date', 'after_or_equal:publicado_desde'],
            'cupos_ilimitados' => ['required', 'boolean'],
            'cantidad_cupos' => [
                'nullable',
                'required_if:cupos_ilimitados,0',
                'integer',
                'min:1',
                'max:2147483647',
            ],
            'permite_lista_espera' => ['required', 'boolean'],
            'estado' => [
                'sometimes',
                'required',
                Rule::in(['borrador', 'publicado', 'pausado', 'cerrado', 'cancelado']),
            ],
        ];
    }

    private function cursoDelUsuario(Request $request, int $id): Curso
    {
        return Curso::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);
    }

    private function generarSlug(string $titulo): string
    {
        $slugBase = Str::slug($titulo);
        if ($slugBase === '') {
            $slugBase = 'curso';
        }
        $slug = $slugBase;
        $contador = 1;
        while (Curso::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $contador;
            $contador++;
        }
        return $slug;
    }

    private function datosFormulario(Curso $curso): array
    {
        return [
            'id' => $curso->id,
            'titulo' => $curso->titulo,
            'slug' => $curso->slug,
            'descripcion' => $curso->descripcion,
            'imagen' => $curso->imagen ? Storage::disk('public')->url($curso->imagen) : null,
            'tipo' => $curso->tipo,
            'url_externa' => $curso->url_externa,
            'es_gratuito' => $curso->es_gratuito,
            'precio' => $curso->precio,
            'modalidad' => $curso->modalidad,
            'pais' => $curso->pais,
            'departamento' => $curso->departamento,
            'ciudad' => $curso->ciudad,
            'direccion' => $curso->direccion,
            'enlace_virtual' => $curso->enlace_virtual,

            // CAMPOS GEO
            'mostrar_direccion' => $curso->mostrar_direccion,
            'radio_alcance_km' => $curso->radio_alcance_km,

            'frecuencia_clases' => $curso->frecuencia_clases,
            'duracion_sesion_minutos' => $curso->duracion_sesion_minutos,
            'intensidad_horaria' => $curso->intensidad_horaria,
            'fecha_inicio' => $curso->fecha_inicio?->format('Y-m-d'),
            'fecha_fin' => $curso->fecha_fin?->format('Y-m-d'),
            'fecha_limite_inscripcion' => $curso->fecha_limite_inscripcion?->format('Y-m-d\TH:i'),
            'publicado_desde' => $curso->publicado_desde?->format('Y-m-d\TH:i'),
            'publicado_hasta' => $curso->publicado_hasta?->format('Y-m-d\TH:i'),
            'cupos_ilimitados' => $curso->cupos_ilimitados,
            'cantidad_cupos' => $curso->cantidad_cupos,
            'permite_lista_espera' => $curso->permite_lista_espera,
            'estado' => $curso->estado,
        ];
    }

    public function misCursos(Request $request): Response
    {
        $usuarioId = $request->user()->id;

        $cursos = Curso::query()
            ->where('user_id', $usuarioId)
            ->withCount([
                'inscripciones as inscripciones_activas_count' => function ($q) {
                    $q->where('estado', 'activa');
                },
            ])
            ->withCount(['inscripciones as inscripciones_count'])
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(function (Curso $curso) {
                if ($curso->cupos_ilimitados) {
                    $cuposDisponibles = null;
                } elseif ($curso->cantidad_cupos !== null) {
                    $cuposDisponibles = max(
                        0,
                        (int) $curso->cantidad_cupos - (int) $curso->inscripciones_activas_count
                    );
                } else {
                    $cuposDisponibles = null;
                }

                return [
                    'id' => $curso->id,
                    'user_id' => $curso->user_id,
                    'titulo' => $curso->titulo,
                    'slug' => $curso->slug,
                    'descripcion' => $curso->descripcion,
                    'imagen' => $curso->imagen ? Storage::disk('public')->url($curso->imagen) : null,
                    'tipo' => $curso->tipo,
                    'es_gratuito' => (bool) $curso->es_gratuito,
                    'precio' => $curso->precio,
                    'modalidad' => $curso->modalidad,
                    'ciudad' => $curso->ciudad,
                    'fecha_inicio' => $curso->fecha_inicio?->format('Y-m-d'),
                    'fecha_fin' => $curso->fecha_fin?->format('Y-m-d'),
                    'publicado_desde' => $curso->publicado_desde?->format('Y-m-d H:i:s'),
                    'publicado_hasta' => $curso->publicado_hasta?->format('Y-m-d H:i:s'),
                    'fecha_limite_inscripcion' => $curso->fecha_limite_inscripcion?->format('Y-m-d H:i:s'),
                    'estado' => $curso->estado,
                    'cupos_ilimitados' => (bool) $curso->cupos_ilimitados,
                    'cantidad_cupos' => $curso->cantidad_cupos,
                    'cupos_disponibles' => $cuposDisponibles,
                    'inscripciones_count' => (int) $curso->inscripciones_count,
                    'inscripciones_activas_count' => (int) $curso->inscripciones_activas_count,
                    'intensidad_horaria' => $curso->intensidad_horaria,
                    'url_externa' => $curso->url_externa,
                    'created_at' => $curso->created_at?->format('Y-m-d H:i:s'),
                    'updated_at' => $curso->updated_at?->format('Y-m-d H:i:s'),
                ];
            });

        return Inertia::render('Cursos/MisCursos', [
            'cursos' => $cursos,
        ]);
    }
}
