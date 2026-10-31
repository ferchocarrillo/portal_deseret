<?php

namespace App\Http\Controllers\Servicio;

use App\Http\Controllers\Controller;
use App\Models\Destacado;
use App\Models\ServicioRequerimiento;
use App\Models\Favorito;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class ServicioRequerimientoController extends Controller
{
    /**
     * Listado público de requerimientos de la comunidad.
     */
    public function index(Request $request): Response
    {
        $usuarioId = Auth::id();

        // Obtener los IDs de favoritos del usuario para requerimientos
        $favoritosRequerimientosIds = Auth::check()
            ? Favorito::query()
            ->where('user_id', $usuarioId)
            ->where('modulo', 'servicios_requerimientos')
            ->pluck('publicacion_id')
            ->map(fn($id) => (int) $id)
            ->toArray()
            : [];

        // 1. Requerimientos paginados con filtros
        $requerimientos = ServicioRequerimiento::query()
            ->with('usuario')
            ->where('estado', 'abierto')
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $buscar = $request->input('buscar');
                $query->where(function ($q) use ($buscar) {
                    $q->where('titulo', 'like', "%{$buscar}%")
                        ->orWhere('descripcion', 'like', "%{$buscar}%")
                        ->orWhere('categoria', 'like', "%{$buscar}%");
                });
            })
            ->when($request->filled('modalidad'), function ($query) use ($request) {
                $query->where('modalidad', $request->input('modalidad'));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(function ($req) use ($favoritosRequerimientosIds) {
                $req->es_favorito = in_array((int) $req->id, $favoritosRequerimientosIds, true);
                return $req;
            });

        // 2. Requerimientos destacados para la franja superior
        $requerimientosDestacados = ServicioRequerimiento::query()
            ->with('usuario')
            ->where('estado', 'abierto')
            ->where('es_destacado', true)
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($req) use ($favoritosRequerimientosIds) {
                $req->setAttribute('es_favorito', in_array((int) $req->id, $favoritosRequerimientosIds, true));

                // 🔥 ESTO ES LO QUE FALTABA: Le inyectamos el 'tipo' para que Vue sepa qué carpeta buscar
                $req->setAttribute('tipo', 'Requerimiento');

                return $req;
            });

        return Inertia::render('Servicios/Requerimientos/Index', [
            'requerimientos'          => $requerimientos,
            'requerimientosDestacados' => $requerimientosDestacados,
            'filtros'                 => $request->only(['buscar', 'modalidad']),
        ])->toResponse($request)->header('Cache-Control', 'no-store, no-cache, must-validate, max-age=0');
    }

    /**
     * Formulario para solicitar un servicio a la comunidad.
     */
    public function create(): Response
    {
        return Inertia::render('Servicios/Requerimientos/Create');
    }

    // Dentro del método store de tu ServicioRequerimientoController:
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'modalidad' => ['required', 'in:presencial,remoto,hibrido'],
            // ... demás validaciones que ya tengas ...
        ]);

        // 1. Crear el requerimiento en la base de datos
        $requerimiento = ServicioRequerimiento::create([
            'user_id' => $request->user()->id,
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'categoria' => $validated['categoria'] ?? null,
            'modalidad' => $validated['modalidad'],
            // ... demás campos ...
            'estado' => 'abierto',
        ]);

        // 2. Registrar automáticamente en la tabla de destacados por defecto
        $categoriaTexto = $requerimiento->categoria ?? null;
        $slugCategoria = $categoriaTexto ? Str::slug(explode(':', $categoriaTexto)[0]) : 'otros';

        Destacado::create([
            'destacable_type' => ServicioRequerimiento::class,
            'destacable_id' => $requerimiento->id,
            'modulo' => 'Requerimiento',
            'categoria_slug' => $slugCategoria,
            'tipo' => Destacado::TIPO_MANUAL,
            'activo' => true,
            'fecha_inicio' => now(),
            'destacado_por' => $request->user()->id,
        ]);

        return redirect()
            ->route('servicios.index')
            ->with('success', 'Tu solicitud de servicio se publicó y se destacó correctamente.');
    }

    /**
     * Ver el detalle de un requerimiento.
     */
    public function show(ServicioRequerimiento $requerimiento): Response
    {
        abort_unless($requerimiento->estado === 'abierto' || auth()->id() === $requerimiento->user_id, 404);

        $requerimiento->load('usuario');

        // Consultar si este requerimiento es favorito del usuario actual
        $esFavorito = Auth::check()
            ? Favorito::query()
            ->where('user_id', Auth::id())
            ->where('publicacion_id', (int) $requerimiento->id)
            ->where('modulo', 'servicios_requerimientos')
            ->exists()
            : false;

        $requerimiento->es_favorito = (bool) $esFavorito;

        return Inertia::render('Servicios/Requerimientos/Show', [
            'requerimiento' => $requerimiento,
            'esPropietario' => auth()->id() === $requerimiento->user_id,
        ]);
    }

    /**
     * Marcar como resuelto, en proceso o cancelado.
     */
    public function cambiarEstado(Request $request, ServicioRequerimiento $requerimiento): RedirectResponse
    {
        abort_unless(auth()->id() === $requerimiento->user_id, 403);

        $validated = $request->validate([
            'estado' => ['required', 'in:abierto,en_proceso,resuelto,cancelado'],
        ]);

        $requerimiento->update([
            'estado' => $validated['estado'],
        ]);

        return back()->with('success', 'El estado del requerimiento se actualizó correctamente.');
    }
}
