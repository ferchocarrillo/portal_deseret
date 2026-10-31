<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends Model
{
    use HasFactory;


    // ==========================================================
    // TABLA
    // ==========================================================

    protected $table = 'empresas';


    // ==========================================================
    // CAMPOS ASIGNABLES
    // ==========================================================

    protected $fillable = [

        // Identificación
        'razon_social',
        'nombre_comercial',
        'nit',

        // Contacto corporativo
        'correo_corporativo',
        'telefono',

        // Ubicación
        'pais',
        'departamento',
        'ciudad',
        'codigo_postal',
        'direccion',

        // Información adicional
        'sitio_web',
        'descripcion',

        // Estado
        'activa',
        'verificada',
        'verificada_at',
    ];


    // ==========================================================
    // CASTS
    // ==========================================================

    protected function casts(): array
    {
        return [

            'activa' => 'boolean',

            'verificada' => 'boolean',

            'verificada_at' => 'datetime',

        ];
    }


    // ==========================================================
    // RELACIÓN: USUARIOS QUE REPRESENTAN LA EMPRESA
    // ==========================================================

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'empresa_usuarios',
            'empresa_id',
            'user_id'
        )
            ->withPivot([
                'cargo',
                'correo_corporativo',
                'telefono_contacto',
                'verificado',
                'verificado_at',
                'es_principal',
            ])
            ->withTimestamps();
    }


    // ==========================================================
    // RELACIÓN: REGISTROS DE REPRESENTANTES
    // ==========================================================

    public function empresaUsuarios(): HasMany
    {
        return $this->hasMany(
            EmpresaUsuario::class,
            'empresa_id'
        );
    }


    // ==========================================================
    // RELACIÓN: OFERTAS DE EMPLEO
    // ==========================================================

    public function ofertasEmpleo(): HasMany
    {
        return $this->hasMany(
            OfertaEmpleo::class,
            'empresa_id'
        );
    }
}

