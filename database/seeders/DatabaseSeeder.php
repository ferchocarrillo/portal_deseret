<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==========================================================
        // PERMISOS
        // ==========================================================

        $this->call([
            PermissionSeeder::class,
        ]);


        // ==========================================================
        // ROLES Y PERMISOS
        // ==========================================================

        $this->call([
            RoleSeeder::class,
        ]);


        // ==========================================================
        // USUARIO ADMINISTRADOR GENERAL
        // ==========================================================

        $this->call([
            AdminUserSeeder::class,
        ]);


        // ==========================================================
        // SOLICITUDES DE INGRESO DE PRUEBA
        // ==========================================================

        $this->call([
            SolicitudIngresoSeeder::class,
        ]);

        $this->call([
            UbicacionesSeeder::class,
        ]);


        $this->call([
            DivisionesAdministrativasSeeder::class,
        ]);
        $this->call([
            CiudadesSeeder::class,
        ]);
        $this->call([
            SkillsSeeder::class,
        ]);
        $this->call([
            MarketplaceCategoriasSeeder::class,
        ]);
        $this->call([
            ClasificadosSeeder::class,
        ]);
        $this->call([
            EmprendimientoCategoriaSeeder::class,
        ]);
        $this->call([
            EmprendimientoSubcategoriaSeeder::class,
        ]);

    }
}

