<?php

namespace App\Http\Controllers;

use App\Models\Clasificado;
use App\Models\Curso;
use App\Models\Emprendimiento;
use App\Models\MarketplacePublicacion;
use App\Models\OfertaEmpleo;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Mostrar listado de usuarios.
     */
    public function index()
    {
        $usuarios = User::query()
            ->with('roles')
            ->orderBy('name')
            ->get();

        $roles = Role::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('Usuarios/Index', [
            'usuarios' => $usuarios,
            'roles'    => $roles,
        ]);
    }

    /**
     * Mostrar información de un usuario.
     */
    public function show(User $user)
    {
        $user->load([
            'roles',
            'permissions',
            'formaciones',
        ]);

        $user->loadCount([
            'publicaciones',
            'favoritos',
            'reacciones',
            'comentarios',
            'calificaciones',
            'calificacionesRecibidas',
            'interacciones',
            'reportes',
            'invitaciones',
            'invitacionesRecibidas',
            'infractions',
        ]);

        return Inertia::render('Usuarios/Show', [
            'usuario' => $user,
        ]);
    }

    /**
     * Mostrar formulario de edición administrativa.
     */
    public function edit(User $user)
    {
        $user->load('roles');

        $roles = Role::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('Usuarios/Edit', [
            'usuario' => $user,
            'roles'   => $roles,
        ]);
    }

    /**
     * Actualizar información, rol, estado y suspensión del usuario.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
            'role' => [
                'required',
                'string',
                'exists:roles,name',
            ],
            'activo' => [
                'required',
                'boolean',
            ],
            'accion_bloqueo' => [
                'nullable',
                'in:mantener,quitar,aplicar',
            ],
            'dias_bloqueo' => [
                'nullable',
                'required_if:accion_bloqueo,aplicar',
                'integer',
                'min:1',
                'max:3650',
            ],
        ]);

        $bloqueadoHasta = $user->bloqueado_hasta;
        $seLevantoSuspension = false;
        $seAplicoSuspension = false;

        $accion = $validated['accion_bloqueo'] ?? 'mantener';

        if ($accion === 'quitar') {
            $bloqueadoHasta = null;
            $seLevantoSuspension = true;
        } elseif ($accion === 'aplicar') {
            $dias = (int) $validated['dias_bloqueo'];
            $bloqueadoHasta = now()->addDays($dias);
            $seAplicoSuspension = true;
        }

        $user->name            = $validated['name'];
        $user->email           = $validated['email'];
        $user->activo          = (bool) $validated['activo'];
        $user->bloqueado_hasta = $bloqueadoHasta;
        $user->save();

        $user->syncRoles([
            $validated['role'],
        ]);

        $estaHabilitado = $user->activo && ($user->bloqueado_hasta === null || now()->gt($user->bloqueado_hasta));

        if ($seLevantoSuspension || ($estaHabilitado && !$seAplicoSuspension)) {
            $this->reactivarPublicacionesUsuario($user->id);
        } elseif (!$user->activo || $seAplicoSuspension) {
            $this->pausarPublicacionesUsuario($user->id);
        }

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'El usuario y sus publicaciones han sido actualizados correctamente.');
    }

    /**
     * Sincroniza dinámicamente el estado de un modelo verificando las columnas reales en BD
     * y respetando las restricciones CHECK de cada tabla.
     */
    private function sincronizarEstadoModelo(string $tabla, $modeloClass, int $userId, bool $activar): void
    {
        $columnaUser = null;
        if (Schema::hasColumn($tabla, 'user_id')) {
            $columnaUser = 'user_id';
        } elseif (Schema::hasColumn($tabla, 'usuario_id')) {
            $columnaUser = 'usuario_id';
        }

        if (!$columnaUser) {
            return;
        }

        $datos = [];

        // Columna numérica/booleana (1 = activo, 0 = inactivo/pausado)
        if (Schema::hasColumn($tabla, 'activo')) {
            $datos['activo'] = $activar ? 1 : 0;
        }

        // Columna string de Marketplace
        if (Schema::hasColumn($tabla, 'estado_publicacion')) {
            $datos['estado_publicacion'] = $activar ? 'activo' : 'pausado';
        }

        // Columna string de estado respetando los CHECK constraints de cada módulo
        if (Schema::hasColumn($tabla, 'estado')) {
            if ($tabla === 'cursos') {
                // Cursos permite: 'publicado', 'borrador', etc.
                $datos['estado'] = $activar ? 'publicado' : 'borrador';
            } elseif ($tabla === 'clasificados') {
                // Clasificados permite: 'publicado', 'pausado', etc.
                $datos['estado'] = $activar ? 'publicado' : 'pausado';
            } elseif ($tabla === 'oferta_empleos') {
                // Empleos suele permitir: 'activa', 'abierta', 'publicada' o 'pausada'
                $datos['estado'] = $activar ? 'activa' : 'pausada';
            } else {
                $datos['estado'] = $activar ? 'activo' : 'pausado';
            }
        }

        if (Schema::hasColumn($tabla, 'pausada')) {
            $datos['pausada'] = $activar ? 0 : 1;
        }

        if (Schema::hasColumn($tabla, 'fecha_suspension')) {
            $datos['fecha_suspension'] = $activar ? null : now();
        }

        if (Schema::hasColumn($tabla, 'motivo_suspension')) {
            $datos['motivo_suspension'] = $activar ? null : 'Suspensión o inhabilitación administrativa de la cuenta';
        }

        if (!empty($datos)) {
            $modeloClass::where($columnaUser, $userId)->update($datos);
        }
    }

    /**
     * Reactivar publicaciones de todos los módulos para un usuario.
     */
    private function reactivarPublicacionesUsuario(int $userId): void
    {
        $this->sincronizarEstadoModelo('marketplace_publicaciones', MarketplacePublicacion::class, $userId, true);
        $this->sincronizarEstadoModelo('servicios', Servicio::class, $userId, true);
        $this->sincronizarEstadoModelo('clasificados', Clasificado::class, $userId, true);
        $this->sincronizarEstadoModelo('emprendimientos', Emprendimiento::class, $userId, true);
        $this->sincronizarEstadoModelo('cursos', Curso::class, $userId, true);
        $this->sincronizarEstadoModelo('oferta_empleos', OfertaEmpleo::class, $userId, true);
    }

    /**
     * Pausar publicaciones de todos los módulos si el usuario es inhabilitado o suspendido.
     */
    private function pausarPublicacionesUsuario(int $userId): void
    {
        $this->sincronizarEstadoModelo('marketplace_publicaciones', MarketplacePublicacion::class, $userId, false);
        $this->sincronizarEstadoModelo('servicios', Servicio::class, $userId, false);
        $this->sincronizarEstadoModelo('clasificados', Clasificado::class, $userId, false);
        $this->sincronizarEstadoModelo('emprendimientos', Emprendimiento::class, $userId, false);
        $this->sincronizarEstadoModelo('cursos', Curso::class, $userId, false);
        $this->sincronizarEstadoModelo('oferta_empleos', OfertaEmpleo::class, $userId, false);
    }

    /**
     * Cambiar contraseña de un usuario.
     */
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'La contraseña ha sido actualizada correctamente.');
    }
}
