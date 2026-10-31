<?php

namespace Database\Seeders;

use App\Models\SolicitudIngreso;
use Illuminate\Database\Seeder;

class SolicitudIngresoSeeder extends Seeder
{
    /**
     * Ejecutar el seeder.
     */
    public function run(): void
    {
        // ==========================================================
        // SOLICITUD 1
        // ==========================================================

        SolicitudIngreso::create([

            'es_miembro' => true,

            'nombres' => 'Carlos',
            'apellidos' => 'Algarra',

            'tipo_documento' => 'CC',
            'documento' => '80952456',

            'pais' => 'CO',
            'region' => 'Cundinamarca',
            'ciudad' => 'Madrid',

            'telefono' => '3112819811',
            'email' => 'carlos.algarra@gmail.com',

            'estaca' => 'Madrid',
            'barrio' => 'Madrid',
            'nombre_obispo' => 'Daniel Gallo',

            'motivo_ingreso' => null,

            'tiene_aval' => null,
            'aval_nombres' => null,
            'aval_apellidos' => null,
            'aval_email' => null,
            'aval_telefono' => null,
            'aval_barrio' => null,

            'observaciones' => 'registro de prueba',

            'acepta_terminos' => true,
            'ha_leido_privacidad' => true,
            'autoriza_tratamiento_datos' => true,

            'estado' => SolicitudIngreso::ESTADO_PENDIENTE,

            'user_id' => null,

            'revisado_por' => null,
            'fecha_revision' => null,
            'observacion_revision' => null,
        ]);


        // ==========================================================
        // SOLICITUD 2
        // ==========================================================

        SolicitudIngreso::create([

            'es_miembro' => true,

            'nombres' => 'Andrés',
            'apellidos' => 'Gómez',

            'tipo_documento' => 'CC',
            'documento' => '80952457',

            'pais' => 'CO',
            'region' => 'Cundinamarca',
            'ciudad' => 'Madrid',

            'telefono' => '3112819812',
            'email' => 'andres.gomez@gmail.com',

            'estaca' => 'Madrid',
            'barrio' => 'Madrid',
            'nombre_obispo' => 'Daniel Gallo',

            'motivo_ingreso' => null,

            'tiene_aval' => null,
            'aval_nombres' => null,
            'aval_apellidos' => null,
            'aval_email' => null,
            'aval_telefono' => null,
            'aval_barrio' => null,

            'observaciones' => 'registro de prueba',

            'acepta_terminos' => true,
            'ha_leido_privacidad' => true,
            'autoriza_tratamiento_datos' => true,

            'estado' => SolicitudIngreso::ESTADO_PENDIENTE,

            'user_id' => null,

            'revisado_por' => null,
            'fecha_revision' => null,
            'observacion_revision' => null,
        ]);


        // ==========================================================
        // SOLICITUD 3
        // ==========================================================

        SolicitudIngreso::create([

            'es_miembro' => false,

            'nombres' => 'Marla',
            'apellidos' => 'Rodriguez',

            'tipo_documento' => 'CC',
            'documento' => '25456789',

            'pais' => 'CO',
            'region' => 'Cundinamarca',
            'ciudad' => 'Madrid',

            'telefono' => '318720092',
            'email' => 'maria.rodriguez@gmail.com',

            'estaca' => null,
            'barrio' => null,
            'nombre_obispo' => null,

            'motivo_ingreso' => 'nada que poner aqui',

            'tiene_aval' => true,

            'aval_nombres' => 'fernando',
            'aval_apellidos' => 'carrillo',
            'aval_email' => 'ferchocarrilloleon@hotmail.com',
            'aval_telefono' => '3112819811',
            'aval_barrio' => 'Madrid',

            'observaciones' => 'aqui no va nada',

            'acepta_terminos' => true,
            'ha_leido_privacidad' => true,
            'autoriza_tratamiento_datos' => true,

            'estado' => SolicitudIngreso::ESTADO_PENDIENTE,

            'user_id' => null,

            'revisado_por' => null,
            'fecha_revision' => null,
            'observacion_revision' => null,
        ]);
    }
}

