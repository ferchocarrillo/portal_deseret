<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PalabraProhibida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class PalabrasProhibidasController extends Controller
{
    // Listar las palabras prohibidas con paginación o búsqueda opcional
    public function index(Request $request)
    {
        $query = PalabraProhibida::query();

        if ($request->filled('busqueda')) {
            $query->where('palabra', 'like', '%' . trim($request->busqueda) . '%');
        }

        // 50 registros por página ordenados alfabéticamente (A-Z)
        $palabras = $query->orderBy('palabra', 'asc')->paginate(10)->withQueryString();

        return Inertia::render('admin/PalabrasProhibidas/Index', [
            'palabras' => $palabras,
            'filtros' => $request->only(['busqueda']),
        ]);
    }

    // Guardar una nueva palabra prohibida
    public function store(Request $request)
    {
        $validated = $request->validate([
            'palabra' => ['required', 'string', 'max:100', 'unique:palabras_prohibidas,palabra'],
            'nivel' => ['required', 'in:alta,media,baja'],
        ]);

        PalabraProhibida::create([
            'palabra' => mb_strtolower(trim($validated['palabra'])),
            'nivel' => $validated['nivel'],
        ]);

        // Limpiamos la caché para que el cambio surta efecto de inmediato en todo el sistema
        Cache::forget('palabras_prohibidas_list');

        return back()->with('success', 'Palabra prohibida agregada exitosamente.');
    }

    // Eliminar una palabra prohibida
    public function destroy(PalabraProhibida $palabrasProhibida)
    {
        $palabrasProhibida->delete();

        // Limpiamos la caché nuevamente
        Cache::forget('palabras_prohibidas_list');

        return back()->with('success', 'Palabra eliminada del diccionario correctamente.');
    }
}
