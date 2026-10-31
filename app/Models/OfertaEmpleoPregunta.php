<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfertaEmpleoPregunta extends Model
{
    use HasFactory;


    // ==========================================================
    // TABLA
    // ==========================================================

    protected $table = 'oferta_empleo_preguntas';


    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [

        'oferta_empleo_id',

        'pregunta',

        'tipo',

        'obligatoria',

        'es_filtro',

        'orden',

    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected function casts(): array
    {
        return [

            'obligatoria' => 'boolean',

            'es_filtro' => 'boolean',

            'orden' => 'integer',

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
    // RELACIÓN: OPCIONES
    // ==========================================================

    public function opciones(): HasMany
    {
        return $this->hasMany(
            OfertaEmpleoPreguntaOpcion::class,
            'pregunta_id'
        )
            ->orderBy('orden');
    }


    // ==========================================================
    // RELACIÓN: RESPUESTAS
    // ==========================================================

    public function respuestas(): HasMany
    {
        return $this->hasMany(
            PostulacionRespuesta::class,
            'pregunta_id'
        );
    }
}

