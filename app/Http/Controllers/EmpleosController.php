<?php

namespace App\Http\Controllers;

use App\Models\EmpresaUsuario;
use App\Models\Favorito;
use App\Models\OfertaEmpleo;
use App\Models\PalabraProhibida;
use App\Models\Postulacion;
use App\Models\PostulacionRespuesta;
use App\Models\Skill;
use App\Rules\SinPalabrasProhibidas;
use App\Services\AiContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class EmpleosController extends Controller
{
    // ==========================================================
    // VISTA PRINCIPAL DE EMPLEOS
    // ==========================================================

    public function index(Request $request)
    {
        $query = OfertaEmpleo::query()
            ->with([
                'usuario:id,name,foto',
                'empresa',
                'skills:id,name,slug',
            ])
            ->where('estado', 'publicada');

        // ---------------------------------------------------------
        // FILTRO GEOESPACIAL POR ALCANCE (RADIO)
        // ---------------------------------------------------------
        $usuarioLogueado = Auth::user();

        if ($usuarioLogueado) {
            $usuarioLogueado->load('informacionRecepcion');
        }

        $infoResidencia = $usuarioLogueado ? $usuarioLogueado->informacionRecepcion : null;

        if ($infoResidencia && $infoResidencia->latitud && $infoResidencia->longitud) {
            $lat = $infoResidencia->latitud;
            $lng = $infoResidencia->longitud;
            $driver = DB::connection()->getDriverName();

            if ($driver === 'sqlite') {
                // SQLite no soporta las funciones trigonométricas nativas requeridas.
                // Para que el entorno local funcione y no oculte las ofertas, simulamos una distancia de 0.
                $query->selectRaw("ofertas_empleo.*, 0 AS distancia_km");
            } else {
                // Fórmula Haversine para MySQL / PostgreSQL
                $haversine = "(6371 * acos(cos(radians(?)) * cos(radians(latitud)) * cos(radians(longitud) - radians(?)) + sin(radians(?)) * sin(radians(latitud))))";

                $query->selectRaw("ofertas_empleo.*, {$haversine} AS distancia_km", [$lat, $lng, $lat])
                    ->where(function ($q) use ($haversine, $lat, $lng) {
                        // Mostrar si NO tiene límite (remoto) o si la distancia calculada es menor/igual al radio
                        $q->whereNull('radio_alcance_km')
                            ->orWhereRaw("{$haversine} <= radio_alcance_km", [$lat, $lng, $lat]);
                    });
            }
        } else {
            // Si el usuario es invitado o no tiene coordenadas configuradas en el Ancla Territorial,
            // solo le mostramos las ofertas que NO tienen límite geográfico (Ej. Remotas).
            $query->whereNull('radio_alcance_km');
            // Como no hubo cálculo, agregamos el campo distancia nulo para evitar errores si la vista lo espera
            $query->selectRaw("ofertas_empleo.*, NULL AS distancia_km");
        }
        // ---------------------------------------------------------
        // ---------------------------------------------------------

        if ($request->filled('busqueda')) {
            $busqueda = trim($request->busqueda);

            $query->where(function ($q) use ($busqueda) {
                $q->where('titulo', 'like', '%' . $busqueda . '%')
                    ->orWhere('descripcion', 'like', '%' . $busqueda . '%')
                    ->orWhere('ciudad', 'like', '%' . $busqueda . '%')
                    ->orWhere('departamento', 'like', '%' . $busqueda . '%')
                    ->orWhereHas('empresa', function ($empresa) use ($busqueda) {
                        $empresa->where('nombre', 'like', '%' . $busqueda . '%');
                    });
            });
        }

        if ($request->filled('pais')) {
            $query->where('pais', $request->pais);
        }

        if ($request->filled('departamento')) {
            $query->where('departamento', $request->departamento);
        }

        if ($request->filled('ciudad')) {
            $query->where('ciudad', $request->ciudad);
        }

        if ($request->filled('modalidad')) {
            $query->where('modalidad', $request->modalidad);
        }

        if ($request->filled('tipo_contrato')) {
            $query->where('tipo_contrato', $request->tipo_contrato);
        }

        if ($request->filled('nivel_experiencia')) {
            $query->where('nivel_experiencia', $request->nivel_experiencia);
        }

        if ($request->filled('skill')) {
            $query->whereHas('skills', function ($q) use ($request) {
                $q->where('skills.id', $request->skill);
            });
        }

        $destacadas = collect();

        // En caso de que se haya agregado un selectRaw, aseguramos el orden correcto
        $ofertas = (clone $query)
            ->latest('fecha_publicacion')
            ->paginate(12)
            ->withQueryString();

        $idsOfertas = $destacadas
            ->pluck('id')
            ->merge($ofertas->getCollection()->pluck('id'))
            ->unique()
            ->values();

        $idsPostuladas = collect();

        if (Auth::check() && $idsOfertas->isNotEmpty()) {
            $idsPostuladas = Postulacion::query()
                ->where('user_id', Auth::id())
                ->whereIn('oferta_empleo_id', $idsOfertas)
                ->pluck('oferta_empleo_id');
        }

        $favoritosIds = [];
        $totalOfertasGuardadas = 0;

        if (Auth::check()) {
            $favoritosIds = Favorito::query()
                ->where('user_id', Auth::id())
                ->where('modulo', 'empleos')
                ->pluck('publicacion_id')
                ->toArray();

            $totalOfertasGuardadas = count($favoritosIds);
        }

        $destacadas->each(function ($oferta) use ($idsPostuladas, $favoritosIds) {
            $oferta->setAttribute('ya_postulado', $idsPostuladas->contains($oferta->id));
            $oferta->setAttribute('es_favorito', in_array($oferta->id, $favoritosIds));
        });

        $ofertas->getCollection()->each(function ($oferta) use ($idsPostuladas, $favoritosIds) {
            $oferta->setAttribute('ya_postulado', $idsPostuladas->contains($oferta->id));
            $oferta->setAttribute('es_favorito', in_array($oferta->id, $favoritosIds));
        });

        $skills = Skill::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $empresasRepresentadas = collect();

        if (Auth::check()) {
            $empresasRepresentadas = EmpresaUsuario::query()
                ->with('empresa')
                ->where('user_id', Auth::id())
                ->where('verificado', true)
                ->get();
        }

        $resumenPostulaciones = [
            'total' => 0,
            'en_revision' => 0,
            'entrevistas' => 0,
            'en_proceso' => 0,
            'finalizadas' => 0,
        ];
        $postulacionesRecientes = collect();

        if (Auth::check()) {
            $userPostulaciones = Postulacion::query()->where('user_id', Auth::id());

            $resumenPostulaciones = [
                'total' => (clone $userPostulaciones)->count(),
                'en_revision' => (clone $userPostulaciones)->where('estado', 'en_revision')->count(),
                'entrevistas' => (clone $userPostulaciones)->where('estado', 'citado')->count(),
                'en_proceso' => (clone $userPostulaciones)->where('estado', 'en_proceso')->count(),
                'finalizadas' => (clone $userPostulaciones)->whereIn('estado', ['seleccionado', 'rechazado', 'retirado'])->count(),
            ];

            $postulacionesRecientes = (clone $userPostulaciones)
                ->with(['ofertaEmpleo.empresa', 'ofertaEmpleo.usuario'])
                ->latest('fecha_postulacion')
                ->take(5)
                ->get();
        }

        return Inertia::render('Empleos/Index', [
            'ofertas' => $ofertas,
            'destacadas' => $destacadas,
            'skills' => $skills,
            'empresasRepresentadas' => $empresasRepresentadas,
            'resumenPostulaciones' => $resumenPostulaciones,
            'postulacionesRecientes' => $postulacionesRecientes,
            'ofertasGuardadas' => $totalOfertasGuardadas,
            'filtros' => [
                'busqueda' => $request->busqueda,
                'pais' => $request->pais,
                'departamento' => $request->departamento,
                'ciudad' => $request->ciudad,
                'modalidad' => $request->modalidad,
                'tipo_contrato' => $request->tipo_contrato,
                'nivel_experiencia' => $request->nivel_experiencia,
                'skill' => $request->skill,
            ],
        ]);
    }

    // ==========================================================
    // DETALLE DE LA OFERTA
    // ==========================================================

    public function show(OfertaEmpleo $ofertaEmpleo)
    {
        $ofertaEmpleo->load([
            'usuario:id,name,foto',
            'empresa',
            'skills:id,name,slug',
        ]);

        $usuario = Auth::user();

        $esPropietario = $usuario
            ? ((int) $ofertaEmpleo->user_id === (int) $usuario->id)
            : false;

        $yaPostulado = false;
        $esFavorito = false;

        if ($usuario) {
            $yaPostulado = Postulacion::query()
                ->where('oferta_empleo_id', $ofertaEmpleo->id)
                ->where('user_id', $usuario->id)
                ->exists();

            $esFavorito = Favorito::query()
                ->where('user_id', $usuario->id)
                ->where('publicacion_id', $ofertaEmpleo->id)
                ->where('modulo', 'empleos')
                ->exists();
        }

        $ofertaEmpleo->setAttribute('es_favorito', $esFavorito);

        $requisitosPerfil = $this->verificarPerfilPostulacion($usuario);
        $experienciaMeses = $this->calcularMesesExperiencia($usuario);
        $experienciaRequerida = (int) ($ofertaEmpleo->experiencia_minima_meses ?? 0);
        $cumpleExperiencia = $experienciaMeses >= $experienciaRequerida;

        $ofertaPublicada = $ofertaEmpleo->estado === 'publicada';

        $puedePostularse = $ofertaPublicada
            && ! $esPropietario
            && ! $yaPostulado
            && $cumpleExperiencia
            && $requisitosPerfil['perfilCompleto'];

        return Inertia::render('Empleos/Show', [
            'oferta' => $ofertaEmpleo,
            'esPropietario' => (bool) $esPropietario,
            'yaPostulado' => (bool) $yaPostulado,
            'puedePostularse' => (bool) $puedePostularse,
            'requisitosPostulacion' => [
                'ofertaPublicada' => $ofertaPublicada,
                'experienciaMeses' => $experienciaMeses,
                'experienciaRequerida' => $experienciaRequerida,
                'cumpleExperiencia' => $cumpleExperiencia,
                'tieneFormacion' => $requisitosPerfil['tieneFormacion'],
                'tieneExperiencia' => $requisitosPerfil['tieneExperiencia'],
                'tieneHojaVida' => $requisitosPerfil['tieneHojaVida'],
                'perfilCompleto' => $requisitosPerfil['perfilCompleto'],
            ],
        ]);
    }

    // ==========================================================
    // POSTULARSE A UNA OFERTA
    // ==========================================================

    public function postular(
        Request $request,
        OfertaEmpleo $ofertaEmpleo
    ) {
        if ($ofertaEmpleo->estado !== 'publicada') {
            return back()->withErrors([
                'postulacion' => 'Esta oferta ya no está disponible para postulaciones.',
            ]);
        }

        $usuario = Auth::user();
        $requisitosPerfil = $this->verificarPerfilPostulacion($usuario);

        if (!$requisitosPerfil['perfilCompleto']) {
            $faltantes = [];

            if (!$requisitosPerfil['tieneFormacion']) {
                $faltantes[] = 'registrar tu formación académica';
            }

            if (!$requisitosPerfil['tieneExperiencia']) {
                $faltantes[] = 'registrar tu experiencia laboral';
            }

            if (!$requisitosPerfil['tieneHojaVida']) {
                $faltantes[] = 'cargar una hoja de vida PDF válida';
            }

            return back()->withErrors([
                'postulacion' => 'Para postularte debes completar tu perfil: '
                    . implode(', ', $faltantes)
                    . '.',
            ]);
        }

        $experienciaMeses = $this->calcularMesesExperiencia($usuario);
        $experienciaRequerida = (int) ($ofertaEmpleo->experiencia_minima_meses ?? 0);

        if ($experienciaMeses < $experienciaRequerida) {
            return back()->withErrors([
                'postulacion' => sprintf(
                    'Esta oferta requiere %d meses de experiencia. Tu perfil registra %d meses.',
                    $experienciaRequerida,
                    $experienciaMeses
                ),
            ]);
        }

        $yaPostulado = Postulacion::query()
            ->where('oferta_empleo_id', $ofertaEmpleo->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($yaPostulado) {
            return back()->withErrors([
                'postulacion' => 'Ya estás postulado a esta oferta.',
            ]);
        }

        $ofertaEmpleo->load([
            'preguntas' => function ($query) {
                $query->orderBy('orden');
            },
        ]);

        $validated = $request->validate([
            'respuestas' => ['nullable', 'array'],
            'respuestas.*' => ['nullable', 'string'],
        ]);

        $respuestas = $validated['respuestas'] ?? [];

        foreach ($ofertaEmpleo->preguntas as $pregunta) {
            if (!$pregunta->obligatoria) {
                continue;
            }

            $respuesta = $respuestas[$pregunta->id] ?? null;

            if ($respuesta === null || trim((string) $respuesta) === '') {
                return back()
                    ->withErrors([
                        "respuestas.{$pregunta->id}" => 'Esta pregunta es obligatoria.',
                    ])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($ofertaEmpleo, $respuestas) {
            $postulacion = Postulacion::create([
                'oferta_empleo_id' => $ofertaEmpleo->id,
                'user_id' => Auth::id(),
                'estado' => 'postulado',
                'fecha_postulacion' => now(),
            ]);

            foreach ($ofertaEmpleo->preguntas as $pregunta) {
                $respuesta = $respuestas[$pregunta->id] ?? null;

                if ($respuesta === null || trim((string) $respuesta) === '') {
                    continue;
                }

                PostulacionRespuesta::create([
                    'postulacion_id' => $postulacion->id,
                    'pregunta_id' => $pregunta->id,
                    'respuesta' => trim((string) $respuesta),
                ]);
            }
        });

        return redirect()
            ->route('empleos.show', $ofertaEmpleo)
            ->with(
                'success',
                'Tu postulación fue enviada correctamente.'
            );
    }

    // ==========================================================
    // FORMULARIO PARA CREAR OFERTA
    // ==========================================================
    public function create()
    {
        $usuario = Auth::user();
        $usuario->load('informacionRecepcion'); // Cargamos la residencia

        $empresasRepresentadas = EmpresaUsuario::query()
            ->with('empresa')
            ->where('user_id', $usuario->id)
            ->where('verificado', true)
            ->get();

        $skills = Skill::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $palabrasProhibidas = Cache::remember('palabras_prohibidas_list', 3600, function () {
            return PalabraProhibida::pluck('palabra')->toArray();
        });

        return Inertia::render(
            'Empleos/Create',
            [
                'empresasRepresentadas' => $empresasRepresentadas,
                'skills' => $skills,
                'palabrasProhibidas' => $palabrasProhibidas,
                'informacion_recepcion' => $usuario->informacionRecepcion, // Pasamos el ancla territorial
            ]
        );
    }

    // ==========================================================
    // PUBLICAR / GUARDAR NUEVA OFERTA
    // ==========================================================
    public function publicarOferta(Request $request, AiContentService $aiService)
    {
        $validated = $request->validate([
            'tipo_publicador' => ['required', 'in:persona,empresa'],
            'empresa_id' => ['nullable', 'exists:empresas,id'],
            'cargo_publicador' => ['nullable', 'string', 'max:255'],
            'correo_corporativo' => ['nullable', 'email', 'max:255'],

            'titulo' => ['required', 'string', 'max:255', new SinPalabrasProhibidas],
            'descripcion' => ['required', 'string', new SinPalabrasProhibidas],

            'pais' => ['required', 'string', 'max:100'],
            'departamento' => ['nullable', 'string', 'max:100'],
            'ciudad' => ['nullable', 'string', 'max:100'],

            'direccion' => ['nullable', 'string', 'max:255'],
            'mostrar_direccion' => ['nullable', 'boolean'],
            'modalidad' => ['required', 'in:Presencial,Híbrido,Remoto'],
            'radio_alcance_km' => ['nullable', 'numeric', 'min:1', 'max:100'],

            'tipo_contrato' => ['nullable', 'string', 'max:50'],
            'nivel_experiencia' => ['nullable', 'string', 'max:50'],
            'experiencia_minima_meses' => ['required', 'integer', 'min:0', 'max:65535'],
            'salario_es_smlv' => ['nullable', 'boolean'],
            'salario_monto' => ['nullable', 'numeric', 'min:0'],
            'salario_a_convenir' => ['nullable', 'boolean'],
            'mostrar_salario' => ['nullable', 'boolean'],
            'incluye_subsidio_transporte' => ['nullable', 'boolean'],
            'incluye_prestaciones_ley' => ['nullable', 'boolean'],
            'incluye_comisiones' => ['nullable', 'boolean'],
            'descripcion_comisiones' => ['nullable', 'string'],
            'incluye_bonificaciones' => ['nullable', 'boolean'],
            'descripcion_bonificaciones' => ['nullable', 'string'],
            'incluye_beneficios_adicionales' => ['nullable', 'boolean'],
            'descripcion_beneficios_adicionales' => ['nullable', 'string'],
            'skills' => ['nullable', 'array'],
            'skills.*.id' => ['required', 'exists:skills,id'],
            'skills.*.nivel_requerido' => ['nullable', 'string', 'max:50'],
            'skills.*.es_obligatorio' => ['nullable', 'boolean'],
            'preguntas' => ['nullable', 'array'],
        ]);

        $usuario = Auth::user();
        $usuario->load('informacionRecepcion');
        $infoResidencia = $usuario->informacionRecepcion;

        $latitud = null;
        $longitud = null;
        $radioAlcance = null;
        $mostrarDireccion = $validated['mostrar_direccion'] ?? false;

        // ---------------------------------------------------------
        // VALIDACIÓN GEOESPACIAL SEGÚN MODALIDAD
        // ---------------------------------------------------------
        if (in_array($validated['modalidad'], ['Presencial', 'Híbrido'])) {
            // Verificamos en el Ancla Territorial
            if (!$infoResidencia || !$infoResidencia->latitud || !$infoResidencia->longitud) {
                return back()->withErrors([
                    'modalidad' => 'Debes registrar tu ubicación exacta en el mapa antes de publicar ofertas presenciales o híbridas.'
                ]);
            }

            // "Congelamos" las coordenadas del Ancla Territorial a la oferta
            $latitud = $infoResidencia->latitud;
            $longitud = $infoResidencia->longitud;
            $radioAlcance = $validated['radio_alcance_km'] ?? 15;
        } else {
            $validated['direccion'] = null;
            $mostrarDireccion = false;
        }

        $tipoSalario = null;
        $salarioMonto = null;

        if (!empty($validated['salario_a_convenir'])) {
            $tipoSalario = 'a_convenir';
        } elseif (!empty($validated['salario_es_smlv'])) {
            $tipoSalario = 'smlmv';
        } elseif (isset($validated['salario_monto']) && $validated['salario_monto'] !== null && $validated['salario_monto'] !== '') {
            $tipoSalario = 'salario_especifico';
            $salarioMonto = $validated['salario_monto'];
        }

        $modTitulo = $aiService->magicalFix($validated['titulo'], 'oferta_empleo_titulo');
        $modDesc = $aiService->magicalFix($validated['descripcion'], 'oferta_empleo_descripcion');

        $tituloFinal = $modTitulo['improved_text'] ?? $validated['titulo'];
        $descripcionFinal = $modDesc['improved_text'] ?? $validated['descripcion'];

        $oferta = OfertaEmpleo::create([
            'user_id' => Auth::id(),
            'empresa_id' => $validated['empresa_id'] ?? null,
            'titulo' => $tituloFinal,
            'descripcion' => $descripcionFinal,
            'pais' => $validated['pais'],
            'departamento' => $validated['departamento'] ?? null,
            'ciudad' => $validated['ciudad'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
            'mostrar_direccion' => $mostrarDireccion,

            // CAMPOS GEO
            'latitud' => $latitud,
            'longitud' => $longitud,
            'radio_alcance_km' => $radioAlcance,

            'modalidad' => $validated['modalidad'],
            'tipo_contrato' => $validated['tipo_contrato'] ?? null,
            'nivel_experiencia' => $validated['nivel_experiencia'] ?? null,
            'experiencia_minima_meses' => (int) $validated['experiencia_minima_meses'],
            'tipo_salario' => $tipoSalario,
            'salario_monto' => $salarioMonto,
            'mostrar_salario' => $validated['mostrar_salario'] ?? true,
            'incluye_subsidio_transporte' => $validated['incluye_subsidio_transporte'] ?? false,
            'incluye_prestaciones_ley' => $validated['incluye_prestaciones_ley'] ?? false,
            'incluye_comisiones' => $validated['incluye_comisiones'] ?? false,
            'descripcion_comisiones' => $validated['descripcion_comisiones'] ?? null,
            'incluye_bonificaciones' => $validated['incluye_bonificaciones'] ?? false,
            'descripcion_bonificaciones' => $validated['descripcion_bonificaciones'] ?? null,
            'incluye_beneficios_adicionales' => $validated['incluye_beneficios_adicionales'] ?? false,
            'descripcion_beneficios_adicionales' => $validated['descripcion_beneficios_adicionales'] ?? null,
            'estado' => 'publicada',
            'fecha_publicacion' => now(),
            'fecha_cierre' => null,
        ]);

        if (!empty($validated['skills'])) {
            $skills = [];

            foreach ($validated['skills'] as $skill) {
                $skills[$skill['id']] = [
                    'nivel_requerido' => $skill['nivel_requerido'] ?? null,
                    'es_obligatorio' => $skill['es_obligatorio'] ?? false,
                ];
            }

            $oferta->skills()->sync($skills);
        }

        return redirect()
            ->route('empleos.index')
            ->with('success', 'La oferta de empleo fue publicada correctamente.');
    }

    // ==========================================================
    // MIS OFERTAS
    // ==========================================================

    public function misOfertas()
    {
        $userId = Auth::id();

        $consulta = OfertaEmpleo::query()
            ->where('user_id', $userId);

        $estadosGestionados = [
            'en_revision',
            'citado',
            'en_proceso',
            'seleccionado',
            'rechazado',
            'retirado',
        ];

        $totalOfertas = (clone $consulta)->count();

        $totalVisualizaciones = (clone $consulta)
            ->sum('visualizaciones');

        $ofertasConConteo = (clone $consulta)
            ->withCount([
                'postulaciones',
                'postulaciones as postulaciones_gestionadas_count' => function ($query) use ($estadosGestionados) {
                    $query->whereIn('estado', $estadosGestionados);
                },
            ])
            ->get(['id']);

        $totalPostulaciones = $ofertasConConteo->sum('postulaciones_count');
        $totalPostulacionesGestionadas = $ofertasConConteo->sum('postulaciones_gestionadas_count');
        $totalOfertasConPostulaciones = $ofertasConConteo->where('postulaciones_count', '>', 0)->count();

        $ofertas = (clone $consulta)
            ->with(['empresa', 'skills'])
            ->withCount([
                'postulaciones',
                'postulaciones as postulaciones_gestionadas_count' => function ($query) use ($estadosGestionados) {
                    $query->whereIn('estado', $estadosGestionados);
                },
            ])
            ->latest('fecha_publicacion')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Empleos/MisOfertas', [
            'ofertas' => $ofertas,
            'estadisticas' => [
                'totalOfertas' => $totalOfertas,
                'totalVisualizaciones' => $totalVisualizaciones,
                'totalPostulaciones' => $totalPostulaciones,
                'totalPostulacionesGestionadas' => $totalPostulacionesGestionadas,
                'totalOfertasConPostulaciones' => $totalOfertasConPostulaciones,
            ],
        ]);
    }

    // ==========================================================
    // MIS POSTULACIONES
    // ==========================================================

    public function misPostulaciones()
    {
        $postulaciones = Auth::user()
            ->postulaciones()
            ->with([
                'ofertaEmpleo.empresa',
                'ofertaEmpleo.skills',
            ])
            ->latest('fecha_postulacion')
            ->paginate(12);

        return Inertia::render(
            'Empleos/MisPostulaciones',
            [
                'postulaciones' => $postulaciones,
            ]
        );
    }

    // ==========================================================
    // PUBLICAR / GUARDAR NUEVA OFERTA
    // ==========================================================

    
    private function calcularMesesExperiencia($usuario): int
    {
        if (!$usuario) return 0;

        $experiencias = $usuario->experiencias()
            ->whereNotNull('fecha_inicio')
            ->get();

        $periodos = [];

        foreach ($experiencias as $experiencia) {
            $inicio = \Carbon\Carbon::parse(
                $experiencia->fecha_inicio
            )->startOfDay();

            $fin = $experiencia->actualmente_trabaja
                ? now()->startOfDay()
                : ($experiencia->fecha_fin
                    ? \Carbon\Carbon::parse($experiencia->fecha_fin)->startOfDay()
                    : null
                );

            if (!$fin || $fin->lt($inicio)) {
                continue;
            }

            $periodos[] = [
                'inicio' => $inicio,
                'fin' => $fin,
            ];
        }

        if (empty($periodos)) {
            return 0;
        }

        usort($periodos, function ($a, $b) {
            return $a['inicio']->timestamp <=> $b['inicio']->timestamp;
        });

        $unidos = [];
        $actual = array_shift($periodos);

        foreach ($periodos as $periodo) {
            if ($periodo['inicio']->lte($actual['fin'])) {
                if ($periodo['fin']->gt($actual['fin'])) {
                    $actual['fin'] = $periodo['fin'];
                }

                continue;
            }

            $unidos[] = $actual;
            $actual = $periodo;
        }

        $unidos[] = $actual;

        $meses = 0;

        foreach ($unidos as $periodo) {
            $meses += (int) $periodo['inicio']->diffInMonths($periodo['fin']);
        }

        return $meses;
    }

    private function verificarPerfilPostulacion($usuario): array
    {
        if (!$usuario) {
            return [
                'tieneFormacion' => false,
                'tieneExperiencia' => false,
                'tieneHojaVida' => false,
                'perfilCompleto' => false,
            ];
        }

        $tieneFormacion = $usuario->formaciones()->exists();
        $tieneExperiencia = $usuario->experiencias()->exists();
        $hojaVidaPrincipal = $usuario->hojaVidaPrincipal;

        $tieneHojaVida = $hojaVidaPrincipal !== null
            && !empty($hojaVidaPrincipal->archivo)
            && Storage::disk('local')->exists($hojaVidaPrincipal->archivo);

        return [
            'tieneFormacion' => $tieneFormacion,
            'tieneExperiencia' => $tieneExperiencia,
            'tieneHojaVida' => $tieneHojaVida,
            'perfilCompleto' => $tieneFormacion && $tieneExperiencia && $tieneHojaVida,
        ];
    }

    public function edit(OfertaEmpleo $ofertaEmpleo)
    {
        abort_unless(
            $ofertaEmpleo->user_id === auth()->id(),
            403,
            'No tienes permiso para editar esta oferta.'
        );

        return Inertia::render('Empleos/Edit', [
            'oferta' => $ofertaEmpleo,
        ]);
    }

    public function update(
        Request $request,
        OfertaEmpleo $ofertaEmpleo
    ) {
        abort_unless(
            $ofertaEmpleo->user_id === auth()->id(),
            403,
            'No tienes permiso para editar esta oferta.'
        );

        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:255', new SinPalabrasProhibidas],
            'descripcion' => ['required', 'string', new SinPalabrasProhibidas],
            'modalidad' => ['required', 'in:Presencial,Híbrido,Remoto'],
            'salario_min' => ['nullable', 'numeric', 'min:0'],
            'salario_max' => ['nullable', 'numeric', 'min:0', 'gte:salario_min'],
            'radio_alcance_km' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'mostrar_direccion' => ['nullable', 'boolean'],
        ]);

        // Si actualizan a Presencial y no tenían alcance, ponemos lógica similar
        if (in_array($datos['modalidad'], ['Presencial', 'Híbrido'])) {
            $usuario = Auth::user();
            if ($usuario->latitud && $usuario->longitud) {
                $datos['latitud'] = $usuario->latitud;
                $datos['longitud'] = $usuario->longitud;
                $datos['radio_alcance_km'] = $datos['radio_alcance_km'] ?? 15;
            }
        } else {
            $datos['latitud'] = null;
            $datos['longitud'] = null;
            $datos['radio_alcance_km'] = null;
            $datos['mostrar_direccion'] = false;
        }

        $ofertaEmpleo->update($datos);

        return redirect()
            ->route('empleos.mis-ofertas')
            ->with('success', 'Oferta actualizada correctamente.');
    }

    public function postulantes(OfertaEmpleo $ofertaEmpleo)
    {
        abort_unless(
            (int) $ofertaEmpleo->user_id === (int) Auth::id(),
            403
        );

        $postulaciones = Postulacion::query()
            ->where('oferta_empleo_id', $ofertaEmpleo->id)
            ->with(['user.hojaVidaPrincipal'])
            ->latest('fecha_postulacion')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Empleos/Postulantes', [
            'oferta' => $ofertaEmpleo->only(['id', 'titulo']),
            'postulaciones' => $postulaciones,
        ]);
    }

    public function verHojaVidaPostulante(
        OfertaEmpleo $ofertaEmpleo,
        Postulacion $postulacion
    ) {
        abort_unless(
            (int) $ofertaEmpleo->user_id === (int) Auth::id(),
            403
        );

        abort_unless(
            (int) $postulacion->oferta_empleo_id === (int) $ofertaEmpleo->id,
            404
        );

        $postulacion->load('user.hojaVidaPrincipal');
        $hojaVida = $postulacion->user?->hojaVidaPrincipal;

        abort_unless(
            $hojaVida &&
                !empty($hojaVida->archivo) &&
                Storage::disk('local')->exists($hojaVida->archivo),
            404,
            'El postulante no tiene una hoja de vida disponible.'
        );

        return Storage::disk('local')->response(
            $hojaVida->archivo,
            'hoja-vida-postulante-' . $postulacion->user_id . '.pdf',
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline',
            ]
        );
    }

    public function actualizarPostulacion(
        Request $request,
        OfertaEmpleo $ofertaEmpleo,
        Postulacion $postulacion
    ) {
        abort_unless(
            (int) $ofertaEmpleo->user_id === (int) Auth::id(),
            403,
            'No tienes permiso para gestionar esta oferta.'
        );

        abort_unless(
            (int) $postulacion->oferta_empleo_id === (int) $ofertaEmpleo->id,
            404,
            'La postulación no pertenece a esta oferta.'
        );

        $datos = $request->validate([
            'estado' => [
                'required',
                'string',
                'in:pendiente,en_revision,citado,en_proceso,seleccionado,rechazado,retirado',
            ],
            'observaciones' => ['nullable', 'string', 'max:5000'],
        ]);

        $postulacion->update([
            'estado' => $datos['estado'],
            'observaciones' => $datos['observaciones'] ?? null,
        ]);

        return back()->with(
            'success',
            'La gestión de la postulación se registró correctamente.'
        );
    }

    public function cerrarOferta(
        Request $request,
        OfertaEmpleo $ofertaEmpleo
    ) {
        abort_unless(
            (int) $ofertaEmpleo->user_id === (int) Auth::id(),
            403,
            'No tienes permiso para cerrar esta oferta.'
        );

        if ($ofertaEmpleo->estado !== 'publicada') {
            return back()->withErrors([
                'cierre' => 'Esta oferta ya no se encuentra publicada.',
            ]);
        }

        $datos = $request->validate([
            'estado' => ['required', 'in:cubierta,cancelada'],
        ]);

        $ofertaEmpleo->estado = $datos['estado'];
        $ofertaEmpleo->fecha_cierre = now();
        $ofertaEmpleo->saveOrFail();
        $ofertaEmpleo->refresh();

        $mensaje = $datos['estado'] === 'cubierta'
            ? 'La oferta se cerró porque la vacante fue cubierta.'
            : 'La oferta se canceló correctamente.';

        return redirect()
            ->route('empleos.mis-ofertas')
            ->with('success', $mensaje);
    }
}
