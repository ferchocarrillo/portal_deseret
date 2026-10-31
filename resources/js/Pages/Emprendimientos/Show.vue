<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { route } from 'ziggy-js'

/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    emprendimiento: {
        type: Object,
        required: true,
    },
})

/*
|--------------------------------------------------------------------------
| PESTAÑA ACTIVA PARA LAS SECCIONES DE INFORMACIÓN
|--------------------------------------------------------------------------
*/
const tabActiva = ref('descripcion')

/*
|--------------------------------------------------------------------------
| USUARIO ACTUAL
|--------------------------------------------------------------------------
*/

const page = usePage()

const usuarioActual = computed(() => {
    return page.props.auth?.user ?? null
})

const esPropietario = computed(() => {
    if (!usuarioActual.value || !props.emprendimiento) {
        return false
    }

    const currentUserId = Number(usuarioActual.value.id)
    const ownerId = Number(
        props.emprendimiento.user_id ||
        props.emprendimiento.usuario_id ||
        props.emprendimiento.usuario?.id
    )

    return currentUserId === ownerId
})

/*
|--------------------------------------------------------------------------
| INFORMACIÓN DEL USUARIO
|--------------------------------------------------------------------------
*/

const nombreUsuario = computed(() => {
    return (
        props.emprendimiento?.usuario?.name ||
        props.emprendimiento?.usuario?.nombre ||
        props.emprendimiento?.user?.name ||
        'Usuario'
    )
})

const inicialUsuario = computed(() => {
    return nombreUsuario.value.charAt(0).toUpperCase()
})

/*
|--------------------------------------------------------------------------
| VIDEO DE YOUTUBE
|--------------------------------------------------------------------------
*/

const youtubeEmbedUrl = computed(() => {
    if (!props.emprendimiento?.youtube_video_id) {
        return null
    }

    return `https://www.youtube.com/embed/${props.emprendimiento.youtube_video_id}`
})

/*
|--------------------------------------------------------------------------
| FAVORITO (UNIFICADO Y OPTIMISTA)
|--------------------------------------------------------------------------
*/

const esFavorito = ref(Boolean(props.emprendimiento?.es_favorito))
const procesandoFavorito = ref(false)

watch(
    () => props.emprendimiento?.es_favorito,
    (nuevoValor) => {
        esFavorito.value = Boolean(nuevoValor)
    }
)

const toggleFavorito = () => {
    if (procesandoFavorito.value) {
        return
    }

    procesandoFavorito.value = true

    // Actualización optimista inmediata en la interfaz
    const nuevoEstado = !esFavorito.value
    esFavorito.value = nuevoEstado
    if (props.emprendimiento) {
        props.emprendimiento.es_favorito = nuevoEstado
    }

    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: props.emprendimiento.id,
            modulo: 'emprendimientos',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onError: (errores) => {
                console.error('Error al alternar favorito:', errores)
                // Revertir en caso de error en el servidor
                esFavorito.value = !nuevoEstado
                if (props.emprendimiento) {
                    props.emprendimiento.es_favorito = esFavorito.value
                }
            },
            onFinish: () => {
                procesandoFavorito.value = false
            },
        }
    )
}

/*
|--------------------------------------------------------------------------
| FORMATEAR FECHA
|--------------------------------------------------------------------------
*/

const fechaPublicacion = computed(() => {
    if (!props.emprendimiento?.created_at) {
        return ''
    }

    const fecha = new Date(props.emprendimiento.created_at)

    return fecha.toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
})

/*
|--------------------------------------------------------------------------
| INICIAR CONVERSACIÓN
|--------------------------------------------------------------------------
*/

const iniciandoConversacion = ref(false)

const iniciarConversacion = () => {
    if (
        iniciandoConversacion.value ||
        esPropietario.value
    ) {
        return
    }

    iniciandoConversacion.value = true

    router.post(
        route(
            'emprendimientos.mensajes.store',
            props.emprendimiento.id
        ),
        {},
        {
            preserveScroll: true,

            onFinish: () => {
                iniciandoConversacion.value = false
            },
        }
    )
}
</script>

<template>

    <Head :title="emprendimiento.nombre" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 bg-purple-50 rounded-xl p-3 mb-2">

                <!-- =========================================================
                ENCABEZADO
                ========================================================== -->
                <div
                    class="mb-6 border-b border-purple-200/80 bg-purple-100/35 backdrop-blur-md shadow-xl shadow-purple-600/20 p-4 rounded-xl">

                    <!-- LÍNEA SUPERIOR: RUTA DE NAVEGACIÓN Y BOTÓN VOLVER DISCRETO A LA IZQUIERDA -->
                    <div
                        class="mb-4 flex flex-wrap items-center justify-between gap-2 text-xs sm:text-sm text-slate-500 font-medium pb-3 border-b border-purple-200/50">

                        <!-- BOTÓN DE REGRESAR AL LISTADO DISCRETO -->
                        <Link :href="route('emprendimientos.index')" :preserve-scroll="false" :preserve-state="false"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-purple-700 transition">
                            ← Volver al listado
                        </Link>

                        <!-- Miga de pan / Breadcrumb -->
                        <div class="flex items-center gap-2">
                            <Link :href="route('emprendimientos.index')" :preserve-scroll="false"
                                :preserve-state="false" class="transition hover:text-purple-600">
                                Emprendimientos
                            </Link>
                            <span>/</span>
                            <span class="text-slate-700 font-bold truncate max-w-[200px] sm:max-w-xs">
                                {{ emprendimiento.nombre }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="mb-2 flex flex-wrap items-center gap-2">
                                <span v-if="emprendimiento.categoria"
                                    class="rounded-full bg-purple-100 border border-purple-200 px-3 py-1 text-xs font-bold text-purple-900 shadow-2xs">
                                    {{ emprendimiento.categoria.nombre }}
                                </span>

                                <span v-if="emprendimiento.subcategoria"
                                    class="rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-xs font-bold text-slate-700 shadow-2xs">
                                    {{ emprendimiento.subcategoria.nombre }}
                                </span>

                                <span v-if="emprendimiento.activo"
                                    class="rounded-full bg-emerald-100 border border-emerald-200 px-3 py-1 text-xs font-bold text-emerald-800 shadow-2xs">
                                    Activo
                                </span>
                            </div>

                            <h1 class="text-2xl font-black text-[#172A3D] sm:text-3xl tracking-tight">
                                {{ emprendimiento.nombre }}
                            </h1>

                            <p v-if="fechaPublicacion" class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                Publicado el {{ fechaPublicacion }}
                            </p>
                        </div>

                        <!-- ACCIONES -->
                        <div class="flex flex-wrap gap-2">
                            <Link v-if="esPropietario" :href="route('emprendimientos.edit', emprendimiento.id)"
                                class="inline-flex items-center rounded-xl bg-purple-600 px-4 py-2 text-xs sm:text-sm font-black text-white shadow-lg shadow-purple-600/30 transition hover:bg-purple-700">
                                Editar
                            </Link>

                            <button type="button"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs sm:text-sm font-bold text-slate-700 shadow-xs transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
                                :disabled="procesandoFavorito" @click="toggleFavorito">
                                <svg class="h-5 w-5 transition-colors"
                                    :class="esFavorito ? 'fill-red-600 text-red-600' : 'fill-none text-slate-400'"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>

                                {{ esFavorito ? 'Favorito' : 'Agregar a favoritos' }}
                            </button>
                        </div>
                    </div>
                </div>
                <br>
                <!-- =========================================================
                CONTENIDO PRINCIPAL
                ========================================================== -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 bg-purple-50 rounded-xl p-3 mt-2">

                    <!-- COLUMNA PRINCIPAL -->
                    <div class="space-y-6 lg:col-span-2">

                        <!-- VIDEO DE YOUTUBE -->
                        <div
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-purple-600/30">
                            <div v-if="youtubeEmbedUrl" class="relative aspect-video w-full bg-black">
                                <iframe :src="youtubeEmbedUrl" title="Video de presentación del emprendimiento"
                                    class="absolute inset-0 h-full w-full" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen></iframe>
                            </div>

                            <div v-else
                                class="flex min-h-[300px] items-center justify-center bg-slate-100 px-6 text-center">
                                <div>
                                    <div
                                        class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-purple-100 border border-purple-200 text-purple-700">
                                        <svg class="h-7 w-7 text-purple-600" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </div>

                                    <p class="font-bold text-[#172A3D]">
                                        Este emprendimiento no tiene video.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- SISTEMA DE PESTAÑAS (TABS) PARA LAS 5 CARDS INFORMATIVAS -->
                        <div
                            class="rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-purple-600/30 overflow-hidden">

                            <!-- Navegación de pestañas (Botonera superior scrolleable) -->
                            <div
                                class="flex overflow-x-auto border-b border-slate-200 bg-slate-50 p-2 gap-1.5 scrollbar-thin">
                                <button type="button" @click="tabActiva = 'descripcion'"
                                    :class="tabActiva === 'descripcion' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100'"
                                    class="px-4 py-2 text-xs sm:text-sm font-bold rounded-xl transition whitespace-nowrap">
                                    Sobre el proyecto
                                </button>

                                <button v-if="emprendimiento.produccion_actual" type="button"
                                    @click="tabActiva = 'produccion'"
                                    :class="tabActiva === 'produccion' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100'"
                                    class="px-4 py-2 text-xs sm:text-sm font-bold rounded-xl transition whitespace-nowrap">
                                    Producción actual
                                </button>

                                <button v-if="emprendimiento.proyeccion" type="button" @click="tabActiva = 'proyeccion'"
                                    :class="tabActiva === 'proyeccion' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100'"
                                    class="px-4 py-2 text-xs sm:text-sm font-bold rounded-xl transition whitespace-nowrap">
                                    Proyección
                                </button>

                                <button v-if="emprendimiento.necesidad" type="button" @click="tabActiva = 'necesidad'"
                                    :class="tabActiva === 'necesidad' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100'"
                                    class="px-4 py-2 text-xs sm:text-sm font-bold rounded-xl transition whitespace-nowrap">
                                    ¿Qué necesita?
                                </button>

                                <button v-if="emprendimiento.propuesta_colaboracion" type="button"
                                    @click="tabActiva = 'colaboracion'"
                                    :class="tabActiva === 'colaboracion' ? 'bg-purple-600 text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100'"
                                    class="px-4 py-2 text-xs sm:text-sm font-bold rounded-xl transition whitespace-nowrap">
                                    Colaboración
                                </button>
                            </div>

                            <!-- Contenido de las pestañas -->
                            <div class="p-6 min-h-[180px]">
                                <!-- Descripción -->
                                <div v-if="tabActiva === 'descripcion'">
                                    <h2 class="mb-3 text-base sm:text-lg font-bold text-[#172A3D]">
                                        Sobre el emprendimiento
                                    </h2>
                                    <div
                                        class="whitespace-pre-line text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                        {{ emprendimiento.descripcion }}
                                    </div>
                                </div>

                                <!-- Producción actual -->
                                <div v-if="tabActiva === 'produccion' && emprendimiento.produccion_actual">
                                    <h2 class="mb-3 text-base sm:text-lg font-bold text-[#172A3D]">
                                        Producción actual
                                    </h2>
                                    <div
                                        class="whitespace-pre-line text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                        {{ emprendimiento.produccion_actual }}
                                    </div>
                                </div>

                                <!-- Proyección -->
                                <div v-if="tabActiva === 'proyeccion' && emprendimiento.proyeccion">
                                    <h2 class="mb-3 text-base sm:text-lg font-bold text-[#172A3D]">
                                        Proyección
                                    </h2>
                                    <div
                                        class="whitespace-pre-line text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                        {{ emprendimiento.proyeccion }}
                                    </div>
                                </div>

                                <!-- Necesidad -->
                                <div v-if="tabActiva === 'necesidad' && emprendimiento.necesidad">
                                    <h2 class="mb-3 text-base sm:text-lg font-bold text-[#172A3D]">
                                        ¿Qué necesita?
                                    </h2>
                                    <div
                                        class="whitespace-pre-line text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                        {{ emprendimiento.necesidad }}
                                    </div>
                                </div>

                                <!-- Colaboración -->
                                <div v-if="tabActiva === 'colaboracion' && emprendimiento.propuesta_colaboracion">
                                    <h2 class="mb-3 text-base sm:text-lg font-bold text-[#172A3D]">
                                        Propuesta de colaboración
                                    </h2>
                                    <div
                                        class="whitespace-pre-line text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                        {{ emprendimiento.propuesta_colaboracion }}
                                    </div>

                                    <div v-if="emprendimiento.tipo_colaboracion"
                                        class="mt-5 pt-4 border-t border-slate-100">
                                        <p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-500">
                                            Tipo de colaboración
                                        </p>
                                        <span
                                            class="inline-flex rounded-full bg-purple-100 border border-purple-200 px-3 py-1 text-xs font-bold text-purple-900 shadow-2xs">
                                            {{ emprendimiento.tipo_colaboracion }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- SIDEBAR -->
                    <aside class="space-y-6">

                        <!-- PROPIETARIO / CONTACTAR -->
                        <div class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-purple-600/30">
                            <h2 class="mb-4 text-xs font-black uppercase tracking-[0.22em] text-purple-950">
                                Emprendedor
                            </h2>

                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-purple-100 border border-purple-200 text-base font-bold text-purple-800 shadow-xs">
                                    {{ inicialUsuario }}
                                </div>

                                <div class="min-w-0">
                                    <p class="truncate text-xs sm:text-sm font-black text-[#172A3D]">
                                        {{ nombreUsuario }}
                                    </p>

                                    <p class="text-xs text-slate-500 font-medium">
                                        Propietario del emprendimiento
                                    </p>
                                </div>
                            </div>

                            <!-- Botón de contacto o mensaje de propietario -->
                            <div v-if="usuarioActual">
                                <button v-if="!esPropietario" type="button"
                                    class="mt-5 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-purple-600 px-4 py-3 text-xs sm:text-sm font-black text-white shadow-lg shadow-purple-600/35 transition hover:bg-purple-700 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="iniciandoConversacion" @click="iniciarConversacion">
                                    💬 {{ iniciandoConversacion ? 'Iniciando...' : 'Contactar emprendedor' }}
                                </button>
                                <div v-else
                                    class="mt-5 rounded-xl bg-purple-50 border border-purple-200 p-3 text-center text-xs font-bold text-purple-900">
                                    Esta es tu publicación
                                </div>
                            </div>
                            <div v-else class="mt-5">
                                <Link :href="route('login')"
                                    class="w-full inline-flex items-center justify-center rounded-xl bg-purple-600 px-4 py-3 text-xs sm:text-sm font-black text-white shadow-lg shadow-purple-600/35 transition hover:bg-purple-700">
                                    Inicia sesión para contactar
                                </Link>
                            </div>
                        </div>

                        <!-- INFORMACIÓN -->
                        <div class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-purple-600/30">
                            <h2 class="mb-4 text-base sm:text-lg font-bold text-[#172A3D]">
                                Información
                            </h2>

                            <div class="space-y-4">

                                <div v-if="emprendimiento.categoria" class="flex items-start gap-3">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                                            Categoría
                                        </p>

                                        <p class="mt-0.5 text-xs sm:text-sm font-bold text-[#172A3D]">
                                            {{ emprendimiento.categoria.nombre }}
                                        </p>
                                    </div>
                                </div>

                                <div v-if="emprendimiento.subcategoria" class="flex items-start gap-3">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                                            Subcategoría
                                        </p>

                                        <p class="mt-0.5 text-xs sm:text-sm font-bold text-[#172A3D]">
                                            {{ emprendimiento.subcategoria.nombre }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">
                                            Publicado
                                        </p>

                                        <p class="mt-0.5 text-xs sm:text-sm font-bold text-[#172A3D]">
                                            {{ fechaPublicacion }}
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ================================================= -->
                        <!-- BOTÓN DE DENUNCIA / REPORTE DE INCIDENTE -->
                        <!-- ================================================= -->
                        <div v-if="!esPropietario"
                            class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-purple-600/30 sm:p-6">
                            <Link
                                :href="route('denuncias.create', { modulo: 'emprendimientos', reportable_type: 'App\\Models\\Emprendimiento', reportable_id: emprendimiento.id })"
                                class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs sm:text-sm font-bold text-red-700 transition hover:bg-red-100 hover:text-red-900 shadow-sm">
                                🚨 Reportar este emprendimiento
                            </Link>
                        </div>

                    </aside>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.fondo-carboncillo {
    background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.75)), url('/images/fondos/formularios-mobile.jpg');
}

@media (min-width: 768px) {
    .fondo-carboncillo {
        background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.75)), url('/images/fondos/formularios-desktop.jpg');
    }
}
</style>