<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Postulacion extends Model
{
    use HasFactory;


    // ==========================================================
    // TABLA
    // ==========================================================

    protected $table = 'postulaciones';


    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [

        'oferta_empleo_id',

        'user_id',

        'estado',

        'observaciones',

        'fecha_postulacion',

    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected function casts(): array
    {
        return [

            'fecha_postulacion' => 'datetime',

        ];
    }


    // ==========================================================
    // RELACIÓN: OFERTA DE EMPLEO
    // ==========================================================

    public function ofertaEmpleo(): BelongsTo
    {
        return $this->belongsTo(
            OfertaEmpleo::class,
            'oferta_empleo_id'
        );
    }


    // ==========================================================
    // RELACIÓN: POSTULANTE
    // ==========================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }


    // ==========================================================
    // RELACIÓN: RESPUESTAS
    // ==========================================================

    public function respuestas(): HasMany
    {
        return $this->hasMany(
            PostulacionRespuesta::class,
            'postulacion_id'
        );
    }
}

