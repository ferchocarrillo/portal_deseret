<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3' // <--- Añade usePage aquí
import { route } from 'ziggy-js'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    // ==========================================================
    // OFERTAS PÚBLICAS
    // ==========================================================
    ofertas: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            meta: {},
        }),
    },
    destacadas: {
        type: Array,
        default: () => [],
    },
    modalidades: {
        type: Array,
        default: () => [],
    },
    tiposContrato: {
        type: Array,
        default: () => [],
    },
    nivelesExperiencia: {
        type: Array,
        default: () => [],
    },
    skills: {
        type: Array,
        default: () => [],
    },
    filtros: {
        type: Object,
        default: () => ({}),
    },

    // ==========================================================
    // ÁREA PRIVADA DEL USUARIO
    // ==========================================================
    resumenPostulaciones: {
        type: Object,
        default: () => ({
            total: 0,
            en_revision: 0,
            entrevistas: 0,
            en_proceso: 0,
            finalizadas: 0,
        }),
    },
    postulacionesRecientes: {
        type: Array,
        default: () => [],
    },
    ofertasGuardadas: {
        type: Number,
        default: 0,
    },
})
const page = usePage()

// Validar si el usuario autenticado tiene un bloqueo temporal activo
const estaBloqueado = computed(() => {
    const usuario = page.props.auth?.user
    if (!usuario?.bloqueado_hasta) return false
    return new Date(usuario.bloqueado_hasta) > new Date()
})
// ==========================================================
// FILTROS
// ==========================================================
const busqueda = ref(props.filtros?.busqueda ?? '')
const modalidad = ref(props.filtros?.modalidad ?? '')
const tipoContrato = ref(props.filtros?.tipo_contrato ?? '')
const experiencia = ref(props.filtros?.nivel_experiencia ?? '')
const skill = ref(props.filtros?.skill ?? '')
const ciudad = ref(props.filtros?.ciudad ?? '')

// ==========================================================
// OFERTAS
// ==========================================================
const ofertasRecientes = computed(() => props.ofertas?.data ?? [])
const ofertasDestacadas = computed(() => props.destacadas ?? [])

// ==========================================================
// ACCIONES Y FAVORITOS RÁPIDOS (OPTIMISTA)
// ==========================================================
const alternarFavorito = (oferta) => {
    const nuevoEstado = !Boolean(oferta.es_favorito)
    oferta.es_favorito = nuevoEstado

    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: oferta.id,
            modulo: 'empleos',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onError: () => {
                oferta.es_favorito = !nuevoEstado
            },
        }
    )
}

const buscar = () => {
    router.get(
        route('empleos.index'),
        {
            busqueda: busqueda.value || undefined,
            modalidad: modalidad.value || undefined,
            tipo_contrato: tipoContrato.value || undefined,
            nivel_experiencia: experiencia.value || undefined,
            skill: skill.value || undefined,
            ciudad: ciudad.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

const limpiarFiltros = () => {
    busqueda.value = ''
    modalidad.value = ''
    tipoContrato.value = ''
    experiencia.value = ''
    skill.value = ''
    ciudad.value = ''

    router.get(
        route('empleos.index'),
        {},
        {
            preserveState: false,
            preserveScroll: false,
        }
    )
}

const hayFiltros = computed(() => {
    return Boolean(
        busqueda.value ||
        modalidad.value ||
        tipoContrato.value ||
        experiencia.value ||
        skill.value ||
        ciudad.value
    )
})

// ==========================================================
// FORMATO
// ==========================================================
const formatearFecha = (fecha) => {
    if (!fecha) return ''
    return new Intl.DateTimeFormat('es-CO', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(fecha))
}

const formatearSalario = (oferta) => {
    if (!oferta.mostrar_salario) return 'Salario no especificado'

    const formatoMoneda = (valor) => {
        return `$${Number(valor).toLocaleString('es-CO', {
            maximumFractionDigits: 0,
        })}`
    }

    switch (oferta.tipo_salario) {
        case 'smlmv':
            return 'Salario mínimo legal vigente (SMLMV)'
        case 'salario_especifico':
            if (oferta.salario_monto !== null && oferta.salario_monto !== undefined && oferta.salario_monto !== '') {
                return formatoMoneda(oferta.salario_monto)
            }
            return 'Salario no especificado'
        case 'a_convenir':
            return 'A convenir'
    }

    if (oferta.salario_minimo && oferta.salario_maximo) {
        return `${formatoMoneda(oferta.salario_minimo)} - ${formatoMoneda(oferta.salario_maximo)}`
    }
    if (oferta.salario_minimo) return `Desde ${formatoMoneda(oferta.salario_minimo)}`
    if (oferta.salario_maximo) return `Hasta ${formatoMoneda(oferta.salario_maximo)}`

    return 'Salario no especificado'
}

const nombreEmpresa = (oferta) => {
    if (oferta.empresa?.nombre) return oferta.empresa.nombre
    if (oferta.usuario?.name) return oferta.usuario.name
    return 'Publicador independiente'
}

const inicialEmpresa = (oferta) => {
    const nombre = nombreEmpresa(oferta)
    return nombre ? nombre.charAt(0).toUpperCase() : 'E'
}

// ==========================================================
// ESTADOS DE POSTULACIÓN
// ==========================================================
const claseEstado = (estado) => {
    const clases = {
        postulado: 'bg-slate-100 text-slate-900 border border-slate-300',
        en_revision: 'bg-amber-100 text-amber-950 border border-amber-300',
        citado: 'bg-indigo-100 text-indigo-950 border border-indigo-300',
        en_proceso: 'bg-blue-100 text-blue-950 border border-blue-300',
        seleccionado: 'bg-emerald-100 text-emerald-950 border border-emerald-300',
        rechazado: 'bg-red-100 text-red-950 border border-red-300',
        retirado: 'bg-slate-200 text-slate-800 border border-slate-300',
    }
    return clases[estado] ?? 'bg-slate-100 text-slate-900 border border-slate-300'
}

const textoEstado = (estado) => {
    const estados = {
        postulado: 'Postulado',
        en_revision: 'En revisión',
        citado: 'Citado a entrevista',
        en_proceso: 'En proceso',
        seleccionado: 'Seleccionado',
        rechazado: 'No seleccionado',
        retirado: 'Retirado',
    }
    return estados[estado] ?? estado
}

const mostrarFiltros = ref(Boolean(
    props.filtros?.modalidad ||
    props.filtros?.tipo_contrato ||
    props.filtros?.nivel_experiencia ||
    props.filtros?.skill ||
    props.filtros?.ciudad
))

const totalFiltrosSecundarios = computed(() => {
    let count = 0
    if (modalidad.value) count++
    if (tipoContrato.value) count++
    if (experiencia.value) count++
    if (skill.value) count++
    if (ciudad.value) count++
    return count
})
</script>

<template>

    <Head title="Empleos" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ENCABEZADO -->
            <section
                class="border-b border-[#D99A24]/80 bg-[#D99A24]/20 backdrop-blur-md shadow-lg shadow-[#D99A24]/10 ">
                <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8 ">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between ">
                        <div class="max-w-3xl ">
                            <p class="mb-2 text-sm font-bold uppercase tracking-wider text-indigo-700">
                                Oportunidades laborales
                            </p>
                            <h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">
                                Encuentra tu próxima oportunidad
                            </h1>
                            <p class="mt-3 max-w-2xl text-base leading-7 text-slate-700">
                                Explora oportunidades laborales, encuentra ofertas acordes con tu perfil y realiza
                                seguimiento a tus postulaciones.
                            </p>
                        </div>

                        <!-- ACCIONES -->
                        <div class="flex flex-wrap gap-3">
                            <Link :href="route('empleos.mis-postulaciones')"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100">
                                Mis postulaciones
                            </Link>

                            <Link :href="route('empleos.mis-ofertas')"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-100">
                                Mis ofertas
                            </Link>

                            <!-- Si está bloqueado, podemos deshabilitarlo o cambiarle el estilo -->
                            <template v-if="estaBloqueado">
                                <span
                                    class="inline-flex items-center justify-center rounded-xl bg-slate-400 px-4 py-2.5 text-sm font-semibold text-white shadow-sm cursor-not-allowed"
                                    title="Tu cuenta tiene un bloqueo temporal activo y no puede crear publicaciones">
                                    Publicación restringida
                                </span>
                            </template>
                            <template v-else>
                                <Link :href="route('empleos.create')"
                                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                                    Publicar oferta
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="mx-auto max-w-7xl px-6 py-8 lg:px-8 space-y-12 bg-[#D99A24]/20 rounded-xl p-3 mt-2"">

                <!-- BUSCADOR -->
                <section
                    class="rounded-2xl border border-slate-300 bg-white p-5 shadow-[0_20px_50px_rgba(79,70,229,0.2)]">
                    <form @submit.prevent="buscar">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">

                            <!-- TEXTO -->
                            <div class="relative flex-1">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="7" />
                                    <path d="m20 20-4-4" />
                                </svg>
                                <input v-model="busqueda" type="text" placeholder="Cargo, empresa o habilidad..."
                                    class="w-full rounded-xl border border-slate-300 py-2.5 pl-11 pr-4 text-sm text-slate-900 placeholder-slate-500 outline-none transition focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100" />
                            </div>

                            <!-- MODALIDAD -->
                            <select v-model="modalidad"
                                class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium text-slate-900 outline-none focus:border-indigo-600">
                                <option value="" class="text-slate-600">Modalidad (Todas)</option>
                                <option v-for="item in modalidades" :key="item.value ?? item"
                                    :value="item.value ?? item" class="text-slate-900">
                                    {{ item.label ?? item }}
                                </option>
                            </select>

                            <!-- CONTRATO -->
                            <select v-model="tipoContrato"
                                class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm font-medium text-slate-900 outline-none focus:border-indigo-600">
                                <option value="" class="text-slate-600">Contrato (Todos)</option>
                                <option v-for="item in tiposContrato" :key="item.value ?? item"
                                    :value="item.value ?? item" class="text-slate-900">
                                    {{ item.label ?? item }}
                                </option>
                            </select>

                            <!-- BUSCAR -->
                            <button type="submit"
                                class="rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                                Buscar
                            </button>
                        </div>

                        <!-- FILTROS SECUNDARIOS -->
                        <div class="mt-3 flex flex-wrap items-center gap-3">
                            <select v-model="experiencia"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-medium text-slate-900 outline-none">
                                <option value="" class="text-slate-600">Experiencia (Todas)</option>
                                <option v-for="item in nivelesExperiencia" :key="item.value ?? item"
                                    :value="item.value ?? item" class="text-slate-900">
                                    {{ item.label ?? item }}
                                </option>
                            </select>

                            <select v-model="skill"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-medium text-slate-900 outline-none">
                                <option value="" class="text-slate-600">Habilidad (Todas)</option>
                                <option v-for="item in skills" :key="item.id" :value="item.id" class="text-slate-900">
                                    {{ item.name }}
                                </option>
                            </select>

                            <input v-model="ciudad" type="text" placeholder="Ciudad"
                                class="w-40 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-medium text-slate-900 placeholder-slate-500 outline-none" />

                            <button v-if="hayFiltros" type="button" @click="limpiarFiltros"
                                class="ml-auto text-xs font-bold text-slate-700 hover:text-indigo-700 underline">
                                Limpiar filtros
                            </button>
                        </div>
                    </form>
                </section>

                <!-- DESTACADAS -->
                <section v-if="ofertasDestacadas.length">
                    <div
                        class="mb-5 rounded-2xl border border-slate-300 bg-white p-5 shadow-[0_20px_50px_rgba(79,70,229,0.2)]">
                        <p class="text-sm font-bold text-indigo-700">Seleccionadas para destacar</p>
                        <h2 class="mt-1 text-2xl font-bold text-slate-950">Oportunidades destacadas</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <div v-for="oferta in ofertasDestacadas" :key="oferta.id"
                            class="group relative overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-[0_20px_50px_rgba(79,70,229,0.2)] transition hover:-translate-y-0.5 hover:border-indigo-400">

                            <!-- Botón de Favorito Flotante con Corazón -->
                            <button type="button" @click.stop="alternarFavorito(oferta)"
                                class="absolute right-3 top-3 z-20 flex h-9 w-9 items-center justify-center rounded-full bg-white/95 shadow-md backdrop-blur transition hover:scale-110"
                                :class="oferta.es_favorito ? 'bg-red-50 ring-1 ring-red-200' : 'hover:bg-slate-50'"
                                :title="oferta.es_favorito ? 'Quitar de favoritos' : 'Guardar en favoritos'">

                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="h-5 w-5 transition-colors"
                                    :class="oferta.es_favorito ? 'fill-red-600 text-red-600' : 'fill-none text-slate-400 hover:text-red-500'">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>
                            </button>

                            <Link :href="route('empleos.show', oferta.id)" class="block">
                                <div class="h-1.5 bg-indigo-600"></div>

                                <div class="p-5">
                                    <div class="flex items-start justify-between gap-4 pr-8">
                                        <div
                                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 border border-slate-200 text-lg font-bold text-slate-900">
                                            {{ inicialEmpresa(oferta) }}
                                        </div>

                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <span v-if="oferta.ya_postulado"
                                                class="rounded-full bg-emerald-100 border border-emerald-300 px-3 py-1 text-xs font-bold text-emerald-950">
                                                Postulado
                                            </span>
                                            <span
                                                class="rounded-full bg-indigo-100 border border-indigo-300 px-3 py-1 text-xs font-bold text-indigo-950">
                                                Destacada
                                            </span>
                                        </div>
                                    </div>

                                    <h3
                                        class="mt-5 line-clamp-2 text-lg font-bold text-slate-950 transition group-hover:text-indigo-700">
                                        {{ oferta.titulo }}
                                    </h3>

                                    <p class="mt-1 text-sm font-semibold text-slate-700">
                                        {{ nombreEmpresa(oferta) }}
                                    </p>

                                    <div class="mt-4 space-y-2 text-sm font-medium text-slate-800">
                                        <p v-if="oferta.ciudad">📍 {{ oferta.ciudad }}</p>
                                        <p v-if="oferta.modalidad">💼 {{ oferta.modalidad }}</p>
                                        <p>💰 {{ formatearSalario(oferta) }}</p>
                                    </div>

                                    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                                        <span class="text-xs font-semibold text-slate-600">
                                            {{ formatearFecha(oferta.fecha_publicacion) }}
                                        </span>
                                        <span class="text-sm font-bold text-indigo-700 group-hover:underline">
                                            Ver oferta →
                                        </span>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </section>

                <!-- OFERTAS RECIENTES -->
                <section>
                    <div
                        class="mb-5 rounded-2xl border border-slate-300 bg-white p-5 shadow-[0_20px_50px_rgba(79,70,229,0.2)] flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-bold text-indigo-700">Oportunidades disponibles</p>
                            <h2 class="mt-1 text-2xl font-bold text-slate-950">Ofertas recientes</h2>
                        </div>

                        <p v-if="ofertas?.total" class="text-sm font-semibold text-slate-700">
                            {{ ofertas.total }} {{ ofertas.total === 1 ? 'oferta disponible' : 'ofertas disponibles' }}
                        </p>
                    </div>

                    <!-- GRID DE OFERTAS -->
                    <div v-if="ofertasRecientes.length" class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                        <div v-for="oferta in ofertasRecientes" :key="oferta.id"
                            class="group relative flex h-full flex-col rounded-2xl border border-slate-300 bg-white p-5 shadow-[0_20px_50px_rgba(79,70,229,0.2)] transition hover:-translate-y-0.5 hover:border-indigo-400">

                            <!-- Botón de Favorito con Corazón SVG Reactivo -->
                            <button type="button" @click.stop="alternarFavorito(oferta)"
                                class="absolute right-3 top-3 z-20 flex h-9 w-9 items-center justify-center rounded-full bg-white/95 text-base shadow-md backdrop-blur transition hover:scale-110"
                                :class="oferta.es_favorito ? 'bg-red-50 ring-1 ring-red-200' : 'hover:bg-slate-50'"
                                :title="oferta.es_favorito ? 'Quitar de favoritos' : 'Guardar en favoritos'">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="h-5 w-5 transition-colors"
                                    :class="oferta.es_favorito ? 'fill-red-600 text-red-600' : 'fill-none text-slate-400 hover:text-red-500'">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>
                            </button>

                            <Link :href="route('empleos.show', oferta.id)" class="flex flex-1 flex-col">
                                <div class="flex items-start justify-between gap-4 pr-8">
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 border border-slate-200 font-bold text-slate-900">
                                        {{ inicialEmpresa(oferta) }}
                                    </div>

                                    <div v-if="oferta.destacada || oferta.ya_postulado"
                                        class="flex flex-wrap items-center justify-end gap-2">
                                        <span v-if="oferta.ya_postulado"
                                            class="rounded-full bg-emerald-100 border border-emerald-300 px-2.5 py-1 text-xs font-bold text-emerald-950">
                                            Postulado
                                        </span>
                                        <span v-if="oferta.destacada"
                                            class="rounded-full bg-indigo-100 border border-indigo-300 px-2.5 py-1 text-xs font-bold text-indigo-950">
                                            Destacada
                                        </span>
                                    </div>
                                </div>

                                <h3
                                    class="mt-4 line-clamp-2 text-lg font-bold text-slate-950 transition group-hover:text-indigo-700">
                                    {{ oferta.titulo }}
                                </h3>

                                <p class="mt-1 truncate text-sm font-semibold text-slate-700">
                                    {{ nombreEmpresa(oferta) }}
                                </p>

                                <div class="mt-4 space-y-2 text-sm font-medium text-slate-800">
                                    <p v-if="oferta.ciudad">📍 {{ oferta.ciudad }}</p>
                                    <p v-if="oferta.modalidad">💼 {{ oferta.modalidad }}</p>
                                    <p v-if="oferta.tipo_contrato">📄 {{ oferta.tipo_contrato }}</p>
                                    <p>💰 {{ formatearSalario(oferta) }}</p>
                                </div>

                                <div v-if="oferta.skills?.length" class="mt-4 flex flex-wrap gap-1.5">
                                    <span v-for="item in oferta.skills.slice(0, 3)" :key="item.id"
                                        class="rounded-full bg-slate-100 border border-slate-300 px-2.5 py-1 text-xs font-semibold text-slate-900">
                                        {{ item.name }}
                                    </span>
                                    <span v-if="oferta.skills.length > 3"
                                        class="rounded-full bg-slate-100 border border-slate-300 px-2.5 py-1 text-xs font-bold text-slate-700">
                                        +{{ oferta.skills.length - 3 }}
                                    </span>
                                </div>

                                <div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4">
                                    <span class="text-xs font-semibold text-slate-600">
                                        {{ formatearFecha(oferta.fecha_publicacion) }}
                                    </span>
                                    <span class="text-sm font-bold text-indigo-700 group-hover:underline">
                                        Ver oferta →
                                    </span>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- SIN OFERTAS -->
                    <div v-else
                        class="rounded-2xl border border-slate-300 bg-white px-6 py-14 text-center shadow-[0_20px_50px_rgba(79,70,229,0.2)]">
                        <h3 class="text-lg font-bold text-slate-950">No encontramos ofertas</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm font-medium text-slate-700">
                            No existen ofertas que coincidan con los filtros seleccionados.
                        </p>
                        <button v-if="hayFiltros" type="button" @click="limpiarFiltros"
                            class="mt-5 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition">
                            Ver todas las ofertas
                        </button>
                    </div>

                    <!-- PAGINACIÓN -->
                    <div v-if="ofertas?.links?.length > 3" class="mt-7 flex flex-wrap justify-center gap-2">
                        <template v-for="(link, index) in ofertas.links" :key="index">
                            <Link v-if="link.url" :href="link.url" preserve-scroll
                                class="rounded-lg border px-3 py-2 text-sm font-semibold transition" :class="link.active
                                    ? 'border-slate-950 bg-slate-950 text-white'
                                    : 'border-slate-300 bg-white text-slate-900 hover:bg-slate-100'"
                                v-html="link.label" />
                            <span v-else
                                class="rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm font-medium text-slate-500"
                                v-html="link.label" />
                        </template>
                    </div>
                </section>

                <!-- ÁREA PERSONAL -->
                <div class="rounded-2xl border border-slate-300 bg-white p-6 shadow-[0_20px_50px_rgba(79,70,229,0.2)]">
                    <p class="text-sm font-bold uppercase tracking-wider text-indigo-700">
                        Área personal
                    </p>
                    <h2 class="mt-1 text-2xl font-bold text-slate-950">
                        Mi actividad laboral
                    </h2>
                    <p class="mt-2 text-sm text-slate-700 font-medium">
                        Consulta el estado de tus postulaciones y realiza seguimiento a tus procesos.
                    </p>
                </div>

                <!-- INDICADORES PRIVADOS -->
                <section class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-5">
                    <div
                        class="rounded-2xl border border-slate-300 bg-white p-5 shadow-[0_20px_50px_rgba(79,70,229,0.2)]">
                        <p class="text-3xl font-bold text-slate-950">
                            {{ resumenPostulaciones.total }}
                        </p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            Postulaciones
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-slate-300 bg-white p-5 shadow-[0_20px_50px_rgba(79,70,229,0.2)]">
                        <p class="text-3xl font-bold text-amber-700">
                            {{ resumenPostulaciones.en_revision }}
                        </p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            En revisión
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-slate-300 bg-white p-5 shadow-[0_20px_50px_rgba(79,70,229,0.2)]">
                        <p class="text-3xl font-bold text-indigo-700">
                            {{ resumenPostulaciones.entrevistas }}
                        </p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            Entrevistas
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-slate-300 bg-white p-5 shadow-[0_20px_50px_rgba(79,70,229,0.2)]">
                        <p class="text-3xl font-bold text-blue-700">
                            {{ resumenPostulaciones.en_proceso }}
                        </p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            En proceso
                        </p>
                    </div>

                    <div
                        class="rounded-2xl border border-slate-300 bg-white p-5 shadow-[0_20px_50px_rgba(79,70,229,0.2)]">
                        <p class="text-3xl font-bold text-slate-950">
                            {{ ofertasGuardadas }}
                        </p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            Guardadas
                        </p>
                    </div>
                </section>

                <!-- POSTULACIONES RECIENTES -->
                <section
                    class="rounded-2xl border border-slate-300 bg-white p-6 shadow-[0_20px_50px_rgba(79,70,229,0.2)] space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-indigo-700">Seguimiento</p>
                            <h2 class="mt-1 text-xl font-bold text-slate-950">
                                Mis postulaciones recientes
                            </h2>
                        </div>

                        <Link :href="route('empleos.mis-postulaciones')"
                            class="text-sm font-bold text-indigo-700 hover:text-indigo-800 underline">
                            Ver todas →
                        </Link>
                    </div>

                    <!-- LISTADO DE POSTULACIONES -->
                    <div v-if="postulacionesRecientes.length" class="space-y-3">
                        <div v-for="postulacion in postulacionesRecientes" :key="postulacion.id"
                            class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-slate-50/70 p-4 sm:flex-row sm:items-center sm:justify-between">

                            <div>
                                <h3 class="font-bold text-slate-950">
                                    {{ postulacion.oferta_empleo?.titulo }}
                                </h3>

                                <p class="mt-1 text-sm font-semibold text-slate-700">
                                    {{ postulacion.oferta_empleo ? nombreEmpresa(postulacion.oferta_empleo) : 'Oferta no disponible' }}
                                </p>

                                <p class="mt-2 text-xs font-semibold text-slate-600">
                                    Postulado {{ formatearFecha(postulacion.fecha_postulacion ?? postulacion.created_at)
                                    }}
                                </p>
                            </div>

                            <div class="flex items-center gap-4">
                                <span class="rounded-full px-3 py-1.5 text-xs font-bold"
                                    :class="claseEstado(postulacion.estado)">
                                    {{ textoEstado(postulacion.estado) }}
                                </span>

                                <Link :href="route('empleos.show', postulacion.oferta_empleo_id)"
                                    class="text-sm font-bold text-indigo-700 hover:underline">
                                    Ver oferta →
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- SIN POSTULACIONES -->
                    <div v-else
                        class="rounded-xl border border-dashed border-slate-300 bg-slate-50/50 px-6 py-8 text-center">
                        <h3 class="font-bold text-slate-950">Aún no tienes postulaciones</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm font-medium text-slate-700">
                            Cuando te postules a una oportunidad, podrás consultar aquí el estado del proceso.
                        </p>
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