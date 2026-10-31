<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ciudad extends Model
{
    use HasFactory;

    protected $table = 'ciudades';

    protected $fillable = [
        'pais_id',
        'division_id',
        'nombre',
        'codigo',
        'tipo',
        'latitud',
        'longitud',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
    ];

    public function pais(): BelongsTo
    {
        return $this->belongsTo(
            Pais::class,
            'pais_id'
        );
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(
            DivisionAdministrativa::class,
            'division_id'
        );
    }
}
