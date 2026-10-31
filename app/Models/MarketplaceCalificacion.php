<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceCalificacion extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    protected $table = 'marketplace_calificaciones';

    /*
    |--------------------------------------------------------------------------
    | Asignación masiva
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'publicacion_id',
        'conversacion_id',
        'comprador_id',
        'vendedor_id',
        'calificacion',
        'comentario',
        'estado',
        'respuesta_vendedor',
        'respuesta_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'calificacion' => 'integer',
        'respuesta_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Publicación relacionada con la calificación.
     */
    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(
            MarketplacePublicacion::class,
            'publicacion_id'
        );
    }

    /**
     * Conversación que originó la calificación.
     */
    public function conversacion(): BelongsTo
    {
        return $this->belongsTo(
            MarketplaceConversacion::class,
            'conversacion_id'
        );
    }

    /**
     * Usuario que realizó la calificación.
     */
    public function comprador(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'comprador_id'
        );
    }

    /**
     * Vendedor que recibió la calificación.
     */
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'vendedor_id'
        );
    }
}
