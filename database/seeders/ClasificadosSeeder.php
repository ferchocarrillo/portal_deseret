<?php

namespace Database\Seeders;

use App\Models\ClasificadoCategoria;
use Illuminate\Database\Seeder;


class ClasificadosSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | VEHÍCULOS
        |--------------------------------------------------------------------------
        */

        $vehiculos = ClasificadoCategoria::firstOrCreate(
            [
                'slug' => 'vehiculos',
            ],
            [
                'parent_id' => null,
                'nombre' => 'Vehículos',
                'descripcion' => 'Vehículos nuevos y usados disponibles para venta o búsqueda.',
                'activo' => true,
                'orden' => 1,
            ]
        );

        $categoriasVehiculos = [
            [
                'nombre' => 'Automóviles',
                'slug' => 'automoviles',
                'descripcion' => 'Automóviles, sedanes, hatchbacks, SUV y similares.',
                'orden' => 1,
            ],
            [
                'nombre' => 'Motocicletas',
                'slug' => 'motocicletas',
                'descripcion' => 'Motocicletas y vehículos de dos ruedas.',
                'orden' => 2,
            ],
            [
                'nombre' => 'Vehículos comerciales',
                'slug' => 'vehiculos-comerciales',
                'descripcion' => 'Camiones, camionetas, buses, furgones y otros vehículos comerciales.',
                'orden' => 3,
            ],
            [
                'nombre' => 'Vehículos especiales',
                'slug' => 'vehiculos-especiales',
                'descripcion' => 'Vehículos destinados a usos especiales o particulares.',
                'orden' => 4,
            ],
            [
                'nombre' => 'Maquinaria',
                'slug' => 'maquinaria',
                'descripcion' => 'Maquinaria pesada, agrícola, industrial y de construcción.',
                'orden' => 5,
            ],
        ];

        foreach ($categoriasVehiculos as $categoria) {
            ClasificadoCategoria::firstOrCreate(
                [
                    'slug' => $categoria['slug'],
                ],
                [
                    'parent_id' => $vehiculos->id,
                    'nombre' => $categoria['nombre'],
                    'descripcion' => $categoria['descripcion'],
                    'activo' => true,
                    'orden' => $categoria['orden'],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PROPIEDADES
        |--------------------------------------------------------------------------
        */

        $propiedades = ClasificadoCategoria::firstOrCreate(
            [
                'slug' => 'propiedades',
            ],
            [
                'parent_id' => null,
                'nombre' => 'Propiedades',
                'descripcion' => 'Propiedades disponibles para venta, arriendo o búsqueda.',
                'activo' => true,
                'orden' => 2,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | VIVIENDA
        |--------------------------------------------------------------------------
        */

        $vivienda = ClasificadoCategoria::firstOrCreate(
            [
                'slug' => 'vivienda',
            ],
            [
                'parent_id' => $propiedades->id,
                'nombre' => 'Vivienda',
                'descripcion' => 'Inmuebles destinados principalmente a vivienda.',
                'activo' => true,
                'orden' => 1,
            ]
        );

        $tiposVivienda = [
            [
                'nombre' => 'Casa',
                'slug' => 'casa',
                'orden' => 1,
            ],
            [
                'nombre' => 'Apartamento',
                'slug' => 'apartamento',
                'orden' => 2,
            ],
            [
                'nombre' => 'Apartaestudio',
                'slug' => 'apartaestudio',
                'orden' => 3,
            ],
            [
                'nombre' => 'Habitación',
                'slug' => 'habitacion',
                'orden' => 4,
            ],
        ];

        foreach ($tiposVivienda as $tipo) {
            ClasificadoCategoria::firstOrCreate(
                [
                    'slug' => $tipo['slug'],
                ],
                [
                    'parent_id' => $vivienda->id,
                    'nombre' => $tipo['nombre'],
                    'descripcion' => null,
                    'activo' => true,
                    'orden' => $tipo['orden'],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TERRENOS
        |--------------------------------------------------------------------------
        */

        $terrenos = ClasificadoCategoria::firstOrCreate(
            [
                'slug' => 'terrenos',
            ],
            [
                'parent_id' => $propiedades->id,
                'nombre' => 'Terrenos',
                'descripcion' => 'Lotes y terrenos destinados a diferentes usos.',
                'activo' => true,
                'orden' => 2,
            ]
        );

        $tiposTerreno = [
            [
                'nombre' => 'Lote urbano',
                'slug' => 'lote-urbano',
                'orden' => 1,
            ],
            [
                'nombre' => 'Lote rural',
                'slug' => 'lote-rural',
                'orden' => 2,
            ],
            [
                'nombre' => 'Lote industrial',
                'slug' => 'lote-industrial',
                'orden' => 3,
            ],
        ];

        foreach ($tiposTerreno as $tipo) {
            ClasificadoCategoria::firstOrCreate(
                [
                    'slug' => $tipo['slug'],
                ],
                [
                    'parent_id' => $terrenos->id,
                    'nombre' => $tipo['nombre'],
                    'descripcion' => null,
                    'activo' => true,
                    'orden' => $tipo['orden'],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | RURALES
        |--------------------------------------------------------------------------
        */

        $rurales = ClasificadoCategoria::firstOrCreate(
            [
                'slug' => 'rurales',
            ],
            [
                'parent_id' => $propiedades->id,
                'nombre' => 'Rurales',
                'descripcion' => 'Propiedades rurales, fincas y parcelas.',
                'activo' => true,
                'orden' => 3,
            ]
        );

        $tiposRurales = [
            [
                'nombre' => 'Finca',
                'slug' => 'finca',
                'orden' => 1,
            ],
            [
                'nombre' => 'Finca de recreo',
                'slug' => 'finca-de-recreo',
                'orden' => 2,
            ],
            [
                'nombre' => 'Parcela',
                'slug' => 'parcela',
                'orden' => 3,
            ],
        ];

        foreach ($tiposRurales as $tipo) {
            ClasificadoCategoria::firstOrCreate(
                [
                    'slug' => $tipo['slug'],
                ],
                [
                    'parent_id' => $rurales->id,
                    'nombre' => $tipo['nombre'],
                    'descripcion' => null,
                    'activo' => true,
                    'orden' => $tipo['orden'],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | COMERCIALES
        |--------------------------------------------------------------------------
        */

        $comerciales = ClasificadoCategoria::firstOrCreate(
            [
                'slug' => 'comerciales',
            ],
            [
                'parent_id' => $propiedades->id,
                'nombre' => 'Comerciales',
                'descripcion' => 'Inmuebles destinados a actividades comerciales o profesionales.',
                'activo' => true,
                'orden' => 4,
            ]
        );

        $tiposComerciales = [
            [
                'nombre' => 'Local',
                'slug' => 'local',
                'orden' => 1,
            ],
            [
                'nombre' => 'Oficina',
                'slug' => 'oficina',
                'orden' => 2,
            ],
            [
                'nombre' => 'Bodega',
                'slug' => 'bodega',
                'orden' => 3,
            ],
            [
                'nombre' => 'Consultorio',
                'slug' => 'consultorio',
                'orden' => 4,
            ],
        ];

        foreach ($tiposComerciales as $tipo) {
            ClasificadoCategoria::firstOrCreate(
                [
                    'slug' => $tipo['slug'],
                ],
                [
                    'parent_id' => $comerciales->id,
                    'nombre' => $tipo['nombre'],
                    'descripcion' => null,
                    'activo' => true,
                    'orden' => $tipo['orden'],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | OTROS
        |--------------------------------------------------------------------------
        */

        $otros = ClasificadoCategoria::firstOrCreate(
            [
                'slug' => 'otros',
            ],
            [
                'parent_id' => $propiedades->id,
                'nombre' => 'Otros',
                'descripcion' => 'Otros tipos de propiedades.',
                'activo' => true,
                'orden' => 5,
            ]
        );

        $tiposOtros = [
            [
                'nombre' => 'Parqueadero',
                'slug' => 'parqueadero',
                'orden' => 1,
            ],
            [
                'nombre' => 'Garaje',
                'slug' => 'garaje',
                'orden' => 2,
            ],
        ];

        foreach ($tiposOtros as $tipo) {
            ClasificadoCategoria::firstOrCreate(
                [
                    'slug' => $tipo['slug'],
                ],
                [
                    'parent_id' => $otros->id,
                    'nombre' => $tipo['nombre'],
                    'descripcion' => null,
                    'activo' => true,
                    'orden' => $tipo['orden'],
                ]
            );
        }
    }
}
