<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Ciudad;
use App\Models\DivisionAdministrativa;
use App\Models\MarketplaceCalificacion;
use App\Models\MarketplaceCategoria;
use App\Models\MarketplaceConversacion;
use App\Models\MarketplacePublicacion;
use App\Models\Pais;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PublicacionController extends Controller
{
    /**
     * Mostrar una publicación.
     */
    public function show(MarketplacePublicacion $publicacion): Response
    {
        abort_unless(
            $publicacion->estado_publicacion === 'publicada'
                || auth()->id() === $publicacion->user_id,
            404
        );

        $publicacion->load([
            'usuario:id,name,nombres,apellidos',
            'categoria:id,nombre,slug',
            'subcategoria:id,categoria_id,nombre,slug',
            'pais',
            'divisionAdministrativa',
            'ciudad',

            'imagenes' => function ($query) {
                $query->orderByDesc('es_principal')
                    ->orderBy('orden');
            },

            'video',

            'calificaciones' => function ($query) {
                $query
                    ->where('estado', 'publicada')
                    ->with([
                        'comprador:id,name,nombres,apellidos',
                    ])
                    ->latest();
            },
        ]);

        /*
        |--------------------------------------------------------------------------
        | Favorito
        |--------------------------------------------------------------------------
        */
        $esFavorito = false;

        if (auth()->check()) {
            $esFavorito = \App\Models\Favorito::query()
                ->where('user_id', auth()->id())
                ->where('publicacion_id', (int) $publicacion->id)
                ->where('modulo', 'marketplace')
                ->exists();
        }

        /*
        |--------------------------------------------------------------------------
        | Reputación
        |--------------------------------------------------------------------------
        */
        $calificaciones = $publicacion->calificaciones;
        $totalCalificaciones = $calificaciones->count();
        $promedioCalificacion = $totalCalificaciones > 0
            ? round($calificaciones->avg('calificacion'), 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Calificación del usuario actual
        |--------------------------------------------------------------------------
        */
        $calificacionUsuario = null;

        if (auth()->check()) {
            $calificacionUsuario = $calificaciones
                ->firstWhere('comprador_id', auth()->id());
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar si el usuario puede calificar
        |--------------------------------------------------------------------------
        */
        $puedeCalificar = false;

        if (
            auth()->check()
            && auth()->id() !== $publicacion->user_id
        ) {
            $puedeCalificar = MarketplaceConversacion::query()
                ->where('publicacion_id', $publicacion->id)
                ->where('comprador_id', auth()->id())
                ->where('vendedor_id', $publicacion->user_id)
                ->exists();

            if ($calificacionUsuario) {
                $puedeCalificar = false;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Resumen de reputación
        |--------------------------------------------------------------------------
        */
        $resumenCalificaciones = [
            'total' => $totalCalificaciones,
            'promedio' => $promedioCalificacion,
            'distribucion' => [
                5 => $calificaciones->where('calificacion', 5)->count(),
                4 => $calificaciones->where('calificacion', 4)->count(),
                3 => $calificaciones->where('calificacion', 3)->count(),
                2 => $calificaciones->where('calificacion', 2)->count(),
                1 => $calificaciones->where('calificacion', 1)->count(),
            ],
        ];

        return Inertia::render('Marketplace/Show', [
            'publicacion'           => $publicacion,
            'esFavorito'            => $esFavorito,
            'calificaciones'        => $calificaciones,
            'resumenCalificaciones' => $resumenCalificaciones,
            'calificacionUsuario'   => $calificacionUsuario,
            'puedeCalificar'        => $puedeCalificar,
        ]);
    }

    /**
     * Mostrar formulario para crear una publicación.
     */
    public function create(): Response
    {
        $categorias = MarketplaceCategoria::query()
            ->where('activo', true)
            ->with([
                'subcategorias' => function ($query) {
                    $query->where('activo', true)
                        ->orderBy('orden')
                        ->orderBy('nombre');
                },
            ])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        $palabrasProhibidas = config('moderacion.palabras_prohibidas', []);

        // Ubicación por defecto basada en el registro de residencia del usuario
        $usuario = auth()->user();
        $recepcion = $usuario?->informacionRecepcion;

        $ubicacionDefecto = [
            'pais'                       => $recepcion?->pais ?? 'Colombia',
            'departamento'               => $recepcion?->departamento ?? '',
            'ciudad'                     => $recepcion?->ciudad ?? '',
            'pais_id'                    => null,
            'division_administrativa_id' => null,
            'ciudad_id'                  => null,
        ];

        if ($recepcion && !empty($recepcion->ciudad)) {
            $nombreCiudad = trim($recepcion->ciudad);
            $ciudadObj = Ciudad::where('nombre', 'like', "%{$nombreCiudad}%")->first();

            if ($ciudadObj) {
                $ubicacionDefecto['ciudad_id'] = (int) $ciudadObj->id;
                $ubicacionDefecto['division_administrativa_id'] = (int) ($ciudadObj->division_administrativa_id ?? $ciudadObj->departamento_id ?? 0) ?: null;
                $ubicacionDefecto['pais_id'] = (int) ($ciudadObj->pais_id ?? 0) ?: null;
            }
        }

        if (!$ubicacionDefecto['division_administrativa_id'] && $recepcion && !empty($recepcion->departamento)) {
            $nombreDep = trim($recepcion->departamento);
            $divObj = DivisionAdministrativa::where('nombre', 'like', "%{$nombreDep}%")->first();

            if ($divObj) {
                $ubicacionDefecto['division_administrativa_id'] = (int) $divObj->id;
                $ubicacionDefecto['pais_id'] = $ubicacionDefecto['pais_id'] ?? ((int) ($divObj->pais_id ?? 0) ?: null);
            }
        }

        if (!$ubicacionDefecto['pais_id'] && $recepcion && !empty($recepcion->pais)) {
            $nombrePais = trim($recepcion->pais);
            $paisObj = Pais::where('nombre', 'like', "%{$nombrePais}%")->first();

            if ($paisObj) {
                $ubicacionDefecto['pais_id'] = (int) $paisObj->id;
            }
        }

        return Inertia::render('Marketplace/Create', [
            'categorias'         => $categorias,
            'palabrasProhibidas' => $palabrasProhibidas,
            'ubicacionDefecto'   => $ubicacionDefecto,
        ]);
    }

    /**
     * Guardar una nueva publicación.
     */
    /**
     * Guardar una nueva publicación.
     */
    public function store(Request $request): RedirectResponse
    {


        $validated = $this->validatePublicacion($request);

        $publicacion = DB::transaction(function () use (
            $request,
            $validated
        ) {
            $publicacion = MarketplacePublicacion::create([
                'user_id'                    => auth()->id(),
                'categoria_id'               => $validated['categoria_id'],
                'subcategoria_id'            => $validated['subcategoria_id'] ?? null,
                'titulo'                     => $validated['titulo'],
                'slug'                       => $this->generarSlug($validated['titulo']),
                'descripcion'                => $validated['descripcion'],
                'precio'                     => $validated['es_gratis'] ? null : ($validated['precio'] ?? null),
                'es_gratis'                  => $validated['es_gratis'],
                'estado_producto'            => $validated['estado_producto'],
                'estado_publicacion'         => 'publicada',
                'pais_id'                    => $validated['pais_id'] ?? null,
                'division_administrativa_id' => $validated['division_administrativa_id'] ?? null,
                'ciudad_id'                  => $validated['ciudad_id'] ?? null,
                'tipo_entrega'               => $validated['tipo_entrega'] ?? null,
                'rango_cobertura'            => $validated['rango_cobertura'],
                'fecha_publicacion'          => now(),
            ]);

            // 2. Guardamos los archivos pasando la instancia ya creada (que ya tiene ID)
            $this->guardarArchivos(
                $request,
                $publicacion
            );

            return $publicacion;
        });

        return redirect()
            ->route('marketplace.show', $publicacion)
            ->with(
                'success',
                'Tu publicación fue creada correctamente.'
            );
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(MarketplacePublicacion $publicacion): Response
    {
        $this->autorizarPropietario($publicacion);

        $categorias = MarketplaceCategoria::query()
            ->where('activo', true)
            ->with([
                'subcategorias' => function ($query) {
                    $query->where('activo', true)
                        ->orderBy('orden')
                        ->orderBy('nombre');
                },
            ])
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        $publicacion->load([
            'categoria',
            'subcategoria',
            'imagenes' => function ($query) {
                $query->orderByDesc('es_principal')
                    ->orderBy('orden');
            },
            'video',
        ]);

        $palabrasProhibidas = config('moderacion.palabras_prohibidas', []);

        return Inertia::render('Marketplace/Edit', [
            'publicacion'        => $publicacion,
            'categorias'         => $categorias,
            'palabrasProhibidas' => $palabrasProhibidas,
        ]);
    }

    /**
     * Actualizar una publicación.
     */
    public function update(
        Request $request,
        MarketplacePublicacion $publicacion
    ): RedirectResponse {
        $this->autorizarPropietario($publicacion);

        $validated = $this->validatePublicacion(
            $request,
            $publicacion
        );

        DB::transaction(function () use (
            $request,
            $validated,
            $publicacion
        ) {
            $publicacion->update([
                'categoria_id'               => $validated['categoria_id'],
                'subcategoria_id'            => $validated['subcategoria_id'] ?? null,
                'titulo'                     => $validated['titulo'],
                'descripcion'                => $validated['descripcion'],
                'precio'                     => $validated['es_gratis'] ? null : ($validated['precio'] ?? null),
                'es_gratis'                  => $validated['es_gratis'],
                'estado_producto'            => $validated['estado_producto'],
                // Si vienen vacíos o nulos, conservamos los que ya tenía la publicación en la BD
                'pais_id'                    => $validated['pais_id'] ?? $publicacion->pais_id,
                'division_administrativa_id' => $validated['division_administrativa_id'] ?? $publicacion->division_administrativa_id,
                'ciudad_id'                  => $validated['ciudad_id'] ?? $publicacion->ciudad_id,
                'tipo_entrega'               => $validated['tipo_entrega'] ?? null,
                'rango_cobertura'            => $validated['rango_cobertura'],
            ]);

            // Eliminar imágenes seleccionadas para borrar
            if ($request->filled('imagenes_a_eliminar')) {
                $idsAEliminar = $request->input('imagenes_a_eliminar');
                $imagenesABorrar = $publicacion->imagenes()->whereIn('id', $idsAEliminar)->get();

                foreach ($imagenesABorrar as $img) {
                    Storage::disk('public')->delete($img->ruta);
                    $img->delete();
                }
            }

            // Eliminar video si se solicitó o si se va a reemplazar
            if ($request->boolean('eliminar_video') || $request->hasFile('video')) {
                if ($publicacion->video) {
                    Storage::disk('public')->delete($publicacion->video->ruta);
                    $publicacion->video()->delete();
                }
            }

            $this->guardarArchivos(
                $request,
                $publicacion
            );
        });

        return redirect()
            ->route('marketplace.show', $publicacion)
            ->with(
                'success',
                'La publicación fue actualizada correctamente.'
            );
    }

    /**
     * Eliminar una publicación.
     */
    public function destroy(MarketplacePublicacion $publicacion): RedirectResponse
    {
        $this->autorizarPropietario($publicacion);

        $publicacion->update([
            'estado_publicacion' => 'eliminada',
        ]);

        $publicacion->delete();

        return redirect()
            ->route('marketplace.mis-publicaciones')
            ->with(
                'success',
                'La publicación fue eliminada correctamente.'
            );
    }

    /**
     * Listar publicaciones del usuario autenticado.
     */
    public function misPublicaciones(Request $request): Response
    {
        $publicaciones = MarketplacePublicacion::query()
            ->with([
                'categoria:id,nombre,slug',
                'subcategoria:id,categoria_id,nombre,slug',
                'imagenes' => function ($query) {
                    $query->orderByDesc('es_principal')
                        ->orderBy('orden');
                },
            ])
            ->where('user_id', auth()->id())
            ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Marketplace/MisPublicaciones', [
            'publicaciones' => $publicaciones,
            'estado'        => $request->get('estado'),
        ]);
    }

    /**
     * Pausar una publicación.
     */
    public function pausar(MarketplacePublicacion $publicacion): RedirectResponse
    {
        $this->autorizarPropietario($publicacion);

        if ($publicacion->estado_publicacion !== 'publicada') {
            return back()->with(
                'error',
                'Solo puedes pausar una publicación que esté publicada.'
            );
        }

        $publicacion->update([
            'estado_publicacion' => 'pausada',
            'fecha_pausa'        => now(),
        ]);

        return back()->with(
            'success',
            'La publicación fue pausada.'
        );
    }

    /**
     * Reactivar una publicación.
     */
    public function activar(MarketplacePublicacion $publicacion): RedirectResponse
    {
        $this->autorizarPropietario($publicacion);

        if ($publicacion->estado_publicacion !== 'pausada') {
            return back()->with(
                'error',
                'Solo puedes activar una publicación pausada.'
            );
        }

        $publicacion->update([
            'estado_publicacion' => 'publicada',
            'fecha_pausa'        => null,
            'fecha_publicacion'  => now(),
        ]);

        return back()->with(
            'success',
            'La publicación fue reactivada.'
        );
    }

    /**
     * Marcar una publicación como reservada.
     */
    public function reservar(MarketplacePublicacion $publicacion): RedirectResponse
    {
        $this->autorizarPropietario($publicacion);

        if ($publicacion->estado_publicacion !== 'publicada') {
            return back()->with(
                'error',
                'Solo puedes reservar una publicación que esté publicada.'
            );
        }

        $publicacion->update([
            'estado_publicacion' => 'reservada',
            'fecha_reserva'      => now(),
        ]);

        return back()->with(
            'success',
            'La publicación fue marcada como reservada.'
        );
    }

    /**
     * Marcar una publicación como vendida.
     */
    public function vendida(MarketplacePublicacion $publicacion): RedirectResponse
    {
        $this->autorizarPropietario($publicacion);

        if (
            !in_array(
                $publicacion->estado_publicacion,
                ['publicada', 'reservada'],
                true
            )
        ) {
            return back()->with(
                'error',
                'Esta publicación no puede marcarse como vendida.'
            );
        }

        $publicacion->update([
            'estado_publicacion' => 'vendida',
            'fecha_venta'        => now(),
        ]);

        return back()->with(
            'success',
            'La publicación fue marcada como vendida.'
        );
    }

    /**
     * Guardar una calificación del vendedor.
     */
    public function guardarCalificacion(
        Request $request,
        MarketplacePublicacion $publicacion
    ): RedirectResponse {
        $validated = $request->validate([
            'conversacion_id' => [
                'required',
                'integer',
                'exists:marketplace_conversaciones,id',
            ],
            'calificacion' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
            'comentario' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $conversacion = MarketplaceConversacion::query()
            ->where('id', $validated['conversacion_id'])
            ->where('publicacion_id', $publicacion->id)
            ->where('comprador_id', auth()->id())
            ->where('vendedor_id', $publicacion->user_id)
            ->firstOrFail();

        abort_if(
            auth()->id() === $publicacion->user_id,
            403,
            'No puedes calificar tu propia publicación.'
        );

        if (
            MarketplaceCalificacion::query()
            ->where('conversacion_id', $conversacion->id)
            ->exists()
        ) {
            return back()->with(
                'error',
                'Ya has calificado esta conversación.'
            );
        }

        MarketplaceCalificacion::create([
            'publicacion_id'  => $publicacion->id,
            'conversacion_id' => $conversacion->id,
            'comprador_id'    => auth()->id(),
            'vendedor_id'     => $publicacion->user_id,
            'calificacion'    => $validated['calificacion'],
            'comentario'      => $validated['comentario'] ?? null,
            'estado'          => 'publicada',
        ]);

        return back()->with(
            'success',
            'Tu calificación fue registrada correctamente.'
        );
    }

    /**
     * Responder una calificación.
     */
    public function responderCalificacion(
        Request $request,
        MarketplaceCalificacion $calificacion
    ): RedirectResponse {
        abort_unless(
            auth()->id() === $calificacion->vendedor_id,
            403
        );

        $validated = $request->validate([
            'respuesta_vendedor' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        if ($calificacion->respuesta_vendedor) {
            return back()->with(
                'error',
                'Esta calificación ya tiene una respuesta.'
            );
        }

        $calificacion->update([
            'respuesta_vendedor' => $validated['respuesta_vendedor'],
            'respuesta_at'       => now(),
        ]);

        return back()->with(
            'success',
            'Tu respuesta fue publicada correctamente.'
        );
    }

    /**
     * Validación central de publicaciones con obligatoriedad territorial según rango.
     */
    /**
     * Validación central de publicaciones con obligatoriedad territorial según rango.
     */
    /**
     * Validación limpia y directa para la creación y edición.
     */
    private function validatePublicacion(
        Request $request,
        ?MarketplacePublicacion $publicacion = null
    ): array {
        $reglas = [
            'categoria_id' => [
                'required',
                'integer',
                'exists:marketplace_categorias,id',
            ],

            'subcategoria_id' => [
                'nullable',
                'integer',
                'exists:marketplace_subcategorias,id',
            ],

            'titulo' => [
                'required',
                'string',
                'min:5',
                'max:255',
            ],

            'descripcion' => [
                'required',
                'string',
                'min:10',
            ],

            'es_gratis' => [
                'required',
                'boolean',
            ],

            'precio' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999999.99',
            ],

            'estado_producto' => [
                'required',
                Rule::in([
                    'nuevo',
                    'como_nuevo',
                    'buen_estado',
                    'usado',
                    'para_reparar',
                ]),
            ],

            'rango_cobertura' => [
                'required',
                'string',
                Rule::in([
                    'local',
                    'departamental',
                    'nacional',
                    'internacional',
                ]),
            ],

            'pais_id' => [
                'required',
                'integer',
                'exists:paises,id',
            ],

            'division_administrativa_id' => [
                Rule::requiredIf(fn() => in_array($request->rango_cobertura, ['local', 'departamental'])),
                'nullable',
                'integer',
                'exists:divisiones_administrativas,id',
            ],

            'ciudad_id' => [
                Rule::requiredIf(fn() => in_array($request->rango_cobertura, ['local', 'departamental'])),
                'nullable',
                'integer',
                'exists:ciudades,id',
            ],

            'tipo_entrega' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
            |--------------------------------------------------------------------------
            | Imágenes
            |--------------------------------------------------------------------------
            */
            'imagenes' => [
                'nullable',
                'array',
                'max:8',
            ],

            'imagenes.*' => [
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            /*
            |--------------------------------------------------------------------------
            | Video (Solo formato válido y máximo 3 minutos / 180 segundos)
            |--------------------------------------------------------------------------
            */
            'video' => [
                'nullable',
                'file',
                'mimes:mp4,webm,mov',
                'max:51200', // Límite en KB (50MB)
                function ($attribute, $value, $fail) {
                    if ($value) {
                        // Obtenemos la ruta temporal del video subido
                        $path = $value->getRealPath();

                        // Intentamos leer la duración usando getID3 o nativamente si tienes getimagesize/ffmpeg (o una validación por tamaño/cabecera básica si no usas paquete externo)
                        // Como Laravel no lee duración de video de forma nativa sin herramientas, validamos por regla general o puedes usar la validación de tamaño que ya cubre el peso. 
                        // Si requieres medir los segundos exactos por código nativo de PHP sin paquetes:
                        $getID3 = new \getID3();
                        $fileInfo = $getID3->analyze($path);
                        $duracion = $fileInfo['playtime_seconds'] ?? 0;

                        if ($duracion > 180) {
                            $fail('El video no puede durar más de 3 minutos.');
                        }
                    }
                },
            ],
        ];

        // Reglas exclusivas si viene de edición
        if ($publicacion) {
            $reglas['imagenes_a_eliminar'] = ['nullable', 'array'];
            $reglas['imagenes_a_eliminar.*'] = ['integer'];
            $reglas['eliminar_video'] = ['nullable', 'boolean'];
        }

        return $request->validate($reglas);
    }

    /**
     * Guardar imágenes y video.
     */
    private function guardarArchivos(
        Request $request,
        MarketplacePublicacion $publicacion
    ): void {
        // Imágenes
        if ($request->hasFile('imagenes')) {
            $nuevasImagenes = $request->file('imagenes');

            foreach ($nuevasImagenes as $index => $imagen) {
                $ruta = $imagen->store(
                    'marketplace/' . $publicacion->id . '/imagenes',
                    'public'
                );

                $publicacion->imagenes()->create([
                    'ruta'            => $ruta,
                    'nombre_original' => $imagen->getClientOriginalName(),
                    'mime_type'       => $imagen->getMimeType(),
                    'tamano'          => $imagen->getSize(),
                    'orden'           => $index,
                    'es_principal'    => $index === 0,
                ]);
            }
        }

        // Video
        if ($request->hasFile('video')) {
            $video = $request->file('video');

            $ruta = $video->store(
                'marketplace/' . $publicacion->id . '/video',
                'public'
            );

            $publicacion->video()->create([
                'ruta'            => $ruta,
                'nombre_original' => $video->getClientOriginalName(),
                'mime_type'       => $video->getMimeType(),
                'tamano'          => $video->getSize(),
                'duracion'        => null,
            ]);
        }
    }

    /**
     * Generar un slug único.
     */
    private function generarSlug(string $titulo): string
    {
        $slugBase = Str::slug($titulo);
        $slug = $slugBase;
        $contador = 1;

        while (
            MarketplacePublicacion::withTrashed()
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $slugBase . '-' . $contador;
            $contador++;
        }

        return $slug;
    }

    /**
     * Verificar que el usuario sea propietario.
     */
    private function autorizarPropietario(
        MarketplacePublicacion $publicacion
    ): void {
        abort_unless(
            auth()->check()
                && auth()->id() === $publicacion->user_id,
            403
        );
    }
}
