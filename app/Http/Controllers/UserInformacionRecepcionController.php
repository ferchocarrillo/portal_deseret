<?php

namespace App\Http\Controllers;

use App\Models\UserInformacionRecepcion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserInformacionRecepcionController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $informacion = UserInformacionRecepcion::query()
            ->where('user_id', $request->user()->id)
            ->first();

        return response()->json([
            'success' => true,
            'informacion' => $informacion,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        // Ambas cosas son estrictamente obligatorias: Dirección y Coordenadas del Mapa
        $validated = $request->validate([
            'pais'                        => ['nullable', 'string', 'max:100'],
            'departamento'                => ['required', 'string', 'max:150'],
            'ciudad'                      => ['required', 'string', 'max:150'],
            'codigo_postal'               => ['nullable', 'string', 'max:20'],
            'barrio'                      => ['nullable', 'string', 'max:150'],
            'tipo_via'                    => ['nullable', 'string', 'max:50'],
            'numero_via'                  => ['required', 'string', 'max:100'],
            'complemento'                 => ['nullable', 'string', 'max:255'],
            'complemento_2'               => ['nullable', 'string', 'max:255'],
            'referencia_direccion'        => ['nullable', 'string', 'max:500'],

            // Coordenadas obligatorias fijadas mediante el mapa o GPS
            'latitud'                     => ['required', 'numeric', 'between:-90,90'],
            'longitud'                    => ['required', 'numeric', 'between:-180,180'],

            'telefono_contacto'           => ['nullable', 'string', 'max:30'],
            'whatsapp'                    => ['nullable', 'string', 'max:30'],
            'correo_contacto'             => ['nullable', 'email', 'max:255'],
            'medio_contacto_preferido'    => ['nullable', 'string', 'max:50'],
            'instrucciones_recepcion'     => ['nullable', 'string', 'max:1000'],
            'permite_contacto'            => ['nullable', 'boolean'],
            'permite_compartir_direccion' => ['nullable', 'boolean'],
        ], [
            'latitud.required'  => 'Debes ubicar y confirmar tu posición exacta en el mapa interactivo o mediante el botón GPS.',
            'longitud.required' => 'Debes ubicar y confirmar tu posición exacta en el mapa interactivo o mediante el botón GPS.',
            'numero_via.required' => 'Debes ingresar tu dirección de residencia.',
        ]);

        $informacion = UserInformacionRecepcion::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json([
            'success'     => true,
            'message'     => 'Información de residencia y coordenadas registradas correctamente.',
            'informacion' => $informacion->fresh(),
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        UserInformacionRecepcion::query()
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Información de residencia eliminada correctamente.',
        ]);
    }
}
