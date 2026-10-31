<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoService
{
    /**
     * Resuelve latitud y longitud mediante Nominatim (OpenStreetMap).
     */
    public static function geocodificar(string $direccion, string $ciudad, string $departamento, string $pais = 'Colombia'): ?array
    {
        // Corregir errores tipográficos comunes en nomenclatura colombiana
        $direccionLimpia = preg_replace('/\btrasnversal\b/i', 'Transversal', $direccion);
        $direccionLimpia = trim($direccionLimpia);

        // Intento 1: Dirección completa estructurada
        $consultaDetallada = trim("{$direccionLimpia}, {$ciudad}, {$departamento}, {$pais}");
        $coords = self::consultarNominatim($consultaDetallada);

        if ($coords) {
            return $coords;
        }

        // Intento 2: Si tiene barrio o sector, buscar Barrio + Ciudad
        // Intento 3: Cabecera municipal (Ciudad, Departamento, Colombia)
        $consultaMunicipio = trim("{$ciudad}, {$departamento}, {$pais}");
        return self::consultarNominatim($consultaMunicipio);
    }

    private static function consultarNominatim(string $query): ?array
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'PortalDeseretApp/1.0 (contacto@portaldeseret.com)',
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'json',
                'limit' => 1,
            ]);

            if ($response->successful() && !empty($response->json())) {
                $item = $response->json()[0];
                return [
                    'latitud'  => (float) $item['lat'],
                    'longitud' => (float) $item['lon'],
                ];
            }
        } catch (\Throwable $e) {
            Log::warning("Error geocodificando [{$query}]: " . $e->getMessage());
        }

        return null;
    }
}
