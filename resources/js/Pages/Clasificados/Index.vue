<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    clasificados: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            meta: {},
        }),
    },

    categorias: {
        type: Array,
        default: () => [],
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

const busqueda = ref(props.filtros?.busqueda ?? '')
const intencion = ref(props.filtros?.intencion ?? '')
const operacion = ref(props.filtros?.operacion ?? '')
const categoriaId = ref(props.filtros?.categoria_id ?? '')
const ciudadId = ref(props.filtros?.ciudad_id ?? '')
const precioMin = ref(props.filtros?.precio_min ?? '')
const precioMax = ref(props.filtros?.precio_max ?? '')

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

const obtenerImagen = clasificado => {
    if (!clasificado?.imagenes?.length) {
        return null
    }

    const principal = clasificado.imagenes.find(
        imagen => Boolean(imagen.es_principal)
    )

    const imagen = principal ?? clasificado.imagenes[0]

    if (!imagen?.ruta) {
        return null
    }

    if (
        imagen.ruta.startsWith('http://') ||
        imagen.ruta.startsWith('https://') ||
        imagen.ruta.startsWith('/')
    ) {
        return imagen.ruta
    }

    return `/storage/${imagen.ruta}`
}

/*
|--------------------------------------------------------------------------
| Tipo de publicación
|--------------------------------------------------------------------------
*/

const obtenerTipo = clasificado => {
    if (
        clasificado.intencion === 'buscar' &&
        clasificado.operacion === 'venta'
    ) {
        return {
            texto: 'Busco comprar',
            clase: 'bg-orange-100 text-orange-900 border border-orange-200',
            icono: '🔎',
        }
    }

    if (
        clasificado.intencion === 'buscar' &&
        clasificado.operacion === 'arriendo'
    ) {
        return {
            texto: 'Busco arrendar',
            clase: 'bg-purple-100 text-purple-900 border border-purple-200',
            icono: '🔎',
        }
    }

    if (
        clasificado.intencion === 'ofrecer' &&
        clasificado.operacion === 'arriendo'
    ) {
        return {
            texto: 'Arriendo',
            clase: 'bg-blue-100 text-blue-900 border border-blue-200',
            icono: '🏠',
        }
    }

    return {
        texto: 'Venta',
        clase: 'bg-emerald-100 text-emerald-900 border border-emerald-200',
        icono: '🏷️',
    }
}

/*
|--------------------------------------------------------------------------
| Tipo de clasificado
|--------------------------------------------------------------------------
*/

const obtenerTipoClasificado = clasificado => {
    if (clasificado.vehiculo) {
        return 'Vehículo'
    }

    if (clasificado.propiedad) {
        return 'Propiedad'
    }

    return 'Clasificado'
}

/*
|--------------------------------------------------------------------------
| Ubicación
|--------------------------------------------------------------------------
*/

const obtenerUbicacion = clasificado => {
    const partes = []

    if (clasificado.ciudad?.nombre) {
        partes.push(clasificado.ciudad.nombre)
    }

    if (clasificado.division_administrativa?.nombre) {
        partes.push(clasificado.division_administrativa.nombre)
    }

    return partes.join(', ')
}

/*
|--------------------------------------------------------------------------
| Información secundaria
|--------------------------------------------------------------------------
*/

const obtenerResumen = clasificado => {
    if (clasificado.vehiculo) {
        const datos = []

        if (clasificado.vehiculo.marca) {
            datos.push(clasificado.vehiculo.marca)
        }

        if (clasificado.vehiculo.modelo) {
            datos.push(clasificado.vehiculo.modelo)
        }

        if (clasificado.vehiculo.anio) {
            datos.push(clasificado.vehiculo.anio)
        }

        return datos.join(' · ')
    }

    if (clasificado.propiedad) {
        const datos = []

        if (clasificado.propiedad.area_total) {
            datos.push(`${clasificado.propiedad.area_total} m²`)
        }

        if (clasificado.propiedad.habitaciones) {
            datos.push(`${clasificado.propiedad.habitaciones} hab.`)
        }

        if (clasificado.propiedad.banos) {
            datos.push(`${clasificado.propiedad.banos} baños`)
        }

        return datos.join(' · ')
    }

    return ''
}

/*
|--------------------------------------------------------------------------
| Favorito Rápido (Registrar y Deseleccionar)
|--------------------------------------------------------------------------
*/
const alternarFavorito = clasificado => {
    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: clasificado.id,
            modulo: 'clasificados',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                clasificado.es_favorito = !clasificado.es_favorito
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
        intencion.value ||
        operacion.value ||
        categoriaId.value ||
        ciudadId.value ||
        precioMin.value ||
        precioMax.value
    )
})

const aplicarFiltros = () => {
    router.get(
        route('clasificados.index'),
        {
            busqueda: busqueda.value || undefined,
            intencion: intencion.value || undefined,
            operacion: operacion.value || undefined,
            categoria_id: categoriaId.value || undefined,
            ciudad_id: ciudadId.value || undefined,
            precio_min: precioMin.value || undefined,
            precio_max: precioMax.value || undefined,
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
    intencion.value = ''
    operacion.value = ''
    categoriaId.value = ''
    ciudadId.value = ''
    precioMin.value = ''
    precioMax.value = ''

    router.get(
        route('clasificados.index'),
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
// Si ya vienen filtros aplicados en la URL, inicia abierto; si no, cerrado.
const filtrosAbiertos = ref(Boolean(
    props.filtros?.busqueda ||
    props.filtros?.intencion ||
    props.filtros?.operacion ||
    props.filtros?.categoria_id ||
    props.filtros?.ciudad_id ||
    props.filtros?.precio_min ||
    props.filtros?.precio_max
))
</script>

<template>

    <Head title="Clasificados | Portal Deseret" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="false">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo">

            <!-- =========================================================
                 ENCABEZADO DE SECCIÓN CON TONO ÁMBAR / NARANJA TRANSLÚCIDO
            ========================================================== -->
            <section
                class="border-b border-amber-200/80 bg-amber-100/25 backdrop-blur-md shadow-lg shadow-amber-500/10">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-amber-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-950">
                                    Avisos y Oportunidades
                                </span>
                            </div>

                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Clasificados
                            </h1>

                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Encuentra propiedades y vehículos o publica lo que estás buscando.
                            </p>
                        </div>

                        <!-- Botón superior protegido con estatus de bloqueo -->
                        <template v-if="estaBloqueado">
                            <span
                                class="inline-flex items-center justify-center rounded-xl bg-slate-400 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md cursor-not-allowed"
                                title="Tu cuenta tiene un bloqueo temporal activo y no puede crear publicaciones">
                                Publicación restringida
                            </span>
                        </template>
                        <template v-else>
                            <Link :href="route('clasificados.create')"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md shadow-amber-500/25 transition hover:bg-amber-700">
                                <span class="text-base">＋</span>
                                Publicar clasificado
                            </Link>
                        </template>
                    </div>
                </div>
            </section>

            <!-- =========================================================
                 CONTENIDO PRINCIPAL
            ========================================================== -->
            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-6 bg-amber-50 rounded-xl p-3 mt-2">

                <!-- ACCESOS RÁPIDOS ESTILIZADOS Y COMPACTOS -->
                <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-4">
                    <!-- En venta -->
                    <button type="button" @click="intencion = 'ofrecer'; operacion = 'venta'; aplicarFiltros();"
                        class="group relative flex items-center gap-3 rounded-xl border px-3.5 py-2.5 text-left transition-all duration-200"
                        :class="intencion === 'ofrecer' && operacion === 'venta'
                            ? 'border-amber-500 bg-amber-50/90 shadow-sm ring-1 ring-amber-400/50'
                            : 'border-slate-200 bg-white hover:border-amber-300 hover:bg-slate-50/80 shadow-xs'">
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 border border-emerald-200/60 text-sm shadow-xs transition group-hover:scale-105">
                            🏷️
                        </span>
                        <div class="min-w-0">
                            <span
                                class="block truncate text-xs font-bold text-[#172A3D] group-hover:text-amber-700 transition">
                                En venta
                            </span>
                            <span class="block truncate text-[11px] text-slate-500">
                                Inmuebles y autos
                            </span>
                        </div>
                    </button>

                    <!-- En arriendo -->
                    <button type="button" @click="intencion = 'ofrecer'; operacion = 'arriendo'; aplicarFiltros();"
                        class="group relative flex items-center gap-3 rounded-xl border px-3.5 py-2.5 text-left transition-all duration-200"
                        :class="intencion === 'ofrecer' && operacion === 'arriendo'
                            ? 'border-amber-500 bg-amber-50/90 shadow-sm ring-1 ring-amber-400/50'
                            : 'border-slate-200 bg-white hover:border-amber-300 hover:bg-slate-50/80 shadow-xs'">
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 border border-blue-200/60 text-sm shadow-xs transition group-hover:scale-105">
                            🏠
                        </span>
                        <div class="min-w-0">
                            <span
                                class="block truncate text-xs font-bold text-[#172A3D] group-hover:text-amber-700 transition">
                                En arriendo
                            </span>
                            <span class="block truncate text-[11px] text-slate-500">
                                Vivienda y comercio
                            </span>
                        </div>
                    </button>

                    <!-- Busco comprar -->
                    <button type="button" @click="intencion = 'buscar'; operacion = 'venta'; aplicarFiltros();"
                        class="group relative flex items-center gap-3 rounded-xl border px-3.5 py-2.5 text-left transition-all duration-200"
                        :class="intencion === 'buscar' && operacion === 'venta'
                            ? 'border-amber-500 bg-amber-50/90 shadow-sm ring-1 ring-amber-400/50'
                            : 'border-slate-200 bg-white hover:border-amber-300 hover:bg-slate-50/80 shadow-xs'">
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-orange-50 border border-orange-200/60 text-sm shadow-xs transition group-hover:scale-105">
                            🔎
                        </span>
                        <div class="min-w-0">
                            <span
                                class="block truncate text-xs font-bold text-[#172A3D] group-hover:text-amber-700 transition">
                                Busco comprar
                            </span>
                            <span class="block truncate text-[11px] text-slate-500">
                                Demandas de compra
                            </span>
                        </div>
                    </button>

                    <!-- Busco arrendar -->
                    <button type="button" @click="intencion = 'buscar'; operacion = 'arriendo'; aplicarFiltros();"
                        class="group relative flex items-center gap-3 rounded-xl border px-3.5 py-2.5 text-left transition-all duration-200"
                        :class="intencion === 'buscar' && operacion === 'arriendo'
                            ? 'border-amber-500 bg-amber-50/90 shadow-sm ring-1 ring-amber-400/50'
                            : 'border-slate-200 bg-white hover:border-amber-300 hover:bg-slate-50/80 shadow-xs'">
                        <span
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-purple-50 border border-purple-200/60 text-sm shadow-xs transition group-hover:scale-105">
                            📋
                        </span>
                        <div class="min-w-0">
                            <span
                                class="block truncate text-xs font-bold text-[#172A3D] group-hover:text-amber-700 transition">
                                Busco arrendar
                            </span>
                            <span class="block truncate text-[11px] text-slate-500">
                                Peticiones de arriendo
                            </span>
                        </div>
                    </button>
                </div>

                <!-- BUSCADOR Y FILTROS COLAPSABLE CON SOMBRA ÁMBAR -->
                <section
                    class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-amber-500/15 transition-all">
                    <div class="flex items-center justify-between gap-4">
                        <!-- Título y descripción (clickeable para alternar) -->
                        <button type="button" @click="filtrosAbiertos = !filtrosAbiertos"
                            class="flex flex-1 items-center justify-between text-left focus:outline-none group">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2
                                        class="text-base font-bold text-[#172A3D] group-hover:text-amber-700 transition">
                                        Buscar clasificados
                                    </h2>
                                    <span v-if="hayFiltros"
                                        class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-extrabold text-amber-800">
                                        Filtros activos
                                    </span>
                                </div>
                                <p class="text-xs sm:text-sm text-slate-500 font-medium">
                                    Filtra propiedades y vehículos según lo que necesitas.
                                </p>
                            </div>

                            <!-- Flecha indicadora de colapso -->
                            <div
                                class="flex items-center gap-2 text-xs font-bold text-slate-500 group-hover:text-amber-700">
                                <span class="hidden sm:inline">
                                    {{ filtrosAbiertos ? 'Ocultar filtros' : 'Mostrar filtros' }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 transform transition-transform duration-300"
                                    :class="filtrosAbiertos ? 'rotate-180 text-amber-600' : 'text-slate-400'"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        <!-- Botón Limpiar filtros (solo si hay filtros activos) -->
                        <button v-if="hayFiltros" type="button" @click="limpiarFiltros"
                            class="shrink-0 text-xs font-bold text-amber-700 hover:text-amber-900 underline">
                            Limpiar filtros
                        </button>
                    </div>

                    <!-- Contenido colapsable con animación -->
                    <Transition enter-active-class="transition duration-300 ease-out"
                        enter-from-class="transform opacity-0 -translate-y-2"
                        enter-to-class="transform opacity-100 translate-y-0"
                        leave-active-class="transition duration-200 ease-in"
                        leave-from-class="transform opacity-100 translate-y-0"
                        leave-to-class="transform opacity-0 -translate-y-2">
                        <div v-show="filtrosAbiertos" class="mt-5 border-t border-slate-100 pt-5">
                            <form @submit.prevent="buscar" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                                <!-- Búsqueda -->
                                <div class="lg:col-span-2">
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        ¿Qué estás buscando?
                                    </label>
                                    <div class="relative">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                        </svg>
                                        <input v-model="busqueda" type="text"
                                            placeholder="Casa, apartamento, lote, carro, moto..."
                                            class="w-full h-11 rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 outline-none transition focus:border-amber-600 focus:ring-2 focus:ring-amber-100" />
                                    </div>
                                </div>

                                <!-- Categoría -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Categoría
                                    </label>
                                    <select v-model="categoriaId"
                                        class="w-full h-11 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-medium text-[#172A3D] focus:border-amber-600 outline-none">
                                        <option value="">Todas</option>
                                        <option v-for="categoria in categorias" :key="categoria.id"
                                            :value="categoria.id">
                                            {{ categoria.nombre }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Ciudad -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Ubicación
                                    </label>
                                    <select v-model="ciudadId"
                                        class="w-full h-11 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-medium text-[#172A3D] focus:border-amber-600 outline-none">
                                        <option value="">Todas las ciudades</option>
                                        <option v-for="ciudad in ciudades" :key="ciudad.id" :value="ciudad.id">
                                            {{ ciudad.nombre }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Intención -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Tipo de aviso
                                    </label>
                                    <select v-model="intencion"
                                        class="w-full h-11 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-medium text-[#172A3D] focus:border-amber-600 outline-none">
                                        <option value="">Todos</option>
                                        <option value="ofrecer">Ofrezco</option>
                                        <option value="buscar">Busco</option>
                                    </select>
                                </div>

                                <!-- Operación -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Operación
                                    </label>
                                    <select v-model="operacion"
                                        class="w-full h-11 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-medium text-[#172A3D] focus:border-amber-600 outline-none">
                                        <option value="">Todas</option>
                                        <option value="venta">Compra / Venta</option>
                                        <option value="arriendo">Arriendo</option>
                                    </select>
                                </div>

                                <!-- Precio mínimo -->
                                <div>
                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Precio mínimo
                                    </label>
                                    <input v-model="precioMin" type="number" min="0" placeholder="$ 0"
                                        class="w-full h-11 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm text-[#172A3D] focus:border-amber-600 outline-none font-semibold" />
                                </div>

                                <!-- Precio máximo y botón buscar -->
                                <div class="flex items-end gap-2">
                                    <div class="flex-1">
                                        <label
                                            class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Precio máximo
                                        </label>
                                        <input v-model="precioMax" type="number" min="0" placeholder="Sin límite"
                                            class="w-full h-11 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm text-[#172A3D] focus:border-amber-600 outline-none font-semibold" />
                                    </div>
                                    <button type="submit"
                                        class="h-11 rounded-xl bg-amber-600 px-6 text-xs sm:text-sm font-black text-white hover:bg-amber-700 transition shadow-md shadow-amber-500/20">
                                        Buscar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </Transition>
                </section>

                <!-- RESULTADOS -->
                <div
                    class="mb-5 flex bg-white rounded-2xl border border-slate-300 p-5 shadow-2xl shadow-amber-500/15 flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-1">
                    <div>
                        <p class="text-xs sm:text-sm font-bold text-amber-700 uppercase tracking-wide">Avisos
                            clasificados</p>
                        <h2 class="mt-1 text-2xl font-black tracking-tight text-[#172A3D]">
                            Publicaciones disponibles
                        </h2>
                    </div>

                    <span v-if="clasificados.data?.length" class="text-xs sm:text-sm font-semibold text-slate-600">
                        {{ clasificados.data.length }} resultados
                    </span>
                </div>

                <!-- SIN RESULTADOS -->
                <div v-if="!clasificados.data?.length"
                    class="rounded-3xl border border-slate-300 bg-white px-6 py-16 text-center shadow-2xl shadow-amber-500/15">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 text-3xl text-amber-600 border border-amber-200">
                        🔎
                    </div>

                    <h3 class="text-lg font-bold text-[#172A3D]">
                        No encontramos clasificados
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-xs sm:text-sm text-slate-500 font-medium">
                        Intenta cambiar los filtros o publica un clasificado indicando lo que estás buscando.
                    </p>

                    <div class="mt-6">
                        <template v-if="estaBloqueado">
                            <span
                                class="inline-flex items-center justify-center rounded-xl bg-slate-400 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md cursor-not-allowed">
                                Publicación restringida
                            </span>
                        </template>
                        <template v-else>
                            <Link :href="route('clasificados.create')"
                                class="inline-flex items-center rounded-xl bg-amber-600 px-5 py-2.5 text-xs sm:text-sm font-black text-white hover:bg-amber-700 transition shadow-md">
                                Publicar clasificado
                            </Link>
                        </template>
                    </div>
                </div>

                <!-- GRID DE CLASIFICADOS: CONTENEDOR BLANCO CON SOMBRA ÁMBAR Y CARDS INTACTAS -->
                <div v-else
                    class="grid grid-cols-1 gap-6 bg-white rounded-2xl border border-slate-300 p-5 shadow-2xl shadow-amber-500/15 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <div v-for="clasificado in clasificados.data" :key="clasificado.id"
                        class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15 transition duration-300 hover:-translate-y-1 hover:border-amber-400">

                        <!-- Imagen (Link al detalle) -->
                        <Link :href="route('clasificados.show', clasificado.id)"
                            class="relative aspect-[4/3] block overflow-hidden bg-slate-100">
                            <img v-if="obtenerImagen(clasificado)" :src="obtenerImagen(clasificado)"
                                :alt="clasificado.titulo"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />

                            <div v-else class="flex h-full w-full flex-col items-center justify-center text-slate-300">
                                <span class="text-4xl">
                                    {{ clasificado.vehiculo ? '🚗' : '🏠' }}
                                </span>
                                <span class="mt-2 text-xs font-bold text-slate-400">
                                    Sin imagen
                                </span>
                            </div>

                            <!-- Tipo de Operación -->
                            <div class="absolute left-3 top-3 pointer-events-none rounded-full px-3 py-1 text-xs font-bold shadow-sm backdrop-blur"
                                :class="obtenerTipo(clasificado).clase">
                                {{ obtenerTipo(clasificado).icono }}
                                {{ obtenerTipo(clasificado).texto }}
                            </div>

                            <!-- Destacado -->
                            <div v-if="clasificado.destacado"
                                class="absolute right-14 top-3 pointer-events-none rounded-full bg-amber-400 px-3 py-1 text-xs font-black text-amber-950 shadow-sm">
                                ⭐ Destacado
                            </div>
                        </Link>

                        <!-- Botón de Favorito Flotante Rápido (Unificado con Corazón) -->
                        <button type="button" @click.stop="alternarFavorito(clasificado)"
                            class="absolute right-3 top-3 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-white/95 shadow-md backdrop-blur transition hover:scale-110"
                            :class="clasificado.es_favorito ? 'bg-red-50 ring-1 ring-red-200' : ''"
                            :title="clasificado.es_favorito ? 'Quitar de favoritos' : 'Guardar en favoritos'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="h-5 w-5 transition-colors"
                                :class="clasificado.es_favorito ? 'fill-red-600 text-red-600' : 'fill-none text-slate-400 hover:text-red-500'">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                        </button>

                        <!-- Contenido -->
                        <Link :href="route('clasificados.show', clasificado.id)" class="flex flex-1 flex-col p-4">
                            <!-- Categoría -->
                            <div class="mb-2 flex items-center gap-2 text-[11px] font-bold text-slate-500">
                                <span class="text-amber-700">
                                    {{ obtenerTipoClasificado(clasificado) }}
                                </span>
                                <span>•</span>
                                <span>
                                    {{ clasificado.categoria?.nombre }}
                                </span>
                            </div>

                            <!-- Título -->
                            <h3
                                class="line-clamp-2 min-h-[2.75rem] text-sm sm:text-base font-black text-[#172A3D] transition group-hover:text-amber-600">
                                {{ clasificado.titulo }}
                            </h3>

                            <!-- Resumen del vehículo / propiedad -->
                            <p v-if="obtenerResumen(clasificado)"
                                class="mt-1 truncate text-xs font-bold text-slate-600">
                                {{ obtenerResumen(clasificado) }}
                            </p>

                            <!-- Precio -->
                            <div class="mt-3">
                                <template v-if="clasificado.precio !== null && clasificado.precio !== undefined">
                                    <span class="text-base sm:text-lg font-black text-[#172A3D]">
                                        {{ formatearPrecio(clasificado.precio) }}
                                    </span>
                                    <span v-if="clasificado.intencion === 'buscar'"
                                        class="ml-1 text-[11px] text-slate-500 font-bold">
                                        máximo
                                    </span>
                                </template>
                                <span v-else class="text-xs font-bold text-slate-500">
                                    Precio a consultar
                                </span>
                            </div>

                            <!-- Ubicación -->
                            <div v-if="obtenerUbicacion(clasificado)"
                                class="mt-auto border-t border-slate-100 pt-3 flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                                <span>📍</span>
                                <span class="truncate">
                                    {{ obtenerUbicacion(clasificado) }}
                                </span>
                            </div>
                        </Link>
                    </div>
                </div>

                <!-- =====================================================
                     PAGINACIÓN
                ========================================================== -->
                <nav v-if="clasificados.links?.length > 3" class="mt-8 flex flex-wrap justify-center gap-2"
                    aria-label="Paginación">
                    <button v-for="link in clasificados.links" :key="link.label" type="button" :disabled="!link.url"
                        @click="cambiarPagina(link.url)"
                        class="rounded-lg border px-3.5 py-2 text-xs sm:text-sm font-bold transition shadow-xs" :class="[
                            link.active
                                ? 'border-[#172A3D] bg-[#172A3D] text-white'
                                : 'border-slate-300 bg-white text-slate-700 hover:border-amber-500 hover:bg-amber-50',
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