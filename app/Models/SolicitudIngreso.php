<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudIngreso extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_ingreso';

    // ==========================================================
    // ESTADOS DE LA SOLICITUD
    // ==========================================================

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_APROBADA = 'aprobada';

    public const ESTADO_RECHAZADA = 'rechazada';


    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [

        // Relación con usuario
        'user_id',

        // Datos personales
        'nombres',
        'apellidos',
        'tipo_documento',
        'documento',

        // Ubicación
        'pais',
        'region',
        'ciudad',

        // Contacto
        'telefono',
        'email',

        // Condición del solicitante
        'es_miembro',

        // Información de miembro
        'estaca',
        'barrio',
        'nombre_obispo',

        // Información de no miembro
        'motivo_ingreso',

        // Aval
        'tiene_aval',
        'aval_nombres',
        'aval_apellidos',
        'aval_email',
        'aval_telefono',
        'aval_barrio',

        // Información adicional
        'observaciones',

        // Consentimientos
        'acepta_terminos',
        'ha_leido_privacidad',
        'autoriza_tratamiento_datos',

        // Estado
        'estado',

        // Revisión administrativa
        'revisado_por',
        'fecha_revision',
        'observacion_revision',
    ];


    // ==========================================================
    // CONVERSIONES DE DATOS
    // ==========================================================

    protected $casts = [

        'es_miembro' => 'boolean',
        'tiene_aval' => 'boolean',

        'acepta_terminos' => 'boolean',
        'ha_leido_privacidad' => 'boolean',
        'autoriza_tratamiento_datos' => 'boolean',

        'fecha_revision' => 'datetime',
    ];


    // ==========================================================
    // RELACIONES
    // ==========================================================

    /**
     * Usuario asociado a la solicitud.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Usuario administrador que revisó la solicitud.
     */
    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }
}
