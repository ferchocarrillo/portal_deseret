<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Favorito;
use App\Models\InscripcionCurso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class InscripcionCursoController extends Controller
{
    /**
     * Mostrar las inscripciones de un curso propio.
     */
    public function index(
        Request $request,
        Curso $curso
    ): Response {
        $this->autorizarPropietario($request, $curso);

        $inscripciones = $curso->inscripciones()
            ->latest()
            ->get()
            ->map(function (
                InscripcionCurso $inscripcion
            ) {
                return [
                    'id' => $inscripcion->id,

                    'user_id' => $inscripcion->user_id,

                    'nombre_inscrito' =>
                    $inscripcion->nombre_inscrito,

                    'email_inscrito' =>
                    $inscripcion->email_inscrito,

                    'telefono_inscrito' =>
                    $inscripcion->telefono_inscrito,

                    'estado' =>
                    $inscripcion->estado,

                    'visto_at' =>
                    $inscripcion->visto_at?->toISOString(),

                    'activado_at' =>
                    $inscripcion->activado_at?->toISOString(),

                    'motivo_cancelacion' =>
                    $inscripcion->motivo_cancelacion,

                    'observacion_cancelacion' =>
                    $inscripcion->observacion_cancelacion,

                    'cancelada_at' =>
                    $inscripcion->cancelada_at?->toISOString(),

                    'created_at' =>
                    $inscripcion->created_at?->toISOString(),

                    'es_nueva' =>
                    $inscripcion->visto_at === null,
                ];
            })
            ->values()
            ->all();

        return Inertia::render(
            'Cursos/Inscripciones/Index',
            [
                'curso' => [
                    'id' => $curso->id,
                    'nombre' => $curso->titulo,
                    'titulo' => $curso->titulo,
                    'modalidad' => $curso->modalidad,
                    'fecha_inicio' => $curso->fecha_inicio?->format('Y-m-d'),
                    'fecha_fin' => $curso->fecha_fin?->format('Y-m-d'),
                ],

                'inscripciones' => $inscripciones,
            ]
        );
    }

    /**
     * Inscribir al usuario autenticado en un curso.
     */
    public function store(
        Request $request,
        Curso $curso
    ): RedirectResponse {
        $usuario = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Validaciones generales del curso
        |--------------------------------------------------------------------------
        */

        // El creador no puede inscribirse en su propio curso.
        if ((int) $curso->user_id === (int) $usuario->id) {
            throw ValidationException::withMessages([
                'inscripcion' => 'No puedes inscribirte en tu propio curso.',
            ]);
        }

        // Solo se permiten inscripciones en cursos publicados.
        if ($curso->estado !== 'publicado') {
            throw ValidationException::withMessages([
                'inscripcion' => 'Este curso no está disponible para inscripción.',
            ]);
        }

        // Validar la fecha límite de inscripción.
        if (
            $curso->fecha_limite_inscripcion &&
            now()->greaterThan($curso->fecha_limite_inscripcion)
        ) {
            throw ValidationException::withMessages([
                'inscripcion' => 'El plazo de inscripción ha finalizado.',
            ]);
        }

        // Validar el inicio de la publicación.
        if (
            $curso->publicado_desde &&
            now()->lessThan($curso->publicado_desde)
        ) {
            throw ValidationException::withMessages([
                'inscripcion' => 'Este curso todavía no está disponible.',
            ]);
        }

        // Validar el fin de la publicación.
        if (
            $curso->publicado_hasta &&
            now()->greaterThan($curso->publicado_hasta)
        ) {
            throw ValidationException::withMessages([
                'inscripcion' => 'La publicación de este curso ha finalizado.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validar los datos de contacto del perfil
        |--------------------------------------------------------------------------
        */

        $nombre = trim((string) $usuario->name);
        $email = trim((string) $usuario->email);
        $telefono = trim((string) $usuario->telefono);

        $errores = [];

        if ($nombre === '') {
            $errores['perfil'] =
                'Debes completar tu nombre en el perfil antes de inscribirte.';
        }

        if (
            $email === '' ||
            filter_var($email, FILTER_VALIDATE_EMAIL) === false
        ) {
            $errores['perfil'] =
                'Debes registrar un correo electrónico válido en tu perfil antes de inscribirte.';
        }

        if ($telefono === '') {
            $errores['perfil'] =
                'Debes registrar un teléfono en tu perfil antes de inscribirte.';
        }

        if (! empty($errores)) {
            throw ValidationException::withMessages($errores);
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar si ya existe una inscripción
        |--------------------------------------------------------------------------
        */

        $inscripcionExistente = InscripcionCurso::query()
            ->where('curso_id', $curso->id)
            ->where('user_id', $usuario->id)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Si existe una inscripción
        |--------------------------------------------------------------------------
        */

        if ($inscripcionExistente) {
            if ($inscripcionExistente->estado === 'activa') {
                return redirect()
                    ->route('cursos.misInscripciones')
                    ->with(
                        'info',
                        'Ya estás inscrito en este curso.'
                    );
            }

            if ($inscripcionExistente->estado === 'pendiente') {
                return redirect()
                    ->route('cursos.misInscripciones')
                    ->with(
                        'info',
                        'Ya tienes una solicitud de inscripción pendiente para este curso.'
                    );
            }

            if ($inscripcionExistente->estado === 'cancelada') {

                /*
                |--------------------------------------------------------------------------
                | Comprobar cupos disponibles
                |--------------------------------------------------------------------------
                */

                if (
                    ! $curso->cupos_ilimitados &&
                    $curso->cantidad_cupos !== null
                ) {
                    $inscripcionesActivas = $curso->inscripciones()
                        ->where('estado', 'activa')
                        ->count();

                    if (
                        $inscripcionesActivas >=
                        $curso->cantidad_cupos
                    ) {
                        throw ValidationException::withMessages([
                            'inscripcion' =>
                            'No hay cupos disponibles para este curso.',
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Reactivar la solicitud como pendiente
                |--------------------------------------------------------------------------
                */

                $inscripcionExistente->forceFill([
                    'nombre_inscrito' => $nombre,
                    'email_inscrito' => $email,
                    'telefono_inscrito' => $telefono,
                    'estado' => 'pendiente',
                    'visto_at' => null,
                    'activado_at' => null,

                    // Limpiar información de la cancelación anterior.
                    'motivo_cancelacion' => null,
                    'observacion_cancelacion' => null,
                    'cancelada_at' => null,
                ])->save();

                return redirect()->route(
                    'cursos.inscripcion.resultado',
                    $curso
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Comprobar cupos disponibles
        |--------------------------------------------------------------------------
        */

        if (
            ! $curso->cupos_ilimitados &&
            $curso->cantidad_cupos !== null
        ) {
            $inscripcionesActivas = $curso->inscripciones()
                ->where('estado', 'activa')
                ->count();

            if ($inscripcionesActivas >= $curso->cantidad_cupos) {
                throw ValidationException::withMessages([
                    'inscripcion' =>
                    'No hay cupos disponibles para este curso.',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Crear la inscripción
        |--------------------------------------------------------------------------
        */

        try {
            InscripcionCurso::create([
                'curso_id' => $curso->id,
                'user_id' => $usuario->id,

                // Copia de los datos del perfil al inscribirse.
                'nombre_inscrito' => $nombre,
                'email_inscrito' => $email,
                'telefono_inscrito' => $telefono,

                'estado' => 'pendiente',
            ]);
        } catch (\Illuminate\Database\QueryException $e) {

            // Protección adicional ante solicitudes simultáneas.
            if (
                str_contains(
                    strtolower($e->getMessage()),
                    'inscripciones_cursos_curso_usuario_unique'
                )
            ) {
                return redirect()
                    ->route('cursos.misInscripciones')
                    ->with(
                        'info',
                        'Ya tienes una solicitud de inscripción registrada para este curso.'
                    );
            }

            throw $e;
        }

        return redirect()->route(
            'cursos.inscripcion.resultado',
            $curso
        );
    }

    /**
     * Mostrar el detalle de una inscripción.
     */
    public function show(
        Request $request,
        Curso $curso,
        InscripcionCurso $inscripcion
    ): Response {
        $this->autorizarPropietario($request, $curso);

        $this->validarInscripcionDelCurso(
            $curso,
            $inscripcion
        );

        // Marcar como vista solo la primera vez.
        if ($inscripcion->visto_at === null) {
            $inscripcion->forceFill([
                'visto_at' => now(),
            ])->save();
        }

        return Inertia::render(
            'Cursos/Inscripciones/Show',
            [
                'curso' => [
                    'id' => $curso->id,
                    'titulo' => $curso->titulo,
                ],

                'inscripcion' => [
                    'id' => $inscripcion->id,

                    'nombre_inscrito' =>
                    $inscripcion->nombre_inscrito,

                    'email_inscrito' =>
                    $inscripcion->email_inscrito,

                    'telefono_inscrito' =>
                    $inscripcion->telefono_inscrito,

                    'estado' =>
                    $inscripcion->estado,

                    'visto_at' =>
                    $inscripcion->visto_at?->toISOString(),

                    'activado_at' =>
                    $inscripcion->activado_at?->toISOString(),

                    'motivo_cancelacion' =>
                    $inscripcion->motivo_cancelacion,

                    'observacion_cancelacion' =>
                    $inscripcion->observacion_cancelacion,

                    'cancelada_at' =>
                    $inscripcion->cancelada_at?->toISOString(),

                    'created_at' =>
                    $inscripcion->created_at?->toISOString(),
                ],
            ]
        );
    }

    /**
     * Activar una inscripción pendiente.
     */
    public function activar(
        Request $request,
        Curso $curso,
        InscripcionCurso $inscripcion
    ): RedirectResponse {
        $this->autorizarPropietario($request, $curso);

        $this->validarInscripcionDelCurso(
            $curso,
            $inscripcion
        );

        return DB::transaction(
            function () use ($curso, $inscripcion) {

                // Bloquear el curso para evitar superar los cupos
                // cuando se activan varias inscripciones simultáneamente.
                $cursoBloqueado = Curso::query()
                    ->whereKey($curso->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $inscripcionBloqueada =
                    InscripcionCurso::query()
                    ->whereKey($inscripcion->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                |--------------------------------------------------------------------------
                | Validar estado
                |--------------------------------------------------------------------------
                */

                if (
                    $inscripcionBloqueada->estado === 'activa'
                ) {
                    return back()->with(
                        'success',
                        'Esta inscripción ya se encuentra activa.'
                    );
                }

                if (
                    $inscripcionBloqueada->estado !== 'pendiente'
                ) {
                    return back()->with(
                        'error',
                        'Solo se pueden activar inscripciones pendientes.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Comprobar capacidad del curso
                |--------------------------------------------------------------------------
                */

                if (
                    ! $cursoBloqueado->cupos_ilimitados &&
                    $cursoBloqueado->cantidad_cupos !== null
                ) {
                    $activas = $cursoBloqueado
                        ->inscripciones()
                        ->where('estado', 'activa')
                        ->count();

                    if (
                        $activas >=
                        $cursoBloqueado->cantidad_cupos
                    ) {
                        throw ValidationException::withMessages([
                            'inscripcion' =>
                            'No hay cupos disponibles para activar esta inscripción.',
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Activar
                |--------------------------------------------------------------------------
                */

                $inscripcionBloqueada->forceFill([
                    'estado' => 'activa',
                    'activado_at' => now(),
                    'visto_at' =>
                    $inscripcionBloqueada->visto_at ?? now(),

                    // Una inscripción activa no debe conservar
                    // datos de una cancelación anterior.
                    'motivo_cancelacion' => null,
                    'observacion_cancelacion' => null,
                    'cancelada_at' => null,
                ])->save();

                return back()->with(
                    'success',
                    'La inscripción fue activada correctamente.'
                );
            }
        );
    }

    /**
     * Autorizar únicamente al creador del curso.
     */
    private function autorizarPropietario(
        Request $request,
        Curso $curso
    ): void {
        abort_unless(
            (int) $curso->user_id ===
                (int) $request->user()->id,
            403,
            'No tienes permiso para administrar las inscripciones de este curso.'
        );
    }

    /**
     * Verificar que la inscripción pertenezca al curso indicado.
     */
    private function validarInscripcionDelCurso(
        Curso $curso,
        InscripcionCurso $inscripcion
    ): void {
        abort_unless(
            (int) $inscripcion->curso_id ===
                (int) $curso->id,
            404
        );
    }

    /**
     * Mostrar las inscripciones del usuario autenticado.
     */
    public function misInscripciones(
        Request $request
    ): Response {
        $usuarioId = $request->user()->id;

        $favoritosIds = Favorito::query()
            ->where('user_id', $usuarioId)
            ->where('modulo', 'cursos')
            ->pluck('publicacion_id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $inscripciones = InscripcionCurso::query()
            ->with([
                'curso:id,titulo,slug,estado,modalidad',
            ])
            ->where(
                'user_id',
                $usuarioId
            )
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(function (
                InscripcionCurso $inscripcion
            ) use ($favoritosIds) {
                return [
                    'id' => $inscripcion->id,

                    'curso' => $inscripcion->curso
                        ? [
                            'id' =>
                            $inscripcion->curso->id,

                            'titulo' =>
                            $inscripcion->curso->titulo,

                            'slug' =>
                            $inscripcion->curso->slug,

                            'estado' =>
                            $inscripcion->curso->estado,

                            'modalidad' =>
                            $inscripcion->curso->modalidad,

                            'es_favorito' => in_array((int) $inscripcion->curso->id, $favoritosIds, true),
                        ]
                        : null,

                    'estado' =>
                    $inscripcion->estado,

                    'created_at' =>
                    $inscripcion->created_at?->toISOString(),

                    'activado_at' =>
                    $inscripcion->activado_at?->toISOString(),

                    'motivo_cancelacion' =>
                    $inscripcion->motivo_cancelacion,

                    'observacion_cancelacion' =>
                    $inscripcion->observacion_cancelacion,

                    'cancelada_at' =>
                    $inscripcion->cancelada_at?->toISOString(),
                ];
            });

        return Inertia::render(
            'Cursos/MisInscripciones',
            [
                'inscripciones' => $inscripciones,
            ]
        );
    }

    /**
     * Exportar las inscripciones de un curso a CSV.
     */
    public function exportar(
        Request $request,
        Curso $curso
    ) {
        $this->autorizarPropietario(
            $request,
            $curso
        );

        $inscripciones = $curso->inscripciones()
            ->orderBy('created_at')
            ->get([
                'id',
                'nombre_inscrito',
                'email_inscrito',
                'telefono_inscrito',
                'estado',
                'motivo_cancelacion',
                'observacion_cancelacion',
                'cancelada_at',
                'visto_at',
                'activado_at',
                'created_at',
            ]);

        $nombreArchivo =
            'inscripciones-curso-' .
            $curso->id .
            '.csv';

        return response()->streamDownload(
            function () use ($inscripciones) {

                $salida = fopen(
                    'php://output',
                    'w'
                );

                // BOM para que Excel reconozca correctamente
                // los acentos.
                fwrite(
                    $salida,
                    "\xEF\xBB\xBF"
                );

                fputcsv(
                    $salida,
                    [
                        'ID',
                        'Nombre',
                        'Correo electrónico',
                        'Teléfono',
                        'Estado',
                        'Motivo de cancelación',
                        'Observación de cancelación',
                        'Fecha de cancelación',
                        'Fecha de inscripción',
                        'Fecha de visualización',
                        'Fecha de activación',
                    ],
                    ';'
                );

                foreach (
                    $inscripciones
                    as $inscripcion
                ) {
                    fputcsv(
                        $salida,
                        [
                            $inscripcion->id,

                            $inscripcion->nombre_inscrito,

                            $inscripcion->email_inscrito,

                            $inscripcion->telefono_inscrito,

                            $inscripcion->estado,

                            $inscripcion->motivo_cancelacion,

                            $inscripcion->observacion_cancelacion,

                            $inscripcion
                                ->cancelada_at
                                ?->format(
                                    'Y-m-d H:i:s'
                                ),

                            $inscripcion
                                ->created_at
                                ?->format(
                                    'Y-m-d H:i:s'
                                ),

                            $inscripcion
                                ->visto_at
                                ?->format(
                                    'Y-m-d H:i:s'
                                ),

                            $inscripcion
                                ->activado_at
                                ?->format(
                                    'Y-m-d H:i:s'
                                ),
                        ],
                        ';'
                    );
                }

                fclose($salida);
            },
            $nombreArchivo,
            [
                'Content-Type' =>
                'text/csv; charset=UTF-8',
            ]
        );
    }

    /**
     * Mostrar la pantalla de confirmación antes de inscribir.
     */

    public function confirmar(
        Request $request,
        Curso $curso
    ): Response|RedirectResponse {
        $usuario = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Validaciones generales
        |--------------------------------------------------------------------------
        */

        // El creador no puede inscribirse en su propio curso.
        if (
            (int) $curso->user_id ===
            (int) $usuario->id
        ) {
            return redirect()
                ->route('cursos.index')
                ->with(
                    'error',
                    'No puedes inscribirte en tu propio curso.'
                );
        }

        // Solo se permiten inscripciones en cursos publicados.
        if ($curso->estado !== 'publicado') {
            return redirect()
                ->route('cursos.index')
                ->with(
                    'error',
                    'Este curso no está disponible para inscripción.'
                );
        }

        // Validar fecha límite.
        if (
            $curso->fecha_limite_inscripcion &&
            now()->greaterThan(
                $curso->fecha_limite_inscripcion
            )
        ) {
            return redirect()
                ->route('cursos.index')
                ->with(
                    'error',
                    'El plazo de inscripción ha finalizado.'
                );
        }

        // Validar inicio de publicación.
        if (
            $curso->publicado_desde &&
            now()->lessThan(
                $curso->publicado_desde
            )
        ) {
            return redirect()
                ->route('cursos.index')
                ->with(
                    'error',
                    'Este curso todavía no está disponible.'
                );
        }

        // Validar fin de publicación.
        if (
            $curso->publicado_hasta &&
            now()->greaterThan(
                $curso->publicado_hasta
            )
        ) {
            return redirect()
                ->route('cursos.index')
                ->with(
                    'error',
                    'La publicación de este curso ha finalizado.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar inscripción existente
        |--------------------------------------------------------------------------
        */

        $inscripcionExistente =
            InscripcionCurso::query()
            ->where(
                'curso_id',
                $curso->id
            )
            ->where(
                'user_id',
                $usuario->id
            )
            ->first();

        if ($inscripcionExistente) {

            // Si está activa, no puede volver a solicitar.
            if (
                $inscripcionExistente->estado ===
                'activa'
            ) {
                return redirect()
                    ->route(
                        'cursos.misInscripciones'
                    )
                    ->with(
                        'info',
                        'Ya estás inscrito en este curso.'
                    );
            }

            // Si está pendiente, tampoco puede duplicar
            // la solicitud.
            if (
                $inscripcionExistente->estado ===
                'pendiente'
            ) {
                return redirect()
                    ->route(
                        'cursos.misInscripciones'
                    )
                    ->with(
                        'info',
                        'Ya tienes una solicitud de inscripción pendiente para este curso.'
                    );
            }

            if (
                $inscripcionExistente->estado ===
                'cancelada'
            ) {
                // Continuamos normalmente.
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Datos actuales del usuario
        |--------------------------------------------------------------------------
        */

        $nombre = trim(
            (string) $usuario->name
        );

        $email = trim(
            (string) $usuario->email
        );

        $telefono = trim(
            (string) $usuario->telefono
        );

        $errores = [];

        if ($nombre === '') {
            $errores['perfil'] =
                'Debes completar tu nombre en el perfil antes de inscribirte.';
        }

        if (
            $email === '' ||
            filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            ) === false
        ) {
            $errores['perfil'] =
                'Debes registrar un correo electrónico válido en tu perfil antes de inscribirte.';
        }

        if ($telefono === '') {
            $errores['perfil'] =
                'Debes registrar un teléfono en tu perfil antes de inscribirte.';
        }

        if (! empty($errores)) {
            throw ValidationException::withMessages(
                $errores
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar cupos
        |--------------------------------------------------------------------------
        */

        $cuposDisponibles = null;

        if (
            ! $curso->cupos_ilimitados &&
            $curso->cantidad_cupos !== null
        ) {
            $inscripcionesActivas =
                $curso->inscripciones()
                ->where(
                    'estado',
                    'activa'
                )
                ->count();

            // CORREGIDO: Se agregó el operador de resta (-) que faltaba aquí
            $cuposDisponibles = max(
                0,
                $curso->cantidad_cupos - $inscripcionesActivas
            );

            if ($cuposDisponibles <= 0) {
                return redirect()
                    ->route('cursos.index')
                    ->with(
                        'error',
                        'No hay cupos disponibles para este curso.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Verificar si el curso está guardado en favoritos
        |--------------------------------------------------------------------------
        */

        $esFavorito = Favorito::query()
            ->where('user_id', $usuario->id)
            ->where('publicacion_id', (int) $curso->id)
            ->where('modulo', 'cursos')
            ->exists();

        return Inertia::render(
            'Cursos/Inscripcion/Confirmar',
            [
                'curso' => [
                    'id' => $curso->id,
                    'titulo' => $curso->titulo,
                    'slug' => $curso->slug,
                    'modalidad' => $curso->modalidad,
                    'fecha_inicio' => $curso->fecha_inicio?->toISOString(),
                    'fecha_fin' => $curso->fecha_fin?->toISOString(),
                    'cantidad_cupos' => $curso->cantidad_cupos,
                    'cupos_ilimitados' => (bool) $curso->cupos_ilimitados,
                    'cupos_disponibles' => $cuposDisponibles,
                    'es_favorito' => (bool) $esFavorito, // Booleano estricto
                ],
                'participante' => [
                    'nombre' => $nombre,
                    'email' => $email,
                    'telefono' => $telefono,
                ],
            ]
        );
    }


    /**
     * Mostrar el resultado de la inscripción.
     */
    public function resultado(
        Request $request,
        Curso $curso
    ): Response|RedirectResponse {
        $usuario = $request->user();

        $inscripcion =
            InscripcionCurso::query()
            ->where(
                'curso_id',
                $curso->id
            )
            ->where(
                'user_id',
                $usuario->id
            )
            ->first();

        if (! $inscripcion) {
            return redirect()
                ->route(
                    'cursos.inscripcion.confirmar',
                    $curso
                )
                ->with(
                    'error',
                    'Todavía no tienes una inscripción registrada para este curso.'
                );
        }

        $esFavorito = Favorito::query()
            ->where('user_id', $usuario->id)
            ->where('publicacion_id', (int) $curso->id)
            ->where('modulo', 'cursos')
            ->exists();

        return Inertia::render(
            'Cursos/Inscripcion/Confirmar',
            [
                'curso' => [
                    'id' => $curso->id,
                    'titulo' => $curso->titulo,
                    'slug' => $curso->slug,
                    'modalidad' => $curso->modalidad,
                    'fecha_inicio' => $curso->fecha_inicio?->toISOString(),
                    'fecha_fin' => $curso->fecha_fin?->toISOString(),
                    'cantidad_cupos' => $curso->cantidad_cupos,
                    'cupos_ilimitados' => $curso->cupos_ilimitados,
                    'cupos_disponibles' => $cuposDisponibles,
                    'es_favorito' => (bool) $esFavorito, // <-- Sincroniza el estado inicial al cargar
                ],
                'participante' => [
                    'nombre' => $nombre,
                    'email' => $email,
                    'telefono' => $telefono,
                ],
            ]
        );
    }

    /**
     * Cancelar una inscripción pendiente del usuario autenticado.
     */
    public function cancelar(
        Request $request,
        Curso $curso,
        InscripcionCurso $inscripcion
    ): RedirectResponse {
        $usuario = $request->user();

        $this->validarInscripcionDelCurso(
            $curso,
            $inscripcion
        );

        if (
            (int) $inscripcion->user_id !==
            (int) $usuario->id
        ) {
            abort(
                403,
                'No tienes permiso para cancelar esta inscripción.'
            );
        }

        if ($inscripcion->estado !== 'pendiente') {
            return back()->with(
                'error',
                'Esta inscripción ya no puede cancelarse desde esta sección.'
            );
        }

        $datos = $request->validate([
            'motivo_cancelacion' => [
                'required',
                'string',
                'in:ya_no_puedo_asistir,horario_no_disponible,modalidad_no_conveniente,inscripcion_por_error,otra_alternativa,motivos_personales,motivos_laborales,motivos_economicos,otro',
            ],

            'observacion_cancelacion' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'motivo_cancelacion.required' =>
            'Debes seleccionar un motivo para cancelar la inscripción.',

            'motivo_cancelacion.in' =>
            'El motivo seleccionado no es válido.',

            'observacion_cancelacion.max' =>
            'La explicación no puede superar los 1000 caracteres.',
        ]);

        if (
            $datos['motivo_cancelacion'] === 'otro' &&
            trim(
                (string) (
                    $datos['observacion_cancelacion']
                    ?? ''
                )
            ) === ''
        ) {
            throw ValidationException::withMessages([
                'observacion_cancelacion' =>
                'Debes explicar brevemente el motivo de la cancelación.',
            ]);
        }

        $inscripcion->forceFill([
            'estado' => 'cancelada',

            'motivo_cancelacion' =>
            $datos['motivo_cancelacion'],

            'observacion_cancelacion' =>
            ! empty(trim(
                (string) (
                    $datos['observacion_cancelacion']
                    ?? ''
                )
            ))
                ? trim(
                    (string) (
                        $datos['observacion_cancelacion']
                        ?? ''
                    )
                )
                : null,

            'cancelada_at' => now(),

            'activado_at' => null,
        ])->save();

        return back()->with(
            'success',
            'Tu inscripción fue cancelada correctamente.'
        );
    }
}
