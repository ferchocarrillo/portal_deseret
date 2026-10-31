<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    publicacion: {
        type: Object,
        required: true,
    },
    esFavorito: {
        type: Boolean,
        default: false,
    },
    calificaciones: {
        type: Array,
        default: () => [],
    },
    resumenCalificaciones: {
        type: Object,
        default: () => ({
            total: 0,
            promedio: 0,
            distribucion: { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 },
        }),
    },
    calificacionUsuario: {
        type: Object,
        default: null,
    },
    puedeCalificar: {
        type: Boolean,
        default: false,
    },
    conversacionUsuario: {
        type: Object,
        default: null,
    },
})

const page = usePage()

const usuario = computed(() => page.props.auth?.user ?? null)

const imagenSeleccionada = ref(0)
const procesando = ref(false)

const calificacionSeleccionada = ref(0)
const comentarioCalificacion = ref('')
const enviandoCalificacion = ref(false)

const respuestaCalificacion = ref({})
const enviandoRespuesta = ref({})

const imagenes = computed(() => {
    return props.publicacion.imagenes ?? []
})

const imagenPrincipal = computed(() => {
    if (!imagenes.value.length) {
        return null
    }
    return imagenes.value[imagenSeleccionada.value] ?? imagenes.value[0]
})

const video = computed(() => {
    return props.publicacion.video ?? null
})

// Determina si están ambos recursos cargados (Imágenes + Video)
const tieneAmbosMedios = computed(() => {
    return imagenes.value.length > 0 && Boolean(video.value)
})

const esPropietario = computed(() => {
    return usuario.value &&
        Number(usuario.value.id) === Number(props.publicacion.user_id)
})

/*
|--------------------------------------------------------------------------
| FUNCIONES AUXILIARES (EVITAN ERRORES DE FORMATEO EN EL TEMPLATE)
|--------------------------------------------------------------------------
*/
function obtenerNombreComprador(calificacion) {
    const c = calificacion?.comprador
    if (!c) {
        return 'Miembro de Portal Deseret'
    }
    const completo = [c.nombres, c.apellidos].filter(Boolean).join(' ')
    return completo || c.name || 'Miembro de Portal Deseret'
}

function formatearFecha(fecha) {
    if (!fecha) return ''
    return new Intl.DateTimeFormat('es-CO', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(fecha))
}

/*
|--------------------------------------------------------------------------
| FAVORITO (UNIFICADO Y OPTIMISTA)
|--------------------------------------------------------------------------
*/
const esFavoritoActual = ref(Boolean(props.esFavorito))

watch(
    () => props.esFavorito,
    (nuevoValor) => {
        esFavoritoActual.value = Boolean(nuevoValor)
    }
)

function alternarFavorito() {
    if (!usuario.value) {
        router.visit(route('login'))
        return
    }

    if (procesando.value) {
        return
    }

    procesando.value = true

    const nuevoEstado = !esFavoritoActual.value
    esFavoritoActual.value = nuevoEstado
    if (props.publicacion) {
        props.publicacion.es_favorito = nuevoEstado
    }

    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: props.publicacion.id,
            modulo: 'marketplace',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onError: (errores) => {
                console.error('Error al alternar favorito:', errores)
                esFavoritoActual.value = !nuevoEstado
                if (props.publicacion) {
                    props.publicacion.es_favorito = esFavoritoActual.value
                }
            },
            onFinish: () => {
                procesando.value = false
            },
        }
    )
}

const estadoPublicacion = computed(() => {
    return props.publicacion.estado_publicacion ?? 'publicada'
})

const puedeContactar = computed(() => {
    return (
        usuario.value &&
        !esPropietario.value &&
        ['publicada', 'reservada'].includes(estadoPublicacion.value)
    )
})

const puedeDenunciar = computed(() => {
    return (
        usuario.value &&
        !esPropietario.value &&
        estadoPublicacion.value === 'publicada'
    )
})

const precioTexto = computed(() => {
    if (props.publicacion.es_gratis) {
        return 'Gratis'
    }

    if (
        props.publicacion.precio === null ||
        props.publicacion.precio === undefined ||
        props.publicacion.precio === ''
    ) {
        return 'Precio no especificado'
    }

    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
    }).format(Number(props.publicacion.precio))
})

const estadoProductoTexto = computed(() => {
    const estados = {
        nuevo: 'Nuevo',
        como_nuevo: 'Como nuevo',
        buen_estado: 'Buen estado',
        usado: 'Usado',
        para_reparar: 'Para reparar',
    }

    return estados[props.publicacion.estado_producto]
        ?? props.publicacion.estado_producto
        ?? 'No especificado'
})

const estadoPublicacionTexto = computed(() => {
    const estados = {
        publicada: 'Disponible',
        reservada: 'Reservada',
        vendida: 'Vendida',
        pausada: 'Pausada',
        suspendida: 'Suspendida',
        eliminada: 'Eliminada',
    }

    return estados[estadoPublicacion.value]
        ?? estadoPublicacion.value
})

const estadoPublicacionClase = computed(() => {
    const clases = {
        publicada: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
        reservada: 'bg-amber-100 text-amber-900 border border-amber-200',
        vendida: 'bg-slate-200 text-slate-700 border border-slate-300',
        pausada: 'bg-orange-100 text-orange-900 border border-orange-200',
        suspendida: 'bg-red-100 text-red-800 border border-red-200',
        eliminada: 'bg-red-100 text-red-800 border border-red-200',
    }

    return clases[estadoPublicacion.value]
        ?? 'bg-slate-100 text-slate-700 border border-slate-200'
})

const ubicacionTexto = computed(() => {
    const partes = []

    if (props.publicacion.ciudad?.nombre) {
        partes.push(props.publicacion.ciudad.nombre)
    }

    if (props.publicacion.division_administrativa?.nombre) {
        partes.push(props.publicacion.division_administrativa.nombre)
    }

    if (props.publicacion.pais?.nombre) {
        partes.push(props.publicacion.pais.nombre)
    }

    return partes.length
        ? partes.join(', ')
        : 'Ubicación no especificada'
})

const vendedorNombre = computed(() => {
    const vendedor = props.publicacion.usuario ?? props.publicacion.user

    if (!vendedor) {
        return 'Vendedor'
    }

    return [
        vendedor.nombres,
        vendedor.apellidos,
    ]
        .filter(Boolean)
        .join(' ')
        || vendedor.name
        || 'Vendedor'
})

const vendedorIniciales = computed(() => {
    const nombre = vendedorNombre.value

    return nombre
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map(parte => parte.charAt(0).toUpperCase())
        .join('')
})

const promedioCalificacion = computed(() => {
    return Number(props.resumenCalificaciones?.promedio ?? 0)
})

const totalCalificaciones = computed(() => {
    return Number(props.resumenCalificaciones?.total ?? 0)
})

function seleccionarImagen(index) {
    imagenSeleccionada.value = index
}

function imagenUrl(imagen) {
    if (!imagen?.ruta) {
        return ''
    }
    return `/storage/${imagen.ruta}`
}

function videoUrl() {
    if (!video.value?.ruta) {
        return ''
    }
    return `/storage/${video.value.ruta}`
}

function volver() {
    window.history.back()
}

const contactarVendedor = () => {
    router.post(
        route('marketplace.mensajes.store', props.publicacion.id)
    )
}

function editarPublicacion() {
    router.visit(
        route('marketplace.edit', props.publicacion.id)
    )
}

function pausarPublicacion() {
    if (procesando.value) return
    procesando.value = true

    router.post(
        route('marketplace.pausar', props.publicacion.id),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                procesando.value = false
            },
        }
    )
}

function activarPublicacion() {
    if (procesando.value) return
    procesando.value = true

    router.post(
        route('marketplace.activar', props.publicacion.id),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                procesando.value = false
            },
        }
    )
}

function reservarPublicacion() {
    if (procesando.value) return
    procesando.value = true

    router.post(
        route('marketplace.reservar', props.publicacion.id),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                procesando.value = false
            },
        }
    )
}

function marcarVendida() {
    if (procesando.value) return
    procesando.value = true

    router.post(
        route('marketplace.vendida', props.publicacion.id),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                procesando.value = false
            },
        }
    )
}

function seleccionarCalificacion(valor) {
    calificacionSeleccionada.value = valor
}

function guardarCalificacion() {
    if (
        !props.puedeCalificar ||
        !props.publicacion?.id ||
        !props.conversacionUsuario?.id ||
        !calificacionSeleccionada.value ||
        enviandoCalificacion.value
    ) {
        return
    }

    enviandoCalificacion.value = true

    router.post(
        route('marketplace.calificacion.store', props.publicacion.id),
        {
            conversacion_id: props.conversacionUsuario.id,
            calificacion: calificacionSeleccionada.value,
            comentario: comentarioCalificacion.value || null,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                calificacionSeleccionada.value = 0
                comentarioCalificacion.value = ''
            },
            onFinish: () => {
                enviandoCalificacion.value = false
            },
        }
    )
}

function responderCalificacion(calificacion) {
    const respuesta = respuestaCalificacion.value[calificacion.id]

    if (
        !respuesta ||
        !respuesta.trim() ||
        enviandoRespuesta.value[calificacion.id]
    ) {
        return
    }

    enviandoRespuesta.value[calificacion.id] = true

    router.post(
        route('marketplace.calificacion.responder', calificacion.id),
        {
            respuesta_vendedor: respuesta,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                respuestaCalificacion.value[calificacion.id] = ''
            },
            onFinish: () => {
                enviandoRespuesta.value[calificacion.id] = false
            },
        }
    )
}
</script>

<template>

    <Head :title="`${publicacion.titulo} | Portal Deseret`" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ENCABEZADO TRANSLÚCIDO -->
            <section class="border-b border-blue-200/80 bg-blue-100/25 backdrop-blur-md shadow-lg shadow-blue-500/10">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between gap-4">
                        <button type="button" @click="volver"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition hover:bg-blue-50 hover:border-blue-300">
                            <span>←</span>
                            <span>Volver al Marketplace</span>
                        </button>

                        <Link :href="route('marketplace.index')" :preserve-scroll="false" :preserve-state="false"
                            class="text-xs sm:text-sm font-black text-[#172A3D] uppercase tracking-wider">
                            Portal Deseret
                        </Link>
                    </div>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 bg-blue-50 rounded-xl p-3 mt-2">

                <!-- BREADCRUMB -->
                <nav class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-slate-500 font-medium px-1">
                    <Link :href="route('marketplace.index')" :preserve-scroll="false" :preserve-state="false"
                        class="hover:text-blue-600">
                        Marketplace
                    </Link>
                    <span>/</span>
                    <Link v-if="publicacion.categoria"
                        :href="route('marketplace.index', { categoria: publicacion.categoria.id })"
                        :preserve-scroll="false" :preserve-state="false" class="hover:text-blue-600">
                        {{ publicacion.categoria.nombre }}
                    </Link>
                    <template v-if="publicacion.subcategoria">
                        <span>/</span>
                        <span class="text-slate-700 font-bold">
                            {{ publicacion.subcategoria.nombre }}
                        </span>
                    </template>
                </nav>

                <!-- GRID PRINCIPAL DINÁMICO QUE EQUILIBRA LAS COLUMNAS -->
                <div class="mt-4 grid gap-6 lg:grid-cols-12 items-start">

                    <!-- COLUMNA IZQUIERDA (7 COLUMNAS) -->
                    <div class="lg:col-span-7 space-y-6">

                        <!-- 1. SI HAY IMÁGENES -->
                        <div v-if="imagenes.length > 0"
                            class="overflow-hidden rounded-3xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15">
                            <div
                                class="relative flex aspect-[4/3] items-center justify-center overflow-hidden bg-slate-100">
                                <img v-if="imagenPrincipal" :src="imagenUrl(imagenPrincipal)"
                                    :alt="imagenPrincipal.nombre_original ?? publicacion.titulo"
                                    class="h-full w-full object-contain" />

                                <div class="absolute left-4 top-4">
                                    <span
                                        class="inline-flex rounded-full px-3 py-1.5 text-xs font-bold shadow-xs backdrop-blur"
                                        :class="estadoPublicacionClase">
                                        {{ estadoPublicacionTexto }}
                                    </span>
                                </div>

                                <div v-if="publicacion.destacado" class="absolute right-4 top-4">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-amber-400 px-3 py-1.5 text-xs font-black text-white shadow-sm">
                                        ⭐ Destacado
                                    </span>
                                </div>
                            </div>

                            <div v-if="imagenes.length > 1"
                                class="flex gap-3 overflow-x-auto border-t border-slate-200 p-4">
                                <button v-for="(imagen, index) in imagenes" :key="imagen.id ?? index" type="button"
                                    @click="seleccionarImagen(index)"
                                    class="h-20 w-20 shrink-0 overflow-hidden rounded-xl border-2 transition shadow-xs"
                                    :class="imagenSeleccionada === index
                                        ? 'border-blue-600 ring-2 ring-blue-200'
                                        : 'border-slate-200 hover:border-slate-300'">
                                    <img :src="imagenUrl(imagen)" :alt="imagen.nombre_original ?? `Imagen ${index + 1}`"
                                        class="h-full w-full object-cover" />
                                </button>
                            </div>
                        </div>

                        <!-- 2. SI NO HAY IMÁGENES PERO SÍ HAY VIDEO (EL VIDEO PASA ARRIBA) -->
                        <div v-else-if="video"
                            class="relative overflow-hidden rounded-3xl border border-slate-300 bg-black shadow-2xl shadow-blue-500/15">
                            <div class="absolute left-4 top-4 z-10">
                                <span
                                    class="inline-flex rounded-full px-3 py-1.5 text-xs font-bold shadow-xs backdrop-blur"
                                    :class="estadoPublicacionClase">
                                    {{ estadoPublicacionTexto }}
                                </span>
                            </div>
                            <video :src="videoUrl()" controls class="aspect-[4/3] w-full object-contain">
                                Tu navegador no soporta la reproducción de video.
                            </video>
                        </div>

                        <!-- 3. SI NO HAY NINGÚN ARCHIVO MULTIMEDIA -->
                        <div v-else
                            class="flex aspect-[4/3] items-center justify-center rounded-3xl border border-slate-300 bg-white shadow-xl text-slate-400">
                            <div class="text-center">
                                <div class="text-6xl">📁</div>
                                <p class="mt-3 text-xs sm:text-sm font-medium">Esta publicación no contiene archivos
                                    multimedia</p>
                            </div>
                        </div>

                        <!-- 4. VIDEO SECUNDARIO (SI HAY IMÁGENES Y TAMBIÉN VIDEO) -->
                        <div v-if="tieneAmbosMedios"
                            class="overflow-hidden rounded-3xl border border-slate-300 bg-white p-5 shadow-2xl shadow-blue-500/15">
                            <h2 class="mb-4 text-base sm:text-lg font-bold text-[#172A3D]">
                                Video del producto
                            </h2>
                            <video :src="videoUrl()" controls class="max-h-[450px] w-full rounded-2xl bg-black">
                                Tu navegador no soporta la reproducción de video.
                            </video>
                        </div>

                        <!-- 5. REACOMODO AUTOMÁTICO: SI FALTA EL VIDEO O LAS FOTOS, LA DESCRIPCIÓN SUBE A LLENAR EL HUECO IZQUIERDO -->
                        <div v-if="!tieneAmbosMedios"
                            class="rounded-3xl border border-slate-300 bg-white p-6 sm:p-8 shadow-2xl shadow-blue-500/15">
                            <h2 class="text-lg sm:text-xl font-black text-[#172A3D]">
                                Descripción
                            </h2>
                            <div
                                class="mt-4 whitespace-pre-line text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                {{ publicacion.descripcion }}
                            </div>
                        </div>

                    </div>

                    <!-- COLUMNA DERECHA (5 COLUMNAS) -->
                    <div class="lg:col-span-5 space-y-6">

                        <!-- TARJETA DE DETALLE Y ADMINISTRACIÓN -->
                        <div class="rounded-3xl border border-slate-300 bg-white p-6 shadow-2xl shadow-blue-500/15">
                            <div class="mb-3 flex flex-wrap items-center gap-2 text-xs font-bold text-blue-700">
                                <span v-if="publicacion.categoria">{{ publicacion.categoria.nombre }}</span>
                                <span v-if="publicacion.subcategoria">{{ publicacion.subcategoria.nombre }}</span>
                            </div>

                            <h1 class="text-2xl sm:text-3xl font-black leading-tight text-[#172A3D]">
                                {{ publicacion.titulo }}
                            </h1>

                            <div class="mt-4">
                                <p class="text-2xl sm:text-3xl font-black"
                                    :class="publicacion.es_gratis ? 'text-emerald-600' : 'text-[#172A3D]'">
                                    {{ precioTexto }}
                                </p>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-3">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Estado</p>
                                    <p class="mt-1 text-xs sm:text-sm font-bold text-[#172A3D]">
                                        {{ estadoProductoTexto }}
                                    </p>
                                </div>

                                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Ubicación</p>
                                    <p class="mt-1 text-xs sm:text-sm font-bold text-[#172A3D] truncate">
                                        {{ ubicacionTexto }}
                                    </p>
                                </div>

                                <div v-if="publicacion.tipo_entrega"
                                    class="col-span-2 rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Entrega</p>
                                    <p class="mt-1 text-xs sm:text-sm font-bold text-[#172A3D]">
                                        {{ publicacion.tipo_entrega }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col gap-3">
                                <button v-if="usuario" type="button" @click="alternarFavorito" :disabled="procesando"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl border px-4 py-3 text-xs sm:text-sm font-bold transition shadow-xs"
                                    :class="esFavoritoActual
                                        ? 'border-red-300 bg-red-50 text-red-900 hover:bg-red-100'
                                        : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" class="h-4 w-4 transition-colors"
                                        :class="esFavoritoActual ? 'fill-red-600 text-red-600' : 'fill-none text-slate-400'">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                    <span>{{ esFavoritoActual ? 'Guardado en favoritos' : 'Agregar a favoritos'
                                        }}</span>
                                </button>

                                <button v-if="puedeContactar" type="button" @click="contactarVendedor"
                                    class="w-full rounded-xl bg-blue-600 px-4 py-3 text-xs sm:text-sm font-black text-white transition hover:bg-blue-700 shadow-md shadow-blue-500/20">
                                    💬 Contactar vendedor
                                </button>

                                <Link v-else-if="!usuario && estadoPublicacion === 'publicada'" href="/login"
                                    class="w-full rounded-xl bg-blue-600 px-4 py-3 text-center text-xs sm:text-sm font-black text-white transition hover:bg-blue-700 shadow-md">
                                    Inicia sesión para contactar
                                </Link>
                            </div>

                            <div v-if="esPropietario" class="mt-8 border-t border-slate-200 pt-6">
                                <p class="mb-3 text-xs sm:text-sm font-black text-[#172A3D] uppercase tracking-wider">
                                    Administrar publicación
                                </p>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    <button type="button" @click="editarPublicacion"
                                        class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 shadow-xs">
                                        ✏️ Editar
                                    </button>

                                    <button v-if="estadoPublicacion === 'publicada'" type="button"
                                        @click="pausarPublicacion" :disabled="procesando"
                                        class="rounded-xl border border-orange-200 bg-white px-3 py-2.5 text-xs sm:text-sm font-bold text-orange-800 transition hover:bg-orange-50 shadow-xs">
                                        ⏸️ Pausar
                                    </button>

                                    <button v-if="estadoPublicacion === 'pausada'" type="button"
                                        @click="activarPublicacion" :disabled="procesando"
                                        class="rounded-xl border border-emerald-200 bg-white px-3 py-2.5 text-xs sm:text-sm font-bold text-emerald-800 transition hover:bg-emerald-50 shadow-xs">
                                        ▶️ Activar
                                    </button>

                                    <button v-if="estadoPublicacion === 'publicada'" type="button"
                                        @click="reservarPublicacion" :disabled="procesando"
                                        class="rounded-xl border border-amber-200 bg-white px-3 py-2.5 text-xs sm:text-sm font-bold text-amber-900 transition hover:bg-amber-50 shadow-xs">
                                        📌 Reservar
                                    </button>

                                    <button v-if="['publicada', 'reservada'].includes(estadoPublicacion)" type="button"
                                        @click="marcarVendida" :disabled="procesando"
                                        class="rounded-xl border border-blue-200 bg-white px-3 py-2.5 text-xs sm:text-sm font-bold text-blue-800 transition hover:bg-blue-50 shadow-xs sm:col-span-2">
                                        ✓ Marcar vendida
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- VENDEDOR Y REPUTACIÓN EN UNA SOLA FILA O COMPACTOS PARA NO DESBORDAR LA DERECHA -->
                        <div
                            class="rounded-3xl border border-slate-300 bg-white p-6 shadow-2xl shadow-blue-500/15 space-y-5">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Publicado por
                                </p>
                                <div class="mt-3 flex items-center gap-4">
                                    <div
                                        class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-blue-600 text-lg font-black text-white shadow-sm">
                                        <img v-if="(publicacion.usuario ?? publicacion.user)?.foto"
                                            :src="`/storage/${(publicacion.usuario ?? publicacion.user).foto}`"
                                            :alt="vendedorNombre" class="h-full w-full object-cover" />
                                        <span v-else>{{ vendedorIniciales }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate font-bold text-[#172A3D]">
                                            {{ vendedorNombre }}
                                        </p>
                                        <p class="text-xs text-slate-500 font-medium">
                                            Miembro de Portal Deseret
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-200 pt-5">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Reputación del vendedor
                                </p>
                                <div class="mt-2 flex items-center gap-3">
                                    <span class="text-3xl font-black text-[#172A3D]">
                                        {{ promedioCalificacion.toFixed(1) }}
                                    </span>
                                    <div>
                                        <div class="text-lg tracking-wide text-amber-400">
                                            <span v-for="estrella in 5" :key="estrella">
                                                {{ estrella <= Math.round(promedioCalificacion) ? '★' : '☆' }} </span>
                                        </div>
                                        <p class="text-xs text-slate-500 font-medium">
                                            {{ totalCalificaciones }} {{ totalCalificaciones === 1 ? 'calificación' :
                                            'calificaciones'
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- REPORTE -->
                        <div v-if="puedeDenunciar"
                            class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-blue-500/15">
                            <Link
                                :href="route('denuncias.create', { modulo: 'marketplace', reportable_type: 'App\\Models\\MarketplacePublicacion', reportable_id: publicacion.id })"
                                class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs sm:text-sm font-bold text-red-700 transition hover:bg-red-100 hover:text-red-900 shadow-sm">
                                🚨 Reportar esta publicación
                            </Link>
                        </div>

                    </div>

                </div>

                <!-- DESCRIPCIÓN EN ANCHO COMPLETO (SOLO CUANDO HAY IMÁGENES Y VIDEO A LA VEZ) -->
                <section v-if="tieneAmbosMedios"
                    class="rounded-3xl border border-slate-300 bg-white p-6 sm:p-8 shadow-2xl shadow-blue-500/15 mt-6">
                    <h2 class="text-lg sm:text-xl font-black text-[#172A3D]">
                        Descripción
                    </h2>
                    <div class="mt-4 whitespace-pre-line text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                        {{ publicacion.descripcion }}
                    </div>
                </section>

                <!-- CALIFICAR VENDEDOR -->
                <section v-if="puedeCalificar"
                    class="rounded-3xl border border-amber-200 bg-amber-50/80 p-6 sm:p-8 shadow-xl backdrop-blur-md mt-6">
                    <h2 class="text-lg sm:text-xl font-black text-amber-950">
                        Califica al vendedor
                    </h2>
                    <p class="mt-1 text-xs sm:text-sm text-amber-900 font-medium">
                        Tu experiencia ayudará a otros miembros de Portal Deseret.
                    </p>

                    <div class="mt-5">
                        <p class="mb-2 text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Tu calificación
                        </p>
                        <div class="flex gap-1">
                            <button v-for="estrella in 5" :key="estrella" type="button"
                                @click="seleccionarCalificacion(estrella)" class="text-3xl transition hover:scale-110"
                                :class="estrella <= calificacionSeleccionada ? 'text-amber-400' : 'text-slate-300'">
                                ★
                            </button>
                        </div>
                    </div>

                    <div class="mt-5">
                        <label class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Comentario
                        </label>
                        <textarea v-model="comentarioCalificacion" rows="4" maxlength="2000"
                            placeholder="Cuéntale a la comunidad cómo fue tu experiencia..."
                            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm text-[#172A3D] outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"></textarea>
                    </div>

                    <button type="button" @click="guardarCalificacion"
                        :disabled="!calificacionSeleccionada || !conversacionUsuario || enviandoCalificacion"
                        class="mt-4 rounded-xl bg-blue-600 px-6 py-3 text-xs sm:text-sm font-black text-white transition hover:bg-blue-700 shadow-md shadow-blue-500/20 disabled:cursor-not-allowed disabled:opacity-50">
                        {{ enviandoCalificacion ? 'Guardando...' : 'Publicar calificación' }}
                    </button>
                </section>

                <!-- YA CALIFICÓ -->
                <section v-else-if="calificacionUsuario"
                    class="rounded-3xl border border-emerald-200 bg-emerald-50/80 p-6 sm:p-8 shadow-xl backdrop-blur-md mt-6">
                    <h2 class="text-base sm:text-lg font-bold text-emerald-950">
                        Tu calificación
                    </h2>
                    <div class="mt-2 text-xl tracking-wide text-amber-400">
                        <span v-for="estrella in 5" :key="estrella">
                            {{ estrella <= Number(calificacionUsuario.calificacion) ? '★' : '☆' }} </span>
                    </div>
                    <p v-if="calificacionUsuario.comentario"
                        class="mt-3 whitespace-pre-line text-xs sm:text-sm leading-relaxed text-emerald-900 font-medium">
                        "{{ calificacionUsuario.comentario }}"
                    </p>
                </section>

                <!-- RESEÑAS -->
                <section
                    class="rounded-3xl border border-slate-300 bg-white p-6 sm:p-8 shadow-2xl shadow-blue-500/15 mt-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg sm:text-xl font-black text-[#172A3D]">
                                Opiniones sobre el vendedor
                            </h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                Experiencias compartidas por otros miembros.
                            </p>
                        </div>
                        <div v-if="totalCalificaciones" class="text-right">
                            <p class="text-2xl sm:text-3xl font-black text-[#172A3D]">
                                {{ promedioCalificacion.toFixed(1) }}
                            </p>
                            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wider">promedio</p>
                        </div>
                    </div>

                    <div v-if="calificaciones.length" class="mt-6 divide-y divide-slate-200">
                        <article v-for="calificacion in calificaciones" :key="calificacion.id"
                            class="py-6 first:pt-0 last:pb-0">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-base tracking-wide text-amber-400">
                                        <span v-for="estrella in 5" :key="estrella">
                                            {{ estrella <= Number(calificacion.calificacion) ? '★' : '☆' }} </span>
                                    </div>
                                    <p class="mt-1 text-xs sm:text-sm font-bold text-[#172A3D]">
                                        {{ obtenerNombreComprador(calificacion) }}
                                    </p>
                                </div>
                                <span v-if="calificacion.created_at" class="text-xs text-slate-400 font-medium">
                                    {{ formatearFecha(calificacion.created_at) }}
                                </span>
                            </div>

                            <p v-if="calificacion.comentario"
                                class="mt-3 whitespace-pre-line text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                {{ calificacion.comentario }}
                            </p>

                            <div v-if="calificacion.respuesta_vendedor"
                                class="mt-4 rounded-2xl bg-slate-50 border border-slate-200 p-4">
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-500">
                                    Respuesta del vendedor
                                </p>
                                <p
                                    class="mt-1.5 whitespace-pre-line text-xs sm:text-sm leading-relaxed text-slate-700 font-medium">
                                    {{ calificacion.respuesta_vendedor }}
                                </p>
                            </div>

                            <div v-else-if="esPropietario && Number(calificacion.vendedor_id) === Number(usuario?.id)"
                                class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs sm:text-sm font-bold text-[#172A3D]">
                                    Responder a esta calificación
                                </p>
                                <textarea v-model="respuestaCalificacion[calificacion.id]" rows="3" maxlength="2000"
                                    placeholder="Escribe una respuesta..."
                                    class="mt-3 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs sm:text-sm text-[#172A3D] outline-none focus:border-blue-600"></textarea>
                                <button type="button" @click="responderCalificacion(calificacion)"
                                    :disabled="enviandoRespuesta[calificacion.id]"
                                    class="mt-3 rounded-xl bg-blue-600 px-4 py-2.5 text-xs sm:text-sm font-black text-white transition hover:bg-blue-700 shadow-xs disabled:opacity-50">
                                    {{ enviandoRespuesta[calificacion.id] ? 'Enviando...' : 'Responder' }}
                                </button>
                            </div>
                        </article>
                    </div>

                    <div v-else
                        class="mt-6 rounded-2xl bg-slate-50 border border-slate-200 p-8 text-center text-xs sm:text-sm text-slate-500 font-medium">
                        ⭐ Este vendedor todavía no tiene calificaciones.
                    </div>
                </section>

                <!-- SEGURIDAD -->
                <section
                    class="rounded-3xl border border-blue-200 bg-blue-50/80 p-6 sm:p-8 shadow-xl backdrop-blur-md mt-6">
                    <div class="flex gap-4">
                        <div class="text-2xl">ℹ️</div>
                        <div>
                            <h2 class="font-bold text-blue-950">
                                Compra de forma segura
                            </h2>
                            <p
                                class="mt-1 bg-white text-xs sm:text-sm leading-relaxed text-blue-900 font-medium p-3 rounded-xl">
                                Portal Deseret facilita el contacto entre los miembros de la comunidad. Los pagos, la
                                entrega y las
                                condiciones de la transacción se acuerdan directamente entre comprador y vendedor.
                            </p>
                        </div>
                    </div>
                </section>

            </main>
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