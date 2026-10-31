<?php

namespace App\Http\Controllers;

use App\Models\Favorito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class FavoritoController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $moduloFiltro = $request->query('modulo', '');

        $query = DB::table('favoritos')
            ->where('user_id', $userId)
            ->orderByDesc('created_at');

        if ($moduloFiltro) {
            $query->where('modulo', $moduloFiltro);
        }

        $registros = $query->get();
        $favoritos = [];

        foreach ($registros as $fav) {
            $item = $this->resolverPublicacion($fav->modulo, $fav->publicacion_id);

            if ($item) {
                $favoritos[] = [
                    'favorito_id'    => $fav->id,
                    'publicacion_id' => $fav->publicacion_id,
                    'modulo'         => $fav->modulo,
                    'titulo'         => $item['titulo'],
                    'subtitulo'      => $item['subtitulo'],
                    'precio'         => $item['precio'],
                    'imagen'         => $item['imagen'],
                    'url_destino'    => $item['url_destino'],
                    'etiqueta'       => $item['etiqueta'],
                    'color'          => $item['color'],
                    'icono'          => $item['icono'],
                    'fecha_guardado' => $fav->created_at,
                ];
            }
        }

        return Inertia::render('Favoritos/Index', [
            'favoritos'    => $favoritos,
            'moduloActivo' => $moduloFiltro,
        ]);
    }

    private function resolverPublicacion($modulo, $id)
    {
        switch ($modulo) {
            case 'marketplace':
                $pub = DB::table('marketplace_publicaciones')->where('id', $id)->first();
                if (!$pub) return null;
                $img = DB::table('marketplace_imagenes')->where('publicacion_id', $id)->orderByDesc('es_principal')->first();
                return [
                    'titulo'      => $pub->titulo,
                    'subtitulo'   => 'Comercio Comunitario',
                    'precio'      => $pub->es_gratis ? 'Gratis' : ($pub->precio ? '$' . number_format($pub->precio, 0, ',', '.') : 'Consultar'),
                    'imagen'      => $img ? "/storage/{$img->ruta}" : null,
                    'url_destino' => route('marketplace.show', $id),
                    'etiqueta'    => 'Marketplace',
                    'color'       => 'blue',
                    'icono'       => '🛍️',
                ];

            case 'clasificados':
                $pub = DB::table('clasificados')->where('id', $id)->first();
                if (!$pub) return null;
                $img = DB::table('clasificado_imagenes')->where('clasificado_id', $id)->orderByDesc('es_principal')->first();
                return [
                    'titulo'      => $pub->titulo,
                    'subtitulo'   => ucfirst($pub->operacion ?? 'Aviso clasificado'),
                    'precio'      => $pub->precio ? '$' . number_format($pub->precio, 0, ',', '.') : 'Precio a consultar',
                    'imagen'      => $img ? "/storage/{$img->ruta}" : null,
                    'url_destino' => route('clasificados.show', $id),
                    'etiqueta'    => 'Clasificados',
                    'color'       => 'amber',
                    'icono'       => '🏷️',
                ];

            case 'servicios':
                $pub = DB::table('servicios')->where('id', $id)->first();
                if (!$pub) return null;
                return [
                    'titulo'      => $pub->titulo,
                    'subtitulo'   => $pub->categoria ?? 'Servicio ofrecido',
                    'precio'      => $pub->precio ? '$' . number_format($pub->precio, 0, ',', '.') : 'A convenir',
                    'imagen'      => $pub->imagen ? (str_starts_with($pub->imagen, 'http') || str_starts_with($pub->imagen, '/') ? $pub->imagen : "/storage/{$pub->imagen}") : null,
                    'url_destino' => route('servicios.show', $id),
                    'etiqueta'    => 'Servicio',
                    'color'       => 'emerald',
                    'icono'       => '🛠️',
                ];

            case 'servicios_requerimientos':
                $pub = DB::table('servicio_requerimientos')->where('id', $id)->first();
                if (!$pub) return null;
                return [
                    'titulo'      => $pub->titulo,
                    'subtitulo'   => 'Solicitud de servicio (' . ucfirst($pub->urgencia ?? 'normal') . ')',
                    'precio'      => $pub->presupuesto_min ? '$' . number_format($pub->presupuesto_min, 0, ',', '.') : 'A convenir',
                    'imagen'      => null,
                    'url_destino' => route('servicios.requerimientos.show', $id),
                    'etiqueta'    => 'Requerimiento',
                    'color'       => 'emerald',
                    'icono'       => '⚡',
                ];

            case 'cursos':
                $pub = DB::table('cursos')->where('id', $id)->first();
                if (!$pub) return null;
                return [
                    'titulo'      => $pub->titulo,
                    'subtitulo'   => 'Formación · ' . ucfirst($pub->modalidad ?? 'Virtual'),
                    'precio'      => $pub->es_gratuito ? 'Gratis' : ($pub->precio ? '$' . number_format($pub->precio, 0, ',', '.') : 'Precio no definido'),
                    'imagen'      => $pub->imagen ? (str_starts_with($pub->imagen, 'http') || str_starts_with($pub->imagen, '/') ? $pub->imagen : "/storage/{$pub->imagen}") : null,
                    'url_destino' => route('cursos.show', $id),
                    'etiqueta'    => 'Curso',
                    'color'       => 'orange',
                    'icono'       => '📚',
                ];

            case 'eventos':
                if (!DB::getSchemaBuilder()->hasTable('eventos')) return null;
                $pub = DB::table('eventos')->where('id', $id)->first();
                if (!$pub) return null;

                // Resolver imagen (desde columna directa o tabla secundaria si existe)
                $rutaImagen = $pub->imagen ?? $pub->imagen_portada ?? $pub->portada ?? null;
                if (!$rutaImagen && DB::getSchemaBuilder()->hasTable('evento_imagenes')) {
                    $img = DB::table('evento_imagenes')->where('evento_id', $id)->orderByDesc('es_principal')->first();
                    $rutaImagen = $img ? $img->ruta : null;
                }
                $imagenFinal = $rutaImagen
                    ? (str_starts_with($rutaImagen, 'http') || str_starts_with($rutaImagen, '/') ? $rutaImagen : "/storage/{$rutaImagen}")
                    : null;

                // Resolver precio / entrada
                $esGratis = $pub->es_gratuito ?? $pub->es_gratis ?? false;
                $montoPrecio = $pub->precio ?? $pub->costo ?? null;
                $precioTexto = $esGratis
                    ? 'Entrada libre'
                    : ($montoPrecio ? '$' . number_format($montoPrecio, 0, ',', '.') : 'Entrada libre');

                // Resolver subtítulo (modalidad, categoría o ubicación)
                $detalleSubtitulo = $pub->modalidad ?? $pub->categoria ?? $pub->ubicacion ?? 'Comunidad';

                return [
                    'titulo'      => $pub->titulo ?? $pub->nombre ?? 'Evento',
                    'subtitulo'   => 'Evento · ' . ucfirst($detalleSubtitulo),
                    'precio'      => $precioTexto,
                    'imagen'      => $imagenFinal,
                    'url_destino' => Route::has('eventos.show') ? route('eventos.show', $id) : '#',
                    'etiqueta'    => 'Evento',
                    'color'       => 'rose',
                    'icono'       => '🎤',
                ];

            case 'empleos':
                $pub = DB::table('ofertas_empleo')->where('id', $id)->first();
                if (!$pub) return null;
                return [
                    'titulo'      => $pub->titulo,
                    'subtitulo'   => 'Oportunidad laboral · ' . ucfirst($pub->modalidad ?? ''),
                    'precio'      => $pub->salario_monto ? '$' . number_format($pub->salario_monto, 0, ',', '.') : 'Salario a convenir',
                    'imagen'      => null,
                    'url_destino' => route('empleos.show', $id),
                    'etiqueta'    => 'Empleo',
                    'color'       => 'indigo',
                    'icono'       => '💼',
                ];

            case 'emprendimientos':
                if (!DB::getSchemaBuilder()->hasTable('emprendimientos')) return null;
                $pub = DB::table('emprendimientos')->where('id', $id)->first();
                if (!$pub) return null;
                return [
                    'titulo'      => $pub->nombre ?? $pub->titulo ?? 'Emprendimiento',
                    'subtitulo'   => $pub->categoria ?? 'Directorio de Emprendimientos',
                    'precio'      => 'Ver catálogo',
                    'imagen'      => isset($pub->logo) && $pub->logo ? "/storage/{$pub->logo}" : null,
                    'url_destino' => Route::has('emprendimientos.show') ? route('emprendimientos.show', $id) : '#',
                    'etiqueta'    => 'Emprendimiento',
                    'color'       => 'purple',
                    'icono'       => '🚀',
                ];

            default:
                return null;
        }
    }

    /**
     * Alternar (Guardar o Quitar) el favorito de cualquier publicación.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'publicacion_id' => ['required', 'integer'],
            'modulo'         => ['required', 'string'],
        ]);

        $userId = Auth::id();
        $publicacionId = (int) $request->input('publicacion_id');
        $modulo = $request->input('modulo');

        $favorito = Favorito::where('user_id', $userId)
            ->where('publicacion_id', $publicacionId)
            ->where('modulo', $modulo)
            ->first();

        if ($favorito) {
            $favorito->delete();
            $esFavorito = false;
        } else {
            Favorito::create([
                'user_id'        => $userId,
                'publicacion_id' => $publicacionId,
                'modulo'         => $modulo,
            ]);
            $esFavorito = true;
        }

        return back()->with([
            'es_favorito' => $esFavorito,
        ]);
    }
}
