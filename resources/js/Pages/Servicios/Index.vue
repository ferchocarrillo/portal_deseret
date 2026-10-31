<script setup>
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { route } from 'ziggy-js'

const props = defineProps({
    items: {
        type: Object,
        required: true,
    },
    tipoActivo: {
        type: String,
        default: 'ofrecidos', // 'ofrecidos' | 'requeridos'
    },
    requerimientosDestacados: {
        type: Array,
        default: () => [],
    },
    filtros: {
        type: Object,
        default: () => ({
            buscar: '',
            modalidad: '',
            tipo: 'ofrecidos',
        }),
    },
})

// Validación de bloqueo temporal para el usuario autenticado
const page = usePage()
const estaBloqueado = computed(() => {
    const usuario = page.props.auth?.user
    if (!usuario?.bloqueado_hasta) return false
    return new Date(usuario.bloqueado_hasta) > new Date()
})

const form = useForm({
    buscar: props.filtros.buscar ?? '',
    modalidad: props.filtros.modalidad ?? '',
    tipo: props.tipoActivo ?? 'ofrecidos',
})

function cambiarTipo(nuevoTipo) {
    form.tipo = nuevoTipo
    buscarServicios()
}

function buscarServicios() {
    router.get(
        route('servicios.index'),
        {
            tipo: form.tipo !== 'ofrecidos' ? form.tipo : undefined,
            buscar: form.buscar || undefined,
            modalidad: form.modalidad || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    )
}

function limpiarFiltros() {
    form.buscar = ''
    form.modalidad = ''
    buscarServicios()
}

/*
|--------------------------------------------------------------------------
| FAVORITOS UNIFICADOS (Servicios y Requerimientos)
|--------------------------------------------------------------------------
*/
function alternarFavorito(item, modulo) {
    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: item.id,
            modulo: modulo,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                item.es_favorito = !Boolean(item.es_favorito)
            },
        }
    )
}

function nombreModalidad(modalidad) {
    const modalidades = {
        presencial: 'Presencial',
        remoto: 'Remoto',
        hibrido: 'Híbrido',
    }
    return modalidades[modalidad] ?? modalidad
}

function precioFormateado(servicio) {
    if (servicio.precio_tipo === 'a_convenir') return 'A convenir'
    if (servicio.precio === null || servicio.precio === undefined) return 'Consultar'

    const precio = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: servicio.moneda || 'COP',
        maximumFractionDigits: 0,
    }).format(Number(servicio.precio))

    const unidades = {
        hora: '/h',
        dia: '/día',
        servicio: '/serv.',
        proyecto: '/proy.',
        fijo: '',
    }
    const unidad = unidades[servicio.precio_tipo] ?? ''
    return unidad ? `${precio} ${unidad}` : precio
}

function presupuestoRequerimiento(req) {
    if (req.presupuesto_tipo === 'a_convenir' || (!req.presupuesto_min && !req.presupuesto_max)) {
        return 'A convenir'
    }

    const formatoMoneda = (val) =>
        new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: req.moneda || 'COP',
            maximumFractionDigits: 0,
        }).format(Number(val))

    if (req.presupuesto_tipo === 'fijo') return formatoMoneda(req.presupuesto_min)
    if (req.presupuesto_tipo === 'rango') {
        if (req.presupuesto_min && req.presupuesto_max) {
            return `${formatoMoneda(req.presupuesto_min)} - ${formatoMoneda(req.presupuesto_max)}`
        }
        if (req.presupuesto_min) return `Desde ${formatoMoneda(req.presupuesto_min)}`
        if (req.presupuesto_max) return `Hasta ${formatoMoneda(req.presupuesto_max)}`
    }
    return 'A convenir'
}

function imagenServicio(servicio) {
    // 1. Imagen de archivo subida explícitamente por el usuario
    if (servicio.imagen_tipo === 'archivo' && servicio.imagen) {
        const imagen = servicio.imagen
        if (imagen.startsWith('http://') || imagen.startsWith('https://') || imagen.startsWith('/')) {
            return imagen
        }
        return `/storage/${imagen}`
    }

    // 2. Foto de perfil del usuario (si seleccionó usar su perfil)
    if (servicio.imagen_tipo === 'perfil' && servicio.user?.foto) {
        const foto = servicio.user.foto
        if (foto.startsWith('http://') || foto.startsWith('https://') || foto.startsWith('/')) {
            return foto
        }
        return `/storage/${foto}`
    }

    // 3. 🔥 NUEVO: Fallback mágico al Banco de Imágenes usando la Categoría
    if (servicio.categoria) {
        // Corta en los dos puntos: "Construcción y hogar: drywall..." -> "Construcción y hogar"
        let categoriaBase = servicio.categoria.split(':')[0].trim();

        // Transforma a "construccion-y-hogar"
        let nombreFormateado = categoriaBase
            .toLowerCase()
            .normalize("NFD")                   // Separa las tildes de las vocales
            .replace(/[\u0300-\u036f]/g, "")    // Elimina las tildes
            .replace(/\s+/g, '-')               // Cambia los espacios por guiones
            .replace(/[^a-z0-9\-]/g, '');       // Limpia cualquier otro carácter

        // Devuelve la ruta exacta de tu carpeta local
        return `/images/banco/Servicios/${nombreFormateado}.jpg`;
    }

    // 4. Si por alguna razón no tiene categoría, devuelve null para pintar el cuadro verde por defecto
    return null
}

function nombrePublicador(usuario) {
    if (!usuario) return 'Comunidad'
    const nombre = [usuario.nombres, usuario.apellidos].filter(Boolean).join(' ')
    return nombre || usuario.name || 'Comunidad'
}

const hayFiltros = computed(() => Boolean(form.buscar || form.modalidad))
</script>

<template>

    <Head title="Servicios | Portal Deseret" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">
        <!-- 2. CONTENEDOR CON FONDO TRANSPARENTE -->
        <div class="min-h-screen bg-transparent text-[#172A3D]">

            <!-- =========================================================
                 ENCABEZADO DE SECCIÓN CON TONO VERDE TRANSLÚCIDO
            ========================================================== -->
            <section
                class="border-b border-emerald-200/80 bg-emerald-100/25 backdrop-blur-md shadow-lg shadow-emerald-500/10">
                <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-3xl">
                            <!-- BADGE IDENTIFICADOR VERDE -->
                            <div
                                class="mb-3 inline-flex items-center gap-2 rounded-full border border-emerald-300/80 bg-white/90 px-3.5 py-1 shadow-sm">
                                <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                                <span class="text-xs font-black uppercase tracking-wider text-emerald-950">
                                    Servicios y Talentos
                                </span>
                            </div>
                            <h1 class="text-3xl font-black tracking-tight text-[#172A3D] sm:text-4xl">
                                Servicios de la comunidad
                            </h1>
                            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-700 font-medium sm:text-base">
                                Encuentra talentos locales capacitados o publica requerimientos específicos para recibir
                                apoyo directo.
                            </p>
                        </div>

                        <!-- ACCIONES DE CABECERA (Protegidas con estatus de bloqueo) -->
                        <div class="flex flex-wrap gap-3">
                            <template v-if="estaBloqueado">
                                <span
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-400 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm cursor-not-allowed"
                                    title="Tu cuenta tiene un bloqueo temporal activo y no puede crear solicitudes">
                                    ⚡ Solicitud restringida
                                </span>
                                <span
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-400 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md cursor-not-allowed"
                                    title="Tu cuenta tiene un bloqueo temporal activo y no puede crear publicaciones">
                                    Restringido por bloqueo
                                </span>
                            </template>
                            <template v-else>
                                <Link :href="route('servicios.requerimientos.create')"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300/90 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-[#172A3D] shadow-sm transition hover:bg-emerald-50">
                                    ⚡ Solicitar servicio
                                </Link>

                                <Link :href="route('servicios.create')"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md shadow-emerald-700/20 transition hover:bg-emerald-800">
                                    + Publicar servicio
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =========================================================
                 CONTENIDO PRINCIPAL
            ========================================================== -->
            <main class="mx-auto max-w-7xl px-6 py-8 lg:px-8 bg-emerald-50 rounded-xl p-3 mt-2">

                <!-- BUSCADOR -->
                <section
                    class="mb-8 rounded-2xl border border-slate-300 bg-white p-4 sm:p-5 shadow-xl shadow-emerald-500/10">
                    <form class="grid grid-cols-1 gap-3 md:grid-cols-12" @submit.prevent="buscarServicios">
                        <div class="md:col-span-7">
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21-4.35-4.35" />
                                </svg>
                                <input id="buscar" v-model="form.buscar" type="search"
                                    placeholder="Buscar por cargo, oficio o especialidad..."
                                    class="w-full h-11 rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm font-semibold text-[#172A3D] placeholder-slate-400 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100" />
                            </div>
                        </div>

                        <div class="md:col-span-3">
                            <select id="modalidad" v-model="form.modalidad"
                                class="w-full h-11 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-medium text-[#172A3D] outline-none transition focus:border-emerald-600">
                                <option value="">Todas las modalidades</option>
                                <option value="presencial">Presencial</option>
                                <option value="remoto">Remoto</option>
                                <option value="hibrido">Híbrido</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2 md:col-span-2">
                            <button type="submit"
                                class="flex-1 inline-flex items-center justify-center h-11 rounded-xl bg-[#172A3D] text-sm font-black text-white shadow-md shadow-[#172A3D]/15 hover:bg-emerald-700 transition">
                                Buscar
                            </button>
                            <button v-if="hayFiltros" type="button" title="Limpiar filtros"
                                class="h-11 w-11 inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-500 hover:bg-slate-50 transition shadow-xs"
                                @click="limpiarFiltros">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M3 6h18M8 6V4h8v2m11 0-1 14H6L5 6" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </section>

                <!-- SECCIÓN DESTACADA: REQUERIMIENTOS URGENTES -->
                <section v-if="requerimientosDestacados.length && form.tipo !== 'requeridos'"
                    class="mb-8 rounded-2xl border border-emerald-200/90 bg-white p-4 shadow-xl shadow-emerald-500/10 sm:p-5">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <span
                                class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-600 text-xs font-bold text-white shadow-xs">⚡</span>
                            <h2 class="text-sm font-bold text-[#172A3D] sm:text-base">Solicitudes urgentes abiertas</h2>
                        </div>
                        <button type="button" @click="cambiarTipo('requeridos')"
                            class="text-xs font-bold text-emerald-700 hover:text-emerald-900 underline">
                            Ver todas →
                        </button>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <article v-for="req in requerimientosDestacados" :key="req.id"
                            class="group relative flex flex-col justify-between rounded-xl border border-slate-200 bg-white p-4 shadow-md shadow-emerald-500/10 transition duration-200 hover:-translate-y-0.5 hover:border-emerald-400 hover:shadow-lg">
                            <div>
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="rounded-full px-2.5 py-0.5 font-bold"
                                        :class="req.urgencia === 'urgente' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-emerald-100 text-emerald-900 border border-emerald-200'">
                                        {{ req.urgencia === 'urgente' ? '🔥 Urgente' : `Urgencia ${req.urgencia}` }}
                                    </span>
                                    <span class="text-slate-500 font-medium capitalize">{{ req.modalidad }}</span>
                                </div>

                                <Link :href="route('servicios.requerimientos.show', req.id)"
                                    class="mt-2 block text-sm font-black leading-snug text-[#172A3D] group-hover:text-emerald-700 line-clamp-1">
                                    {{ req.titulo }}
                                </Link>

                                <p class="mt-1 line-clamp-2 text-xs leading-relaxed text-slate-600">
                                    {{ req.descripcion }}
                                </p>
                            </div>

                            <div
                                class="mt-3 flex items-center justify-between border-t border-slate-200/80 pt-2.5 text-xs">
                                <span class="font-bold text-[#172A3D]">{{ presupuestoRequerimiento(req) }}</span>
                                <div class="flex items-center gap-2">
                                    <!-- Botón Favorito Requerimiento Destacado -->
                                    <button type="button"
                                        @click.stop="alternarFavorito(req, 'servicios_requerimientos')"
                                        class="flex h-7 w-7 items-center justify-center rounded-full bg-white shadow-xs transition hover:scale-110"
                                        :class="req.es_favorito ? 'text-red-600 bg-red-50' : 'text-slate-400 hover:text-red-500'"
                                        title="Guardar en favoritos">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" class="h-4 w-4"
                                            :class="req.es_favorito ? 'fill-red-600 text-red-600' : 'fill-none'">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                        </svg>
                                    </button>
                                    <Link :href="route('servicios.requerimientos.show', req.id)"
                                        class="font-black text-emerald-700 hover:underline">
                                        Ayudar →
                                    </Link>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <!-- PESTAÑAS (TABS) -->
                <div class="mb-5 rounded-2xl border border-slate-300 bg-white p-5 shadow-xl shadow-emerald-500/10">
                    <nav class="-mb-px flex space-x-6 text-sm font-bold">
                        <button type="button" @click="cambiarTipo('ofrecidos')"
                            class="whitespace-nowrap pb-3 px-1 border-b-2 transition"
                            :class="form.tipo === 'ofrecidos' ? 'border-emerald-600 text-emerald-800' : 'border-transparent text-slate-500 hover:text-slate-800'">
                            🛠️ Servicios ofrecidos
                        </button>
                        <button type="button" @click="cambiarTipo('requeridos')"
                            class="whitespace-nowrap pb-3 px-1 border-b-2 transition"
                            :class="form.tipo === 'requeridos' ? 'border-emerald-600 text-emerald-800' : 'border-transparent text-slate-500 hover:text-slate-800'">
                            ⚡ Solicitudes de la comunidad
                        </button>
                    </nav>
                </div>

                <!-- CONTADOR DE RESULTADOS -->
                <div
                    class="mb-4 flex items-center justify-between text-xs text-slate-600 px-1 rounded-2xl border border-slate-300 bg-white p-5 shadow-xl shadow-emerald-500/10">
                    <span class="font-medium">{{ form.tipo === 'requeridos' ? 'Requerimientos abiertos' : 'Servicios disponibles'
                        }}</span>
                    <span
                        class="rounded-full bg-white px-3 py-1 font-bold text-[#172A3D] border border-slate-200 shadow-xs">
                        {{ items.total }} {{ items.total === 1 ? 'resultado' : 'resultados' }}
                    </span>
                </div>

                <!-- ============================================================
                     CATÁLOGO 1: SERVICIOS OFRECIDOS
                ============================================================ -->
                <div v-if="items.data.length && form.tipo === 'ofrecidos'"
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <article v-for="servicio in items.data" :key="servicio.id"
                        class="group relative h-80 overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-xl shadow-emerald-500/10 transition-all duration-300 hover:-translate-y-1 hover:border-emerald-400 hover:shadow-2xl hover:shadow-emerald-500/20">

                        <!-- IMAGEN DE PORTADA -->
                        <div class="absolute inset-0 h-full w-full">
                            <img v-if="imagenServicio(servicio)" :src="imagenServicio(servicio)" :alt="servicio.titulo"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                loading="lazy" />
                            <div v-else
                                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-emerald-900 to-slate-900 text-white/50">
                                <span class="text-xs font-black uppercase tracking-wider">Servicio</span>
                            </div>

                            <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-black/60 to-transparent">
                            </div>
                        </div>

                        <!-- BADGE DE CATEGORÍA Y CORAZÓN EN LA ESQUINA -->
                        <div class="relative z-10 flex items-center justify-between p-3.5">
                            <span
                                class="rounded-lg bg-white/95 px-2.5 py-1 text-[11px] font-black text-emerald-950 shadow-md backdrop-blur">
                                {{ servicio.categoria }}
                            </span>

                            <!-- Botón Favorito en la esquina superior derecha -->
                            <button type="button" @click.stop="alternarFavorito(servicio, 'servicios')"
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-white/95 shadow-md backdrop-blur transition hover:scale-110"
                                :class="servicio.es_favorito ? 'bg-red-50 ring-1 ring-red-200' : ''"
                                title="Guardar en favoritos">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="h-5 w-5 transition-colors"
                                    :class="servicio.es_favorito ? 'fill-red-600 text-red-600' : 'fill-none text-slate-400 hover:text-red-500'">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>
                            </button>
                        </div>

                        <!-- TARJETA FRONTAL BLANCA FLOTANTE CON MODALIDAD ABAJO -->
                        <div
                            class="absolute inset-x-3 bottom-3 z-10 rounded-xl border border-slate-200/80 bg-white p-3.5 shadow-lg">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <Link :href="route('servicios.show', servicio.id)"
                                    class="block text-sm font-black leading-snug text-[#172A3D] transition hover:text-emerald-700 line-clamp-1">
                                    {{ servicio.titulo }}
                                </Link>
                                <span
                                    class="shrink-0 rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700">
                                    {{ nombreModalidad(servicio.modalidad) }}
                                </span>
                            </div>

                            <p class="line-clamp-2 text-xs leading-relaxed text-slate-600">
                                {{ servicio.descripcion }}
                            </p>

                            <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-2.5">
                                <div class="min-w-0 pr-2">
                                    <p class="truncate text-[10px] font-medium text-slate-400">
                                        {{ nombrePublicador(servicio.user) }}
                                    </p>
                                    <p class="text-xs font-black text-emerald-700">
                                        {{ precioFormateado(servicio) }}
                                    </p>
                                </div>

                                <Link :href="route('servicios.show', servicio.id)"
                                    class="shrink-0 rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800">
                                    Ver →
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- ============================================================
                     CATÁLOGO 2: SOLICITUDES DE LA COMUNIDAD (REQUERIMIENTOS)
                ============================================================ -->
                <div v-else-if="items.data.length && form.tipo === 'requeridos'"
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    <article v-for="req in items.data" :key="req.id"
                        class="group relative flex h-64 flex-col justify-between overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-4 shadow-xl shadow-emerald-500/10 transition-all duration-300 hover:-translate-y-1 hover:border-emerald-400 hover:shadow-2xl hover:shadow-emerald-500/20">
                        <div>
                            <div class="flex items-center justify-between text-[10px]">
                                <span class="rounded-full px-2.5 py-0.5 font-bold"
                                    :class="req.urgencia === 'urgente' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-emerald-100 text-emerald-950 border border-emerald-200'">
                                    {{ req.urgencia === 'urgente' ? '🔥 Urgente' : `Urgencia ${req.urgencia}` }}
                                </span>
                                <!-- Botón Favorito Requerimiento en la esquina -->
                                <button type="button" @click.stop="alternarFavorito(req, 'servicios_requerimientos')"
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 transition hover:scale-110"
                                    :class="req.es_favorito ? 'text-red-600 bg-red-50' : 'text-slate-400 hover:text-red-500'"
                                    title="Guardar en favoritos">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" class="h-4 w-4"
                                        :class="req.es_favorito ? 'fill-red-600 text-red-600' : 'fill-none'">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </button>
                            </div>

                            <div class="mt-2 flex items-center justify-between gap-2">
                                <Link :href="route('servicios.requerimientos.show', req.id)"
                                    class="block text-sm font-black leading-tight text-[#172A3D] group-hover:text-emerald-700 line-clamp-1">
                                    {{ req.titulo }}
                                </Link>
                            </div>

                            <p class="mt-1.5 line-clamp-2 text-xs leading-relaxed text-slate-600">
                                {{ req.descripcion }}
                            </p>
                        </div>

                        <div class="border-t border-slate-100 pt-3">
                            <div class="flex items-center justify-between text-[10px] text-slate-500 mb-1">
                                <span class="truncate">Por: <span class="font-bold text-[#172A3D]">{{
                                    nombrePublicador(req.usuario) }}</span></span>
                                <span class="capitalize font-semibold text-slate-600">{{ nombreModalidad(req.modalidad)
                                    }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-black text-emerald-800">
                                    {{ presupuestoRequerimiento(req) }}
                                </p>
                                <Link :href="route('servicios.requerimientos.show', req.id)"
                                    class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-emerald-800 shadow-xs">
                                    Ayudar →
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- ESTADO VACÍO -->
                <div v-else
                    class="rounded-2xl border border-slate-200/90 bg-white px-6 py-14 text-center shadow-xl shadow-emerald-500/10">
                    <h3 class="text-lg font-bold text-[#172A3D]">
                        {{ form.tipo === 'requeridos' ? 'No se encontraron requerimientos' : 'No encontramos servicios'
                        }}
                    </h3>
                    <p class="mx-auto mt-2 max-w-md text-sm font-medium text-slate-600">
                        Prueba ajustando los términos de búsqueda o eliminando los filtros seleccionados.
                    </p>
                    <button v-if="hayFiltros" type="button"
                        class="mt-5 rounded-xl bg-[#172A3D] px-5 py-2.5 text-sm font-bold text-white hover:bg-emerald-700 transition shadow-md"
                        @click="limpiarFiltros">
                        Limpiar filtros
                    </button>
                </div>

                <!-- PAGINACIÓN -->
                <div v-if="items.links && items.links.length > 3" class="mt-8 flex flex-wrap justify-center gap-2">
                    <template v-for="(link, index) in items.links" :key="index">
                        <Link v-if="link.url" :href="link.url" preserve-scroll
                            class="rounded-lg border px-3 py-2 text-xs sm:text-sm font-bold transition shadow-sm"
                            :class="link.active
                                ? 'border-[#172A3D] bg-[#172A3D] text-white'
                                : 'border-slate-300 bg-white text-[#172A3D] hover:border-emerald-600 hover:bg-emerald-50'"
                            v-html="link.label" />
                        <span v-else
                            class="rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-xs sm:text-sm font-medium text-slate-400"
                            v-html="link.label" />
                    </template>
                </div>

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