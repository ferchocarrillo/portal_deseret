<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ARGENTINA
    |--------------------------------------------------------------------------
    */

    'AR' => [

        'nombre' => 'Argentina',
        'codigo_iso2' => 'AR',
        'codigo_iso3' => 'ARG',
        'codigo_telefonico' => '54',

        'division_label' => 'Provincia',
        'localidad_label' => 'Localidad',

        'divisiones' => [

            [
                'nombre' => 'Buenos Aires',
                'codigo' => '06',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 1,
                'hijos' => [],
            ],

            [
                'nombre' => 'Catamarca',
                'codigo' => '10',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 2,
                'hijos' => [],
            ],

            [
                'nombre' => 'Chaco',
                'codigo' => '22',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 3,
                'hijos' => [],
            ],

            [
                'nombre' => 'Chubut',
                'codigo' => '26',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 4,
                'hijos' => [],
            ],

            [
                'nombre' => 'Ciudad Autónoma de Buenos Aires',
                'codigo' => '02',
                'tipo' => 'ciudad_autonoma',
                'label' => 'Ciudad Autónoma',
                'orden' => 5,
                'hijos' => [],
            ],

            [
                'nombre' => 'Córdoba',
                'codigo' => '14',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 6,
                'hijos' => [],
            ],

            [
                'nombre' => 'Corrientes',
                'codigo' => '18',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 7,
                'hijos' => [],
            ],

            [
                'nombre' => 'Entre Ríos',
                'codigo' => '30',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 8,
                'hijos' => [],
            ],

            [
                'nombre' => 'Formosa',
                'codigo' => '34',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 9,
                'hijos' => [],
            ],

            [
                'nombre' => 'Jujuy',
                'codigo' => '38',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 10,
                'hijos' => [],
            ],

            [
                'nombre' => 'La Pampa',
                'codigo' => '42',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 11,
                'hijos' => [],
            ],

            [
                'nombre' => 'La Rioja',
                'codigo' => '46',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 12,
                'hijos' => [],
            ],

            [
                'nombre' => 'Mendoza',
                'codigo' => '50',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 13,
                'hijos' => [],
            ],

            [
                'nombre' => 'Misiones',
                'codigo' => '54',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 14,
                'hijos' => [],
            ],

            [
                'nombre' => 'Neuquén',
                'codigo' => '58',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 15,
                'hijos' => [],
            ],

            [
                'nombre' => 'Río Negro',
                'codigo' => '62',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 16,
                'hijos' => [],
            ],

            [
                'nombre' => 'Salta',
                'codigo' => '66',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 17,
                'hijos' => [],
            ],

            [
                'nombre' => 'San Juan',
                'codigo' => '70',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 18,
                'hijos' => [],
            ],

            [
                'nombre' => 'San Luis',
                'codigo' => '74',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 19,
                'hijos' => [],
            ],

            [
                'nombre' => 'Santa Cruz',
                'codigo' => '78',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 20,
                'hijos' => [],
            ],

            [
                'nombre' => 'Santa Fe',
                'codigo' => '82',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 21,
                'hijos' => [],
            ],

            [
                'nombre' => 'Santiago del Estero',
                'codigo' => '86',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 22,
                'hijos' => [],
            ],

            [
                'nombre' => 'Tierra del Fuego, Antártida e Islas del Atlántico Sur',
                'codigo' => '94',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 23,
                'hijos' => [],
            ],

            [
                'nombre' => 'Tucumán',
                'codigo' => '90',
                'tipo' => 'provincia',
                'label' => 'Provincia',
                'orden' => 24,
                'hijos' => [],
            ],

        ],
    ],


    /*
|--------------------------------------------------------------------------
| BOLIVIA
|--------------------------------------------------------------------------
*/

    'BO' => [

        'nombre' => 'Bolivia',
        'codigo_iso2' => 'BO',
        'codigo_iso3' => 'BOL',
        'codigo_telefonico' => '591',

        'division_label' => 'Departamento',
        'localidad_label' => 'Municipio',

        'divisiones' => [

            /*
        |--------------------------------------------------------------------------
        | CHUQUISACA
        |--------------------------------------------------------------------------
        */

            [
                'nombre' => 'Chuquisaca',
                'codigo' => '01',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 1,

                'hijos' => [

                    [
                        'nombre' => 'Oropeza',
                        'codigo' => '01',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,

                        'ciudades' => [
                            [
                                'nombre' => 'Sucre',
                                'tipo' => 'municipio',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Yotala',
                                'tipo' => 'municipio',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Poroma',
                                'tipo' => 'municipio',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Azurduy',
                        'codigo' => '02',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,

                        'ciudades' => [
                            [
                                'nombre' => 'Azurduy',
                                'tipo' => 'municipio',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Tarvita',
                                'tipo' => 'municipio',
                                'orden' => 2,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Zudáñez',
                        'codigo' => '03',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 3,

                        'ciudades' => [
                            [
                                'nombre' => 'Zudáñez',
                                'tipo' => 'municipio',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Presto',
                                'tipo' => 'municipio',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Mojocoya',
                                'tipo' => 'municipio',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Icla',
                                'tipo' => 'municipio',
                                'orden' => 4,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Tomina',
                        'codigo' => '04',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 4,

                        'ciudades' => [
                            [
                                'nombre' => 'Padilla',
                                'tipo' => 'municipio',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Tomina',
                                'tipo' => 'municipio',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Sopachuy',
                                'tipo' => 'municipio',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Alcalá',
                                'tipo' => 'municipio',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'El Villar',
                                'tipo' => 'municipio',
                                'orden' => 5,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Hernando Siles',
                        'codigo' => '05',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 5,

                        'ciudades' => [
                            [
                                'nombre' => 'Monteagudo',
                                'tipo' => 'municipio',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Huacareta',
                                'tipo' => 'municipio',
                                'orden' => 2,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Yamparáez',
                        'codigo' => '06',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 6,

                        'ciudades' => [
                            [
                                'nombre' => 'Tarabuco',
                                'tipo' => 'municipio',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Yamparáez',
                                'tipo' => 'municipio',
                                'orden' => 2,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Nor Cinti',
                        'codigo' => '07',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 7,

                        'ciudades' => [
                            [
                                'nombre' => 'Camargo',
                                'tipo' => 'municipio',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'San Lucas',
                                'tipo' => 'municipio',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Incahuasi',
                                'tipo' => 'municipio',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Villa Charcas',
                                'tipo' => 'municipio',
                                'orden' => 4,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Belisario Boeto',
                        'codigo' => '08',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 8,

                        'ciudades' => [
                            [
                                'nombre' => 'Villa Serrano',
                                'tipo' => 'municipio',
                                'orden' => 1,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Sud Cinti',
                        'codigo' => '09',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 9,

                        'ciudades' => [
                            [
                                'nombre' => 'Villa Abecia',
                                'tipo' => 'municipio',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Culpina',
                                'tipo' => 'municipio',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Las Carreras',
                                'tipo' => 'municipio',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Luis Calvo',
                        'codigo' => '10',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 10,

                        'ciudades' => [
                            [
                                'nombre' => 'Villa Vaca Guzmán',
                                'tipo' => 'municipio',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Huacaya',
                                'tipo' => 'municipio',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Macharetí',
                                'tipo' => 'municipio',
                                'orden' => 3,
                            ],
                        ],
                    ],

                ],
            ],

            /*
        |--------------------------------------------------------------------------
        | LA PAZ
        |--------------------------------------------------------------------------
        */

            [
                'nombre' => 'La Paz',
                'codigo' => '02',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 2,

                'hijos' => [

                    [
                        'nombre' => 'Murillo',
                        'codigo' => '01',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,

                        'ciudades' => [
                            ['nombre' => 'Nuestra Señora de La Paz', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Palca', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Mecapaca', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Achocalla', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'El Alto', 'tipo' => 'municipio', 'orden' => 5],
                        ],
                    ],

                    [
                        'nombre' => 'Omasuyos',
                        'codigo' => '02',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,

                        'ciudades' => [
                            ['nombre' => 'Achacachi', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Villa Ancoraimes', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Huarina', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Santiago de Huata', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'Huatajata', 'tipo' => 'municipio', 'orden' => 5],
                            ['nombre' => 'Chua Cocani', 'tipo' => 'municipio', 'orden' => 6],
                        ],
                    ],

                    [
                        'nombre' => 'Pacajes',
                        'codigo' => '03',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 3,

                        'ciudades' => [
                            ['nombre' => 'Corocoro', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Caquiaviri', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Calacoto', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Comanche', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'Charaña', 'tipo' => 'municipio', 'orden' => 5],
                            ['nombre' => 'Waldo Ballivián', 'tipo' => 'municipio', 'orden' => 6],
                            ['nombre' => 'Nazacara de Pacajes', 'tipo' => 'municipio', 'orden' => 7],
                            ['nombre' => 'Santiago de Callapa', 'tipo' => 'municipio', 'orden' => 8],
                        ],
                    ],

                    [
                        'nombre' => 'Camacho',
                        'codigo' => '04',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 4,

                        'ciudades' => [
                            ['nombre' => 'Puerto Acosta', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Mocomoco', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Puerto Mayor de Carabuco', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Humanata', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'Escoma', 'tipo' => 'municipio', 'orden' => 5],
                        ],
                    ],

                    [
                        'nombre' => 'Muñecas',
                        'codigo' => '05',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 5,

                        'ciudades' => [
                            ['nombre' => 'Chuma', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Ayata', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Aucapata', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Larecaja',
                        'codigo' => '06',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 6,

                        'ciudades' => [
                            ['nombre' => 'Sorata', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Guanay', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Tacacoma', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Quiabaya', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'Combaya', 'tipo' => 'municipio', 'orden' => 5],
                            ['nombre' => 'Tipuani', 'tipo' => 'municipio', 'orden' => 6],
                            ['nombre' => 'Mapiri', 'tipo' => 'municipio', 'orden' => 7],
                            ['nombre' => 'Teoponte', 'tipo' => 'municipio', 'orden' => 8],
                        ],
                    ],

                    [
                        'nombre' => 'Franz Tamayo',
                        'codigo' => '07',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 7,

                        'ciudades' => [
                            ['nombre' => 'Apolo', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Pelechuco', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Ingavi',
                        'codigo' => '08',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 8,

                        'ciudades' => [
                            ['nombre' => 'Viacha', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Puerto Mayor de Guaqui', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Tiahuanacu', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Desaguadero', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'La Marka San Andrés de Machaca', 'tipo' => 'municipio', 'orden' => 5],
                            ['nombre' => 'Jesús de Machaca', 'tipo' => 'municipio', 'orden' => 6],
                            ['nombre' => 'Taraco', 'tipo' => 'municipio', 'orden' => 7],
                        ],
                    ],

                    [
                        'nombre' => 'Loayza',
                        'codigo' => '09',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 9,

                        'ciudades' => [
                            ['nombre' => 'Luribay', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Sapahaqui', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Yaco', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Malla', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'Cairoma', 'tipo' => 'municipio', 'orden' => 5],
                        ],
                    ],

                    [
                        'nombre' => 'Inquisivi',
                        'codigo' => '10',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 10,

                        'ciudades' => [
                            ['nombre' => 'Inquisivi', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Quime', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Cajuata', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Colquiri', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'Ichoca', 'tipo' => 'municipio', 'orden' => 5],
                            ['nombre' => 'Villa Libertad Licoma', 'tipo' => 'municipio', 'orden' => 6],
                        ],
                    ],

                    [
                        'nombre' => 'Sud Yungas',
                        'codigo' => '11',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 11,

                        'ciudades' => [
                            ['nombre' => 'Chulumani', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Irupana', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Yanacachi', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Palos Blancos', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'La Asunta', 'tipo' => 'municipio', 'orden' => 5],
                        ],
                    ],

                    [
                        'nombre' => 'Los Andes',
                        'codigo' => '12',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 12,

                        'ciudades' => [
                            ['nombre' => 'Pucarani', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Laja', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Batallas', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Puerto Pérez', 'tipo' => 'municipio', 'orden' => 4],
                        ],
                    ],

                    [
                        'nombre' => 'Aroma',
                        'codigo' => '13',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 13,

                        'ciudades' => [
                            ['nombre' => 'Sicasica', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Umala', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Ayo Ayo', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Calamarca', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'Patacamaya', 'tipo' => 'municipio', 'orden' => 5],
                            ['nombre' => 'Colquencha', 'tipo' => 'municipio', 'orden' => 6],
                            ['nombre' => 'Collana', 'tipo' => 'municipio', 'orden' => 7],
                        ],
                    ],

                    [
                        'nombre' => 'Nor Yungas',
                        'codigo' => '14',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 14,

                        'ciudades' => [
                            ['nombre' => 'Coroico', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Coripata', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Abel Iturralde',
                        'codigo' => '15',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 15,

                        'ciudades' => [
                            ['nombre' => 'Ixiamas', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'San Buenaventura', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Bautista Saavedra',
                        'codigo' => '16',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 16,

                        'ciudades' => [
                            ['nombre' => 'Charazani', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Curva', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Manco Kapac',
                        'codigo' => '17',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 17,

                        'ciudades' => [
                            ['nombre' => 'Copacabana', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'San Pedro de Tiquina', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Tito Yupanqui', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Gualberto Villarroel',
                        'codigo' => '18',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 18,

                        'ciudades' => [
                            ['nombre' => 'San Pedro de Curahuara', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Papel Pampa', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Chacarilla', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'General José Manuel Pando',
                        'codigo' => '19',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 19,

                        'ciudades' => [
                            ['nombre' => 'Santiago de Machaca', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Catacora', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Caranavi',
                        'codigo' => '20',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 20,

                        'ciudades' => [
                            ['nombre' => 'Caranavi', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Alto Beni', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                ],
            ],

            /*
        |--------------------------------------------------------------------------
        | COCHABAMBA
        |--------------------------------------------------------------------------
        */

            [
                'nombre' => 'Cochabamba',
                'codigo' => '03',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 3,

                'hijos' => [

                    [
                        'nombre' => 'Cercado',
                        'codigo' => '01',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,

                        'ciudades' => [
                            ['nombre' => 'Cochabamba', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                    [
                        'nombre' => 'Campero',
                        'codigo' => '02',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,

                        'ciudades' => [
                            ['nombre' => 'Aiquile', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Pasorapa', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Omereque', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Ayopaya',
                        'codigo' => '03',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 3,

                        'ciudades' => [
                            ['nombre' => 'Ayopaya', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Morochata', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Cocapata', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Esteban Arce',
                        'codigo' => '04',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 4,

                        'ciudades' => [
                            ['nombre' => 'Tarata', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Anzaldo', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Arbieto', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Sacabamba', 'tipo' => 'municipio', 'orden' => 4],
                        ],
                    ],

                    [
                        'nombre' => 'Arani',
                        'codigo' => '05',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 5,

                        'ciudades' => [
                            ['nombre' => 'Arani', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Vacas', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Arque',
                        'codigo' => '06',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 6,

                        'ciudades' => [
                            ['nombre' => 'Arque', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Tacopaya', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Capinota',
                        'codigo' => '07',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 7,

                        'ciudades' => [
                            ['nombre' => 'Capinota', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Santivañez', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Sicaya', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Germán Jordán',
                        'codigo' => '08',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 8,

                        'ciudades' => [
                            ['nombre' => 'Cliza', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Toco', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Tolata', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Quillacollo',
                        'codigo' => '09',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 9,

                        'ciudades' => [
                            ['nombre' => 'Quillacollo', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Sipe Sipe', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Tiquipaya', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Vinto', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'Colcapirhua', 'tipo' => 'municipio', 'orden' => 5],
                        ],
                    ],

                    [
                        'nombre' => 'Chapare',
                        'codigo' => '10',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 10,

                        'ciudades' => [
                            ['nombre' => 'Sacaba', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Colomi', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Villa Tunari', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Tapacarí',
                        'codigo' => '11',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 11,

                        'ciudades' => [
                            ['nombre' => 'Tapacarí', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                    [
                        'nombre' => 'Carrasco',
                        'codigo' => '12',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 12,

                        'ciudades' => [
                            ['nombre' => 'Totora', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Pojo', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Pocona', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Chimoré', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'Puerto Villarroel', 'tipo' => 'municipio', 'orden' => 5],
                            ['nombre' => 'Entre Ríos', 'tipo' => 'municipio', 'orden' => 6],
                        ],
                    ],

                    [
                        'nombre' => 'Mizque',
                        'codigo' => '13',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 13,

                        'ciudades' => [
                            ['nombre' => 'Mizque', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Vila Vila', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Alalay', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Raqaypampa', 'tipo' => 'municipio', 'orden' => 4],
                        ],
                    ],

                    [
                        'nombre' => 'Punata',
                        'codigo' => '14',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 14,

                        'ciudades' => [
                            ['nombre' => 'Punata', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Villa Rivero', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'San Benito', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Tacachi', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'Villa Gualberto Villarroel', 'tipo' => 'municipio', 'orden' => 5],
                        ],
                    ],

                    [
                        'nombre' => 'Bolívar',
                        'codigo' => '15',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 15,

                        'ciudades' => [
                            ['nombre' => 'Bolívar', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                    [
                        'nombre' => 'Tiraque',
                        'codigo' => '16',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 16,

                        'ciudades' => [
                            ['nombre' => 'Tiraque', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Shinahota', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                ],
            ],

            /*
        |--------------------------------------------------------------------------
        | ORURO
        |--------------------------------------------------------------------------
        */

            [
                'nombre' => 'Oruro',
                'codigo' => '04',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 4,

                'hijos' => [

                    [
                        'nombre' => 'Cercado',
                        'codigo' => '01',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,
                        'ciudades' => [
                            ['nombre' => 'Oruro', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Caracollo', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'El Choro', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Paria', 'tipo' => 'municipio', 'orden' => 4],
                        ],
                    ],

                    [
                        'nombre' => 'Abaroa',
                        'codigo' => '02',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,
                        'ciudades' => [
                            ['nombre' => 'Challapata', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Santuario de Quillacas', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Carangas',
                        'codigo' => '03',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 3,
                        'ciudades' => [
                            ['nombre' => 'Corque', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Choquecota', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Sajama',
                        'codigo' => '04',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 4,
                        'ciudades' => [
                            ['nombre' => 'Curahuara de Carangas', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Turco', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Litoral',
                        'codigo' => '05',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 5,
                        'ciudades' => [
                            ['nombre' => 'Huachacalla', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Escara', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Cruz de Machacamarca', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Yunguyo del Litoral', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'Esmeralda', 'tipo' => 'municipio', 'orden' => 5],
                        ],
                    ],

                    [
                        'nombre' => 'Poopó',
                        'codigo' => '06',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 6,
                        'ciudades' => [
                            ['nombre' => 'Poopó', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Pazña', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Antequera', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Pantaleón Dalence',
                        'codigo' => '07',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 7,
                        'ciudades' => [
                            ['nombre' => 'Huanuni', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Machacamarca', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Ladislao Cabrera',
                        'codigo' => '08',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 8,
                        'ciudades' => [
                            ['nombre' => 'Salinas', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Pampa Aullagas', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Sabaya',
                        'codigo' => '09',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 9,
                        'ciudades' => [
                            ['nombre' => 'Sabaya', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Coipasa', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Nación Originaria Uru Chipaya', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Saucarí',
                        'codigo' => '10',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 10,
                        'ciudades' => [
                            ['nombre' => 'Toledo', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                    [
                        'nombre' => 'Tomás Barrón',
                        'codigo' => '11',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 11,
                        'ciudades' => [
                            ['nombre' => 'Eucaliptus', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                    [
                        'nombre' => 'Sur Carangas',
                        'codigo' => '12',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 12,
                        'ciudades' => [
                            ['nombre' => 'Andamarca', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Belén de Andamarca', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'San Pedro de Totora',
                        'codigo' => '13',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 13,
                        'ciudades' => [
                            ['nombre' => 'Totora', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                    [
                        'nombre' => 'Sebastián Pagador',
                        'codigo' => '14',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 14,
                        'ciudades' => [
                            ['nombre' => 'Santiago de Huari', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                    [
                        'nombre' => 'Mejillones',
                        'codigo' => '15',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 15,
                        'ciudades' => [
                            ['nombre' => 'La Rivera', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Todos Santos', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Carangas', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Nor Carangas',
                        'codigo' => '16',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 16,
                        'ciudades' => [
                            ['nombre' => 'Santiago de Huayllamarca', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                ],
            ],

            /*
        |--------------------------------------------------------------------------
        | POTOSÍ
        |--------------------------------------------------------------------------
        */

            [
                'nombre' => 'Potosí',
                'codigo' => '05',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 5,

                'hijos' => [

                    [
                        'nombre' => 'Tomás Frías',
                        'codigo' => '01',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,
                        'ciudades' => [
                            ['nombre' => 'Potosí', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Tinguipaya', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Yocalla', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Urmiri', 'tipo' => 'municipio', 'orden' => 4],
                        ],
                    ],

                    [
                        'nombre' => 'Rafael Bustillo',
                        'codigo' => '02',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,
                        'ciudades' => [
                            ['nombre' => 'Uncía', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Chayanta', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Llallagua', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Chuquihuta Ayllu Jucumani', 'tipo' => 'municipio', 'orden' => 4],
                        ],
                    ],

                    [
                        'nombre' => 'Cornelio Saavedra',
                        'codigo' => '03',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 3,
                        'ciudades' => [
                            ['nombre' => 'Betanzos', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Chaquí', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Tacobamba', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Chayanta',
                        'codigo' => '04',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 4,
                        'ciudades' => [
                            ['nombre' => 'Colquechaca', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Ravelo', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Pocoata', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Ocurí', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'San Pedro de Macha', 'tipo' => 'municipio', 'orden' => 5],
                        ],
                    ],

                    [
                        'nombre' => 'Charcas',
                        'codigo' => '05',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 5,
                        'ciudades' => [
                            ['nombre' => 'San Pedro', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Toro Toro', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Nor Chichas',
                        'codigo' => '06',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 6,
                        'ciudades' => [
                            ['nombre' => 'Cotagaita', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Vitichi', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Alonso de Ibáñez',
                        'codigo' => '07',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 7,
                        'ciudades' => [
                            ['nombre' => 'Sacaca', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Caripuyo', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Sur Chichas',
                        'codigo' => '08',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 8,
                        'ciudades' => [
                            ['nombre' => 'Tupiza', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Atocha', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Nor Lípez',
                        'codigo' => '09',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 9,
                        'ciudades' => [
                            ['nombre' => 'Colcha K', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'San Pedro de Quemes', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Sud Lípez',
                        'codigo' => '10',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 10,
                        'ciudades' => [
                            ['nombre' => 'San Pablo de Lípez', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Mojinete', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'San Antonio de Esmoruco', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'José María Linares',
                        'codigo' => '11',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 11,
                        'ciudades' => [
                            ['nombre' => 'Puna', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Caiza D', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Ckochas', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Antonio Quijarro',
                        'codigo' => '12',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 12,
                        'ciudades' => [
                            ['nombre' => 'Uyuni', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Tomave', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Porco', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Jatun Ayllu Yura', 'tipo' => 'municipio', 'orden' => 4],
                        ],
                    ],

                    [
                        'nombre' => 'General Bernardino Bilbao',
                        'codigo' => '13',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 13,
                        'ciudades' => [
                            ['nombre' => 'Arampampa', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Acasio', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Daniel Campos',
                        'codigo' => '14',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 14,
                        'ciudades' => [
                            ['nombre' => 'Llica', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Tahua', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Modesto Omiste',
                        'codigo' => '15',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 15,
                        'ciudades' => [
                            ['nombre' => 'Villazón', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                    [
                        'nombre' => 'Enrique Baldivieso',
                        'codigo' => '16',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 16,
                        'ciudades' => [
                            ['nombre' => 'San Agustín', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                ],
            ],

            /*
        |--------------------------------------------------------------------------
        | TARIJA
        |--------------------------------------------------------------------------
        */

            [
                'nombre' => 'Tarija',
                'codigo' => '06',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 6,

                'hijos' => [

                    [
                        'nombre' => 'Cercado',
                        'codigo' => '01',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,
                        'ciudades' => [
                            ['nombre' => 'Tarija', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                    [
                        'nombre' => 'Arce',
                        'codigo' => '02',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,
                        'ciudades' => [
                            ['nombre' => 'Padcaya', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Bermejo', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Gran Chaco',
                        'codigo' => '03',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 3,
                        'ciudades' => [
                            ['nombre' => 'Yacuiba', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Caraparí', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Villa Montes', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Avilés',
                        'codigo' => '04',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 4,
                        'ciudades' => [
                            ['nombre' => 'Uriondo', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Yunchará', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Méndez',
                        'codigo' => '05',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 5,
                        'ciudades' => [
                            ['nombre' => 'San Lorenzo', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'El Puente', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => "O'Connor",
                        'codigo' => '06',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 6,
                        'ciudades' => [
                            ['nombre' => 'Entre Ríos', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                ],
            ],

            /*
        |--------------------------------------------------------------------------
        | SANTA CRUZ
        |--------------------------------------------------------------------------
        */

            [
                'nombre' => 'Santa Cruz',
                'codigo' => '07',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 7,

                'hijos' => [

                    [
                        'nombre' => 'Andrés Ibáñez',
                        'codigo' => '01',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,
                        'ciudades' => [
                            ['nombre' => 'Santa Cruz de la Sierra', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Cotoca', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Porongo', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'La Guardia', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'El Torno', 'tipo' => 'municipio', 'orden' => 5],
                        ],
                    ],

                    [
                        'nombre' => 'Warnes',
                        'codigo' => '02',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,
                        'ciudades' => [
                            ['nombre' => 'Warnes', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Okinawa Uno', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Velasco',
                        'codigo' => '03',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 3,
                        'ciudades' => [
                            ['nombre' => 'San Ignacio', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'San Miguel', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'San Rafael', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Ichilo',
                        'codigo' => '04',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 4,
                        'ciudades' => [
                            ['nombre' => 'Buena Vista', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'San Carlos', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Yapacaní', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'San Juan', 'tipo' => 'municipio', 'orden' => 4],
                        ],
                    ],

                    [
                        'nombre' => 'Chiquitos',
                        'codigo' => '05',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 5,
                        'ciudades' => [
                            ['nombre' => 'San José', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Pailón', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Roboré', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Sara',
                        'codigo' => '06',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 6,
                        'ciudades' => [
                            ['nombre' => 'Portachuelo', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Santa Rosa', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Colpa Bélgica', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Cordillera',
                        'codigo' => '07',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 7,
                        'ciudades' => [
                            ['nombre' => 'Lagunillas', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Charagua Iyambae', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Cabezas', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Cuevo', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'Kereimba Iyambae', 'tipo' => 'municipio', 'orden' => 5],
                            ['nombre' => 'Camiri', 'tipo' => 'municipio', 'orden' => 6],
                            ['nombre' => 'Boyuibe', 'tipo' => 'municipio', 'orden' => 7],
                        ],
                    ],

                    [
                        'nombre' => 'Vallegrande',
                        'codigo' => '08',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 8,
                        'ciudades' => [
                            ['nombre' => 'Vallegrande', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Trigal', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Moromoro', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Postrervalle', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'Pucará', 'tipo' => 'municipio', 'orden' => 5],
                        ],
                    ],

                    [
                        'nombre' => 'Florida',
                        'codigo' => '09',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 9,
                        'ciudades' => [
                            ['nombre' => 'Samaipata', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Pampa Grande', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Mairana', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Quirusillas', 'tipo' => 'municipio', 'orden' => 4],
                        ],
                    ],

                    [
                        'nombre' => 'Obispo Santistevan',
                        'codigo' => '10',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 10,
                        'ciudades' => [
                            ['nombre' => 'Montero', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'General Saavedra', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Mineros', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Fernández Alonso', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'San Pedro', 'tipo' => 'municipio', 'orden' => 5],
                        ],
                    ],

                    [
                        'nombre' => 'Ñuflo de Chávez',
                        'codigo' => '11',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 11,
                        'ciudades' => [
                            ['nombre' => 'Concepción', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'San Javier', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'San Julián', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'San Antonio de Lomerío', 'tipo' => 'municipio', 'orden' => 4],
                            ['nombre' => 'San Ramón', 'tipo' => 'municipio', 'orden' => 5],
                            ['nombre' => 'Cuatro Cañadas', 'tipo' => 'municipio', 'orden' => 6],
                        ],
                    ],

                    [
                        'nombre' => 'Ángel Sandoval',
                        'codigo' => '12',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 12,
                        'ciudades' => [
                            ['nombre' => 'San Matías', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                    [
                        'nombre' => 'Manuel María Caballero',
                        'codigo' => '13',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 13,
                        'ciudades' => [
                            ['nombre' => 'Comarapa', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Saipina', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Germán Busch',
                        'codigo' => '14',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 14,
                        'ciudades' => [
                            ['nombre' => 'Puerto Suárez', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Puerto Quijarro', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'El Carmen Rivero Tórrez', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Guarayos',
                        'codigo' => '15',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 15,
                        'ciudades' => [
                            ['nombre' => 'Ascensión de Guarayos', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Urubichá', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'El Puente', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                ],
            ],

            /*
        |--------------------------------------------------------------------------
        | BENI
        |--------------------------------------------------------------------------
        */

            [
                'nombre' => 'Beni',
                'codigo' => '08',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 8,

                'hijos' => [

                    [
                        'nombre' => 'Cercado',
                        'codigo' => '01',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,
                        'ciudades' => [
                            ['nombre' => 'Trinidad', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'San Javier', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Vaca Díez',
                        'codigo' => '02',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,
                        'ciudades' => [
                            ['nombre' => 'Riberalta', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Guayaramerín', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'General José Ballivián',
                        'codigo' => '03',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 3,
                        'ciudades' => [
                            ['nombre' => 'Reyes', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'San Borja', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Santa Rosa', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Rurrenabaque', 'tipo' => 'municipio', 'orden' => 4],
                        ],
                    ],

                    [
                        'nombre' => 'Yacuma',
                        'codigo' => '04',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 4,
                        'ciudades' => [
                            ['nombre' => 'Santa Ana', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Exaltación', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Moxos',
                        'codigo' => '05',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 5,
                        'ciudades' => [
                            ['nombre' => 'San Ignacio', 'tipo' => 'municipio', 'orden' => 1],
                        ],
                    ],

                    [
                        'nombre' => 'Marbán',
                        'codigo' => '06',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 6,
                        'ciudades' => [
                            ['nombre' => 'Loreto', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'San Andrés', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Mamoré',
                        'codigo' => '07',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 7,
                        'ciudades' => [
                            ['nombre' => 'San Joaquín', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'San Ramón', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Puerto Siles', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Itenez',
                        'codigo' => '08',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 8,
                        'ciudades' => [
                            ['nombre' => 'Magdalena', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Baures', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Huacaraje', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Yacuma - Moxos',
                        'codigo' => '09',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 9,
                        'ciudades' => [
                            [
                                'nombre' => 'Territorio Indígena Multiétnico - TIM',
                                'tipo' => 'municipio',
                                'orden' => 1,
                            ],
                        ],
                    ],

                ],
            ],

            /*
        |--------------------------------------------------------------------------
        | PANDO
        |--------------------------------------------------------------------------
        */

            [
                'nombre' => 'Pando',
                'codigo' => '09',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 9,

                'hijos' => [

                    [
                        'nombre' => 'Nicolás Suárez',
                        'codigo' => '01',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,
                        'ciudades' => [
                            ['nombre' => 'Cobija', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Porvenir', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Bolpebra', 'tipo' => 'municipio', 'orden' => 3],
                            ['nombre' => 'Bella Flor', 'tipo' => 'municipio', 'orden' => 4],
                        ],
                    ],

                    [
                        'nombre' => 'Manuripi',
                        'codigo' => '02',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,
                        'ciudades' => [
                            ['nombre' => 'Puerto Rico', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'San Pedro', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Filadelfia', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Madre de Dios',
                        'codigo' => '03',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 3,
                        'ciudades' => [
                            ['nombre' => 'Puerto Gonzalo Moreno', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'San Lorenzo', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Sena', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                    [
                        'nombre' => 'Abuná',
                        'codigo' => '04',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 4,
                        'ciudades' => [
                            ['nombre' => 'Santa Rosa', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Ingavi', 'tipo' => 'municipio', 'orden' => 2],
                        ],
                    ],

                    [
                        'nombre' => 'Federico Román',
                        'codigo' => '05',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 5,
                        'ciudades' => [
                            ['nombre' => 'Nueva Esperanza', 'tipo' => 'municipio', 'orden' => 1],
                            ['nombre' => 'Villa Nueva', 'tipo' => 'municipio', 'orden' => 2],
                            ['nombre' => 'Santos Mercado', 'tipo' => 'municipio', 'orden' => 3],
                        ],
                    ],

                ],
            ],

        ],
    ],


    /*
|--------------------------------------------------------------------------
| CHILE
|--------------------------------------------------------------------------
*/

    'CL' => [

        'nombre' => 'Chile',
        'codigo_iso2' => 'CL',
        'codigo_iso3' => 'CHL',
        'codigo_telefonico' => '56',

        'division_label' => 'Región',
        'localidad_label' => 'Comuna / Ciudad',

        'divisiones' => [

            [
                'nombre' => 'Arica y Parinacota',
                'codigo' => '15',
                'tipo' => 'region',
                'label' => 'Región',
                'orden' => 1,

                'hijos' => [

                    [
                        'nombre' => 'Arica',
                        'codigo' => '151',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,

                        'ciudades' => [
                            [
                                'nombre' => 'Arica',
                                'codigo' => '15101',
                                'tipo' => 'comuna',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Camarones',
                                'codigo' => '15102',
                                'tipo' => 'comuna',
                                'orden' => 2,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Parinacota',
                        'codigo' => '152',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,

                        'ciudades' => [
                            [
                                'nombre' => 'Putre',
                                'codigo' => '15201',
                                'tipo' => 'comuna',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'General Lagos',
                                'codigo' => '15202',
                                'tipo' => 'comuna',
                                'orden' => 2,
                            ],
                        ],
                    ],

                ],
            ],

            [
                'nombre' => 'Tarapacá',
                'codigo' => '01',
                'tipo' => 'region',
                'label' => 'Región',
                'orden' => 2,

                'hijos' => [

                    [
                        'nombre' => 'Iquique',
                        'codigo' => '011',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,

                        'ciudades' => [
                            [
                                'nombre' => 'Iquique',
                                'codigo' => '1101',
                                'tipo' => 'comuna',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Alto Hospicio',
                                'codigo' => '1107',
                                'tipo' => 'comuna',
                                'orden' => 2,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Tamarugal',
                        'codigo' => '014',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,

                        'ciudades' => [
                            [
                                'nombre' => 'Pozo Almonte',
                                'codigo' => '1401',
                                'tipo' => 'comuna',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Camiña',
                                'codigo' => '1402',
                                'tipo' => 'comuna',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Colchane',
                                'codigo' => '1403',
                                'tipo' => 'comuna',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Huara',
                                'codigo' => '1404',
                                'tipo' => 'comuna',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Pica',
                                'codigo' => '1405',
                                'tipo' => 'comuna',
                                'orden' => 5,
                            ],
                        ],
                    ],

                ],
            ],

            [
                'nombre' => 'Antofagasta',
                'codigo' => '02',
                'tipo' => 'region',
                'label' => 'Región',
                'orden' => 3,

                'hijos' => [

                    [
                        'nombre' => 'Antofagasta',
                        'codigo' => '021',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,

                        'ciudades' => [
                            [
                                'nombre' => 'Antofagasta',
                                'codigo' => '2101',
                                'tipo' => 'comuna',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Mejillones',
                                'codigo' => '2102',
                                'tipo' => 'comuna',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Sierra Gorda',
                                'codigo' => '2103',
                                'tipo' => 'comuna',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Taltal',
                                'codigo' => '2104',
                                'tipo' => 'comuna',
                                'orden' => 4,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'El Loa',
                        'codigo' => '022',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,

                        'ciudades' => [
                            [
                                'nombre' => 'Calama',
                                'codigo' => '2201',
                                'tipo' => 'comuna',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Ollagüe',
                                'codigo' => '2202',
                                'tipo' => 'comuna',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'San Pedro de Atacama',
                                'codigo' => '2203',
                                'tipo' => 'comuna',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Tocopilla',
                        'codigo' => '023',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 3,

                        'ciudades' => [
                            [
                                'nombre' => 'Tocopilla',
                                'codigo' => '2301',
                                'tipo' => 'comuna',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'María Elena',
                                'codigo' => '2302',
                                'tipo' => 'comuna',
                                'orden' => 2,
                            ],
                        ],
                    ],

                ],
            ],

            [
                'nombre' => 'Atacama',
                'codigo' => '03',
                'tipo' => 'region',
                'label' => 'Región',
                'orden' => 4,

                'hijos' => [

                    [
                        'nombre' => 'Copiapó',
                        'codigo' => '031',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,

                        'ciudades' => [
                            [
                                'nombre' => 'Copiapó',
                                'codigo' => '3101',
                                'tipo' => 'comuna',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Caldera',
                                'codigo' => '3102',
                                'tipo' => 'comuna',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Tierra Amarilla',
                                'codigo' => '3103',
                                'tipo' => 'comuna',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Chañaral',
                        'codigo' => '032',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,

                        'ciudades' => [
                            [
                                'nombre' => 'Chañaral',
                                'codigo' => '3201',
                                'tipo' => 'comuna',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Diego de Almagro',
                                'codigo' => '3202',
                                'tipo' => 'comuna',
                                'orden' => 2,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Huasco',
                        'codigo' => '033',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 3,

                        'ciudades' => [
                            [
                                'nombre' => 'Vallenar',
                                'codigo' => '3301',
                                'tipo' => 'comuna',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Alto del Carmen',
                                'codigo' => '3302',
                                'tipo' => 'comuna',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Freirina',
                                'codigo' => '3303',
                                'tipo' => 'comuna',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Huasco',
                                'codigo' => '3304',
                                'tipo' => 'comuna',
                                'orden' => 4,
                            ],
                        ],
                    ],

                ],
            ],

            [
                'nombre' => 'Coquimbo',
                'codigo' => '04',
                'tipo' => 'region',
                'label' => 'Región',
                'orden' => 5,

                'hijos' => [

                    [
                        'nombre' => 'Elqui',
                        'codigo' => '041',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,

                        'ciudades' => [
                            [
                                'nombre' => 'La Serena',
                                'codigo' => '4101',
                                'tipo' => 'comuna',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Coquimbo',
                                'codigo' => '4102',
                                'tipo' => 'comuna',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Andacollo',
                                'codigo' => '4103',
                                'tipo' => 'comuna',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'La Higuera',
                                'codigo' => '4104',
                                'tipo' => 'comuna',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Paiguano',
                                'codigo' => '4105',
                                'tipo' => 'comuna',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Vicuña',
                                'codigo' => '4106',
                                'tipo' => 'comuna',
                                'orden' => 6,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Limarí',
                        'codigo' => '042',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,

                        'ciudades' => [
                            [
                                'nombre' => 'Ovalle',
                                'codigo' => '4301',
                                'tipo' => 'comuna',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Combarbalá',
                                'codigo' => '4302',
                                'tipo' => 'comuna',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Monte Patria',
                                'codigo' => '4303',
                                'tipo' => 'comuna',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Punitaqui',
                                'codigo' => '4304',
                                'tipo' => 'comuna',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Río Hurtado',
                                'codigo' => '4305',
                                'tipo' => 'comuna',
                                'orden' => 5,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Choapa',
                        'codigo' => '043',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 3,

                        'ciudades' => [
                            [
                                'nombre' => 'Illapel',
                                'codigo' => '4201',
                                'tipo' => 'comuna',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Canela',
                                'codigo' => '4202',
                                'tipo' => 'comuna',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Los Vilos',
                                'codigo' => '4203',
                                'tipo' => 'comuna',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Salamanca',
                                'codigo' => '4204',
                                'tipo' => 'comuna',
                                'orden' => 4,
                            ],
                        ],
                    ],

                ],
            ],

            /*
         * Las regiones restantes deben mantener exactamente
         * esta misma estructura:
         *
         * Región
         *   └── Provincia
         *       └── Comuna
         *
         * No deben colocarse comunas directamente bajo la región
         * cuando exista una provincia intermedia.
         */
        ],
    ],

    'CO' => [

        'nombre' => 'Colombia',
        'codigo_iso2' => 'CO',
        'codigo_iso3' => 'COL',
        'codigo_telefonico' => '57',

        'division_label' => 'Departamento',
        'localidad_label' => 'Municipio',

        'divisiones' => [
            [
                'nombre' => 'Bogotá, D.C.',
                'codigo' => '11',
                'tipo' => 'departamento',
                'label' => 'Distrito Capital',
                'orden' => 0,

                'ciudades' => [
                    [
                        'nombre' => 'Bogotá, D.C.',
                        'codigo' => '11001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                ],
            ],


            [
                'nombre' => 'Amazonas',
                'codigo' => '91',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 1,

                'ciudades' => [
                    [
                        'nombre' => 'Leticia',
                        'codigo' => '91001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'El Encanto',
                        'codigo' => '91263',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'La Chorrera',
                        'codigo' => '91405',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'La Pedrera',
                        'codigo' => '91407',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'La Victoria',
                        'codigo' => '91430',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Mirití - Paraná',
                        'codigo' => '91460',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Puerto Alegría',
                        'codigo' => '91530',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Puerto Arica',
                        'codigo' => '91536',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Puerto Nariño',
                        'codigo' => '91540',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Puerto Santander',
                        'codigo' => '91669',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Tarapacá',
                        'codigo' => '91798',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                ],
            ],

            [
                'nombre' => 'Antioquia',
                'codigo' => '05',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 2,

                'ciudades' => [
                    [
                        'nombre' => 'Medellín',
                        'codigo' => '05001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Abejorral',
                        'codigo' => '05002',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Abriaquí',
                        'codigo' => '05004',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Alejandría',
                        'codigo' => '05021',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Amagá',
                        'codigo' => '05030',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Amalfi',
                        'codigo' => '05031',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Andes',
                        'codigo' => '05034',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Angelópolis',
                        'codigo' => '05036',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Angostura',
                        'codigo' => '05038',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Anorí',
                        'codigo' => '05040',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Santa Fe de Antioquia',
                        'codigo' => '05042',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Anzá',
                        'codigo' => '05044',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Apartadó',
                        'codigo' => '05045',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Arboletes',
                        'codigo' => '05051',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Argelia',
                        'codigo' => '05055',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Armenia',
                        'codigo' => '05059',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Barbosa',
                        'codigo' => '05079',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Belmira',
                        'codigo' => '05086',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Bello',
                        'codigo' => '05088',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Betania',
                        'codigo' => '05091',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Betulia',
                        'codigo' => '05093',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Ciudad Bolívar',
                        'codigo' => '05101',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'Briceño',
                        'codigo' => '05107',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'Buriticá',
                        'codigo' => '05113',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Cáceres',
                        'codigo' => '05120',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'Caicedo',
                        'codigo' => '05125',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'Caldas',
                        'codigo' => '05129',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'Campamento',
                        'codigo' => '05134',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'Cañasgordas',
                        'codigo' => '05138',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'Caracolí',
                        'codigo' => '05142',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                    [
                        'nombre' => 'Caramanta',
                        'codigo' => '05145',
                        'tipo' => 'municipio',
                        'orden' => 31,
                    ],
                    [
                        'nombre' => 'Carepa',
                        'codigo' => '05147',
                        'tipo' => 'municipio',
                        'orden' => 32,
                    ],
                    [
                        'nombre' => 'El Carmen de Viboral',
                        'codigo' => '05148',
                        'tipo' => 'municipio',
                        'orden' => 33,
                    ],
                    [
                        'nombre' => 'Carolina',
                        'codigo' => '05150',
                        'tipo' => 'municipio',
                        'orden' => 34,
                    ],
                    [
                        'nombre' => 'Caucasia',
                        'codigo' => '05154',
                        'tipo' => 'municipio',
                        'orden' => 35,
                    ],
                    [
                        'nombre' => 'Chigorodó',
                        'codigo' => '05172',
                        'tipo' => 'municipio',
                        'orden' => 36,
                    ],
                    [
                        'nombre' => 'Cisneros',
                        'codigo' => '05190',
                        'tipo' => 'municipio',
                        'orden' => 37,
                    ],
                    [
                        'nombre' => 'Cocorná',
                        'codigo' => '05197',
                        'tipo' => 'municipio',
                        'orden' => 38,
                    ],
                    [
                        'nombre' => 'Concepción',
                        'codigo' => '05206',
                        'tipo' => 'municipio',
                        'orden' => 39,
                    ],
                    [
                        'nombre' => 'Concordia',
                        'codigo' => '05209',
                        'tipo' => 'municipio',
                        'orden' => 40,
                    ],
                    [
                        'nombre' => 'Copacabana',
                        'codigo' => '05212',
                        'tipo' => 'municipio',
                        'orden' => 41,
                    ],
                    [
                        'nombre' => 'Dabeiba',
                        'codigo' => '05234',
                        'tipo' => 'municipio',
                        'orden' => 42,
                    ],
                    [
                        'nombre' => 'Donmatías',
                        'codigo' => '05237',
                        'tipo' => 'municipio',
                        'orden' => 43,
                    ],
                    [
                        'nombre' => 'Ebéjico',
                        'codigo' => '05240',
                        'tipo' => 'municipio',
                        'orden' => 44,
                    ],
                    [
                        'nombre' => 'El Bagre',
                        'codigo' => '05250',
                        'tipo' => 'municipio',
                        'orden' => 45,
                    ],
                    [
                        'nombre' => 'Entrerríos',
                        'codigo' => '05264',
                        'tipo' => 'municipio',
                        'orden' => 46,
                    ],
                    [
                        'nombre' => 'Envigado',
                        'codigo' => '05266',
                        'tipo' => 'municipio',
                        'orden' => 47,
                    ],
                    [
                        'nombre' => 'Fredonia',
                        'codigo' => '05282',
                        'tipo' => 'municipio',
                        'orden' => 48,
                    ],
                    [
                        'nombre' => 'Frontino',
                        'codigo' => '05284',
                        'tipo' => 'municipio',
                        'orden' => 49,
                    ],
                    [
                        'nombre' => 'Giraldo',
                        'codigo' => '05306',
                        'tipo' => 'municipio',
                        'orden' => 50,
                    ],
                    [
                        'nombre' => 'Girardota',
                        'codigo' => '05308',
                        'tipo' => 'municipio',
                        'orden' => 51,
                    ],
                    [
                        'nombre' => 'Gómez Plata',
                        'codigo' => '05310',
                        'tipo' => 'municipio',
                        'orden' => 52,
                    ],
                    [
                        'nombre' => 'Granada',
                        'codigo' => '05313',
                        'tipo' => 'municipio',
                        'orden' => 53,
                    ],
                    [
                        'nombre' => 'Guadalupe',
                        'codigo' => '05315',
                        'tipo' => 'municipio',
                        'orden' => 54,
                    ],
                    [
                        'nombre' => 'Guarne',
                        'codigo' => '05318',
                        'tipo' => 'municipio',
                        'orden' => 55,
                    ],
                    [
                        'nombre' => 'Guatapé',
                        'codigo' => '05321',
                        'tipo' => 'municipio',
                        'orden' => 56,
                    ],
                    [
                        'nombre' => 'Heliconia',
                        'codigo' => '05347',
                        'tipo' => 'municipio',
                        'orden' => 57,
                    ],
                    [
                        'nombre' => 'Hispania',
                        'codigo' => '05353',
                        'tipo' => 'municipio',
                        'orden' => 58,
                    ],
                    [
                        'nombre' => 'Itagüí',
                        'codigo' => '05360',
                        'tipo' => 'municipio',
                        'orden' => 59,
                    ],
                    [
                        'nombre' => 'Ituango',
                        'codigo' => '05361',
                        'tipo' => 'municipio',
                        'orden' => 60,
                    ],
                    [
                        'nombre' => 'Jardín',
                        'codigo' => '05364',
                        'tipo' => 'municipio',
                        'orden' => 61,
                    ],
                    [
                        'nombre' => 'Jericó',
                        'codigo' => '05368',
                        'tipo' => 'municipio',
                        'orden' => 62,
                    ],
                    [
                        'nombre' => 'La Ceja',
                        'codigo' => '05376',
                        'tipo' => 'municipio',
                        'orden' => 63,
                    ],
                    [
                        'nombre' => 'La Estrella',
                        'codigo' => '05380',
                        'tipo' => 'municipio',
                        'orden' => 64,
                    ],
                    [
                        'nombre' => 'La Pintada',
                        'codigo' => '05390',
                        'tipo' => 'municipio',
                        'orden' => 65,
                    ],
                    [
                        'nombre' => 'La Unión',
                        'codigo' => '05400',
                        'tipo' => 'municipio',
                        'orden' => 66,
                    ],
                    [
                        'nombre' => 'Liborina',
                        'codigo' => '05411',
                        'tipo' => 'municipio',
                        'orden' => 67,
                    ],
                    [
                        'nombre' => 'Maceo',
                        'codigo' => '05425',
                        'tipo' => 'municipio',
                        'orden' => 68,
                    ],
                    [
                        'nombre' => 'Marinilla',
                        'codigo' => '05440',
                        'tipo' => 'municipio',
                        'orden' => 69,
                    ],
                    [
                        'nombre' => 'Montebello',
                        'codigo' => '05467',
                        'tipo' => 'municipio',
                        'orden' => 70,
                    ],
                    [
                        'nombre' => 'Murindó',
                        'codigo' => '05475',
                        'tipo' => 'municipio',
                        'orden' => 71,
                    ],
                    [
                        'nombre' => 'Mutatá',
                        'codigo' => '05480',
                        'tipo' => 'municipio',
                        'orden' => 72,
                    ],
                    [
                        'nombre' => 'Nariño',
                        'codigo' => '05483',
                        'tipo' => 'municipio',
                        'orden' => 73,
                    ],
                    [
                        'nombre' => 'Necoclí',
                        'codigo' => '05490',
                        'tipo' => 'municipio',
                        'orden' => 74,
                    ],
                    [
                        'nombre' => 'Nechí',
                        'codigo' => '05495',
                        'tipo' => 'municipio',
                        'orden' => 75,
                    ],
                    [
                        'nombre' => 'Olaya',
                        'codigo' => '05501',
                        'tipo' => 'municipio',
                        'orden' => 76,
                    ],
                    [
                        'nombre' => 'Peñol',
                        'codigo' => '05541',
                        'tipo' => 'municipio',
                        'orden' => 77,
                    ],
                    [
                        'nombre' => 'Peque',
                        'codigo' => '05543',
                        'tipo' => 'municipio',
                        'orden' => 78,
                    ],
                    [
                        'nombre' => 'Pueblorrico',
                        'codigo' => '05576',
                        'tipo' => 'municipio',
                        'orden' => 79,
                    ],
                    [
                        'nombre' => 'Puerto Berrío',
                        'codigo' => '05579',
                        'tipo' => 'municipio',
                        'orden' => 80,
                    ],
                    [
                        'nombre' => 'Puerto Nare',
                        'codigo' => '05585',
                        'tipo' => 'municipio',
                        'orden' => 81,
                    ],
                    [
                        'nombre' => 'Puerto Triunfo',
                        'codigo' => '05591',
                        'tipo' => 'municipio',
                        'orden' => 82,
                    ],
                    [
                        'nombre' => 'Remedios',
                        'codigo' => '05604',
                        'tipo' => 'municipio',
                        'orden' => 83,
                    ],
                    [
                        'nombre' => 'Retiro',
                        'codigo' => '05607',
                        'tipo' => 'municipio',
                        'orden' => 84,
                    ],
                    [
                        'nombre' => 'Rionegro',
                        'codigo' => '05615',
                        'tipo' => 'municipio',
                        'orden' => 85,
                    ],
                    [
                        'nombre' => 'Sabanalarga',
                        'codigo' => '05628',
                        'tipo' => 'municipio',
                        'orden' => 86,
                    ],
                    [
                        'nombre' => 'Sabaneta',
                        'codigo' => '05631',
                        'tipo' => 'municipio',
                        'orden' => 87,
                    ],
                    [
                        'nombre' => 'Salgar',
                        'codigo' => '05642',
                        'tipo' => 'municipio',
                        'orden' => 88,
                    ],
                    [
                        'nombre' => 'San Andrés de Cuerquía',
                        'codigo' => '05647',
                        'tipo' => 'municipio',
                        'orden' => 89,
                    ],
                    [
                        'nombre' => 'San Carlos',
                        'codigo' => '05649',
                        'tipo' => 'municipio',
                        'orden' => 90,
                    ],
                    [
                        'nombre' => 'San Francisco',
                        'codigo' => '05652',
                        'tipo' => 'municipio',
                        'orden' => 91,
                    ],
                    [
                        'nombre' => 'San Jerónimo',
                        'codigo' => '05656',
                        'tipo' => 'municipio',
                        'orden' => 92,
                    ],
                    [
                        'nombre' => 'San José de la Montaña',
                        'codigo' => '05658',
                        'tipo' => 'municipio',
                        'orden' => 93,
                    ],
                    [
                        'nombre' => 'San Juan de Urabá',
                        'codigo' => '05659',
                        'tipo' => 'municipio',
                        'orden' => 94,
                    ],
                    [
                        'nombre' => 'San Luis',
                        'codigo' => '05660',
                        'tipo' => 'municipio',
                        'orden' => 95,
                    ],
                    [
                        'nombre' => 'San Pedro de los Milagros',
                        'codigo' => '05664',
                        'tipo' => 'municipio',
                        'orden' => 96,
                    ],
                    [
                        'nombre' => 'San Pedro de Urabá',
                        'codigo' => '05665',
                        'tipo' => 'municipio',
                        'orden' => 97,
                    ],
                    [
                        'nombre' => 'San Rafael',
                        'codigo' => '05667',
                        'tipo' => 'municipio',
                        'orden' => 98,
                    ],
                    [
                        'nombre' => 'San Roque',
                        'codigo' => '05670',
                        'tipo' => 'municipio',
                        'orden' => 99,
                    ],
                    [
                        'nombre' => 'San Vicente Ferrer',
                        'codigo' => '05674',
                        'tipo' => 'municipio',
                        'orden' => 100,
                    ],
                    [
                        'nombre' => 'Santa Bárbara',
                        'codigo' => '05679',
                        'tipo' => 'municipio',
                        'orden' => 101,
                    ],
                    [
                        'nombre' => 'Santa Rosa de Osos',
                        'codigo' => '05686',
                        'tipo' => 'municipio',
                        'orden' => 102,
                    ],
                    [
                        'nombre' => 'Santo Domingo',
                        'codigo' => '05690',
                        'tipo' => 'municipio',
                        'orden' => 103,
                    ],
                    [
                        'nombre' => 'El Santuario',
                        'codigo' => '05697',
                        'tipo' => 'municipio',
                        'orden' => 104,
                    ],
                    [
                        'nombre' => 'Segovia',
                        'codigo' => '05736',
                        'tipo' => 'municipio',
                        'orden' => 105,
                    ],
                    [
                        'nombre' => 'Sonsón',
                        'codigo' => '05756',
                        'tipo' => 'municipio',
                        'orden' => 106,
                    ],
                    [
                        'nombre' => 'Sopetrán',
                        'codigo' => '05761',
                        'tipo' => 'municipio',
                        'orden' => 107,
                    ],
                    [
                        'nombre' => 'Támesis',
                        'codigo' => '05789',
                        'tipo' => 'municipio',
                        'orden' => 108,
                    ],
                    [
                        'nombre' => 'Tarazá',
                        'codigo' => '05790',
                        'tipo' => 'municipio',
                        'orden' => 109,
                    ],
                    [
                        'nombre' => 'Tarso',
                        'codigo' => '05792',
                        'tipo' => 'municipio',
                        'orden' => 110,
                    ],
                    [
                        'nombre' => 'Titiribí',
                        'codigo' => '05809',
                        'tipo' => 'municipio',
                        'orden' => 111,
                    ],
                    [
                        'nombre' => 'Toledo',
                        'codigo' => '05819',
                        'tipo' => 'municipio',
                        'orden' => 112,
                    ],
                    [
                        'nombre' => 'Turbo',
                        'codigo' => '05837',
                        'tipo' => 'municipio',
                        'orden' => 113,
                    ],
                    [
                        'nombre' => 'Uramita',
                        'codigo' => '05842',
                        'tipo' => 'municipio',
                        'orden' => 114,
                    ],
                    [
                        'nombre' => 'Urrao',
                        'codigo' => '05847',
                        'tipo' => 'municipio',
                        'orden' => 115,
                    ],
                    [
                        'nombre' => 'Valdivia',
                        'codigo' => '05854',
                        'tipo' => 'municipio',
                        'orden' => 116,
                    ],
                    [
                        'nombre' => 'Valparaíso',
                        'codigo' => '05856',
                        'tipo' => 'municipio',
                        'orden' => 117,
                    ],
                    [
                        'nombre' => 'Vegachí',
                        'codigo' => '05858',
                        'tipo' => 'municipio',
                        'orden' => 118,
                    ],
                    [
                        'nombre' => 'Venecia',
                        'codigo' => '05861',
                        'tipo' => 'municipio',
                        'orden' => 119,
                    ],
                    [
                        'nombre' => 'Vigía del Fuerte',
                        'codigo' => '05873',
                        'tipo' => 'municipio',
                        'orden' => 120,
                    ],
                    [
                        'nombre' => 'Yalí',
                        'codigo' => '05885',
                        'tipo' => 'municipio',
                        'orden' => 121,
                    ],
                    [
                        'nombre' => 'Yarumal',
                        'codigo' => '05887',
                        'tipo' => 'municipio',
                        'orden' => 122,
                    ],
                    [
                        'nombre' => 'Yolombó',
                        'codigo' => '05890',
                        'tipo' => 'municipio',
                        'orden' => 123,
                    ],
                    [
                        'nombre' => 'Yondó',
                        'codigo' => '05893',
                        'tipo' => 'municipio',
                        'orden' => 124,
                    ],
                    [
                        'nombre' => 'Zaragoza',
                        'codigo' => '05895',
                        'tipo' => 'municipio',
                        'orden' => 125,
                    ],
                ],
            ],
            [
                'nombre' => 'Arauca',
                'codigo' => '81',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 3,

                'ciudades' => [
                    [
                        'nombre' => 'Arauca',
                        'codigo' => '81001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Arauquita',
                        'codigo' => '81065',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Cravo Norte',
                        'codigo' => '81220',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Fortul',
                        'codigo' => '81300',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Puerto Rondón',
                        'codigo' => '81591',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Saravena',
                        'codigo' => '81736',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Tame',
                        'codigo' => '81794',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                ],
            ],
            [
                'nombre' => 'Atlántico',
                'codigo' => '08',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 4,

                'ciudades' => [
                    [
                        'nombre' => 'Barranquilla',
                        'codigo' => '08001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Baranoa',
                        'codigo' => '08078',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Campo de la Cruz',
                        'codigo' => '08137',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Candelaria',
                        'codigo' => '08141',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Galapa',
                        'codigo' => '08296',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Juan de Acosta',
                        'codigo' => '08372',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Luruaco',
                        'codigo' => '08421',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Malambo',
                        'codigo' => '08433',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Manatí',
                        'codigo' => '08436',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Palmar de Varela',
                        'codigo' => '08520',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Piojó',
                        'codigo' => '08549',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Polonuevo',
                        'codigo' => '08558',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Ponedera',
                        'codigo' => '08560',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Puerto Colombia',
                        'codigo' => '08573',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Repelón',
                        'codigo' => '08606',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Sabanagrande',
                        'codigo' => '08634',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Sabanalarga',
                        'codigo' => '08638',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Santa Lucía',
                        'codigo' => '08675',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Santo Tomás',
                        'codigo' => '08685',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Soledad',
                        'codigo' => '08758',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Suan',
                        'codigo' => '08770',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Tubará',
                        'codigo' => '08832',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'Usiacurí',
                        'codigo' => '08849',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                ],
            ],
            [
                'nombre' => 'Bolívar',
                'codigo' => '13',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 5,

                'ciudades' => [
                    [
                        'nombre' => 'Cartagena de Indias',
                        'codigo' => '13001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Achí',
                        'codigo' => '13006',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Altos del Rosario',
                        'codigo' => '13030',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Arenal',
                        'codigo' => '13042',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Arjona',
                        'codigo' => '13052',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Arroyohondo',
                        'codigo' => '13062',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Barranco de Loba',
                        'codigo' => '13074',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Calamar',
                        'codigo' => '13140',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Cantagallo',
                        'codigo' => '13160',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Cicuco',
                        'codigo' => '13188',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Córdoba',
                        'codigo' => '13212',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Clemencia',
                        'codigo' => '13222',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'El Carmen de Bolívar',
                        'codigo' => '13244',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'El Guamo',
                        'codigo' => '13248',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'El Peñón',
                        'codigo' => '13268',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Hatillo de Loba',
                        'codigo' => '13300',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Magangué',
                        'codigo' => '13430',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Mahates',
                        'codigo' => '13433',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Margarita',
                        'codigo' => '13440',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'María La Baja',
                        'codigo' => '13442',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Montecristo',
                        'codigo' => '13458',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Santa Cruz de Mompox',
                        'codigo' => '13468',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'Morales',
                        'codigo' => '13473',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'Norosí',
                        'codigo' => '13490',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Pinillos',
                        'codigo' => '13549',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'Regidor',
                        'codigo' => '13580',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'Río Viejo',
                        'codigo' => '13600',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'San Cristóbal',
                        'codigo' => '13620',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'San Estanislao',
                        'codigo' => '13647',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'San Fernando',
                        'codigo' => '13650',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                    [
                        'nombre' => 'San Jacinto',
                        'codigo' => '13654',
                        'tipo' => 'municipio',
                        'orden' => 31,
                    ],
                    [
                        'nombre' => 'San Jacinto del Cauca',
                        'codigo' => '13655',
                        'tipo' => 'municipio',
                        'orden' => 32,
                    ],
                    [
                        'nombre' => 'San Juan Nepomuceno',
                        'codigo' => '13657',
                        'tipo' => 'municipio',
                        'orden' => 33,
                    ],
                    [
                        'nombre' => 'San Martín de Loba',
                        'codigo' => '13667',
                        'tipo' => 'municipio',
                        'orden' => 34,
                    ],
                    [
                        'nombre' => 'San Pablo',
                        'codigo' => '13670',
                        'tipo' => 'municipio',
                        'orden' => 35,
                    ],
                    [
                        'nombre' => 'Santa Catalina',
                        'codigo' => '13673',
                        'tipo' => 'municipio',
                        'orden' => 36,
                    ],
                    [
                        'nombre' => 'Santa Rosa',
                        'codigo' => '13683',
                        'tipo' => 'municipio',
                        'orden' => 37,
                    ],
                    [
                        'nombre' => 'Santa Rosa del Sur',
                        'codigo' => '13688',
                        'tipo' => 'municipio',
                        'orden' => 38,
                    ],
                    [
                        'nombre' => 'Simití',
                        'codigo' => '13744',
                        'tipo' => 'municipio',
                        'orden' => 39,
                    ],
                    [
                        'nombre' => 'Soplaviento',
                        'codigo' => '13760',
                        'tipo' => 'municipio',
                        'orden' => 40,
                    ],
                    [
                        'nombre' => 'Talaigua Nuevo',
                        'codigo' => '13780',
                        'tipo' => 'municipio',
                        'orden' => 41,
                    ],
                    [
                        'nombre' => 'Tiquisio',
                        'codigo' => '13810',
                        'tipo' => 'municipio',
                        'orden' => 42,
                    ],
                    [
                        'nombre' => 'Turbaco',
                        'codigo' => '13836',
                        'tipo' => 'municipio',
                        'orden' => 43,
                    ],
                    [
                        'nombre' => 'Turbaná',
                        'codigo' => '13838',
                        'tipo' => 'municipio',
                        'orden' => 44,
                    ],
                    [
                        'nombre' => 'Villanueva',
                        'codigo' => '13873',
                        'tipo' => 'municipio',
                        'orden' => 45,
                    ],
                    [
                        'nombre' => 'Zambrano',
                        'codigo' => '13894',
                        'tipo' => 'municipio',
                        'orden' => 46,
                    ],
                ],
            ],
            [
                'nombre' => 'Boyacá',
                'codigo' => '15',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 6,

                'ciudades' => [
                    [
                        'nombre' => 'Tunja',
                        'codigo' => '15001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Almeida',
                        'codigo' => '15022',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Aquitania',
                        'codigo' => '15047',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Arcabuco',
                        'codigo' => '15051',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Belén',
                        'codigo' => '15087',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Berbeo',
                        'codigo' => '15090',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Betéitiva',
                        'codigo' => '15092',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Boavita',
                        'codigo' => '15097',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Boyacá',
                        'codigo' => '15104',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Briceño',
                        'codigo' => '15106',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Buenavista',
                        'codigo' => '15109',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Busbanzá',
                        'codigo' => '15114',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Caldas',
                        'codigo' => '15131',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Campohermoso',
                        'codigo' => '15135',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Cerinza',
                        'codigo' => '15162',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Chinavita',
                        'codigo' => '15172',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Chiquinquirá',
                        'codigo' => '15176',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Chiscas',
                        'codigo' => '15180',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Chita',
                        'codigo' => '15183',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Chitaraque',
                        'codigo' => '15185',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Chivatá',
                        'codigo' => '15187',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Ciénega',
                        'codigo' => '15189',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'Cómbita',
                        'codigo' => '15204',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'Coper',
                        'codigo' => '15212',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Corrales',
                        'codigo' => '15215',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'Covarachía',
                        'codigo' => '15218',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'Cubará',
                        'codigo' => '15223',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'Cucaita',
                        'codigo' => '15224',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'Cuítiva',
                        'codigo' => '15226',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'Chíquiza',
                        'codigo' => '15232',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                    [
                        'nombre' => 'Chivor',
                        'codigo' => '15236',
                        'tipo' => 'municipio',
                        'orden' => 31,
                    ],
                    [
                        'nombre' => 'Duitama',
                        'codigo' => '15238',
                        'tipo' => 'municipio',
                        'orden' => 32,
                    ],
                    [
                        'nombre' => 'El Cocuy',
                        'codigo' => '15244',
                        'tipo' => 'municipio',
                        'orden' => 33,
                    ],
                    [
                        'nombre' => 'El Espino',
                        'codigo' => '15248',
                        'tipo' => 'municipio',
                        'orden' => 34,
                    ],
                    [
                        'nombre' => 'Firavitoba',
                        'codigo' => '15272',
                        'tipo' => 'municipio',
                        'orden' => 35,
                    ],
                    [
                        'nombre' => 'Floresta',
                        'codigo' => '15276',
                        'tipo' => 'municipio',
                        'orden' => 36,
                    ],
                    [
                        'nombre' => 'Gachantivá',
                        'codigo' => '15293',
                        'tipo' => 'municipio',
                        'orden' => 37,
                    ],
                    [
                        'nombre' => 'Gámeza',
                        'codigo' => '15296',
                        'tipo' => 'municipio',
                        'orden' => 38,
                    ],
                    [
                        'nombre' => 'Garagoa',
                        'codigo' => '15299',
                        'tipo' => 'municipio',
                        'orden' => 39,
                    ],
                    [
                        'nombre' => 'Guacamayas',
                        'codigo' => '15317',
                        'tipo' => 'municipio',
                        'orden' => 40,
                    ],
                    [
                        'nombre' => 'Guateque',
                        'codigo' => '15322',
                        'tipo' => 'municipio',
                        'orden' => 41,
                    ],
                    [
                        'nombre' => 'Guayatá',
                        'codigo' => '15325',
                        'tipo' => 'municipio',
                        'orden' => 42,
                    ],
                    [
                        'nombre' => 'Güicán de la Sierra',
                        'codigo' => '15332',
                        'tipo' => 'municipio',
                        'orden' => 43,
                    ],
                    [
                        'nombre' => 'Iza',
                        'codigo' => '15362',
                        'tipo' => 'municipio',
                        'orden' => 44,
                    ],
                    [
                        'nombre' => 'Jenesano',
                        'codigo' => '15367',
                        'tipo' => 'municipio',
                        'orden' => 45,
                    ],
                    [
                        'nombre' => 'Jericó',
                        'codigo' => '15368',
                        'tipo' => 'municipio',
                        'orden' => 46,
                    ],
                    [
                        'nombre' => 'Labranzagrande',
                        'codigo' => '15377',
                        'tipo' => 'municipio',
                        'orden' => 47,
                    ],
                    [
                        'nombre' => 'La Capilla',
                        'codigo' => '15380',
                        'tipo' => 'municipio',
                        'orden' => 48,
                    ],
                    [
                        'nombre' => 'La Victoria',
                        'codigo' => '15401',
                        'tipo' => 'municipio',
                        'orden' => 49,
                    ],
                    [
                        'nombre' => 'La Uvita',
                        'codigo' => '15403',
                        'tipo' => 'municipio',
                        'orden' => 50,
                    ],
                    [
                        'nombre' => 'Villa de Leyva',
                        'codigo' => '15407',
                        'tipo' => 'municipio',
                        'orden' => 51,
                    ],
                    [
                        'nombre' => 'Macanal',
                        'codigo' => '15425',
                        'tipo' => 'municipio',
                        'orden' => 52,
                    ],
                    [
                        'nombre' => 'Maripí',
                        'codigo' => '15442',
                        'tipo' => 'municipio',
                        'orden' => 53,
                    ],
                    [
                        'nombre' => 'Miraflores',
                        'codigo' => '15455',
                        'tipo' => 'municipio',
                        'orden' => 54,
                    ],
                    [
                        'nombre' => 'Mongua',
                        'codigo' => '15464',
                        'tipo' => 'municipio',
                        'orden' => 55,
                    ],
                    [
                        'nombre' => 'Monguí',
                        'codigo' => '15466',
                        'tipo' => 'municipio',
                        'orden' => 56,
                    ],
                    [
                        'nombre' => 'Moniquirá',
                        'codigo' => '15469',
                        'tipo' => 'municipio',
                        'orden' => 57,
                    ],
                    [
                        'nombre' => 'Motavita',
                        'codigo' => '15476',
                        'tipo' => 'municipio',
                        'orden' => 58,
                    ],
                    [
                        'nombre' => 'Muzo',
                        'codigo' => '15480',
                        'tipo' => 'municipio',
                        'orden' => 59,
                    ],
                    [
                        'nombre' => 'Nobsa',
                        'codigo' => '15491',
                        'tipo' => 'municipio',
                        'orden' => 60,
                    ],
                    [
                        'nombre' => 'Nuevo Colón',
                        'codigo' => '15494',
                        'tipo' => 'municipio',
                        'orden' => 61,
                    ],
                    [
                        'nombre' => 'Oicatá',
                        'codigo' => '15500',
                        'tipo' => 'municipio',
                        'orden' => 62,
                    ],
                    [
                        'nombre' => 'Otanche',
                        'codigo' => '15507',
                        'tipo' => 'municipio',
                        'orden' => 63,
                    ],
                    [
                        'nombre' => 'Pachavita',
                        'codigo' => '15511',
                        'tipo' => 'municipio',
                        'orden' => 64,
                    ],
                    [
                        'nombre' => 'Páez',
                        'codigo' => '15514',
                        'tipo' => 'municipio',
                        'orden' => 65,
                    ],
                    [
                        'nombre' => 'Paipa',
                        'codigo' => '15516',
                        'tipo' => 'municipio',
                        'orden' => 66,
                    ],
                    [
                        'nombre' => 'Pajarito',
                        'codigo' => '15518',
                        'tipo' => 'municipio',
                        'orden' => 67,
                    ],
                    [
                        'nombre' => 'Panqueba',
                        'codigo' => '15522',
                        'tipo' => 'municipio',
                        'orden' => 68,
                    ],
                    [
                        'nombre' => 'Pauna',
                        'codigo' => '15531',
                        'tipo' => 'municipio',
                        'orden' => 69,
                    ],
                    [
                        'nombre' => 'Paya',
                        'codigo' => '15533',
                        'tipo' => 'municipio',
                        'orden' => 70,
                    ],
                    [
                        'nombre' => 'Paz de Río',
                        'codigo' => '15537',
                        'tipo' => 'municipio',
                        'orden' => 71,
                    ],
                    [
                        'nombre' => 'Pesca',
                        'codigo' => '15542',
                        'tipo' => 'municipio',
                        'orden' => 72,
                    ],
                    [
                        'nombre' => 'Pisba',
                        'codigo' => '15550',
                        'tipo' => 'municipio',
                        'orden' => 73,
                    ],
                    [
                        'nombre' => 'Puerto Boyacá',
                        'codigo' => '15572',
                        'tipo' => 'municipio',
                        'orden' => 74,
                    ],
                    [
                        'nombre' => 'Quípama',
                        'codigo' => '15580',
                        'tipo' => 'municipio',
                        'orden' => 75,
                    ],
                    [
                        'nombre' => 'Ramiriquí',
                        'codigo' => '15599',
                        'tipo' => 'municipio',
                        'orden' => 76,
                    ],
                    [
                        'nombre' => 'Ráquira',
                        'codigo' => '15600',
                        'tipo' => 'municipio',
                        'orden' => 77,
                    ],
                    [
                        'nombre' => 'Rondón',
                        'codigo' => '15621',
                        'tipo' => 'municipio',
                        'orden' => 78,
                    ],
                    [
                        'nombre' => 'Saboyá',
                        'codigo' => '15632',
                        'tipo' => 'municipio',
                        'orden' => 79,
                    ],
                    [
                        'nombre' => 'Sáchica',
                        'codigo' => '15638',
                        'tipo' => 'municipio',
                        'orden' => 80,
                    ],
                    [
                        'nombre' => 'Samacá',
                        'codigo' => '15646',
                        'tipo' => 'municipio',
                        'orden' => 81,
                    ],
                    [
                        'nombre' => 'San Eduardo',
                        'codigo' => '15660',
                        'tipo' => 'municipio',
                        'orden' => 82,
                    ],
                    [
                        'nombre' => 'San José de Pare',
                        'codigo' => '15664',
                        'tipo' => 'municipio',
                        'orden' => 83,
                    ],
                    [
                        'nombre' => 'San Luis de Gaceno',
                        'codigo' => '15667',
                        'tipo' => 'municipio',
                        'orden' => 84,
                    ],
                    [
                        'nombre' => 'San Mateo',
                        'codigo' => '15673',
                        'tipo' => 'municipio',
                        'orden' => 85,
                    ],
                    [
                        'nombre' => 'San Miguel de Sema',
                        'codigo' => '15676',
                        'tipo' => 'municipio',
                        'orden' => 86,
                    ],
                    [
                        'nombre' => 'San Pablo de Borbur',
                        'codigo' => '15681',
                        'tipo' => 'municipio',
                        'orden' => 87,
                    ],
                    [
                        'nombre' => 'Santa Rosa de Viterbo',
                        'codigo' => '15693',
                        'tipo' => 'municipio',
                        'orden' => 88,
                    ],
                    [
                        'nombre' => 'Santa Sofía',
                        'codigo' => '15696',
                        'tipo' => 'municipio',
                        'orden' => 89,
                    ],
                    [
                        'nombre' => 'Santana',
                        'codigo' => '15686',
                        'tipo' => 'municipio',
                        'orden' => 90,
                    ],
                    [
                        'nombre' => 'Santa María',
                        'codigo' => '15690',
                        'tipo' => 'municipio',
                        'orden' => 91,
                    ],
                    [
                        'nombre' => 'Sativanorte',
                        'codigo' => '15720',
                        'tipo' => 'municipio',
                        'orden' => 92,
                    ],
                    [
                        'nombre' => 'Sativasur',
                        'codigo' => '15723',
                        'tipo' => 'municipio',
                        'orden' => 93,
                    ],
                    [
                        'nombre' => 'Siachoque',
                        'codigo' => '15740',
                        'tipo' => 'municipio',
                        'orden' => 94,
                    ],
                    [
                        'nombre' => 'Soatá',
                        'codigo' => '15753',
                        'tipo' => 'municipio',
                        'orden' => 95,
                    ],
                    [
                        'nombre' => 'Socotá',
                        'codigo' => '15755',
                        'tipo' => 'municipio',
                        'orden' => 96,
                    ],
                    [
                        'nombre' => 'Socha',
                        'codigo' => '15757',
                        'tipo' => 'municipio',
                        'orden' => 97,
                    ],
                    [
                        'nombre' => 'Sogamoso',
                        'codigo' => '15759',
                        'tipo' => 'municipio',
                        'orden' => 98,
                    ],
                    [
                        'nombre' => 'Somondoco',
                        'codigo' => '15761',
                        'tipo' => 'municipio',
                        'orden' => 99,
                    ],
                    [
                        'nombre' => 'Sora',
                        'codigo' => '15762',
                        'tipo' => 'municipio',
                        'orden' => 100,
                    ],
                    [
                        'nombre' => 'Sotaquirá',
                        'codigo' => '15763',
                        'tipo' => 'municipio',
                        'orden' => 101,
                    ],
                    [
                        'nombre' => 'Soracá',
                        'codigo' => '15764',
                        'tipo' => 'municipio',
                        'orden' => 102,
                    ],
                    [
                        'nombre' => 'Susacón',
                        'codigo' => '15774',
                        'tipo' => 'municipio',
                        'orden' => 103,
                    ],
                    [
                        'nombre' => 'Sutamarchán',
                        'codigo' => '15776',
                        'tipo' => 'municipio',
                        'orden' => 104,
                    ],
                    [
                        'nombre' => 'Sutatenza',
                        'codigo' => '15778',
                        'tipo' => 'municipio',
                        'orden' => 105,
                    ],
                    [
                        'nombre' => 'Tasco',
                        'codigo' => '15790',
                        'tipo' => 'municipio',
                        'orden' => 106,
                    ],
                    [
                        'nombre' => 'Tenza',
                        'codigo' => '15798',
                        'tipo' => 'municipio',
                        'orden' => 107,
                    ],
                    [
                        'nombre' => 'Tibaná',
                        'codigo' => '15804',
                        'tipo' => 'municipio',
                        'orden' => 108,
                    ],
                    [
                        'nombre' => 'Tibasosa',
                        'codigo' => '15806',
                        'tipo' => 'municipio',
                        'orden' => 109,
                    ],
                    [
                        'nombre' => 'Tinjacá',
                        'codigo' => '15808',
                        'tipo' => 'municipio',
                        'orden' => 110,
                    ],
                    [
                        'nombre' => 'Tipacoque',
                        'codigo' => '15810',
                        'tipo' => 'municipio',
                        'orden' => 111,
                    ],
                    [
                        'nombre' => 'Toca',
                        'codigo' => '15814',
                        'tipo' => 'municipio',
                        'orden' => 112,
                    ],
                    [
                        'nombre' => 'Togüí',
                        'codigo' => '15816',
                        'tipo' => 'municipio',
                        'orden' => 113,
                    ],
                    [
                        'nombre' => 'Tópaga',
                        'codigo' => '15820',
                        'tipo' => 'municipio',
                        'orden' => 114,
                    ],
                    [
                        'nombre' => 'Tota',
                        'codigo' => '15822',
                        'tipo' => 'municipio',
                        'orden' => 115,
                    ],
                    [
                        'nombre' => 'Tununguá',
                        'codigo' => '15832',
                        'tipo' => 'municipio',
                        'orden' => 116,
                    ],
                    [
                        'nombre' => 'Turmequé',
                        'codigo' => '15835',
                        'tipo' => 'municipio',
                        'orden' => 117,
                    ],
                    [
                        'nombre' => 'Tuta',
                        'codigo' => '15837',
                        'tipo' => 'municipio',
                        'orden' => 118,
                    ],
                    [
                        'nombre' => 'Tutazá',
                        'codigo' => '15839',
                        'tipo' => 'municipio',
                        'orden' => 119,
                    ],
                    [
                        'nombre' => 'Úmbita',
                        'codigo' => '15842',
                        'tipo' => 'municipio',
                        'orden' => 120,
                    ],
                    [
                        'nombre' => 'Ventaquemada',
                        'codigo' => '15861',
                        'tipo' => 'municipio',
                        'orden' => 121,
                    ],
                    [
                        'nombre' => 'Viracachá',
                        'codigo' => '15879',
                        'tipo' => 'municipio',
                        'orden' => 122,
                    ],
                    [
                        'nombre' => 'Zetaquira',
                        'codigo' => '15897',
                        'tipo' => 'municipio',
                        'orden' => 123,
                    ],
                ],
            ],
            [
                'nombre' => 'Caldas',
                'codigo' => '17',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 7,

                'ciudades' => [
                    [
                        'nombre' => 'Manizales',
                        'codigo' => '17001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Aguadas',
                        'codigo' => '17013',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Anserma',
                        'codigo' => '17042',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Aranzazu',
                        'codigo' => '17050',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Belalcázar',
                        'codigo' => '17088',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Chinchiná',
                        'codigo' => '17174',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Filadelfia',
                        'codigo' => '17272',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'La Dorada',
                        'codigo' => '17380',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'La Merced',
                        'codigo' => '17388',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Manzanares',
                        'codigo' => '17433',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Marmato',
                        'codigo' => '17442',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Marquetalia',
                        'codigo' => '17444',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Marulanda',
                        'codigo' => '17446',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Neira',
                        'codigo' => '17486',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Norcasia',
                        'codigo' => '17495',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Pácora',
                        'codigo' => '17513',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Palestina',
                        'codigo' => '17524',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Pensilvania',
                        'codigo' => '17541',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Riosucio',
                        'codigo' => '17614',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Risaralda',
                        'codigo' => '17616',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Salamina',
                        'codigo' => '17653',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Samaná',
                        'codigo' => '17662',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'San José',
                        'codigo' => '17665',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'Supía',
                        'codigo' => '17777',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Victoria',
                        'codigo' => '17867',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'Villamaría',
                        'codigo' => '17873',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'Viterbo',
                        'codigo' => '17877',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                ],
            ],
            [
                'nombre' => 'Caquetá',
                'codigo' => '18',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 8,

                'ciudades' => [
                    [
                        'nombre' => 'Florencia',
                        'codigo' => '18001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Albania',
                        'codigo' => '18029',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Belén de los Andaquíes',
                        'codigo' => '18094',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Cartagena del Chairá',
                        'codigo' => '18150',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Curillo',
                        'codigo' => '18205',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'El Doncello',
                        'codigo' => '18247',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'El Paujíl',
                        'codigo' => '18256',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'La Montañita',
                        'codigo' => '18410',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Milán',
                        'codigo' => '18460',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Morelia',
                        'codigo' => '18479',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Puerto Rico',
                        'codigo' => '18592',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'San José del Fragua',
                        'codigo' => '18610',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'San Vicente del Caguán',
                        'codigo' => '18753',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Solano',
                        'codigo' => '18756',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Solita',
                        'codigo' => '18785',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Valparaíso',
                        'codigo' => '18860',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                ],
            ],
            [
                'nombre' => 'Casanare',
                'codigo' => '85',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 9,

                'ciudades' => [
                    [
                        'nombre' => 'Yopal',
                        'codigo' => '85001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Aguazul',
                        'codigo' => '85010',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Chámeza',
                        'codigo' => '85015',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Hato Corozal',
                        'codigo' => '85125',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'La Salina',
                        'codigo' => '85136',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Maní',
                        'codigo' => '85139',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Monterrey',
                        'codigo' => '85162',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Nunchía',
                        'codigo' => '85225',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Orocué',
                        'codigo' => '85230',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Paz de Ariporo',
                        'codigo' => '85250',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Pore',
                        'codigo' => '85263',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Recetor',
                        'codigo' => '85279',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Sabanalarga',
                        'codigo' => '85300',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Sácama',
                        'codigo' => '85315',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'San Luis de Palenque',
                        'codigo' => '85325',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Támara',
                        'codigo' => '85400',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Tauramena',
                        'codigo' => '85410',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Trinidad',
                        'codigo' => '85430',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Villanueva',
                        'codigo' => '85440',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                ],
            ],
            [
                'nombre' => 'Cauca',
                'codigo' => '19',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 10,

                'ciudades' => [
                    [
                        'nombre' => 'Popayán',
                        'codigo' => '19001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Almaguer',
                        'codigo' => '19022',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Argelia',
                        'codigo' => '19050',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Balboa',
                        'codigo' => '19075',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Bolívar',
                        'codigo' => '19100',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Buenos Aires',
                        'codigo' => '19110',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Cajibío',
                        'codigo' => '19130',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Caldono',
                        'codigo' => '19137',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Caloto',
                        'codigo' => '19142',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Corinto',
                        'codigo' => '19212',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'El Tambo',
                        'codigo' => '19256',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Florencia',
                        'codigo' => '19290',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Guachené',
                        'codigo' => '19300',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Guapi',
                        'codigo' => '19318',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Inzá',
                        'codigo' => '19355',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Jambaló',
                        'codigo' => '19364',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'La Sierra',
                        'codigo' => '19392',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'La Vega',
                        'codigo' => '19397',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'López de Micay',
                        'codigo' => '19418',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Mercaderes',
                        'codigo' => '19450',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Miranda',
                        'codigo' => '19455',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Morales',
                        'codigo' => '19473',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'Padilla',
                        'codigo' => '19513',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'Páez',
                        'codigo' => '19517',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Patía',
                        'codigo' => '19532',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'Piamonte',
                        'codigo' => '19533',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'Piendamó',
                        'codigo' => '19548',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'Puerto Tejada',
                        'codigo' => '19573',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'Puracé',
                        'codigo' => '19585',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'Rosas',
                        'codigo' => '19622',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                    [
                        'nombre' => 'San Sebastián',
                        'codigo' => '19693',
                        'tipo' => 'municipio',
                        'orden' => 31,
                    ],
                    [
                        'nombre' => 'Santa Rosa',
                        'codigo' => '19701',
                        'tipo' => 'municipio',
                        'orden' => 32,
                    ],
                    [
                        'nombre' => 'Silvia',
                        'codigo' => '19743',
                        'tipo' => 'municipio',
                        'orden' => 33,
                    ],
                    [
                        'nombre' => 'Sotará',
                        'codigo' => '19760',
                        'tipo' => 'municipio',
                        'orden' => 34,
                    ],
                    [
                        'nombre' => 'Suárez',
                        'codigo' => '19780',
                        'tipo' => 'municipio',
                        'orden' => 35,
                    ],
                    [
                        'nombre' => 'Sucre',
                        'codigo' => '19785',
                        'tipo' => 'municipio',
                        'orden' => 36,
                    ],
                    [
                        'nombre' => 'Timbío',
                        'codigo' => '19807',
                        'tipo' => 'municipio',
                        'orden' => 37,
                    ],
                    [
                        'nombre' => 'Timbiquí',
                        'codigo' => '19809',
                        'tipo' => 'municipio',
                        'orden' => 38,
                    ],
                    [
                        'nombre' => 'Toribío',
                        'codigo' => '19821',
                        'tipo' => 'municipio',
                        'orden' => 39,
                    ],
                    [
                        'nombre' => 'Totoró',
                        'codigo' => '19824',
                        'tipo' => 'municipio',
                        'orden' => 40,
                    ],
                    [
                        'nombre' => 'Villa Rica',
                        'codigo' => '19845',
                        'tipo' => 'municipio',
                        'orden' => 41,
                    ],
                ],
            ],
            [
                'nombre' => 'Cesar',
                'codigo' => '20',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 11,

                'ciudades' => [
                    [
                        'nombre' => 'Valledupar',
                        'codigo' => '20001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Aguachica',
                        'codigo' => '20011',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Agustín Codazzi',
                        'codigo' => '20013',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Astrea',
                        'codigo' => '20032',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Becerril',
                        'codigo' => '20045',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Bosconia',
                        'codigo' => '20060',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Chimichagua',
                        'codigo' => '20175',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Chiriguaná',
                        'codigo' => '20178',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Curumaní',
                        'codigo' => '20228',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'El Copey',
                        'codigo' => '20238',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'El Paso',
                        'codigo' => '20250',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Gamarra',
                        'codigo' => '20295',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'González',
                        'codigo' => '20310',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'La Gloria',
                        'codigo' => '20383',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'La Jagua de Ibirico',
                        'codigo' => '20400',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'La Paz',
                        'codigo' => '20621',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Manaure Balcón del Cesar',
                        'codigo' => '20443',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Pailitas',
                        'codigo' => '20517',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Pelaya',
                        'codigo' => '20550',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Pueblo Bello',
                        'codigo' => '20570',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Río de Oro',
                        'codigo' => '20614',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'San Alberto',
                        'codigo' => '20710',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'San Diego',
                        'codigo' => '20750',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'San Martín',
                        'codigo' => '20770',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Tamalameque',
                        'codigo' => '20787',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                ],
            ],
            [
                'nombre' => 'Chocó',
                'codigo' => '27',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 12,

                'ciudades' => [
                    [
                        'nombre' => 'Quibdó',
                        'codigo' => '27001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Acandí',
                        'codigo' => '27006',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Alto Baudó',
                        'codigo' => '27025',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Atrato',
                        'codigo' => '27050',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Bagadó',
                        'codigo' => '27073',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Bahía Solano',
                        'codigo' => '27075',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Bajo Baudó',
                        'codigo' => '27077',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Bojayá',
                        'codigo' => '27099',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'El Cantón del San Pablo',
                        'codigo' => '27135',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Carmen del Darién',
                        'codigo' => '27150',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Cértegui',
                        'codigo' => '27160',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Condoto',
                        'codigo' => '27205',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'El Carmen de Atrato',
                        'codigo' => '27245',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'El Litoral del San Juan',
                        'codigo' => '27250',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Istmina',
                        'codigo' => '27361',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Juradó',
                        'codigo' => '27372',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Lloró',
                        'codigo' => '27413',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Medio Atrato',
                        'codigo' => '27425',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Medio Baudó',
                        'codigo' => '27430',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Medio San Juan',
                        'codigo' => '27450',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Nóvita',
                        'codigo' => '27491',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Nuquí',
                        'codigo' => '27495',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'Río Iró',
                        'codigo' => '27580',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'Río Quito',
                        'codigo' => '27600',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Riosucio',
                        'codigo' => '27615',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'San José del Palmar',
                        'codigo' => '27660',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'Sipí',
                        'codigo' => '27745',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'Tadó',
                        'codigo' => '27787',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'Unguía',
                        'codigo' => '27800',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'Unión Panamericana',
                        'codigo' => '27810',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                ],
            ],
            [
                'nombre' => 'Córdoba',
                'codigo' => '23',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 13,

                'ciudades' => [
                    [
                        'nombre' => 'Montería',
                        'codigo' => '23001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Ayapel',
                        'codigo' => '23068',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Buenavista',
                        'codigo' => '23079',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Canalete',
                        'codigo' => '23090',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Cereté',
                        'codigo' => '23162',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Chimá',
                        'codigo' => '23168',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Chinú',
                        'codigo' => '23182',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Ciénaga de Oro',
                        'codigo' => '23189',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Cotorra',
                        'codigo' => '23300',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'La Apartada',
                        'codigo' => '23350',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Lorica',
                        'codigo' => '23417',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Los Córdobas',
                        'codigo' => '23419',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Momil',
                        'codigo' => '23464',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Montelíbano',
                        'codigo' => '23466',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Moñitos',
                        'codigo' => '23500',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Planeta Rica',
                        'codigo' => '23555',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Pueblo Nuevo',
                        'codigo' => '23570',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Puerto Escondido',
                        'codigo' => '23574',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Puerto Libertador',
                        'codigo' => '23580',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Purísima',
                        'codigo' => '23586',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Sahagún',
                        'codigo' => '23660',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'San Andrés de Sotavento',
                        'codigo' => '23670',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'San Antero',
                        'codigo' => '23672',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'San Bernardo del Viento',
                        'codigo' => '23675',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'San Carlos',
                        'codigo' => '23678',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'San José de Uré',
                        'codigo' => '23682',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'San Pelayo',
                        'codigo' => '23686',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'Tierralta',
                        'codigo' => '23807',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'Tuchín',
                        'codigo' => '23815',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'Valencia',
                        'codigo' => '23855',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                ],
            ],
            [
                'nombre' => 'Cundinamarca',
                'codigo' => '25',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 14,

                'ciudades' => [
                    [
                        'nombre' => 'Agua de Dios',
                        'codigo' => '25001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Albán',
                        'codigo' => '25019',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Anapoima',
                        'codigo' => '25035',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Anolaima',
                        'codigo' => '25040',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Arbeláez',
                        'codigo' => '25053',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Beltrán',
                        'codigo' => '25086',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Bituima',
                        'codigo' => '25095',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Bojacá',
                        'codigo' => '25099',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Cabrera',
                        'codigo' => '25120',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Cachipay',
                        'codigo' => '25123',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Cajicá',
                        'codigo' => '25126',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Caparrapí',
                        'codigo' => '25148',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Cáqueza',
                        'codigo' => '25151',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Chaguaní',
                        'codigo' => '25168',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Chía',
                        'codigo' => '25175',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Chipaque',
                        'codigo' => '25178',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Choachí',
                        'codigo' => '25181',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Chocontá',
                        'codigo' => '25183',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Cogua',
                        'codigo' => '25200',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Cota',
                        'codigo' => '25214',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Cucunubá',
                        'codigo' => '25224',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'El Colegio',
                        'codigo' => '25245',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'El Peñón',
                        'codigo' => '25258',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'El Rosal',
                        'codigo' => '25260',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Facatativá',
                        'codigo' => '25269',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'Fómeque',
                        'codigo' => '25279',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'Fosca',
                        'codigo' => '25281',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'Funza',
                        'codigo' => '25286',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'Fúquene',
                        'codigo' => '25288',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'Fusagasugá',
                        'codigo' => '25290',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                    [
                        'nombre' => 'Gachalá',
                        'codigo' => '25293',
                        'tipo' => 'municipio',
                        'orden' => 31,
                    ],
                    [
                        'nombre' => 'Gachancipá',
                        'codigo' => '25295',
                        'tipo' => 'municipio',
                        'orden' => 32,
                    ],
                    [
                        'nombre' => 'Gachetá',
                        'codigo' => '25297',
                        'tipo' => 'municipio',
                        'orden' => 33,
                    ],
                    [
                        'nombre' => 'Gama',
                        'codigo' => '25299',
                        'tipo' => 'municipio',
                        'orden' => 34,
                    ],
                    [
                        'nombre' => 'Girardot',
                        'codigo' => '25307',
                        'tipo' => 'municipio',
                        'orden' => 35,
                    ],
                    [
                        'nombre' => 'Granada',
                        'codigo' => '25312',
                        'tipo' => 'municipio',
                        'orden' => 36,
                    ],
                    [
                        'nombre' => 'Guachetá',
                        'codigo' => '25317',
                        'tipo' => 'municipio',
                        'orden' => 37,
                    ],
                    [
                        'nombre' => 'Guaduas',
                        'codigo' => '25320',
                        'tipo' => 'municipio',
                        'orden' => 38,
                    ],
                    [
                        'nombre' => 'Guasca',
                        'codigo' => '25322',
                        'tipo' => 'municipio',
                        'orden' => 39,
                    ],
                    [
                        'nombre' => 'Guataquí',
                        'codigo' => '25324',
                        'tipo' => 'municipio',
                        'orden' => 40,
                    ],
                    [
                        'nombre' => 'Guatavita',
                        'codigo' => '25326',
                        'tipo' => 'municipio',
                        'orden' => 41,
                    ],
                    [
                        'nombre' => 'Guayabal de Síquima',
                        'codigo' => '25328',
                        'tipo' => 'municipio',
                        'orden' => 42,
                    ],
                    [
                        'nombre' => 'Guayabetal',
                        'codigo' => '25335',
                        'tipo' => 'municipio',
                        'orden' => 43,
                    ],
                    [
                        'nombre' => 'Gutiérrez',
                        'codigo' => '25339',
                        'tipo' => 'municipio',
                        'orden' => 44,
                    ],
                    [
                        'nombre' => 'Jerusalén',
                        'codigo' => '25368',
                        'tipo' => 'municipio',
                        'orden' => 45,
                    ],
                    [
                        'nombre' => 'Junín',
                        'codigo' => '25372',
                        'tipo' => 'municipio',
                        'orden' => 46,
                    ],
                    [
                        'nombre' => 'La Calera',
                        'codigo' => '25377',
                        'tipo' => 'municipio',
                        'orden' => 47,
                    ],
                    [
                        'nombre' => 'La Mesa',
                        'codigo' => '25386',
                        'tipo' => 'municipio',
                        'orden' => 48,
                    ],
                    [
                        'nombre' => 'La Palma',
                        'codigo' => '25394',
                        'tipo' => 'municipio',
                        'orden' => 49,
                    ],
                    [
                        'nombre' => 'La Peña',
                        'codigo' => '25398',
                        'tipo' => 'municipio',
                        'orden' => 50,
                    ],
                    [
                        'nombre' => 'La Vega',
                        'codigo' => '25402',
                        'tipo' => 'municipio',
                        'orden' => 51,
                    ],
                    [
                        'nombre' => 'Lenguazaque',
                        'codigo' => '25407',
                        'tipo' => 'municipio',
                        'orden' => 52,
                    ],
                    [
                        'nombre' => 'Machetá',
                        'codigo' => '25426',
                        'tipo' => 'municipio',
                        'orden' => 53,
                    ],
                    [
                        'nombre' => 'Madrid',
                        'codigo' => '25430',
                        'tipo' => 'municipio',
                        'orden' => 54,
                    ],
                    [
                        'nombre' => 'Manta',
                        'codigo' => '25436',
                        'tipo' => 'municipio',
                        'orden' => 55,
                    ],
                    [
                        'nombre' => 'Medina',
                        'codigo' => '25438',
                        'tipo' => 'municipio',
                        'orden' => 56,
                    ],
                    [
                        'nombre' => 'Mosquera',
                        'codigo' => '25473',
                        'tipo' => 'municipio',
                        'orden' => 57,
                    ],
                    [
                        'nombre' => 'Nariño',
                        'codigo' => '25483',
                        'tipo' => 'municipio',
                        'orden' => 58,
                    ],
                    [
                        'nombre' => 'Nemocón',
                        'codigo' => '25486',
                        'tipo' => 'municipio',
                        'orden' => 59,
                    ],
                    [
                        'nombre' => 'Nilo',
                        'codigo' => '25488',
                        'tipo' => 'municipio',
                        'orden' => 60,
                    ],
                    [
                        'nombre' => 'Nocaima',
                        'codigo' => '25491',
                        'tipo' => 'municipio',
                        'orden' => 61,
                    ],
                    [
                        'nombre' => 'Pacho',
                        'codigo' => '25513',
                        'tipo' => 'municipio',
                        'orden' => 62,
                    ],
                    [
                        'nombre' => 'Paime',
                        'codigo' => '25518',
                        'tipo' => 'municipio',
                        'orden' => 63,
                    ],
                    [
                        'nombre' => 'Pandi',
                        'codigo' => '25524',
                        'tipo' => 'municipio',
                        'orden' => 64,
                    ],
                    [
                        'nombre' => 'Paratebueno',
                        'codigo' => '25530',
                        'tipo' => 'municipio',
                        'orden' => 65,
                    ],
                    [
                        'nombre' => 'Pasca',
                        'codigo' => '25535',
                        'tipo' => 'municipio',
                        'orden' => 66,
                    ],
                    [
                        'nombre' => 'Puerto Salgar',
                        'codigo' => '25572',
                        'tipo' => 'municipio',
                        'orden' => 67,
                    ],
                    [
                        'nombre' => 'Pulí',
                        'codigo' => '25580',
                        'tipo' => 'municipio',
                        'orden' => 68,
                    ],
                    [
                        'nombre' => 'Quebradanegra',
                        'codigo' => '25592',
                        'tipo' => 'municipio',
                        'orden' => 69,
                    ],
                    [
                        'nombre' => 'Quetame',
                        'codigo' => '25594',
                        'tipo' => 'municipio',
                        'orden' => 70,
                    ],
                    [
                        'nombre' => 'Quipile',
                        'codigo' => '25596',
                        'tipo' => 'municipio',
                        'orden' => 71,
                    ],
                    [
                        'nombre' => 'Ricaurte',
                        'codigo' => '25599',
                        'tipo' => 'municipio',
                        'orden' => 72,
                    ],
                    [
                        'nombre' => 'San Antonio del Tequendama',
                        'codigo' => '25612',
                        'tipo' => 'municipio',
                        'orden' => 73,
                    ],
                    [
                        'nombre' => 'San Bernardo',
                        'codigo' => '25649',
                        'tipo' => 'municipio',
                        'orden' => 74,
                    ],
                    [
                        'nombre' => 'San Cayetano',
                        'codigo' => '25653',
                        'tipo' => 'municipio',
                        'orden' => 75,
                    ],
                    [
                        'nombre' => 'San Francisco',
                        'codigo' => '25658',
                        'tipo' => 'municipio',
                        'orden' => 76,
                    ],
                    [
                        'nombre' => 'San Juan de Rioseco',
                        'codigo' => '25662',
                        'tipo' => 'municipio',
                        'orden' => 77,
                    ],
                    [
                        'nombre' => 'Sasaima',
                        'codigo' => '25718',
                        'tipo' => 'municipio',
                        'orden' => 78,
                    ],
                    [
                        'nombre' => 'Sesquilé',
                        'codigo' => '25736',
                        'tipo' => 'municipio',
                        'orden' => 79,
                    ],
                    [
                        'nombre' => 'Sibaté',
                        'codigo' => '25740',
                        'tipo' => 'municipio',
                        'orden' => 80,
                    ],
                    [
                        'nombre' => 'Silvania',
                        'codigo' => '25743',
                        'tipo' => 'municipio',
                        'orden' => 81,
                    ],
                    [
                        'nombre' => 'Simijaca',
                        'codigo' => '25745',
                        'tipo' => 'municipio',
                        'orden' => 82,
                    ],
                    [
                        'nombre' => 'Soacha',
                        'codigo' => '25754',
                        'tipo' => 'municipio',
                        'orden' => 83,
                    ],
                    [
                        'nombre' => 'Sopó',
                        'codigo' => '25758',
                        'tipo' => 'municipio',
                        'orden' => 84,
                    ],
                    [
                        'nombre' => 'Subachoque',
                        'codigo' => '25769',
                        'tipo' => 'municipio',
                        'orden' => 85,
                    ],
                    [
                        'nombre' => 'Suesca',
                        'codigo' => '25772',
                        'tipo' => 'municipio',
                        'orden' => 86,
                    ],
                    [
                        'nombre' => 'Supatá',
                        'codigo' => '25777',
                        'tipo' => 'municipio',
                        'orden' => 87,
                    ],
                    [
                        'nombre' => 'Susa',
                        'codigo' => '25779',
                        'tipo' => 'municipio',
                        'orden' => 88,
                    ],
                    [
                        'nombre' => 'Sutatausa',
                        'codigo' => '25781',
                        'tipo' => 'municipio',
                        'orden' => 89,
                    ],
                    [
                        'nombre' => 'Tabio',
                        'codigo' => '25785',
                        'tipo' => 'municipio',
                        'orden' => 90,
                    ],
                    [
                        'nombre' => 'Tausa',
                        'codigo' => '25793',
                        'tipo' => 'municipio',
                        'orden' => 91,
                    ],
                    [
                        'nombre' => 'Tena',
                        'codigo' => '25797',
                        'tipo' => 'municipio',
                        'orden' => 92,
                    ],
                    [
                        'nombre' => 'Tenjo',
                        'codigo' => '25799',
                        'tipo' => 'municipio',
                        'orden' => 93,
                    ],
                    [
                        'nombre' => 'Tibacuy',
                        'codigo' => '25805',
                        'tipo' => 'municipio',
                        'orden' => 94,
                    ],
                    [
                        'nombre' => 'Tibirita',
                        'codigo' => '25807',
                        'tipo' => 'municipio',
                        'orden' => 95,
                    ],
                    [
                        'nombre' => 'Tocaima',
                        'codigo' => '25815',
                        'tipo' => 'municipio',
                        'orden' => 96,
                    ],
                    [
                        'nombre' => 'Tocancipá',
                        'codigo' => '25817',
                        'tipo' => 'municipio',
                        'orden' => 97,
                    ],
                    [
                        'nombre' => 'Topaipí',
                        'codigo' => '25823',
                        'tipo' => 'municipio',
                        'orden' => 98,
                    ],
                    [
                        'nombre' => 'Ubalá',
                        'codigo' => '25839',
                        'tipo' => 'municipio',
                        'orden' => 99,
                    ],
                    [
                        'nombre' => 'Ubaque',
                        'codigo' => '25841',
                        'tipo' => 'municipio',
                        'orden' => 100,
                    ],
                    [
                        'nombre' => 'Une',
                        'codigo' => '25845',
                        'tipo' => 'municipio',
                        'orden' => 101,
                    ],
                    [
                        'nombre' => 'Útica',
                        'codigo' => '25851',
                        'tipo' => 'municipio',
                        'orden' => 102,
                    ],
                    [
                        'nombre' => 'Venecia',
                        'codigo' => '25506',
                        'tipo' => 'municipio',
                        'orden' => 103,
                    ],
                    [
                        'nombre' => 'Vergara',
                        'codigo' => '25862',
                        'tipo' => 'municipio',
                        'orden' => 104,
                    ],
                    [
                        'nombre' => 'Vianí',
                        'codigo' => '25867',
                        'tipo' => 'municipio',
                        'orden' => 105,
                    ],
                    [
                        'nombre' => 'Villagómez',
                        'codigo' => '25871',
                        'tipo' => 'municipio',
                        'orden' => 106,
                    ],
                    [
                        'nombre' => 'Villapinzón',
                        'codigo' => '25873',
                        'tipo' => 'municipio',
                        'orden' => 107,
                    ],
                    [
                        'nombre' => 'Villeta',
                        'codigo' => '25875',
                        'tipo' => 'municipio',
                        'orden' => 108,
                    ],
                    [
                        'nombre' => 'Viotá',
                        'codigo' => '25878',
                        'tipo' => 'municipio',
                        'orden' => 109,
                    ],
                    [
                        'nombre' => 'Yacopí',
                        'codigo' => '25885',
                        'tipo' => 'municipio',
                        'orden' => 110,
                    ],
                    [
                        'nombre' => 'Zipacón',
                        'codigo' => '25898',
                        'tipo' => 'municipio',
                        'orden' => 111,
                    ],
                    [
                        'nombre' => 'Zipaquirá',
                        'codigo' => '25899',
                        'tipo' => 'municipio',
                        'orden' => 112,
                    ],
                ],
            ],
            [
                'nombre' => 'Guainía',
                'codigo' => '94',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 15,

                'ciudades' => [
                    [
                        'nombre' => 'Inírida',
                        'codigo' => '94001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Barrancominas',
                        'codigo' => '94343',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                ],
            ],
            [
                'nombre' => 'Guaviare',
                'codigo' => '95',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 16,

                'ciudades' => [
                    [
                        'nombre' => 'San José del Guaviare',
                        'codigo' => '95001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Calamar',
                        'codigo' => '95015',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'El Retorno',
                        'codigo' => '95025',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Miraflores',
                        'codigo' => '95200',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                ],
            ],
            [
                'nombre' => 'Huila',
                'codigo' => '41',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 17,

                'ciudades' => [
                    [
                        'nombre' => 'Neiva',
                        'codigo' => '41001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Acevedo',
                        'codigo' => '41006',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Agrado',
                        'codigo' => '41013',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Aipe',
                        'codigo' => '41016',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Algeciras',
                        'codigo' => '41020',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Altamira',
                        'codigo' => '41026',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Baraya',
                        'codigo' => '41078',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Campoalegre',
                        'codigo' => '41132',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Colombia',
                        'codigo' => '41206',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Elías',
                        'codigo' => '41244',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Garzón',
                        'codigo' => '41298',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Gigante',
                        'codigo' => '41306',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Guadalupe',
                        'codigo' => '41319',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Hobo',
                        'codigo' => '41349',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Íquira',
                        'codigo' => '41357',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Isnos',
                        'codigo' => '41359',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'La Argentina',
                        'codigo' => '41378',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'La Plata',
                        'codigo' => '41396',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Nátaga',
                        'codigo' => '41483',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Oporapa',
                        'codigo' => '41503',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Paicol',
                        'codigo' => '41518',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Palermo',
                        'codigo' => '41524',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'Palestina',
                        'codigo' => '41530',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'Pital',
                        'codigo' => '41548',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Pitalito',
                        'codigo' => '41551',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'Rivera',
                        'codigo' => '41615',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'Saladoblanco',
                        'codigo' => '41660',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'San Agustín',
                        'codigo' => '41668',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'Santa María',
                        'codigo' => '41676',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'Suaza',
                        'codigo' => '41770',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                    [
                        'nombre' => 'Tarqui',
                        'codigo' => '41791',
                        'tipo' => 'municipio',
                        'orden' => 31,
                    ],
                    [
                        'nombre' => 'Tello',
                        'codigo' => '41799',
                        'tipo' => 'municipio',
                        'orden' => 32,
                    ],
                    [
                        'nombre' => 'Teruel',
                        'codigo' => '41801',
                        'tipo' => 'municipio',
                        'orden' => 33,
                    ],
                    [
                        'nombre' => 'Tesalia',
                        'codigo' => '41807',
                        'tipo' => 'municipio',
                        'orden' => 34,
                    ],
                    [
                        'nombre' => 'Timaná',
                        'codigo' => '41807',
                        'tipo' => 'municipio',
                        'orden' => 35,
                    ],
                    [
                        'nombre' => 'Villavieja',
                        'codigo' => '41872',
                        'tipo' => 'municipio',
                        'orden' => 36,
                    ],
                    [
                        'nombre' => 'Yaguará',
                        'codigo' => '41885',
                        'tipo' => 'municipio',
                        'orden' => 37,
                    ],
                ],
            ],
            [
                'nombre' => 'La Guajira',
                'codigo' => '44',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 18,

                'ciudades' => [
                    [
                        'nombre' => 'Riohacha',
                        'codigo' => '44001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Albania',
                        'codigo' => '44035',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Barrancas',
                        'codigo' => '44078',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Dibulla',
                        'codigo' => '44090',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Distracción',
                        'codigo' => '44098',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'El Molino',
                        'codigo' => '44110',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Fonseca',
                        'codigo' => '44279',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Hatonuevo',
                        'codigo' => '44378',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'La Jagua del Pilar',
                        'codigo' => '44420',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Maicao',
                        'codigo' => '44430',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Manaure',
                        'codigo' => '44560',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'San Juan del Cesar',
                        'codigo' => '44650',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Uribia',
                        'codigo' => '44847',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Urumita',
                        'codigo' => '44855',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Villanueva',
                        'codigo' => '44874',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                ],
            ],
            [
                'nombre' => 'Magdalena',
                'codigo' => '47',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 19,

                'ciudades' => [
                    [
                        'nombre' => 'Santa Marta',
                        'codigo' => '47001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Algarrobo',
                        'codigo' => '47030',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Aracataca',
                        'codigo' => '47053',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Ariguaní',
                        'codigo' => '47058',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Cerro de San Antonio',
                        'codigo' => '47161',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Chibolo',
                        'codigo' => '47170',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Ciénaga',
                        'codigo' => '47189',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Concordia',
                        'codigo' => '47205',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'El Banco',
                        'codigo' => '47245',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'El Piñón',
                        'codigo' => '47258',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'El Retén',
                        'codigo' => '47268',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Fundación',
                        'codigo' => '47288',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Guamal',
                        'codigo' => '47318',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Nueva Granada',
                        'codigo' => '47460',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Pedraza',
                        'codigo' => '47541',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Pijiño del Carmen',
                        'codigo' => '47545',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Pivijay',
                        'codigo' => '47551',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Plato',
                        'codigo' => '47555',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Puebloviejo',
                        'codigo' => '47570',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Remolino',
                        'codigo' => '47605',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Sabanas de San Ángel',
                        'codigo' => '47660',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Salamina',
                        'codigo' => '47675',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'San Sebastián de Buenavista',
                        'codigo' => '47692',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'San Zenón',
                        'codigo' => '47703',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Santa Ana',
                        'codigo' => '47707',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'Santa Bárbara de Pinto',
                        'codigo' => '47720',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'Sitionuevo',
                        'codigo' => '47745',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'Tenerife',
                        'codigo' => '47798',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'Zapayán',
                        'codigo' => '47960',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'Zona Bananera',
                        'codigo' => '47980',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                ],
            ],
            [
                'nombre' => 'Meta',
                'codigo' => '50',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 20,

                'ciudades' => [
                    [
                        'nombre' => 'Villavicencio',
                        'codigo' => '50001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Acacías',
                        'codigo' => '50006',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Barranca de Upía',
                        'codigo' => '50110',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Cabuyaro',
                        'codigo' => '50124',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Castilla La Nueva',
                        'codigo' => '50150',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Cubarral',
                        'codigo' => '50223',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Cumaral',
                        'codigo' => '50226',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'El Calvario',
                        'codigo' => '50245',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'El Castillo',
                        'codigo' => '50251',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'El Dorado',
                        'codigo' => '50270',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Fuente de Oro',
                        'codigo' => '50287',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Granada',
                        'codigo' => '50313',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Guamal',
                        'codigo' => '50318',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Mapiripán',
                        'codigo' => '50325',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Mesetas',
                        'codigo' => '50330',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'La Macarena',
                        'codigo' => '50350',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Uribe',
                        'codigo' => '50370',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Lejanías',
                        'codigo' => '50400',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Puerto Concordia',
                        'codigo' => '50450',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Puerto Gaitán',
                        'codigo' => '50568',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Puerto López',
                        'codigo' => '50573',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Puerto Lleras',
                        'codigo' => '50577',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'Puerto Rico',
                        'codigo' => '50590',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'Restrepo',
                        'codigo' => '50606',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'San Carlos de Guaroa',
                        'codigo' => '50680',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'San Juan de Arama',
                        'codigo' => '50683',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'San Juanito',
                        'codigo' => '50686',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'San Martín',
                        'codigo' => '50689',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'Vistahermosa',
                        'codigo' => '50711',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                ],
            ],
            [
                'nombre' => 'Nariño',
                'codigo' => '52',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 21,

                'ciudades' => [
                    [
                        'nombre' => 'Pasto',
                        'codigo' => '52001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Albán',
                        'codigo' => '52019',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Aldana',
                        'codigo' => '52022',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Ancuya',
                        'codigo' => '52036',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Arboleda',
                        'codigo' => '52051',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Barbacoas',
                        'codigo' => '52079',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Belén',
                        'codigo' => '52083',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Buesaco',
                        'codigo' => '52110',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Colón',
                        'codigo' => '52203',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Consacá',
                        'codigo' => '52207',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Contadero',
                        'codigo' => '52210',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Córdoba',
                        'codigo' => '52215',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Cuaspud Carlosama',
                        'codigo' => '52224',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Cumbal',
                        'codigo' => '52227',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Cumbitara',
                        'codigo' => '52233',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Chachagüí',
                        'codigo' => '52240',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'El Charco',
                        'codigo' => '52250',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'El Peñol',
                        'codigo' => '52254',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'El Rosario',
                        'codigo' => '52256',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'El Tablón de Gómez',
                        'codigo' => '52258',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'El Tambo',
                        'codigo' => '52260',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Funes',
                        'codigo' => '52287',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'Guachucal',
                        'codigo' => '52317',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'Guaitarilla',
                        'codigo' => '52320',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Gualmatán',
                        'codigo' => '52323',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'Iles',
                        'codigo' => '52352',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'Imués',
                        'codigo' => '52354',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'Ipiales',
                        'codigo' => '52356',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'La Cruz',
                        'codigo' => '52378',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'La Florida',
                        'codigo' => '52381',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                    [
                        'nombre' => 'La Llanada',
                        'codigo' => '52385',
                        'tipo' => 'municipio',
                        'orden' => 31,
                    ],
                    [
                        'nombre' => 'La Tola',
                        'codigo' => '52390',
                        'tipo' => 'municipio',
                        'orden' => 32,
                    ],
                    [
                        'nombre' => 'La Unión',
                        'codigo' => '52399',
                        'tipo' => 'municipio',
                        'orden' => 33,
                    ],
                    [
                        'nombre' => 'Leiva',
                        'codigo' => '52405',
                        'tipo' => 'municipio',
                        'orden' => 34,
                    ],
                    [
                        'nombre' => 'Linares',
                        'codigo' => '52411',
                        'tipo' => 'municipio',
                        'orden' => 35,
                    ],
                    [
                        'nombre' => 'Los Andes',
                        'codigo' => '52418',
                        'tipo' => 'municipio',
                        'orden' => 36,
                    ],
                    [
                        'nombre' => 'Magüí',
                        'codigo' => '52427',
                        'tipo' => 'municipio',
                        'orden' => 37,
                    ],
                    [
                        'nombre' => 'Mallama',
                        'codigo' => '52435',
                        'tipo' => 'municipio',
                        'orden' => 38,
                    ],
                    [
                        'nombre' => 'Mosquera',
                        'codigo' => '52473',
                        'tipo' => 'municipio',
                        'orden' => 39,
                    ],
                    [
                        'nombre' => 'Nariño',
                        'codigo' => '52480',
                        'tipo' => 'municipio',
                        'orden' => 40,
                    ],
                    [
                        'nombre' => 'Olaya Herrera',
                        'codigo' => '52490',
                        'tipo' => 'municipio',
                        'orden' => 41,
                    ],
                    [
                        'nombre' => 'Ospina',
                        'codigo' => '52506',
                        'tipo' => 'municipio',
                        'orden' => 42,
                    ],
                    [
                        'nombre' => 'Francisco Pizarro',
                        'codigo' => '52520',
                        'tipo' => 'municipio',
                        'orden' => 43,
                    ],
                    [
                        'nombre' => 'Policarpa',
                        'codigo' => '52540',
                        'tipo' => 'municipio',
                        'orden' => 44,
                    ],
                    [
                        'nombre' => 'Potosí',
                        'codigo' => '52560',
                        'tipo' => 'municipio',
                        'orden' => 45,
                    ],
                    [
                        'nombre' => 'Providencia',
                        'codigo' => '52565',
                        'tipo' => 'municipio',
                        'orden' => 46,
                    ],
                    [
                        'nombre' => 'Puerres',
                        'codigo' => '52573',
                        'tipo' => 'municipio',
                        'orden' => 47,
                    ],
                    [
                        'nombre' => 'Pupiales',
                        'codigo' => '52585',
                        'tipo' => 'municipio',
                        'orden' => 48,
                    ],
                    [
                        'nombre' => 'Ricaurte',
                        'codigo' => '52612',
                        'tipo' => 'municipio',
                        'orden' => 49,
                    ],
                    [
                        'nombre' => 'Roberto Payán',
                        'codigo' => '52621',
                        'tipo' => 'municipio',
                        'orden' => 50,
                    ],
                    [
                        'nombre' => 'Samaniego',
                        'codigo' => '52678',
                        'tipo' => 'municipio',
                        'orden' => 51,
                    ],
                    [
                        'nombre' => 'Sandoná',
                        'codigo' => '52683',
                        'tipo' => 'municipio',
                        'orden' => 52,
                    ],
                    [
                        'nombre' => 'San Bernardo',
                        'codigo' => '52685',
                        'tipo' => 'municipio',
                        'orden' => 53,
                    ],
                    [
                        'nombre' => 'San Lorenzo',
                        'codigo' => '52687',
                        'tipo' => 'municipio',
                        'orden' => 54,
                    ],
                    [
                        'nombre' => 'San Pablo',
                        'codigo' => '52693',
                        'tipo' => 'municipio',
                        'orden' => 55,
                    ],
                    [
                        'nombre' => 'San Pedro de Cartago',
                        'codigo' => '52694',
                        'tipo' => 'municipio',
                        'orden' => 56,
                    ],
                    [
                        'nombre' => 'Santa Bárbara',
                        'codigo' => '52696',
                        'tipo' => 'municipio',
                        'orden' => 57,
                    ],
                    [
                        'nombre' => 'Santacruz',
                        'codigo' => '52699',
                        'tipo' => 'municipio',
                        'orden' => 58,
                    ],
                    [
                        'nombre' => 'Sapuyes',
                        'codigo' => '52720',
                        'tipo' => 'municipio',
                        'orden' => 59,
                    ],
                    [
                        'nombre' => 'Taminango',
                        'codigo' => '52786',
                        'tipo' => 'municipio',
                        'orden' => 60,
                    ],
                    [
                        'nombre' => 'Tangua',
                        'codigo' => '52788',
                        'tipo' => 'municipio',
                        'orden' => 61,
                    ],
                    [
                        'nombre' => 'San Andrés de Tumaco',
                        'codigo' => '52835',
                        'tipo' => 'municipio',
                        'orden' => 62,
                    ],
                    [
                        'nombre' => 'Túquerres',
                        'codigo' => '52838',
                        'tipo' => 'municipio',
                        'orden' => 63,
                    ],
                    [
                        'nombre' => 'Yacuanquer',
                        'codigo' => '52885',
                        'tipo' => 'municipio',
                        'orden' => 64,
                    ],
                ],
            ],
            [
                'nombre' => 'Norte de Santander',
                'codigo' => '54',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 22,

                'ciudades' => [
                    [
                        'nombre' => 'Cúcuta',
                        'codigo' => '54001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Ábrego',
                        'codigo' => '54003',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Arboledas',
                        'codigo' => '54051',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Bochalema',
                        'codigo' => '54099',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Bucarasica',
                        'codigo' => '54109',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Cácota',
                        'codigo' => '54125',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Cáchira',
                        'codigo' => '54128',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Chinácota',
                        'codigo' => '54172',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Chitagá',
                        'codigo' => '54174',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Convención',
                        'codigo' => '54206',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Cucutilla',
                        'codigo' => '54223',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Durania',
                        'codigo' => '54239',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'El Carmen',
                        'codigo' => '54245',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'El Tarra',
                        'codigo' => '54250',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'El Zulia',
                        'codigo' => '54261',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Gramalote',
                        'codigo' => '54313',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Hacarí',
                        'codigo' => '54344',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Herrán',
                        'codigo' => '54347',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Labateca',
                        'codigo' => '54377',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'La Esperanza',
                        'codigo' => '54385',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'La Playa de Belén',
                        'codigo' => '54398',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Los Patios',
                        'codigo' => '54405',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'Lourdes',
                        'codigo' => '54418',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'Mutiscua',
                        'codigo' => '54480',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Ocaña',
                        'codigo' => '54498',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'Pamplona',
                        'codigo' => '54518',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'Pamplonita',
                        'codigo' => '54520',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'Puerto Santander',
                        'codigo' => '54553',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'Ragonvalia',
                        'codigo' => '54599',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'Salazar',
                        'codigo' => '54660',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                    [
                        'nombre' => 'San Calixto',
                        'codigo' => '54670',
                        'tipo' => 'municipio',
                        'orden' => 31,
                    ],
                    [
                        'nombre' => 'San Cayetano',
                        'codigo' => '54673',
                        'tipo' => 'municipio',
                        'orden' => 32,
                    ],
                    [
                        'nombre' => 'Santiago',
                        'codigo' => '54680',
                        'tipo' => 'municipio',
                        'orden' => 33,
                    ],
                    [
                        'nombre' => 'Sardinata',
                        'codigo' => '54720',
                        'tipo' => 'municipio',
                        'orden' => 34,
                    ],
                    [
                        'nombre' => 'Silos',
                        'codigo' => '54743',
                        'tipo' => 'municipio',
                        'orden' => 35,
                    ],
                    [
                        'nombre' => 'Teorama',
                        'codigo' => '54800',
                        'tipo' => 'municipio',
                        'orden' => 36,
                    ],
                    [
                        'nombre' => 'Tibú',
                        'codigo' => '54810',
                        'tipo' => 'municipio',
                        'orden' => 37,
                    ],
                    [
                        'nombre' => 'Toledo',
                        'codigo' => '54820',
                        'tipo' => 'municipio',
                        'orden' => 38,
                    ],
                    [
                        'nombre' => 'Villa Caro',
                        'codigo' => '54871',
                        'tipo' => 'municipio',
                        'orden' => 39,
                    ],
                    [
                        'nombre' => 'Villa del Rosario',
                        'codigo' => '54874',
                        'tipo' => 'municipio',
                        'orden' => 40,
                    ],
                ],
            ],
            [
                'nombre' => 'Putumayo',
                'codigo' => '86',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 23,

                'ciudades' => [
                    [
                        'nombre' => 'Mocoa',
                        'codigo' => '86001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Colón',
                        'codigo' => '86219',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Orito',
                        'codigo' => '86320',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Puerto Asís',
                        'codigo' => '86568',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Puerto Caicedo',
                        'codigo' => '86569',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Puerto Guzmán',
                        'codigo' => '86571',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Puerto Leguízamo',
                        'codigo' => '86573',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Sibundoy',
                        'codigo' => '86749',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'San Francisco',
                        'codigo' => '86755',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'San Miguel',
                        'codigo' => '86757',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Santiago',
                        'codigo' => '86760',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Valle del Guamuez',
                        'codigo' => '86865',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Villagarzón',
                        'codigo' => '86885',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                ],
            ],
            [
                'nombre' => 'Quindío',
                'codigo' => '63',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 24,

                'ciudades' => [
                    [
                        'nombre' => 'Armenia',
                        'codigo' => '63001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Buenavista',
                        'codigo' => '63111',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Calarcá',
                        'codigo' => '63130',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Circasia',
                        'codigo' => '63190',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Córdoba',
                        'codigo' => '63212',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Filandia',
                        'codigo' => '63272',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Génova',
                        'codigo' => '63302',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'La Tebaida',
                        'codigo' => '63401',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Montenegro',
                        'codigo' => '63470',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Pijao',
                        'codigo' => '63548',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Quimbaya',
                        'codigo' => '63594',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Salento',
                        'codigo' => '63690',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                ],
            ],
            [
                'nombre' => 'Risaralda',
                'codigo' => '66',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 25,

                'ciudades' => [
                    [
                        'nombre' => 'Pereira',
                        'codigo' => '66001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Apía',
                        'codigo' => '66045',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Balboa',
                        'codigo' => '66075',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Belén de Umbría',
                        'codigo' => '66088',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Dosquebradas',
                        'codigo' => '66170',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Guática',
                        'codigo' => '66318',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'La Celia',
                        'codigo' => '66383',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'La Virginia',
                        'codigo' => '66400',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Marsella',
                        'codigo' => '66440',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Mistrató',
                        'codigo' => '66456',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Pueblo Rico',
                        'codigo' => '66572',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Quinchía',
                        'codigo' => '66594',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Santa Rosa de Cabal',
                        'codigo' => '66682',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Santuario',
                        'codigo' => '66687',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                ],
            ],
            [
                'nombre' => 'San Andrés y Providencia',
                'codigo' => '88',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 26,

                'ciudades' => [
                    [
                        'nombre' => 'San Andrés',
                        'codigo' => '88001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Providencia',
                        'codigo' => '88564',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                ],
            ],
            [
                'nombre' => 'Santander',
                'codigo' => '68',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 27,

                'ciudades' => [
                    [
                        'nombre' => 'Bucaramanga',
                        'codigo' => '68001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Aguada',
                        'codigo' => '68013',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Albania',
                        'codigo' => '68020',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Aratoca',
                        'codigo' => '68051',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Barbosa',
                        'codigo' => '68077',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Barichara',
                        'codigo' => '68079',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Barrancabermeja',
                        'codigo' => '68081',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Betulia',
                        'codigo' => '68092',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Bolívar',
                        'codigo' => '68101',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Cabrera',
                        'codigo' => '68121',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'California',
                        'codigo' => '68132',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Capitanejo',
                        'codigo' => '68147',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Carcasí',
                        'codigo' => '68152',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Cepitá',
                        'codigo' => '68160',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Cerrito',
                        'codigo' => '68162',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Charalá',
                        'codigo' => '68167',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Charta',
                        'codigo' => '68169',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Chima',
                        'codigo' => '68176',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Chipatá',
                        'codigo' => '68179',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Cimitarra',
                        'codigo' => '68190',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Concepción',
                        'codigo' => '68207',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Confines',
                        'codigo' => '68209',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'Contratación',
                        'codigo' => '68211',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'Coromoro',
                        'codigo' => '68217',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Curití',
                        'codigo' => '68229',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'El Carmen de Chucurí',
                        'codigo' => '68235',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'El Guacamayo',
                        'codigo' => '68245',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'El Peñón',
                        'codigo' => '68250',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'El Playón',
                        'codigo' => '68255',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'Encino',
                        'codigo' => '68264',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                    [
                        'nombre' => 'Enciso',
                        'codigo' => '68266',
                        'tipo' => 'municipio',
                        'orden' => 31,
                    ],
                    [
                        'nombre' => 'Florián',
                        'codigo' => '68271',
                        'tipo' => 'municipio',
                        'orden' => 32,
                    ],
                    [
                        'nombre' => 'Floridablanca',
                        'codigo' => '68276',
                        'tipo' => 'municipio',
                        'orden' => 33,
                    ],
                    [
                        'nombre' => 'Galán',
                        'codigo' => '68296',
                        'tipo' => 'municipio',
                        'orden' => 34,
                    ],
                    [
                        'nombre' => 'Gámbita',
                        'codigo' => '68298',
                        'tipo' => 'municipio',
                        'orden' => 35,
                    ],
                    [
                        'nombre' => 'Girón',
                        'codigo' => '68307',
                        'tipo' => 'municipio',
                        'orden' => 36,
                    ],
                    [
                        'nombre' => 'Guaca',
                        'codigo' => '68318',
                        'tipo' => 'municipio',
                        'orden' => 37,
                    ],
                    [
                        'nombre' => 'Guadalupe',
                        'codigo' => '68320',
                        'tipo' => 'municipio',
                        'orden' => 38,
                    ],
                    [
                        'nombre' => 'Guapotá',
                        'codigo' => '68322',
                        'tipo' => 'municipio',
                        'orden' => 39,
                    ],
                    [
                        'nombre' => 'Guavatá',
                        'codigo' => '68324',
                        'tipo' => 'municipio',
                        'orden' => 40,
                    ],
                    [
                        'nombre' => 'Güepsa',
                        'codigo' => '68327',
                        'tipo' => 'municipio',
                        'orden' => 41,
                    ],
                    [
                        'nombre' => 'Hato',
                        'codigo' => '68344',
                        'tipo' => 'municipio',
                        'orden' => 42,
                    ],
                    [
                        'nombre' => 'Jesús María',
                        'codigo' => '68368',
                        'tipo' => 'municipio',
                        'orden' => 43,
                    ],
                    [
                        'nombre' => 'Jordán',
                        'codigo' => '68370',
                        'tipo' => 'municipio',
                        'orden' => 44,
                    ],
                    [
                        'nombre' => 'La Belleza',
                        'codigo' => '68377',
                        'tipo' => 'municipio',
                        'orden' => 45,
                    ],
                    [
                        'nombre' => 'La Paz',
                        'codigo' => '68397',
                        'tipo' => 'municipio',
                        'orden' => 46,
                    ],
                    [
                        'nombre' => 'Landázuri',
                        'codigo' => '68406',
                        'tipo' => 'municipio',
                        'orden' => 47,
                    ],
                    [
                        'nombre' => 'Lebrija',
                        'codigo' => '68406',
                        'tipo' => 'municipio',
                        'orden' => 48,
                    ],
                    [
                        'nombre' => 'Los Santos',
                        'codigo' => '68418',
                        'tipo' => 'municipio',
                        'orden' => 49,
                    ],
                    [
                        'nombre' => 'Macaravita',
                        'codigo' => '68425',
                        'tipo' => 'municipio',
                        'orden' => 50,
                    ],
                    [
                        'nombre' => 'Málaga',
                        'codigo' => '68432',
                        'tipo' => 'municipio',
                        'orden' => 51,
                    ],
                    [
                        'nombre' => 'Matanza',
                        'codigo' => '68444',
                        'tipo' => 'municipio',
                        'orden' => 52,
                    ],
                    [
                        'nombre' => 'Mogotes',
                        'codigo' => '68464',
                        'tipo' => 'municipio',
                        'orden' => 53,
                    ],
                    [
                        'nombre' => 'Molagavita',
                        'codigo' => '68468',
                        'tipo' => 'municipio',
                        'orden' => 54,
                    ],
                    [
                        'nombre' => 'Ocamonte',
                        'codigo' => '68498',
                        'tipo' => 'municipio',
                        'orden' => 55,
                    ],
                    [
                        'nombre' => 'Oiba',
                        'codigo' => '68500',
                        'tipo' => 'municipio',
                        'orden' => 56,
                    ],
                    [
                        'nombre' => 'Onzaga',
                        'codigo' => '68502',
                        'tipo' => 'municipio',
                        'orden' => 57,
                    ],
                    [
                        'nombre' => 'Palmar',
                        'codigo' => '68522',
                        'tipo' => 'municipio',
                        'orden' => 58,
                    ],
                    [
                        'nombre' => 'Palmas del Socorro',
                        'codigo' => '68524',
                        'tipo' => 'municipio',
                        'orden' => 59,
                    ],
                    [
                        'nombre' => 'Páramo',
                        'codigo' => '68533',
                        'tipo' => 'municipio',
                        'orden' => 60,
                    ],
                    [
                        'nombre' => 'Piedecuesta',
                        'codigo' => '68547',
                        'tipo' => 'municipio',
                        'orden' => 61,
                    ],
                    [
                        'nombre' => 'Pinchote',
                        'codigo' => '68549',
                        'tipo' => 'municipio',
                        'orden' => 62,
                    ],
                    [
                        'nombre' => 'Puente Nacional',
                        'codigo' => '68572',
                        'tipo' => 'municipio',
                        'orden' => 63,
                    ],
                    [
                        'nombre' => 'Puerto Parra',
                        'codigo' => '68573',
                        'tipo' => 'municipio',
                        'orden' => 64,
                    ],
                    [
                        'nombre' => 'Puerto Wilches',
                        'codigo' => '68575',
                        'tipo' => 'municipio',
                        'orden' => 65,
                    ],
                    [
                        'nombre' => 'Rionegro',
                        'codigo' => '68615',
                        'tipo' => 'municipio',
                        'orden' => 66,
                    ],
                    [
                        'nombre' => 'Sabana de Torres',
                        'codigo' => '68655',
                        'tipo' => 'municipio',
                        'orden' => 67,
                    ],
                    [
                        'nombre' => 'San Andrés',
                        'codigo' => '68669',
                        'tipo' => 'municipio',
                        'orden' => 68,
                    ],
                    [
                        'nombre' => 'San Benito',
                        'codigo' => '68673',
                        'tipo' => 'municipio',
                        'orden' => 69,
                    ],
                    [
                        'nombre' => 'San Gil',
                        'codigo' => '68679',
                        'tipo' => 'municipio',
                        'orden' => 70,
                    ],
                    [
                        'nombre' => 'San Joaquín',
                        'codigo' => '68682',
                        'tipo' => 'municipio',
                        'orden' => 71,
                    ],
                    [
                        'nombre' => 'San José de Miranda',
                        'codigo' => '68684',
                        'tipo' => 'municipio',
                        'orden' => 72,
                    ],
                    [
                        'nombre' => 'San Miguel',
                        'codigo' => '68686',
                        'tipo' => 'municipio',
                        'orden' => 73,
                    ],
                    [
                        'nombre' => 'San Vicente de Chucurí',
                        'codigo' => '68689',
                        'tipo' => 'municipio',
                        'orden' => 74,
                    ],
                    [
                        'nombre' => 'Santa Bárbara',
                        'codigo' => '68705',
                        'tipo' => 'municipio',
                        'orden' => 75,
                    ],
                    [
                        'nombre' => 'Santa Helena del Opón',
                        'codigo' => '68720',
                        'tipo' => 'municipio',
                        'orden' => 76,
                    ],
                    [
                        'nombre' => 'Simacota',
                        'codigo' => '68745',
                        'tipo' => 'municipio',
                        'orden' => 77,
                    ],
                    [
                        'nombre' => 'Socorro',
                        'codigo' => '68755',
                        'tipo' => 'municipio',
                        'orden' => 78,
                    ],
                    [
                        'nombre' => 'Suaita',
                        'codigo' => '68770',
                        'tipo' => 'municipio',
                        'orden' => 79,
                    ],
                    [
                        'nombre' => 'Sucre',
                        'codigo' => '68773',
                        'tipo' => 'municipio',
                        'orden' => 80,
                    ],
                    [
                        'nombre' => 'Suratá',
                        'codigo' => '68780',
                        'tipo' => 'municipio',
                        'orden' => 81,
                    ],
                    [
                        'nombre' => 'Tona',
                        'codigo' => '68820',
                        'tipo' => 'municipio',
                        'orden' => 82,
                    ],
                    [
                        'nombre' => 'Valle de San José',
                        'codigo' => '68855',
                        'tipo' => 'municipio',
                        'orden' => 83,
                    ],
                    [
                        'nombre' => 'Vélez',
                        'codigo' => '68861',
                        'tipo' => 'municipio',
                        'orden' => 84,
                    ],
                    [
                        'nombre' => 'Vetas',
                        'codigo' => '68867',
                        'tipo' => 'municipio',
                        'orden' => 85,
                    ],
                    [
                        'nombre' => 'Villanueva',
                        'codigo' => '68872',
                        'tipo' => 'municipio',
                        'orden' => 86,
                    ],
                    [
                        'nombre' => 'Zapatoca',
                        'codigo' => '68895',
                        'tipo' => 'municipio',
                        'orden' => 87,
                    ],
                ],
            ],
            [
                'nombre' => 'Sucre',
                'codigo' => '70',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 28,

                'ciudades' => [
                    [
                        'nombre' => 'Sincelejo',
                        'codigo' => '70001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Buenavista',
                        'codigo' => '70110',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Caimito',
                        'codigo' => '70124',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Colosó',
                        'codigo' => '70204',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Corozal',
                        'codigo' => '70215',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Coveñas',
                        'codigo' => '70221',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Chalán',
                        'codigo' => '70230',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'El Roble',
                        'codigo' => '70233',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Galeras',
                        'codigo' => '70235',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Guaranda',
                        'codigo' => '70265',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'La Unión',
                        'codigo' => '70400',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Los Palmitos',
                        'codigo' => '70418',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Majagual',
                        'codigo' => '70429',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Morroa',
                        'codigo' => '70473',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Ovejas',
                        'codigo' => '70508',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Palmito',
                        'codigo' => '70523',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Sampués',
                        'codigo' => '70670',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'San Benito Abad',
                        'codigo' => '70678',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'San Juan de Betulia',
                        'codigo' => '70702',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'San Marcos',
                        'codigo' => '70708',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'San Onofre',
                        'codigo' => '70713',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'San Pedro',
                        'codigo' => '70717',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'San Luis de Sincé',
                        'codigo' => '70742',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'Sucre',
                        'codigo' => '70771',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Santiago de Tolú',
                        'codigo' => '70820',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'Tolú Viejo',
                        'codigo' => '70823',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                ],
            ],
            [
                'nombre' => 'Tolima',
                'codigo' => '73',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 29,

                'ciudades' => [
                    [
                        'nombre' => 'Ibagué',
                        'codigo' => '73001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Alpujarra',
                        'codigo' => '73024',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Alvarado',
                        'codigo' => '73026',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Ambalema',
                        'codigo' => '73030',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Anzoátegui',
                        'codigo' => '73043',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Armero',
                        'codigo' => '73055',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Ataco',
                        'codigo' => '73067',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Cajamarca',
                        'codigo' => '73124',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Carmen de Apicalá',
                        'codigo' => '73148',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Casabianca',
                        'codigo' => '73152',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Chaparral',
                        'codigo' => '73168',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Coello',
                        'codigo' => '73200',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Coyaima',
                        'codigo' => '73217',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Cunday',
                        'codigo' => '73226',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'Dolores',
                        'codigo' => '73236',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'Espinal',
                        'codigo' => '73268',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'Falan',
                        'codigo' => '73270',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'Flandes',
                        'codigo' => '73275',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Fresno',
                        'codigo' => '73283',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Guamo',
                        'codigo' => '73319',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Herveo',
                        'codigo' => '73347',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Honda',
                        'codigo' => '73349',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'Icononzo',
                        'codigo' => '73352',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'Lérida',
                        'codigo' => '73408',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'Líbano',
                        'codigo' => '73411',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'Mariquita',
                        'codigo' => '73443',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'Melgar',
                        'codigo' => '73449',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'Murillo',
                        'codigo' => '73461',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'Natagaima',
                        'codigo' => '73483',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'Ortega',
                        'codigo' => '73504',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                    [
                        'nombre' => 'Palocabildo',
                        'codigo' => '73520',
                        'tipo' => 'municipio',
                        'orden' => 31,
                    ],
                    [
                        'nombre' => 'Piedras',
                        'codigo' => '73547',
                        'tipo' => 'municipio',
                        'orden' => 32,
                    ],
                    [
                        'nombre' => 'Planadas',
                        'codigo' => '73555',
                        'tipo' => 'municipio',
                        'orden' => 33,
                    ],
                    [
                        'nombre' => 'Prado',
                        'codigo' => '73563',
                        'tipo' => 'municipio',
                        'orden' => 34,
                    ],
                    [
                        'nombre' => 'Purificación',
                        'codigo' => '73585',
                        'tipo' => 'municipio',
                        'orden' => 35,
                    ],
                    [
                        'nombre' => 'Rioblanco',
                        'codigo' => '73616',
                        'tipo' => 'municipio',
                        'orden' => 36,
                    ],
                    [
                        'nombre' => 'Roncesvalles',
                        'codigo' => '73622',
                        'tipo' => 'municipio',
                        'orden' => 37,
                    ],
                    [
                        'nombre' => 'Rovira',
                        'codigo' => '73624',
                        'tipo' => 'municipio',
                        'orden' => 38,
                    ],
                    [
                        'nombre' => 'Saldaña',
                        'codigo' => '73671',
                        'tipo' => 'municipio',
                        'orden' => 39,
                    ],
                    [
                        'nombre' => 'San Antonio',
                        'codigo' => '73675',
                        'tipo' => 'municipio',
                        'orden' => 40,
                    ],
                    [
                        'nombre' => 'San Luis',
                        'codigo' => '73678',
                        'tipo' => 'municipio',
                        'orden' => 41,
                    ],
                    [
                        'nombre' => 'Santa Isabel',
                        'codigo' => '73686',
                        'tipo' => 'municipio',
                        'orden' => 42,
                    ],
                    [
                        'nombre' => 'Suárez',
                        'codigo' => '73770',
                        'tipo' => 'municipio',
                        'orden' => 43,
                    ],
                    [
                        'nombre' => 'Valle de San Juan',
                        'codigo' => '73854',
                        'tipo' => 'municipio',
                        'orden' => 44,
                    ],
                    [
                        'nombre' => 'Venadillo',
                        'codigo' => '73861',
                        'tipo' => 'municipio',
                        'orden' => 45,
                    ],
                    [
                        'nombre' => 'Villahermosa',
                        'codigo' => '73870',
                        'tipo' => 'municipio',
                        'orden' => 46,
                    ],
                    [
                        'nombre' => 'Villarrica',
                        'codigo' => '73873',
                        'tipo' => 'municipio',
                        'orden' => 47,
                    ],
                ],
            ],
            [
                'nombre' => 'Valle del Cauca',
                'codigo' => '76',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 30,

                'ciudades' => [
                    [
                        'nombre' => 'Cali',
                        'codigo' => '76001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Alcalá',
                        'codigo' => '76020',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Andalucía',
                        'codigo' => '76036',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Ansermanuevo',
                        'codigo' => '76041',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                    [
                        'nombre' => 'Argelia',
                        'codigo' => '76054',
                        'tipo' => 'municipio',
                        'orden' => 5,
                    ],
                    [
                        'nombre' => 'Bolívar',
                        'codigo' => '76100',
                        'tipo' => 'municipio',
                        'orden' => 6,
                    ],
                    [
                        'nombre' => 'Buenaventura',
                        'codigo' => '76109',
                        'tipo' => 'municipio',
                        'orden' => 7,
                    ],
                    [
                        'nombre' => 'Guadalajara de Buga',
                        'codigo' => '76111',
                        'tipo' => 'municipio',
                        'orden' => 8,
                    ],
                    [
                        'nombre' => 'Bugalagrande',
                        'codigo' => '76113',
                        'tipo' => 'municipio',
                        'orden' => 9,
                    ],
                    [
                        'nombre' => 'Caicedonia',
                        'codigo' => '76122',
                        'tipo' => 'municipio',
                        'orden' => 10,
                    ],
                    [
                        'nombre' => 'Calima',
                        'codigo' => '76126',
                        'tipo' => 'municipio',
                        'orden' => 11,
                    ],
                    [
                        'nombre' => 'Candelaria',
                        'codigo' => '76130',
                        'tipo' => 'municipio',
                        'orden' => 12,
                    ],
                    [
                        'nombre' => 'Cartago',
                        'codigo' => '76147',
                        'tipo' => 'municipio',
                        'orden' => 13,
                    ],
                    [
                        'nombre' => 'Dagua',
                        'codigo' => '76233',
                        'tipo' => 'municipio',
                        'orden' => 14,
                    ],
                    [
                        'nombre' => 'El Águila',
                        'codigo' => '76243',
                        'tipo' => 'municipio',
                        'orden' => 15,
                    ],
                    [
                        'nombre' => 'El Cairo',
                        'codigo' => '76246',
                        'tipo' => 'municipio',
                        'orden' => 16,
                    ],
                    [
                        'nombre' => 'El Cerrito',
                        'codigo' => '76248',
                        'tipo' => 'municipio',
                        'orden' => 17,
                    ],
                    [
                        'nombre' => 'El Dovio',
                        'codigo' => '76250',
                        'tipo' => 'municipio',
                        'orden' => 18,
                    ],
                    [
                        'nombre' => 'Florida',
                        'codigo' => '76275',
                        'tipo' => 'municipio',
                        'orden' => 19,
                    ],
                    [
                        'nombre' => 'Ginebra',
                        'codigo' => '76306',
                        'tipo' => 'municipio',
                        'orden' => 20,
                    ],
                    [
                        'nombre' => 'Guacarí',
                        'codigo' => '76318',
                        'tipo' => 'municipio',
                        'orden' => 21,
                    ],
                    [
                        'nombre' => 'Jamundí',
                        'codigo' => '76364',
                        'tipo' => 'municipio',
                        'orden' => 22,
                    ],
                    [
                        'nombre' => 'La Cumbre',
                        'codigo' => '76377',
                        'tipo' => 'municipio',
                        'orden' => 23,
                    ],
                    [
                        'nombre' => 'La Unión',
                        'codigo' => '76400',
                        'tipo' => 'municipio',
                        'orden' => 24,
                    ],
                    [
                        'nombre' => 'La Victoria',
                        'codigo' => '76403',
                        'tipo' => 'municipio',
                        'orden' => 25,
                    ],
                    [
                        'nombre' => 'Obando',
                        'codigo' => '76497',
                        'tipo' => 'municipio',
                        'orden' => 26,
                    ],
                    [
                        'nombre' => 'Palmira',
                        'codigo' => '76520',
                        'tipo' => 'municipio',
                        'orden' => 27,
                    ],
                    [
                        'nombre' => 'Pradera',
                        'codigo' => '76563',
                        'tipo' => 'municipio',
                        'orden' => 28,
                    ],
                    [
                        'nombre' => 'Restrepo',
                        'codigo' => '76606',
                        'tipo' => 'municipio',
                        'orden' => 29,
                    ],
                    [
                        'nombre' => 'Riofrío',
                        'codigo' => '76616',
                        'tipo' => 'municipio',
                        'orden' => 30,
                    ],
                    [
                        'nombre' => 'Roldanillo',
                        'codigo' => '76622',
                        'tipo' => 'municipio',
                        'orden' => 31,
                    ],
                    [
                        'nombre' => 'San Pedro',
                        'codigo' => '76670',
                        'tipo' => 'municipio',
                        'orden' => 32,
                    ],
                    [
                        'nombre' => 'Sevilla',
                        'codigo' => '76736',
                        'tipo' => 'municipio',
                        'orden' => 33,
                    ],
                    [
                        'nombre' => 'Toro',
                        'codigo' => '76823',
                        'tipo' => 'municipio',
                        'orden' => 34,
                    ],
                    [
                        'nombre' => 'Trujillo',
                        'codigo' => '76828',
                        'tipo' => 'municipio',
                        'orden' => 35,
                    ],
                    [
                        'nombre' => 'Tuluá',
                        'codigo' => '76834',
                        'tipo' => 'municipio',
                        'orden' => 36,
                    ],
                    [
                        'nombre' => 'Ulloa',
                        'codigo' => '76845',
                        'tipo' => 'municipio',
                        'orden' => 37,
                    ],
                    [
                        'nombre' => 'Versalles',
                        'codigo' => '76863',
                        'tipo' => 'municipio',
                        'orden' => 38,
                    ],
                    [
                        'nombre' => 'Vijes',
                        'codigo' => '76869',
                        'tipo' => 'municipio',
                        'orden' => 39,
                    ],
                    [
                        'nombre' => 'Yotoco',
                        'codigo' => '76890',
                        'tipo' => 'municipio',
                        'orden' => 40,
                    ],
                    [
                        'nombre' => 'Yumbo',
                        'codigo' => '76892',
                        'tipo' => 'municipio',
                        'orden' => 41,
                    ],
                    [
                        'nombre' => 'Zarzal',
                        'codigo' => '76895',
                        'tipo' => 'municipio',
                        'orden' => 42,
                    ],
                ],
            ],
            [
                'nombre' => 'Vaupés',
                'codigo' => '97',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 31,

                'ciudades' => [
                    [
                        'nombre' => 'Mitú',
                        'codigo' => '97001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'Carurú',
                        'codigo' => '97161',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Taraira',
                        'codigo' => '97666',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                ],
            ],
            [
                'nombre' => 'Vichada',
                'codigo' => '99',
                'tipo' => 'departamento',
                'label' => 'Departamento',
                'orden' => 32,

                'ciudades' => [
                    [
                        'nombre' => 'Puerto Carreño',
                        'codigo' => '99001',
                        'tipo' => 'municipio',
                        'orden' => 1,
                    ],
                    [
                        'nombre' => 'La Primavera',
                        'codigo' => '99524',
                        'tipo' => 'municipio',
                        'orden' => 2,
                    ],
                    [
                        'nombre' => 'Santa Rosalía',
                        'codigo' => '99624',
                        'tipo' => 'municipio',
                        'orden' => 3,
                    ],
                    [
                        'nombre' => 'Cumaribo',
                        'codigo' => '99773',
                        'tipo' => 'municipio',
                        'orden' => 4,
                    ],
                ],
            ],
        ],
    ],

            /*
|--------------------------------------------------------------------------
| ECUADOR
|--------------------------------------------------------------------------
*/

            'EC' => [

                'nombre' => 'Ecuador',
                'codigo_iso2' => 'EC',
                'codigo_iso3' => 'ECU',
                'codigo_telefonico' => '593',

                'division_label' => 'Provincia',
                'localidad_label' => 'Cantón / Ciudad',

                'divisiones' => [

                    // ==============================================================
                    // AZUAY
                    // ==============================================================

                    [
                        'nombre' => 'Azuay',
                        'codigo' => '01',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 1,

                        'ciudades' => [
                            ['nombre' => 'Cuenca', 'codigo' => '0101', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Girón', 'codigo' => '0102', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Gualaceo', 'codigo' => '0103', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Nabón', 'codigo' => '0104', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Paute', 'codigo' => '0105', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'Pucará', 'codigo' => '0106', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'San Fernando', 'codigo' => '0107', 'tipo' => 'cantón', 'orden' => 7],
                            ['nombre' => 'Santa Isabel', 'codigo' => '0108', 'tipo' => 'cantón', 'orden' => 8],
                            ['nombre' => 'Sígsig', 'codigo' => '0109', 'tipo' => 'cantón', 'orden' => 9],
                            ['nombre' => 'Oña', 'codigo' => '0110', 'tipo' => 'cantón', 'orden' => 10],
                            ['nombre' => 'Chordeleg', 'codigo' => '0111', 'tipo' => 'cantón', 'orden' => 11],
                            ['nombre' => 'El Pan', 'codigo' => '0112', 'tipo' => 'cantón', 'orden' => 12],
                            ['nombre' => 'Sevilla de Oro', 'codigo' => '0113', 'tipo' => 'cantón', 'orden' => 13],
                            ['nombre' => 'Guachapala', 'codigo' => '0114', 'tipo' => 'cantón', 'orden' => 14],
                            ['nombre' => 'Camilo Ponce Enríquez', 'codigo' => '0115', 'tipo' => 'cantón', 'orden' => 15],
                        ],
                    ],

                    // ==============================================================
                    // BOLÍVAR
                    // ==============================================================

                    [
                        'nombre' => 'Bolívar',
                        'codigo' => '02',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 2,

                        'ciudades' => [
                            ['nombre' => 'Guaranda', 'codigo' => '0201', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Chillanes', 'codigo' => '0202', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Chimbo', 'codigo' => '0203', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Echeandía', 'codigo' => '0204', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'San Miguel', 'codigo' => '0205', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'Caluma', 'codigo' => '0206', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Las Naves', 'codigo' => '0207', 'tipo' => 'cantón', 'orden' => 7],
                        ],
                    ],

                    // ==============================================================
                    // CAÑAR
                    // ==============================================================

                    [
                        'nombre' => 'Cañar',
                        'codigo' => '03',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 3,

                        'ciudades' => [
                            ['nombre' => 'Azogues', 'codigo' => '0301', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Biblián', 'codigo' => '0302', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Cañar', 'codigo' => '0303', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'La Troncal', 'codigo' => '0304', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'El Tambo', 'codigo' => '0305', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'Déleg', 'codigo' => '0306', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Suscal', 'codigo' => '0307', 'tipo' => 'cantón', 'orden' => 7],
                        ],
                    ],

                    // ==============================================================
                    // CARCHI
                    // ==============================================================

                    [
                        'nombre' => 'Carchi',
                        'codigo' => '04',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 4,

                        'ciudades' => [
                            ['nombre' => 'Tulcán', 'codigo' => '0401', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Bolívar', 'codigo' => '0402', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Espejo', 'codigo' => '0403', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Mira', 'codigo' => '0404', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Montúfar', 'codigo' => '0405', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'San Pedro de Huaca', 'codigo' => '0406', 'tipo' => 'cantón', 'orden' => 6],
                        ],
                    ],

                    // ==============================================================
                    // COTOPAXI
                    // ==============================================================

                    [
                        'nombre' => 'Cotopaxi',
                        'codigo' => '05',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 5,

                        'ciudades' => [
                            ['nombre' => 'Latacunga', 'codigo' => '0501', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'La Maná', 'codigo' => '0502', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Pangua', 'codigo' => '0503', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Pujilí', 'codigo' => '0504', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Salcedo', 'codigo' => '0505', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'Saquisilí', 'codigo' => '0506', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Sigchos', 'codigo' => '0507', 'tipo' => 'cantón', 'orden' => 7],
                        ],
                    ],

                    // ==============================================================
                    // CHIMBORAZO
                    // ==============================================================

                    [
                        'nombre' => 'Chimborazo',
                        'codigo' => '06',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 6,

                        'ciudades' => [
                            ['nombre' => 'Riobamba', 'codigo' => '0601', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Alausí', 'codigo' => '0602', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Colta', 'codigo' => '0603', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Chambo', 'codigo' => '0604', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Chunchi', 'codigo' => '0605', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'Guamote', 'codigo' => '0606', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Guano', 'codigo' => '0607', 'tipo' => 'cantón', 'orden' => 7],
                            ['nombre' => 'Pallatanga', 'codigo' => '0608', 'tipo' => 'cantón', 'orden' => 8],
                            ['nombre' => 'Penipe', 'codigo' => '0609', 'tipo' => 'cantón', 'orden' => 9],
                            ['nombre' => 'Cumandá', 'codigo' => '0610', 'tipo' => 'cantón', 'orden' => 10],
                        ],
                    ],

                    // ==============================================================
                    // EL ORO
                    // ==============================================================

                    [
                        'nombre' => 'El Oro',
                        'codigo' => '07',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 7,

                        'ciudades' => [
                            ['nombre' => 'Machala', 'codigo' => '0701', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Arenillas', 'codigo' => '0702', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Atahualpa', 'codigo' => '0703', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Balsas', 'codigo' => '0704', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Chilla', 'codigo' => '0705', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'El Guabo', 'codigo' => '0706', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Huaquillas', 'codigo' => '0707', 'tipo' => 'cantón', 'orden' => 7],
                            ['nombre' => 'Marcabelí', 'codigo' => '0708', 'tipo' => 'cantón', 'orden' => 8],
                            ['nombre' => 'Pasaje', 'codigo' => '0709', 'tipo' => 'cantón', 'orden' => 9],
                            ['nombre' => 'Piñas', 'codigo' => '0710', 'tipo' => 'cantón', 'orden' => 10],
                            ['nombre' => 'Portovelo', 'codigo' => '0711', 'tipo' => 'cantón', 'orden' => 11],
                            ['nombre' => 'Santa Rosa', 'codigo' => '0712', 'tipo' => 'cantón', 'orden' => 12],
                            ['nombre' => 'Zaruma', 'codigo' => '0713', 'tipo' => 'cantón', 'orden' => 13],
                            ['nombre' => 'Las Lajas', 'codigo' => '0714', 'tipo' => 'cantón', 'orden' => 14],
                        ],
                    ],

                    // ==============================================================
                    // ESMERALDAS
                    // ==============================================================

                    [
                        'nombre' => 'Esmeraldas',
                        'codigo' => '08',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 8,

                        'ciudades' => [
                            ['nombre' => 'Esmeraldas', 'codigo' => '0801', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Eloy Alfaro', 'codigo' => '0802', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Muisne', 'codigo' => '0803', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Quinindé', 'codigo' => '0804', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'San Lorenzo', 'codigo' => '0805', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'Atacames', 'codigo' => '0806', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Rioverde', 'codigo' => '0807', 'tipo' => 'cantón', 'orden' => 7],
                        ],
                    ],

                    // ==============================================================
                    // GUAYAS
                    // ==============================================================

                    [
                        'nombre' => 'Guayas',
                        'codigo' => '09',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 9,

                        'ciudades' => [
                            ['nombre' => 'Guayaquil', 'codigo' => '0901', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Alfredo Baquerizo Moreno', 'codigo' => '0902', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Balao', 'codigo' => '0903', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Balzar', 'codigo' => '0904', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Colimes', 'codigo' => '0905', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'Daule', 'codigo' => '0906', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Durán', 'codigo' => '0907', 'tipo' => 'cantón', 'orden' => 7],
                            ['nombre' => 'El Empalme', 'codigo' => '0908', 'tipo' => 'cantón', 'orden' => 8],
                            ['nombre' => 'El Triunfo', 'codigo' => '0909', 'tipo' => 'cantón', 'orden' => 9],
                            ['nombre' => 'Milagro', 'codigo' => '0910', 'tipo' => 'cantón', 'orden' => 10],
                            ['nombre' => 'Naranjal', 'codigo' => '0911', 'tipo' => 'cantón', 'orden' => 11],
                            ['nombre' => 'Naranjito', 'codigo' => '0912', 'tipo' => 'cantón', 'orden' => 12],
                            ['nombre' => 'Palestina', 'codigo' => '0913', 'tipo' => 'cantón', 'orden' => 13],
                            ['nombre' => 'Pedro Carbo', 'codigo' => '0914', 'tipo' => 'cantón', 'orden' => 14],
                            ['nombre' => 'Samborondón', 'codigo' => '0915', 'tipo' => 'cantón', 'orden' => 15],
                            ['nombre' => 'Santa Lucía', 'codigo' => '0916', 'tipo' => 'cantón', 'orden' => 16],
                            ['nombre' => 'Salitre', 'codigo' => '0917', 'tipo' => 'cantón', 'orden' => 17],
                            ['nombre' => 'Yaguachi', 'codigo' => '0918', 'tipo' => 'cantón', 'orden' => 18],
                            ['nombre' => 'Playas', 'codigo' => '0919', 'tipo' => 'cantón', 'orden' => 19],
                            ['nombre' => 'Simón Bolívar', 'codigo' => '0920', 'tipo' => 'cantón', 'orden' => 20],
                            ['nombre' => 'Coronel Marcelino Maridueña', 'codigo' => '0921', 'tipo' => 'cantón', 'orden' => 21],
                            ['nombre' => 'Lomas de Sargentillo', 'codigo' => '0922', 'tipo' => 'cantón', 'orden' => 22],
                            ['nombre' => 'Nobol', 'codigo' => '0923', 'tipo' => 'cantón', 'orden' => 23],
                            ['nombre' => 'General Antonio Elizalde', 'codigo' => '0924', 'tipo' => 'cantón', 'orden' => 24],
                            ['nombre' => 'Isidro Ayora', 'codigo' => '0925', 'tipo' => 'cantón', 'orden' => 25],
                        ],
                    ],

                    // ==============================================================
                    // IMBABURA
                    // ==============================================================

                    [
                        'nombre' => 'Imbabura',
                        'codigo' => '10',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 10,

                        'ciudades' => [
                            ['nombre' => 'Ibarra', 'codigo' => '1001', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Antonio Ante', 'codigo' => '1002', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Cotacachi', 'codigo' => '1003', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Otavalo', 'codigo' => '1004', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Pimampiro', 'codigo' => '1005', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'San Miguel de Urcuquí', 'codigo' => '1006', 'tipo' => 'cantón', 'orden' => 6],
                        ],
                    ],

                    // ==============================================================
                    // LOJA
                    // ==============================================================

                    [
                        'nombre' => 'Loja',
                        'codigo' => '11',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 11,

                        'ciudades' => [
                            ['nombre' => 'Loja', 'codigo' => '1101', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Calvas', 'codigo' => '1102', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Catamayo', 'codigo' => '1103', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Celica', 'codigo' => '1104', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Chaguarpamba', 'codigo' => '1105', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'Espíndola', 'codigo' => '1106', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Gonzanamá', 'codigo' => '1107', 'tipo' => 'cantón', 'orden' => 7],
                            ['nombre' => 'Macará', 'codigo' => '1108', 'tipo' => 'cantón', 'orden' => 8],
                            ['nombre' => 'Paltas', 'codigo' => '1109', 'tipo' => 'cantón', 'orden' => 9],
                            ['nombre' => 'Puyango', 'codigo' => '1110', 'tipo' => 'cantón', 'orden' => 10],
                            ['nombre' => 'Saraguro', 'codigo' => '1111', 'tipo' => 'cantón', 'orden' => 11],
                            ['nombre' => 'Sozoranga', 'codigo' => '1112', 'tipo' => 'cantón', 'orden' => 12],
                            ['nombre' => 'Zapotillo', 'codigo' => '1113', 'tipo' => 'cantón', 'orden' => 13],
                            ['nombre' => 'Pindal', 'codigo' => '1114', 'tipo' => 'cantón', 'orden' => 14],
                            ['nombre' => 'Quilanga', 'codigo' => '1115', 'tipo' => 'cantón', 'orden' => 15],
                            ['nombre' => 'Olmedo', 'codigo' => '1116', 'tipo' => 'cantón', 'orden' => 16],
                        ],
                    ],

                    // ==============================================================
                    // LOS RÍOS
                    // ==============================================================

                    [
                        'nombre' => 'Los Ríos',
                        'codigo' => '12',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 12,

                        'ciudades' => [
                            ['nombre' => 'Babahoyo', 'codigo' => '1201', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Baba', 'codigo' => '1202', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Montalvo', 'codigo' => '1203', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Puebloviejo', 'codigo' => '1204', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Quevedo', 'codigo' => '1205', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'Urdaneta', 'codigo' => '1206', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Ventanas', 'codigo' => '1207', 'tipo' => 'cantón', 'orden' => 7],
                            ['nombre' => 'Vinces', 'codigo' => '1208', 'tipo' => 'cantón', 'orden' => 8],
                            ['nombre' => 'Palenque', 'codigo' => '1209', 'tipo' => 'cantón', 'orden' => 9],
                            ['nombre' => 'Buena Fe', 'codigo' => '1210', 'tipo' => 'cantón', 'orden' => 10],
                            ['nombre' => 'Valencia', 'codigo' => '1211', 'tipo' => 'cantón', 'orden' => 11],
                            ['nombre' => 'Mocache', 'codigo' => '1212', 'tipo' => 'cantón', 'orden' => 12],
                            ['nombre' => 'Quinsaloma', 'codigo' => '1213', 'tipo' => 'cantón', 'orden' => 13],
                        ],
                    ],

                    // ==============================================================
                    // MANABÍ
                    // ==============================================================

                    [
                        'nombre' => 'Manabí',
                        'codigo' => '13',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 13,

                        'ciudades' => [
                            ['nombre' => 'Portoviejo', 'codigo' => '1301', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Bolívar', 'codigo' => '1302', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Chone', 'codigo' => '1303', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'El Carmen', 'codigo' => '1304', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Flavio Alfaro', 'codigo' => '1305', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'Jipijapa', 'codigo' => '1306', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Junín', 'codigo' => '1307', 'tipo' => 'cantón', 'orden' => 7],
                            ['nombre' => 'Manta', 'codigo' => '1308', 'tipo' => 'cantón', 'orden' => 8],
                            ['nombre' => 'Montecristi', 'codigo' => '1309', 'tipo' => 'cantón', 'orden' => 9],
                            ['nombre' => 'Paján', 'codigo' => '1310', 'tipo' => 'cantón', 'orden' => 10],
                            ['nombre' => 'Pichincha', 'codigo' => '1311', 'tipo' => 'cantón', 'orden' => 11],
                            ['nombre' => 'Rocafuerte', 'codigo' => '1312', 'tipo' => 'cantón', 'orden' => 12],
                            ['nombre' => 'Santa Ana', 'codigo' => '1313', 'tipo' => 'cantón', 'orden' => 13],
                            ['nombre' => 'Sucre', 'codigo' => '1314', 'tipo' => 'cantón', 'orden' => 14],
                            ['nombre' => 'Tosagua', 'codigo' => '1315', 'tipo' => 'cantón', 'orden' => 15],
                            ['nombre' => '24 de Mayo', 'codigo' => '1316', 'tipo' => 'cantón', 'orden' => 16],
                            ['nombre' => 'Pedernales', 'codigo' => '1317', 'tipo' => 'cantón', 'orden' => 17],
                            ['nombre' => 'Olmedo', 'codigo' => '1318', 'tipo' => 'cantón', 'orden' => 18],
                            ['nombre' => 'Puerto López', 'codigo' => '1319', 'tipo' => 'cantón', 'orden' => 19],
                            ['nombre' => 'Jama', 'codigo' => '1320', 'tipo' => 'cantón', 'orden' => 20],
                            ['nombre' => 'Jaramijó', 'codigo' => '1321', 'tipo' => 'cantón', 'orden' => 21],
                            ['nombre' => 'San Vicente', 'codigo' => '1322', 'tipo' => 'cantón', 'orden' => 22],
                        ],
                    ],

                    // ==============================================================
                    // MORONA SANTIAGO
                    // ==============================================================

                    [
                        'nombre' => 'Morona Santiago',
                        'codigo' => '14',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 14,

                        'ciudades' => [
                            ['nombre' => 'Morona', 'codigo' => '1401', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Gualaquiza', 'codigo' => '1402', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Limón Indanza', 'codigo' => '1403', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Palora', 'codigo' => '1404', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Santiago', 'codigo' => '1405', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'Sucúa', 'codigo' => '1406', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Huamboya', 'codigo' => '1407', 'tipo' => 'cantón', 'orden' => 7],
                            ['nombre' => 'San Juan Bosco', 'codigo' => '1408', 'tipo' => 'cantón', 'orden' => 8],
                            ['nombre' => 'Taisha', 'codigo' => '1409', 'tipo' => 'cantón', 'orden' => 9],
                            ['nombre' => 'Logroño', 'codigo' => '1410', 'tipo' => 'cantón', 'orden' => 10],
                            ['nombre' => 'Pablo Sexto', 'codigo' => '1411', 'tipo' => 'cantón', 'orden' => 11],
                            ['nombre' => 'Tiwintza', 'codigo' => '1412', 'tipo' => 'cantón', 'orden' => 12],
                        ],
                    ],

                    // ==============================================================
                    // NAPO
                    // ==============================================================

                    [
                        'nombre' => 'Napo',
                        'codigo' => '15',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 15,

                        'ciudades' => [
                            ['nombre' => 'Tena', 'codigo' => '1501', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Archidona', 'codigo' => '1502', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'El Chaco', 'codigo' => '1503', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Quijos', 'codigo' => '1504', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Carlos Julio Arosemena Tola', 'codigo' => '1507', 'tipo' => 'cantón', 'orden' => 5],
                        ],
                    ],

                    // ==============================================================
                    // PASTAZA
                    // ==============================================================

                    [
                        'nombre' => 'Pastaza',
                        'codigo' => '16',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 16,

                        'ciudades' => [
                            ['nombre' => 'Pastaza', 'codigo' => '1601', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Mera', 'codigo' => '1602', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Santa Clara', 'codigo' => '1603', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Arajuno', 'codigo' => '1604', 'tipo' => 'cantón', 'orden' => 4],
                        ],
                    ],

                    // ==============================================================
                    // PICHINCHA
                    // ==============================================================

                    [
                        'nombre' => 'Pichincha',
                        'codigo' => '17',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 17,

                        'ciudades' => [
                            ['nombre' => 'Quito', 'codigo' => '1701', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Cayambe', 'codigo' => '1702', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Mejía', 'codigo' => '1703', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Pedro Moncayo', 'codigo' => '1704', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Rumiñahui', 'codigo' => '1705', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'San Miguel de los Bancos', 'codigo' => '1706', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Pedro Vicente Maldonado', 'codigo' => '1707', 'tipo' => 'cantón', 'orden' => 7],
                            ['nombre' => 'Puerto Quito', 'codigo' => '1708', 'tipo' => 'cantón', 'orden' => 8],
                        ],
                    ],

                    // ==============================================================
                    // TUNGURAHUA
                    // ==============================================================

                    [
                        'nombre' => 'Tungurahua',
                        'codigo' => '18',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 18,

                        'ciudades' => [
                            ['nombre' => 'Ambato', 'codigo' => '1801', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Baños de Agua Santa', 'codigo' => '1802', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Cevallos', 'codigo' => '1803', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Mocha', 'codigo' => '1804', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Patate', 'codigo' => '1805', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'Quero', 'codigo' => '1806', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'San Pedro de Pelileo', 'codigo' => '1807', 'tipo' => 'cantón', 'orden' => 7],
                            ['nombre' => 'Santiago de Píllaro', 'codigo' => '1808', 'tipo' => 'cantón', 'orden' => 8],
                            ['nombre' => 'Tisaleo', 'codigo' => '1809', 'tipo' => 'cantón', 'orden' => 9],
                        ],
                    ],

                    // ==============================================================
                    // ZAMORA CHINCHIPE
                    // ==============================================================

                    [
                        'nombre' => 'Zamora Chinchipe',
                        'codigo' => '19',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 19,

                        'ciudades' => [
                            ['nombre' => 'Zamora', 'codigo' => '1901', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Chinchipe', 'codigo' => '1902', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Nangaritza', 'codigo' => '1903', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Yacuambi', 'codigo' => '1904', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Yantzaza', 'codigo' => '1905', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'El Pangui', 'codigo' => '1906', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Centinela del Cóndor', 'codigo' => '1907', 'tipo' => 'cantón', 'orden' => 7],
                            ['nombre' => 'Palanda', 'codigo' => '1908', 'tipo' => 'cantón', 'orden' => 8],
                            ['nombre' => 'Paquisha', 'codigo' => '1909', 'tipo' => 'cantón', 'orden' => 9],
                        ],
                    ],

                    // ==============================================================
                    // GALÁPAGOS
                    // ==============================================================

                    [
                        'nombre' => 'Galápagos',
                        'codigo' => '20',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 20,

                        'ciudades' => [
                            ['nombre' => 'San Cristóbal', 'codigo' => '2001', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Isabela', 'codigo' => '2002', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Santa Cruz', 'codigo' => '2003', 'tipo' => 'cantón', 'orden' => 3],
                        ],
                    ],

                    // ==============================================================
                    // SUCUMBÍOS
                    // ==============================================================

                    [
                        'nombre' => 'Sucumbíos',
                        'codigo' => '21',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 21,

                        'ciudades' => [
                            ['nombre' => 'Lago Agrio', 'codigo' => '2101', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Gonzalo Pizarro', 'codigo' => '2102', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Putumayo', 'codigo' => '2103', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Shushufindi', 'codigo' => '2104', 'tipo' => 'cantón', 'orden' => 4],
                            ['nombre' => 'Sucumbíos', 'codigo' => '2105', 'tipo' => 'cantón', 'orden' => 5],
                            ['nombre' => 'Cascales', 'codigo' => '2106', 'tipo' => 'cantón', 'orden' => 6],
                            ['nombre' => 'Cuyabeno', 'codigo' => '2107', 'tipo' => 'cantón', 'orden' => 7],
                        ],
                    ],

                    // ==============================================================
                    // ORELLANA
                    // ==============================================================

                    [
                        'nombre' => 'Orellana',
                        'codigo' => '22',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 22,

                        'ciudades' => [
                            ['nombre' => 'Francisco de Orellana', 'codigo' => '2201', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'Aguarico', 'codigo' => '2202', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'La Joya de los Sachas', 'codigo' => '2203', 'tipo' => 'cantón', 'orden' => 3],
                            ['nombre' => 'Loreto', 'codigo' => '2204', 'tipo' => 'cantón', 'orden' => 4],
                        ],
                    ],

                    // ==============================================================
                    // SANTO DOMINGO DE LOS TSÁCHILAS
                    // ==============================================================

                    [
                        'nombre' => 'Santo Domingo de los Tsáchilas',
                        'codigo' => '23',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 23,

                        'ciudades' => [
                            ['nombre' => 'Santo Domingo', 'codigo' => '2301', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'La Concordia', 'codigo' => '2302', 'tipo' => 'cantón', 'orden' => 2],
                        ],
                    ],

                    // ==============================================================
                    // SANTA ELENA
                    // ==============================================================

                    [
                        'nombre' => 'Santa Elena',
                        'codigo' => '24',
                        'tipo' => 'provincia',
                        'label' => 'Provincia',
                        'orden' => 24,

                        'ciudades' => [
                            ['nombre' => 'Santa Elena', 'codigo' => '2401', 'tipo' => 'cantón', 'orden' => 1],
                            ['nombre' => 'La Libertad', 'codigo' => '2402', 'tipo' => 'cantón', 'orden' => 2],
                            ['nombre' => 'Salinas', 'codigo' => '2403', 'tipo' => 'cantón', 'orden' => 3],
                        ],
                    ],

                ],
            ],


            /*
|--------------------------------------------------------------------------
| PARAGUAY
|--------------------------------------------------------------------------
*/

            'PY' => [

                'nombre' => 'Paraguay',
                'codigo_iso2' => 'PY',
                'codigo_iso3' => 'PRY',
                'codigo_telefonico' => '595',

                'division_label' => 'Departamento',
                'localidad_label' => 'Distrito / Ciudad',

                'divisiones' => [

                    [
                        'nombre' => 'Alto Paraguay',
                        'codigo' => '16',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 1,

                        'ciudades' => [
                            [
                                'nombre' => 'Fuerte Olimpo',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Bahía Negra',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Puerto Casado',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Alto Paraná',
                        'codigo' => '10',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 2,

                        'ciudades' => [
                            [
                                'nombre' => 'Ciudad del Este',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Presidente Franco',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Hernandarias',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Minga Guazú',
                                'tipo' => 'distrito',
                                'orden' => 4,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Amambay',
                        'codigo' => '13',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 3,

                        'ciudades' => [
                            [
                                'nombre' => 'Pedro Juan Caballero',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Bella Vista Norte',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Capitán Bado',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Boquerón',
                        'codigo' => '19',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 4,

                        'ciudades' => [
                            [
                                'nombre' => 'Filadelfia',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Loma Plata',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Mariscal Estigarribia',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Caaguazú',
                        'codigo' => '5',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 5,

                        'ciudades' => [
                            [
                                'nombre' => 'Caaguazú',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Coronel Oviedo',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'J. Eulogio Estigarribia',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Repatriación',
                                'tipo' => 'distrito',
                                'orden' => 4,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Caazapá',
                        'codigo' => '6',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 6,

                        'ciudades' => [
                            [
                                'nombre' => 'Caazapá',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Abaí',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'San Juan Nepomuceno',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Canindeyú',
                        'codigo' => '14',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 7,

                        'ciudades' => [
                            [
                                'nombre' => 'Salto del Guairá',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Curuguaty',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Katueté',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Corpus Christi',
                                'tipo' => 'distrito',
                                'orden' => 4,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Central',
                        'codigo' => '11',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 8,

                        'ciudades' => [
                            [
                                'nombre' => 'San Lorenzo',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Luque',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Fernando de la Mora',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Capiatá',
                                'tipo' => 'distrito',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Lambaré',
                                'tipo' => 'distrito',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Mariano Roque Alonso',
                                'tipo' => 'distrito',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Limpio',
                                'tipo' => 'distrito',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Ñemby',
                                'tipo' => 'distrito',
                                'orden' => 8,
                            ],
                            [
                                'nombre' => 'Villa Elisa',
                                'tipo' => 'distrito',
                                'orden' => 9,
                            ],
                            [
                                'nombre' => 'Itauguá',
                                'tipo' => 'distrito',
                                'orden' => 10,
                            ],
                            [
                                'nombre' => 'Areguá',
                                'tipo' => 'distrito',
                                'orden' => 11,
                            ],
                            [
                                'nombre' => 'Itá',
                                'tipo' => 'distrito',
                                'orden' => 12,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Concepción',
                        'codigo' => '1',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 9,

                        'ciudades' => [
                            [
                                'nombre' => 'Concepción',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Horqueta',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Belén',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Yby Yaú',
                                'tipo' => 'distrito',
                                'orden' => 4,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Cordillera',
                        'codigo' => '3',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 10,

                        'ciudades' => [
                            [
                                'nombre' => 'Caacupé',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Eusebio Ayala',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Tobatí',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Piribebuy',
                                'tipo' => 'distrito',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Altos',
                                'tipo' => 'distrito',
                                'orden' => 5,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Guairá',
                        'codigo' => '4',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 11,

                        'ciudades' => [
                            [
                                'nombre' => 'Villarrica',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Independencia',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Colonia Independencia',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Mbocayaty',
                                'tipo' => 'distrito',
                                'orden' => 4,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Itapúa',
                        'codigo' => '7',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 12,

                        'ciudades' => [
                            [
                                'nombre' => 'Encarnación',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Hohenau',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Obligado',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Bella Vista',
                                'tipo' => 'distrito',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Natalio',
                                'tipo' => 'distrito',
                                'orden' => 5,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Misiones',
                        'codigo' => '8',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 13,

                        'ciudades' => [
                            [
                                'nombre' => 'San Juan Bautista',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'San Ignacio',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Santa Rosa',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Ayolas',
                                'tipo' => 'distrito',
                                'orden' => 4,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Ñeembucú',
                        'codigo' => '12',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 14,

                        'ciudades' => [
                            [
                                'nombre' => 'Pilar',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Alberdi',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Ayolas',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Paraguarí',
                        'codigo' => '9',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 15,

                        'ciudades' => [
                            [
                                'nombre' => 'Paraguarí',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Carapeguá',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Yaguarón',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Pirayú',
                                'tipo' => 'distrito',
                                'orden' => 4,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Presidente Hayes',
                        'codigo' => '15',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 16,

                        'ciudades' => [
                            [
                                'nombre' => 'Villa Hayes',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Benjamín Aceval',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Nanawa',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'San Pedro',
                        'codigo' => '2',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 17,

                        'ciudades' => [
                            [
                                'nombre' => 'San Pedro de Ycuamandiyú',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'San Estanislao',
                                'tipo' => 'distrito',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Santa Rosa del Aguaray',
                                'tipo' => 'distrito',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'General Elizardo Aquino',
                                'tipo' => 'distrito',
                                'orden' => 4,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Asunción',
                        'codigo' => 'ASU',
                        'tipo' => 'distrito_capital',
                        'label' => 'Distrito Capital',
                        'orden' => 18,

                        'ciudades' => [
                            [
                                'nombre' => 'Asunción',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                        ],
                    ],

                ],
            ],

            /*
|--------------------------------------------------------------------------
| PERÚ
|--------------------------------------------------------------------------
*/

            'PE' => [

                'nombre' => 'Perú',
                'codigo_iso2' => 'PE',
                'codigo_iso3' => 'PER',
                'codigo_telefonico' => '51',

                'division_label' => 'Departamento',
                'localidad_label' => 'Provincia / Ciudad',

                'divisiones' => [

                    [
                        'nombre' => 'Amazonas',
                        'codigo' => '01',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 1,

                        'ciudades' => [
                            [
                                'nombre' => 'Chachapoyas',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Bagua',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Bongará',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Condorcanqui',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Luya',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Rodríguez de Mendoza',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Utcubamba',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Áncash',
                        'codigo' => '02',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 2,

                        'ciudades' => [
                            [
                                'nombre' => 'Huaraz',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Aija',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Antonio Raymondi',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Asunción',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Bolognesi',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Carhuaz',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Carlos Fermín Fitzcarrald',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Casma',
                                'tipo' => 'provincia',
                                'orden' => 8,
                            ],
                            [
                                'nombre' => 'Corongo',
                                'tipo' => 'provincia',
                                'orden' => 9,
                            ],
                            [
                                'nombre' => 'Huari',
                                'tipo' => 'provincia',
                                'orden' => 10,
                            ],
                            [
                                'nombre' => 'Huarmey',
                                'tipo' => 'provincia',
                                'orden' => 11,
                            ],
                            [
                                'nombre' => 'Huaylas',
                                'tipo' => 'provincia',
                                'orden' => 12,
                            ],
                            [
                                'nombre' => 'Mariscal Luzuriaga',
                                'tipo' => 'provincia',
                                'orden' => 13,
                            ],
                            [
                                'nombre' => 'Ocros',
                                'tipo' => 'provincia',
                                'orden' => 14,
                            ],
                            [
                                'nombre' => 'Pallasca',
                                'tipo' => 'provincia',
                                'orden' => 15,
                            ],
                            [
                                'nombre' => 'Pomabamba',
                                'tipo' => 'provincia',
                                'orden' => 16,
                            ],
                            [
                                'nombre' => 'Recuay',
                                'tipo' => 'provincia',
                                'orden' => 17,
                            ],
                            [
                                'nombre' => 'Santa',
                                'tipo' => 'provincia',
                                'orden' => 18,
                            ],
                            [
                                'nombre' => 'Sihuas',
                                'tipo' => 'provincia',
                                'orden' => 19,
                            ],
                            [
                                'nombre' => 'Yungay',
                                'tipo' => 'provincia',
                                'orden' => 20,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Apurímac',
                        'codigo' => '03',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 3,

                        'ciudades' => [
                            [
                                'nombre' => 'Abancay',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Andahuaylas',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Antabamba',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Aymaraes',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Cotabambas',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Chincheros',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Grau',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Arequipa',
                        'codigo' => '04',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 4,

                        'ciudades' => [
                            [
                                'nombre' => 'Arequipa',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Camaná',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Caravelí',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Castilla',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Caylloma',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Condesuyos',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Islay',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'La Unión',
                                'tipo' => 'provincia',
                                'orden' => 8,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Ayacucho',
                        'codigo' => '05',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 5,

                        'ciudades' => [
                            [
                                'nombre' => 'Huamanga',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Cangallo',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Huanca Sancos',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Huanta',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'La Mar',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Lucanas',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Parinacochas',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Páucar del Sara Sara',
                                'tipo' => 'provincia',
                                'orden' => 8,
                            ],
                            [
                                'nombre' => 'Sucre',
                                'tipo' => 'provincia',
                                'orden' => 9,
                            ],
                            [
                                'nombre' => 'Víctor Fajardo',
                                'tipo' => 'provincia',
                                'orden' => 10,
                            ],
                            [
                                'nombre' => 'Vilcas Huamán',
                                'tipo' => 'provincia',
                                'orden' => 11,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Cajamarca',
                        'codigo' => '06',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 6,

                        'ciudades' => [
                            [
                                'nombre' => 'Cajamarca',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Cajabamba',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Celendín',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Chota',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Contumazá',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Cutervo',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Hualgayoc',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Jaén',
                                'tipo' => 'provincia',
                                'orden' => 8,
                            ],
                            [
                                'nombre' => 'San Ignacio',
                                'tipo' => 'provincia',
                                'orden' => 9,
                            ],
                            [
                                'nombre' => 'San Marcos',
                                'tipo' => 'provincia',
                                'orden' => 10,
                            ],
                            [
                                'nombre' => 'San Miguel',
                                'tipo' => 'provincia',
                                'orden' => 11,
                            ],
                            [
                                'nombre' => 'San Pablo',
                                'tipo' => 'provincia',
                                'orden' => 12,
                            ],
                            [
                                'nombre' => 'Santa Cruz',
                                'tipo' => 'provincia',
                                'orden' => 13,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Cusco',
                        'codigo' => '08',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 7,

                        'ciudades' => [
                            [
                                'nombre' => 'Cusco',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Acomayo',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Anta',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Calca',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Canas',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Canchis',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Chumbivilcas',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Espinar',
                                'tipo' => 'provincia',
                                'orden' => 8,
                            ],
                            [
                                'nombre' => 'La Convención',
                                'tipo' => 'provincia',
                                'orden' => 9,
                            ],
                            [
                                'nombre' => 'Paruro',
                                'tipo' => 'provincia',
                                'orden' => 10,
                            ],
                            [
                                'nombre' => 'Paucartambo',
                                'tipo' => 'provincia',
                                'orden' => 11,
                            ],
                            [
                                'nombre' => 'Quispicanchi',
                                'tipo' => 'provincia',
                                'orden' => 12,
                            ],
                            [
                                'nombre' => 'Urubamba',
                                'tipo' => 'provincia',
                                'orden' => 13,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Huancavelica',
                        'codigo' => '09',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 8,

                        'ciudades' => [
                            [
                                'nombre' => 'Huancavelica',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Acobamba',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Angaraes',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Castrovirreyna',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Churcampa',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Huaytará',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Tayacaja',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Huánuco',
                        'codigo' => '10',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 9,

                        'ciudades' => [
                            [
                                'nombre' => 'Huánuco',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Ambo',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Dos de Mayo',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Huacaybamba',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Huamalíes',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Leoncio Prado',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Marañón',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Pachitea',
                                'tipo' => 'provincia',
                                'orden' => 8,
                            ],
                            [
                                'nombre' => 'Puerto Inca',
                                'tipo' => 'provincia',
                                'orden' => 9,
                            ],
                            [
                                'nombre' => 'Lauricocha',
                                'tipo' => 'provincia',
                                'orden' => 10,
                            ],
                            [
                                'nombre' => 'Yarowilca',
                                'tipo' => 'provincia',
                                'orden' => 11,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Ica',
                        'codigo' => '11',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 10,

                        'ciudades' => [
                            [
                                'nombre' => 'Ica',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Chincha',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Nazca',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Palpa',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Pisco',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Junín',
                        'codigo' => '12',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 11,

                        'ciudades' => [
                            [
                                'nombre' => 'Huancayo',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Concepción',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Chanchamayo',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Jauja',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Junín',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Satipo',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Tarma',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Yauli',
                                'tipo' => 'provincia',
                                'orden' => 8,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'La Libertad',
                        'codigo' => '13',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 12,

                        'ciudades' => [
                            [
                                'nombre' => 'Trujillo',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Ascope',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Bolívar',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Chepén',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Gran Chimú',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Julcán',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Otuzco',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Pacasmayo',
                                'tipo' => 'provincia',
                                'orden' => 8,
                            ],
                            [
                                'nombre' => 'Pataz',
                                'tipo' => 'provincia',
                                'orden' => 9,
                            ],
                            [
                                'nombre' => 'Sánchez Carrión',
                                'tipo' => 'provincia',
                                'orden' => 10,
                            ],
                            [
                                'nombre' => 'Santiago de Chuco',
                                'tipo' => 'provincia',
                                'orden' => 11,
                            ],
                            [
                                'nombre' => 'Virú',
                                'tipo' => 'provincia',
                                'orden' => 12,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Lambayeque',
                        'codigo' => '14',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 13,

                        'ciudades' => [
                            [
                                'nombre' => 'Chiclayo',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Ferreñafe',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Lambayeque',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Lima',
                        'codigo' => '15',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 14,

                        'ciudades' => [
                            [
                                'nombre' => 'Lima',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Barranca',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Cajatambo',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Canta',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Cañete',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Huaral',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Huarochirí',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Huaura',
                                'tipo' => 'provincia',
                                'orden' => 8,
                            ],
                            [
                                'nombre' => 'Oyón',
                                'tipo' => 'provincia',
                                'orden' => 9,
                            ],
                            [
                                'nombre' => 'Yauyos',
                                'tipo' => 'provincia',
                                'orden' => 10,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Loreto',
                        'codigo' => '16',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 15,

                        'ciudades' => [
                            [
                                'nombre' => 'Maynas',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Alto Amazonas',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Loreto',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Mariscal Ramón Castilla',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Requena',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Ucayali',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Datem del Marañón',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Putumayo',
                                'tipo' => 'provincia',
                                'orden' => 8,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Madre de Dios',
                        'codigo' => '17',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 16,

                        'ciudades' => [
                            [
                                'nombre' => 'Tambopata',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Manu',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Tahuamanu',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Moquegua',
                        'codigo' => '18',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 17,

                        'ciudades' => [
                            [
                                'nombre' => 'Mariscal Nieto',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'General Sánchez Cerro',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Ilo',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Pasco',
                        'codigo' => '19',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 18,

                        'ciudades' => [
                            [
                                'nombre' => 'Pasco',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Daniel Alcides Carrión',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Oxapampa',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Piura',
                        'codigo' => '20',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 19,

                        'ciudades' => [
                            [
                                'nombre' => 'Piura',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Ayabaca',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Huancabamba',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Morropón',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Paita',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Sechura',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Sullana',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Talara',
                                'tipo' => 'provincia',
                                'orden' => 8,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Puno',
                        'codigo' => '21',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 20,

                        'ciudades' => [
                            [
                                'nombre' => 'Puno',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Azángaro',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Carabaya',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Chucuito',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'El Collao',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Huancané',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Lampa',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Melgar',
                                'tipo' => 'provincia',
                                'orden' => 8,
                            ],
                            [
                                'nombre' => 'Moho',
                                'tipo' => 'provincia',
                                'orden' => 9,
                            ],
                            [
                                'nombre' => 'San Antonio de Putina',
                                'tipo' => 'provincia',
                                'orden' => 10,
                            ],
                            [
                                'nombre' => 'San Román',
                                'tipo' => 'provincia',
                                'orden' => 11,
                            ],
                            [
                                'nombre' => 'Sandia',
                                'tipo' => 'provincia',
                                'orden' => 12,
                            ],
                            [
                                'nombre' => 'Yunguyo',
                                'tipo' => 'provincia',
                                'orden' => 13,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'San Martín',
                        'codigo' => '22',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 21,

                        'ciudades' => [
                            [
                                'nombre' => 'Moyobamba',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Bellavista',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'El Dorado',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Huallaga',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Lamas',
                                'tipo' => 'provincia',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Mariscal Cáceres',
                                'tipo' => 'provincia',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Picota',
                                'tipo' => 'provincia',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Rioja',
                                'tipo' => 'provincia',
                                'orden' => 8,
                            ],
                            [
                                'nombre' => 'San Martín',
                                'tipo' => 'provincia',
                                'orden' => 9,
                            ],
                            [
                                'nombre' => 'Tocache',
                                'tipo' => 'provincia',
                                'orden' => 10,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Tacna',
                        'codigo' => '23',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 22,

                        'ciudades' => [
                            [
                                'nombre' => 'Tacna',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Candarave',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Jorge Basadre',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Tarata',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Tumbes',
                        'codigo' => '24',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 23,

                        'ciudades' => [
                            [
                                'nombre' => 'Tumbes',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Contralmirante Villar',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Zarumilla',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Ucayali',
                        'codigo' => '25',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 24,

                        'ciudades' => [
                            [
                                'nombre' => 'Coronel Portillo',
                                'tipo' => 'provincia',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Atalaya',
                                'tipo' => 'provincia',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Padre Abad',
                                'tipo' => 'provincia',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Purús',
                                'tipo' => 'provincia',
                                'orden' => 4,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Callao',
                        'codigo' => '07',
                        'tipo' => 'provincia_constitucional',
                        'label' => 'Provincia Constitucional',
                        'orden' => 25,

                        'ciudades' => [
                            [
                                'nombre' => 'Callao',
                                'tipo' => 'distrito',
                                'orden' => 1,
                            ],
                        ],
                    ],

                ],
            ],

            /*
|--------------------------------------------------------------------------
| URUGUAY
|--------------------------------------------------------------------------
*/

            'UY' => [

                'nombre' => 'Uruguay',
                'codigo_iso2' => 'UY',
                'codigo_iso3' => 'URY',
                'codigo_telefonico' => '598',

                'division_label' => 'Departamento',
                'localidad_label' => 'Ciudad / Localidad',

                'divisiones' => [

                    [
                        'nombre' => 'Artigas',
                        'codigo' => 'AR',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 1,

                        'ciudades' => [
                            [
                                'nombre' => 'Artigas',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Bella Unión',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Tomás Gomensoro',
                                'tipo' => 'localidad',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Canelones',
                        'codigo' => 'CA',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 2,

                        'ciudades' => [
                            [
                                'nombre' => 'Canelones',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Ciudad de la Costa',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Las Piedras',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Pando',
                                'tipo' => 'ciudad',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'La Paz',
                                'tipo' => 'ciudad',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Santa Lucía',
                                'tipo' => 'ciudad',
                                'orden' => 6,
                            ],
                            [
                                'nombre' => 'Barros Blancos',
                                'tipo' => 'ciudad',
                                'orden' => 7,
                            ],
                            [
                                'nombre' => 'Progreso',
                                'tipo' => 'ciudad',
                                'orden' => 8,
                            ],
                            [
                                'nombre' => 'Atlántida',
                                'tipo' => 'ciudad',
                                'orden' => 9,
                            ],
                            [
                                'nombre' => 'San Ramón',
                                'tipo' => 'ciudad',
                                'orden' => 10,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Cerro Largo',
                        'codigo' => 'CL',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 3,

                        'ciudades' => [
                            [
                                'nombre' => 'Melo',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Río Branco',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Fraile Muerto',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Colonia',
                        'codigo' => 'CO',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 4,

                        'ciudades' => [
                            [
                                'nombre' => 'Colonia del Sacramento',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Carmelo',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Nueva Palmira',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Juan Lacaze',
                                'tipo' => 'ciudad',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Nueva Helvecia',
                                'tipo' => 'ciudad',
                                'orden' => 5,
                            ],
                            [
                                'nombre' => 'Rosario',
                                'tipo' => 'ciudad',
                                'orden' => 6,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Durazno',
                        'codigo' => 'DU',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 5,

                        'ciudades' => [
                            [
                                'nombre' => 'Durazno',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Sarandí del Yí',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Flores',
                        'codigo' => 'FS',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 6,

                        'ciudades' => [
                            [
                                'nombre' => 'Trinidad',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Ismael Cortinas',
                                'tipo' => 'localidad',
                                'orden' => 2,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Florida',
                        'codigo' => 'FD',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 7,

                        'ciudades' => [
                            [
                                'nombre' => 'Florida',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Sarandí Grande',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Casupá',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Lavalleja',
                        'codigo' => 'LA',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 8,

                        'ciudades' => [
                            [
                                'nombre' => 'Minas',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'José Pedro Varela',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'José Batlle y Ordóñez',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Maldonado',
                        'codigo' => 'MA',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 9,

                        'ciudades' => [
                            [
                                'nombre' => 'Maldonado',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Punta del Este',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'San Carlos',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Piriápolis',
                                'tipo' => 'ciudad',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'Pan de Azúcar',
                                'tipo' => 'ciudad',
                                'orden' => 5,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Montevideo',
                        'codigo' => 'MO',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 10,

                        'ciudades' => [
                            [
                                'nombre' => 'Montevideo',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Paysandú',
                        'codigo' => 'PA',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 11,

                        'ciudades' => [
                            [
                                'nombre' => 'Paysandú',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Guichón',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Quebracho',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Río Negro',
                        'codigo' => 'RN',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 12,

                        'ciudades' => [
                            [
                                'nombre' => 'Fray Bentos',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Young',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Nuevo Berlín',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Rivera',
                        'codigo' => 'RV',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 13,

                        'ciudades' => [
                            [
                                'nombre' => 'Rivera',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Tranqueras',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Minas de Corrales',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Rocha',
                        'codigo' => 'RO',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 14,

                        'ciudades' => [
                            [
                                'nombre' => 'Rocha',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Chuy',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Castillos',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                            [
                                'nombre' => 'Lascano',
                                'tipo' => 'ciudad',
                                'orden' => 4,
                            ],
                            [
                                'nombre' => 'La Paloma',
                                'tipo' => 'ciudad',
                                'orden' => 5,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Salto',
                        'codigo' => 'SA',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 15,

                        'ciudades' => [
                            [
                                'nombre' => 'Salto',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Constitución',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Belén',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'San José',
                        'codigo' => 'SJ',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 16,

                        'ciudades' => [
                            [
                                'nombre' => 'San José de Mayo',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Libertad',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Ciudad del Plata',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Soriano',
                        'codigo' => 'SO',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 17,

                        'ciudades' => [
                            [
                                'nombre' => 'Mercedes',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Dolores',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Cardona',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Tacuarembó',
                        'codigo' => 'TA',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 18,

                        'ciudades' => [
                            [
                                'nombre' => 'Tacuarembó',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Paso de los Toros',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'San Gregorio de Polanco',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                        ],
                    ],

                    [
                        'nombre' => 'Treinta y Tres',
                        'codigo' => 'TT',
                        'tipo' => 'departamento',
                        'label' => 'Departamento',
                        'orden' => 19,

                        'ciudades' => [
                            [
                                'nombre' => 'Treinta y Tres',
                                'tipo' => 'ciudad',
                                'orden' => 1,
                            ],
                            [
                                'nombre' => 'Vergara',
                                'tipo' => 'ciudad',
                                'orden' => 2,
                            ],
                            [
                                'nombre' => 'Santa Clara de Olimar',
                                'tipo' => 'ciudad',
                                'orden' => 3,
                            ],
                        ],
                    ],

                ],
            ],

                /*
|--------------------------------------------------------------------------
| VENEZUELA
|--------------------------------------------------------------------------
*/

                'VE' => [

                    'nombre' => 'Venezuela',
                    'codigo_iso2' => 'VE',
                    'codigo_iso3' => 'VEN',
                    'codigo_telefonico' => '58',

                    'division_label' => 'Estado',
                    'localidad_label' => 'Municipio / Ciudad',

                    'divisiones' => [

                        [
                            'nombre' => 'Distrito Capital',
                            'codigo' => 'A',
                            'tipo' => 'distrito',
                            'label' => 'Distrito',
                            'orden' => 1,

                            'ciudades' => [
                                [
                                    'nombre' => 'Caracas',
                                    'tipo' => 'ciudad',
                                    'orden' => 1,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Amazonas',
                            'codigo' => 'Z',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 2,

                            'ciudades' => [
                                [
                                    'nombre' => 'Puerto Ayacucho',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'San Fernando de Atabapo',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Maroa',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'San Carlos de Río Negro',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Anzoátegui',
                            'codigo' => 'B',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 3,

                            'ciudades' => [
                                [
                                    'nombre' => 'Barcelona',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Puerto La Cruz',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Lechería',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'El Tigre',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                                [
                                    'nombre' => 'Anaco',
                                    'tipo' => 'municipio',
                                    'orden' => 5,
                                ],
                                [
                                    'nombre' => 'Cantaura',
                                    'tipo' => 'municipio',
                                    'orden' => 6,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Apure',
                            'codigo' => 'C',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 4,

                            'ciudades' => [
                                [
                                    'nombre' => 'San Fernando de Apure',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Biruaca',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Achaguas',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Bruzual',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                                [
                                    'nombre' => 'Guasdualito',
                                    'tipo' => 'municipio',
                                    'orden' => 5,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Aragua',
                            'codigo' => 'D',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 5,

                            'ciudades' => [
                                [
                                    'nombre' => 'Maracay',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Turmero',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'La Victoria',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Cagua',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                                [
                                    'nombre' => 'Villa de Cura',
                                    'tipo' => 'municipio',
                                    'orden' => 5,
                                ],
                                [
                                    'nombre' => 'El Limón',
                                    'tipo' => 'municipio',
                                    'orden' => 6,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Barinas',
                            'codigo' => 'E',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 6,

                            'ciudades' => [
                                [
                                    'nombre' => 'Barinas',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Barinitas',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Socopó',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Santa Bárbara de Barinas',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Bolívar',
                            'codigo' => 'F',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 7,

                            'ciudades' => [
                                [
                                    'nombre' => 'Ciudad Bolívar',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Ciudad Guayana',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Upata',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Tumeremo',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                                [
                                    'nombre' => 'Caicara del Orinoco',
                                    'tipo' => 'municipio',
                                    'orden' => 5,
                                ],
                                [
                                    'nombre' => 'Santa Elena de Uairén',
                                    'tipo' => 'municipio',
                                    'orden' => 6,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Carabobo',
                            'codigo' => 'G',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 8,

                            'ciudades' => [
                                [
                                    'nombre' => 'Valencia',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Puerto Cabello',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Naguanagua',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'San Diego',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                                [
                                    'nombre' => 'Guacara',
                                    'tipo' => 'municipio',
                                    'orden' => 5,
                                ],
                                [
                                    'nombre' => 'Mariara',
                                    'tipo' => 'municipio',
                                    'orden' => 6,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Cojedes',
                            'codigo' => 'H',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 9,

                            'ciudades' => [
                                [
                                    'nombre' => 'San Carlos',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Tinaquillo',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Tinaco',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'El Baúl',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Delta Amacuro',
                            'codigo' => 'Y',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 10,

                            'ciudades' => [
                                [
                                    'nombre' => 'Tucupita',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Pedernales',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Curiapo',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Falcón',
                            'codigo' => 'I',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 11,

                            'ciudades' => [
                                [
                                    'nombre' => 'Coro',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Punto Fijo',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Chichiriviche',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Tucacas',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                                [
                                    'nombre' => 'Dabajuro',
                                    'tipo' => 'municipio',
                                    'orden' => 5,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Guárico',
                            'codigo' => 'J',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 12,

                            'ciudades' => [
                                [
                                    'nombre' => 'San Juan de los Morros',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Calabozo',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Valle de la Pascua',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Zaraza',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                                [
                                    'nombre' => 'Altagracia de Orituco',
                                    'tipo' => 'municipio',
                                    'orden' => 5,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Lara',
                            'codigo' => 'K',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 13,

                            'ciudades' => [
                                [
                                    'nombre' => 'Barquisimeto',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Cabudare',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Carora',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'El Tocuyo',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                                [
                                    'nombre' => 'Quíbor',
                                    'tipo' => 'municipio',
                                    'orden' => 5,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'La Guaira',
                            'codigo' => 'W',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 14,

                            'ciudades' => [
                                [
                                    'nombre' => 'La Guaira',
                                    'tipo' => 'ciudad',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Catia La Mar',
                                    'tipo' => 'ciudad',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Maiquetía',
                                    'tipo' => 'ciudad',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Caraballeda',
                                    'tipo' => 'ciudad',
                                    'orden' => 4,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Mérida',
                            'codigo' => 'L',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 15,

                            'ciudades' => [
                                [
                                    'nombre' => 'Mérida',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'El Vigía',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Ejido',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Tovar',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                                [
                                    'nombre' => 'Mucuchíes',
                                    'tipo' => 'municipio',
                                    'orden' => 5,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Miranda',
                            'codigo' => 'M',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 16,

                            'ciudades' => [
                                [
                                    'nombre' => 'Los Teques',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Baruta',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Chacao',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Petare',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                                [
                                    'nombre' => 'Guarenas',
                                    'tipo' => 'municipio',
                                    'orden' => 5,
                                ],
                                [
                                    'nombre' => 'Guatire',
                                    'tipo' => 'municipio',
                                    'orden' => 6,
                                ],
                                [
                                    'nombre' => 'Charallave',
                                    'tipo' => 'municipio',
                                    'orden' => 7,
                                ],
                                [
                                    'nombre' => 'Ocumare del Tuy',
                                    'tipo' => 'municipio',
                                    'orden' => 8,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Monagas',
                            'codigo' => 'N',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 17,

                            'ciudades' => [
                                [
                                    'nombre' => 'Maturín',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Punta de Mata',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Caripe',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Caripito',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Nueva Esparta',
                            'codigo' => 'O',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 18,

                            'ciudades' => [
                                [
                                    'nombre' => 'La Asunción',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Porlamar',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Pampatar',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Juan Griego',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Portuguesa',
                            'codigo' => 'P',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 19,

                            'ciudades' => [
                                [
                                    'nombre' => 'Guanare',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Acarigua',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Araure',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Guanarito',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                                [
                                    'nombre' => 'Biscucuy',
                                    'tipo' => 'municipio',
                                    'orden' => 5,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Sucre',
                            'codigo' => 'R',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 20,

                            'ciudades' => [
                                [
                                    'nombre' => 'Cumaná',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Carúpano',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Güiria',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Cariaco',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Táchira',
                            'codigo' => 'S',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 21,

                            'ciudades' => [
                                [
                                    'nombre' => 'San Cristóbal',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Táriba',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Rubio',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'San Antonio del Táchira',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                                [
                                    'nombre' => 'La Grita',
                                    'tipo' => 'municipio',
                                    'orden' => 5,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Trujillo',
                            'codigo' => 'T',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 22,

                            'ciudades' => [
                                [
                                    'nombre' => 'Trujillo',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Valera',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Boconó',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Carache',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Yaracuy',
                            'codigo' => 'U',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 23,

                            'ciudades' => [
                                [
                                    'nombre' => 'San Felipe',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Yaritagua',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Chivacoa',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Nirgua',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                            ],
                        ],

                        [
                            'nombre' => 'Zulia',
                            'codigo' => 'V',
                            'tipo' => 'estado',
                            'label' => 'Estado',
                            'orden' => 24,

                            'ciudades' => [
                                [
                                    'nombre' => 'Maracaibo',
                                    'tipo' => 'municipio',
                                    'orden' => 1,
                                ],
                                [
                                    'nombre' => 'Cabimas',
                                    'tipo' => 'municipio',
                                    'orden' => 2,
                                ],
                                [
                                    'nombre' => 'Ciudad Ojeda',
                                    'tipo' => 'municipio',
                                    'orden' => 3,
                                ],
                                [
                                    'nombre' => 'Santa Rita',
                                    'tipo' => 'municipio',
                                    'orden' => 4,
                                ],
                                [
                                    'nombre' => 'Machiques',
                                    'tipo' => 'municipio',
                                    'orden' => 5,
                                ],
                                [
                                    'nombre' => 'San Francisco',
                                    'tipo' => 'municipio',
                                    'orden' => 6,
                                ],
                                [
                                    'nombre' => 'La Villa del Rosario',
                                    'tipo' => 'municipio',
                                    'orden' => 7,
                                ],
                                [
                                    'nombre' => 'Los Puertos de Altagracia',
                                    'tipo' => 'municipio',
                                    'orden' => 8,
                                ],
                            ],
                        ],
                    ],
                ],


];
