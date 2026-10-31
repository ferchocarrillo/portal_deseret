<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Crear todos los permisos del sistema.
     */
    public function run(): void
    {
        $permisos = [

            // ==========================================================
            // DASHBOARD
            // ==========================================================

            'dashboard.ver',


            // ==========================================================
            // SOLICITUDES DE INGRESO
            // ==========================================================

            'solicitudes.ver',
            'solicitudes.ver_detalle',
            'solicitudes.aprobar',
            'solicitudes.rechazar',


            // ==========================================================
            // USUARIOS
            // ==========================================================

            'usuarios.ver',
            'usuarios.ver_detalle',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.activar',
            'usuarios.desactivar',
            'usuarios.eliminar',
            'usuarios.restablecer_password',


            // ==========================================================
            // PUBLICACIONES
            // ==========================================================

            'publicaciones.ver',
            'publicaciones.ver_detalle',
            'publicaciones.crear',
            'publicaciones.editar',
            'publicaciones.editar_propias',
            'publicaciones.eliminar',
            'publicaciones.eliminar_propias',
            'publicaciones.aprobar',
            'publicaciones.rechazar',
            'publicaciones.destacar',


            // ==========================================================
            // FAVORITOS
            // ==========================================================

            'favoritos.ver',
            'favoritos.agregar',
            'favoritos.eliminar',


            // ==========================================================
            // REACCIONES
            // ==========================================================

            'reacciones.crear',
            'reacciones.eliminar',


            // ==========================================================
            // COMENTARIOS
            // ==========================================================

            'comentarios.ver',
            'comentarios.crear',
            'comentarios.editar_propios',
            'comentarios.eliminar_propios',
            'comentarios.eliminar',


            // ==========================================================
            // MENSAJES
            // ==========================================================

            'mensajes.ver',
            'mensajes.enviar',
            'mensajes.eliminar',


            // ==========================================================
            // COMPRAS
            // ==========================================================

            'compras.ver_propias',
            'compras.crear',
            'compras.cancelar_propias',


            // ==========================================================
            // VENTAS
            // ==========================================================

            'ventas.ver_propias',
            'ventas.gestionar_propias',


            // ==========================================================
            // CALIFICACIONES
            // ==========================================================

            'calificaciones.ver',
            'calificaciones.crear',
            'calificaciones.editar_propias',
            'calificaciones.eliminar_propias',


            // ==========================================================
            // REPORTES
            // ==========================================================

            'reportes.crear',
            'reportes.ver_propios',
            'reportes.ver',
            'reportes.gestionar',
            'reportes.resolver',


            // ==========================================================
            // PERFIL / MI CUENTA
            // ==========================================================

            'perfil.ver',
            'perfil.editar',
            'perfil.cambiar_password',


            // ==========================================================
            // CONFIGURACIÓN
            // ==========================================================

            'configuracion.ver',
            'configuracion.editar',


            // ==========================================================
            // ROLES
            // ==========================================================

            'roles.ver',
            'roles.crear',
            'roles.editar',
            'roles.eliminar',
            'roles.asignar',


            // ==========================================================
            // PERMISOS
            // ==========================================================

            'permisos.ver',
            'permisos.asignar',

        ];


        foreach ($permisos as $permiso) {

            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web',
            ]);

        }
    }
}

