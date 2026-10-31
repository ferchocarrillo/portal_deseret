<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GooglePlacesService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.api_key');

        if (!$this->apiKey) {
            throw new RuntimeException(
                'No está configurada la clave GOOGLE_MAPS_API_KEY.'
            );
        }
    }

    /**
     * Busca ubicaciones de la Iglesia cerca de unas coordenadas.
     */
    public function buscarIglesiasCercanas(
        float $latitud,
        float $longitud
    ): array {

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-Goog-Api-Key' => $this->apiKey,
            'X-Goog-FieldMask' =>
            'places.id,' .
                'places.displayName,' .
                'places.formattedAddress,' .
                'places.location,' .
                'places.googleMapsUri',
        ])->post(
            'https://places.googleapis.com/v1/places:searchText',
            [
                'textQuery' =>
                'Iglesia de Jesucristo de los Santos de los Últimos Días Bogotá',

                'languageCode' => 'es',

                'regionCode' => 'CO',

                'pageSize' => 5,
            ]
        );

        if ($response->failed()) {

            throw new RuntimeException(
                'Error de Google Places: ' .
                    $response->status() .
                    ' - ' .
                    $response->body()
            );
        }

        return $response->json();
    }
}
