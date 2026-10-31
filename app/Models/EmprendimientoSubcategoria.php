<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmprendimientoSubcategoria extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    protected $table = 'emprendimiento_subcategorias';

    /*
    |--------------------------------------------------------------------------
    | Asignación masiva
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'categoria_id',
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
        'categoria_id' => 'integer',
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    /**
     * Categoría a la que pertenece la subcategoría.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(
            EmprendimientoCategoria::class,
            'categoria_id'
        );
    }

    /**
     * Emprendimientos pertenecientes a esta subcategoría.
     */
    public function emprendimientos(): HasMany
    {
        return $this->hasMany(
            Emprendimiento::class,
            'subcategoria_id'
        );
    }
}
