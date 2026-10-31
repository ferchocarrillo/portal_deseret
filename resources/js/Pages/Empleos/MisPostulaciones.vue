<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
    postulaciones: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            meta: {},
        }),
    },
})

const formatearFecha = (fecha) => {
    if (!fecha) return ''
    return new Intl.DateTimeFormat('es-CO', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(fecha))
}

const nombreEmpresa = (oferta) => {
    if (oferta?.empresa?.nombre) return oferta.empresa.nombre
    return 'Publicador independiente'
}

const inicialEmpresa = (oferta) => {
    const nombre = nombreEmpresa(oferta)
    return nombre ? nombre.charAt(0).toUpperCase() : 'E'
}

const claseEstado = (estado) => {
    const clases = {
        postulado: 'bg-slate-100 text-slate-700 border border-slate-200',
        pendiente: 'bg-slate-100 text-slate-700 border border-slate-200',
        en_revision: 'bg-amber-50 text-amber-800 border border-amber-200',
        citado: 'bg-indigo-50 text-indigo-800 border border-indigo-200',
        en_proceso: 'bg-blue-50 text-blue-800 border border-blue-200',
        seleccionado: 'bg-emerald-50 text-emerald-800 border border-emerald-200',
        rechazado: 'bg-red-50 text-red-800 border border-red-200',
        retirado: 'bg-slate-100 text-slate-500 border border-slate-200',
    }
    return clases[estado] ?? 'bg-slate-100 text-slate-600 border border-slate-200'
}

const textoEstado = (estado) => {
    const estados = {
        postulado: 'Postulada',
        pendiente: 'Pendiente',
        en_revision: 'En revisión',
        citado: 'Citado a entrevista',
        en_proceso: 'En proceso',
        seleccionado: 'Seleccionado',
        rechazado: 'No seleccionado',
        retirado: 'Retirada',
    }
    return estados[estado] ?? estado
}
</script>

<template>

    <Head title="Mis Postulaciones | Empleos" />

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
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-[#D99A24]"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">
                                    Seguimiento de empleo
                                </span>
                            </div>
                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Mis postulaciones
                            </h1>
                            <p class="mt-1 text-sm text-slate-600 font-medium">
                                Consulta y realiza seguimiento al estado de tus procesos laborales.
                            </p>
                        </div>

                        <Link :href="route('empleos.index')"
                            class="inline-flex items-center justify-center rounded-xl bg-[#172A3D] px-4 py-2.5 text-xs sm:text-sm font-black text-white shadow-md shadow-[#172A3D]/20 transition hover:bg-[#D99A24] hover:text-[#172A3D]">
                            Explorar más empleos
                        </Link>
                    </div>
                </div>
            </section>

            <!-- ======================================================
                 CONTENIDO PRINCIPAL
            ======================================================= -->
            <div class="py-8 ">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6 bg-[#D99A24]/20 rounded-xl p-3 mt-2">

                    <!-- LISTADO DE POSTULACIONES -->
                    <div v-if="postulaciones.data?.length" class="space-y-4">
                        <div v-for="item in postulaciones.data" :key="item.id"
                            class="flex flex-col gap-5 rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15 transition duration-300 hover:border-amber-400 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex items-start gap-4">
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border border-amber-200 bg-amber-50 text-lg font-black text-amber-900 shadow-xs">
                                    {{ inicialEmpresa(item.oferta_empleo) }}
                                </div>

                                <div>
                                    <h3 class="text-lg font-black text-[#172A3D]">
                                        {{ item.oferta_empleo?.titulo ?? 'Oferta no disponible' }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-slate-600 font-semibold">
                                        {{ nombreEmpresa(item.oferta_empleo) }}
                                    </p>

                                    <div
                                        class="mt-2 flex flex-wrap items-center gap-3 text-xs text-slate-500 font-medium">
                                        <span>
                                            Postulado: {{ formatearFecha(item.fecha_postulacion ?? item.created_at) }}
                                        </span>
                                        <span v-if="item.oferta_empleo?.modalidad">
                                            • {{ item.oferta_empleo.modalidad }}
                                        </span>
                                        <span v-if="item.oferta_empleo?.ciudad">
                                            • {{ item.oferta_empleo.ciudad }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-between gap-4 border-t border-slate-100 pt-4 sm:border-0 sm:pt-0 sm:justify-end">
                                <span class="rounded-full px-3 py-1.5 text-xs font-bold shadow-xs"
                                    :class="claseEstado(item.estado)">
                                    {{ textoEstado(item.estado) }}
                                </span>

                                <Link v-if="item.oferta_empleo" :href="route('empleos.show', item.oferta_empleo.id)"
                                    class="inline-flex items-center text-xs sm:text-sm font-bold text-[#D99A24] hover:text-amber-700 transition">
                                    Ver oferta →
                                </Link>
                            </div>
                        </div>

                        <!-- PAGINACIÓN -->
                        <div v-if="postulaciones.links?.length > 3" class="mt-8 flex flex-wrap justify-center gap-2">
                            <template v-for="(link, idx) in postulaciones.links" :key="idx">
                                <Link v-if="link.url" :href="link.url" preserve-scroll
                                    class="rounded-lg border px-3 py-2 text-xs font-bold transition shadow-xs"
                                    :class="link.active
                                        ? 'border-[#172A3D] bg-[#172A3D] text-white'
                                        : 'border-slate-300 bg-white text-slate-700 hover:border-amber-400 hover:bg-amber-50'" v-html="link.label" />
                                <span v-else
                                    class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2 text-xs text-slate-300"
                                    v-html="link.label" />
                            </template>
                        </div>
                    </div>

                    <!-- ESTADO VACÍO -->
                    <div v-else
                        class="rounded-3xl border border-slate-300 bg-white px-6 py-16 text-center shadow-2xl shadow-amber-500/15">
                        <div
                            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 text-3xl text-[#D99A24]">
                            💼
                        </div>
                        <h3 class="text-lg font-bold text-[#172A3D]">
                            No tienes postulaciones activas
                        </h3>
                        <p class="mx-auto mt-2 max-w-md text-xs sm:text-sm text-slate-500 font-medium">
                            Aún no te has postulado a ninguna vacante. Encuentra oportunidades que se ajusten a tu
                            perfil.
                        </p>
                        <Link :href="route('empleos.index')"
                            class="mt-6 inline-flex rounded-xl bg-[#D99A24] px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md transition hover:bg-amber-600">
                            Ver ofertas de empleo
                        </Link>
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