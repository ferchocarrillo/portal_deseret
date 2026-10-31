<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\DivisionAdministrativa;
use App\Models\Pais;
use Illuminate\Http\JsonResponse;

class UbicacionesController extends Controller
{
    /**
     * Listar todos los países activos.
     *
     * GET /api/ubicaciones/paises
     */
    public function paises(): JsonResponse
    {
        $paises = Pais::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get([
                'id',
                'nombre',
                'codigo_iso2',
                'codigo_iso3',
                'codigo_telefonico',
                'division_label',
                'localidad_label',
            ]);

        return response()->json($paises);
    }


    /**
     * Obtener las divisiones administrativas de un país.
     *
     * Este método devuelve únicamente las divisiones
     * del primer nivel.
     *
     * Ejemplo:
     *
     * Colombia → Departamentos
     * Ecuador  → Provincias
     * Chile    → Regiones
     * Venezuela → Estados
     *
     * GET /api/ubicaciones/paises/{pais}/divisiones
     */
    public function divisiones(Pais $pais): JsonResponse
    {
        $divisiones = DivisionAdministrativa::query()
            ->where('pais_id', $pais->id)
            ->whereNull('parent_id')
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get([
                'id',
                'pais_id',
                'parent_id',
                'nombre',
                'codigo',
                'tipo',
                'nivel',
                'label',
            ]);

        return response()->json([
            'pais' => [
                'id' => $pais->id,
                'nombre' => $pais->nombre,
                'codigo_iso2' => $pais->codigo_iso2,
                'division_label' => $pais->division_label,
                'localidad_label' => $pais->localidad_label,
            ],
            'divisiones' => $divisiones,
        ]);
    }


    /**
     * Obtener las subdivisiones de una división administrativa.
     *
     * Sirve para países que tienen varios niveles administrativos.
     *
     * Ejemplo:
     *
     * Chile
     *   Región
     *      ↓
     *   Provincia
     *
     * GET /api/ubicaciones/divisiones/{division}/hijos
     */
    public function hijos(DivisionAdministrativa $division): JsonResponse
    {
        $hijos = DivisionAdministrativa::query()
            ->where('parent_id', $division->id)
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get([
                'id',
                'pais_id',
                'parent_id',
                'nombre',
                'codigo',
                'tipo',
                'nivel',
                'label',
            ]);

        return response()->json([
            'division' => [
                'id' => $division->id,
                'nombre' => $division->nombre,
                'tipo' => $division->tipo,
                'nivel' => $division->nivel,
                'label' => $division->label,
            ],
            'hijos' => $hijos,
        ]);
    }


    /**
     * Obtener las ciudades/localidades directamente
     * asociadas a una división administrativa.
     *
     * GET /api/ubicaciones/divisiones/{division}/ciudades
     */
    public function ciudades(DivisionAdministrativa $division): JsonResponse
    {
        $ciudades = Ciudad::query()
            ->where('division_id', $division->id)
            ->where('activo', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get([
                'id',
                'pais_id',
                'division_id',
                'nombre',
                'codigo',
                'tipo',
                'latitud',
                'longitud',
            ]);

        return response()->json([
            'division' => [
                'id' => $division->id,
                'nombre' => $division->nombre,
                'tipo' => $division->tipo,
                'nivel' => $division->nivel,
                'label' => $division->label,
            ],
            'ciudades' => $ciudades,
        ]);
    }


    /**
     * Obtener una división administrativa completa,
     * incluyendo sus hijos y ciudades.
     *
     * Útil para consultas específicas o futuras funcionalidades.
     *
     * GET /api/ubicaciones/divisiones/{division}
     */
    public function mostrarDivision(
        DivisionAdministrativa $division
    ): JsonResponse {

        $division->load([
            'pais:id,nombre,codigo_iso2,division_label,localidad_label',
            'hijos' => function ($query) {
                $query
                    ->where('activo', true)
                    ->orderBy('orden')
                    ->orderBy('nombre');
            },
            'ciudades' => function ($query) {
                $query
                    ->where('activo', true)
                    ->orderBy('orden')
                    ->orderBy('nombre');
            },
        ]);

        return response()->json($division);
    }
}

