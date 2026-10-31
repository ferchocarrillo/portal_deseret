<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClasificadoConversacion extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    protected $table = 'clasificado_conversaciones';

    /*
    |--------------------------------------------------------------------------
    | Campos asignables
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'clasificado_id',
        'comprador_id',
        'vendedor_id',
        'ultimo_mensaje_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'ultimo_mensaje_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Clasificado relacionado.
     */
    public function clasificado(): BelongsTo
    {
        return $this->belongsTo(
            Clasificado::class,
            'clasificado_id'
        );
    }

    /**
     * Usuario interesado en el clasificado.
     */
    public function comprador(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'comprador_id'
        );
    }

    /**
     * Propietario del clasificado.
     */
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'vendedor_id'
        );
    }

    /**
     * Mensajes de la conversación.
     */
    public function mensajes(): HasMany
    {
        return $this->hasMany(
            ClasificadoMensaje::class,
            'conversacion_id'
        )->orderBy('created_at', 'asc');
    }

    /**
     * Último mensaje.
     */
    public function ultimoMensaje(): HasOne
    {
        return $this->hasOne(
            ClasificadoMensaje::class,
            'conversacion_id'
        )->latestOfMany();
    }
}
