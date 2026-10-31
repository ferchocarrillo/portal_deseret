<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFormacion extends Model
{
    use HasFactory;

    protected $table = 'user_formaciones';


    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [
        'user_id',
        'nivel',
        'institucion',
        'programa',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'en_curso',
    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected function casts(): array
    {
        return [

            'fecha_inicio' => 'date',

            'fecha_fin' => 'date',

            'en_curso' => 'boolean',

        ];
    }


    // ==========================================================
    // RELACIÓN: USUARIO
    // ==========================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}
