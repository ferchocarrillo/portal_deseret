<?php

namespace Database\Seeders;

use App\Models\DivisionAdministrativa;
use App\Models\Pais;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionesAdministrativasSeeder extends Seeder
{
    /**
     * Ejecuta la carga de las divisiones administrativas
     * de todos los países definidos en ubicaciones.php.
     */
    public function run(): void
    {
        $ubicaciones = require database_path(
            'data/ubicaciones/ubicaciones.php'
        );

        DB::transaction(function () use ($ubicaciones) {

            foreach ($ubicaciones as $codigoPais => $paisData) {

                $pais = Pais::where(
                    'codigo_iso2',
                    $paisData['codigo_iso2']
                )->first();

                if (!$pais) {

                    $this->command->warn(
                        "El país {$codigoPais} no existe en la tabla paises."
                    );

                    continue;
                }

                $this->command->info(
                    "Procesando: {$pais->nombre}"
                );

                $this->procesarDivisiones(
                    pais: $pais,
                    divisiones: $paisData['divisiones'] ?? [],
                    parentId: null,
                    nivel: 1
                );
            }
        });

        $this->command->newLine();

        $this->command->info(
            'Divisiones administrativas cargadas correctamente.'
        );
    }

    /**
     * Procesa recursivamente las divisiones administrativas.
     *
     * Permite cualquier cantidad de niveles:
     *
     * País
     *   └── Nivel 1
     *       └── Nivel 2
     *           └── Nivel 3
     *               └── ...
     */
    private function procesarDivisiones(
        Pais $pais,
        array $divisiones,
        ?int $parentId,
        int $nivel
    ): void {

        foreach ($divisiones as $divisionData) {

            /*
             * No procesar registros sin nombre.
             */
            if (
                !isset($divisionData['nombre']) ||
                trim($divisionData['nombre']) === ''
            ) {
                continue;
            }

            $nombre = trim(
                $divisionData['nombre']
            );

            /*
             * Crear o actualizar la división.
             *
             * La combinación:
             *
             * pais + padre + nombre
             *
             * identifica de manera única la división.
             */
            $division = DivisionAdministrativa::updateOrCreate(
                [
                    'pais_id' => $pais->id,
                    'parent_id' => $parentId,
                    'nombre' => $nombre,
                ],
                [
                    'codigo' => $divisionData['codigo'] ?? null,

                    'tipo' => $divisionData['tipo']
                        ?? 'division',

                    'nivel' => $nivel,

                    'label' => $divisionData['label']
                        ?? ucfirst(
                            $divisionData['tipo']
                            ?? 'División'
                        ),

                    'activo' => true,

                    'orden' => $divisionData['orden']
                        ?? 0,
                ]
            );

            /*
             * Mostrar progreso en consola.
             */
            $this->command->line(
                str_repeat('  ', $nivel - 1)
                . '└── '
                . $division->nombre
            );

            /*
             * Procesar hijos.
             *
             * Esto permite estructuras como:
             *
             * Chile
             * └── Región
             *     └── Provincia
             *         └── Comuna
             */
            if (
                isset($divisionData['hijos']) &&
                is_array($divisionData['hijos'])
            ) {

                $this->procesarDivisiones(
                    pais: $pais,
                    divisiones: $divisionData['hijos'],
                    parentId: $division->id,
                    nivel: $nivel + 1
                );
            }
        }
    }
}

