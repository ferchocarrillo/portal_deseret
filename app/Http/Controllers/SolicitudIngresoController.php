<?php

namespace App\Http\Controllers;

use App\Models\SolicitudIngreso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SolicitudIngresoController extends Controller
{
    /**
     * Mostrar las solicitudes de ingreso en el panel administrativo.
     */
    public function index()
    {
        // ==========================================================
        // SOLICITUDES
        // ==========================================================

        $solicitudes = SolicitudIngreso::query()
            ->orderByDesc('created_at')
            ->get();


        // ==========================================================
        // CONTADORES
        // ==========================================================

        $pendientes = SolicitudIngreso::where(
            'estado',
            SolicitudIngreso::ESTADO_PENDIENTE
        )->count();

        $aprobadas = SolicitudIngreso::where(
            'estado',
            'aprobada'
        )->count();

        $rechazadas = SolicitudIngreso::where(
            'estado',
            'rechazada'
        )->count();

        $total = SolicitudIngreso::count();


        // ==========================================================
        // VISTA ADMINISTRATIVA
        // ==========================================================

        return Inertia::render('admin/SolicitudesIngreso', [

            'solicitudes' => $solicitudes,

            'pendientes' => $pendientes,

            'aprobadas' => $aprobadas,

            'rechazadas' => $rechazadas,

            'total' => $total,

        ]);
    }


    /**
     * Guardar una nueva solicitud de ingreso.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([

            // ==========================================================
            // DECISIÓN INICIAL
            // ==========================================================

            'es_miembro' => [
                'required',
                'boolean',
            ],


            // ==========================================================
            // DATOS PERSONALES
            // ==========================================================

            'nombres' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñÀ-ÖØ-öø-ÿ\'\-\s]+$/u',
            ],

            'apellidos' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñÀ-ÖØ-öø-ÿ\'\-\s]+$/u',
            ],

            'tipo_documento' => [
                'required',
                'string',
                'in:CC,CE,TI,PAS',
            ],

            'documento' => [
                'required',
                'string',
                'max:10',
                'regex:/^[0-9]+$/',
            ],


            // ==========================================================
            // UBICACIÓN
            // ==========================================================

            'pais' => [
                'required',
                'string',
                'size:2',
            ],

            'region' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñÀ-ÖØ-öø-ÿ\'\-\s]+$/u',
            ],

            'ciudad' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñÀ-ÖØ-öø-ÿ\'\-\s]+$/u',
            ],


            // ==========================================================
            // CONTACTO
            // ==========================================================

            'telefono' => [
                'required',
                'string',
                'max:15',
                'regex:/^[0-9]+$/',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],


            // ==========================================================
            // INFORMACIÓN COMO MIEMBRO
            // ==========================================================

            'estaca' => [
                'nullable',
                'string',
                'max:255',
                'required_if:es_miembro,1',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñÀ-ÖØ-öø-ÿ\'\-\s]+$/u',
            ],

            'barrio' => [
                'nullable',
                'string',
                'max:255',
                'required_if:es_miembro,1',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñÀ-ÖØ-öø-ÿ\'\-\s]+$/u',
            ],

            'nombre_obispo' => [
                'nullable',
                'string',
                'max:255',
                'required_if:es_miembro,1',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñÀ-ÖØ-öø-ÿ\'\-\s]+$/u',
            ],


            // ==========================================================
            // INFORMACIÓN DE NO MIEMBRO
            // ==========================================================

            'motivo_ingreso' => [
                'nullable',
                'string',
                'required_if:es_miembro,0',
            ],


            // ==========================================================
            // AVAL
            // ==========================================================

            'tiene_aval' => [
                'nullable',
                'required_if:es_miembro,0',
                'boolean',

                Rule::when(
                    $request->input('es_miembro') === '0',
                    ['accepted']
                ),
            ],

            'aval_nombres' => [
                'nullable',
                'string',
                'max:100',
                'required_if:tiene_aval,1',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñÀ-ÖØ-öø-ÿ\'\-\s]+$/u',
            ],

            'aval_apellidos' => [
                'nullable',
                'string',
                'max:100',
                'required_if:tiene_aval,1',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñÀ-ÖØ-öø-ÿ\'\-\s]+$/u',
            ],

            'aval_email' => [
                'nullable',
                'email',
                'max:255',
                'required_if:tiene_aval,1',
            ],

            'aval_telefono' => [
                'nullable',
                'string',
                'max:15',
                'required_if:tiene_aval,1',
                'regex:/^[0-9]+$/',
            ],

            'aval_barrio' => [
                'nullable',
                'string',
                'max:255',
                'required_if:tiene_aval,1',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÜüÑñÀ-ÖØ-öø-ÿ\'\-\s]+$/u',
            ],


            // ==========================================================
            // INFORMACIÓN ADICIONAL
            // ==========================================================

            'observaciones' => [
                'nullable',
                'string',
                'max:250',
            ],


            // ==========================================================
            // CONSENTIMIENTOS
            // ==========================================================

            'acepta_terminos' => [
                'required',
                'accepted',
            ],

            'ha_leido_privacidad' => [
                'required',
                'accepted',
            ],

            'autoriza_tratamiento_datos' => [
                'required',
                'accepted',
            ],

        ]);


        // ==========================================================
        // ESTADO INICIAL
        // ==========================================================

        $validated['estado'] = SolicitudIngreso::ESTADO_PENDIENTE;

        $validated['user_id'] = null;


        // ==========================================================
        // REVISIÓN ADMINISTRATIVA
        // ==========================================================

        $validated['revisado_por'] = null;

        $validated['fecha_revision'] = null;

        $validated['observacion_revision'] = null;


        // ==========================================================
        // GUARDAR SOLICITUD
        // ==========================================================

        SolicitudIngreso::create($validated);


        // ==========================================================
        // RESPUESTA
        // ==========================================================

        return redirect()
            ->route('welcome')
            ->with(
                'success',
                'Hemos recibido tu solicitud. Agradecemos que la hayas enviado. Vamos a validar la información y te notificaremos el resultado. ¡Hasta pronto!'
            );
    }


    /**
     * Evaluar una solicitud de ingreso.
     */
    public function evaluar(
        Request $request,
        SolicitudIngreso $solicitud
    ): RedirectResponse {

        // ==========================================================
        // VALIDAR DECISIÓN
        // ==========================================================

        $validated = $request->validate([

            'decision' => [
                'required',
                'in:aprobada,rechazada',
            ],

            'observacion_revision' => [
                'nullable',
                'string',
                'max:1000',
            ],

        ]);


        // ==========================================================
        // IMPEDIR REVISAR UNA SOLICITUD YA PROCESADA
        // ==========================================================

        if ($solicitud->estado !== SolicitudIngreso::ESTADO_PENDIENTE) {

            return redirect()
                ->route('admin.solicitudes.index')
                ->with(
                    'error',
                    'Esta solicitud ya fue evaluada anteriormente.'
                );
        }


        // ==========================================================
        // ADMINISTRADOR ACTUAL
        // ==========================================================

        $administrador = $request->user();


        // ==========================================================
        // DECISIÓN
        // ==========================================================

        if ($validated['decision'] === 'rechazada') {

            $solicitud->update([

                'estado' => 'rechazada',

                'revisado_por' => $administrador->id,

                'fecha_revision' => now(),

                'observacion_revision' =>
                    $validated['observacion_revision'] ?? null,

            ]);


            return redirect()
                ->route('admin.solicitudes.index')
                ->with(
                    'success',
                    'La solicitud ha sido rechazada correctamente.'
                );
        }


        // ==========================================================
        // APROBACIÓN
        // ==========================================================

        $resultado = DB::transaction(function () use (
            $solicitud,
            $administrador,
            $validated
        ) {

            // ------------------------------------------------------
            // VERIFICAR QUE NO EXISTA UN USUARIO CON ESE EMAIL
            // ------------------------------------------------------

            if (User::where('email', $solicitud->email)->exists()) {

                throw new \RuntimeException(
                    'Ya existe un usuario registrado con el correo de esta solicitud.'
                );
            }


            // ------------------------------------------------------
            // GENERAR CONTRASEÑA TEMPORAL
            // ------------------------------------------------------

            $passwordTemporal = Str::random(12);


            // ------------------------------------------------------
            // CREAR USUARIO
            // ------------------------------------------------------

            $usuario = User::create([

                'name' => trim(
                    $solicitud->nombres .
                    ' ' .
                    $solicitud->apellidos
                ),

                'email' => $solicitud->email,

                'password' => $passwordTemporal,

                'debe_cambiar_password' => true,

            ]);


            // ------------------------------------------------------
            // ASIGNAR ROL
            // ------------------------------------------------------

            $usuario->assignRole('Usuario');


            // ------------------------------------------------------
            // ACTUALIZAR SOLICITUD
            // ------------------------------------------------------

            $solicitud->update([

                'estado' => 'aprobada',

                'user_id' => $usuario->id,

                'revisado_por' => $administrador->id,

                'fecha_revision' => now(),

                'observacion_revision' =>
                    $validated['observacion_revision'] ?? null,

            ]);


            // ------------------------------------------------------
            // DEVOLVER DATOS PARA EL SIGUIENTE PASO
            // ------------------------------------------------------

            return [

                'usuario' => $usuario,

                'password_temporal' => $passwordTemporal,

            ];
        });


        // ==========================================================
        // PRUEBA
        // ==========================================================

        // Por ahora mostramos la contraseña temporal únicamente
        // durante las pruebas. Posteriormente será enviada por
        // WhatsApp y no será necesario mostrarla aquí.

        return redirect()
            ->route('admin.solicitudes.index')
            ->with(
                'success',
                'Solicitud aprobada correctamente. Usuario creado. Contraseña temporal: ' .
                    $resultado['password_temporal']
            );
    }
}

