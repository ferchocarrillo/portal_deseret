<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    /**
     * Mostrar el módulo de roles y permisos.
     */
    public function index()
    {
        $roles = Role::query()
            ->with('permissions')
            ->orderBy('name')
            ->get();

        $permisos = Permission::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('Configuracion/RolesPermisos', [
            'roles' => $roles,
            'permisos' => $permisos,
        ]);
    }


    /**
     * Actualizar los permisos de un rol.
     */
    public function updatePermissions(
        Request $request,
        Role $role
    ) {
        $validated = $request->validate([
            'permissions' => [
                'array',
            ],

            'permissions.*' => [
                'string',
                'exists:permissions,name',
            ],
        ]);


        $role->syncPermissions(
            $validated['permissions'] ?? []
        );


        return redirect()
            ->route('configuracion.roles-permisos')
            ->with(
                'success',
                'Los permisos del rol han sido actualizados correctamente.'
            );
    }
}
