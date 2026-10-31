<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pais extends Model
{
    use HasFactory;

    protected $table = 'paises';

    protected $fillable = [
        'nombre',
        'codigo_iso2',
        'codigo_iso3',
        'codigo_telefonico',
        'division_label',
        'localidad_label',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function divisiones(): HasMany
    {
        return $this->hasMany(
            DivisionAdministrativa::class,
            'pais_id'
        );
    }

    public function ciudades(): HasMany
    {
        return $this->hasMany(
            Ciudad::class,
            'pais_id'
        );
    }
}
