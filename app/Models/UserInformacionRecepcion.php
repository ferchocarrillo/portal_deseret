<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInformacionRecepcion extends Model
{
    use HasFactory;

    protected $table = 'user_informaciones_recepcion';

    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [
        'user_id',

        // Ubicación
        'pais',
        'departamento',
        'ciudad',
        'codigo_postal',
        'barrio',

        // Dirección
        'tipo_via',
        'numero_via',
        'complemento',
        'complemento_2',
        'referencia_direccion',

        // Georreferenciación
        'latitud',
        'longitud',

        // Contacto
        'telefono_contacto',
        'whatsapp',
        'correo_contacto',

        // Preferencia
        'medio_contacto_preferido',

        // Recepción
        'instrucciones_recepcion',

        // Permisos
        'permite_contacto',
        'permite_compartir_direccion',
    ];

    // ==========================================================
    // CASTS
    // ==========================================================

    protected function casts(): array
    {
        return [
            'latitud'                     => 'float',
            'longitud'                    => 'float',
            'permite_contacto'            => 'boolean',
            'permite_compartir_direccion' => 'boolean',
        ];
    }

    // ==========================================================
    // RELACIÓN: USUARIO
    // ==========================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}
