<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClasificadoPropiedad extends Model
{
    use HasFactory;

    protected $table = 'clasificado_propiedades';

    protected $fillable = [
        'clasificado_id',
        'area_total',
        'area_construida',
        'habitaciones',
        'banos',
        'garajes',
        'pisos',
        'piso',
        'estrato',
        'antiguedad',
        'remodelada',
        'observaciones',

        // Servicios básicos y documentos
        'tiene_servicios_publicos',
        'tiene_agua',
        'tiene_energia',
        'tiene_gas',
        'tiene_alcantarillado',
        'tiene_internet',
        'tiene_tv',
        'papeles_al_dia',
        'libre_gravamen',

        // Administración (Arriendos)
        'incluye_administracion',
        'precio_administracion',

        // Zonas comunes y amenidades
        'tiene_ascensor',
        'tiene_parqueadero',
        'tiene_gimnasio',
        'tiene_piscina',
        'tiene_bbq',
        'tiene_salon_comunal',
        'tiene_zona_infantil',
        'tiene_zonas_verdes',
        'tiene_cancha_multiple',
        'tiene_sauna_turco',
        'tiene_coworking',

        // Seguridad y accesos
        'tiene_porteria_24_7',
        'tiene_circuito_cerrado',
        'tiene_parqueadero_visitantes',

        // Interiores y políticas
        'permite_mascotas',
        'tiene_balcon',
        'tiene_terraza',
        'tiene_deposito',
        'tiene_calentador',
        'tiene_aire_acondicionado',
        'cocina_integral',
        'zona_lavanderia',
    ];

    protected $casts = [
        'area_total'                    => 'decimal:2',
        'area_construida'               => 'decimal:2',
        'habitaciones'                  => 'integer',
        'banos'                         => 'integer',
        'garajes'                       => 'integer',
        'pisos'                         => 'integer',
        'piso'                          => 'integer',
        'remodelada'                    => 'boolean',
        'incluye_administracion'        => 'boolean',
        'precio_administracion'         => 'decimal:2',
        'tiene_servicios_publicos'      => 'boolean',
        'tiene_agua'                    => 'boolean',
        'tiene_energia'                 => 'boolean',
        'tiene_gas'                     => 'boolean',
        'tiene_alcantarillado'          => 'boolean',
        'tiene_internet'                => 'boolean',
        'tiene_tv'                      => 'boolean',
        'papeles_al_dia'                => 'boolean',
        'libre_gravamen'                => 'boolean',
        'tiene_ascensor'                => 'boolean',
        'tiene_parqueadero'             => 'boolean',
        'tiene_gimnasio'                => 'boolean',
        'tiene_piscina'                 => 'boolean',
        'tiene_bbq'                     => 'boolean',
        'tiene_salon_comunal'           => 'boolean',
        'tiene_zona_infantil'           => 'boolean',
        'tiene_zonas_verdes'            => 'boolean',
        'tiene_cancha_multiple'         => 'boolean',
        'tiene_sauna_turco'             => 'boolean',
        'tiene_coworking'               => 'boolean',
        'tiene_porteria_24_7'           => 'boolean',
        'tiene_circuito_cerrado'        => 'boolean',
        'tiene_parqueadero_visitantes'  => 'boolean',
        'permite_mascotas'              => 'boolean',
        'tiene_balcon'                  => 'boolean',
        'tiene_terraza'                 => 'boolean',
        'tiene_deposito'                => 'boolean',
        'tiene_calentador'              => 'boolean',
        'tiene_aire_acondicionado'      => 'boolean',
        'cocina_integral'               => 'boolean',
        'zona_lavanderia'               => 'boolean',
    ];

    public function clasificado(): BelongsTo
    {
        return $this->belongsTo(Clasificado::class, 'clasificado_id');
    }
}
