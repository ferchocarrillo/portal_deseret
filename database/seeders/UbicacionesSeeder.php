<?php

namespace Database\Seeders;

use App\Models\Ciudad;
use App\Models\DivisionAdministrativa;
use App\Models\Pais;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UbicacionesSeeder extends Seeder
{
    public function run(): void
    {
        $ubicaciones = require database_path(
            'data/ubicaciones/ubicaciones.php'
        );

        DB::transaction(function () use ($ubicaciones) {

            foreach ($ubicaciones as $paisData) {

                $pais = Pais::updateOrCreate(
                    [
                        'codigo_iso2' => $paisData['codigo_iso2'],
                    ],
                    [
                        'nombre' => $paisData['nombre'],
                        'codigo_iso3' => $paisData['codigo_iso3'],
                        'codigo_telefonico' => $paisData['codigo_telefonico'],
                        'division_label' => $paisData['division_label'],
                        'localidad_label' => $paisData['localidad_label'],
                        'activo' => true,
                        'orden' => $paisData['orden'] ?? 0,
                    ]
                );

                $this->procesarDivisiones(
                    $pais,
                    $paisData['divisiones'] ?? [],
                    null,
                    1
                );
            }
        });

        $this->command->info(
            'Ubicaciones cargadas correctamente.'
        );
    }

    /**
     * Procesa recursivamente las divisiones administrativas.
     */
    private function procesarDivisiones(
        Pais $pais,
        array $divisiones,
        ?int $parentId = null,
        int $nivel = 1
    ): void {

        foreach ($divisiones as $divisionData) {

            if (empty($divisionData['nombre'])) {
                continue;
            }

            $division = DivisionAdministrativa::updateOrCreate(
                [
                    'pais_id' => $pais->id,
                    'parent_id' => $parentId,
                    'nombre' => $divisionData['nombre'],
                ],
                [
                    'codigo' => $divisionData['codigo'] ?? null,
                    'tipo' => $divisionData['tipo'] ?? 'division',
                    'nivel' => $nivel,
                    'label' => $divisionData['label']
                        ?? ucfirst($divisionData['tipo'] ?? 'División'),
                    'activo' => true,
                    'orden' => $divisionData['orden'] ?? 0,
                ]
            );

            /*
             * Si esta división tiene ciudades directamente,
             * las registramos.
             */
            if (!empty($divisionData['ciudades'])) {

                foreach ($divisionData['ciudades'] as $ciudadData) {

                    if (empty($ciudadData['nombre'])) {
                        continue;
                    }

                    Ciudad::updateOrCreate(
                        [
                            'division_id' => $division->id,
                            'nombre' => $ciudadData['nombre'],
                        ],
                        [
                            'pais_id' => $pais->id,
                            'codigo' => $ciudadData['codigo'] ?? null,
                            'tipo' => $ciudadData['tipo'] ?? null,
                            'latitud' => $ciudadData['latitud'] ?? null,
                            'longitud' => $ciudadData['longitud'] ?? null,
                            'activo' => true,
                            'orden' => $ciudadData['orden'] ?? 0,
                        ]
                    );
                }
            }

            /*
             * Si existen subdivisiones administrativas,
             * continuamos recorriendo el árbol.
             */
            if (!empty($divisionData['hijos'])) {

                $this->procesarDivisiones(
                    $pais,
                    $divisionData['hijos'],
                    $division->id,
                    $nivel + 1
                );
            }
        }
    }
}
