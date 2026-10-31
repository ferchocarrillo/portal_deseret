<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceFavorito;
use App\Models\MarketplacePublicacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FavoritoController extends Controller
{
    /**
     * Agregar una publicación a favoritos.
     */
    public function store(
        MarketplacePublicacion $publicacion
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | Solo se pueden marcar como favoritas publicaciones públicas.
        |--------------------------------------------------------------------------
        */
        abort_unless(
            $publicacion->estado_publicacion === 'publicada',
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Evitar que el propietario agregue su propia publicación.
        |--------------------------------------------------------------------------
        */
        if ($publicacion->user_id === auth()->id()) {
            return back()->with(
                'error',
                'No puedes agregar tu propia publicación a favoritos.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Crear favorito.
        |
        | firstOrCreate evita duplicados aunque el usuario haga clic
        | varias veces.
        |--------------------------------------------------------------------------
        */
        MarketplaceFavorito::firstOrCreate([
            'user_id' => auth()->id(),
            'publicacion_id' => $publicacion->id,
        ]);

        return back()->with(
            'success',
            'Publicación agregada a favoritos.'
        );
    }

    /**
     * Eliminar una publicación de favoritos.
     */
    public function destroy(
        MarketplacePublicacion $publicacion
    ): RedirectResponse {
        MarketplaceFavorito::query()
            ->where('user_id', auth()->id())
            ->where('publicacion_id', $publicacion->id)
            ->delete();

        return back()->with(
            'success',
            'Publicación eliminada de favoritos.'
        );
    }

    /**
     * Mostrar los favoritos del usuario autenticado.
     */
    public function index(
        Request $request
    ): Response {
        $favoritos = MarketplaceFavorito::query()
            ->with([
                'publicacion' => function ($query) {
                    $query->with([
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
                    ]);
                },
            ])
            ->where('user_id', auth()->id())
            ->whereHas('publicacion')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render(
            'Marketplace/Favoritos',
            [
                'favoritos' => $favoritos,
            ]
        );
    }
}
