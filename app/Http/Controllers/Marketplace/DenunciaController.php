<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceDenuncia;
use App\Models\MarketplacePublicacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DenunciaController extends Controller
{
    /**
     * Mostrar formulario para denunciar una publicación.
     */
    public function create(
        MarketplacePublicacion $publicacion
    ): Response {
        /*
        |--------------------------------------------------------------------------
        | Solo se pueden denunciar publicaciones que estén publicadas.
        |--------------------------------------------------------------------------
        */
        abort_unless(
            $publicacion->estado_publicacion === 'publicada',
            404
        );

        /*
        |--------------------------------------------------------------------------
        | El propietario no puede denunciar su propia publicación.
        |--------------------------------------------------------------------------
        */
        abort_unless(
            $publicacion->user_id !== auth()->id(),
            403
        );

        $publicacion->load([
            'categoria:id,nombre,slug',
            'imagenes' => function ($query) {
                $query->orderByDesc('es_principal')
                    ->orderBy('orden');
            },
        ]);

        return Inertia::render(
            'Marketplace/Denunciar',
            [
                'publicacion' => $publicacion,

                'motivos' => $this->motivos(),
            ]
        );
    }

    /**
     * Registrar una denuncia.
     */
    public function store(
        Request $request,
        MarketplacePublicacion $publicacion
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | La publicación debe estar publicada.
        |--------------------------------------------------------------------------
        */
        abort_unless(
            $publicacion->estado_publicacion === 'publicada',
            404
        );

        /*
        |--------------------------------------------------------------------------
        | El propietario no puede denunciar su propia publicación.
        |--------------------------------------------------------------------------
        */
        if ($publicacion->user_id === auth()->id()) {
            return back()->with(
                'error',
                'No puedes denunciar tu propia publicación.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validación.
        |--------------------------------------------------------------------------
        */
        $validated = $request->validate([
            'motivo' => [
                'required',
                'string',
                Rule::in(array_keys($this->motivos())),
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Registrar denuncia.
        |
        | No suspendemos ni eliminamos automáticamente la publicación.
        | La denuncia queda pendiente para revisión administrativa.
        |--------------------------------------------------------------------------
        */
        MarketplaceDenuncia::create([
            'publicacion_id' => $publicacion->id,
            'user_id' => auth()->id(),

            'motivo' => $validated['motivo'],

            'descripcion' => $validated['descripcion'] ?? null,

            'estado' => 'pendiente',

            'resolucion' => null,
            'resuelto_por' => null,
            'resuelto_at' => null,
        ]);

        return redirect()
            ->route(
                'marketplace.show',
                $publicacion
            )
            ->with(
                'success',
                'La denuncia fue enviada correctamente. Será revisada por el equipo administrativo.'
            );
    }

    /**
     * Motivos disponibles para denunciar.
     */
    private function motivos(): array
    {
        return [
            'producto_prohibido' => 'Producto prohibido',
            'posible_estafa' => 'Posible estafa',
            'informacion_enganosa' => 'Información engañosa',
            'contenido_inapropiado' => 'Contenido inapropiado',
            'publicacion_duplicada' => 'Publicación duplicada',
            'categoria_incorrecta' => 'Categoría incorrecta',
            'otro' => 'Otro',
        ];
    }
}
