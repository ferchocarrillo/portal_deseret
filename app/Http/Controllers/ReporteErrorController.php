<?php

namespace App\Http\Controllers;

use App\Models\ReporteError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ReporteErrorController extends Controller
{
    /**
     * Muestra el formulario para reportar un error (Público autenticado).
     */
    public function create()
    {
        return Inertia::render('ReportesErrores/Create');
    }

    /**
     * Guarda el reporte enviado por el usuario.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'modulo'           => ['required', 'string', 'max:50'],
            'titulo'           => ['required', 'string', 'max:200'],
            'severidad'        => ['required', 'in:baja,media,alta,critica'],
            'descripcion'      => ['required', 'string', 'max:3000'],
            'pasos_reproducir' => ['nullable', 'string', 'max:2000'],
            'url_afectada'     => ['nullable', 'string', 'max:255'],
            'captura'          => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
        ]);

        $capturaPath = null;
        if ($request->hasFile('captura')) {
            $capturaPath = $request->file('captura')->store('reportes-errores', 'public');
        }

        $codigo = 'BUG-' . date('Y') . '-' . strtoupper(Str::random(5));

        ReporteError::create([
            'codigo_ticket'    => $codigo,
            'user_id'          => $request->user()->id,
            'modulo'           => $validated['modulo'],
            'titulo'           => $validated['titulo'],
            'severidad'        => $validated['severidad'],
            'descripcion'      => $validated['descripcion'],
            'pasos_reproducir' => $validated['pasos_reproducir'] ?? null,
            'url_afectada'     => $validated['url_afectada'] ?? null,
            'captura_path'     => $capturaPath,
            'estado'           => 'abierto',
        ]);

        return redirect()->route('dashboard')
            ->with('success', "Reporte registrado correctamente. Ticket: {$codigo}");
    }

    /**
     * Panel de administración: Listado y filtrado de reportes.
     */
    public function index(Request $request)
    {
        $query = ReporteError::with(['usuario:id,name,email', 'resolutor:id,name']);

        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('modulo') && $request->modulo !== 'todos') {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('severidad') && $request->severidad !== 'todos') {
            $query->where('severidad', $request->severidad);
        }

        if ($request->filled('search')) {
            $b = $request->search;
            $query->where(function ($q) use ($b) {
                $q->where('codigo_ticket', 'like', "%{$b}%")
                    ->orWhere('titulo', 'like', "%{$b}%")
                    ->orWhere('descripcion', 'like', "%{$b}%");
            });
        }

        $reportes = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('admin/ReportesErrores/Index', [
            'reportes' => $reportes,
            'filtros'  => [
                'estado'    => $request->query('estado', 'todos'),
                'modulo'    => $request->query('modulo', 'todos'),
                'severidad' => $request->query('severidad', 'todos'),
                'search'    => $request->query('search', ''),
            ],
            'conteos'  => [
                'abiertos'     => ReporteError::where('estado', 'abierto')->count(),
                'en_revision'  => ReporteError::where('estado', 'en_revision')->count(),
                'resueltos'    => ReporteError::where('estado', 'resuelto')->count(),
                'total'        => ReporteError::count(),
            ],
        ]);
    }

    /**
     * Panel de administración: Actualizar estado y notas de solución.
     */
    public function updateEstado(Request $request, ReporteError $reporte)
    {
        $validated = $request->validate([
            'estado'         => ['required', 'in:abierto,en_revision,resuelto,descartado'],
            'notas_solucion' => ['nullable', 'string', 'max:3000'],
        ]);

        $esResuelto = in_array($validated['estado'], ['resuelto', 'descartado']);

        $reporte->update([
            'estado'         => $validated['estado'],
            'notas_solucion' => $validated['notas_solucion'] ?? $reporte->notas_solucion,
            'resuelto_por'   => $esResuelto ? $request->user()->id : null,
            'resuelto_el'    => $esResuelto ? now() : null,
        ]);

        return back()->with('success', "Ticket {$reporte->codigo_ticket} actualizado a estado '{$validated['estado']}'.");
    }
}
