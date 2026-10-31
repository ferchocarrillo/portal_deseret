<?php

use App\Http\Controllers\Admin\Marketplace\CategoriaAdminController;
use App\Http\Controllers\Admin\Marketplace\MarketplaceAdminController;
use App\Http\Controllers\Admin\Marketplace\SubcategoriaAdminController;
use App\Http\Controllers\Admin\DenunciaAdminController as AdminDenunciaAdminController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Admin\PalabrasProhibidasController;
use App\Http\Controllers\Admin\PublicacionAdminController;
use App\Http\Controllers\Admin\TutorialAdminController;
use App\Http\Controllers\CentroMensajesController;
use App\Http\Controllers\Clasificado\ClasificadoCategoriaController;
use App\Http\Controllers\Clasificado\ClasificadoController;
use App\Http\Controllers\Clasificado\ClasificadoDenunciaController;
use App\Http\Controllers\Clasificado\ConversacionController as ClasificadoConversacionController;
use App\Http\Controllers\Clasificado\MensajeController as ClasificadoMensajeController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\EmpleosController;
use App\Http\Controllers\Emprendimiento\EmprendimientoController;
use App\Http\Controllers\Emprendimiento\EmprendimientoConversacionController;
use App\Http\Controllers\Emprendimiento\EmprendimientoDenunciaController;
use App\Http\Controllers\Emprendimiento\EmprendimientoFavoritoController;
use App\Http\Controllers\Emprendimiento\EmprendimientoMensajeController;
use App\Http\Controllers\InscripcionCursoController;
use App\Http\Controllers\Marketplace\ConversacionController as MarketplaceConversacionController;
use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\Marketplace\MensajeController as MarketplaceMensajeController;
use App\Http\Controllers\Marketplace\PublicacionController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\Servicio\ServicioConversacionController;
use App\Http\Controllers\Servicio\ServicioMensajeController;
use App\Http\Controllers\Servicio\ServicioRequerimientoController;
use App\Http\Controllers\Servicio\ServiciosController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\SolicitudIngresoController;
use App\Http\Controllers\UbicacionesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserExperienciaController;
use App\Http\Controllers\UserFormacionController;
use App\Http\Controllers\UserHojaVidaController;
use App\Http\Controllers\UserInformacionRecepcionController;
use App\Models\OfertaEmpleo;
use App\Models\Servicio;
use App\Models\ServicioRequerimiento;
use App\Models\Emprendimiento;
use App\Models\MarketplacePublicacion;
use App\Models\Curso;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\Api\AiAssistanceController;
use App\Http\Controllers\DenunciaController;
use App\Http\Controllers\DestacadoController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\Evento\EventoController;
use App\Http\Controllers\Evento\EventoConversacionController;
use App\Http\Controllers\Evento\EventoMensajeController;
use App\Http\Controllers\InfractionController;
use App\Http\Controllers\TutorialController;
use App\Models\Destacado;

/*
|--------------------------------------------------------------------------
| HELPER PARA RESOLVER IMÁGENES DEL BANCO (DEFINIDO PRIMERO)
|--------------------------------------------------------------------------
*/

if (!function_exists('resolverImagenCategoria')) {
    function resolverImagenCategoria(string $modulo, ?string $nombreCategoria, ?string $slugCategoria = null): string
    {
        $carpetaBase = "images/banco/{$modulo}";

        if (!empty($nombreCategoria)) {
            $rutaNombre = "{$carpetaBase}/{$nombreCategoria}.jpg";
            if (file_exists(public_path($rutaNombre))) {
                return '/' . str_replace('\\', '/', $rutaNombre);
            }
        }

        $slug = $slugCategoria ?: (\Illuminate\Support\Str::slug($nombreCategoria ?? ''));
        if (!empty($slug)) {
            $rutaSlug = "{$carpetaBase}/{$slug}.jpg";
            if (file_exists(public_path($rutaSlug))) {
                return '/' . str_replace('\\', '/', $rutaSlug);
            }
        }

        $fallback = "{$carpetaBase}/Otros.jpg";
        if (file_exists(public_path($fallback))) {
            return '/' . $fallback;
        }

        return '/images/landing/banner1.png';
    }
}

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/solicitud-ingreso', function () {
    return Inertia::render('Auth/SolicitudIngreso');
})->name('solicitud.ingreso');

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->middleware('guest')->name('register');

Route::post('/solicitud-ingreso', [SolicitudIngresoController::class, 'store'])
    ->middleware('guest')
    ->name('solicitud.ingreso.store');

Route::get('/terminos', function () {
    return Inertia::render('Legal/Terminos');
})->name('terminos');

Route::get('/privacidad', function () {
    return Inertia::render('Legal/Privacidad');
})->name('privacidad');

/*
|--------------------------------------------------------------------------
| RUTAS AUTENTICADAS GENERALES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // ==========================================================
    // CONFIRMACIÓN DE ADVERTENCIAS / INFRACCIONES
    // ==========================================================
    Route::post('/infractions/{infraction}/acknowledge', [InfractionController::class, 'acknowledge'])
        ->name('infractions.acknowledge');

    Route::post('/users/{user}/infractions', [InfractionController::class, 'store'])
        ->name('infractions.store');
    // ==========================================================
    // DASHBOARD
    // ==========================================================
    Route::post('/donaciones', [DonationController::class, 'store'])->name('donations.store');
    Route::get('/ayuda/tutoriales', [TutorialController::class, 'index'])->name('tutorials.index');

    // ==========================================
    // RUTAS DE ADMINISTRACIÓN (Protegidas)
    // ==========================================
    Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
        // Listado y gestión de tutoriales para administradores
        Route::get('/tutoriales', [TutorialAdminController::class, 'index'])->name('tutorials.index');
        Route::get('/tutoriales/crear', [TutorialAdminController::class, 'create'])->name('tutorials.create');
        Route::post('/tutoriales', [TutorialAdminController::class, 'store'])->name('tutorials.store');
        Route::get('/tutoriales/{tutorial}/editar', [TutorialAdminController::class, 'edit'])->name('tutorials.edit');
        Route::put('/tutoriales/{tutorial}', [TutorialAdminController::class, 'update'])->name('tutorials.update');
        Route::delete('/tutoriales/{tutorial}', [TutorialAdminController::class, 'destroy'])->name('tutorials.destroy');
    });
    Route::get('/dashboard', function () {
        $resolverImg = function (string $modulo, ?string $slugCategoria): ?string {
            $carpetaModulo = match (ucfirst(strtolower($modulo))) {
                'Servicios', 'Servicio', 'Requerimiento', 'Requerimientos' => 'Servicios',
                'Marketplace' => 'Marketplace',
                'Emprendimientos', 'Emprendimiento' => 'Emprendimientos',
                default => null // Módulos como Clasificados, Empleos, Cursos, Eventos no usan banco local
            };

            if (!$carpetaModulo) {
                return null;
            }

            $carpetaBase = "images/banco/{$carpetaModulo}";

            if (!empty($slugCategoria)) {
                $rutaSlug = "{$carpetaBase}/{$slugCategoria}.jpg";
                if (file_exists(public_path($rutaSlug))) {
                    return '/' . str_replace('\\', '/', $rutaSlug);
                }
                $rutaSlugPng = "{$carpetaBase}/{$slugCategoria}.png";
                if (file_exists(public_path($rutaSlugPng))) {
                    return '/' . str_replace('\\', '/', $rutaSlugPng);
                }
            }

            foreach (['otros.jpg', 'Otros.jpg', 'OTROS.jpg'] as $nombreFallback) {
                $fallbackModulo = "{$carpetaBase}/{$nombreFallback}";
                if (file_exists(public_path($fallbackModulo))) {
                    return '/' . str_replace('\\', '/', $fallbackModulo);
                }
            }

            return null;
        };

        $destacadosRegistrados = Destacado::with('destacable')
            ->where('activo', true)
            ->latest()
            ->get();

        $destacadosReales = $destacadosRegistrados->map(function ($destacado) use ($resolverImg) {
            $pub = $destacado->destacable;
            if (!$pub) return null;

            $modulo = $destacado->modulo;
            $slugCat = $destacado->categoria_slug;

            if (empty($slugCat)) {
                $catValor = $pub->categoria ?? $pub->categoria_nombre ?? null;
                $slugCat = is_object($catValor) ? ($catValor->slug ?? Str::slug($catValor->nombre ?? '')) : Str::slug($catValor ?? '');
            }

            // 1. Intentar buscar imagen en el banco de categorías (para los módulos permitidos)
            $imagenFinal = $resolverImg($modulo, $slugCat);

            // 2. Si no hay banco o no encontró imagen de categoría, buscar la foto propia del producto
            if (!$imagenFinal) {
                $imagenPropia = null;

                try {
                    // Verificamos si tiene relación de imágenes (como en Clasificados o Marketplace)
                    if (method_exists($pub, 'imagenPrincipal') && $pub->imagenPrincipal) {
                        $imgObj = $pub->imagenPrincipal;
                        $imagenPropia = $imgObj->ruta ?? $imgObj->url ?? $imgObj->path ?? $imgObj->imagen ?? $imgObj->nombre ?? null;
                    } elseif (method_exists($pub, 'imagenes') && $pub->imagenes()->exists()) {
                        $imgObj = $pub->imagenes()->orderBy('es_principal', 'desc')->orderBy('orden')->first();
                        $imagenPropia = $imgObj->ruta ?? $imgObj->url ?? $imgObj->path ?? $imgObj->imagen ?? $imgObj->nombre ?? null;
                    }
                } catch (\Exception $e) {
                }

                // Si no se encontró por relación, revisamos campos directos en la tabla de la publicación
                if (!$imagenPropia) {
                    $imagenPropia = $pub->imagen ?? $pub->logo ?? $pub->imagen_principal ?? $pub->foto ?? $pub->path ?? null;
                }

                if ($imagenPropia) {
                    // Limpiamos la ruta para asegurar que apunte correctamente a storage o URL externa
                    $imagenFinal = str_starts_with($imagenPropia, 'http')
                        ? $imagenPropia
                        : (str_starts_with($imagenPropia, '/') ? $imagenPropia : '/storage/' . ltrim($imagenPropia, '/'));
                } else {
                    // 3. Respaldo general si el producto no tiene ninguna imagen registrada
                    $imagenFinal = '/images/landing/banner1.png';
                }
            }

            $config = match (ucfirst(strtolower($modulo))) {
                'Requerimiento' => [
                    'tipo' => 'Requerimiento',
                    'icono' => '⚡',
                    'color' => '#D97706',
                    'ruta' => "/servicios/requerimientos/{$pub->id}",
                    'titulo' => $pub->titulo,
                    'descripcion' => $pub->descripcion,
                ],
                'Servicios', 'Servicio' => [
                    'tipo' => 'Servicios',
                    'icono' => '🛠️',
                    'color' => '#3B7A70',
                    'ruta' => "/servicios/{$pub->id}",
                    'titulo' => $pub->titulo,
                    'descripcion' => $pub->descripcion,
                ],
                'Emprendimiento', 'Emprendimientos' => [
                    'tipo' => 'Emprendimiento',
                    'icono' => '🚀',
                    'color' => '#D99A24',
                    'ruta' => "/emprendimientos/{$pub->id}",
                    'titulo' => $pub->nombre ?? $pub->titulo,
                    'descripcion' => $pub->descripcion_corta ?? $pub->descripcion,
                ],
                'Marketplace' => [
                    'tipo' => 'Marketplace',
                    'icono' => '🛍️',
                    'color' => '#2563EB',
                    'ruta' => "/marketplace/publicacion/{$pub->id}",
                    'titulo' => $pub->titulo,
                    'descripcion' => $pub->descripcion,
                ],
                'Clasificado', 'Clasificados' => [
                    'tipo' => 'Clasificados',
                    'icono' => '📣',
                    'color' => '#C96A32',
                    'ruta' => "/clasificados/{$pub->id}",
                    'titulo' => $pub->titulo,
                    'descripcion' => $pub->descripcion,
                ],
                'Empleos', 'Ofertaempleo' => [
                    'tipo' => 'Empleos',
                    'icono' => '💼',
                    'color' => '#2F80ED',
                    'ruta' => "/empleos/{$pub->id}",
                    'titulo' => $pub->titulo,
                    'descripcion' => $pub->descripcion,
                ],
                'Cursos' => [
                    'tipo' => 'Cursos',
                    'icono' => '📚',
                    'color' => '#496B86',
                    'ruta' => "/cursos/{$pub->id}",
                    'titulo' => $pub->titulo,
                    'descripcion' => $pub->descripcion,
                ],
                'Eventos', 'Evento' => [
                    'tipo' => 'Eventos',
                    'icono' => '📅',
                    'color' => '#8B5CF6',
                    'ruta' => "/eventos/{$pub->id}",
                    'titulo' => $pub->titulo,
                    'descripcion' => $pub->descripcion,
                ],
                default => [
                    'tipo' => $modulo,
                    'icono' => '📌',
                    'color' => '#3B7A70',
                    'ruta' => "/dashboard",
                    'titulo' => $pub->titulo ?? 'Sin título',
                    'descripcion' => $pub->descripcion ?? '',
                ]
            };

            return [
                'id'          => strtolower($config['tipo']) . '-' . $pub->id,
                'tipo'        => $config['tipo'],
                'icono'       => $config['icono'],
                'titulo'      => $config['titulo'],
                'descripcion' => $config['descripcion'],
                'imagen'      => $imagenFinal,
                'color'       => $config['color'],
                'ruta'        => $config['ruta'],
            ];
        })
            ->filter()
            ->shuffle()
            ->values();

        return Inertia::render('Dashboard', [
            'destacados' => $destacadosReales,
        ]);
    })->middleware(['auth', 'verified'])->name('dashboard');

   

    // ==========================================================
    // BANDEJA CENTRAL UNIFICADA DE MENSAJES
    // ==========================================================
    Route::get('/mensajes', [CentroMensajesController::class, 'index'])
        ->name('mensajes.index');

    // ==========================================================
    // MI PERFIL
    // ==========================================================
    Route::get('/perfil', [PerfilController::class, 'show'])
        ->middleware('permission:perfil.ver')
        ->name('perfil.show');

    Route::put('/perfil', [PerfilController::class, 'update'])
        ->middleware('permission:perfil.editar')
        ->name('perfil.update');

    Route::post('/perfil/foto', [PerfilController::class, 'updateFoto'])
        ->middleware('permission:perfil.editar')
        ->name('perfil.foto.update');

    Route::put('/perfil/password', [PerfilController::class, 'updatePassword'])
        ->middleware('permission:perfil.cambiar_password')
        ->name('perfil.password.update');

    // ==========================================================
    // FORMACIÓN ACADÉMICA
    // ==========================================================
    Route::post('/perfil/formacion', [UserFormacionController::class, 'store'])
        ->middleware('permission:perfil.editar')
        ->name('perfil.formacion.store');

    Route::put('/perfil/formacion/{formacion}', [UserFormacionController::class, 'update'])
        ->middleware('permission:perfil.editar')
        ->name('perfil.formacion.update');

    Route::delete('/perfil/formacion/{formacion}', [UserFormacionController::class, 'destroy'])
        ->middleware('permission:perfil.editar')
        ->name('perfil.formacion.destroy');

    // ==========================================================
    // EXPERIENCIA LABORAL
    // ==========================================================
    Route::post('/perfil/experiencia', [UserExperienciaController::class, 'store'])
        ->middleware('permission:perfil.editar')
        ->name('perfil.experiencia.store');

    Route::put('/perfil/experiencia/{experiencia}', [UserExperienciaController::class, 'update'])
        ->middleware('permission:perfil.editar')
        ->name('perfil.experiencia.update');

    Route::delete('/perfil/experiencia/{experiencia}', [UserExperienciaController::class, 'destroy'])
        ->middleware('permission:perfil.editar')
        ->name('perfil.experiencia.destroy');

    // ==========================================================
    // SKILLS
    // ==========================================================
    Route::get('/perfil/skills', [SkillController::class, 'index'])->name('perfil.skills.index');
    Route::post('/skills', [SkillController::class, 'createSkill'])->name('skills.create');
    Route::post('/perfil/skills', [SkillController::class, 'store'])->name('perfil.skills.store');
    Route::delete('/perfil/skills/{skill}', [SkillController::class, 'destroy'])->name('perfil.skills.destroy');

    // ==========================================================
    // HOJA DE VIDA
    // ==========================================================
    Route::post('/perfil/hoja-vida', [UserHojaVidaController::class, 'store'])->name('perfil.hoja-vida.store');
    Route::get('/perfil/hoja-vida/{id}/ver', [UserHojaVidaController::class, 'show'])->name('perfil.hoja-vida.show');
    Route::get('/perfil/hoja-vida/{id}/descargar', [UserHojaVidaController::class, 'download'])->name('perfil.hoja-vida.download');
    Route::delete('/perfil/hoja-vida/{id}', [UserHojaVidaController::class, 'destroy'])->name('perfil.hoja-vida.destroy');
    Route::patch('/perfil/hoja-vida/{id}/principal', [UserHojaVidaController::class, 'principal'])->name('perfil.hoja-vida.principal');

    // ==========================================================
    // INFORMACIÓN DE RECEPCIÓN
    // ==========================================================
    Route::get('/perfil/informacion-recepcion', [UserInformacionRecepcionController::class, 'show'])->name('perfil.informacion-recepcion.show');
    Route::post('/perfil/informacion-recepcion', [UserInformacionRecepcionController::class, 'store'])->name('perfil.informacion-recepcion.store');
    Route::delete('/perfil/informacion-recepcion', [UserInformacionRecepcionController::class, 'destroy'])->name('perfil.informacion-recepcion.destroy');

    // ==========================================================
    // EMPLEOS
    // ==========================================================
    Route::get('/empleos', [EmpleosController::class, 'index'])->name('empleos.index');
    Route::get('/empleos/crear', [EmpleosController::class, 'create'])->name('empleos.create');
    Route::post('/empleos', [EmpleosController::class, 'publicarOferta'])->name('empleos.store');
    Route::get('/mis-ofertas', [EmpleosController::class, 'misOfertas'])->name('empleos.mis-ofertas');
    Route::get('/mis-postulaciones', [EmpleosController::class, 'misPostulaciones'])->name('empleos.mis-postulaciones');
    Route::post('/empleos/{ofertaEmpleo}/postular', [EmpleosController::class, 'postular'])->name('empleos.postular');
    Route::get('/empleos/{ofertaEmpleo}', [EmpleosController::class, 'show'])->name('empleos.show');
    Route::get('/empleos/{ofertaEmpleo}/editar', [EmpleosController::class, 'edit'])->name('empleos.edit');
    Route::put('/empleos/{ofertaEmpleo}', [EmpleosController::class, 'update'])->name('empleos.update');
    Route::get('/empleos/{ofertaEmpleo}/postulantes', [EmpleosController::class, 'postulantes'])->name('empleos.postulantes');
    Route::get('/empleos/{ofertaEmpleo}/postulantes/{postulacion}/hoja-vida', [EmpleosController::class, 'verHojaVidaPostulante'])->name('empleos.postulantes.hoja-vida');
    Route::put('/empleos/{ofertaEmpleo}/postulaciones/{postulacion}', [EmpleosController::class, 'actualizarPostulacion'])->name('empleos.postulaciones.actualizar');
    Route::post('/empleos/{ofertaEmpleo}/cerrar', [EmpleosController::class, 'cerrarOferta'])->name('empleos.cerrar');

    // ==========================================================
    // USUARIOS (ADMIN)
    // ==========================================================
    Route::get('/admin/usuarios', [UserController::class, 'index'])
        ->middleware('permission:usuarios.ver')
        ->name('usuarios.index');

    Route::get('/admin/usuarios/{user}', [UserController::class, 'show'])
        ->middleware('permission:usuarios.ver_detalle')
        ->name('usuarios.show');

    Route::get('/admin/usuarios/{user}/editar', [UserController::class, 'edit'])
        ->middleware('permission:usuarios.editar')
        ->name('usuarios.edit');

    Route::put('/admin/usuarios/{user}', [UserController::class, 'update'])
        ->middleware('permission:usuarios.editar')
        ->name('usuarios.update');

    Route::put('/admin/usuarios/{user}/password', [UserController::class, 'updatePassword'])
        ->middleware('permission:usuarios.restablecer_password')
        ->name('usuarios.password.update');

    // ==========================================================
    // CONFIGURACIÓN Y PALABRAS PROHIBIDAS (ADMIN)
    // ==========================================================
    Route::get('/admin/configuracion', [ConfiguracionController::class, 'index'])
        ->middleware('permission:configuracion.ver')
        ->name('admin.configuracion.index');

    Route::get('/admin/palabras-prohibidas', [PalabrasProhibidasController::class, 'index'])
        ->middleware('permission:configuracion.ver')
        ->name('admin.palabras.index');

    Route::post('/admin/palabras-prohibidas', [PalabrasProhibidasController::class, 'store'])
        ->middleware('permission:configuracion.ver')
        ->name('admin.palabras.store');

    Route::delete('/admin/palabras-prohibidas/{palabrasProhibida}', [PalabrasProhibidasController::class, 'destroy'])
        ->middleware('permission:configuracion.ver')
        ->name('admin.palabras.destroy');

    // ==========================================================
    // UBICACIONES
    // ==========================================================
    Route::prefix('ubicaciones')->group(function () {
        Route::get('/paises', [UbicacionesController::class, 'paises'])->name('ubicaciones.paises');
        Route::get('/paises/{pais}/divisiones', [UbicacionesController::class, 'divisiones'])->name('ubicaciones.divisiones');
        Route::get('/divisiones/{division}/hijos', [UbicacionesController::class, 'hijos'])->name('ubicaciones.hijos');
        Route::get('/divisiones/{division}/ciudades', [UbicacionesController::class, 'ciudades'])->name('ubicaciones.ciudades');
        Route::get('/divisiones/{division}', [UbicacionesController::class, 'mostrarDivision'])->name('ubicaciones.division');
    });

    // ==========================================================
    // SOLICITUDES DE INGRESO (ADMIN)
    // ==========================================================
    Route::get('/admin/solicitudes', [SolicitudIngresoController::class, 'index'])
        ->middleware('permission:solicitudes.ver')
        ->name('admin.solicitudes.index');

    Route::post('/admin/solicitudes/{solicitud}/evaluar', [SolicitudIngresoController::class, 'evaluar'])
        ->middleware('permission:solicitudes.aprobar')
        ->name('admin.solicitudes.evaluar');

    // ==========================================================
    // ROLES Y PERMISOS (ADMIN)
    // ==========================================================
    Route::get('/configuracion/roles-permisos', [RolePermissionController::class, 'index'])
        ->middleware('permission:roles.ver')
        ->name('configuracion.roles-permisos');

    Route::put('/configuracion/roles-permisos/{role}/permissions', [RolePermissionController::class, 'updatePermissions'])
        ->middleware('permission:permisos.asignar')
        ->name('configuracion.roles-permisos.update');

    // ==========================================================
    // MARKETPLACE (RUTAS GENERALES)
    // ==========================================================
    Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');
    Route::get('/marketplace/categoria/{categoria}', [MarketplaceController::class, 'categoria'])->name('marketplace.categoria');
    Route::get('/marketplace/publicacion/{publicacion}', [PublicacionController::class, 'show'])->name('marketplace.show');
    Route::get('/marketplace/publicar', [PublicacionController::class, 'create'])->name('marketplace.publicar');
    Route::get('/marketplace/mis-publicaciones', [PublicacionController::class, 'misPublicaciones'])->name('marketplace.mis-publicaciones');
    Route::get('/marketplace/publicacion/{publicacion}/editar', [PublicacionController::class, 'edit'])->name('marketplace.edit');
    Route::put('/marketplace/publicacion/{publicacion}', [PublicacionController::class, 'update'])->name('marketplace.update');
    Route::delete('/marketplace/publicacion/{publicacion}', [PublicacionController::class, 'destroy'])->name('marketplace.destroy');
    Route::post('/marketplace/publicacion/{publicacion}/pausar', [PublicacionController::class, 'pausar'])->name('marketplace.pausar');
    Route::post('/marketplace/publicacion/{publicacion}/activar', [PublicacionController::class, 'activar'])->name('marketplace.activar');
    Route::post('/marketplace/publicacion/{publicacion}/reservar', [PublicacionController::class, 'reservar'])->name('marketplace.reservar');
    Route::post('/marketplace/publicacion/{publicacion}/vendida', [PublicacionController::class, 'vendida'])->name('marketplace.vendida');
    Route::post('/marketplace/publicacion/{publicacion}/calificar', [PublicacionController::class, 'guardarCalificacion'])->name('marketplace.calificacion.store');
    Route::post('/marketplace/calificacion/{calificacion}/responder', [PublicacionController::class, 'responderCalificacion'])->name('marketplace.calificacion.responder');

    // Favoritos Marketplace
    Route::get('/marketplace/favoritos', [FavoritoController::class, 'index'])->name('marketplace.favoritos');
    Route::post('/marketplace/publicacion/{publicacion}/favorito', [FavoritoController::class, 'store'])->name('marketplace.favorito.store');
    Route::delete('/marketplace/publicacion/{publicacion}/favorito', [FavoritoController::class, 'destroy'])->name('marketplace.favorito.destroy');

    // Mensajes Marketplace
    Route::post('/marketplace/publicacion/{publicacion}/mensajes', [MarketplaceConversacionController::class, 'store'])->name('marketplace.mensajes.store');
    Route::get('/marketplace/mensajes/no-leidos', [MarketplaceConversacionController::class, 'noLeidos'])->name('marketplace.mensajes.no-leidos');
    Route::get('/marketplace/mensajes/{conversacion}', [MarketplaceConversacionController::class, 'show'])->name('marketplace.mensajes.show');
    Route::post('/marketplace/mensajes/{conversacion}', [MarketplaceMensajeController::class, 'store'])->name('marketplace.mensajes.enviar');
    Route::put('/marketplace/mensaje/{mensaje}', [MarketplaceMensajeController::class, 'update'])->name('marketplace.mensaje.update');
    Route::post('/marketplace/mensajes/{conversacion}/leidos', [MarketplaceMensajeController::class, 'marcarLeidos'])->name('marketplace.mensajes.leidos');
    Route::delete('/marketplace/mensaje/{mensaje}', [MarketplaceMensajeController::class, 'destroy'])->name('marketplace.mensaje.destroy');

    // Administración Marketplace
    Route::get('/admin/marketplace', [MarketplaceAdminController::class, 'index'])->name('admin.marketplace.index');
    Route::get('/admin/marketplace/publicaciones/{publicacion}', [MarketplaceAdminController::class, 'show'])->name('admin.marketplace.publicaciones.show');
    Route::post('/admin/marketplace/publicaciones/{publicacion}/suspender', [MarketplaceAdminController::class, 'suspender'])->name('admin.marketplace.publicaciones.suspender');
    Route::post('/admin/marketplace/publicaciones/{publicacion}/reactivar', [MarketplaceAdminController::class, 'reactivar'])->name('admin.marketplace.publicaciones.reactivar');
    Route::delete('/admin/marketplace/publicaciones/{publicacion}', [MarketplaceAdminController::class, 'destroy'])->name('admin.marketplace.publicaciones.destroy');
    Route::post('/admin/marketplace/publicaciones/{publicacion}/restaurar', [MarketplaceAdminController::class, 'restaurar'])->name('admin.marketplace.publicaciones.restaurar');
    Route::patch('/admin/marketplace/publicaciones/{publicacion}/estado', [MarketplaceAdminController::class, 'cambiarEstado'])->name('admin.marketplace.publicaciones.estado');

    Route::get('/admin/marketplace/categorias', [CategoriaAdminController::class, 'index'])->name('admin.marketplace.categorias.index');
    Route::get('/admin/marketplace/categorias/crear', [CategoriaAdminController::class, 'create'])->name('admin.marketplace.categorias.create');
    Route::post('/admin/marketplace/categorias', [CategoriaAdminController::class, 'store'])->name('admin.marketplace.categorias.store');
    Route::get('/admin/marketplace/categorias/{categoria}/editar', [CategoriaAdminController::class, 'edit'])->name('admin.marketplace.categorias.edit');
    Route::put('/admin/marketplace/categorias/{categoria}', [CategoriaAdminController::class, 'update'])->name('admin.marketplace.categorias.update');
    Route::post('/admin/marketplace/categorias/{categoria}/activar', [CategoriaAdminController::class, 'activar'])->name('admin.marketplace.categorias.activar');
    Route::post('/admin/marketplace/categorias/{categoria}/desactivar', [CategoriaAdminController::class, 'desactivar'])->name('admin.marketplace.categorias.desactivar');
    Route::delete('/admin/marketplace/categorias/{categoria}', [CategoriaAdminController::class, 'destroy'])->name('admin.marketplace.categorias.destroy');

    Route::get('/admin/marketplace/subcategorias', [SubcategoriaAdminController::class, 'index'])->name('admin.marketplace.subcategorias.index');
    Route::get('/admin/marketplace/subcategorias/crear', [SubcategoriaAdminController::class, 'create'])->name('admin.marketplace.subcategorias.create');
    Route::post('/admin/marketplace/subcategorias', [SubcategoriaAdminController::class, 'store'])->name('admin.marketplace.subcategorias.store');
    Route::get('/admin/marketplace/subcategorias/{subcategoria}/editar', [SubcategoriaAdminController::class, 'edit'])->name('admin.marketplace.subcategorias.edit');
    Route::put('/admin/marketplace/subcategorias/{subcategoria}', [SubcategoriaAdminController::class, 'update'])->name('admin.marketplace.subcategorias.update');
    Route::post('/admin/marketplace/subcategorias/{subcategoria}/activar', [SubcategoriaAdminController::class, 'activar'])->name('admin.marketplace.subcategorias.activar');
    Route::post('/admin/marketplace/subcategorias/{subcategoria}/desactivar', [SubcategoriaAdminController::class, 'desactivar'])->name('admin.marketplace.subcategorias.desactivar');
    Route::delete('/admin/marketplace/subcategorias/{subcategoria}', [SubcategoriaAdminController::class, 'destroy'])->name('admin.marketplace.subcategorias.destroy');

    // ==========================================================
    // CLASIFICADOS
    // ==========================================================
    Route::get('/clasificados', [ClasificadoController::class, 'index'])->name('clasificados.index');
    Route::get('/clasificados/crear', [ClasificadoController::class, 'create'])->name('clasificados.create');
    Route::delete('/clasificados/imagen/{imagen}', [ClasificadoController::class, 'eliminarImagen'])->name('clasificados.imagen.destroy');
    Route::post('/clasificados/imagen/{imagen}/principal', [ClasificadoController::class, 'establecerImagenPrincipal'])->name('clasificados.imagen.principal');
    Route::post('/clasificados/{clasificado}/pausar', [ClasificadoController::class, 'pausar'])->name('clasificados.pausar');
    Route::post('/clasificados/{clasificado}/activar', [ClasificadoController::class, 'activar'])->name('clasificados.activar');
    Route::post('/clasificados/{clasificado}/cerrar', [ClasificadoController::class, 'cerrar'])->name('clasificados.cerrar');

    // Clasificados - Mensajes
    Route::get('/clasificados/mensajes', [ClasificadoConversacionController::class, 'index'])->name('clasificados.mensajes');
    Route::post('/clasificados/{clasificado}/mensajes', [ClasificadoConversacionController::class, 'store'])->name('clasificados.mensajes.store');
    Route::get('/clasificados/mensajes/no-leidos', [ClasificadoConversacionController::class, 'noLeidos'])->name('clasificados.mensajes.no-leidos');
    Route::get('/clasificados/mensajes/{conversacion}', [ClasificadoConversacionController::class, 'show'])->name('clasificados.mensajes.show');
    Route::post('/clasificados/mensajes/{conversacion}', [ClasificadoMensajeController::class, 'store'])->name('clasificados.mensajes.enviar');
    Route::put('/clasificados/mensaje/{mensaje}', [ClasificadoMensajeController::class, 'update'])->name('clasificados.mensaje.update');
    Route::post('/clasificados/mensajes/{conversacion}/leidos', [ClasificadoMensajeController::class, 'marcarLeidos'])->name('clasificados.mensajes.leidos');
    Route::delete('/clasificados/mensaje/{mensaje}', [ClasificadoMensajeController::class, 'destroy'])->name('clasificados.mensaje.destroy');
    Route::post('/clasificados/{clasificado}/denunciar', [ClasificadoDenunciaController::class, 'store'])->name('clasificados.denuncias.store');
    Route::get('/clasificados/{clasificado}', [ClasificadoController::class, 'show'])->name('clasificados.show');
    Route::get('/clasificados/{clasificado}/editar', [ClasificadoController::class, 'edit'])->name('clasificados.edit');
    Route::put('/clasificados/{clasificado}', [ClasificadoController::class, 'update'])->name('clasificados.update');
    Route::delete('/clasificados/{clasificado}', [ClasificadoController::class, 'destroy'])->name('clasificados.destroy');

    // Clasificados - Administración
    Route::get('/admin/clasificados/denuncias', [ClasificadoDenunciaController::class, 'index'])
        ->middleware('permission:clasificados.denuncias.ver')
        ->name('clasificados.denuncias.index');

    Route::get('/admin/clasificados/denuncias/{denuncia}', [ClasificadoDenunciaController::class, 'show'])
        ->middleware('permission:clasificados.denuncias.ver')
        ->name('clasificados.denuncias.show');

    Route::post('/admin/clasificados/denuncias/{denuncia}/revision', [ClasificadoDenunciaController::class, 'enRevision'])
        ->middleware('permission:clasificados.denuncias.revisar')
        ->name('clasificados.denuncias.revision');

    Route::post('/admin/clasificados/denuncias/{denuncia}/resolver', [ClasificadoDenunciaController::class, 'resolver'])
        ->middleware('permission:clasificados.denuncias.resolver')
        ->name('clasificados.denuncias.resolver');

    Route::post('/admin/clasificados/denuncias/{denuncia}/rechazar', [ClasificadoDenunciaController::class, 'rechazar'])
        ->middleware('permission:clasificados.denuncias.resolver')
        ->name('clasificados.denuncias.rechazar');

    Route::get('/admin/clasificados/categorias', [ClasificadoCategoriaController::class, 'index'])
        ->middleware('permission:clasificados.categorias.ver')
        ->name('clasificados.categorias.index');

    Route::get('/admin/clasificados/categorias/crear', [ClasificadoCategoriaController::class, 'create'])
        ->middleware('permission:clasificados.categorias.crear')
        ->name('clasificados.categorias.create');

    Route::post('/admin/clasificados/categorias', [ClasificadoCategoriaController::class, 'store'])
        ->middleware('permission:clasificados.categorias.crear')
        ->name('clasificados.categorias.store');

    Route::get('/admin/clasificados/categorias/{categoria}/editar', [ClasificadoCategoriaController::class, 'edit'])
        ->middleware('permission:clasificados.categorias.editar')
        ->name('clasificados.categorias.edit');

    Route::put('/admin/clasificados/categorias/{categoria}', [ClasificadoCategoriaController::class, 'update'])
        ->middleware('permission:clasificados.categorias.editar')
        ->name('clasificados.categorias.update');

    Route::post('/admin/clasificados/categorias/{categoria}/toggle', [ClasificadoCategoriaController::class, 'toggleActivo'])
        ->middleware('permission:clasificados.categorias.activar')
        ->name('clasificados.categorias.toggle');

    Route::delete('/admin/clasificados/categorias/{categoria}', [ClasificadoCategoriaController::class, 'destroy'])
        ->middleware('permission:clasificados.categorias.eliminar')
        ->name('clasificados.categorias.destroy');

    // ==========================================================
    // REQUERIMIENTOS DE SERVICIOS
    // ==========================================================
    Route::prefix('servicios/requerimientos')->name('servicios.requerimientos.')->group(function () {
        Route::get('/', [ServicioRequerimientoController::class, 'index'])->name('index');
        Route::get('/crear', [ServicioRequerimientoController::class, 'create'])->name('create');
        Route::post('/', [ServicioRequerimientoController::class, 'store'])->name('store');
        Route::get('/{requerimiento}', [ServicioRequerimientoController::class, 'show'])->name('show');
        Route::patch('/{requerimiento}/estado', [ServicioRequerimientoController::class, 'cambiarEstado'])->name('estado');
    });

    // ==========================================================
    // SERVICIOS (CATÁLOGO Y PUBLICACIÓN)
    // ==========================================================
    Route::get('/servicios', [ServiciosController::class, 'index'])->name('servicios.index');
    Route::get('/servicios/crear', [ServiciosController::class, 'create'])->name('servicios.create');

    // Conversaciones y Mensajería de Servicios
    Route::post('/servicios/{servicio}/conversacion', [ServicioConversacionController::class, 'store'])
        ->name('servicios.conversacion.store');

    Route::get('/servicios/conversaciones/{conversacion}', [ServicioConversacionController::class, 'show'])
        ->name('servicios.conversacion.show');

    Route::post('/servicios/conversaciones/{conversacion}/mensajes', [ServicioConversacionController::class, 'enviarMensaje'])
        ->name('servicios.mensajes.store');

    Route::patch('/servicios/conversaciones/{conversacion}/mensajes/leidos', [ServicioMensajeController::class, 'marcarLeidos'])
        ->name('servicios.mensajes.leidos');

    Route::put('/servicios/conversaciones/{conversacion}/mensajes/{mensaje}', [ServicioMensajeController::class, 'update'])
        ->name('servicios.mensajes.update');

    Route::delete('/servicios/conversaciones/{conversacion}/mensajes/{mensaje}', [ServicioMensajeController::class, 'destroy'])
        ->name('servicios.mensajes.destroy');

    // Publicación, Interacción y Reseñas de Servicios
    Route::get('/servicios/{servicio}', [ServiciosController::class, 'show'])->name('servicios.show');
    Route::get('/servicios/{servicio}/editar', [ServiciosController::class, 'edit'])->name('servicios.edit');
    Route::put('/servicios/{servicio}', [ServiciosController::class, 'update'])->name('servicios.update');
    Route::patch('/servicios/{servicio}/estado', [ServiciosController::class, 'cambiarEstado'])->name('servicios.estado');
    Route::delete('/servicios/{servicio}', [ServiciosController::class, 'destroy'])->name('servicios.destroy');
    Route::post('/servicios/{servicio}/contacto', [ServiciosController::class, 'registrarContacto'])->name('servicios.contacto');
    Route::post('/servicios/{servicio}/resenas', [ServiciosController::class, 'guardarResena'])->name('servicios.resenas.store');
    Route::post('/servicios/{servicio}/resenas/{resena}/respuesta', [ServiciosController::class, 'responderResena'])->name('servicios.resenas.responder');

    // ==========================================================
    // CURSOS
    // ==========================================================
    Route::get('/cursos', [CursoController::class, 'index'])->name('cursos.index');
    Route::get('/mis-cursos', [CursoController::class, 'misCursos'])->name('cursos.misCursos');
    Route::get('/cursos/create', [CursoController::class, 'create'])->name('cursos.create');
    Route::get('/cursos/{curso}/edit', [CursoController::class, 'edit'])->name('cursos.edit');
    Route::put('/cursos/{curso}', [CursoController::class, 'update'])->name('cursos.update');
    Route::get('/cursos/{curso}/inscripcion/confirmar', [InscripcionCursoController::class, 'confirmar'])->name('cursos.inscripcion.confirmar');
    Route::post('/cursos/{curso}/inscribirse', [InscripcionCursoController::class, 'store'])->name('cursos.inscribirse');
    Route::get('/cursos/{curso}/inscripcion/resultado', [InscripcionCursoController::class, 'resultado'])->name('cursos.inscripcion.resultado');
    Route::get('/mis-inscripciones', [InscripcionCursoController::class, 'misInscripciones'])->name('cursos.misInscripciones');
    Route::delete('/cursos/{curso}/inscripciones/{inscripcion}/cancelar', [InscripcionCursoController::class, 'cancelar'])->name('cursos.inscripciones.cancelar');
    Route::get('/cursos/{curso}/inscripciones', [InscripcionCursoController::class, 'index'])->name('cursos.inscripciones.index');
    Route::get('/cursos/{curso}/inscripciones/{inscripcion}', [InscripcionCursoController::class, 'show'])->name('cursos.inscripciones.show');
    Route::patch('/cursos/{curso}/inscripciones/{inscripcion}/activar', [InscripcionCursoController::class, 'activar'])->name('cursos.inscripciones.activar');
    Route::patch('/cursos/{curso}/inscripciones/{inscripcion}/rechazar', [InscripcionCursoController::class, 'rechazar'])->name('cursos.inscripciones.rechazar');
    Route::get('/cursos/{curso}/inscripciones/descargar', [InscripcionCursoController::class, 'exportar'])->name('cursos.inscripciones.exportar');
    Route::get('/cursos/{curso}', [CursoController::class, 'show'])->name('cursos.show');
    Route::delete('/cursos/{curso}', [CursoController::class, 'destroy'])->name('cursos.destroy');

    // ==========================================================
    // EMPRENDIMIENTOS
    // ==========================================================
    Route::get('/emprendimientos', [EmprendimientoController::class, 'index'])->name('emprendimientos.index');
    Route::get('/emprendimientos/crear', [EmprendimientoController::class, 'create'])->name('emprendimientos.create');
    Route::get('/emprendimientos/conversaciones', [EmprendimientoConversacionController::class, 'index'])->name('emprendimientos.conversaciones');
    Route::post('/emprendimientos/{emprendimiento}/mensajes', [EmprendimientoConversacionController::class, 'store'])->name('emprendimientos.mensajes.store');
    Route::get('/emprendimientos/mensajes/{conversacion}', [EmprendimientoConversacionController::class, 'show'])->name('emprendimientos.mensajes.show');
    Route::delete('/emprendimientos/mensajes/{conversacion}', [EmprendimientoConversacionController::class, 'destroy'])->name('emprendimientos.mensajes.destroy');

    // Emprendimientos - Mensajes
    Route::get('/emprendimientos/mensajes/{conversacion}/lista', [EmprendimientoMensajeController::class, 'index'])->name('emprendimientos.mensajes.index');
    Route::post('/emprendimientos/mensajes/{conversacion}', [EmprendimientoMensajeController::class, 'store'])->name('emprendimientos.mensajes.enviar');
    Route::post('/emprendimientos/mensajes/{conversacion}/leidos', [EmprendimientoMensajeController::class, 'marcarLeidos'])->name('emprendimientos.mensajes.leidos');
    Route::delete('/emprendimientos/mensaje/{mensaje}', [EmprendimientoMensajeController::class, 'destroy'])->name('emprendimientos.mensaje.destroy');

    // Emprendimiento Detalle y Acciones
    Route::get('/emprendimientos/{emprendimiento}/editar', [EmprendimientoController::class, 'edit'])->name('emprendimientos.edit');
    Route::get('/emprendimientos/{emprendimiento}', [EmprendimientoController::class, 'show'])->name('emprendimientos.show');
    Route::put('/emprendimientos/{emprendimiento}', [EmprendimientoController::class, 'update'])->name('emprendimientos.update');
    Route::delete('/emprendimientos/{emprendimiento}', [EmprendimientoController::class, 'destroy'])->name('emprendimientos.destroy');
    Route::post('/emprendimientos/{emprendimiento}/favorito', [EmprendimientoFavoritoController::class, 'store'])->name('emprendimientos.favoritos.store');
    Route::delete('/emprendimientos/{emprendimiento}/favorito', [EmprendimientoFavoritoController::class, 'destroy'])->name('emprendimientos.favoritos.destroy');
    Route::post('/emprendimientos/{emprendimiento}/denuncias', [EmprendimientoDenunciaController::class, 'store'])->name('emprendimientos.denuncias.store');

    // ==========================================================
    // FAVORITOS Y ASISTENCIA IA
    // ==========================================================
    Route::get('/favoritos', [FavoritoController::class, 'index'])->name('favoritos.index');
    Route::post('/favoritos/toggle', [FavoritoController::class, 'toggle'])->name('favoritos.toggle');
    Route::post('/ai/correct-text', [AiAssistanceController::class, 'correctText']);

    // ==========================================================
    // RUTAS DE DENUNCIAS (USUARIOS REGULARES)
    // ==========================================================
    Route::get('/denuncias/crear', [DenunciaController::class, 'create'])->name('denuncias.create');
    Route::post('/denuncias', [DenunciaController::class, 'store'])->name('denuncias.store');
    Route::get('/mis-denuncias', [DenunciaController::class, 'misDenuncias'])->name('denuncias.index');
    Route::get('/denuncias/exito', function () {
        return Inertia::render('admin/Denuncias/Exito');
    })->name('denuncias.creada.exito');

    // Panel de Moderación General (Solo administradores / usuarios con permiso)
    Route::get('/admin/publicaciones', [PublicacionAdminController::class, 'index'])
        ->middleware('permission:publicaciones.editar')
        ->name('admin.publicaciones.index');

    // Panel de "Mis Publicaciones" (Para cualquier usuario autenticado, incluido el Admin)
    Route::get('/mis-publicaciones', [PublicacionAdminController::class, 'index'])
        ->name('publicaciones.mis-publicaciones');

    // ==========================================================
    // REPORTE DE ERRORES E INCIDENCIAS (GENERAL)
    // ==========================================================
    Route::get('/reportar-error', [\App\Http\Controllers\ReporteErrorController::class, 'create'])
        ->name('reportes-errores.create');
    Route::post('/reportar-error', [\App\Http\Controllers\ReporteErrorController::class, 'store'])
        ->name('reportes-errores.store');

    // Panel de gestión y seguimiento (Administrador)
    Route::get('/admin/reportes-errores', [\App\Http\Controllers\ReporteErrorController::class, 'index'])
        ->middleware('permission:configuracion.ver')
        ->name('admin.reportes-errores.index');
    Route::patch('/admin/reportes-errores/{reporte}/estado', [\App\Http\Controllers\ReporteErrorController::class, 'updateEstado'])
        ->middleware('permission:configuracion.ver')
        ->name('admin.reportes-errores.estado');

    // ==========================================================
    // BUZÓN DE OPINIONES (SUGERENCIAS, QUEJAS, FELICITACIONES)
    // ==========================================================
    Route::get('/buzon-opinion', [\App\Http\Controllers\BuzonOpinionController::class, 'create'])
        ->name('buzon.create');
    Route::post('/buzon-opinion', [\App\Http\Controllers\BuzonOpinionController::class, 'store'])
        ->name('buzon.store');

    Route::get('/admin/buzon-opiniones', [\App\Http\Controllers\BuzonOpinionController::class, 'index'])
        ->middleware('permission:configuracion.ver')
        ->name('admin.buzon.index');
    Route::patch('/admin/buzon-opiniones/{opinion}/toggle-leido', [\App\Http\Controllers\BuzonOpinionController::class, 'toggleLeido'])
        ->middleware('permission:configuracion.ver')
        ->name('admin.buzon.toggle-leido');

    // ==========================================================
    // EVENTOS
    // ==========================================================
    Route::get('/eventos', [EventoController::class, 'index'])->name('eventos.index');
    Route::get('/eventos/crear', [EventoController::class, 'create'])->name('eventos.create');
    Route::get('/eventos/{evento}', [EventoController::class, 'show'])->name('eventos.show');
    Route::get('/eventos/{evento}/editar', [EventoController::class, 'edit'])->name('eventos.edit');
    Route::put('/eventos/{evento}', [EventoController::class, 'update'])->name('eventos.update');
    Route::delete('/eventos/{evento}', [EventoController::class, 'destroy'])->name('eventos.destroy');

    // Mensajería de Eventos
    Route::get('/eventos/mensajes', [EventoConversacionController::class, 'index'])->name('eventos.mensajes.index');
    Route::post('/eventos/{evento}/mensajes', [EventoConversacionController::class, 'store'])->name('eventos.mensajes.store');
    Route::get('/eventos/mensajes/{conversacion}', [EventoConversacionController::class, 'show'])->name('eventos.mensajes.show');
    Route::post('/eventos/mensajes/{conversacion}/enviar', [EventoMensajeController::class, 'store'])->name('eventos.mensajes.enviar');
    Route::post('/eventos/mensajes/{conversacion}/leidos', [EventoMensajeController::class, 'marcarLeidos'])->name('eventos.mensajes.leidos');
    Route::delete('/eventos/mensajes/mensaje/{mensaje}', [EventoMensajeController::class, 'destroy'])->name('eventos.mensajes.destroy');


    Route::post('/admin/destacados/toggle', [DestacadoController::class, 'toggle'])
        ->name('admin.destacados.toggle');

});

/*
|--------------------------------------------------------------------------
| RUTAS CON BLOQUEO TEMPORAL (PROTECCIÓN DE PUBLICACIÓN)
|--------------------------------------------------------------------------
| Solo los usuarios no suspendidos pueden crear publicaciones nuevas.
*/
Route::middleware(['auth', 'verified', 'bloqueo.temporal'])->group(function () {
    Route::post('/servicios', [ServiciosController::class, 'store'])->name('servicios.store');
    Route::post('/marketplace/publicar', [PublicacionController::class, 'store'])->name('marketplace.publicar.store');
    Route::post('/clasificados', [ClasificadoController::class, 'store'])->name('clasificados.store');
    Route::post('/cursos', [CursoController::class, 'store'])->name('cursos.store');
    Route::post('/emprendimientos', [EmprendimientoController::class, 'store'])->name('emprendimientos.store');
    Route::post('/eventos', [EventoController::class, 'store'])->name('eventos.store');

});

/*
|--------------------------------------------------------------------------
| PANEL DE ADMINISTRACIÓN / MODERACIÓN DE DENUNCIAS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'permission:denuncias.ver'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/denuncias', [AdminDenunciaAdminController::class, 'index'])->name('denuncias.index');

    // 👇 AGREGA ESTA LÍNEA EXACTA AQUÍ MISMO:
    Route::get('/denuncias/pendientes-count', [AdminDenunciaAdminController::class, 'contarPendientes'])->name('denuncias.pendientes-count');

    Route::get('/denuncias/{denuncia}', [AdminDenunciaAdminController::class, 'show'])->name('denuncias.show');
    Route::patch('/denuncias/{denuncia}/seguimiento', [AdminDenunciaAdminController::class, 'actualizarSeguimiento'])->name('denuncias.seguimiento');
    Route::post('/denuncias/{denuncia}/bloquear-publicacion', [AdminDenunciaAdminController::class, 'bloquearPublicacion'])->name('denuncias.bloquear-publicacion');
    Route::post('/denuncias/{denuncia}/bloquear-usuario', [AdminDenunciaAdminController::class, 'bloquearUsuario'])->name('denuncias.bloquear-usuario');
    Route::post('/denuncias/{denuncia}/enviar-advertencia', [AdminDenunciaAdminController::class, 'enviarAdvertencia'])->name('denuncias.enviar-advertencia');
    Route::post('/denuncias/{denuncia}/bloqueo-temporal', [AdminDenunciaAdminController::class, 'bloqueoTemporal'])->name('denuncias.bloqueo-temporal');
    Route::post('/denuncias/{denuncia}/reactivar-publicacion', [AdminDenunciaAdminController::class, 'reactivarPublicacion'])->name('denuncias.reactivar-publicacion');
    Route::post('/denuncias/{denuncia}/quitar-bloqueo-temporal', [AdminDenunciaAdminController::class, 'quitarBloqueoTemporal'])->name('denuncias.quitar-bloqueo-temporal');
    Route::post('/denuncias/{denuncia}/reactivar-usuario', [AdminDenunciaAdminController::class, 'reactivarUsuario'])->name('denuncias.reactivar-usuario');
});

require __DIR__ . '/auth.php';
