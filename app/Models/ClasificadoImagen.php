<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClasificadoImagen extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    protected $table = 'clasificado_imagenes';

    /*
    |--------------------------------------------------------------------------
    | Campos asignables
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'clasificado_id',
        'ruta',
        'orden',
        'es_principal',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'orden' => 'integer',
        'es_principal' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Clasificado
    |--------------------------------------------------------------------------
    */

    public function clasificado(): BelongsTo
    {
        return $this->belongsTo(
            Clasificado::class,
            'clasificado_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function esPrincipal(): bool
    {
        return $this->es_principal === true;
    }
}
