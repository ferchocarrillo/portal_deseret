<?php

namespace App\Http\Middleware;

use App\Models\ClasificadoMensaje;
use App\Models\EmprendimientoMensaje;
use App\Models\EventoMensaje;
use App\Models\MarketplaceMensaje;
use App\Models\ServicioMensaje;
use App\Models\SolicitudIngreso;
use App\Models\UserInfraction;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $usuario = $request->user();

        // Buscar si el usuario autenticado tiene alguna infracción pendiente por reconocer
        $activeInfraction = null;
        if ($usuario) {
            $infraction = $usuario->infractions()
                ->whereNull('read_at')
                ->latest()
                ->first();

            if ($infraction) {
                $activeInfraction = [
                    'id'           => $infraction->id,
                    'rule_id'      => $infraction->rule_id,
                    'detail'       => $infraction->detail,
                    'strike_level' => $infraction->strike_level,
                    'rule'         => config("community_rules.rules.{$infraction->rule_id}"),
                    'consequence'  => config("community_rules.consequences.{$infraction->strike_level}"),
                ];
            }
        }

        return [
            ...parent::share($request),

            /*
            |--------------------------------------------------------------------------
            | AUTENTICACIÓN
            |--------------------------------------------------------------------------
            */

            'auth' => [
                'user' => $usuario,

                'permissions' => $usuario
                    ? $usuario->getAllPermissions()
                    ->pluck('name')
                    ->values()
                    ->toArray()
                    : [],
            ],

            /*
            |--------------------------------------------------------------------------
            | PASARELA DE PAGOS (WOMPI)
            |--------------------------------------------------------------------------
            */

            'wompiPublicKey' => config('services.wompi.public_key'),

            /*
            |--------------------------------------------------------------------------
            | SOLICITUDES DE INGRESO
            |--------------------------------------------------------------------------
            */

            'solicitudesPendientes' => SolicitudIngreso::where(
                'estado',
                SolicitudIngreso::ESTADO_PENDIENTE
            )->count(),

            /*
            |--------------------------------------------------------------------------
            | MENSAJES NO LEÍDOS
            |--------------------------------------------------------------------------
            */

            'mensajesNoLeidos' => $usuario
                ? (
                    MarketplaceMensaje::query()
                    ->where('user_id', '!=', $usuario->id)
                    ->whereNull('leido_at')
                    ->whereHas('conversacion', function ($query) use ($usuario) {
                        $query
                            ->where('comprador_id', $usuario->id)
                            ->orWhere('vendedor_id', $usuario->id);
                    })
                    ->count()

                    +

                    ClasificadoMensaje::query()
                    ->where('user_id', '!=', $usuario->id)
                    ->whereNull('leido_at')
                    ->whereHas('conversacion', function ($query) use ($usuario) {
                        $query
                            ->where('comprador_id', $usuario->id)
                            ->orWhere('vendedor_id', $usuario->id);
                    })
                    ->count()

                    +

                    EmprendimientoMensaje::query()
                    ->where('user_id', '!=', $usuario->id)
                    ->whereNull('leido_at')
                    ->whereHas('conversacion', function ($query) use ($usuario) {
                        $query
                            ->where('usuario_iniciador_id', $usuario->id)
                            ->orWhere('usuario_receptor_id', $usuario->id);
                    })
                    ->count()

                    +

                    ServicioMensaje::query()
                    ->where('user_id', '!=', $usuario->id)
                    ->whereNull('leido_at')
                    ->whereHas('conversacion', function ($query) use ($usuario) {
                        $query
                            ->where('usuario_interesado_id', $usuario->id)
                            ->orWhere('propietario_id', $usuario->id);
                    })
                    ->count()

                    +

                    EventoMensaje::query()
                    ->where('user_id', '!=', $usuario->id)
                    ->whereNull('leido_at')
                    ->whereHas('conversacion', function ($query) use ($usuario) {
                        $query
                            ->where('comprador_id', $usuario->id)
                            ->orWhere('organizador_id', $usuario->id);
                    })
                    ->count()
                )
                : 0,

            /*
            |--------------------------------------------------------------------------
            | MODERACIÓN / NORMAS DE LA COMUNIDAD
            |--------------------------------------------------------------------------
            */

            'activeInfraction' => $activeInfraction,
            'communityRules'   => config('community_rules.rules', []),
        ];
    }
}
