<?php

namespace Database\Seeders;

use App\Models\EmprendimientoCategoria;
use Illuminate\Database\Seeder;

class EmprendimientoCategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Alimentos y bebidas',
                'descripcion' => 'Emprendimientos relacionados con la producción, preparación y comercialización de alimentos y bebidas.',
                'orden' => 1,
            ],
            [
                'nombre' => 'Artesanías y manualidades',
                'descripcion' => 'Emprendimientos dedicados a la elaboración de productos artesanales, artísticos y hechos a mano.',
                'orden' => 2,
            ],
            [
                'nombre' => 'Belleza y cuidado personal',
                'descripcion' => 'Productos y servicios relacionados con belleza, estética y cuidado personal.',
                'orden' => 3,
            ],
            [
                'nombre' => 'Comercio',
                'descripcion' => 'Emprendimientos dedicados a la compra, venta, distribución y comercialización de productos.',
                'orden' => 4,
            ],
            [
                'nombre' => 'Educación y capacitación',
                'descripcion' => 'Emprendimientos relacionados con formación, enseñanza, capacitación y desarrollo de conocimientos.',
                'orden' => 5,
            ],
            [
                'nombre' => 'Hogar',
                'descripcion' => 'Productos y servicios destinados al mantenimiento, mejoramiento y funcionamiento del hogar.',
                'orden' => 6,
            ],
            [
                'nombre' => 'Tecnología',
                'descripcion' => 'Emprendimientos relacionados con tecnología, software, servicios digitales e informática.',
                'orden' => 7,
            ],
            [
                'nombre' => 'Servicios profesionales',
                'descripcion' => 'Servicios especializados ofrecidos por profesionales y consultores.',
                'orden' => 8,
            ],
            [
                'nombre' => 'Transporte y logística',
                'descripcion' => 'Emprendimientos relacionados con transporte, distribución, mensajería y logística.',
                'orden' => 9,
            ],
            [
                'nombre' => 'Agricultura y producción',
                'descripcion' => 'Emprendimientos relacionados con agricultura, producción agropecuaria y productos del campo.',
                'orden' => 10,
            ],
            [
                'nombre' => 'Manufactura',
                'descripcion' => 'Emprendimientos dedicados a la transformación y fabricación de productos.',
                'orden' => 11,
            ],
            [
                'nombre' => 'Moda y confección',
                'descripcion' => 'Emprendimientos relacionados con ropa, calzado, accesorios, diseño y confección.',
                'orden' => 12,
            ],
            [
                'nombre' => 'Entretenimiento y cultura',
                'descripcion' => 'Emprendimientos relacionados con arte, música, fotografía, producción audiovisual y entretenimiento.',
                'orden' => 13,
            ],
            [
                'nombre' => 'Turismo y hospitalidad',
                'descripcion' => 'Emprendimientos relacionados con turismo, alojamiento, experiencias y hospitalidad.',
                'orden' => 14,
            ],
            [
                'nombre' => 'Otros',
                'descripcion' => 'Emprendimientos que no se encuentran dentro de las demás categorías.',
                'orden' => 15,
            ],
        ];

        foreach ($categorias as $categoria) {
            EmprendimientoCategoria::updateOrCreate(
                [
                    'nombre' => $categoria['nombre'],
                ],
                [
                    'descripcion' => $categoria['descripcion'],
                    'activo' => true,
                    'orden' => $categoria['orden'],
                ]
            );
        }
    }
}
