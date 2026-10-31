<?php

namespace Database\Seeders;

use App\Models\Ciudad;
use App\Models\DivisionAdministrativa;
use App\Models\Pais;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CiudadesSeeder extends Seeder
{
    /**
     * Ejecuta la carga de ciudades y localidades.
     *
     * Las ciudades se toman del archivo:
     *
     * database/data/ubicaciones/ubicaciones.php
     *
     * y se relacionan con:
     *
     * País
     *   └── División administrativa
     *       └── Ciudad / localidad
     *
     * También permite estructuras con varios niveles:
     *
     * País
     *   └── Región
     *       └── Provincia
     *           └── Ciudad
     */
    public function run(): void
    {
        $ubicaciones = require database_path(
            'data/ubicaciones/ubicaciones.php'
        );

        DB::transaction(function () use ($ubicaciones) {

            foreach ($ubicaciones as $codigoPais => $paisData) {

                /*
                 * Buscar el país por código ISO2.
                 */
                $pais = Pais::where(
                    'codigo_iso2',
                    $paisData['codigo_iso2']
                )->first();

                /*
                 * Si el país no existe, continuar
                 * con el siguiente.
                 */
                if (!$pais) {

                    $this->command->warn(
                        "El país {$codigoPais} "
                            . "({$paisData['nombre']}) no existe."
                    );

                    continue;
                }

                $this->command->newLine();

                $this->command->info(
                    "Procesando ciudades de {$pais->nombre}"
                );

                /*
                 * Comenzamos desde las divisiones
                 * administrativas de primer nivel.
                 */
                $this->procesarDivisiones(
                    pais: $pais,
                    divisiones: $paisData['divisiones'] ?? [],
                    parentId: null
                );
            }
        });

        $this->command->newLine();

        $this->command->info(
            'Ciudades y localidades cargadas correctamente.'
        );
    }


    /**
     * Recorre recursivamente todas las divisiones.
     *
     * Permite cualquier cantidad de niveles.
     *
     * Ejemplo:
     *
     * Colombia
     *   └── Antioquia
     *       └── Medellín
     *
     * Chile
     *   └── Región Metropolitana
     *       └── Provincia de Santiago
     *           └── Santiago
     */
    private function procesarDivisiones(
        Pais $pais,
        array $divisiones,
        ?int $parentId
    ): void {

        foreach ($divisiones as $divisionData) {

            /*
             * Validar que exista nombre.
             */
            if (
                !isset($divisionData['nombre']) ||
                trim($divisionData['nombre']) === ''
            ) {
                continue;
            }

            $nombreDivision = trim(
                $divisionData['nombre']
            );

            /*
             * Buscar la división que previamente
             * fue creada por:
             *
             * DivisionesAdministrativasSeeder
             */
            $division = DivisionAdministrativa::query()
                ->where('pais_id', $pais->id)
                ->where('parent_id', $parentId)
                ->where('nombre', $nombreDivision)
                ->first();

            /*
             * Si no existe la división, no podemos
             * relacionar las ciudades.
             */
            if (!$division) {

                $this->command->warn(
                    "No se encontró la división "
                        . "'{$nombreDivision}' "
                        . "para {$pais->nombre}."
                );

                continue;
            }

            /*
             * ======================================================
             * CIUDADES DE ESTA DIVISIÓN
             * ======================================================
             */

            if (
                isset($divisionData['ciudades']) &&
                is_array($divisionData['ciudades'])
            ) {

                foreach ($divisionData['ciudades'] as $ciudadData) {

                    /*
                     * Validar nombre de ciudad.
                     */
                    if (
                        !isset($ciudadData['nombre']) ||
                        trim($ciudadData['nombre']) === ''
                    ) {
                        continue;
                    }

                    $nombreCiudad = trim(
                        $ciudadData['nombre']
                    );

                    /*
                     * Crear o actualizar ciudad.
                     *
                     * La combinación:
                     *
                     * division + nombre
                     *
                     * identifica la localidad.
                     */
                    $ciudad = Ciudad::updateOrCreate(
                        [
                            'division_id' => $division->id,
                            'nombre' => $nombreCiudad,
                        ],
                        [
                            'pais_id' => $pais->id,

                            'codigo' => $ciudadData['codigo']
                                ?? null,

                            'tipo' => $ciudadData['tipo']
                                ?? null,

                            'latitud' => $ciudadData['latitud']
                                ?? null,

                            'longitud' => $ciudadData['longitud']
                                ?? null,

                            'activo' => $ciudadData['activo']
                                ?? true,

                            'orden' => $ciudadData['orden']
                                ?? 0,
                        ]
                    );

                    /*
                     * Mostrar progreso.
                     */
                    $this->command->line(
                        '      └── '
                            . $ciudad->nombre
                    );
                }
            }

            /*
             * ======================================================
             * DIVISIONES HIJAS
             * ======================================================
             *
             * Si esta división tiene hijos, continuamos
             * recorriendo la estructura.
             */
            if (
                isset($divisionData['hijos']) &&
                is_array($divisionData['hijos'])
            ) {

                $this->procesarDivisiones(
                    pais: $pais,
                    divisiones: $divisionData['hijos'],
                    parentId: $division->id
                );
            }
        }
    }
}
