<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InscripcionCurso extends Model
{
    use HasFactory;

    protected $table = 'inscripciones_cursos';

    /*
    |--------------------------------------------------------------------------
    | Asignación masiva
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'curso_id',
        'user_id',

        /*
        |----------------------------------------------------------------------
        | Datos del participante
        |----------------------------------------------------------------------
        */

        'nombre_inscrito',
        'email_inscrito',
        'telefono_inscrito',

        /*
        |----------------------------------------------------------------------
        | Estado y seguimiento
        |----------------------------------------------------------------------
        */

        'estado',
        'visto_at',
        'activado_at',

        /*
        |----------------------------------------------------------------------
        | Cancelación
        |----------------------------------------------------------------------
        */

        'motivo_cancelacion',
        'observacion_cancelacion',
        'cancelada_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Conversiones de atributos
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'visto_at' => 'datetime',
            'activado_at' => 'datetime',
            'cancelada_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Curso al que pertenece la inscripción.
     */
    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    /**
     * Usuario que realizó la inscripción.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
