<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed } from 'vue';

const props = defineProps({
    curso: {
        type: Object,
        required: true,
    },

    inscripciones: {
        type: Array,
        default: () => [],
    },
});

const pendientes = computed(() =>
    props.inscripciones.filter(
        (inscripcion) => inscripcion.estado === 'pendiente'
    )
);

const activas = computed(() =>
    props.inscripciones.filter(
        (inscripcion) => inscripcion.estado === 'activa'
    )
);

const canceladas = computed(() =>
    props.inscripciones.filter(
        (inscripcion) => inscripcion.estado === 'cancelada'
    )
);

function estadoLabel(estado) {
    switch (estado) {
        case 'activa':
            return 'Activa';

        case 'cancelada':
            return 'Cancelada';

        case 'rechazada':
            return 'Rechazada';

        case 'pendiente':
        default:
            return 'Pendiente';
    }
}

function estadoClasses(estado) {
    switch (estado) {
        case 'activa':
            return 'bg-emerald-50 text-emerald-800 border border-emerald-200';

        case 'cancelada':
            return 'bg-red-50 text-red-800 border border-red-200';

        case 'rechazada':
            return 'bg-slate-100 text-slate-700 border border-slate-200';

        case 'pendiente':
        default:
            return 'bg-amber-50 text-amber-900 border border-amber-200';
    }
}

function formatearFecha(fecha) {
    if (!fecha) {
        return '—';
    }

    return new Date(fecha).toLocaleDateString('es-CO', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
}
</script>

<template>

    <Head :title="`Inscripciones - ${curso.nombre} | Portal Deseret`" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- =========================================================
                 ENCABEZADO DE SECCIÓN CON TONO NARANJA TRANSLÚCIDO
            ========================================================== -->
            <section
                class="border-b border-orange-200/80 bg-orange-100/25 backdrop-blur-md shadow-lg shadow-orange-500/10">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-orange-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-orange-950">
                                    Administración de formación
                                </span>
                            </div>

                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Inscripciones
                            </h1>

                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Administra los participantes de tu curso.
                            </p>
                        </div>

                        <Link :href="route('cursos.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition hover:bg-orange-50 hover:border-orange-300">
                            ← Volver a cursos
                        </Link>
                    </div>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-6">

                <!-- Información del curso -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                    <div class="p-6 sm:p-7">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-orange-600">
                                    Curso
                                </p>

                                <h2 class="mt-1 text-xl sm:text-2xl font-black text-[#172A3D]">
                                    {{ curso.nombre }}
                                </h2>

                                <div class="mt-3 flex flex-wrap gap-2 text-xs font-bold text-slate-600">
                                    <span v-if="curso.modalidad"
                                        class="rounded-full bg-slate-100 border border-slate-200 px-3 py-1">
                                        {{ curso.modalidad }}
                                    </span>

                                    <span v-if="curso.fecha_inicio"
                                        class="rounded-full bg-slate-100 border border-slate-200 px-3 py-1">
                                        Inicio: {{ formatearFecha(curso.fecha_inicio) }}
                                    </span>

                                    <span v-if="curso.fecha_fin"
                                        class="rounded-full bg-slate-100 border border-slate-200 px-3 py-1">
                                        Fin: {{ formatearFecha(curso.fecha_fin) }}
                                    </span>
                                </div>
                            </div>

                            <Link :href="route('cursos.show', curso.id)"
                                class="inline-flex items-center justify-center rounded-xl border border-orange-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-orange-950 transition hover:bg-orange-50 shadow-xs">
                                Ver curso
                            </Link>
                        </div>
                    </div>
                </section>

                <!-- Resumen -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <!-- Pendientes -->
                    <div class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-orange-500/15">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Pendientes
                                </p>
                                <p class="mt-1 text-2xl sm:text-3xl font-black text-amber-600">
                                    {{ pendientes.length }}
                                </p>
                            </div>

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 font-bold">
                                ⏳
                            </div>
                        </div>
                    </div>

                    <!-- Activas -->
                    <div class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-orange-500/15">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Inscritos oficialmente
                                </p>
                                <p class="mt-1 text-2xl sm:text-3xl font-black text-emerald-600">
                                    {{ activas.length }}
                                </p>
                            </div>

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-600 font-bold">
                                ✓
                            </div>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-orange-500/15">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Total de solicitudes
                                </p>
                                <p class="mt-1 text-2xl sm:text-3xl font-black text-[#172A3D]">
                                    {{ inscripciones.length }}
                                </p>
                            </div>

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-50 border border-orange-200 text-orange-600 font-bold">
                                👥
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                    <div class="border-b border-slate-100 bg-orange-50/40 px-6 py-5">
                        <h3 class="text-base sm:text-lg font-bold text-[#172A3D]">
                            Solicitudes de inscripción
                        </h3>
                        <p class="mt-0.5 text-xs text-slate-500 font-medium">
                            Revisa las solicitudes y administra el estado de cada participante.
                        </p>
                    </div>

                    <!-- Sin registros -->
                    <div v-if="inscripciones.length === 0" class="px-6 py-16 text-center">
                        <div
                            class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 text-3xl text-orange-600">
                            📭
                        </div>

                        <h4 class="mt-4 text-lg font-bold text-[#172A3D]">
                            Aún no hay inscripciones
                        </h4>

                        <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                            Cuando los usuarios soliciten inscribirse aparecerán aquí.
                        </p>
                    </div>

                    <!-- Tabla desktop -->
                    <div v-else class="hidden overflow-x-auto md:block">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                        Participante
                                    </th>
                                    <th
                                        class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                        Contacto
                                    </th>
                                    <th
                                        class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                        Solicitud
                                    </th>
                                    <th
                                        class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                                        Estado
                                    </th>
                                    <th
                                        class="px-6 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-500">
                                        Acción
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 bg-white">
                                <tr v-for="inscripcion in inscripciones" :key="inscripcion.id"
                                    class="transition hover:bg-orange-50/40">
                                    <!-- Participante -->
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="font-bold text-slate-800">
                                            {{ inscripcion.nombre_inscrito }}
                                        </div>
                                        <div v-if="inscripcion.usuario"
                                            class="mt-0.5 text-xs text-slate-400 font-medium">
                                            Usuario #{{ inscripcion.user_id }}
                                        </div>
                                    </td>

                                    <!-- Contacto -->
                                    <td class="px-6 py-4">
                                        <div class="text-xs sm:text-sm font-semibold text-slate-700">
                                            {{ inscripcion.email_inscrito }}
                                        </div>
                                        <div class="mt-0.5 text-xs text-slate-500 font-medium">
                                            {{ inscripcion.telefono_inscrito || 'Sin teléfono' }}
                                        </div>
                                    </td>

                                    <!-- Fecha -->
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-xs sm:text-sm text-slate-600 font-medium">
                                        {{ formatearFecha(inscripcion.created_at) }}
                                    </td>

                                    <!-- Estado -->
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold shadow-xs"
                                            :class="estadoClasses(inscripcion.estado)">
                                            {{ estadoLabel(inscripcion.estado) }}
                                        </span>
                                    </td>

                                    <!-- Acción -->
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <Link :href="route('cursos.inscripciones.show', {
                                            curso: curso.id,
                                            inscripcion: inscripcion.id
                                        })"
                                            class="inline-flex items-center rounded-xl bg-orange-600 px-3.5 py-2 text-xs sm:text-sm font-black text-white transition hover:bg-orange-700 shadow-xs">
                                            Ver / Administrar
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Cards móvil -->
                    <div v-if="inscripciones.length > 0" class="divide-y divide-slate-100 md:hidden">
                        <div v-for="inscripcion in inscripciones" :key="inscripcion.id" class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-[#172A3D]">
                                        {{ inscripcion.nombre_inscrito }}
                                    </h4>

                                    <p class="mt-0.5 break-all text-xs text-slate-600 font-medium">
                                        {{ inscripcion.email_inscrito }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                        {{ inscripcion.telefono_inscrito || 'Sin teléfono' }}
                                    </p>
                                </div>

                                <span class="shrink-0 rounded-full px-3 py-1 text-xs font-bold shadow-xs"
                                    :class="estadoClasses(inscripcion.estado)">
                                    {{ estadoLabel(inscripcion.estado) }}
                                </span>
                            </div>

                            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3">
                                <span class="text-xs text-slate-400 font-medium">
                                    Solicitud: {{ formatearFecha(inscripcion.created_at) }}
                                </span>

                                <Link :href="route('cursos.inscripciones.show', {
                                    curso: curso.id,
                                    inscripcion: inscripcion.id
                                })"
                                    class="rounded-xl bg-orange-600 px-3.5 py-2 text-xs font-black text-white transition hover:bg-orange-700 shadow-xs">
                                    Administrar
                                </Link>
                            </div>
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