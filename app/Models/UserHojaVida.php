<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHojaVida extends Model
{
    use HasFactory;

    protected $table = 'user_hojas_vida';


    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [
        'user_id',
        'archivo',
        'nombre_original',
        'tipo_mime',
        'tamanio',
        'principal',
    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected function casts(): array
    {
        return [

            'tamanio' => 'integer',

            'principal' => 'boolean',

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
