<?php

namespace Database\Seeders;

use App\Models\EmprendimientoCategoria;
use App\Models\EmprendimientoSubcategoria;
use Illuminate\Database\Seeder;

class EmprendimientoSubcategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $subcategorias = [

            /*
            |--------------------------------------------------------------------------
            | ALIMENTOS Y BEBIDAS
            |--------------------------------------------------------------------------
            */

            'Alimentos y bebidas' => [
                'Repostería',
                'Panadería',
                'Comida preparada',
                'Restaurantes y comidas',
                'Catering',
                'Productos artesanales',
                'Bebidas',
                'Productos naturales',
                'Otros alimentos',
            ],

            /*
            |--------------------------------------------------------------------------
            | ARTESANÍAS Y MANUALIDADES
            |--------------------------------------------------------------------------
            */

            'Artesanías y manualidades' => [
                'Artesanías',
                'Decoración artesanal',
                'Bisutería',
                'Productos hechos a mano',
                'Arte y pintura',
                'Tejidos',
                'Otros productos artesanales',
            ],

            /*
            |--------------------------------------------------------------------------
            | BELLEZA Y CUIDADO PERSONAL
            |--------------------------------------------------------------------------
            */

            'Belleza y cuidado personal' => [
                'Peluquería',
                'Barbería',
                'Manicure y pedicure',
                'Estética',
                'Cosmética',
                'Cuidado personal',
                'Productos de belleza',
            ],

            /*
            |--------------------------------------------------------------------------
            | COMERCIO
            |--------------------------------------------------------------------------
            */

            'Comercio' => [
                'Comercio minorista',
                'Comercio mayorista',
                'Ventas por catálogo',
                'Comercio electrónico',
                'Importación y distribución',
                'Otros comercios',
            ],

            /*
            |--------------------------------------------------------------------------
            | EDUCACIÓN Y CAPACITACIÓN
            |--------------------------------------------------------------------------
            */

            'Educación y capacitación' => [
                'Cursos',
                'Tutorías',
                'Capacitación empresarial',
                'Formación técnica',
                'Idiomas',
                'Consultoría educativa',
                'Otros servicios educativos',
            ],

            /*
            |--------------------------------------------------------------------------
            | HOGAR
            |--------------------------------------------------------------------------
            */

            'Hogar' => [
                'Limpieza',
                'Decoración',
                'Mantenimiento del hogar',
                'Jardinería',
                'Reparaciones',
                'Muebles',
                'Otros servicios para el hogar',
            ],

            /*
            |--------------------------------------------------------------------------
            | TECNOLOGÍA
            |--------------------------------------------------------------------------
            */

            'Tecnología' => [
                'Desarrollo de software',
                'Diseño web',
                'Aplicaciones móviles',
                'Soporte técnico',
                'Servicios informáticos',
                'Marketing digital',
                'Diseño gráfico',
                'Comercio tecnológico',
                'Otros servicios tecnológicos',
            ],

            /*
            |--------------------------------------------------------------------------
            | SERVICIOS PROFESIONALES
            |--------------------------------------------------------------------------
            */

            'Servicios profesionales' => [
                'Contabilidad',
                'Asesoría jurídica',
                'Consultoría empresarial',
                'Marketing y publicidad',
                'Diseño',
                'Arquitectura',
                'Ingeniería',
                'Recursos humanos',
                'Otros servicios profesionales',
            ],

            /*
            |--------------------------------------------------------------------------
            | TRANSPORTE Y LOGÍSTICA
            |--------------------------------------------------------------------------
            */

            'Transporte y logística' => [
                'Transporte de pasajeros',
                'Transporte de carga',
                'Mensajería',
                'Logística',
                'Mudanzas',
                'Servicios para vehículos',
                'Otros servicios de transporte',
            ],

            /*
            |--------------------------------------------------------------------------
            | AGRICULTURA Y PRODUCCIÓN
            |--------------------------------------------------------------------------
            */

            'Agricultura y producción' => [
                'Agricultura',
                'Producción de alimentos',
                'Ganadería',
                'Avicultura',
                'Producción agrícola',
                'Productos orgánicos',
                'Otros servicios agropecuarios',
            ],

            /*
            |--------------------------------------------------------------------------
            | MANUFACTURA
            |--------------------------------------------------------------------------
            */

            'Manufactura' => [
                'Textiles',
                'Confección',
                'Muebles',
                'Productos industriales',
                'Productos para el hogar',
                'Fabricación artesanal',
                'Otros productos manufacturados',
            ],

            /*
            |--------------------------------------------------------------------------
            | MODA Y CONFECCIÓN
            |--------------------------------------------------------------------------
            */

            'Moda y confección' => [
                'Ropa',
                'Calzado',
                'Accesorios',
                'Confección',
                'Diseño de moda',
                'Moda infantil',
                'Otros productos de moda',
            ],

            /*
            |--------------------------------------------------------------------------
            | ENTRETENIMIENTO Y CULTURA
            |--------------------------------------------------------------------------
            */

            'Entretenimiento y cultura' => [
                'Fotografía',
                'Producción audiovisual',
                'Música',
                'Eventos',
                'Arte',
                'Entretenimiento',
                'Otros servicios culturales',
            ],

            /*
            |--------------------------------------------------------------------------
            | TURISMO Y HOSPITALIDAD
            |--------------------------------------------------------------------------
            */

            'Turismo y hospitalidad' => [
                'Turismo',
                'Alojamiento',
                'Guías turísticos',
                'Experiencias',
                'Agencias de viajes',
                'Gastronomía turística',
                'Otros servicios turísticos',
            ],

            /*
            |--------------------------------------------------------------------------
            | OTROS
            |--------------------------------------------------------------------------
            */

            'Otros' => [
                'Otros emprendimientos',
            ],
        ];

        foreach ($subcategorias as $categoriaNombre => $items) {

            $categoria = EmprendimientoCategoria::where(
                'nombre',
                $categoriaNombre
            )->first();

            if (!$categoria) {
                $this->command?->warn(
                    "No se encontró la categoría: {$categoriaNombre}"
                );

                continue;
            }

            foreach ($items as $index => $nombre) {

                EmprendimientoSubcategoria::updateOrCreate(
                    [
                        'categoria_id' => $categoria->id,
                        'nombre' => $nombre,
                    ],
                    [
                        'descripcion' => null,
                        'activo' => true,
                        'orden' => $index + 1,
                    ]
                );
            }
        }
    }
}
