<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\PalabraProhibida;
use Illuminate\Support\Facades\Cache;

class SinPalabrasProhibidas implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            return;
        }

        // Optimizacion: Usar caché por 1 hora para no consultar la BD en cada validación
        $palabras = Cache::remember('palabras_prohibidas_list', 3600, function () {
            return PalabraProhibida::pluck('palabra')->toArray();
        });

        $textoLower = mb_strtolower($value);

        foreach ($palabras as $palabra) {
            $palabraLower = mb_strtolower(trim($palabra));

            // Verificamos si la palabra prohibida está contenida en el texto
            if (!empty($palabraLower) && str_contains($textoLower, $palabraLower)) {
                $fail("El campo :attribute contiene términos no permitidos por las políticas de la comunidad.");
                return;
            }
        }
    }
}
