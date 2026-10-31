<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clasificado;
use App\Models\Curso;
use App\Models\Destacado;
use App\Models\Emprendimiento;
use App\Models\MarketplacePublicacion;
use App\Models\OfertaEmpleo;
use App\Models\Servicio;
use App\Models\Evento;
use App\Models\ServicioRequerimiento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;

class PublicacionAdminController extends Controller
{
    private function resolverAutor($item): array
    {
        $autor = null;

        if (method_exists($item, 'usuario') && $item->relationLoaded('usuario')) {
            $autor = $item->usuario;
        } elseif (method_exists($item, 'user') && $item->relationLoaded('user')) {
            $autor = $item->user;
        } elseif (!empty($item->user_id)) {
            $autor = User::find($item->user_id);
        }

        return [
            'nombre' => $autor?->name ?? 'Desconocido',
            'email'  => $autor?->email ?? null,
        ];
    }

    private function verificarDestaque($item, string $modelClass): bool
    {
        return Destacado::where('destacable_type', $modelClass)
            ->where('destacable_id', $item->id)
            ->where('activo', true)
            ->exists();
    }

    public function index(Request $request)
    {
        // Detectar si estamos en el modo "Mis publicaciones" o en el panel "Admin"
        $esModoPropio = $request->routeIs('publicaciones.mis-publicaciones');
        $userId = $request->user()->id;

        $moduloFiltro = $request->query('modulo', 'todos');
        $busqueda = $request->query('search', '');
        $estadoFiltro = $request->query('estado', 'todos');
        $perPage = (int) $request->query('per_page', 15);
        $page = (int) $request->query('page', 1);

        $items = collect();

        // 1. Marketplace
        if ($moduloFiltro === 'todos' || $moduloFiltro === 'marketplace') {
            $q = MarketplacePublicacion::with('user:id,name,email')->latest();
            if ($esModoPropio) $q->where('user_id', $userId);

            $items = $items->concat(
                $q->get()->map(function ($item) use ($esModoPropio) {
                    $autor = $this->resolverAutor($item);
                    return [
                        'id'           => $item->id,
                        'modulo'       => 'Marketplace',
                        'modulo_key'   => 'marketplace',
                        'titulo'       => $item->titulo,
                        'autor'        => $autor['nombre'],
                        'autor_email'  => $autor['email'],
                        'estado'       => $item->estado ?? ($item->activo ? 'activo' : 'inactivo'),
                        'fecha'        => $item->created_at,
                        'url_ver'      => route('marketplace.show', $item->id),
                        // RESTRICCIÓN: Si es modo propio (Mis publicaciones), nadie puede destacar aquí
                        'es_destacado' => $esModoPropio ? false : $this->verificarDestaque($item, MarketplacePublicacion::class),
                        'model_class'  => MarketplacePublicacion::class,
                    ];
                })
            );
        }

        // 2. Servicios
        if ($moduloFiltro === 'todos' || $moduloFiltro === 'servicios') {
            $rel = method_exists(Servicio::class, 'usuario') ? 'usuario:id,name,email' : (method_exists(Servicio::class, 'user') ? 'user:id,name,email' : null);
            $q = Servicio::query()->latest();
            if ($rel) $q->with($rel);
            if ($esModoPropio) $q->where('user_id', $userId);

            $items = $items->concat(
                $q->get()->map(function ($item) use ($esModoPropio) {
                    $autor = $this->resolverAutor($item);
                    return [
                        'id'           => $item->id,
                        'modulo'       => 'Servicios',
                        'modulo_key'   => 'servicios',
                        'titulo'       => $item->titulo,
                        'autor'        => $autor['nombre'],
                        'autor_email'  => $autor['email'],
                        'estado'       => $item->estado ?? 'activo',
                        'fecha'        => $item->created_at,
                        'url_ver'      => route('servicios.show', $item->id),
                        'es_destacado' => $esModoPropio ? false : $this->verificarDestaque($item, Servicio::class),
                        'model_class'  => Servicio::class,
                    ];
                })
            );
        }

        // 3. Clasificados
        if ($moduloFiltro === 'todos' || $moduloFiltro === 'clasificados') {
            $q = Clasificado::with('usuario:id,name,email')->latest();
            if ($esModoPropio) $q->where('user_id', $userId);

            $items = $items->concat(
                $q->get()->map(function ($item) use ($esModoPropio) {
                    $autor = $this->resolverAutor($item);
                    return [
                        'id'           => $item->id,
                        'modulo'       => 'Clasificados',
                        'modulo_key'   => 'clasificados',
                        'titulo'       => $item->titulo,
                        'autor'        => $autor['nombre'],
                        'autor_email'  => $autor['email'],
                        'estado'       => $item->estado ?? 'activo',
                        'fecha'        => $item->created_at,
                        'url_ver'      => route('clasificados.show', $item->slug ?? $item->id),
                        'es_destacado' => $esModoPropio ? false : $this->verificarDestaque($item, Clasificado::class),
                        'model_class'  => Clasificado::class,
                    ];
                })
            );
        }

        // 4. Emprendimientos
        if ($moduloFiltro === 'todos' || $moduloFiltro === 'emprendimientos') {
            $rel = method_exists(Emprendimiento::class, 'usuario') ? 'usuario:id,name,email' : (method_exists(Emprendimiento::class, 'user') ? 'user:id,name,email' : null);
            $q = Emprendimiento::query()->latest();
            if ($rel) $q->with($rel);
            if ($esModoPropio) $q->where('user_id', $userId);

            $items = $items->concat(
                $q->get()->map(function ($item) use ($esModoPropio) {
                    $autor = $this->resolverAutor($item);
                    return [
                        'id'           => $item->id,
                        'modulo'       => 'Emprendimientos',
                        'modulo_key'   => 'emprendimientos',
                        'titulo'       => $item->nombre ?? $item->titulo,
                        'autor'        => $autor['nombre'],
                        'autor_email'  => $autor['email'],
                        'estado'       => $item->estado ?? 'activo',
                        'fecha'        => $item->created_at,
                        'url_ver'      => route('emprendimientos.show', $item->id),
                        'es_destacado' => $esModoPropio ? false : $this->verificarDestaque($item, Emprendimiento::class),
                        'model_class'  => Emprendimiento::class,
                    ];
                })
            );
        }

        // 5. Cursos
        if ($moduloFiltro === 'todos' || $moduloFiltro === 'cursos') {
            $rel = method_exists(Curso::class, 'usuario') ? 'usuario:id,name,email' : (method_exists(Curso::class, 'user') ? 'user:id,name,email' : null);
            $q = Curso::query()->latest();
            if ($rel) $q->with($rel);
            if ($esModoPropio) $q->where('user_id', $userId);

            $items = $items->concat(
                $q->get()->map(function ($item) use ($esModoPropio) {
                    $autor = $this->resolverAutor($item);
                    return [
                        'id'           => $item->id,
                        'modulo'       => 'Cursos',
                        'modulo_key'   => 'cursos',
                        'titulo'       => $item->titulo,
                        'autor'        => $autor['nombre'],
                        'autor_email'  => $autor['email'],
                        'estado'       => $item->estado ?? 'activo',
                        'fecha'        => $item->created_at,
                        'url_ver'      => route('cursos.show', $item->id),
                        'es_destacado' => $esModoPropio ? false : $this->verificarDestaque($item, Curso::class),
                        'model_class'  => Curso::class,
                    ];
                })
            );
        }

        // 6. Ofertas de Empleo
        if ($moduloFiltro === 'todos' || $moduloFiltro === 'empleos') {
            $rel = method_exists(OfertaEmpleo::class, 'usuario') ? 'usuario:id,name,email' : (method_exists(OfertaEmpleo::class, 'user') ? 'user:id,name,email' : null);
            $q = OfertaEmpleo::query()->latest();
            if ($rel) $q->with($rel);
            if ($esModoPropio) $q->where('user_id', $userId);

            $items = $items->concat(
                $q->get()->map(function ($item) use ($esModoPropio) {
                    $autor = $this->resolverAutor($item);
                    return [
                        'id'           => $item->id,
                        'modulo'       => 'Empleos',
                        'modulo_key'   => 'empleos',
                        'titulo'       => $item->titulo,
                        'autor'        => $autor['nombre'],
                        'autor_email'  => $autor['email'],
                        'estado'       => $item->estado ?? 'activo',
                        'fecha'        => $item->created_at,
                        'url_ver'      => route('empleos.show', $item->id),
                        'es_destacado' => $esModoPropio ? false : $this->verificarDestaque($item, OfertaEmpleo::class),
                        'model_class'  => OfertaEmpleo::class,
                    ];
                })
            );
        }

        // 7. Eventos
        if ($moduloFiltro === 'todos' || $moduloFiltro === 'eventos') {
            $rel = method_exists(Evento::class, 'usuario') ? 'usuario:id,name,email' : (method_exists(Evento::class, 'user') ? 'user:id,name,email' : null);
            $q = Evento::query()->latest();
            if ($rel) $q->with($rel);
            if ($esModoPropio) $q->where('user_id', $userId);

            $items = $items->concat(
                $q->get()->map(function ($item) use ($esModoPropio) {
                    $autor = $this->resolverAutor($item);
                    return [
                        'id'           => $item->id,
                        'modulo'       => 'Eventos',
                        'modulo_key'   => 'eventos',
                        'titulo'       => $item->titulo,
                        'autor'        => $autor['nombre'],
                        'autor_email'  => $autor['email'],
                        'estado'       => $item->estado ?? 'activo',
                        'fecha'        => $item->created_at,
                        'url_ver'      => route('eventos.show', $item->id),
                        'es_destacado' => $esModoPropio ? false : $this->verificarDestaque($item, Evento::class),
                        'model_class'  => Evento::class,
                    ];
                })
            );
        }

        // 8. Servicios Requeridos / Solicitudes Urgentes
        if ($moduloFiltro === 'todos' || $moduloFiltro === 'requerimientos') {
            $q = ServicioRequerimiento::query()->latest();
            if ($esModoPropio) $q->where('user_id', $userId);

            $items = $items->concat(
                $q->get()->map(function ($item) use ($esModoPropio) {
                    $autor = $this->resolverAutor($item);
                    return [
                        'id'           => $item->id,
                        'modulo'       => 'Requerimiento',
                        'modulo_key'   => 'requerimientos',
                        'titulo'       => $item->titulo,
                        'autor'        => $autor['nombre'],
                        'autor_email'  => $autor['email'],
                        'estado'       => $item->estado ?? 'abierto',
                        'fecha'        => $item->created_at,
                        'url_ver'      => route('servicios.requerimientos.show', $item->id),
                        'es_destacado' => $esModoPropio ? false : $this->verificarDestaque($item, ServicioRequerimiento::class),
                        'model_class'  => ServicioRequerimiento::class,
                    ];
                })
            );
        }

        // Filtro unificado de estado (limpio, sin duplicados)
        if ($estadoFiltro !== 'todos') {
            $items = $items->filter(function ($i) use ($estadoFiltro) {
                $est = strtolower($i['estado']);
                if ($estadoFiltro === 'activo') {
                    return in_array($est, ['activo', 'abierto', 'publicado', 'activa']);
                }
                return $est === strtolower($estadoFiltro);
            });
        }

        // Filtro de búsqueda por texto
        if (!empty($busqueda)) {
            $b = mb_strtolower($busqueda);
            $items = $items->filter(
                fn($i) =>
                str_contains(mb_strtolower($i['titulo']), $b) ||
                    str_contains(mb_strtolower($i['autor']), $b) ||
                    str_contains(mb_strtolower($i['autor_email'] ?? ''), $b)
            );
        }

        // Ordenar globalmente por fecha más reciente
        $ordenados = $items->sortByDesc('fecha')->values();

        // Paginación manual corregida (sin saltos de línea con errores)
        $total = $ordenados->count();
        $slice = $ordenados->slice(($page - 1) * $perPage, $perPage)->values();

        $paginador = new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Contadores según el ámbito (propios o totales)
        $fnCount = function ($modelo) use ($esModoPropio, $userId) {
            $q = $modelo::query();
            if ($esModoPropio) $q->where('user_id', $userId);
            return $q->count();
        };

        return Inertia::render('admin/Publicaciones/Index', [
            'publicaciones'    => $paginador,
            'esModoPropio'     => $esModoPropio,
            'filtros'          => [
                'modulo'   => $moduloFiltro,
                'search'   => $busqueda,
                'estado'   => $estadoFiltro,
                'per_page' => $perPage,
            ],
            'totalesPorModulo' => [
                'todos'          => $total,
                'marketplace'    => $fnCount(MarketplacePublicacion::class),
                'servicios'      => $fnCount(Servicio::class),
                'requerimientos' => $fnCount(ServicioRequerimiento::class),
                'clasificados'   => $fnCount(Clasificado::class),
                'emprendimientos' => $fnCount(Emprendimiento::class),
                'cursos'         => $fnCount(Curso::class),
                'empleos'        => $fnCount(OfertaEmpleo::class),
                'eventos'        => $fnCount(Evento::class),
            ],
        ]);
    }
}
