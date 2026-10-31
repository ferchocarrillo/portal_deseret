<?php

namespace App\Http\Controllers\Clasificado;

use App\Http\Controllers\Controller;
use App\Models\Clasificado;
use App\Models\ClasificadoConversacion;
use App\Models\ClasificadoMensaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ConversacionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO DE CONVERSACIONES
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $userId = Auth::id();

        $conversaciones = ClasificadoConversacion::query()
            ->with([
                'clasificado' => function ($query) {
                    $query->select([
                        'id',
                        'titulo',
                        'slug',
                        'estado',
                    ]);
                },

                'clasificado.imagenes' => function ($query) {
                    $query
                        ->select([
                            'id',
                            'clasificado_id',
                            'ruta',
                            'orden',
                        ])
                        ->orderBy('orden');
                },

                'comprador:id,name',
                'vendedor:id,name',

                'ultimoMensaje',
            ])

            ->withCount([
                'mensajes as mensajes_no_leidos' => function ($query) use ($userId) {
                    $query
                        ->whereNull('leido_at')
                        ->where('user_id', '!=', $userId);
                },
            ])

            ->where(function ($query) use ($userId) {
                $query
                    ->where('comprador_id', $userId)
                    ->orWhere('vendedor_id', $userId);
            })

            ->orderByDesc('ultimo_mensaje_at')
            ->orderByDesc('updated_at')
            ->get();

        return Inertia::render('Clasificados/Mensajes', [
            'conversaciones' => $conversaciones,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR / OBTENER CONVERSACIÓN
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Clasificado $clasificado
    ) {
        $userId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | El clasificado debe estar publicado
        |--------------------------------------------------------------------------
        */

        if ($clasificado->estado !== 'publicado') {
            return back()->with(
                'error',
                'Este clasificado no está disponible para recibir mensajes.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | El propietario no puede contactarse a sí mismo
        |--------------------------------------------------------------------------
        */

        if ($clasificado->user_id === $userId) {
            return back()->with(
                'error',
                'No puedes iniciar una conversación con tu propia publicación.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Buscar o crear conversación
        |--------------------------------------------------------------------------
        */

        $conversacion = ClasificadoConversacion::firstOrCreate(
            [
                'clasificado_id' => $clasificado->id,
                'comprador_id' => $userId,
                'vendedor_id' => $clasificado->user_id,
            ],
            [
                'ultimo_mensaje_at' => null,
            ]
        );

        return redirect()->route(
            'clasificados.mensajes.show',
            $conversacion
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR CONVERSACIÓN
    |--------------------------------------------------------------------------
    */

    public function show(
        ClasificadoConversacion $conversacion
    ) {
        $userId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Verificar que el usuario pertenezca a la conversación
        |--------------------------------------------------------------------------
        */

        if (
            $conversacion->comprador_id !== $userId &&
            $conversacion->vendedor_id !== $userId
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Cargar información
        |--------------------------------------------------------------------------
        */

        $conversacion->load([
            'clasificado.imagenes',
            'clasificado.usuario',
            'comprador',
            'vendedor',
            'mensajes.usuario',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Marcar como leídos los mensajes del otro usuario
        |--------------------------------------------------------------------------
        */

        $conversacion->mensajes()
            ->where('user_id', '!=', $userId)
            ->whereNull('leido_at')
            ->update([
                'leido_at' => now(),
            ]);

        return Inertia::render('Clasificados/Conversacion', [
            'conversacion' => $conversacion,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CONTADOR DE MENSAJES NO LEÍDOS
    |--------------------------------------------------------------------------
    */

    public function noLeidos()
    {
        $userId = Auth::id();

        $cantidad = ClasificadoMensaje::query()
            ->where('user_id', '!=', $userId)
            ->whereNull('leido_at')
            ->whereHas('conversacion', function ($query) use ($userId) {
                $query
                    ->where('comprador_id', $userId)
                    ->orWhere('vendedor_id', $userId);
            })
            ->count();

        return response()->json([
            'cantidad' => $cantidad,
        ]);
    }
}
