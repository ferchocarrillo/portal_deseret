<?php

namespace App\Http\Controllers\Evento;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Favorito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class EventoController extends Controller
{
    /**
     * Listado general de eventos con filtros y paginación.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Obtener la ubicación desde la tabla 'user_informaciones_recepcion' si el usuario la tiene
        $infoRecepcion = null;
        if ($user) {
            $infoRecepcion = DB::table('user_informaciones_recepcion')
                ->where('user_id', $user->id)
                ->first();
        }

        // 2. Resolver los IDs de referencia priorizando el request, luego el usuario, y luego los textos de recepción
        $ciudadReferenciaId = $request->input('ciudad_id') ?? $user?->ciudad_id;
        $departamentoReferenciaId = $request->input('division_administrativa_id') ?? $user?->division_administrativa_id;

        // Si no hay ID numérico pero el usuario tiene texto en 'user_informaciones_recepcion', buscamos el ID en la BD
        if (!$ciudadReferenciaId && $infoRecepcion && !empty($infoRecepcion->ciudad)) {
            $ciudadBD = DB::table('ciudades')
                ->where('nombre', 'like', '%' . trim($infoRecepcion->ciudad) . '%')
                ->first();
            if ($ciudadBD) {
                $ciudadReferenciaId = $ciudadBD->id;
            }
        }

        if (!$departamentoReferenciaId && $infoRecepcion && !empty($infoRecepcion->departamento)) {
            $deptoBD = DB::table('divisiones_administrativas')
                ->where('nombre', 'like', '%' . trim($infoRecepcion->departamento) . '%')
                ->first();
            if ($deptoBD) {
                $departamentoReferenciaId = $deptoBD->id;
            }
        }

        $eventos = Evento::query()
            ->with(['usuario', 'ciudad', 'divisionAdministrativa'])
            ->where('estado', 'activo')
            // Filtro territorial basado en país, departamento y ciudad
            ->where(function ($query) use ($ciudadReferenciaId, $departamentoReferenciaId, $user) {
                // El creador siempre ve su evento
                if ($user) {
                    $query->orWhere('user_id', $user->id);
                }

                // Eventos virtuales o sin ubicación estricta son públicos
                $query->orWhere('modalidad', 'virtual');
                $query->orWhereNull('ciudad_id');

                // Coincidencia exacta por ciudad/municipio
                if ($ciudadReferenciaId) {
                    $query->orWhere('ciudad_id', $ciudadReferenciaId);
                }

                // Coincidencia por departamento / división administrativa
                if ($departamentoReferenciaId) {
                    $query->orWhere('division_administrativa_id', $departamentoReferenciaId);
                }
            })
            ->when($request->filled('buscar'), function ($query) use ($request) {
                $buscar = $request->input('buscar');
                $query->where(function ($q) use ($buscar) {
                    $q->where('titulo', 'like', "%{$buscar}%")
                        ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            })
            ->when($request->filled('modalidad'), function ($query) use ($request) {
                $query->where('modalidad', $request->input('modalidad'));
            })
            ->latest('fecha_inicio')
            ->paginate(12)
            ->withQueryString();

        if (Auth::check()) {
            $eventos->getCollection()->transform(function ($evento) {
                $esFavorito = Favorito::where('user_id', Auth::id())
                    ->where('publicacion_id', $evento->id)
                    ->where('modulo', 'eventos')
                    ->exists();

                $evento->setAttribute('es_favorito', $esFavorito);
                return $evento;
            });
        }

        return Inertia::render('Eventos/Index', [
            'eventos' => $eventos,
            'filtros' => $request->only(['buscar', 'modalidad', 'ciudad_id']),
        ]);
    }

    /**
     * Formulario de creación de un evento.
     */
    public function create()
    {
        return Inertia::render('Eventos/Create');
    }

    /**
     * Almacenar un nuevo evento en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo'                     => 'required|string|max:255',
            'descripcion'                => 'required|string',
            'modalidad'                  => 'required|in:presencial,virtual,hibrido',
            'ubicacion'                  => 'nullable|required_if:modalidad,presencial,hibrido|string|max:255',
            'enlace_virtual'             => 'nullable|required_if:modalidad,virtual,hibrido|url|max:255',
            'fecha_inicio'               => 'required|date',
            'fecha_fin'                  => 'nullable|date|after:fecha_inicio',
            'es_gratuito'                => 'boolean',
            'precio'                     => 'nullable|required_if:es_gratuito,false|numeric|min:0',
            'precio_preventa'            => 'nullable|numeric|min:0',
            'fecha_limite_preventa'      => 'nullable|required_with:precio_preventa|date|before:fecha_inicio',
            'precios_localidades'        => 'nullable|array',
            'capacidad'                  => 'nullable|integer|min:1',
            'pais_id'                    => 'nullable|exists:paises,id',
            'division_administrativa_id' => 'nullable|exists:divisiones_administrativas,id',
            'ciudad_id'                  => 'nullable|exists:ciudades,id',
            'imagen'                     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['estado'] = 'activo';
        $validated['es_gratuito'] = $request->boolean('es_gratuito');

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('eventos', 'public');
        }

        $evento = Evento::create($validated);

        return redirect()->route('eventos.show', $evento->id)
            ->with('success', '¡Evento publicado exitosamente!');
    }

    /**
     * Mostrar el detalle de un evento.
     */
    public function show(Evento $evento)
    {
        $evento->load(['usuario', 'ciudad', 'divisionAdministrativa']);

        $esFavorito = false;
        if (Auth::check()) {
            $esFavorito = Favorito::where('user_id', Auth::id())
                ->where('publicacion_id', $evento->id)
                ->where('modulo', 'eventos')
                ->exists();
        }

        $evento->setAttribute('es_favorito', $esFavorito);

        return Inertia::render('Eventos/Show', [
            'evento' => $evento,
        ]);
    }

    /**
     * Formulario de edición.
     */
    public function edit(Evento $evento)
    {
        $this->autorizarAccion($evento, 'editar');

        return Inertia::render('Eventos/Edit', [
            'evento' => $evento,
        ]);
    }

    /**
     * Actualizar evento.
     */
    public function update(Request $request, Evento $evento)
    {
        $this->autorizarAccion($evento, 'editar');

        $validated = $request->validate([
            'titulo'                     => 'required|string|max:255',
            'descripcion'                => 'required|string',
            'modalidad'                  => 'required|in:presencial,virtual,hibrido',
            'ubicacion'                  => 'nullable|required_if:modalidad,presencial,hibrido|string|max:255',
            'enlace_virtual'             => 'nullable|required_if:modalidad,virtual,hibrido|url|max:255',
            'fecha_inicio'               => 'required|date',
            'fecha_fin'                  => 'nullable|date|after:fecha_inicio',
            'es_gratuito'                => 'boolean',
            'precio'                     => 'nullable|required_if:es_gratuito,false|numeric|min:0',
            'precio_preventa'            => 'nullable|numeric|min:0',
            'fecha_limite_preventa'      => 'nullable|required_with:precio_preventa|date|before:fecha_inicio',
            'precios_localidades'        => 'nullable|array',
            'capacidad'                  => 'nullable|integer|min:1',
            'pais_id'                    => 'nullable|exists:paises,id',
            'division_administrativa_id' => 'nullable|exists:divisiones_administrativas,id',
            'ciudad_id'                  => 'nullable|exists:ciudades,id',
            'imagen'                     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['es_gratuito'] = $request->boolean('es_gratuito');

        if ($request->hasFile('imagen')) {
            if ($evento->imagen) {
                Storage::disk('public')->delete($evento->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('eventos', 'public');
        }

        $evento->update($validated);

        return redirect()->route('eventos.show', $evento->id)
            ->with('success', '¡Evento actualizado correctamente!');
    }

    /**
     * Eliminar evento.
     */
    public function destroy(Evento $evento)
    {
        $this->autorizarAccion($evento, 'eliminar');

        $eventoReportes = Evento::find($evento->id);
        if ($eventoReportes && $eventoReportes->imagen) {
            Storage::disk('public')->delete($eventoReportes->imagen);
        }

        $evento->delete();

        return redirect()->route('eventos.index')
            ->with('success', 'Evento eliminado.');
    }

    /**
     * Valida permisos globales vs permisos sobre registros propios sin errores de IDE.
     */
    private function autorizarAccion(Evento $evento, string $accion)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'No autorizado.');
        }

        $esPropietario = $evento->user_id === $user->id;
        $esAdminGeneral = $user->hasRole('Administrador General');

        if ($accion === 'editar') {
            $tienePermisoGlobal = $esAdminGeneral || $user->can('eventos.editar');
            $tienePermisoPropias = $esPropietario && $user->can('eventos.editar_propias');

            if (!$tienePermisoGlobal && !$tienePermisoPropias) {
                abort(403, 'No tienes autorización para editar este evento.');
            }
        }

        if ($accion === 'eliminar') {
            $tienePermisoGlobal = $esAdminGeneral || $user->can('eventos.eliminar');
            $tienePermisoPropias = $esPropietario && $user->can('eventos.eliminar_propias');

            if (!$tienePermisoGlobal && !$tienePermisoPropias) {
                abort(403, 'No tienes autorización para eliminar este evento.');
            }
        }
    }
}
