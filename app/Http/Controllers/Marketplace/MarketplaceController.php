<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Ciudad;
use App\Models\DivisionAdministrativa;
use App\Models\Favorito;
use App\Models\MarketplaceCategoria;
use App\Models\MarketplacePublicacion;
use App\Models\Pais;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MarketplaceController extends Controller
{
    /**
     * Página principal del Marketplace.
     */
    public function index(Request $request): Response
    {
        $usuarioId = Auth::id();

        $favoritosIds = Auth::check()
            ? Favorito::query()
            ->where('user_id', $usuarioId)
            ->where('modulo', 'marketplace')
            ->pluck('publicacion_id')
            ->map(fn($id) => (int) $id)
            ->toArray()
            : [];

        $query = MarketplacePublicacion::query()
            ->with([
                'categoria:id,nombre,slug',
                'subcategoria:id,categoria_id,nombre,slug',
                'usuario:id,name',
                'pais',
                'divisionAdministrativa',
                'ciudad',
                'imagenes' => function ($query) {
                    $query->orderByDesc('es_principal')
                        ->orderBy('orden');
                },
                'video',
            ])
            ->where('estado_publicacion', 'publicada');

        /*
        |--------------------------------------------------------------------------
        | Búsqueda
        |--------------------------------------------------------------------------
        */
        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $query->where(function ($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Categoría
        |--------------------------------------------------------------------------
        */
        if ($request->filled('categoria')) {
            $categoria = $request->categoria;

            $query->whereHas('categoria', function ($q) use ($categoria) {
                $q->where('slug', $categoria);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Subcategoría
        |--------------------------------------------------------------------------
        */
        if ($request->filled('subcategoria')) {
            $subcategoria = $request->subcategoria;

            $query->whereHas('subcategoria', function ($q) use ($subcategoria) {
                $q->where('slug', $subcategoria);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Estado del producto
        |--------------------------------------------------------------------------
        */
        if ($request->filled('estado_producto')) {
            $query->where(
                'estado_producto',
                $request->estado_producto
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Rango de Cobertura / Exhibición directo (Filtro opcional específico)
        |--------------------------------------------------------------------------
        */
        if ($request->filled('rango_cobertura')) {
            $query->where('rango_cobertura', $request->rango_cobertura);
        }

        /*
        |--------------------------------------------------------------------------
        | Ubicación y Rango de Cobertura Inclusivo
        |--------------------------------------------------------------------------
        | Resuelve la disponibilidad comercial territorial del espectador.
        |--------------------------------------------------------------------------
        */
        $this->aplicarFiltroUbicacionComercial($query, $request);

        /*
        |--------------------------------------------------------------------------
        | Tipo de publicación
        |--------------------------------------------------------------------------
        */
        if ($request->filled('tipo')) {
            if ($request->tipo === 'gratis') {
                $query->where('es_gratis', true);
            }

            if ($request->tipo === 'con_precio') {
                $query->where('es_gratis', false);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Precio mínimo
        |--------------------------------------------------------------------------
        */
        if (
            $request->filled('precio_min') &&
            is_numeric($request->precio_min)
        ) {
            $query->where(
                'precio',
                '>=',
                $request->precio_min
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Precio máximo
        |--------------------------------------------------------------------------
        */
        if (
            $request->filled('precio_max') &&
            is_numeric($request->precio_max)
        ) {
            $query->where(
                'precio',
                '<=',
                $request->precio_max
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ordenamiento
        |--------------------------------------------------------------------------
        */
        switch ($request->get('orden')) {
            case 'precio_menor':
                $query->orderByRaw(
                    'CASE WHEN es_gratis = 1 THEN 0 ELSE precio END ASC'
                );
                break;

            case 'precio_mayor':
                $query->orderByRaw(
                    'CASE WHEN es_gratis = 1 THEN 0 ELSE precio END DESC'
                );
                break;

            case 'antiguos':
                $query->orderBy('fecha_publicacion', 'asc');
                break;

            case 'recientes':
            default:
                $query->orderByDesc('destacado')
                    ->orderByDesc('fecha_publicacion')
                    ->orderByDesc('id');
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Publicaciones con inyección de favoritos
        |--------------------------------------------------------------------------
        */
        $publicaciones = $query
            ->paginate(12)
            ->withQueryString()
            ->through(function ($publicacion) use ($favoritosIds) {
                $publicacion->es_favorito = in_array((int) $publicacion->id, $favoritosIds, true);
                return $publicacion;
            });

        /*
        |--------------------------------------------------------------------------
        | Categorías
        |--------------------------------------------------------------------------
        */
        $categorias = MarketplaceCategoria::query()
            ->where('activo', true)
            ->with([
                'subcategorias' => function ($query) {
                    $query->where('activo', true)
                        ->orderBy('orden')
                        ->orderBy('nombre');
                },
            ])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Respuesta Inertia
        |--------------------------------------------------------------------------
        */
        return Inertia::render('Marketplace/Index', [
            'publicaciones' => $publicaciones,
            'categorias' => $categorias,
            'filtros' => [
                'buscar' => $request->get('buscar'),
                'categoria' => $request->get('categoria'),
                'subcategoria' => $request->get('subcategoria'),
                'estado_producto' => $request->get('estado_producto'),
                'pais_id' => $request->get('pais_id'),
                'division_administrativa_id' => $request->get('division_administrativa_id'),
                'ciudad_id' => $request->get('ciudad_id'),
                'rango_cobertura' => $request->get('rango_cobertura'),
                'tipo' => $request->get('tipo'),
                'precio_min' => $request->get('precio_min'),
                'precio_max' => $request->get('precio_max'),
                'orden' => $request->get('orden', 'recientes'),
            ],
            'estadosProducto' => [
                ['value' => 'nuevo', 'label' => 'Nuevo'],
                ['value' => 'como_nuevo', 'label' => 'Como nuevo'],
                ['value' => 'buen_estado', 'label' => 'Buen estado'],
                ['value' => 'usado', 'label' => 'Usado'],
                ['value' => 'para_reparar', 'label' => 'Para reparar'],
            ],
            'rangosCobertura' => [
                ['value' => 'local', 'label' => 'Solo en mi ciudad / municipio'],
                ['value' => 'departamental', 'label' => 'A nivel regional / departamental'],
                ['value' => 'nacional', 'label' => 'Todo el país'],
                ['value' => 'internacional', 'label' => 'Envíos internacionales'],
            ],
        ]);
    }

    /**
     * Mostrar publicaciones pertenecientes a una categoría.
     */
    public function categoria(
        Request $request,
        MarketplaceCategoria $categoria
    ): Response {
        abort_unless(
            $categoria->activo,
            404
        );

        $usuarioId = Auth::id();

        $favoritosIds = Auth::check()
            ? Favorito::query()
            ->where('user_id', $usuarioId)
            ->where('modulo', 'marketplace')
            ->pluck('publicacion_id')
            ->map(fn($id) => (int) $id)
            ->toArray()
            : [];

        $query = MarketplacePublicacion::query()
            ->with([
                'categoria:id,nombre,slug',
                'subcategoria:id,categoria_id,nombre,slug',
                'usuario:id,name',
                'pais',
                'divisionAdministrativa',
                'ciudad',
                'imagenes' => function ($query) {
                    $query->orderByDesc('es_principal')
                        ->orderBy('orden');
                },
                'video',
            ])
            ->where('categoria_id', $categoria->id)
            ->where('estado_publicacion', 'publicada');

        if ($request->filled('subcategoria')) {
            $subcategoria = $request->subcategoria;
            $query->whereHas('subcategoria', function ($q) use ($subcategoria) {
                $q->where('slug', $subcategoria);
            });
        }

        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $query->where(function ($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado_producto')) {
            $query->where('estado_producto', $request->estado_producto);
        }

        if ($request->filled('rango_cobertura')) {
            $query->where('rango_cobertura', $request->rango_cobertura);
        }

        /*
        |--------------------------------------------------------------------------
        | Ubicación y Rango de Cobertura Inclusivo
        |--------------------------------------------------------------------------
        */
        $this->aplicarFiltroUbicacionComercial($query, $request);

        if ($request->filled('tipo')) {
            if ($request->tipo === 'gratis') {
                $query->where('es_gratis', true);
            }
            if ($request->tipo === 'con_precio') {
                $query->where('es_gratis', false);
            }
        }

        if ($request->filled('precio_min') && is_numeric($request->precio_min)) {
            $query->where('precio', '>=', $request->precio_min);
        }

        if ($request->filled('precio_max') && is_numeric($request->precio_max)) {
            $query->where('precio', '<=', $request->precio_max);
        }

        switch ($request->get('orden')) {
            case 'precio_menor':
                $query->orderByRaw('CASE WHEN es_gratis = 1 THEN 0 ELSE precio END ASC');
                break;
            case 'precio_mayor':
                $query->orderByRaw('CASE WHEN es_gratis = 1 THEN 0 ELSE precio END DESC');
                break;
            case 'antiguos':
                $query->orderBy('fecha_publicacion', 'asc');
                break;
            case 'recientes':
            default:
                $query->orderByDesc('destacado')
                    ->orderByDesc('fecha_publicacion')
                    ->orderByDesc('id');
                break;
        }

        $publicaciones = $query
            ->paginate(12)
            ->withQueryString()
            ->through(function ($publicacion) use ($favoritosIds) {
                $publicacion->es_favorito = in_array((int) $publicacion->id, $favoritosIds, true);
                return $publicacion;
            });

        $categorias = MarketplaceCategoria::query()
            ->where('activo', true)
            ->with([
                'subcategorias' => function ($query) {
                    $query->where('activo', true)
                        ->orderBy('orden')
                        ->orderBy('nombre');
                },
            ])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        $categoria->load([
            'subcategorias' => function ($query) {
                $query->where('activo', true)
                    ->orderBy('orden')
                    ->orderBy('nombre');
            },
        ]);

        /*
        |--------------------------------------------------------------------------
        | Respuesta Inertia
        |--------------------------------------------------------------------------
        */
        return Inertia::render('Marketplace/Index', [
            'publicaciones' => $publicaciones,
            'categorias' => $categorias,
            'categoriaActiva' => $categoria,
            'filtros' => [
                'buscar' => $request->get('buscar'),
                'categoria' => $categoria->slug,
                'subcategoria' => $request->get('subcategoria'),
                'estado_producto' => $request->get('estado_producto'),
                'pais_id' => $request->get('pais_id'),
                'division_administrativa_id' => $request->get('division_administrativa_id'),
                'ciudad_id' => $request->get('ciudad_id'),
                'rango_cobertura' => $request->get('rango_cobertura'),
                'tipo' => $request->get('tipo'),
                'precio_min' => $request->get('precio_min'),
                'precio_max' => $request->get('precio_max'),
                'orden' => $request->get('orden', 'recientes'),
            ],
            'estadosProducto' => [
                ['value' => 'nuevo', 'label' => 'Nuevo'],
                ['value' => 'como_nuevo', 'label' => 'Como nuevo'],
                ['value' => 'buen_estado', 'label' => 'Buen estado'],
                ['value' => 'usado', 'label' => 'Usado'],
                ['value' => 'para_reparar', 'label' => 'Para reparar'],
            ],
            'rangosCobertura' => [
                ['value' => 'local', 'label' => 'Solo en mi ciudad / municipio'],
                ['value' => 'departamental', 'label' => 'A nivel regional / departamental'],
                ['value' => 'nacional', 'label' => 'Todo el país'],
                ['value' => 'internacional', 'label' => 'Envíos internacionales'],
            ],
        ]);
    }

    /**
     * Aplica el filtro inclusivo de disponibilidad territorial comercial.
     */
    protected function aplicarFiltroUbicacionComercial(Builder $query, Request $request): void
    {
        $usuario = Auth::user();
        $recepcion = $usuario?->informacionRecepcion;

        // 1. Obtener IDs: primero del Request
        $paisId = $request->filled('pais_id') ? (int) $request->get('pais_id') : null;
        $divisionId = $request->filled('division_administrativa_id') ? (int) $request->get('division_administrativa_id') : null;
        $ciudadId = $request->filled('ciudad_id') ? (int) $request->get('ciudad_id') : null;

        // 2. Si no vienen por Request, resolver los IDs a partir de UserInformacionRecepcion
        if (!$ciudadId && !$divisionId && !$paisId && $recepcion) {
            if (!empty($recepcion->ciudad)) {
                $nombreCiudad = trim($recepcion->ciudad);
                $ciudadObj = Ciudad::query()
                    ->where('nombre', 'like', "%{$nombreCiudad}%")
                    ->first();

                if ($ciudadObj) {
                    $ciudadId = (int) $ciudadObj->id;
                    $divisionId = (int) ($ciudadObj->division_administrativa_id ?? $ciudadObj->departamento_id ?? 0) ?: null;
                    $paisId = (int) ($ciudadObj->pais_id ?? 0) ?: null;
                }
            }

            if (!$divisionId && !empty($recepcion->departamento)) {
                $nombreDep = trim($recepcion->departamento);
                $divObj = DivisionAdministrativa::query()
                    ->where('nombre', 'like', "%{$nombreDep}%")
                    ->first();

                if ($divObj) {
                    $divisionId = (int) $divObj->id;
                    $paisId = $paisId ?? ((int) ($divObj->pais_id ?? 0) ?: null);
                }
            }

            if (!$paisId && !empty($recepcion->pais)) {
                $nombrePais = trim($recepcion->pais);
                $paisObj = Pais::query()
                    ->where('nombre', 'like', "%{$nombrePais}%")
                    ->first();

                if ($paisObj) {
                    $paisId = (int) $paisObj->id;
                }
            }
        }

        // 3. Aplicar el filtro de rango comercial estricto
        if ($paisId || $divisionId || $ciudadId) {
            $query->where(function (Builder $q) use ($paisId, $divisionId, $ciudadId) {

                // A. Cobertura internacional: siempre visible
                $q->where('rango_cobertura', 'internacional');

                // B. Cobertura nacional: debe coincidir el país
                if ($paisId) {
                    $q->orWhere(function (Builder $sub) use ($paisId) {
                        $sub->where('rango_cobertura', 'nacional')
                            ->where('pais_id', $paisId);
                    });
                }

                // C. Cobertura departamental: debe coincidir el departamento
                if ($divisionId) {
                    $q->orWhere(function (Builder $sub) use ($divisionId) {
                        $sub->where('rango_cobertura', 'departamental')
                            ->where('division_administrativa_id', $divisionId);
                    });
                }

                // D. Cobertura local: SOLO visible si la ciudad coincide exactamente
                if ($ciudadId) {
                    $q->orWhere(function (Builder $sub) use ($ciudadId) {
                        $sub->where('rango_cobertura', 'local')
                            ->where('ciudad_id', $ciudadId);
                    });
                }
            });
        }
    }
}
