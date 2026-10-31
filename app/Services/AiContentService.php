<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiContentService
{
    public function magicalFix(string $text, string $contextType = 'general'): array
    {
        $prompt = "Actúa como un editor profesional y redactor publicitario para la plataforma comunitaria Portal Deseret.
Mejora el siguiente texto correspondiente al módulo o contexto: '{$contextType}'.
Corrige errores ortográficos, tildes, puntuación y gramática, manteniendo el significado original.

Debes generar 3 alternativas de estilo:
1. 'directa': Corrección ortográfica y gramatical estricta respetando casi intacto el estilo original.
2. 'profesional': Redacción pulida, formal, atractiva y de alto nivel.
3. 'concisa': Redacción clara, breve y directa al grano.

Evalúa también si contiene lenguaje ofensivo, estafas o contenido inapropiado (has_profanity).

Devuelve estrictamente un JSON válido con esta estructura exacta:
{
    \"improved_text\": \"Texto con la mejor corrección general\",
    \"has_profanity\": false,
    \"options\": [
        {
            \"tipo\": \"directa\",
            \"titulo\": \"Corrección directa\",
            \"descripcion\": \"Corrige ortografía y gramática manteniendo tus palabras.\",
            \"texto\": \"...\"
        },
        {
            \"tipo\": \"profesional\",
            \"titulo\": \"Profesional y pulido\",
            \"descripcion\": \"Tono formal, confiable y mejor redactado.\",
            \"texto\": \"...\"
        },
        {
            \"tipo\": \"concisa\",
            \"titulo\": \"Claro y conciso\",
            \"descripcion\": \"Directo al punto y sin rodeos.\",
            \"texto\": \"...\"
        }
    ]
}

Texto a corregir: \"{$text}\"";

        $apiKey = config('services.gemini.key') ?? env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash');

        if (!$apiKey) {
            Log::error('AiContentService: No se encontró la API Key de Gemini en el entorno.');
            return $this->fallbackResponse($text);
        }

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $response = Http::timeout(10)->retry(2, 300)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, [
                'contents' => [
                    [
                        'parts' => [['text' => $prompt]]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'responseMimeType' => 'application/json',
                ]
            ]);

            if ($response->successful()) {
                $rawContent = $response->json('candidates.0.content.parts.0.text', '');
                $cleanedJson = preg_replace('/```(?:json)?\s*([\s\S]*?)\s*```/', '$1', $rawContent);
                $decoded = json_decode(trim($cleanedJson), true);

                if ($decoded && isset($decoded['improved_text'])) {
                    return $decoded;
                }
            } else {
                Log::error("Error en API Gemini ({$model}): " . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::warning('Fallo de conexión con Google Gemini: ' . $e->getMessage());
        }

        return $this->fallbackResponse($text);
    }

    private function fallbackResponse(string $text): array
    {
        return [
            'improved_text' => $text,
            'has_profanity' => false,
            'options' => [],
            'changes_summary' => 'Respaldo aplicado debido a indisponibilidad temporal del servicio de IA.'
        ];
    }
}
