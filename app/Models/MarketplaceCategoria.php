<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceCategoria extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    protected $table = 'marketplace_categorias';

    /*
    |--------------------------------------------------------------------------
    | Asignación masiva
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'icono',
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
     * Subcategorías pertenecientes a esta categoría.
     */
    public function subcategorias(): HasMany
    {
        return $this->hasMany(
            MarketplaceSubcategoria::class,
            'categoria_id'
        )->orderBy('orden');
    }

    /**
     * Publicaciones pertenecientes a esta categoría.
     */
    public function publicaciones(): HasMany
    {
        return $this->hasMany(
            MarketplacePublicacion::class,
            'categoria_id'
        );
    }
}
