<script setup>
import { reactive } from 'vue'
import { Link, router, Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    oferta: {
        type: Object,
        required: true,
    },
    postulaciones: {
        type: Object,
        required: true,
    },
})

const opcionesEstado = [
    { label: 'Pendiente de revisión', value: 'pendiente' },
    { label: 'En revisión', value: 'en_revision' },
    { label: 'Citado', value: 'citado' },
    { label: 'En proceso de selección', value: 'en_proceso' },
    { label: 'Seleccionado', value: 'seleccionado' },
    { label: 'Rechazado', value: 'rechazado' },
    { label: 'Retiró su candidatura', value: 'retirado' },
]

const formularios = reactive({})
const errores = reactive({})
const guardando = reactive({})
const mensajes = reactive({})

const normalizarEstado = (estado) => {
    const valor = String(estado || '').toLowerCase()

    const equivalencias = {
        postulado: 'pendiente',
        aceptado: 'seleccionado',
        pendiente: 'pendiente',
        pendiente_revision: 'pendiente',
        en_revision: 'en_revision',
        citado: 'citado',
        en_proceso: 'en_proceso',
        seleccionado: 'seleccionado',
        rechazado: 'rechazado',
        retirado: 'retirado',
    }

    return equivalencias[valor] || 'pendiente'
}

const formularioPostulacion = (postulacion) => {
    if (!formularios[postulacion.id]) {
        formularios[postulacion.id] = {
            estado: normalizarEstado(postulacion.estado),
            observaciones: postulacion.observaciones || '',
        }
    }

    return formularios[postulacion.id]
}

const nombrePostulante = (user) => {
    if (!user) return 'Usuario no disponible'

    const nombre = [
        user.nombres,
        user.apellidos,
    ].filter(Boolean).join(' ').trim()

    return nombre || user.name || 'Postulante'
}

const inicialesPostulante = (user) => {
    return nombrePostulante(user)
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((parte) => parte[0])
        .join('')
        .toUpperCase()
}

const formatoFecha = (fecha) => {
    if (!fecha) return 'Sin fecha'

    const fechaLimpia = String(fecha).substring(0, 10)
    const [anio, mes, dia] = fechaLimpia.split('-')

    if (!anio || !mes || !dia) return 'Sin fecha'

    return new Date(
        Number(anio),
        Number(mes) - 1,
        Number(dia)
    ).toLocaleDateString('es-CO', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}

const estadoLegible = (estado) => {
    const valor = String(estado || '').toLowerCase()

    const opcion = opcionesEstado.find(
        (item) => item.value === valor
    )

    if (opcion) return opcion.label

    const equivalencias = {
        postulado: 'Pendiente de revisión',
        pendiente: 'Pendiente de revisión',
        aceptado: 'Seleccionado',
        rechazado: 'Rechazado',
    }

    return equivalencias[valor] || estado || 'Sin estado'
}

const colorEstado = (estado) => {
    const valor = String(estado || '').toLowerCase()

    const colores = {
        seleccionado: 'border-emerald-200 bg-emerald-50 text-emerald-800',
        aceptado: 'border-emerald-200 bg-emerald-50 text-emerald-800',
        citado: 'border-blue-200 bg-blue-50 text-blue-800',
        en_proceso: 'border-indigo-200 bg-indigo-50 text-indigo-800',
        en_revision: 'border-amber-200 bg-amber-50 text-amber-900',
        pendiente: 'border-amber-200 bg-amber-50 text-amber-900',
        postulado: 'border-amber-200 bg-amber-50 text-amber-900',
        rechazado: 'border-red-200 bg-red-50 text-red-800',
        retirado: 'border-slate-200 bg-slate-100 text-slate-700',
    }

    return colores[valor] || 'border-slate-200 bg-slate-50 text-slate-700'
}

const contarEstados = (estados) => {
    return (props.postulaciones.data || []).filter((postulacion) =>
        estados.includes(String(postulacion.estado || '').toLowerCase())
    ).length
}

const guardarCambios = (postulacion) => {
    const id = postulacion.id
    const formulario = formularioPostulacion(postulacion)

    errores[id] = {}
    mensajes[id] = ''
    guardando[id] = true

    router.put(
        route('empleos.postulaciones.actualizar', {
            ofertaEmpleo: props.oferta.id,
            postulacion: id,
        }),
        {
            estado: formulario.estado,
            observaciones: formulario.observaciones,
        },
        {
            preserveScroll: true,

            onSuccess: () => {
                mensajes[id] = 'Gestión registrada correctamente.'
            },

            onError: (erroresServidor) => {
                errores[id] = erroresServidor
                mensajes[id] = 'No fue posible registrar la gestión.'
            },

            onFinish: () => {
                guardando[id] = false
            },
        }
    )
}
</script>

<template>

    <Head :title="`Postulantes: ${oferta.titulo} | Empleos`" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">

        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ======================================================
                 ENCABEZADO CON TONO AMARILLO/ÁMBAR TRANSLÚCIDO
            ======================================================= -->
            <section
                class="border-b border-amber-200/80 bg-amber-100/25 backdrop-blur-md shadow-lg shadow-amber-500/10">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <header class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
                        <div>
                            <Link :href="route('empleos.mis-ofertas')"
                                class="group inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-600 transition hover:text-[#D99A24]">
                                <span class="transition group-hover:-translate-x-1">←</span>
                                Mis ofertas
                            </Link>

                            <div class="mt-4 mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-[#D99A24]"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">
                                    Gestión de talento
                                </span>
                            </div>

                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Postulantes
                            </h1>

                            <p class="mt-1 text-xs sm:text-sm leading-6 text-slate-600 font-medium max-w-2xl">
                                Administra las candidaturas, revisa las hojas de vida y registra el avance de tu proceso
                                de selección.
                            </p>

                            <div
                                class="mt-3 inline-flex max-w-full items-center gap-2 rounded-xl border border-slate-300 bg-white/90 px-3 py-2 text-xs sm:text-sm text-slate-700 shadow-xs backdrop-blur">
                                <svg class="h-4 w-4 shrink-0 text-[#D99A24]" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 7h18M5 7l1 14h12l1-14M9 7V4h6v3" />
                                </svg>
                                <span class="truncate font-bold text-[#172A3D]">
                                    {{ oferta.titulo }}
                                </span>
                            </div>
                        </div>

                        <div
                            class="flex shrink-0 items-center gap-3 rounded-2xl border border-amber-200 bg-white/90 p-4 shadow-md backdrop-blur">
                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#D99A24] text-white font-bold shadow-xs">
                                👥
                            </div>
                            <div>
                                <p class="text-2xl font-black leading-none text-[#172A3D]">
                                    {{ postulaciones.total }}
                                </p>
                                <p class="mt-1 text-[11px] font-bold text-amber-900 uppercase tracking-wide">
                                    {{ postulaciones.total === 1 ? 'Postulación' : 'Postulaciones' }}
                                </p>
                            </div>
                        </div>
                    </header>
                </div>
            </section>

            <!-- ======================================================
                 CONTENIDO PRINCIPAL
            ======================================================= -->
            <div class="py-8">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">

                    <!-- Resumen -->
                    <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div
                            class="flex items-center gap-3 rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-amber-500/15">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700 font-bold">
                                ⏳
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Por revisar</p>
                                <p class="mt-0.5 text-2xl font-black text-[#172A3D]">
                                    {{ contarEstados(['postulado', 'pendiente']) }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-center gap-3 rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-amber-500/15">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 font-bold">
                                🔄
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">En proceso</p>
                                <p class="mt-0.5 text-2xl font-black text-[#172A3D]">
                                    {{ contarEstados(['en_revision', 'citado', 'en_proceso']) }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-center gap-3 rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-amber-500/15">
                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 font-bold">
                                ✓
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Seleccionados</p>
                                <p class="mt-0.5 text-2xl font-black text-[#172A3D]">
                                    {{ contarEstados(['seleccionado', 'aceptado']) }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Listado -->
                    <section class="space-y-6">
                        <div class="flex flex-wrap items-center justify-between gap-2 px-1">
                            <h2 class="text-xl font-black text-[#172A3D]">
                                Candidaturas recibidas
                            </h2>
                            <span class="text-xs sm:text-sm font-semibold text-slate-600">
                                Mostrando {{ postulaciones.data.length }} de {{ postulaciones.total }}
                            </span>
                        </div>

                        <!-- Estado vacío -->
                        <div v-if="!postulaciones.data?.length"
                            class="rounded-3xl border border-slate-300 bg-white px-6 py-16 text-center shadow-2xl shadow-amber-500/15">
                            <div
                                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 text-3xl text-[#D99A24]">
                                👤
                            </div>
                            <h3 class="text-lg font-bold text-[#172A3D]">
                                Todavía no tienes postulantes
                            </h3>
                            <p class="mx-auto mt-2 max-w-md text-xs sm:text-sm text-slate-500 font-medium">
                                Cuando una persona se postule a esta oferta, podrás revisar su perfil y gestionar su
                                candidatura desde aquí.
                            </p>
                        </div>

                        <div v-else class="space-y-5">
                            <article v-for="postulacion in postulaciones.data" :key="postulacion.id"
                                class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15 transition duration-300 hover:border-amber-400">

                                <!-- Cabecera de tarjeta -->
                                <div
                                    class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                                    <div class="flex min-w-0 items-start gap-4">
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#172A3D] text-lg font-black text-white shadow-sm">
                                            {{ inicialesPostulante(postulacion.user) }}
                                        </div>

                                        <div class="min-w-0">
                                            <h3 class="truncate text-base sm:text-lg font-black text-[#172A3D]">
                                                {{ nombrePostulante(postulacion.user) }}
                                            </h3>

                                            <p class="mt-0.5 break-all text-xs sm:text-sm text-slate-600 font-medium">
                                                {{ postulacion.user?.email || 'Correo no disponible' }}
                                            </p>

                                            <p class="mt-1.5 text-xs text-slate-400 font-medium">
                                                Postulado el {{ formatoFecha(postulacion.fecha_postulacion) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3 sm:justify-end">
                                        <span
                                            class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold shadow-xs"
                                            :class="colorEstado(postulacion.estado)">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ estadoLegible(postulacion.estado) }}
                                        </span>

                                        <a v-if="postulacion.user?.hoja_vida_principal?.id" :href="route('empleos.postulantes.hoja-vida', {
                                            ofertaEmpleo: oferta.id,
                                            postulacion: postulacion.id,
                                        })" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#172A3D] px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm transition hover:bg-[#D99A24] hover:text-[#172A3D]">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14 2v6h6M8 13h8M8 17h8" />
                                            </svg>
                                            Ver hoja de vida
                                        </a>

                                        <span v-else
                                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-bold text-slate-400">
                                            Sin hoja de vida
                                        </span>
                                    </div>
                                </div>

                                <!-- Gestión -->
                                <div class="border-t border-slate-200 bg-slate-50/80 px-5 py-5 sm:px-6">
                                    <div class="mb-4 flex items-center gap-2">
                                        <span class="text-sm">📋</span>
                                        <h4
                                            class="text-xs sm:text-sm font-bold text-[#172A3D] uppercase tracking-wider">
                                            Seguimiento de candidatura
                                        </h4>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
                                        <div class="lg:col-span-2">
                                            <label :for="`estado-${postulacion.id}`"
                                                class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                                Estado
                                            </label>

                                            <select :id="`estado-${postulacion.id}`"
                                                v-model="formularioPostulacion(postulacion).estado"
                                                class="w-full rounded-xl border-slate-300 bg-white px-3 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] shadow-xs transition focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100 outline-none">
                                                <option v-for="opcion in opcionesEstado" :key="opcion.value"
                                                    :value="opcion.value">
                                                    {{ opcion.label }}
                                                </option>
                                            </select>

                                            <p v-if="errores[postulacion.id]?.estado"
                                                class="mt-1 text-xs text-red-600 font-medium">
                                                {{ errores[postulacion.id].estado }}
                                            </p>
                                        </div>

                                        <div class="lg:col-span-3">
                                            <label :for="`observaciones-${postulacion.id}`"
                                                class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                                Observaciones internas
                                            </label>

                                            <textarea :id="`observaciones-${postulacion.id}`"
                                                v-model="formularioPostulacion(postulacion).observaciones" rows="3"
                                                maxlength="5000"
                                                placeholder="Registra comentarios de la revisión, entrevista o decisión..."
                                                class="w-full resize-y rounded-xl border-slate-300 bg-white px-3 py-3 text-xs sm:text-sm text-[#172A3D] shadow-xs transition placeholder:text-slate-400 focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100 outline-none"></textarea>

                                            <p v-if="errores[postulacion.id]?.observaciones"
                                                class="mt-1 text-xs text-red-600 font-medium">
                                                {{ errores[postulacion.id].observaciones }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="mt-4 flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:items-center sm:justify-between">
                                        <p v-if="mensajes[postulacion.id]" class="text-xs sm:text-sm font-bold"
                                            :class="Object.keys(errores[postulacion.id] || {}).length ? 'text-red-600' : 'text-emerald-700'"
                                            role="status">
                                            {{ mensajes[postulacion.id] }}
                                        </p>

                                        <p v-else class="text-xs text-slate-400 font-medium">
                                            Guarda los cambios para actualizar el seguimiento.
                                        </p>

                                        <button type="button" :disabled="guardando[postulacion.id]"
                                            @click="guardarCambios(postulacion)"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#172A3D] px-5 py-3 text-xs sm:text-sm font-black text-white shadow-sm transition hover:bg-[#D99A24] hover:text-[#172A3D] disabled:opacity-60 sm:w-auto">
                                            <svg v-if="guardando[postulacion.id]" class="h-4 w-4 animate-spin"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4" />
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                                            </svg>
                                            <span v-else>✓</span>
                                            {{ guardando[postulacion.id] ? 'Registrando...' : 'Registrar gestión' }}
                                        </button>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </section>

                    <!-- Paginación -->
                    <div v-if="postulaciones.links?.length > 3"
                        class="flex flex-wrap items-center justify-center gap-2 pt-2">
                        <template v-for="(link, index) in postulaciones.links" :key="index">
                            <Link v-if="link.url" :href="link.url" preserve-scroll
                                class="rounded-lg border px-3.5 py-2 text-xs font-bold transition shadow-xs"
                                :class="link.active
                                    ? 'border-[#172A3D] bg-[#172A3D] text-white'
                                    : 'border-slate-300 bg-white text-slate-700 hover:border-amber-400 hover:bg-amber-50'" v-html="link.label" />
                            <span v-else
                                class="rounded-lg border border-slate-100 bg-slate-50 px-3.5 py-2 text-xs text-slate-300"
                                v-html="link.label" />
                        </template>
                    </div>

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