<?php

namespace App\Http\Controllers;

use App\Models\UserExperiencia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserExperienciaController extends Controller
{
    /**
     * Guardar experiencia laboral o declaración
     * de ausencia de experiencia.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Caso 1: Declaración de ausencia de experiencia
    |--------------------------------------------------------------------------
    */

        if ($request->boolean('sin_experiencia')) {

            // Solo se permite si no tiene ningún registro laboral.
            if ($user->experiencias()->exists()) {
                throw ValidationException::withMessages([
                    'sin_experiencia' =>
                    'Ya tienes registros laborales. No puedes declarar que no tienes experiencia.',
                ]);
            }

            $user->experiencias()->create([
                'empresa' => null,
                'cargo' => null,
                'descripcion' => null,
                'fecha_inicio' => null,
                'fecha_fin' => null,
                'actualmente_trabaja' => false,
                'sin_experiencia' => true,
            ]);

            return redirect()
                ->route('perfil.show')
                ->with(
                    'success',
                    'Tu perfil quedó registrado sin experiencia laboral.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Caso 2: Experiencia laboral normal
    |--------------------------------------------------------------------------
    */

        $validated = $this->validateExperiencia($request);

        $validated['actualmente_trabaja'] =
            $request->boolean('actualmente_trabaja');

        if ($validated['actualmente_trabaja']) {
            $validated['fecha_fin'] = null;
        }

        $validated['sin_experiencia'] = false;

        $user->experiencias()->create($validated);

        return redirect()
            ->route('perfil.show')
            ->with(
                'success',
                'La experiencia laboral se agregó correctamente.'
            );
    }

    /**
     * Actualizar experiencia o declaración.
     */
    public function update(
        Request $request,
        UserExperiencia $experiencia
    ): RedirectResponse {
        $user = $request->user();

        $this->authorizeExperiencia($user->id, $experiencia);

        /*
    |--------------------------------------------------------------------------
    | Caso 1: Declaración de ausencia de experiencia
    |--------------------------------------------------------------------------
    */

        if ($request->boolean('sin_experiencia')) {

            // No permitir la declaración si hay otros registros laborales.
            if (
                $user->experiencias()
                ->where('id', '!=', $experiencia->id)
                ->exists()
            ) {
                throw ValidationException::withMessages([
                    'sin_experiencia' =>
                    'No puedes declarar que no tienes experiencia mientras existan otros registros laborales.',
                ]);
            }

            $experiencia->update([
                'empresa' => null,
                'cargo' => null,
                'descripcion' => null,
                'fecha_inicio' => null,
                'fecha_fin' => null,
                'actualmente_trabaja' => false,
                'sin_experiencia' => true,
            ]);

            return redirect()
                ->route('perfil.show')
                ->with(
                    'success',
                    'Tu perfil quedó registrado sin experiencia laboral.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Caso 2: Actualización de experiencia laboral normal
    |--------------------------------------------------------------------------
    */

        $validated = $this->validateExperiencia($request);

        $validated['actualmente_trabaja'] =
            $request->boolean('actualmente_trabaja');

        if ($validated['actualmente_trabaja']) {
            $validated['fecha_fin'] = null;
        }

        $validated['sin_experiencia'] = false;

        $experiencia->update($validated);

        return redirect()
            ->route('perfil.show')
            ->with(
                'success',
                'La experiencia laboral se actualizó correctamente.'
            );
    }

    /**
     * Eliminar una experiencia laboral.
     */
    public function destroy(
        Request $request,
        UserExperiencia $experiencia
    ): RedirectResponse {
        $this->authorizeExperiencia(
            $request->user()->id,
            $experiencia
        );

        $experiencia->delete();

        return redirect()
            ->route('perfil.show')
            ->with(
                'success',
                'El registro laboral se eliminó correctamente.'
            );
    }

    /**
     * Validar experiencia o declaración sin experiencia.
     */
    private function validateExperiencia(Request $request): array
    {
        return $request->validate([
            'empresa' => [
                'required',
                'string',
                'max:255',
            ],

            'cargo' => [
                'required',
                'string',
                'max:255',
            ],

            'descripcion' => [
                'nullable',
                'string',
            ],

            'fecha_inicio' => [
                'nullable',
                'date',
            ],

            'fecha_fin' => [
                'nullable',
                'date',
                'after_or_equal:fecha_inicio',
            ],

            'actualmente_trabaja' => [
                'required',
                'boolean',
            ],
        ]);
    }

    /**
     * Verificar que el registro pertenezca al usuario.
     */
    private function authorizeExperiencia(
        int $userId,
        UserExperiencia $experiencia
    ): void {
        abort_unless(
            $experiencia->user_id === $userId,
            403
        );
    }
}
