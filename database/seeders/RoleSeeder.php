<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Crear los roles y asignar sus permisos.
     */
    public function run(): void
    {
        // ==========================================================
        // ADMINISTRADOR GENERAL
        // ==========================================================

        $administradorGeneral = Role::firstOrCreate([
            'name' => 'Administrador General',
            'guard_name' => 'web',
        ]);

        // El Administrador General tendrá todos los permisos.
        $administradorGeneral->syncPermissions(
            \Spatie\Permission\Models\Permission::all()
        );


        // ==========================================================
        // USUARIO
        // ==========================================================

        $usuario = Role::firstOrCreate([
            'name' => 'Usuario',
            'guard_name' => 'web',
        ]);

        $permisosUsuario = [

            // ======================================================
            // DASHBOARD
            // ======================================================

            'dashboard.ver',


            // ======================================================
            // PUBLICACIONES
            // ======================================================

            'publicaciones.ver',
            'publicaciones.ver_detalle',
            'publicaciones.crear',
            'publicaciones.editar_propias',
            'publicaciones.eliminar_propias',


            // ======================================================
            // FAVORITOS
            // ======================================================

            'favoritos.ver',
            'favoritos.agregar',
            'favoritos.eliminar',


            // ======================================================
            // REACCIONES
            // ======================================================

            'reacciones.crear',
            'reacciones.eliminar',


            // ======================================================
            // COMENTARIOS
            // ======================================================

            'comentarios.ver',
            'comentarios.crear',
            'comentarios.editar_propios',
            'comentarios.eliminar_propios',


            // ======================================================
            // MENSAJES
            // ======================================================

            'mensajes.ver',
            'mensajes.enviar',
            'mensajes.eliminar',


            // ======================================================
            // COMPRAS
            // ======================================================

            'compras.ver_propias',
            'compras.crear',
            'compras.cancelar_propias',


            // ======================================================
            // VENTAS
            // ======================================================

            'ventas.ver_propias',
            'ventas.gestionar_propias',


            // ======================================================
            // CALIFICACIONES
            // ======================================================

            'calificaciones.ver',
            'calificaciones.crear',
            'calificaciones.editar_propias',
            'calificaciones.eliminar_propias',


            // ======================================================
            // REPORTES
            // ======================================================

            'reportes.crear',
            'reportes.ver_propios',


            // ======================================================
            // PERFIL / MI CUENTA
            // ======================================================

            'perfil.ver',
            'perfil.editar',
            'perfil.cambiar_password',

        ];


        $usuario->syncPermissions($permisosUsuario);
    }
}

