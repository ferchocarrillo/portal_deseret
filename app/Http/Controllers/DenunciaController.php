<?php

namespace App\Http\Controllers;

use App\Models\Clasificado;
use App\Models\Curso;
use App\Models\Denuncia;
use App\Models\Emprendimiento;
use App\Models\MarketplacePublicacion;
use App\Models\OfertaEmpleo;
use App\Models\PalabraProhibida;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DenunciaController extends Controller
{
    /**
     * Muestra el listado de denuncias para el panel de administración (con seguimiento).
     */
    public function index(Request $request)
    {
        $query = Denuncia::with(['usuario', 'administrador']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->input('modulo'));
        }

        $denuncias = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('admin/Denuncias/Index', [
            'denuncias' => $denuncias,
            'filtros'   => $request->only(['estado', 'modulo']),
        ]);
    }

    /**
     * Muestra el formulario unificado de denuncia para los usuarios.
     */
    public function create(Request $request)
    {
        $palabrasProhibidas = PalabraProhibida::pluck('palabra')->toArray();

        return Inertia::render('admin/Denuncias/Create', [
            'moduloPreseleccionado' => $request->input('modulo'),
            'reportableType'        => $request->input('reportable_type'),
            'reportableId'          => $request->input('reportable_id'),
            'palabrasProhibidas'    => $palabrasProhibidas,
        ]);
    }

    /**
     * Devuelve el número de denuncias pendientes para las notificaciones del sidebar.
     */
    public function contarPendientes()
    {
        return response()->json([
            'denunciasPendientes' => Denuncia::where('estado', 'pendiente')->count(),
        ]);
    }

    /**
     * Almacena una nueva denuncia en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'modulo'          => 'required|string',
            'reportable_type' => 'nullable|string',
            'reportable_id'   => 'nullable|integer',
            'motivo'          => 'required|string|max:255',
            'descripcion'     => 'required|string|max:2000',
            'evidencia'       => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        $evidenciaPath = null;
        if ($request->hasFile('evidencia')) {
            $evidenciaPath = $request->file('evidencia')->store('denuncias-evidencias', 'public');
        }

        $codigoUnico = 'DEN-' . date('Y') . '-' . strtoupper(Str::random(5));

        Denuncia::create([
            'codigo_unico'    => $codigoUnico,
            'user_id'         => $request->user()->id,
            'modulo'          => $request->input('modulo'),
            'reportable_type' => $request->input('reportable_type'),
            'reportable_id'   => $request->input('reportable_id'),
            'motivo'          => $request->input('motivo'),
            'descripcion'     => $request->input('descripcion'),
            'evidencia_path'  => $evidenciaPath,
            'estado'          => 'pendiente',
        ]);

        return redirect()->route('denuncias.creada.exito')
            ->with('success', 'Denuncia registrada correctamente. Código de seguimiento: ' . $codigoUnico);
    }

    /**
     * Muestra el detalle de una denuncia específica para su gestión y seguimiento.
     */
    public function show(Denuncia $denuncia)
    {
        $denuncia->load(['usuario', 'administrador', 'reportable']);

        $publicacionUrl = null;
        if ($denuncia->reportable_id) {
            switch ($denuncia->modulo) {
                case 'marketplace':
                    $publicacionUrl = route('marketplace.show', $denuncia->reportable_id);
                    break;
                case 'clasificados':
                    $publicacionUrl = route('clasificados.show', $denuncia->reportable_id);
                    break;
                case 'empleos':
                    $publicacionUrl = route('empleos.show', $denuncia->reportable_id);
                    break;
                case 'servicios':
                    $publicacionUrl = route('servicios.show', $denuncia->reportable_id);
                    break;
                case 'emprendimientos':
                    $publicacionUrl = route('emprendimientos.show', $denuncia->reportable_id);
                    break;
                case 'cursos':
                    $publicacionUrl = route('cursos.show', $denuncia->reportable_id);
                    break;
            }
        }

        return Inertia::render('admin/Denuncias/Show', [
            'denuncia'       => $denuncia,
            'publicacionUrl' => $publicacionUrl,
        ]);
    }

    /**
     * Actualiza el estado, notas de seguimiento o realiza el cierre de la incidencia.
     */
    public function actualizarSeguimiento(Request $request, Denuncia $denuncia)
    {
        $request->validate([
            'estado'         => 'required|in:pendiente,en_revision,resuelto,descartado',
            'notas_internas' => 'nullable|string|max:3000',
        ]);

        $nuevoEstado = $request->input('estado');

        $fechaCierre = $denuncia->fecha_cierre;
        if (in_array($nuevoEstado, ['resuelto', 'descartado']) && !in_array($denuncia->estado, ['resuelto', 'descartado'])) {
            $fechaCierre = now();
        } elseif (!in_array($nuevoEstado, ['resuelto', 'descartado'])) {
            $fechaCierre = null;
        }

        $denuncia->update([
            'estado'         => $nuevoEstado,
            'admin_id'       => $request->user()->id,
            'notas_internas' => $request->input('notas_internas'),
            'fecha_cierre'   => $fechaCierre,
        ]);

        return back()->with('success', 'Seguimiento de la incidencia actualizado exitosamente.');
    }

    /**
     * Muestra el listado de denuncias realizadas por el usuario autenticado.
     */
    public function misDenuncias(Request $request)
    {
        $denuncias = Denuncia::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10); // Corregido: eliminado el punto de concatenación erróneo

        return Inertia::render('Denuncias/Index', [
            'denuncias' => $denuncias,
        ]);
    }

    /**
     * Quita la suspensión temporal y reactiva las publicaciones del usuario denunciado.
     */
    public function quitarBloqueoTemporal(Denuncia $denuncia)
    {
        $usuario = $denuncia->denunciado ?? User::find($denuncia->denunciado_id);

        if ($usuario) {
            $usuario->update([
                'bloqueado_hasta' => null,
            ]);

            $this->reactivarPublicacionesUsuario($usuario->id);
        }

        return back()->with('success', 'Suspensión temporal retirada y publicaciones reactivadas.');
    }

    /**
     * Reactiva la cuenta del usuario y sus publicaciones asociadas.
     */
    public function reactivarUsuario(Denuncia $denuncia)
    {
        $usuario = $denuncia->denunciado ?? User::find($denuncia->denunciado_id);

        if ($usuario) {
            $usuario->update([
                'activo'          => true,
                'bloqueado_hasta' => null,
            ]);

            $this->reactivarPublicacionesUsuario($usuario->id);
        }

        return back()->with('success', 'Usuario habilitado y publicaciones reactivadas.');
    }

    /**
     * Función auxiliar para restaurar publicaciones respetando el esquema real de cada módulo.
     */
    private function reactivarPublicacionesUsuario(int $userId): void
    {
        // 1. Marketplace (No tiene columna estado, solo activo)
        MarketplacePublicacion::where('user_id', $userId)
            ->where('activo', false)
            ->update([
                'activo' => true,
            ]);

        // 2. Servicios
        Servicio::where('user_id', $userId)
            ->whereIn('estado', ['pausado', 'suspendido', 'inactivo', 'bloqueado'])
            ->update([
                'estado' => 'activo',
            ]);

        // 3. Clasificados
        Clasificado::where('user_id', $userId)
            ->whereIn('estado', ['pausado', 'suspendido', 'inactivo', 'bloqueado'])
            ->update([
                'estado'            => 'publicado',
                'fecha_suspension'  => null,
                'motivo_suspension' => null,
            ]);

        // 4. Emprendimientos
        Emprendimiento::where('user_id', $userId)
            ->whereIn('estado', ['pausado', 'suspendido', 'inactivo', 'bloqueado'])
            ->update([
                'estado' => 'activo',
            ]);

        // 5. Cursos
        Curso::where('user_id', $userId)
            ->whereIn('estado', ['pausado', 'suspendido', 'inactivo', 'bloqueado'])
            ->update([
                'estado' => 'activo',
            ]);

        // 6. Ofertas de Empleo
        OfertaEmpleo::where('user_id', $userId)
            ->whereIn('estado', ['pausado', 'suspendido', 'inactivo', 'bloqueado'])
            ->update([
                'estado' => 'activo',
            ]);
    }
}
