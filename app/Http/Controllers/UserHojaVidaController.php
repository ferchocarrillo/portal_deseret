<?php

namespace App\Http\Controllers;

use App\Models\UserHojaVida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserHojaVidaController extends Controller
{
    // ==========================================================
    // SUBIR HOJA DE VIDA
    // ==========================================================

    public function store(Request $request)
    {
        $user = $request->user();

        // ======================================================
        // VALIDACIÓN
        // ======================================================

        $validated = $request->validate([
            'archivo' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240',
            ],
        ], [
            'archivo.required' => 'Debes seleccionar una hoja de vida.',
            'archivo.file' => 'El archivo seleccionado no es válido.',
            'archivo.mimes' => 'La hoja de vida debe estar en formato PDF.',
            'archivo.max' => 'La hoja de vida no puede superar los 10 MB.',
        ]);


        // ======================================================
        // ARCHIVO
        // ======================================================

        $archivo = $validated['archivo'];

        $nombreOriginal = $archivo->getClientOriginalName();

        $nombreArchivo = Str::uuid() . '.pdf';


        // ======================================================
        // CARPETA DEL USUARIO
        // ======================================================

        $directorio = 'hojas-vida/' . $user->id;


        // ======================================================
        // GUARDAR ARCHIVO
        // ======================================================

        $ruta = $archivo->storeAs(
            $directorio,
            $nombreArchivo,
            'local'
        );


        // ======================================================
        // DETERMINAR SI SERÁ PRINCIPAL
        // ======================================================

        $esPrincipal = ! $user->hojasVida()->exists();


        // ======================================================
        // REGISTRAR HOJA DE VIDA
        // ======================================================

        $user->hojasVida()->create([
            'archivo' => $ruta,
            'nombre_original' => $nombreOriginal,
            'tipo_mime' => $archivo->getMimeType(),
            'tamanio' => $archivo->getSize(),
            'principal' => $esPrincipal,
        ]);


        // ======================================================
        // RESPUESTA INERTIA
        // ======================================================

        return back()->with(
            'success',
            'Hoja de vida cargada correctamente.'
        );
    }


    // ==========================================================
    // VISUALIZAR HOJA DE VIDA
    // ==========================================================

    public function show(string $id)
    {
        $user = request()->user();

        $hojaVida = $user->hojasVida()
            ->where('id', $id)
            ->firstOrFail();


        // ======================================================
        // VERIFICAR ARCHIVO
        // ======================================================

        abort_unless(
            Storage::disk('local')->exists($hojaVida->archivo),
            404,
            'El archivo de la hoja de vida no existe.'
        );


        // ======================================================
        // MOSTRAR PDF
        // ======================================================

        return response(
            Storage::disk('local')->get($hojaVida->archivo),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' .
                    addslashes($hojaVida->nombre_original) .
                    '"',
            ]
        );
    }


    // ==========================================================
    // DESCARGAR HOJA DE VIDA
    // ==========================================================

    public function download(string $id)
    {
        $user = request()->user();

        $hojaVida = $user->hojasVida()
            ->where('id', $id)
            ->firstOrFail();


        // ======================================================
        // VERIFICAR ARCHIVO
        // ======================================================

        abort_unless(
            Storage::disk('local')->exists($hojaVida->archivo),
            404,
            'El archivo de la hoja de vida no existe.'
        );


        // ======================================================
        // DESCARGAR PDF
        // ======================================================

        return Storage::disk('local')->download(
            $hojaVida->archivo,
            $hojaVida->nombre_original,
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }


    // ==========================================================
    // ELIMINAR HOJA DE VIDA
    // ==========================================================

    public function destroy(string $id)
    {
        $user = request()->user();

        $hojaVida = $user->hojasVida()
            ->where('id', $id)
            ->firstOrFail();


        // ======================================================
        // ELIMINAR ARCHIVO FÍSICO
        // ======================================================

        if (
            Storage::disk('local')
            ->exists($hojaVida->archivo)
        ) {
            Storage::disk('local')
                ->delete($hojaVida->archivo);
        }


        // ======================================================
        // ELIMINAR REGISTRO
        // ======================================================

        $hojaVida->delete();


        // ======================================================
        // RESPUESTA
        // ======================================================

        return response()->json([
            'success' => true,
            'message' => 'Hoja de vida eliminada correctamente.',
        ]);
    }


    // ==========================================================
    // ESTABLECER COMO PRINCIPAL
    // ==========================================================

    public function principal(string $id)
    {
        $user = request()->user();

        $hojaVida = $user->hojasVida()
            ->where('id', $id)
            ->firstOrFail();


        // ======================================================
        // QUITAR PRINCIPAL A LAS DEMÁS
        // ======================================================

        $user->hojasVida()
            ->where('id', '!=', $hojaVida->id)
            ->update([
                'principal' => false,
            ]);


        // ======================================================
        // ESTABLECER PRINCIPAL
        // ======================================================

        $hojaVida->update([
            'principal' => true,
        ]);


        // ======================================================
        // RESPUESTA
        // ======================================================

        return response()->json([
            'success' => true,
            'message' => 'Hoja de vida establecida como principal.',
        ]);
    }
}
