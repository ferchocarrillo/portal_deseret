<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PalabraProhibida;

class PalabraProhibidaSeeder extends Seeder
{
    public function run(): void
    {
        $palabras = [
            // Groserías comunes y variaciones en español / contexto latino
            'mierda',
            'puta',
            'puto',
            'pendejo',
            'pendeja',
            'estúpido',
            'estúpida',
            'imbécil',
            'cabrón',
            'cabrona',
            'cacorro',
            'pirobo',
            'maricón',
            'marica',
            'huevón',
            'huevona',
            'verga',
            'pija',
            'coño',
            'gilipollas',
            'culero',
            'culera',
            'culito',
            'culote',
            'zorra',
            'malparido',
            'malparida',
            'gonorrea',
            'hpta',
            'hijueputa',
            'putazo',
            'idiota',
            'cretino',
            'estupidez',
            'carajo',
            'chinga',
            'chimba',
            'chimbo',
            'carechimba',
            'chingado',
            'polla',
            'panocha',
            'cuca',
            'cuquita',
            'teta',
            'tetas',
            'chichis',
            'mamador',
            'mamadora',
            'mamar',
            // Términos relacionados con estafas comunes en clasificados/empleos para filtrar
            'inversion segura',
            'dinero rapido',
            'sin trabajar',
            'gana millones desde casa',
            'prestamo inmediato sin requisitos',
            'multiplica tu dinero'
        ];

        foreach ($palabras as $palabra) {
            PalabraProhibida::firstOrCreate(
                ['palabra' => mb_strtolower(trim($palabra))],
                ['nivel' => 'alta']
            );
        }
    }
}
