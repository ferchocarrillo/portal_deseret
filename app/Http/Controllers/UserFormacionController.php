<?php

namespace App\Http\Controllers;

use App\Models\UserFormacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserFormacionController extends Controller
{
    /**
     * Guardar una nueva formación académica
     * para el usuario autenticado.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateFormacion($request);

        $validated['user_id'] = Auth::id();

        if ($request->boolean('en_curso')) {
            $validated['en_curso'] = true;
            $validated['fecha_fin'] = null;
        } else {
            $validated['en_curso'] = false;
        }

        UserFormacion::create($validated);

        return redirect()
            ->route('perfil.show')
            ->with(
                'success',
                'La formación académica se agregó correctamente.'
            );
    }


    /**
     * Actualizar una formación académica
     * perteneciente al usuario autenticado.
     */
    public function update(
        Request $request,
        UserFormacion $formacion
    ): RedirectResponse {

        $this->authorizeFormacion($formacion);

        $validated = $this->validateFormacion($request);

        if ($request->boolean('en_curso')) {
            $validated['en_curso'] = true;
            $validated['fecha_fin'] = null;
        } else {
            $validated['en_curso'] = false;
        }

        $formacion->update($validated);

        return redirect()
            ->route('perfil.show')
            ->with(
                'success',
                'La formación académica se actualizó correctamente.'
            );
    }


    /**
     * Eliminar una formación académica
     * perteneciente al usuario autenticado.
     */
    public function destroy(
        UserFormacion $formacion
    ): RedirectResponse {

        $this->authorizeFormacion($formacion);

        $formacion->delete();

        return redirect()
            ->route('perfil.show')
            ->with(
                'success',
                'La formación académica se eliminó correctamente.'
            );
    }


    /**
     * Validar los datos de una formación académica.
     */
    private function validateFormacion(Request $request): array
    {
        return $request->validate([
            'nivel' => [
                'nullable',
                'string',
                'max:255',
            ],

            'institucion' => [
                'nullable',
                'string',
                'max:255',
            ],

            'programa' => [
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

            'en_curso' => [
                'boolean',
            ],
        ]);
    }


    /**
     * Verificar que la formación pertenezca
     * al usuario autenticado.
     */
    private function authorizeFormacion(
        UserFormacion $formacion
    ): void {
        abort_unless(
            $formacion->user_id === Auth::id(),
            403
        );
    }
}
