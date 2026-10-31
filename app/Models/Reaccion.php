<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaccion extends Model
{
    use HasFactory;

    protected $table = 'reacciones';

    // ==========================================================
    // TIPOS DE REACCIÓN
    // ==========================================================

    public const TIPO_LIKE = 'like';

    public const TIPO_DISLIKE = 'dislike';


    // ==========================================================
    // ASIGNACIÓN MASIVA
    // ==========================================================

    protected $fillable = [
        'user_id',
        'publicacion_id',
        'tipo',
    ];


    // ==========================================================
    // RELACIÓN: USUARIO
    // ==========================================================

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }


    // ==========================================================
    // RELACIÓN: PUBLICACIÓN
    // ==========================================================

    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(
            Publicacion::class,
            'publicacion_id'
        );
    }
}
