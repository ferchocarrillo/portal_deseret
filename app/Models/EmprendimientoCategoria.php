<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmprendimientoCategoria extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    protected $table = 'emprendimiento_categorias';

    /*
    |--------------------------------------------------------------------------
    | Asignación masiva
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'nombre',
        'descripcion',
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
            EmprendimientoSubcategoria::class,
            'categoria_id'
        );
    }

    /**
     * Emprendimientos pertenecientes a esta categoría.
     */
    public function emprendimientos(): HasMany
    {
        return $this->hasMany(
            Emprendimiento::class,
            'categoria_id'
        );
    }
}
