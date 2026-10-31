<?php

namespace App\Http\Controllers;

use App\Models\Destacado;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DestacadoController extends Controller
{
    /**
     * Alternar el estado de destacado de cualquier publicación compatible.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'id' => ['required', 'integer'],
            'model' => ['required', 'string'], // Ej: App\Models\Servicio
            'modulo' => ['required', 'string'], // Ej: servicios, emprendimientos
        ]);

        $modelClass = $request->input('model');
        $pubId = $request->input('id');
        $modulo = $request->input('modulo');

        // Buscar el modelo de la publicación
        $publicacion = $modelClass::findOrFail($pubId);

        // Buscar si ya existe un registro activo en la tabla destacados
        $destacadoExistente = Destacado::where('destacable_type', $modelClass)
            ->where('destacable_id', $pubId)
            ->first();

        if ($destacadoExistente) {
            // Si ya está destacado, lo eliminamos (desmarcar)
            $destacadoExistente->delete();
            $mensaje = "Se ha quitado el destaque correctamente.";
        } else {
            // Si no está destacado, lo creamos (marcar)
            $categoriaTexto = $publicacion->categoria ?? null;
            $slugCategoria = $categoriaTexto ? Str::slug(explode(':', $categoriaTexto)[0]) : 'otros';

            Destacado::create([
                'destacable_type' => $modelClass,
                'destacable_id' => $pubId,
                'tipo' => Destacado::TIPO_MANUAL,
                'modulo' => ucfirst($modulo),
                'categoria_slug' => $slugCategoria,
                'activo' => true,
                'fecha_inicio' => now(),
                'destacado_por' => auth()->id(),
            ]);
            $mensaje = "Se ha marcado como destacado correctamente.";
        }

        return back()->with('success', $mensaje);
    }
}
