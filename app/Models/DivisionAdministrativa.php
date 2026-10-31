<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DivisionAdministrativa extends Model
{
    use HasFactory;

    protected $table = 'divisiones_administrativas';

    protected $fillable = [
        'pais_id',
        'parent_id',
        'nombre',
        'codigo',
        'tipo',
        'nivel',
        'label',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function pais(): BelongsTo
    {
        return $this->belongsTo(
            Pais::class,
            'pais_id'
        );
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(
            DivisionAdministrativa::class,
            'parent_id'
        );
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(
            DivisionAdministrativa::class,
            'parent_id'
        );
    }

    public function ciudades(): HasMany
    {
        return $this->hasMany(
            Ciudad::class,
            'division_id'
        );
    }
}
