<?php

namespace App\Http\Controllers\Admin\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceCategoria;
use App\Models\MarketplacePublicacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MarketplaceAdminController extends Controller
{
    /**
     * Panel principal de administración del Marketplace.
     */
    public function index(Request $request): Response
    {
        $query = MarketplacePublicacion::query()
            ->with([
                'usuario:id,name',
                'categoria:id,nombre,slug',
                'subcategoria:id,nombre,slug',
            ])
            ->withCount('denuncias');

        /*
        |--------------------------------------------------------------------------
        | Búsqueda
        |--------------------------------------------------------------------------
        */
        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);

            $query->where(function ($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhereHas('usuario', function ($q) use ($buscar) {
                        $q->where('name', 'like', "%{$buscar}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Estado de publicación
        |--------------------------------------------------------------------------
        */
        if ($request->filled('estado')) {
            $query->where(
                'estado_publicacion',
                $request->estado
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Categoría
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
        | Solo publicaciones denunciadas
        |--------------------------------------------------------------------------
        */
        if ($request->boolean('con_denuncias')) {
            $query->has('denuncias');
        }

        /*
        |--------------------------------------------------------------------------
        | Orden
        |--------------------------------------------------------------------------
        */
        $query->orderByDesc('created_at');

        /*
        |--------------------------------------------------------------------------
        | Publicaciones
        |--------------------------------------------------------------------------
        */
        $publicaciones = $query
            ->withTrashed()
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Categorías
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
            'admin/Marketplace/Index',
            [
                'publicaciones' => $publicaciones,

                'categorias' => $categorias,

                'filtros' => [
                    'buscar' => $request->get('buscar'),
                    'estado' => $request->get('estado'),
                    'categoria_id' => $request->get('categoria_id'),
                    'con_denuncias' =>
                    $request->boolean('con_denuncias'),
                ],

                'estados' => [
                    [
                        'value' => 'publicada',
                        'label' => 'Publicada',
                    ],
                    [
                        'value' => 'reservada',
                        'label' => 'Reservada',
                    ],
                    [
                        'value' => 'vendida',
                        'label' => 'Vendida',
                    ],
                    [
                        'value' => 'pausada',
                        'label' => 'Pausada',
                    ],
                    [
                        'value' => 'suspendida',
                        'label' => 'Suspendida',
                    ],
                    [
                        'value' => 'eliminada',
                        'label' => 'Eliminada',
                    ],
                ],
            ]
        );
    }

    /**
     * Mostrar una publicación desde administración.
     */
    public function show(
        MarketplacePublicacion $publicacion
    ): Response {
        $publicacion->load([
            'usuario:id,name,email',
            'categoria:id,nombre,slug',
            'subcategoria:id,nombre,slug',
            'pais',
            'divisionAdministrativa',
            'ciudad',
            'imagenes' => function ($query) {
                $query->orderByDesc('es_principal')
                    ->orderBy('orden');
            },
            'video',
            'denuncias' => function ($query) {
                $query->latest();
            },
            'denuncias.usuario:id,name',
            'denuncias.resueltoPor:id,name',
        ]);

        return Inertia::render(
            'admin/Marketplace/PublicacionShow',
            [
                'publicacion' => $publicacion,
            ]
        );
    }

    /**
     * Suspender una publicación.
     */
    public function suspender(
        Request $request,
        MarketplacePublicacion $publicacion
    ): RedirectResponse {
        $validated = $request->validate([
            'motivo_suspension' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],
        ]);

        if (
            in_array(
                $publicacion->estado_publicacion,
                ['eliminada', 'vendida'],
                true
            )
        ) {
            return back()->with(
                'error',
                'Esta publicación no puede ser suspendida en su estado actual.'
            );
        }

        $publicacion->update([
            'estado_publicacion' => 'suspendida',
            'fecha_suspension' => now(),
            'motivo_suspension' =>
            $validated['motivo_suspension'],
        ]);

        return back()->with(
            'success',
            'La publicación fue suspendida correctamente.'
        );
    }

    /**
     * Reactivar una publicación suspendida.
     */
    public function reactivar(
        MarketplacePublicacion $publicacion
    ): RedirectResponse {
        if (
            $publicacion->estado_publicacion !== 'suspendida'
        ) {
            return back()->with(
                'error',
                'Solo puedes reactivar publicaciones suspendidas.'
            );
        }

        $publicacion->update([
            'estado_publicacion' => 'publicada',
            'fecha_suspension' => null,
            'motivo_suspension' => null,
            'fecha_publicacion' => now(),
        ]);

        return back()->with(
            'success',
            'La publicación fue reactivada correctamente.'
        );
    }

    /**
     * Eliminar una publicación desde administración.
     */
    public function destroy(
        MarketplacePublicacion $publicacion
    ): RedirectResponse {
        $publicacion->update([
            'estado_publicacion' => 'eliminada',
        ]);

        $publicacion->delete();

        return redirect()
            ->route('admin.marketplace.index')
            ->with(
                'success',
                'La publicación fue eliminada correctamente.'
            );
    }

    /**
     * Restaurar una publicación eliminada.
     */
    public function restaurar(
        int $publicacion
    ): RedirectResponse {
        $registro = MarketplacePublicacion::withTrashed()
            ->findOrFail($publicacion);

        if (
            $registro->estado_publicacion !== 'eliminada'
        ) {
            return back()->with(
                'error',
                'Esta publicación no está eliminada.'
            );
        }

        $registro->restore();

        $registro->update([
            'estado_publicacion' => 'publicada',
            'fecha_publicacion' => now(),
        ]);

        return back()->with(
            'success',
            'La publicación fue restaurada correctamente.'
        );
    }

    /**
     * Cambiar el estado de una publicación manualmente.
     *
     * Este método sirve para acciones administrativas controladas.
     */
    public function cambiarEstado(
        Request $request,
        MarketplacePublicacion $publicacion
    ): RedirectResponse {
        $validated = $request->validate([
            'estado_publicacion' => [
                'required',
                Rule::in([
                    'publicada',
                    'reservada',
                    'vendida',
                    'pausada',
                    'suspendida',
                    'eliminada',
                ]),
            ],
        ]);

        $estado = $validated['estado_publicacion'];

        $datos = [
            'estado_publicacion' => $estado,
        ];

        /*
        |--------------------------------------------------------------------------
        | Fechas asociadas al estado.
        |--------------------------------------------------------------------------
        */
        switch ($estado) {
            case 'publicada':
                $datos['fecha_publicacion'] = now();
                $datos['fecha_pausa'] = null;
                $datos['fecha_suspension'] = null;
                $datos['motivo_suspension'] = null;
                break;

            case 'reservada':
                $datos['fecha_reserva'] = now();
                break;

            case 'vendida':
                $datos['fecha_venta'] = now();
                break;

            case 'pausada':
                $datos['fecha_pausa'] = now();
                break;

            case 'suspendida':
                $datos['fecha_suspension'] = now();
                break;
        }

        $publicacion->update($datos);

        /*
        |--------------------------------------------------------------------------
        | Eliminación lógica si el administrador selecciona eliminada.
        |--------------------------------------------------------------------------
        */
        if ($estado === 'eliminada') {
            $publicacion->delete();
        }

        return back()->with(
            'success',
            'El estado de la publicación fue actualizado.'
        );
    }
}
