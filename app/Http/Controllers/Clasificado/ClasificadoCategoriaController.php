<?php

namespace App\Http\Controllers\Clasificado;

use App\Http\Controllers\Controller;
use App\Models\ClasificadoCategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ClasificadoCategoriaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Listado
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $categorias = ClasificadoCategoria::query()
            ->whereNull('parent_id')
            ->with([
                'hijas' => function ($query) {
                    $query->with([
                        'hijas' => function ($query) {
                            $query->with('hijas')
                                ->orderBy('orden');
                        },
                    ])
                        ->orderBy('orden');
                },
            ])
            ->orderBy('orden')
            ->get();

        return Inertia::render(
            'Clasificados/Categorias/Index',
            [
                'categorias' => $categorias,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Formulario de creación
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categorias = ClasificadoCategoria::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        return Inertia::render(
            'Clasificados/Categorias/Create',
            [
                'categorias' => $categorias,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Guardar
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => [
                'nullable',
                'exists:clasificado_categorias,id',
            ],

            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
            ],

            'descripcion' => [
                'nullable',
                'string',
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

        /*
        |--------------------------------------------------------------------------
        | Generar slug
        |--------------------------------------------------------------------------
        */

        $slugBase = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['nombre']);

        $slug = $this->generarSlugUnico($slugBase);

        /*
        |--------------------------------------------------------------------------
        | Crear categoría
        |--------------------------------------------------------------------------
        */

        ClasificadoCategoria::create([
            'parent_id' => $validated['parent_id'] ?? null,
            'nombre' => $validated['nombre'],
            'slug' => $slug,
            'descripcion' => $validated['descripcion'] ?? null,
            'activo' => $validated['activo'] ?? true,
            'orden' => $validated['orden'] ?? 0,
        ]);

        return redirect()
            ->route('clasificados.categorias.index')
            ->with(
                'success',
                'La categoría fue creada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Formulario de edición
    |--------------------------------------------------------------------------
    */

    public function edit(ClasificadoCategoria $categoria)
    {
        $categorias = ClasificadoCategoria::query()
            ->where('id', '!=', $categoria->id)
            ->where('activo', true)
            ->orderBy('orden')
            ->get();

        return Inertia::render(
            'Clasificados/Categorias/Edit',
            [
                'categoria' => $categoria,
                'categorias' => $categorias,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Actualizar
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        ClasificadoCategoria $categoria
    ) {
        $validated = $request->validate([
            'parent_id' => [
                'nullable',
                'exists:clasificado_categorias,id',
            ],

            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
            ],

            'descripcion' => [
                'nullable',
                'string',
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

        /*
        |--------------------------------------------------------------------------
        | Una categoría no puede ser su propia padre
        |--------------------------------------------------------------------------
        */

        if (
            !empty($validated['parent_id']) &&
            (int) $validated['parent_id'] === $categoria->id
        ) {
            return back()
                ->withErrors([
                    'parent_id' =>
                    'Una categoría no puede ser su propia categoría padre.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Evitar ciclos en la jerarquía
        |--------------------------------------------------------------------------
        */

        if (
            !empty($validated['parent_id']) &&
            $this->esDescendiente(
                $validated['parent_id'],
                $categoria->id
            )
        ) {
            return back()
                ->withErrors([
                    'parent_id' =>
                    'No puedes asignar una categoría descendiente como categoría padre.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Slug
        |--------------------------------------------------------------------------
        */

        $slugBase = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['nombre']);

        $slug = $this->generarSlugUnico(
            $slugBase,
            $categoria->id
        );

        $categoria->update([
            'parent_id' => $validated['parent_id'] ?? null,
            'nombre' => $validated['nombre'],
            'slug' => $slug,
            'descripcion' => $validated['descripcion'] ?? null,
            'activo' => $validated['activo'] ?? false,
            'orden' => $validated['orden'] ?? 0,
        ]);

        return redirect()
            ->route('clasificados.categorias.index')
            ->with(
                'success',
                'La categoría fue actualizada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Activar / desactivar
    |--------------------------------------------------------------------------
    */

    public function toggleActivo(
        ClasificadoCategoria $categoria
    ) {
        $categoria->update([
            'activo' => !$categoria->activo,
        ]);

        return back()->with(
            'success',
            $categoria->activo
                ? 'La categoría fue activada.'
                : 'La categoría fue desactivada.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar
    |--------------------------------------------------------------------------
    */

    public function destroy(
        ClasificadoCategoria $categoria
    ) {
        /*
        |--------------------------------------------------------------------------
        | No eliminar si tiene categorías hijas
        |--------------------------------------------------------------------------
        */

        if ($categoria->hijas()->exists()) {
            return back()->withErrors([
                'categoria' =>
                'No puedes eliminar una categoría que tiene subcategorías.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | No eliminar si tiene publicaciones
        |--------------------------------------------------------------------------
        */

        if ($categoria->clasificados()->exists()) {
            return back()->withErrors([
                'categoria' =>
                'No puedes eliminar una categoría que tiene clasificados asociados. Puedes desactivarla.',
            ]);
        }

        $categoria->delete();

        return redirect()
            ->route('clasificados.categorias.index')
            ->with(
                'success',
                'La categoría fue eliminada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    private function generarSlugUnico(
        string $slug,
        ?int $exceptoId = null
    ): string {
        $original = $slug;
        $contador = 1;

        while (
            ClasificadoCategoria::query()
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

    private function esDescendiente(
        int $posibleDescendiente,
        int $categoriaId
    ): bool {
        $categoria = ClasificadoCategoria::find(
            $posibleDescendiente
        );

        while ($categoria) {
            if (
                $categoria->parent_id === $categoriaId
            ) {
                return true;
            }

            $categoria = $categoria->padre;
        }

        return false;
    }
}
