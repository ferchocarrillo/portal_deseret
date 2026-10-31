<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResenaServicio extends Model
{
    use HasFactory;

    // ==========================================================
    // TABLA
    // ==========================================================

    protected $table = 'resenas_servicios';


    // ==========================================================
    // ASIGNACIÓN MASIVA
    // ==========================================================

    protected $fillable = [
        'contacto_servicio_id',
        'servicio_id',
        'user_id',
        'tipo',
        'calificacion',
        'comentario',
        'estado',
        'respuesta_propietario',
        'respuesta_at',
    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected function casts(): array
    {
        return [
            'calificacion' => 'integer',
            'respuesta_at' => 'datetime',
        ];
    }


    // ==========================================================
    // RELACIONES
    // ==========================================================

    /**
     * Intento de contacto que originó la reseña.
     */
    public function contacto(): BelongsTo
    {
        return $this->belongsTo(
            ContactoServicio::class,
            'contacto_servicio_id'
        );
    }

    /**
     * Servicio que se está calificando.
     */
    public function servicio(): BelongsTo
    {
        return $this->belongsTo(
            Servicio::class,
            'servicio_id'
        );
    }

    /**
     * Usuario que publicó la reseña.
     *
     * Permite acceder mediante $resena->user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * Alias de la relación user().
     *
     * Se conserva para el código que utiliza
     * $resena->usuario.
     */
    public function usuario(): BelongsTo
    {
        return $this->user();
    }
}
