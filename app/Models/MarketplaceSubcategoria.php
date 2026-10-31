<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceSubcategoria extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    protected $table = 'marketplace_subcategorias';

    /*
    |--------------------------------------------------------------------------
    | Asignación masiva
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'categoria_id',
        'nombre',
        'slug',
        'activo',
        'orden',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Categoría padre.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(
            MarketplaceCategoria::class,
            'categoria_id'
        );
    }

    /**
     * Publicaciones pertenecientes a esta subcategoría.
     */
    public function publicaciones(): HasMany
    {
        return $this->hasMany(
            MarketplacePublicacion::class,
            'subcategoria_id'
        );
    }
}
