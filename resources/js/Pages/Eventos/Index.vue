<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    eventos: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            meta: {},
            total: 0,
        }),
    },

    filtros: {
        type: Object,
        default: () => ({}),
    },

    ciudades: {
        type: Array,
        default: () => [],
    },
})

// Validación de bloqueo temporal para el usuario autenticado
const page = usePage()
const estaBloqueado = computed(() => {
    const usuario = page.props.auth?.user
    if (!usuario?.bloqueado_hasta) return false
    return new Date(usuario.bloqueado_hasta) > new Date()
})

/*
|--------------------------------------------------------------------------
| Estado de filtros
|--------------------------------------------------------------------------
*/

const busqueda = ref(props.filtros?.buscar ?? '')
const modalidad = ref(props.filtros?.modalidad ?? '')
const ciudadId = ref(props.filtros?.ciudad_id ?? '')
const esGratuito = ref(props.filtros?.es_gratuito ?? '')

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const formatearPrecio = precio => {
    if (precio === null || precio === undefined || precio === '') {
        return null
    }

    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
    }).format(Number(precio))
}

const obtenerImagen = evento => {
    if (!evento?.imagen) {
        return null
    }

    const ruta = evento.imagen

    if (
        ruta.startsWith('http://') ||
        ruta.startsWith('https://') ||
        ruta.startsWith('/')
    ) {
        return ruta
    }

    return `/storage/${ruta}`
}

/*
|--------------------------------------------------------------------------
| Tipo de Modalidad
|--------------------------------------------------------------------------
*/

const obtenerModalidadBadge = evento => {
    if (evento.modalidad === 'virtual') {
        return {
            texto: 'Virtual',
            clase: 'bg-emerald-100 text-emerald-900 border border-emerald-200',
            icono: '💻',
        }
    }

    if (evento.modalidad === 'hibrido') {
        return {
            texto: 'Híbrido',
            clase: 'bg-blue-100 text-blue-900 border border-blue-200',
            icono: '🔄',
        }
    }

    return {
        texto: 'Presencial',
        clase: 'bg-purple-100 text-purple-900 border border-purple-200',
        icono: '📍',
    }
}

/*
|--------------------------------------------------------------------------
| Ubicación
|--------------------------------------------------------------------------
*/

const obtenerUbicacion = evento => {
    const partes = []

    if (evento.ciudad?.nombre) {
        partes.push(evento.ciudad.nombre)
    }

    if (evento.division_administrativa?.nombre) {
        partes.push(evento.division_administrativa.nombre)
    }

    return partes.join(', ') || evento.ubicacion || 'Ubicación por definir'
}

/*
|--------------------------------------------------------------------------
| Favorito Rápido (Registrar y Deseleccionar)
|--------------------------------------------------------------------------
*/
const alternarFavorito = evento => {
    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: evento.id,
            modulo: 'eventos',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                evento.es_favorito = !evento.es_favorito
            },
        }
    )
}

/*
|--------------------------------------------------------------------------
| Filtrado
|--------------------------------------------------------------------------
*/

const hayFiltros = computed(() => {
    return Boolean(
        busqueda.value ||
        modalidad.value ||
        ciudadId.value ||
        esGratuito.value
    )
})

const aplicarFiltros = () => {
    router.get(
        route('eventos.index'),
        {
            buscar: busqueda.value || undefined,
            modalidad: modalidad.value || undefined,
            ciudad_id: ciudadId.value || undefined,
            es_gratuito: esGratuito.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

const limpiarFiltros = () => {
    busqueda.value = ''
    modalidad.value = ''
    ciudadId.value = ''
    esGratuito.value = ''

    router.get(
        route('eventos.index'),
        {},
        {
            preserveState: false,
            preserveScroll: false,
            replace: true,
        }
    )
}

const buscar = () => {
    aplicarFiltros()
}

const cambiarPagina = url => {
    if (!url) return

    router.get(
        url,
        {},
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

// Control para colapsar/expandir el card de búsqueda
const filtrosAbiertos = ref(Boolean(
    props.filtros?.buscar ||
    props.filtros?.modalidad ||
    props.filtros?.ciudad_id ||
    props.filtros?.es_gratuito
))
</script>

<template>

    <Head title="Eventos | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="false">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo">

            <!-- ENCABEZADO DE SECCIÓN -->
            <section
                class="border-b border-purple-200/80 bg-purple-100/25 backdrop-blur-md shadow-lg shadow-purple-500/10">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-purple-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-purple-950">
                                    Talleres y Encuentros
                                </span>
                            </div>

                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Eventos
                            </h1>

                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Descubre talleres, capacitaciones y eventos de la comunidad.
                            </p>
                        </div>

                        <template v-if="estaBloqueado">
                            <span
                                class="inline-flex items-center justify-center rounded-xl bg-slate-400 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md cursor-not-allowed"
                                title="Tu cuenta tiene un bloqueo temporal activo y no puede crear publicaciones">
                                Publicación restringida
                            </span>
                        </template>
                        <template v-else>
                            <Link :href="route('eventos.create')"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-purple-600 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md shadow-purple-500/25 transition hover:bg-purple-700">
                                <span class="text-base">＋</span>
                                Publicar evento
                            </Link>
                        </template>
                    </div>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-6 bg-purple-50 rounded-xl p-3 mt-2">

                <!-- ACCESOS RÁPIDOS ESTILIZADOS -->
                <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-4">
                    <!-- Presenciales -->
                    <button type="button" @click="modalidad = 'presencial'; esGratuito = ''; aplicarFiltros();"
                        class="group relative flex items-center gap-3 rounded-xl border px-3.5 py-2.5 text-left transition-all duration-200"
                        :class="modalidad === 'presencial' && !esGratuito
                            ? 'border-purple-500 bg-purple-50/90 shadow-sm ring-1 ring-purple-400/50'
                            : 'border-slate-200 bg-white hover:border-purple-300 hover:bg-slate-50/80 shadow-xs'">
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 border border-blue-200/60 text-sm shadow-xs transition group-hover:scale-105">
                            📍
                        </span>
                        <div class="min-w-0">
                            <span
                                class="block truncate text-xs font-bold text-[#172A3D] group-hover:text-purple-700 transition">
                                Presenciales
                            </span>
                            <span class="block truncate text-[11px] text-slate-500">
                                Encuentros físicos
                            </span>
                        </div>
                    </button>

                    <!-- Virtuales -->
                    <button type="button" @click="modalidad = 'virtual'; esGratuito = ''; aplicarFiltros();"
                        class="group relative flex items-center gap-3 rounded-xl border px-3.5 py-2.5 text-left transition-all duration-200"
                        :class="modalidad === 'virtual' && !esGratuito
                            ? 'border-purple-500 bg-purple-50/90 shadow-sm ring-1 ring-purple-400/50'
                            : 'border-slate-200 bg-white hover:border-purple-300 hover:bg-slate-50/80 shadow-xs'">
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 border border-emerald-200/60 text-sm shadow-xs transition group-hover:scale-105">
                            💻
                        </span>
                        <div class="min-w-0">
                            <span
                                class="block truncate text-xs font-bold text-[#172A3D] group-hover:text-purple-700 transition">
                                Virtuales
                            </span>
                            <span class="block truncate text-[11px] text-slate-500">
                                Transmisiones online
                            </span>
                        </div>
                    </button>

                    <!-- Híbridos -->
                    <button type="button" @click="modalidad = 'hibrido'; esGratuito = ''; aplicarFiltros();"
                        class="group relative flex items-center gap-3 rounded-xl border px-3.5 py-2.5 text-left transition-all duration-200"
                        :class="modalidad === 'hibrido' && !esGratuito
                            ? 'border-purple-500 bg-purple-50/90 shadow-sm ring-1 ring-purple-400/50'
                            : 'border-slate-200 bg-white hover:border-purple-300 hover:bg-slate-50/80 shadow-xs'">
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-50 border border-indigo-200/60 text-sm shadow-xs transition group-hover:scale-105">
                            🔄
                        </span>
                        <div class="min-w-0">
                            <span
                                class="block truncate text-xs font-bold text-[#172A3D] group-hover:text-purple-700 transition">
                                Híbridos
                            </span>
                            <span class="block truncate text-[11px] text-slate-500">
                                Presencial y online
                            </span>
                        </div>
                    </button>

                    <!-- Gratuitos -->
                    <button type="button" @click="esGratuito = '1'; modalidad = ''; aplicarFiltros();"
                        class="group relative flex items-center gap-3 rounded-xl border px-3.5 py-2.5 text-left transition-all duration-200"
                        :class="esGratuito === '1'
                            ? 'border-purple-500 bg-purple-50/90 shadow-sm ring-1 ring-purple-400/50'
                            : 'border-slate-200 bg-white hover:border-purple-300 hover:bg-slate-50/80 shadow-xs'">
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-green-50 border border-green-200/60 text-sm shadow-xs transition group-hover:scale-105">
                            🎁
                        </span>
                        <div class="min-w-0">
                            <span
                                class="block truncate text-xs font-bold text-[#172A3D] group-hover:text-purple-700 transition">
                                Gratuitos
                            </span>
                            <span class="block truncate text-[11px] text-slate-500">
                                Sin costo de entrada
                            </span>
                        </div>
                    </button>
                </div>

                <!-- BUSCADOR Y FILTROS -->
                <section
                    class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-purple-500/15 transition-all">
                    <div class="flex items-center justify-between gap-4">
                        <button type="button" @click="filtrosAbiertos = !filtrosAbiertos"
                            class="flex flex-1 items-center justify-between text-left focus:outline-none group">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2
                                        class="text-base font-bold text-[#172A3D] group-hover:text-purple-700 transition">
                                        Buscar eventos
                                    </h2>
                                    <span v-if="hayFiltros"
                                        class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-extrabold text-purple-800">
                                        Filtros activos
                                    </span>
                                </div>
                                <p class="text-xs sm:text-sm text-slate-500 font-medium">
                                    Filtra por título, modalidad o ubicación.
                                </p>
                            </div>

                            <div
                                class="flex items-center gap-2 text-xs font-bold text-slate-500 group-hover:text-purple-700">
                                <span class="hidden sm:inline">
                                    {{ filtrosAbiertos ? 'Ocultar filtros' : 'Mostrar filtros' }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 transform transition-transform duration-300"
                                    :class="filtrosAbiertos ? 'rotate-180 text-purple-600' : 'text-slate-400'"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        <button v-if="hayFiltros" type="button" @click="limpiarFiltros"
                            class="shrink-0 text-xs font-bold text-purple-700 hover:text-purple-900 underline">
                            Limpiar filtros
                        </button>
                    </div>

                    <Transition enter-active-class="transition duration-300 ease-out"
                        enter-from-class="transform opacity-0 -translate-y-2"
                        enter-to-class="transform opacity-100 translate-y-0"
                        leave-active-class="transition duration-200 ease-in"
                        leave-from-class="transform opacity-100 translate-y-0"
                        leave-to-class="transform opacity-0 -translate-y-2">
                        <div v-show="filtrosAbiertos" class="mt-5 border-t border-slate-100 pt-5">
                            <form @submit.prevent="buscar" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                                <div class="lg:col-span-2">
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        ¿Qué evento buscas?
                                    </label>
                                    <div class="relative">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                        </svg>
                                        <input v-model="busqueda" type="text"
                                            placeholder="Título, taller, conferencia..."
                                            class="w-full h-11 rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100" />
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Modalidad
                                    </label>
                                    <select v-model="modalidad"
                                        class="w-full h-11 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-medium text-[#172A3D] focus:border-purple-600 outline-none">
                                        <option value="">Todas</option>
                                        <option value="presencial">Presencial</option>
                                        <option value="virtual">Virtual</option>
                                        <option value="hibrido">Híbrido</option>
                                    </select>
                                </div>

                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Ubicación
                                    </label>
                                    <select v-model="ciudadId"
                                        class="w-full h-11 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-medium text-[#172A3D] focus:border-purple-600 outline-none">
                                        <option value="">Todas las ciudades</option>
                                        <option v-for="ciudad in ciudades" :key="ciudad.id" :value="ciudad.id">
                                            {{ ciudad.nombre }}
                                        </option>
                                    </select>
                                </div>

                                <div class="lg:col-span-2">
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Tipo de costo
                                    </label>
                                    <select v-model="esGratuito"
                                        class="w-full h-11 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-medium text-[#172A3D] focus:border-purple-600 outline-none">
                                        <option value="">Todos (Gratis y de pago)</option>
                                        <option value="1">Solo gratuitos</option>
                                    </select>
                                </div>

                                <div class="flex items-end lg:col-span-2">
                                    <button type="submit"
                                        class="w-full h-11 rounded-xl bg-purple-600 px-6 text-xs sm:text-sm font-black text-white hover:bg-purple-700 transition shadow-md shadow-purple-500/20">
                                        Buscar eventos
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </section>

                <!-- RESULTADOS -->
                <div
                    class="mb-5 flex bg-white rounded-2xl border border-slate-300 p-5 shadow-2xl shadow-purple-500/15 flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-4">
                    <div>
                        <p class="text-xs sm:text-sm font-bold text-purple-700 uppercase tracking-wide">Agenda
                            disponible</p>
                        <h2 class="mt-1 text-2xl font-black tracking-tight text-[#172A3D]">
                            Próximos eventos
                        </h2>
                    </div>

                    <span v-if="eventos.total !== undefined" class="text-xs sm:text-sm font-semibold text-slate-600">
                        {{ eventos.total }} resultados en total
                    </span>
                </div>

                <!-- SIN RESULTADOS -->
                <div v-if="!eventos.data?.length"
                    class="rounded-3xl border border-slate-300 bg-white px-6 py-16 text-center shadow-2xl shadow-purple-500/15">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-purple-50 text-3xl text-purple-600 border border-purple-200">
                        📅
                    </div>

                    <h3 class="text-lg font-bold text-[#172A3D]">
                        No encontramos eventos
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-xs sm:text-sm text-slate-500 font-medium">
                        Intenta cambiar los filtros o publica un nuevo evento para la comunidad.
                    </p>

                    <div class="mt-6">
                        <template v-if="estaBloqueado">
                            <span
                                class="inline-flex items-center justify-center rounded-xl bg-slate-400 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md cursor-not-allowed">
                                Publicación restringida
                            </span>
                        </template>
                        <template v-else>
                            <Link :href="route('eventos.create')"
                                class="inline-flex items-center rounded-xl bg-purple-600 px-5 py-2.5 text-xs sm:text-sm font-black text-white hover:bg-purple-700 transition shadow-md">
                                Publicar evento
                            </Link>
                        </template>
                    </div>
                </div>

                <!-- GRID DE EVENTOS -->
                <div v-else
                    class="grid grid-cols-1 gap-6 bg-white rounded-2xl border border-slate-300 p-5 shadow-2xl shadow-purple-500/15 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div v-for="evento in eventos.data" :key="evento.id"
                        class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-purple-500/15 transition duration-300 hover:-translate-y-1 hover:border-purple-400">

                        <!-- Imagen -->
                        <Link :href="route('eventos.show', evento.id)"
                            class="relative aspect-[4/3] block overflow-hidden bg-slate-100">
                            <img v-if="obtenerImagen(evento)" :src="obtenerImagen(evento)" :alt="evento.titulo"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />

                            <div v-else class="flex h-full w-full flex-col items-center justify-center text-slate-300">
                                <span class="text-4xl">📅</span>
                                <span class="mt-2 text-xs font-bold text-slate-400">
                                    Sin imagen
                                </span>
                            </div>

                            <div class="absolute left-3 top-3 pointer-events-none rounded-full px-3 py-1 text-xs font-bold shadow-sm backdrop-blur"
                                :class="obtenerModalidadBadge(evento).clase">
                                {{ obtenerModalidadBadge(evento).icono }}
                                {{ obtenerModalidadBadge(evento).texto }}
                            </div>

                            <div v-if="evento.es_destacado"
                                class="absolute right-14 top-3 pointer-events-none rounded-full bg-amber-400 px-3 py-1 text-xs font-black text-amber-950 shadow-sm">
                                ⭐ Destacado
                            </div>
                        </Link>

                        <!-- Botón de Favorito Flotante -->
                        <button type="button" @click.stop="alternarFavorito(evento)"
                            class="absolute right-3 top-3 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-white/95 shadow-md backdrop-blur transition hover:scale-110"
                            :class="evento.es_favorito ? 'bg-red-50 ring-1 ring-red-200' : ''"
                            :title="evento.es_favorito ? 'Quitar de favoritos' : 'Guardar en favoritos'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="h-5 w-5 transition-colors"
                                :class="evento.es_favorito ? 'fill-red-600 text-red-600' : 'fill-none text-slate-400 hover:text-red-500'">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                        </button>

                        <!-- Contenido -->
                        <Link :href="route('eventos.show', evento.id)" class="flex flex-1 flex-col p-4">
                            <div class="mb-2 flex items-center gap-2 text-[11px] font-bold text-slate-500">
                                <span class="text-purple-700">
                                    {{ new Date(evento.fecha_inicio).toLocaleDateString('es-CO', {
                                        day: '2-digit',
                                    month: 'short' }) }}
                                </span>
                                <span>•</span>
                                <span>
                                    {{ new Date(evento.fecha_inicio).toLocaleTimeString('es-CO', {
                                        hour: '2-digit',
                                    minute: '2-digit' })
                                    }}
                                </span>
                            </div>

                            <h3
                                class="line-clamp-2 min-h-[2.75rem] text-sm sm:text-base font-black text-[#172A3D] transition group-hover:text-purple-600">
                                {{ evento.titulo }}
                            </h3>

                            <div class="mt-3">
                                <template
                                    v-if="!evento.es_gratuito && evento.precio !== null && evento.precio !== undefined">
                                    <span class="text-base sm:text-lg font-black text-[#172A3D]">
                                        {{ formatearPrecio(evento.precio) }}
                                    </span>
                                </template>
                                <span v-else class="text-xs font-bold text-emerald-600">
                                    ¡Evento Gratuito!
                                </span>
                            </div>

                            <div v-if="obtenerUbicacion(evento)"
                                class="mt-auto border-t border-slate-100 pt-3 flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                                <span>📍</span>
                                <span class="truncate">
                                    {{ obtenerUbicacion(evento) }}
                                </span>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- =====================================================
                     PAGINACIÓN
                ========================================================== -->
                <nav v-if="eventos.links?.length > 3" class="mt-8 flex flex-wrap justify-center gap-2"
                    aria-label="Paginación">
                    <button v-for="link in eventos.links" :key="link.label" type="button" :disabled="!link.url"
                        @click="cambiarPagina(link.url)"
                        class="rounded-lg border px-3.5 py-2 text-xs sm:text-sm font-bold transition shadow-xs" :class="[
                            link.active
                                ? 'border-[#172A3D] bg-[#172A3D] text-white'
                                : 'border-slate-300 bg-white text-slate-700 hover:border-purple-500 hover:bg-purple-50',
                            !link.url ? 'cursor-not-allowed opacity-40' : '',
                        ]" v-html="link.label" />
                </nav>

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