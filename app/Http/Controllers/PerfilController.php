<?php

namespace App\Http\Controllers;

use App\Models\UserInformacionRecepcion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class PerfilController extends Controller
{
    /**
     * Mostrar el perfil del usuario autenticado.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        $user->load([
            'formaciones',
            'experiencias',
            'skills',
            'hojaVidaPrincipal',
            'informacionRecepcion',
        ]);

        return Inertia::render('Perfil/Show', [
            'usuario' => $user,
            'formaciones' => $user->formaciones,
            'experiencias' => $user->experiencias,
            'skills' => $user->skills,
            'hoja_vida' => $user->hojaVidaPrincipal,
            'informacion_recepcion' => $user->informacionRecepcion,
        ]);
    }

    /**
     * Actualizar información básica del perfil.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'fecha_nacimiento' => [
                'nullable',
                'date',
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:30',
            ],
        ]);

        $emailCambio = $user->email !== $validated['email'];

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->fecha_nacimiento = $validated['fecha_nacimiento'] ?? null;
        $user->telefono = $validated['telefono'] ?? null;

        if ($emailCambio) {
            $user->email_verified_at = null;
        }

        $user->save();

        return back()->with(
            'success',
            'La información de tu perfil fue actualizada correctamente.'
        );
    }

    /**
     * Actualizar fotografía del perfil.
     */
    public function updateFoto(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'foto' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $foto = $request->file('foto');

        $ruta = $foto->store(
            'usuarios/fotos',
            'public'
        );

        $user->foto = $ruta;
        $user->save();

        return back()->with(
            'success',
            'La fotografía de perfil fue actualizada correctamente.'
        );
    }

    /**
     * Actualizar contraseña.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => [
                'required',
                'current_password',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ]);

        $user->password = Hash::make(
            $validated['password']
        );

        $user->debe_cambiar_password = false;
        $user->save();

        return back()->with(
            'success',
            'La contraseña fue actualizada correctamente.'
        );
    }

    // ==========================================================
    // RESIDENCIA Y GEORREFERENCIACIÓN
    // ==========================================================

    /**
     * Guardar o actualizar la información de residencia y coordenadas.
     */
    public function storeInformacionRecepcion(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'pais'                        => ['required', 'string', 'max:100'],
            'departamento'                => ['required', 'string', 'max:100'],
            'ciudad'                      => ['required', 'string', 'max:100'],
            'codigo_postal'               => ['nullable', 'string', 'max:20'],
            'barrio'                      => ['nullable', 'string', 'max:120'],
            'tipo_via'                    => ['nullable', 'string', 'max:50'],
            'numero_via'                  => ['required', 'string', 'max:150'],
            'complemento'                 => ['nullable', 'string', 'max:150'],
            'complemento_2'               => ['nullable', 'string', 'max:150'],
            'referencia_direccion'        => ['nullable', 'string', 'max:255'],
            'latitud'                     => ['nullable', 'numeric', 'between:-90,90'],
            'longitud'                    => ['nullable', 'numeric', 'between:-180,180'],
            'telefono_contacto'           => ['nullable', 'string', 'max:30'],
            'whatsapp'                    => ['nullable', 'string', 'max:30'],
            'correo_contacto'             => ['nullable', 'email', 'max:150'],
            'medio_contacto_preferido'    => ['nullable', 'string', 'max:50'],
            'instrucciones_recepcion'     => ['nullable', 'string', 'max:500'],
            'permite_contacto'            => ['nullable', 'boolean'],
            'permite_compartir_direccion' => ['nullable', 'boolean'],
        ]);

        $informacion = UserInformacionRecepcion::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json([
            'message'     => 'Información de residencia guardada correctamente.',
            'informacion' => $informacion,
        ]);
    }

    /**
     * Eliminar la información de residencia.
     */
    public function destroyInformacionRecepcion(Request $request): JsonResponse
    {
        $user = $request->user();

        $informacion = $user->informacionRecepcion;

        if ($informacion) {
            $informacion->delete();
        }

        return response()->json([
            'message' => 'Información de residencia eliminada correctamente.',
        ]);
    }
}
