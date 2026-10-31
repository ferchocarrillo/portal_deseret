<?php

namespace App\Http\Controllers;

use App\Models\BuzonOpinion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BuzonOpinionController extends Controller
{
    /**
     * Formulario de envío (Cualquier usuario autenticado).
     */
    public function create()
    {
        return Inertia::render('BuzonOpiniones/Create');
    }

    /**
     * Guardar sugerencia, queja o felicitación.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo'    => ['required', 'in:sugerencia,queja,felicitacion'],
            'modulo'  => ['required', 'string', 'max:50'],
            'asunto'  => ['required', 'string', 'max:150'],
            'mensaje' => ['required', 'string', 'max:2500'],
        ]);

        BuzonOpinion::create([
            'user_id' => $request->user()->id,
            'tipo'    => $validated['tipo'],
            'modulo'  => $validated['modulo'],
            'asunto'  => $validated['asunto'],
            'mensaje' => $validated['mensaje'],
            'leido'   => false,
        ]);

        return redirect()->route('dashboard')
            ->with('success', '¡Gracias por tu mensaje! Tu opinión nos ayuda a seguir mejorando.');
    }

    /**
     * Bandeja de entrada de opiniones (Admin).
     */
    public function index(Request $request)
    {
        $query = BuzonOpinion::with('usuario:id,name,email');

        if ($request->filled('tipo') && $request->tipo !== 'todos') {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('leido', $request->estado === 'leido');
        }

        $opiniones = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('BuzonOpiniones/Index', [
            'opiniones' => $opiniones,
            'filtros'   => [
                'tipo'   => $request->query('tipo', 'todos'),
                'estado' => $request->query('estado', 'todos'),
            ],
            'conteos'   => [
                'pendientes'     => BuzonOpinion::where('leido', false)->count(),
                'sugerencias'    => BuzonOpinion::where('tipo', 'sugerencia')->count(),
                'quejas'         => BuzonOpinion::where('tipo', 'queja')->count(),
                'felicitaciones' => BuzonOpinion::where('tipo', 'felicitacion')->count(),
            ],
        ]);
    }

    /**
     * Alternar estado de lectura (Marcar leído / no leído).
     */
    public function toggleLeido(Request $request, BuzonOpinion $opinion)
    {
        $nuevoEstado = !$opinion->leido;

        $opinion->update([
            'leido'     => $nuevoEstado,
            'leido_por' => $nuevoEstado ? $request->user()->id : null,
            'leido_el'  => $nuevoEstado ? now() : null,
        ]);

        return back();
    }
}
