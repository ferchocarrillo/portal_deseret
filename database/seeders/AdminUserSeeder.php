<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Crear los usuarios base del sistema.
     */
    public function run(): void
    {
        // ==========================================================
        // ADMINISTRADOR GENERAL
        // ==========================================================

        $administrador = User::updateOrCreate(
            [
                'email' => 'admin@portaldeseret.com',
            ],
            [
                'name' => 'Administrador General',
                'password' => Hash::make('Admin12345*'),
                'role' => 'Administrador General',
                'debe_cambiar_password' => false,
            ]
        );

        // Asignar el rol de Spatie
        $administrador->syncRoles([
            'Administrador General',
        ]);

        // ==========================================================
        // USUARIOS DE PRUEBA
        // ==========================================================

        $usuariosPrueba = [
            [
                'email' => 'pruebas.deseret@portaldeseret.com',
                'name' => 'Usuario Pruebas',
            ],
            [
                'email' => 'pruebas2.deseret@portaldeseret.com',
                'name' => 'Usuario Pruebas 2',
            ],
            [
                'email' => 'pruebas3.deseret@portaldeseret.com',
                'name' => 'Usuario Pruebas 3',
            ],
            [
                'email' => 'pruebas4.deseret@portaldeseret.com',
                'name' => 'Usuario Pruebas 4',
            ],
        ];

        foreach ($usuariosPrueba as $datos) {

            $usuario = User::updateOrCreate(
                [
                    'email' => $datos['email'],
                ],
                [
                    'name' => $datos['name'],
                    'password' => Hash::make('123456789'),
                    'role' => 'Usuario',
                    'debe_cambiar_password' => false,
                ]
            );

            // Cada usuario recibe su propio rol
            $usuario->syncRoles([
                'Usuario',
            ]);
        }
    }
}
