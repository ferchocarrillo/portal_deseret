<?php

namespace App\Http\Controllers\Admin\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceCategoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CategoriaAdminController extends Controller
{
    /**
     * Listar categorías.
     */
    public function index(): Response
    {
        $categorias = MarketplaceCategoria::query()
            ->withCount([
                'subcategorias',
                'publicaciones',
            ])
            ->with([
                'subcategorias' => function ($query) {
                    $query->orderBy('orden')
                        ->orderBy('nombre');
                },
            ])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return Inertia::render(
            'admin/Marketplace/Categorias',
            [
                'categorias' => $categorias,
            ]
        );
    }

    /**
     * Mostrar formulario para crear una categoría.
     */
    public function create(): Response
    {
        return Inertia::render(
            'admin/Marketplace/Categorias/Create'
        );
    }

    /**
     * Crear una categoría.
     */
    public function store(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                'unique:marketplace_categorias,nombre',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:marketplace_categorias,slug',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'icono' => [
                'nullable',
                'string',
                'max:100',
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

        MarketplaceCategoria::create([
            'nombre' => $validated['nombre'],

            'slug' => $this->generarSlug(
                $validated['slug']
                    ?? $validated['nombre']
            ),

            'descripcion' =>
            $validated['descripcion'] ?? null,

            'icono' =>
            $validated['icono'] ?? null,

            'activo' =>
            $validated['activo'] ?? true,

            'orden' =>
            $validated['orden'] ?? 0,
        ]);

        return redirect()
            ->route('admin.marketplace.categorias.index')
            ->with(
                'success',
                'La categoría fue creada correctamente.'
            );
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(
        MarketplaceCategoria $categoria
    ): Response {
        return Inertia::render(
            'admin/Marketplace/Categorias/Edit',
            [
                'categoria' => $categoria,
            ]
        );
    }

    /**
     * Actualizar una categoría.
     */
    public function update(
        Request $request,
        MarketplaceCategoria $categoria
    ): RedirectResponse {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'marketplace_categorias',
                    'nombre'
                )->ignore($categoria->id),
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique(
                    'marketplace_categorias',
                    'slug'
                )->ignore($categoria->id),
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'icono' => [
                'nullable',
                'string',
                'max:100',
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

        $categoria->update([
            'nombre' => $validated['nombre'],

            'slug' => $this->generarSlug(
                $validated['slug']
                    ?? $validated['nombre'],
                $categoria->id
            ),

            'descripcion' =>
            $validated['descripcion'] ?? null,

            'icono' =>
            $validated['icono'] ?? null,

            'activo' =>
            $validated['activo'] ?? false,

            'orden' =>
            $validated['orden'] ?? 0,
        ]);

        return redirect()
            ->route('admin.marketplace.categorias.index')
            ->with(
                'success',
                'La categoría fue actualizada correctamente.'
            );
    }

    /**
     * Activar una categoría.
     */
    public function activar(
        MarketplaceCategoria $categoria
    ): RedirectResponse {
        $categoria->update([
            'activo' => true,
        ]);

        return back()->with(
            'success',
            'La categoría fue activada.'
        );
    }

    /**
     * Desactivar una categoría.
     */
    public function desactivar(
        MarketplaceCategoria $categoria
    ): RedirectResponse {
        $categoria->update([
            'activo' => false,
        ]);

        return back()->with(
            'success',
            'La categoría fue desactivada.'
        );
    }

    /**
     * Eliminar una categoría.
     */
    public function destroy(
        MarketplaceCategoria $categoria
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | No permitimos eliminar una categoría que tenga publicaciones.
        |--------------------------------------------------------------------------
        */
        if ($categoria->publicaciones()->exists()) {
            return back()->with(
                'error',
                'No puedes eliminar una categoría que tiene publicaciones asociadas. Desactívala en su lugar.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Tampoco eliminamos si tiene subcategorías.
        |--------------------------------------------------------------------------
        */
        if ($categoria->subcategorias()->exists()) {
            return back()->with(
                'error',
                'No puedes eliminar una categoría que tiene subcategorías asociadas.'
            );
        }

        $categoria->delete();

        return redirect()
            ->route('admin.marketplace.categorias.index')
            ->with(
                'success',
                'La categoría fue eliminada correctamente.'
            );
    }

    /**
     * Generar un slug único.
     */
    private function generarSlug(
        string $valor,
        ?int $exceptoId = null
    ): string {
        $slugBase = Str::slug($valor);

        if ($slugBase === '') {
            $slugBase = 'categoria';
        }

        $slug = $slugBase;
        $contador = 1;

        while (true) {
            $query = MarketplaceCategoria::query()
                ->where('slug', $slug);

            if ($exceptoId !== null) {
                $query->where('id', '!=', $exceptoId);
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
