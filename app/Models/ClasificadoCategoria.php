<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClasificadoCategoria extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    protected $table = 'clasificado_categorias';

    /*
    |--------------------------------------------------------------------------
    | Campos asignables
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'parent_id',
        'nombre',
        'slug',
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
    | Categoría padre
    |--------------------------------------------------------------------------
    */

    public function padre(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Categorías hijas
    |--------------------------------------------------------------------------
    */

    public function hijas(): HasMany
    {
        return $this->hasMany(
            self::class,
            'parent_id'
        )->orderBy('orden');
    }

    /*
    |--------------------------------------------------------------------------
    | Clasificados asociados
    |--------------------------------------------------------------------------
    */

    public function clasificados(): HasMany
    {
        return $this->hasMany(
            Clasificado::class,
            'categoria_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePrincipales($query)
    {
        return $query
            ->whereNull('parent_id')
            ->orderBy('orden');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function esPrincipal(): bool
    {
        return is_null($this->parent_id);
    }

    public function tieneHijas(): bool
    {
        return $this->hijas()->exists();
    }
}
