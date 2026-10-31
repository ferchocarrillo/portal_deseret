<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfertaEmpleoPreguntaOpcion extends Model
{
    use HasFactory;


    // ==========================================================
    // TABLA
    // ==========================================================

    protected $table = 'oferta_empleo_pregunta_opciones';


    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [

        'pregunta_id',

        'opcion',

        'orden',

    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected function casts(): array
    {
        return [

            'orden' => 'integer',

        ];
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
