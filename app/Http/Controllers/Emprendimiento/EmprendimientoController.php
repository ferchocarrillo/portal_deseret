<?php

namespace App\Http\Controllers\Emprendimiento;

use App\Http\Controllers\Controller;
use App\Models\Emprendimiento;
use App\Models\EmprendimientoCategoria;
use App\Models\Favorito;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class EmprendimientoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO PÚBLICO
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): Response
    {
        $usuarioId = Auth::id();

        $favoritosIds = Auth::check()
            ? Favorito::query()
            ->where('user_id', $usuarioId)
            ->where('modulo', 'emprendimientos')
            ->pluck('publicacion_id')
            ->map(fn($id) => (int) $id)
            ->toArray()
            : [];

        $query = Emprendimiento::query()
            ->with([
                'usuario',
                'categoria',
                'subcategoria',
            ])
            ->where('activo', true);

        /*
        |--------------------------------------------------------------------------
        | BÚSQUEDA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%")
                    ->orWhere('produccion_actual', 'like', "%{$search}%")
                    ->orWhere('proyeccion', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRO POR CATEGORÍA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('categoria_id')) {
            $query->where(
                'categoria_id',
                $request->integer('categoria_id')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRO POR SUBCATEGORÍA
        |--------------------------------------------------------------------------
        */

        if ($request->filled('subcategoria_id')) {
            $query->where(
                'subcategoria_id',
                $request->integer('subcategoria_id')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINACIÓN
        |--------------------------------------------------------------------------
        */

        $emprendimientos = $query
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(function ($emprendimiento) use ($favoritosIds) {
                $emprendimiento->es_favorito = in_array((int) $emprendimiento->id, $favoritosIds, true);
                return $emprendimiento;
            });

        /*
        |--------------------------------------------------------------------------
        | CATEGORÍAS
        |--------------------------------------------------------------------------
        */

        $categorias = EmprendimientoCategoria::query()
            ->where('activo', true)
            ->with([
                'subcategorias' => function ($query) {
                    $query
                        ->where('activo', true)
                        ->orderBy('orden')
                        ->orderBy('nombre');
                },
            ])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Emprendimientos/Index', [
            'emprendimientos' => $emprendimientos,

            'categorias' => $categorias,

            'filters' => [
                'search' => $request->input('search'),
                'categoria_id' => $request->input('categoria_id'),
                'subcategoria_id' => $request->input('subcategoria_id'),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO DE CREACIÓN
    |--------------------------------------------------------------------------
    */

    public function create(): Response
    {
        $categorias = EmprendimientoCategoria::query()
            ->where('activo', true)
            ->with([
                'subcategorias' => function ($query) {
                    $query
                        ->where('activo', true)
                        ->orderBy('orden')
                        ->orderBy('nombre');
                },
            ])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Emprendimientos/Create', [
            'categorias' => $categorias,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GUARDAR
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'categoria_id' => [
                'required',
                'integer',
                'exists:emprendimiento_categorias,id',
            ],

            'subcategoria_id' => [
                'nullable',
                'integer',
                'exists:emprendimiento_subcategorias,id',
            ],

            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'descripcion' => [
                'required',
                'string',
            ],

            'produccion_actual' => [
                'nullable',
                'string',
            ],

            'proyeccion' => [
                'nullable',
                'string',
            ],

            'necesidad' => [
                'nullable',
                'string',
            ],

            'propuesta_colaboracion' => [
                'nullable',
                'string',
            ],

            'tipo_colaboracion' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | YOUTUBE
            |--------------------------------------------------------------------------
            |
            | El enlace es obligatorio para crear el emprendimiento.
            |
            */

            'youtube_url' => [
                'required',
                'string',
                'max:500',
                'url',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | VERIFICAR SUBCATEGORÍA
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['subcategoria_id'])) {
            $subcategoriaPertenece =
                \App\Models\EmprendimientoSubcategoria::query()
                ->where(
                    'id',
                    $validated['subcategoria_id']
                )
                ->where(
                    'categoria_id',
                    $validated['categoria_id']
                )
                ->exists();

            if (!$subcategoriaPertenece) {
                return back()
                    ->withErrors([
                        'subcategoria_id' =>
                        'La subcategoría seleccionada no pertenece a la categoría indicada.',
                    ])
                    ->withInput();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | EXTRAER ID DE YOUTUBE
        |--------------------------------------------------------------------------
        */

        $youtubeVideoId = $this->extraerYoutubeVideoId(
            $validated['youtube_url']
        );

        if (!$youtubeVideoId) {
            return back()
                ->withErrors([
                    'youtube_url' =>
                    'El enlace proporcionado no corresponde a un video válido de YouTube.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | PROPIETARIO
        |--------------------------------------------------------------------------
        */

        $validated['user_id'] = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | PREPARAR VIDEO
        |--------------------------------------------------------------------------
        */

        $validated['youtube_video_id'] = $youtubeVideoId;

        unset($validated['youtube_url']);

        /*
        |--------------------------------------------------------------------------
        | CREAR EMPRENDIMIENTO
        |--------------------------------------------------------------------------
        */

        $emprendimiento = Emprendimiento::create($validated);

        /*
        |--------------------------------------------------------------------------
        | REDIRECCIÓN
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'emprendimientos.show',
                $emprendimiento
            )
            ->with(
                'success',
                'Emprendimiento creado correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | MOSTRAR
    |--------------------------------------------------------------------------
    */

    public function show(
        Emprendimiento $emprendimiento
    ): Response {
        abort_unless(
            $emprendimiento->activo,
            404
        );

        $emprendimiento->load([
            'usuario',
            'categoria',
            'subcategoria',
        ]);

        // 1. Consultar si el emprendimiento es favorito del usuario actual
        $esFavorito = Auth::check()
            ? \App\Models\Favorito::query()
            ->where('user_id', Auth::id())
            ->where('publicacion_id', (int) $emprendimiento->id)
            ->where('modulo', 'emprendimientos')
            ->exists()
            : false;

        // 2. Asignar la propiedad al objeto
        $emprendimiento->es_favorito = (bool) $esFavorito;

        return Inertia::render('Emprendimientos/Show', [
            'emprendimiento' => $emprendimiento,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO DE EDICIÓN
    |--------------------------------------------------------------------------
    */

    public function edit(
        Emprendimiento $emprendimiento
    ): Response {
        /*
        |--------------------------------------------------------------------------
        | Verificar propietario
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $emprendimiento->user_id === Auth::id(),
            403
        );

        $categorias = EmprendimientoCategoria::query()
            ->where('activo', true)
            ->with([
                'subcategorias' => function ($query) {
                    $query
                        ->where('activo', true)
                        ->orderBy('orden')
                        ->orderBy('nombre');
                },
            ])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Emprendimientos/Edit', [
            'emprendimiento' => $emprendimiento,
            'categorias' => $categorias,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Emprendimiento $emprendimiento
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Verificar propietario
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $emprendimiento->user_id === Auth::id(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'categoria_id' => [
                'required',
                'integer',
                'exists:emprendimiento_categorias,id',
            ],

            'subcategoria_id' => [
                'nullable',
                'integer',
                'exists:emprendimiento_subcategorias,id',
            ],

            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'descripcion' => [
                'required',
                'string',
            ],

            'produccion_actual' => [
                'nullable',
                'string',
            ],

            'proyeccion' => [
                'nullable',
                'string',
            ],

            'necesidad' => [
                'nullable',
                'string',
            ],

            'propuesta_colaboracion' => [
                'nullable',
                'string',
            ],

            'tipo_colaboracion' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | YOUTUBE
            |--------------------------------------------------------------------------
            |
            | En edición el enlace es opcional porque puede conservarse
            | el video actual.
            |
            */

            'youtube_url' => [
                'nullable',
                'string',
                'max:500',
                'url',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | VERIFICAR SUBCATEGORÍA
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['subcategoria_id'])) {
            $subcategoriaPertenece =
                \App\Models\EmprendimientoSubcategoria::query()
                ->where(
                    'id',
                    $validated['subcategoria_id']
                )
                ->where(
                    'categoria_id',
                    $validated['categoria_id']
                )
                ->exists();

            if (!$subcategoriaPertenece) {
                return back()
                    ->withErrors([
                        'subcategoria_id' =>
                        'La subcategoría seleccionada no pertenece a la categoría indicada.',
                    ])
                    ->withInput();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR VIDEO DE YOUTUBE
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['youtube_url'])) {
            $youtubeVideoId = $this->extraerYoutubeVideoId(
                $validated['youtube_url']
            );

            if (!$youtubeVideoId) {
                return back()
                    ->withErrors([
                        'youtube_url' =>
                        'El enlace proporcionado no corresponde a un video válido de YouTube.',
                    ])
                    ->withInput();
            }

            $validated['youtube_video_id'] = $youtubeVideoId;
        }

        unset($validated['youtube_url']);

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR EMPRENDIMIENTO
        |--------------------------------------------------------------------------
        */

        $emprendimiento->update($validated);

        /*
        |--------------------------------------------------------------------------
        | REDIRECCIÓN
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'emprendimientos.show',
                $emprendimiento
            )
            ->with(
                'success',
                'Emprendimiento actualizado correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Emprendimiento $emprendimiento
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Verificar propietario
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $emprendimiento->user_id === Auth::id(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | ELIMINACIÓN LÓGICA
        |--------------------------------------------------------------------------
        |
        | No eliminamos físicamente el registro.
        |
        */

        $emprendimiento->update([
            'activo' => false,
        ]);

        return redirect()
            ->route('emprendimientos.index')
            ->with(
                'success',
                'Emprendimiento retirado correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | EXTRAER ID DE YOUTUBE
    |--------------------------------------------------------------------------
    */

    private function extraerYoutubeVideoId(string $url): ?string
    {
        $parsedUrl = parse_url(trim($url));

        if (!$parsedUrl || empty($parsedUrl['host'])) {
            return null;
        }

        $host = strtolower($parsedUrl['host']);

        /*
        |--------------------------------------------------------------------------
        | youtube.com
        |--------------------------------------------------------------------------
        */

        if (
            $host === 'youtube.com' ||
            $host === 'www.youtube.com' ||
            $host === 'm.youtube.com'
        ) {
            /*
            | URL normal:
            | https://www.youtube.com/watch?v=ABC123
            */

            if (
                isset($parsedUrl['path']) &&
                $parsedUrl['path'] === '/watch'
            ) {
                parse_str(
                    $parsedUrl['query'] ?? '',
                    $query
                );

                $videoId = $query['v'] ?? null;

                return $this->validarYoutubeVideoId(
                    $videoId
                );
            }

            /*
            | URL embed:
            | https://www.youtube.com/embed/ABC123
            */

            if (
                isset($parsedUrl['path']) &&
                preg_match(
                    '#^/embed/([^/]+)#',
                    $parsedUrl['path'],
                    $matches
                )
            ) {
                return $this->validarYoutubeVideoId(
                    $matches[1]
                );
            }

            /*
            | URL shorts:
            | https://www.youtube.com/shorts/ABC123
            */

            if (
                isset($parsedUrl['path']) &&
                preg_match(
                    '#^/shorts/([^/]+)#',
                    $parsedUrl['path'],
                    $matches
                )
            ) {
                return $this->validarYoutubeVideoId(
                    $matches[1]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | youtu.be
        |--------------------------------------------------------------------------
        */

        if ($host === 'youtu.be') {
            $videoId = trim(
                $parsedUrl['path'] ?? '',
                '/'
            );

            return $this->validarYoutubeVideoId(
                $videoId
            );
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAR ID DE YOUTUBE
    |--------------------------------------------------------------------------
    */

    private function validarYoutubeVideoId(
        ?string $videoId
    ): ?string {
        if (!$videoId) {
            return null;
        }

        /*
        | Los IDs normales de YouTube tienen 11 caracteres.
        */

        if (!preg_match(
            '/^[A-Za-z0-9_-]{11}$/',
            $videoId
        )) {
            return null;
        }

        return $videoId;
    }
}
