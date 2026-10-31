<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketplaceCategoria;
use App\Models\MarketplaceSubcategoria;
use Illuminate\Support\Str;

class MarketplaceCategoriasSeeder extends Seeder
{
    /**
     * Ejecutar el seeder.
     */
    public function run(): void
    {
        $categorias = [

            [
                'nombre' => 'Tecnología',
                'descripcion' => 'Computadores, celulares, dispositivos electrónicos y accesorios.',
                'icono' => '💻',
                'subcategorias' => [
                    'Computadores y portátiles',
                    'Celulares y smartphones',
                    'Tablets',
                    'Monitores y pantallas',
                    'Impresoras y escáneres',
                    'Componentes de computador',
                    'Accesorios tecnológicos',
                    'Audio y sonido',
                    'Videojuegos y consolas',
                    'Cámaras y fotografía',
                    'Otros productos tecnológicos',
                ],
            ],

            [
                'nombre' => 'Hogar y muebles',
                'descripcion' => 'Muebles, electrodomésticos, decoración y artículos para el hogar.',
                'icono' => '🏠',
                'subcategorias' => [
                    'Muebles',
                    'Electrodomésticos',
                    'Cocina',
                    'Decoración',
                    'Iluminación',
                    'Dormitorio',
                    'Baño',
                    'Jardín y exteriores',
                    'Herramientas para el hogar',
                    'Organización y almacenamiento',
                    'Otros artículos para el hogar',
                ],
            ],

            [
                'nombre' => 'Vehículos',
                'descripcion' => 'Bicicletas, repuestos y accesorios.',
                'icono' => '🚗',
                'subcategorias' => [
                    'Bicicletas',
                    'Patinetas electricas',
                    'Repuestos y autopartes',
                    'Accesorios para vehículos',
                    'Llantas y neumáticos',
                    'Herramientas y mantenimiento',
                    'Otros vehículos',
                ],
            ],

            [
                'nombre' => 'Ropa y accesorios',
                'descripcion' => 'Ropa, calzado, bolsos, joyería y accesorios.',
                'icono' => '👕',
                'subcategorias' => [
                    'Ropa para mujer',
                    'Ropa para hombre',
                    'Ropa para niños',
                    'Ropa para bebés',
                    'Calzado',
                    'Bolsos y carteras',
                    'Accesorios',
                    'Relojes',
                    'Joyería',
                    'Ropa deportiva',
                    'Otros artículos de moda',
                ],
            ],

            [
                'nombre' => 'Deportes y recreación',
                'descripcion' => 'Artículos deportivos, recreativos y para actividades al aire libre.',
                'icono' => '⚽',
                'subcategorias' => [
                    'Fútbol',
                    'Ciclismo',
                    'Gimnasio y fitness',
                    'Running',
                    'Camping y actividades al aire libre',
                    'Deportes acuáticos',
                    'Deportes de combate',
                    'Deportes de raqueta',
                    'Juegos y entretenimiento',
                    'Otros deportes y recreación',
                ],
            ],

            [
                'nombre' => 'Libros y educación',
                'descripcion' => 'Libros, material educativo, papelería e instrumentos de aprendizaje.',
                'icono' => '📚',
                'subcategorias' => [
                    'Libros',
                    'Libros académicos',
                    'Libros infantiles',
                    'Biblias y libros religiosos',
                    'Material educativo',
                    'Papelería',
                    'Instrumentos musicales',
                    'Material de estudio',
                    'Otros artículos educativos',
                ],
            ],

            [
                'nombre' => 'Niños y bebés',
                'descripcion' => 'Productos, ropa, juguetes y artículos para niños y bebés.',
                'icono' => '🧸',
                'subcategorias' => [
                    'Ropa para bebés',
                    'Ropa para niños',
                    'Juguetes',
                    'Coches y caminadores',
                    'Sillas para bebé',
                    'Muebles infantiles',
                    'Artículos para alimentación',
                    'Artículos para cuidado del bebé',
                    'Otros artículos para niños',
                ],
            ],

            [
                'nombre' => 'Oficina y negocio',
                'descripcion' => 'Equipos, muebles y elementos para oficinas y emprendimientos.',
                'icono' => '🏢',
                'subcategorias' => [
                    'Muebles de oficina',
                    'Computadores para oficina',
                    'Impresoras y equipos',
                    'Papelería y suministros',
                    'Equipos comerciales',
                    'Herramientas para emprendimientos',
                    'Equipos de seguridad',
                    'Otros artículos para oficina',
                ],
            ],

            [
                'nombre' => 'Herramientas y construcción',
                'descripcion' => 'Herramientas, equipos y materiales para construcción y mantenimiento.',
                'icono' => '🔨',
                'subcategorias' => [
                    'Herramientas manuales',
                    'Herramientas eléctricas',
                    'Maquinaria',
                    'Materiales de construcción',
                    'Pinturas y acabados',
                    'Plomería',
                    'Electricidad',
                    'Ferretería',
                    'Seguridad industrial',
                    'Otros artículos de construcción',
                ],
            ],

            [
                'nombre' => 'Mascotas',
                'descripcion' => 'Productos, alimentos y accesorios para mascotas.',
                'icono' => '🐾',
                'subcategorias' => [
                    'Perros',
                    'Gatos',
                    'Aves',
                    'Peces',
                    'Otros animales',
                    'Alimentos para mascotas',
                    'Accesorios para mascotas',
                    'Camas y casas',
                    'Juguetes para mascotas',
                    'Otros artículos para mascotas',
                ],
            ],

            [
                'nombre' => 'Arte y coleccionables',
                'descripcion' => 'Obras de arte, artesanías, antigüedades y artículos coleccionables.',
                'icono' => '🎨',
                'subcategorias' => [
                    'Pinturas y obras de arte',
                    'Artesanías',
                    'Antigüedades',
                    'Coleccionables',
                    'Figuras y modelos',
                    'Monedas y billetes',
                    'Decoración artesanal',
                    'Otros artículos de colección',
                ],
            ],

            [
                'nombre' => 'Música y entretenimiento',
                'descripcion' => 'Instrumentos, música, películas y artículos de entretenimiento.',
                'icono' => '🎵',
                'subcategorias' => [
                    'Instrumentos musicales',
                    'Accesorios musicales',
                    'Películas',
                    'Música',
                    'Discos y vinilos',
                    'Colecciones',
                    'Juegos de mesa',
                    'Otros artículos de entretenimiento',
                ],
            ],

            [
                'nombre' => 'Salud y bienestar',
                'descripcion' => 'Artículos relacionados con bienestar, ejercicio y cuidado personal.',
                'icono' => '💚',
                'subcategorias' => [
                    'Fitness',
                    'Cuidado personal',
                    'Masaje y relajación',
                    'Equipos de ejercicio',
                    'Productos de bienestar',
                    'Otros artículos de salud y bienestar',
                ],
            ],

            [
                'nombre' => 'Otros',
                'descripcion' => 'Productos que no corresponden a ninguna de las categorías anteriores.',
                'icono' => '📦',
                'subcategorias' => [
                    'Otros productos',
                    'Artículos diversos',
                ],
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | CREAR CATEGORÍAS Y SUBCATEGORÍAS
        |--------------------------------------------------------------------------
        */

        foreach ($categorias as $indiceCategoria => $categoriaData) {

            $categoria = MarketplaceCategoria::firstOrCreate(
                [
                    'slug' => Str::slug($categoriaData['nombre']),
                ],
                [
                    'nombre' => $categoriaData['nombre'],
                    'descripcion' => $categoriaData['descripcion'],
                    'icono' => $categoriaData['icono'],
                    'activo' => true,
                    'orden' => $indiceCategoria + 1,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | SUBCATEGORÍAS
            |--------------------------------------------------------------------------
            */

            foreach ($categoriaData['subcategorias'] as $indiceSubcategoria => $nombreSubcategoria) {

                MarketplaceSubcategoria::firstOrCreate(
                    [
                        'categoria_id' => $categoria->id,
                        'slug' => Str::slug($nombreSubcategoria),
                    ],
                    [
                        'nombre' => $nombreSubcategoria,
                        'activo' => true,
                        'orden' => $indiceSubcategoria + 1,
                    ]
                );
            }
        }

        $this->command->info(
            'Categorías y subcategorías del Marketplace creadas correctamente.'
        );
    }
}
