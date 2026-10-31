<?php

namespace App\Http\Controllers\Admin\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceCategoria;
use App\Models\MarketplaceSubcategoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SubcategoriaAdminController extends Controller
{
    /**
     * Listar subcategorías.
     */
    public function index(Request $request): Response
    {
        $query = MarketplaceSubcategoria::query()
            ->with([
                'categoria:id,nombre,slug',
            ])
            ->withCount('publicaciones');

        /*
        |--------------------------------------------------------------------------
        | Filtrar por categoría
        |--------------------------------------------------------------------------
        */
        if ($request->filled('categoria_id')) {
            $query->where(
                'categoria_id',
                $request->categoria_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Buscar
        |--------------------------------------------------------------------------
        */
        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $query->where(function ($q) use ($buscar) {
                $q->where(
                    'nombre',
                    'like',
                    "%{$buscar}%"
                )->orWhere(
                    'slug',
                    'like',
                    "%{$buscar}%"
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Orden
        |--------------------------------------------------------------------------
        */
        $query->orderBy('orden')
            ->orderBy('nombre');

        /*
        |--------------------------------------------------------------------------
        | Resultado
        |--------------------------------------------------------------------------
        */
        $subcategorias = $query
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Categorías disponibles
        |--------------------------------------------------------------------------
        */
        $categorias = MarketplaceCategoria::query()
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
                'slug',
                'activo',
            ]);

        return Inertia::render(
            'Catálogo comunitario /Subcategorias',
            [
                'subcategorias' => $subcategorias,

                'categorias' => $categorias,

                'filtros' => [
                    'categoria_id' =>
                    $request->get('categoria_id'),

                    'buscar' =>
                    $request->get('buscar'),
                ],
            ]
        );
    }

    /**
     * Mostrar formulario para crear una subcategoría.
     */
    public function create(): Response
    {
        $categorias = MarketplaceCategoria::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
                'slug',
            ]);

        return Inertia::render(
            'admin/Marketplace/Subcategorias/Create',
            [
                'categorias' => $categorias,
            ]
        );
    }

    /**
     * Crear una subcategoría.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'categoria_id' => [
                'required',
                'integer',
                'exists:marketplace_categorias,id',
            ],

            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'marketplace_subcategorias',
                    'nombre'
                )->where(function ($query) use ($request) {
                    return $query->where(
                        'categoria_id',
                        $request->categoria_id
                    );
                }),
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
            ],

            'activo' => [
                'nullable',
                'boolean',
            ],

            'orden' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $categoria = MarketplaceCategoria::findOrFail(
            $validated['categoria_id']
        );

        /*
        |--------------------------------------------------------------------------
        | No crear subcategorías dentro de una categoría inactiva.
        |--------------------------------------------------------------------------
        */
        if (!$categoria->activo) {
            return back()
                ->withErrors([
                    'categoria_id' =>
                    'No puedes crear una subcategoría dentro de una categoría inactiva.',
                ])
                ->withInput();
        }

        MarketplaceSubcategoria::create([
            'categoria_id' =>
            $validated['categoria_id'],

            'nombre' =>
            $validated['nombre'],

            'slug' =>
            $this->generarSlug(
                $validated['slug']
                    ?? $validated['nombre'],
                $validated['categoria_id']
            ),

            'activo' =>
            $validated['activo'] ?? true,

            'orden' =>
            $validated['orden'] ?? 0,
        ]);

        return redirect()
            ->route(
                'admin.marketplace.subcategorias.index'
            )
            ->with(
                'success',
                'La subcategoría fue creada correctamente.'
            );
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(
        MarketplaceSubcategoria $subcategoria
    ): Response {
        $categorias = MarketplaceCategoria::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
                'slug',
            ]);

        return Inertia::render(
            'admin/Marketplace/Subcategorias/Edit',
            [
                'subcategoria' => $subcategoria,

                'categorias' => $categorias,
            ]
        );
    }

    /**
     * Actualizar una subcategoría.
     */
    public function update(
        Request $request,
        MarketplaceSubcategoria $subcategoria
    ): RedirectResponse {
        $validated = $request->validate([
            'categoria_id' => [
                'required',
                'integer',
                'exists:marketplace_categorias,id',
            ],

            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'marketplace_subcategorias',
                    'nombre'
                )
                    ->where(function ($query) use ($request) {
                        return $query->where(
                            'categoria_id',
                            $request->categoria_id
                        );
                    })
                    ->ignore($subcategoria->id),
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
            ],

            'activo' => [
                'nullable',
                'boolean',
            ],

            'orden' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ]);

        $categoria = MarketplaceCategoria::findOrFail(
            $validated['categoria_id']
        );

        /*
        |--------------------------------------------------------------------------
        | No mover una subcategoría a una categoría inactiva.
        |--------------------------------------------------------------------------
        */
        if (!$categoria->activo) {
            return back()
                ->withErrors([
                    'categoria_id' =>
                    'No puedes asignar la subcategoría a una categoría inactiva.',
                ])
                ->withInput();
        }

        $subcategoria->update([
            'categoria_id' =>
            $validated['categoria_id'],

            'nombre' =>
            $validated['nombre'],

            'slug' =>
            $this->generarSlug(
                $validated['slug']
                    ?? $validated['nombre'],
                $validated['categoria_id'],
                $subcategoria->id
            ),

            'activo' =>
            $validated['activo'] ?? false,

            'orden' =>
            $validated['orden'] ?? 0,
        ]);

        return redirect()
            ->route(
                'admin.marketplace.subcategorias.index'
            )
            ->with(
                'success',
                'La subcategoría fue actualizada correctamente.'
            );
    }

    /**
     * Activar una subcategoría.
     */
    public function activar(
        MarketplaceSubcategoria $subcategoria
    ): RedirectResponse {
        if (!$subcategoria->categoria?->activo) {
            return back()->with(
                'error',
                'No puedes activar una subcategoría cuya categoría está inactiva.'
            );
        }

        $subcategoria->update([
            'activo' => true,
        ]);

        return back()->with(
            'success',
            'La subcategoría fue activada.'
        );
    }

    /**
     * Desactivar una subcategoría.
     */
    public function desactivar(
        MarketplaceSubcategoria $subcategoria
    ): RedirectResponse {
        $subcategoria->update([
            'activo' => false,
        ]);

        return back()->with(
            'success',
            'La subcategoría fue desactivada.'
        );
    }

    /**
     * Eliminar una subcategoría.
     */
    public function destroy(
        MarketplaceSubcategoria $subcategoria
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | No eliminar si tiene publicaciones.
        |--------------------------------------------------------------------------
        */
        if ($subcategoria->publicaciones()->exists()) {
            return back()->with(
                'error',
                'No puedes eliminar una subcategoría que tiene publicaciones asociadas. Desactívala en su lugar.'
            );
        }

        $subcategoria->delete();

        return redirect()
            ->route(
                'admin.marketplace.subcategorias.index'
            )
            ->with(
                'success',
                'La subcategoría fue eliminada correctamente.'
            );
    }

    /**
     * Generar slug único dentro de una categoría.
     */
    private function generarSlug(
        string $valor,
        int $categoriaId,
        ?int $exceptoId = null
    ): string {
        $slugBase = Str::slug($valor);

        if ($slugBase === '') {
            $slugBase = 'subcategoria';
        }

        $slug = $slugBase;
        $contador = 1;

        while (true) {
            $query = MarketplaceSubcategoria::query()
                ->where('categoria_id', $categoriaId)
                ->where('slug', $slug);

            if ($exceptoId !== null) {
                $query->where(
                    'id',
                    '!=',
                    $exceptoId
                );
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $slugBase . '-' . $contador;
            $contador++;
        }

        return $slug;
    }
}
