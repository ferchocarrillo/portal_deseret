<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MarketplaceConversacion extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    protected $table = 'marketplace_conversaciones';

    /*
    |--------------------------------------------------------------------------
    | Asignación masiva
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'publicacion_id',
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
     * Publicación relacionada con la conversación.
     */
    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(
            MarketplacePublicacion::class,
            'publicacion_id'
        );
    }

    /**
     * Usuario interesado en la publicación.
     */
    public function comprador(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'comprador_id'
        );
    }

    /**
     * Propietario de la publicación.
     */
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'vendedor_id'
        );
    }

    /**
     * Mensajes pertenecientes a la conversación.
     */
    public function mensajes(): HasMany
    {
        return $this->hasMany(
            MarketplaceMensaje::class,
            'conversacion_id'
        )->orderBy('created_at');
    }
    /*
|--------------------------------------------------------------------------
| Calificación
|--------------------------------------------------------------------------
*/

    /**
     * Calificación asociada a la conversación.
     */
    public function calificacion(): HasOne
    {
        return $this->hasOne(
            MarketplaceCalificacion::class,
            'conversacion_id'
        );
    }
}
