<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostulacionRespuesta extends Model
{
    use HasFactory;


    // ==========================================================
    // TABLA
    // ==========================================================

    protected $table = 'postulacion_respuestas';


    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [

        'postulacion_id',

        'pregunta_id',

        'respuesta',

    ];


    // ==========================================================
    // RELACIÓN: POSTULACIÓN
    // ==========================================================

    public function postulacion(): BelongsTo
    {
        return $this->belongsTo(
            Postulacion::class,
            'postulacion_id'
        );
    }


    // ==========================================================
    // RELACIÓN: PREGUNTA
    // ==========================================================

    public function pregunta(): BelongsTo
    {
        return $this->belongsTo(
            OfertaEmpleoPregunta::class,
            'pregunta_id'
        );
    }
}

