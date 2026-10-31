<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';


    // ==========================================================
    // ASIGNACIÓN MASIVA
    // ==========================================================

    protected $fillable = [
        'user_id',
        'calificable_type',
        'calificable_id',
        'calificacion',
        'comentario',
    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected $casts = [
        'calificacion' => 'integer',
    ];


    // ==========================================================
    // RELACIÓN: USUARIO QUE CALIFICA
    // ==========================================================

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }


    // ==========================================================
    // RELACIÓN POLIMÓRFICA: ELEMENTO CALIFICADO
    // ==========================================================

    public function calificable(): MorphTo
    {
        return $this->morphTo();
    }


    // ==========================================================
    // MÉTODOS AUXILIARES
    // ==========================================================

    /**
     * Determina si la calificación es de una publicación.
     */
    public function esDePublicacion(): bool
    {
        return $this->calificable_type === Publicacion::class;
    }


    /**
     * Determina si la calificación es de un usuario/publicador.
     */
    public function esDeUsuario(): bool
    {
        return $this->calificable_type === User::class;
    }


    /**
     * Devuelve la calificación como estrellas.
     */
    public function estrellas(): string
    {
        return str_repeat('★', $this->calificacion)
            . str_repeat('☆', 5 - $this->calificacion);
    }
}
