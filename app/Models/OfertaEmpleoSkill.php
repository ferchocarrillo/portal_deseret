<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfertaEmpleoSkill extends Model
{
    use HasFactory;


    // ==========================================================
    // TABLA
    // ==========================================================

    protected $table = 'oferta_empleo_skills';


    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [

        'oferta_empleo_id',
        'skill_id',

        'nivel_requerido',
        'es_obligatorio',

    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected function casts(): array
    {
        return [

            'es_obligatorio' => 'boolean',

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
    // RELACIÓN: SKILL
    // ==========================================================

    public function skill(): BelongsTo
    {
        return $this->belongsTo(
            Skill::class,
            'skill_id'
        );
    }
}


