<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaUsuario extends Model
{
    use HasFactory;


    // ==========================================================
    // TABLA
    // ==========================================================

    protected $table = 'empresa_usuarios';


    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [

        'empresa_id',
        'user_id',

        'cargo',

        'correo_corporativo',
        'telefono_contacto',

        'verificado',
        'verificado_at',

        'es_principal',
    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected function casts(): array
    {
        return [

            'verificado' => 'boolean',

            'verificado_at' => 'datetime',

            'es_principal' => 'boolean',

        ];
    }


    // ==========================================================
    // RELACIÓN: EMPRESA
    // ==========================================================

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(
            Empresa::class,
            'empresa_id'
        );
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

