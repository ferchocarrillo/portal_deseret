<?php

namespace App\Http\Controllers\Clasificado;

use App\Http\Controllers\Controller;
use App\Models\Clasificado;
use App\Models\ClasificadoDenuncia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClasificadoDenunciaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registrar denuncia
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Clasificado $clasificado
    ) {
        /*
        |--------------------------------------------------------------------------
        | El propietario no puede denunciar su propia publicación
        |--------------------------------------------------------------------------
        */

        abort_if(
            $clasificado->user_id === Auth::id(),
            403,
            'No puedes denunciar tu propia publicación.'
        );

        /*
        |--------------------------------------------------------------------------
        | La publicación debe estar activa
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $clasificado->estado === 'publicado',
            404
        );

        /*
        |--------------------------------------------------------------------------
        | Validación
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'motivo' => [
                'required',
                'string',
                'max:255',
            ],

            'descripcion' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Evitar denuncias duplicadas pendientes
        |--------------------------------------------------------------------------
        */

        $denunciaExistente = ClasificadoDenuncia::query()
            ->where('clasificado_id', $clasificado->id)
            ->where('user_id', Auth::id())
            ->whereIn('estado', [
                'pendiente',
                'en_revision',
            ])
            ->exists();

        if ($denunciaExistente) {
            return back()->withErrors([
                'denuncia' =>
                'Ya tienes una denuncia pendiente para esta publicación.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Crear denuncia
        |--------------------------------------------------------------------------
        */

        ClasificadoDenuncia::create([
            'clasificado_id' => $clasificado->id,
            'user_id' => Auth::id(),
            'motivo' => $validated['motivo'],
            'descripcion' => $validated['descripcion'] ?? null,
            'estado' => 'pendiente',
        ]);

        return back()->with(
            'success',
            'La denuncia fue enviada correctamente. Será revisada por el equipo de administración.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Listado administrativo
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $denuncias = ClasificadoDenuncia::query()
            ->with([
                'clasificado' => function ($query) {
                    $query->with([
                        'imagenes' => function ($query) {
                            $query->where('es_principal', true);
                        },
                        'usuario',
                        'categoria',
                    ]);
                },
                'usuario',
                'revisor',
            ])
            ->when(
                $request->filled('estado'),
                fn($query) =>
                $query->where(
                    'estado',
                    $request->input('estado')
                )
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render(
            'Clasificados/Denuncias/Index',
            [
                'denuncias' => $denuncias,
                'filtros' => [
                    'estado' => $request->input('estado'),
                ],
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ver denuncia
    |--------------------------------------------------------------------------
    */

    public function show(ClasificadoDenuncia $denuncia)
    {
        $denuncia->load([
            'clasificado' => function ($query) {
                $query->with([
                    'usuario',
                    'categoria',
                    'imagenes' => function ($query) {
                        $query->orderBy('orden');
                    },
                    'vehiculo',
                    'propiedad',
                    'pais',
                    'divisionAdministrativa',
                    'ciudad',
                ]);
            },
            'usuario',
            'revisor',
        ]);

        return Inertia::render(
            'Clasificados/Denuncias/Show',
            [
                'denuncia' => $denuncia,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Marcar como en revisión
    |--------------------------------------------------------------------------
    */

    public function enRevision(
        ClasificadoDenuncia $denuncia
    ) {
        $denuncia->update([
            'estado' => 'en_revision',
            'revisado_por' => Auth::id(),
            'revisada_at' => now(),
        ]);

        return back()->with(
            'success',
            'La denuncia fue marcada como en revisión.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Resolver denuncia
    |--------------------------------------------------------------------------
    */

    public function resolver(
        Request $request,
        ClasificadoDenuncia $denuncia
    ) {
        $validated = $request->validate([
            'respuesta_admin' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $denuncia->update([
            'estado' => 'resuelta',
            'respuesta_admin' =>
            $validated['respuesta_admin'] ?? null,
            'revisado_por' => Auth::id(),
            'revisada_at' => now(),
        ]);

        return back()->with(
            'success',
            'La denuncia fue resuelta.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rechazar denuncia
    |--------------------------------------------------------------------------
    */

    public function rechazar(
        Request $request,
        ClasificadoDenuncia $denuncia
    ) {
        $validated = $request->validate([
            'respuesta_admin' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $denuncia->update([
            'estado' => 'rechazada',
            'respuesta_admin' =>
            $validated['respuesta_admin'] ?? null,
            'revisado_por' => Auth::id(),
            'revisada_at' => now(),
        ]);

        return back()->with(
            'success',
            'La denuncia fue rechazada.'
        );
    }
}
