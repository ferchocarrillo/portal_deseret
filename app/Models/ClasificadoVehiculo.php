<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClasificadoVehiculo extends Model
{
    use HasFactory;

    protected $table = 'clasificado_vehiculos';

    protected $fillable = [
        'clasificado_id',
        'marca',
        'modelo',
        'anio',
        'kilometraje',
        'cilindraje',
        'combustible',
        'transmision',
        'color',
        'placa',
        'unico_propietario',
        'documentacion_al_dia',
        'vehiculo_observaciones', // <-- Corregido aquí
    ];

    protected $casts = [
        'anio' => 'integer',
        'kilometraje' => 'integer',
        'cilindraje' => 'integer',
        'unico_propietario' => 'boolean',
        'documentacion_al_dia' => 'boolean',
    ];

    public function clasificado(): BelongsTo
    {
        return $this->belongsTo(
            Clasificado::class,
            'clasificado_id'
        );
    }
}
