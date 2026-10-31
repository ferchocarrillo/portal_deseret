<?php

namespace App\Http\Controllers\Clasificado;

use App\Http\Controllers\Controller;
use App\Models\Clasificado;
use App\Models\ClasificadoCategoria;
use App\Models\Favorito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ClasificadoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Listado con Segmentación Territorial (Estilo Marketplace)
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = Auth::user();

        // Determinar ubicación de referencia (filtro activo o datos del perfil del usuario)
        $ciudadReferenciaId = $request->input('ciudad_id') ?? $user?->ciudad_id;
        $departamentoReferenciaId = $request->input('division_administrativa_id') ?? $user?->division_administrativa_id;

        $clasificados = Clasificado::query()
            ->with([
                'categoria',
                'imagenes' => function ($query) {
                    $query->where('es_principal', true);
                },
                'ciudad',
                'divisionAdministrativa',
                'usuario',
                'propiedad',
            ])
            ->where('estado', 'publicado')

            // Segmentación territorial estricta pero flexible
            ->where(function ($query) use ($ciudadReferenciaId, $departamentoReferenciaId, $user) {

                // 1. El autor SIEMPRE puede ver sus propias publicaciones
                if ($user) {
                    $query->orWhere('user_id', $user->id);
                }

                // 2. Cobertura nacional: visible para todos
                $query->orWhere('rango_cobertura', 'nacional');

                // 3. Cobertura departamental: visible solo si coincide el departamento
                if ($departamentoReferenciaId) {
                    $query->orWhere(function ($sub) use ($departamentoReferenciaId) {
                        $sub->where('rango_cobertura', 'departamental')
                            ->where('division_administrativa_id', $departamentoReferenciaId);
                    });
                }

                // 4. Cobertura local: visible EXCLUSIVAMENTE si coincide de forma exacta la ciudad
                if ($ciudadReferenciaId) {
                    $query->orWhere(function ($sub) use ($ciudadReferenciaId) {
                        $sub->where('rango_cobertura', 'local')
                            ->where('ciudad_id', $ciudadReferenciaId);
                    });
                }

                // 5. Comodín de seguridad para publicaciones antiguas sin rango de cobertura
                $query->orWhereNull('rango_cobertura');
            })
            ->when(
                $request->filled('buscar'),
                function ($query) use ($request) {
                    $buscar = $request->input('buscar');

                    $query->where(function ($q) use ($buscar) {
                        $q->where('titulo', 'like', "%{$buscar}%")
                            ->orWhere('descripcion', 'like', "%{$buscar}%");
                    });
                }
            )
            ->when(
                $request->filled('intencion'),
                fn($query) =>
                $query->where(
                    'intencion',
                    $request->input('intencion')
                )
            )
            ->when(
                $request->filled('operacion'),
                fn($query) =>
                $query->where(
                    'operacion',
                    $request->input('operacion')
                )
            )
            ->when(
                $request->filled('categoria_id'),
                fn($query) =>
                $query->where(
                    'categoria_id',
                    $request->input('categoria_id')
                )
            )
            ->when(
                $request->filled('ciudad_id'),
                fn($query) =>
                $query->where(
                    'ciudad_id',
                    $request->input('ciudad_id')
                )
            )
            ->latest('fecha_publicacion')
            ->paginate(12)
            ->withQueryString();

        // ==========================================================
        // ADJUNTAR EL ESTADO DE FAVORITO PARA CADA CLASIFICADO
        // ==========================================================
        if (Auth::check()) {
            $clasificados->getCollection()->transform(function ($clasificado) {
                $esFavorito = Favorito::where('user_id', Auth::id())
                    ->where('publicacion_id', $clasificado->id)
                    ->where('modulo', 'clasificados')
                    ->exists();

                $clasificado->setAttribute('es_favorito', $esFavorito);
                return $clasificado;
            });
        }

        $categorias = ClasificadoCategoria::query()
            ->whereNull('parent_id')
            ->where('activo', true)
            ->with([
                'hijas' => function ($query) {
                    $query->where('activo', true)
                        ->with([
                            'hijas' => function ($query) {
                                $query->where('activo', true);
                            },
                        ]);
                },
            ])
            ->orderBy('orden')
            ->get();

        return Inertia::render('Clasificados/Index', [
            'clasificados' => $clasificados,
            'categorias' => $categorias,
            'filtros' => [
                'buscar' => $request->input('buscar'),
                'intencion' => $request->input('intencion'),
                'operacion' => $request->input('operacion'),
                'categoria_id' => $request->input('categoria_id'),
                'ciudad_id' => $request->input('ciudad_id'),
                'rango_cobertura' => $request->input('rango_cobertura'),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Formulario de creación
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $user = Auth::user();

        $paisNombre = 'Colombia';
        $departamentoNombre = '';
        $ciudadNombre = '';

        if ($user) {
            if ($user->ciudad_id) {
                $ciudad = \App\Models\Ciudad::with('divisionAdministrativa.pais')->find($user->ciudad_id);
                if ($ciudad) {
                    $ciudadNombre = $ciudad->nombre ?? '';
                    $departamentoNombre = $ciudad->divisionAdministrativa?->nombre ?? '';
                    $paisNombre = $ciudad->divisionAdministrativa?->pais?->nombre ?? 'Colombia';
                }
            } elseif ($user->division_administrativa_id) {
                $depto = \App\Models\DivisionAdministrativa::with('pais')->find($user->division_administrativa_id);
                if ($depto) {
                    $departamentoNombre = $depto->nombre ?? '';
                    $paisNombre = $depto->pais?->nombre ?? 'Colombia';
                }
            }
        }

        $ubicacionDefecto = [
            'pais_id'                    => $user?->pais_id,
            'division_administrativa_id' => $user?->division_administrativa_id,
            'ciudad_id'                  => $user?->ciudad_id,
            'pais'                       => $paisNombre,
            'departamento'               => $departamentoNombre,
            'ciudad'                     => $ciudadNombre,
        ];

        $categorias = ClasificadoCategoria::query()
            ->whereNull('parent_id')
            ->where('activo', true)
            ->with([
                'hijas' => function ($query) {
                    $query->where('activo', true)
                        ->with(['hijas' => fn($q) => $q->where('activo', true)]);
                },
            ])
            ->orderBy('orden')
            ->get();

        return Inertia::render('Clasificados/Create', [
            'categorias'       => $categorias,
            'ubicacionDefecto' => $ubicacionDefecto,
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | Guardar publicación
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => [
                'required',
                'exists:clasificado_categorias,id',
            ],
            'intencion' => [
                'required',
                'in:ofrecer,buscar',
            ],
            'operacion' => [
                'required',
                'in:venta,arriendo',
            ],
            'titulo' => [
                'required',
                'string',
                'max:255',
            ],
            'descripcion' => [
                'required',
                'string',
            ],
            'precio' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'pais_id' => [
                'nullable',
                'exists:paises,id',
            ],
            'division_administrativa_id' => [
                'nullable',
                'exists:divisiones_administrativas,id',
            ],
            'ciudad_id' => [
                'nullable',
                'exists:ciudades,id',
            ],
            'rango_cobertura' => [
                'required',
                'in:local,departamental,nacional',
            ],

            // Características de vehículos
            'marca' => ['nullable', 'string', 'max:255'],
            'modelo' => ['nullable', 'string', 'max:255'],
            'anio' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'kilometraje' => ['nullable', 'integer', 'min:0'],
            'cilindraje' => ['nullable', 'integer', 'min:0'],
            'combustible' => ['nullable', 'string', 'max:50'],
            'transmision' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'placa' => ['nullable', 'string', 'max:20'],
            'unico_propietario' => ['nullable', 'boolean'],
            'documentacion_al_dia' => ['nullable', 'boolean'],
            'vehiculo_observaciones' => ['nullable', 'string'],

            // Características de propiedades - Generales
            'area_total' => ['nullable', 'numeric', 'min:0'],
            'area_construida' => ['nullable', 'numeric', 'min:0'],
            'habitaciones' => ['nullable', 'integer', 'min:0'],
            'banos' => ['nullable', 'integer', 'min:0'],
            'garajes' => ['nullable', 'integer', 'min:0'],
            'pisos' => ['nullable', 'integer', 'min:0'],
            'piso' => ['nullable', 'integer'],
            'estrato' => ['nullable', 'string', 'max:20'],
            'antiguedad' => ['nullable', 'string', 'max:50'],
            'remodelada' => ['nullable', 'boolean'],
            'propiedad_observaciones' => ['nullable', 'string'],

            // Propiedades - Administración (arriendos)
            'incluye_administracion' => ['nullable', 'boolean'],
            'precio_administracion' => ['nullable', 'numeric', 'min:0'],

            // Propiedades - Servicios básicos y documentos
            'tiene_servicios_publicos' => ['nullable', 'boolean'],
            'tiene_agua' => ['nullable', 'boolean'],
            'tiene_energia' => ['nullable', 'boolean'],
            'tiene_gas' => ['nullable', 'boolean'],
            'tiene_alcantarillado' => ['nullable', 'boolean'],
            'tiene_internet' => ['nullable', 'boolean'],
            'tiene_tv' => ['nullable', 'boolean'],
            'papeles_al_dia' => ['nullable', 'boolean'],
            'libre_gravamen' => ['nullable', 'boolean'],

            // Propiedades - Zonas comunes y amenidades
            'tiene_ascensor' => ['nullable', 'boolean'],
            'tiene_parqueadero' => ['nullable', 'boolean'],
            'tiene_gimnasio' => ['nullable', 'boolean'],
            'tiene_piscina' => ['nullable', 'boolean'],
            'tiene_bbq' => ['nullable', 'boolean'],
            'tiene_salon_comunal' => ['nullable', 'boolean'],
            'tiene_zona_infantil' => ['nullable', 'boolean'],
            'tiene_zonas_verdes' => ['nullable', 'boolean'],
            'tiene_cancha_multiple' => ['nullable', 'boolean'],
            'tiene_sauna_turco' => ['nullable', 'boolean'],
            'tiene_coworking' => ['nullable', 'boolean'],
            'tiene_parqueadero_visitantes' => ['nullable', 'boolean'],

            // Propiedades - Seguridad, interiores y políticas
            'tiene_porteria_24_7' => ['nullable', 'boolean'],
            'tiene_circuito_cerrado' => ['nullable', 'boolean'],
            'permite_mascotas' => ['nullable', 'boolean'],
            'tiene_balcon' => ['nullable', 'boolean'],
            'tiene_terraza' => ['nullable', 'boolean'],
            'tiene_deposito' => ['nullable', 'boolean'],
            'tiene_calentador' => ['nullable', 'boolean'],
            'tiene_aire_acondicionado' => ['nullable', 'boolean'],
            'cocina_integral' => ['nullable', 'boolean'],
            'zona_lavanderia' => ['nullable', 'boolean'],

            // Imágenes
            'imagenes' => [
                'nullable',
                'array',
                'max:8',
            ],
            'imagenes.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $categoria = ClasificadoCategoria::with('padre')
            ->findOrFail($validated['categoria_id']);

        $tipo = $this->determinarTipoClasificado($categoria);

        if (!$tipo) {
            return back()
                ->withErrors([
                    'categoria_id' => 'La categoría seleccionada no corresponde a un vehículo ni a una propiedad.',
                ])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $slugBase = Str::slug($validated['titulo']);
            $slug = $this->generarSlugUnico($slugBase);

            $clasificado = Clasificado::create([
                'user_id' => Auth::id(),
                'categoria_id' => $validated['categoria_id'],
                'intencion' => $validated['intencion'],
                'operacion' => $validated['operacion'],
                'titulo' => $validated['titulo'],
                'slug' => $slug,
                'codigo_global' => Clasificado::generarCodigoGlobal(),
                'descripcion' => $validated['descripcion'],
                'precio' => $validated['precio'] ?? null,
                'pais_id' => $validated['pais_id'] ?? null,
                'division_administrativa_id' => $validated['division_administrativa_id'] ?? null,
                'ciudad_id' => $validated['ciudad_id'] ?? null,
                'rango_cobertura' => $validated['rango_cobertura'] ?? 'departamental',
                'estado' => 'publicado',
                'fecha_publicacion' => now(),
            ]);

            // Guardar datos específicos si es vehículo
            if ($tipo === 'vehiculo') {
                $datosVehiculo = $request->only([
                    'marca',
                    'modelo',
                    'anio',
                    'kilometraje',
                    'cilindraje',
                    'combustible',
                    'transmision',
                    'color',
                    'placa',
                    'unico_propietario',
                    'documentacion_al_dia',
                ]);
                $datosVehiculo['observaciones'] = $request->input('vehiculo_observaciones');

                $clasificado->vehiculo()->create($datosVehiculo);
            }

            // Guardar datos específicos si es propiedad con todas las amenidades
            if ($tipo === 'propiedad') {
                $datosPropiedad = $request->only([
                    'area_total',
                    'area_construida',
                    'habitaciones',
                    'banos',
                    'garajes',
                    'pisos',
                    'piso',
                    'estrato',
                    'antiguedad',
                    'remodelada',
                    'tiene_servicios_publicos',
                    'tiene_agua',
                    'tiene_energia',
                    'tiene_gas',
                    'tiene_alcantarillado',
                    'tiene_internet',
                    'tiene_tv',
                    'papeles_al_dia',
                    'libre_gravamen',
                    'incluye_administracion',
                    'precio_administracion',
                    'tiene_ascensor',
                    'tiene_parqueadero',
                    'tiene_gimnasio',
                    'tiene_piscina',
                    'tiene_bbq',
                    'tiene_salon_comunal',
                    'tiene_zona_infantil',
                    'tiene_zonas_verdes',
                    'tiene_cancha_multiple',
                    'tiene_sauna_turco',
                    'tiene_coworking',
                    'tiene_parqueadero_visitantes',
                    'tiene_porteria_24_7',
                    'tiene_circuito_cerrado',
                    'permite_mascotas',
                    'tiene_balcon',
                    'tiene_terraza',
                    'tiene_deposito',
                    'tiene_calentador',
                    'tiene_aire_acondicionado',
                    'cocina_integral',
                    'zona_lavanderia',
                ]);
                $datosPropiedad['observaciones'] = $request->input('propiedad_observaciones');

                $clasificado->propiedad()->create($datosPropiedad);
            }

            // Guardar imágenes
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $orden => $imagen) {
                    $ruta = $imagen->store(
                        'clasificados/' . $clasificado->id . '/imagenes',
                        'public'
                    );

                    $clasificado->imagenes()->create([
                        'ruta' => $ruta,
                        'orden' => $orden,
                        'es_principal' => $orden === 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('clasificados.show', $clasificado)
                ->with('success', 'El clasificado fue publicado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error creando clasificado: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());

            return back()
                ->withErrors([
                    'general' => 'No fue posible publicar el clasificado: ' . $e->getMessage(),
                ])
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Mostrar publicación
    |--------------------------------------------------------------------------
    */

    public function show(Clasificado $clasificado)
    {
        $clasificado->load([
            'usuario',
            'categoria.padre',
            'pais',
            'divisionAdministrativa',
            'ciudad',
            'vehiculo',
            'propiedad',
            'imagenes' => function ($query) {
                $query->orderBy('orden');
            },
        ]);

        if (
            $clasificado->estado !== 'publicado' &&
            $clasificado->user_id !== Auth::id()
        ) {
            abort(404);
        }

        // ==========================================================
        // INYECTAR EL ESTADO DE FAVORITO PARA EL USUARIO AUTENTICADO
        // ==========================================================
        if (Auth::check()) {
            $esFavorito = Favorito::where('user_id', Auth::id())
                ->where('publicacion_id', $clasificado->id)
                ->where('modulo', 'clasificados')
                ->exists();

            $clasificado->setAttribute('es_favorito', $esFavorito);
        } else {
            $clasificado->setAttribute('es_favorito', false);
        }

        return Inertia::render(
            'Clasificados/Show',
            [
                'clasificado' => $clasificado,
                'esPropietario' => Auth::id() === $clasificado->user_id,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Formulario de edición
    |--------------------------------------------------------------------------
    */

    public function edit(Clasificado $clasificado)
    {
        $this->autorizarPropietario($clasificado);

        $clasificado->load([
            'categoria',
            'pais',
            'divisionAdministrativa',
            'ciudad',
            'vehiculo',
            'propiedad',
            'imagenes' => function ($query) {
                $query->orderBy('orden');
            },
        ]);

        $categorias = ClasificadoCategoria::query()
            ->whereNull('parent_id')
            ->where('activo', true)
            ->with([
                'hijas' => function ($query) {
                    $query->where('activo', true)
                        ->with([
                            'hijas' => function ($query) {
                                $query->where('activo', true);
                            },
                        ]);
                },
            ])
            ->orderBy('orden')
            ->get();

        return Inertia::render('Clasificados/Edit', [
            'clasificado' => $clasificado,
            'categorias' => $categorias,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar publicación
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Clasificado $clasificado
    ) {
        $this->autorizarPropietario($clasificado);

        $validated = $request->validate([
            'categoria_id' => [
                'required',
                'exists:clasificado_categorias,id',
            ],
            'intencion' => [
                'required',
                'in:ofrecer,buscar',
            ],
            'operacion' => [
                'required',
                'in:venta,arriendo',
            ],
            'titulo' => [
                'required',
                'string',
                'max:255',
            ],
            'descripcion' => [
                'required',
                'string',
            ],
            'precio' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'pais_id' => [
                'nullable',
                'exists:paises,id',
            ],
            'division_administrativa_id' => [
                'nullable',
                'exists:divisiones_administrativas,id',
            ],
            'ciudad_id' => [
                'nullable',
                'exists:ciudades,id',
            ],
            'rango_cobertura' => [
                'required',
                'in:local,departamental,nacional',
            ],

            // Características de vehículos
            'marca' => ['nullable', 'string', 'max:255'],
            'modelo' => ['nullable', 'string', 'max:255'],
            'anio' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'kilometraje' => ['nullable', 'integer', 'min:0'],
            'cilindraje' => ['nullable', 'integer', 'min:0'],
            'combustible' => ['nullable', 'string', 'max:50'],
            'transmision' => ['nullable', 'string', 'max:50'],
            'color' => ['nullable', 'string', 'max:50'],
            'placa' => ['nullable', 'string', 'max:20'],
            'unico_propietario' => ['nullable', 'boolean'],
            'documentacion_al_dia' => ['nullable', 'boolean'],
            'vehiculo_observaciones' => ['nullable', 'string'],

            // Características de propiedades - Generales
            'area_total' => ['nullable', 'numeric', 'min:0'],
            'area_construida' => ['nullable', 'numeric', 'min:0'],
            'habitaciones' => ['nullable', 'integer', 'min:0'],
            'banos' => ['nullable', 'integer', 'min:0'],
            'garajes' => ['nullable', 'integer', 'min:0'],
            'pisos' => ['nullable', 'integer', 'min:0'],
            'piso' => ['nullable', 'integer'],
            'estrato' => ['nullable', 'string', 'max:20'],
            'antiguedad' => ['nullable', 'string', 'max:50'],
            'remodelada' => ['nullable', 'boolean'],
            'propiedad_observaciones' => ['nullable', 'string'],

            // Propiedades - Administración (arriendos)
            'incluye_administracion' => ['nullable', 'boolean'],
            'precio_administracion' => ['nullable', 'numeric', 'min:0'],

            // Propiedades - Servicios básicos y documentos
            'tiene_servicios_publicos' => ['nullable', 'boolean'],
            'tiene_agua' => ['nullable', 'boolean'],
            'tiene_energia' => ['nullable', 'boolean'],
            'tiene_gas' => ['nullable', 'boolean'],
            'tiene_alcantarillado' => ['nullable', 'boolean'],
            'tiene_internet' => ['nullable', 'boolean'],
            'tiene_tv' => ['nullable', 'boolean'],
            'papeles_al_dia' => ['nullable', 'boolean'],
            'libre_gravamen' => ['nullable', 'boolean'],

            // Propiedades - Zonas comunes y amenidades
            'tiene_ascensor' => ['nullable', 'boolean'],
            'tiene_parqueadero' => ['nullable', 'boolean'],
            'tiene_gimnasio' => ['nullable', 'boolean'],
            'tiene_piscina' => ['nullable', 'boolean'],
            'tiene_bbq' => ['nullable', 'boolean'],
            'tiene_salon_comunal' => ['nullable', 'boolean'],
            'tiene_zona_infantil' => ['nullable', 'boolean'],
            'tiene_zonas_verdes' => ['nullable', 'boolean'],
            'tiene_cancha_multiple' => ['nullable', 'boolean'],
            'tiene_sauna_turco' => ['nullable', 'boolean'],
            'tiene_coworking' => ['nullable', 'boolean'],
            'tiene_parqueadero_visitantes' => ['nullable', 'boolean'],

            // Propiedades - Seguridad, interiores y políticas
            'tiene_porteria_24_7' => ['nullable', 'boolean'],
            'tiene_circuito_cerrado' => ['nullable', 'boolean'],
            'permite_mascotas' => ['nullable', 'boolean'],
            'tiene_balcon' => ['nullable', 'boolean'],
            'tiene_terraza' => ['nullable', 'boolean'],
            'tiene_deposito' => ['nullable', 'boolean'],
            'tiene_calentador' => ['nullable', 'boolean'],
            'tiene_aire_acondicionado' => ['nullable', 'boolean'],
            'cocina_integral' => ['nullable', 'boolean'],
            'zona_lavanderia' => ['nullable', 'boolean'],

            // Imágenes y control de eliminación/principal
            'imagenes' => ['nullable', 'array', 'max:8'],
            'imagenes.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'imagenes_a_eliminar' => ['nullable', 'array'],
            'imagenes_a_eliminar.*' => ['integer', 'exists:clasificado_imagenes,id'],
            'imagen_principal_id' => ['nullable', 'integer'],
        ]);

        $categoria = ClasificadoCategoria::with('padre')
            ->findOrFail($validated['categoria_id']);

        $tipo = $this->determinarTipoClasificado($categoria);

        if (!$tipo) {
            return back()
                ->withErrors([
                    'categoria_id' => 'La categoría seleccionada no es válida.',
                ])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $slugActual = $clasificado->slug;

            if (
                Str::slug($validated['titulo']) !==
                Str::slug($clasificado->titulo)
            ) {
                $slugActual = $this->generarSlugUnico(
                    Str::slug($validated['titulo']),
                    $clasificado->id
                );
            }

            $clasificado->update([
                'categoria_id' => $validated['categoria_id'],
                'intencion' => $validated['intencion'],
                'operacion' => $validated['operacion'],
                'titulo' => $validated['titulo'],
                'slug' => $slugActual,
                'descripcion' => $validated['descripcion'],
                'precio' => $validated['precio'] ?? null,
                'pais_id' => $validated['pais_id'] ?? null,
                'division_administrativa_id' =>
                $validated['division_administrativa_id'] ?? null,
                'ciudad_id' => $validated['ciudad_id'] ?? null,
                'rango_cobertura' => $validated['rango_cobertura'] ?? 'departamental',
            ]);

            // Actualizar datos de vehículo si aplica
            if ($tipo === 'vehiculo') {
                $datosVehiculo = $request->only([
                    'marca',
                    'modelo',
                    'anio',
                    'kilometraje',
                    'cilindraje',
                    'combustible',
                    'transmision',
                    'color',
                    'placa',
                    'unico_propietario',
                    'documentacion_al_dia',
                ]);
                $datosVehiculo['observaciones'] = $request->input('vehiculo_observaciones');

                $clasificado->vehiculo()->updateOrCreate(
                    ['clasificado_id' => $clasificado->id],
                    $datosVehiculo
                );
            }

            // Actualizar datos de propiedad con todas las amenidades
            if ($tipo === 'propiedad') {
                $datosPropiedad = $request->only([
                    'area_total',
                    'area_construida',
                    'habitaciones',
                    'banos',
                    'garajes',
                    'pisos',
                    'piso',
                    'estrato',
                    'antiguedad',
                    'remodelada',
                    'tiene_servicios_publicos',
                    'tiene_agua',
                    'tiene_energia',
                    'tiene_gas',
                    'tiene_alcantarillado',
                    'tiene_internet',
                    'tiene_tv',
                    'papeles_al_dia',
                    'libre_gravamen',
                    'incluye_administracion',
                    'precio_administracion',
                    'tiene_ascensor',
                    'tiene_parqueadero',
                    'tiene_gimnasio',
                    'tiene_piscina',
                    'tiene_bbq',
                    'tiene_salon_comunal',
                    'tiene_zona_infantil',
                    'tiene_zonas_verdes',
                    'tiene_cancha_multiple',
                    'tiene_sauna_turco',
                    'tiene_coworking',
                    'tiene_parqueadero_visitantes',
                    'tiene_porteria_24_7',
                    'tiene_circuito_cerrado',
                    'permite_mascotas',
                    'tiene_balcon',
                    'tiene_terraza',
                    'tiene_deposito',
                    'tiene_calentador',
                    'tiene_aire_acondicionado',
                    'cocina_integral',
                    'zona_lavanderia',
                ]);
                $datosPropiedad['observaciones'] = $request->input('propiedad_observaciones');

                $clasificado->propiedad()->updateOrCreate(
                    ['clasificado_id' => $clasificado->id],
                    $datosPropiedad
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Procesar Eliminación de Imágenes Pendientes
            |--------------------------------------------------------------------------
            */
            if ($request->filled('imagenes_a_eliminar')) {
                $idsAEliminar = $request->input('imagenes_a_eliminar');
                $imagenesABorrar = $clasificado->imagenes()->whereIn('id', $idsAEliminar)->get();

                foreach ($imagenesABorrar as $img) {
                    if ($img->ruta && Storage::disk('public')->exists($img->ruta)) {
                        Storage::disk('public')->delete($img->ruta);
                    }
                    $img->delete();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Sincronizar Imagen Principal Seleccionada
            |--------------------------------------------------------------------------
            */
            if ($request->filled('imagen_principal_id')) {
                $clasificado->imagenes()->update(['es_principal' => false]);
                $clasificado->imagenes()->where('id', $request->input('imagen_principal_id'))->update(['es_principal' => true]);
            }

            /*
            |--------------------------------------------------------------------------
            | Nuevas imágenes
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('imagenes')) {
                $cantidadActual = $clasificado->imagenes()->count();
                $nuevas = count($request->file('imagenes'));

                if (($cantidadActual + $nuevas) > 8) {
                    return back()
                        ->withErrors([
                            'imagenes' => 'Una publicación puede tener máximo 8 imágenes.',
                        ])
                        ->withInput();
                }

                foreach ($request->file('imagenes') as $imagen) {
                    $ruta = $imagen->store(
                        'clasificados/' . $clasificado->id . '/imagenes',
                        'public'
                    );

                    $tienePrincipal = $clasificado->imagenes()->where('es_principal', true)->exists();

                    $clasificado->imagenes()->create([
                        'ruta' => $ruta,
                        'orden' => ($clasificado->imagenes()->max('orden') ?? 0) + 1,
                        'es_principal' => !$tienePrincipal, // Si no hay principal asignada, la primera nueva será principal
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('clasificados.show', $clasificado)
                ->with(
                    'success',
                    'El clasificado fue actualizado correctamente.'
                );
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error actualizando clasificado: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());

            return back()
                ->withErrors([
                    'general' => 'No fue posible actualizar el clasificado: ' . $e->getMessage(),
                ])
                ->withInput();
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Eliminar publicación
    |--------------------------------------------------------------------------
    */

    public function destroy(Clasificado $clasificado)
    {
        $this->autorizarPropietario($clasificado);

        DB::transaction(function () use ($clasificado) {
            foreach ($clasificado->imagenes as $imagen) {
                if (
                    $imagen->ruta &&
                    Storage::disk('public')->exists($imagen->ruta)
                ) {
                    Storage::disk('public')
                        ->delete($imagen->ruta);
                }
            }

            $clasificado->delete();
        });

        return redirect()
            ->route('clasificados.index')
            ->with(
                'success',
                'El clasificado fue eliminado correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Pausar
    |--------------------------------------------------------------------------
    */

    public function pausar(Clasificado $clasificado)
    {
        $this->autorizarPropietario($clasificado);

        $clasificado->update([
            'estado' => 'pausado',
            'fecha_cierre' => null,
        ]);

        return back()->with(
            'success',
            'El clasificado fue pausado.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Activar
    |--------------------------------------------------------------------------
    */

    public function activar(Clasificado $clasificado)
    {
        $this->autorizarPropietario($clasificado);

        $clasificado->update([
            'estado' => 'publicado',
            'fecha_publicacion' =>
            $clasificado->fecha_publicacion ?? now(),
            'fecha_cierre' => null,
        ]);

        return back()->with(
            'success',
            'El clasificado fue publicado nuevamente.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Cerrar
    |--------------------------------------------------------------------------
    */

    public function cerrar(Clasificado $clasificado)
    {
        $this->autorizarPropietario($clasificado);

        $clasificado->update([
            'estado' => 'cerrado',
            'fecha_cierre' => now(),
        ]);

        return back()->with(
            'success',
            'El clasificado fue cerrado.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar imagen
    |--------------------------------------------------------------------------
    */

    public function eliminarImagen($imagen)
    {
        $imagen = \App\Models\ClasificadoImagen::with(
            'clasificado'
        )->findOrFail($imagen);

        $this->autorizarPropietario(
            $imagen->clasificado
        );

        if (
            $imagen->ruta &&
            Storage::disk('public')->exists($imagen->ruta)
        ) {
            Storage::disk('public')
                ->delete($imagen->ruta);
        }

        $eraPrincipal = $imagen->es_principal;

        $clasificado = $imagen->clasificado;

        $imagen->delete();

        /*
        |--------------------------------------------------------------------------
        | Si eliminamos la principal, asignamos otra
        |--------------------------------------------------------------------------
        */

        if ($eraPrincipal) {
            $nuevaPrincipal = $clasificado
                ->imagenes()
                ->orderBy('orden')
                ->first();

            if ($nuevaPrincipal) {
                $nuevaPrincipal->update([
                    'es_principal' => true,
                ]);
            }
        }

        return back()->with(
            'success',
            'La imagen fue eliminada.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Establecer imagen principal
    |--------------------------------------------------------------------------
    */

    public function establecerImagenPrincipal($imagen)
    {
        $imagen = \App\Models\ClasificadoImagen::with(
            'clasificado'
        )->findOrFail($imagen);

        $this->autorizarPropietario(
            $imagen->clasificado
        );

        $imagen->clasificado
            ->imagenes()
            ->update([
                'es_principal' => false,
            ]);

        $imagen->update([
            'es_principal' => true,
        ]);

        return back()->with(
            'success',
            'La imagen principal fue actualizada.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers privados
    |--------------------------------------------------------------------------
    */

    private function autorizarPropietario(
        Clasificado $clasificado
    ): void {
        abort_unless(
            Auth::id() === $clasificado->user_id,
            403
        );
    }

    private function generarSlugUnico(
        string $slug,
        ?int $exceptoId = null
    ): string {
        $original = $slug;
        $contador = 1;

        while (
            Clasificado::query()
            ->when(
                $exceptoId,
                fn($query) =>
                $query->where('id', '!=', $exceptoId)
            )
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $original . '-' . $contador;
            $contador++;
        }

        return $slug;
    }

    private function determinarTipoClasificado(
        ClasificadoCategoria $categoria
    ): ?string {
        $actual = $categoria;

        while ($actual) {
            if ($actual->slug === 'vehiculos') {
                return 'vehiculo';
            }

            if ($actual->slug === 'propiedades') {
                return 'propiedad';
            }

            $actual = $actual->padre;
        }

        return null;
    }
}
