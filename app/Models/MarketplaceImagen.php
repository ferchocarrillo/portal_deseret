<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceImagen extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    protected $table = 'marketplace_imagenes';

    /*
    |--------------------------------------------------------------------------
    | Asignación masiva
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'publicacion_id',
        'ruta',
        'nombre_original',
        'mime_type',
        'tamano',
        'orden',
        'es_principal',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'tamano' => 'integer',
        'orden' => 'integer',
        'es_principal' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Publicación a la que pertenece la imagen.
     */
    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(
            MarketplacePublicacion::class,
            'publicacion_id'
        );
    }
}
