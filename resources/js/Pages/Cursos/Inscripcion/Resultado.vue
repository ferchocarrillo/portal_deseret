<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    curso: {
        type: Object,
        required: true,
    },

    inscripcion: {
        type: Object,
        required: true,
    },
});

function formatearFecha(fecha) {
    if (!fecha) {
        return 'No especificada';
    }

    return new Intl.DateTimeFormat('es-CO', {
        dateStyle: 'long',
    }).format(new Date(fecha));
}

function formatearModalidad(modalidad) {
    if (!modalidad) {
        return 'No especificada';
    }

    return modalidad.charAt(0).toUpperCase() + modalidad.slice(1);
}
</script>

<template>

    <Head title="Inscripción enviada | Portal Deseret" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ENCABEZADO TRANSLÚCIDO CON IDENTIDAD NARANJA -->
            <section
                class="border-b border-orange-200/80 bg-orange-100/25 backdrop-blur-md shadow-lg shadow-orange-500/10">
                <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="mb-1 flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-orange-600"></span>
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-orange-950">
                            Proceso de formación
                        </span>
                    </div>

                    <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                        Solicitud de inscripción
                    </h1>

                    <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                        Tu solicitud ha sido procesada exitosamente.
                    </p>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">

                <!-- Tarjeta principal -->
                <div
                    class="overflow-hidden rounded-3xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">

                    <!-- Encabezado de éxito -->
                    <div class="px-6 py-10 text-center sm:px-10">
                        <!-- Icono -->
                        <div
                            class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-emerald-100 border border-emerald-300 text-emerald-700 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-emerald-600"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>

                        <h2 class="mt-6 text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                            ¡Solicitud de inscripción enviada!
                        </h2>

                        <p class="mx-auto mt-3 max-w-xl text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                            Tu solicitud de inscripción fue registrada correctamente. El creador del curso revisará tu
                            solicitud antes de activar tu inscripción.
                        </p>
                    </div>

                    <!-- Estado -->
                    <div class="border-y border-slate-200 bg-orange-50/40 px-6 py-6 sm:px-10">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Estado de la solicitud
                                </p>

                                <p class="mt-1 text-sm sm:text-base font-black text-[#172A3D]">
                                    Pendiente de revisión
                                </p>
                            </div>

                            <span
                                class="inline-flex items-center rounded-full bg-amber-100 px-3.5 py-1.5 text-xs font-black text-amber-900 border border-amber-300 shadow-xs">
                                Pendiente
                            </span>
                        </div>
                    </div>

                    <!-- Información del curso -->
                    <div class="px-6 py-7 sm:px-10">
                        <h3 class="text-base sm:text-lg font-bold text-[#172A3D]">
                            Información del curso
                        </h3>

                        <div class="mt-5 space-y-5">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Curso
                                </p>

                                <p class="mt-1 text-base sm:text-lg font-black text-[#172A3D]">
                                    {{ curso.titulo }}
                                </p>
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Modalidad
                                    </p>

                                    <p class="mt-1 text-sm sm:text-base font-bold text-slate-700">
                                        {{ formatearModalidad(curso.modalidad) }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Fecha de inicio
                                    </p>

                                    <p class="mt-1 text-sm sm:text-base font-bold text-slate-700">
                                        {{ formatearFecha(curso.fecha_inicio) }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="inscripcion.created_at">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Solicitud enviada
                                </p>

                                <p class="mt-1 text-sm sm:text-base font-bold text-slate-700">
                                    {{ formatearFecha(inscripcion.created_at) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="mx-6 mb-7 rounded-2xl border border-orange-200 bg-orange-50/70 p-5 sm:mx-10 shadow-xs">
                        <div class="flex gap-3">
                            <div class="mt-0.5 shrink-0 text-orange-600 font-bold">
                                ℹ️
                            </div>

                            <div>
                                <p class="font-bold text-orange-950">
                                    ¿Qué sigue?
                                </p>

                                <p class="mt-1 text-xs sm:text-sm leading-relaxed text-orange-900 font-medium">
                                    Tu solicitud quedó registrada como pendiente. Puedes consultar el estado de tus
                                    inscripciones desde <strong>Mis inscripciones</strong>.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div
                        class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50/70 px-6 py-6 sm:flex-row sm:justify-center sm:px-10">
                        <Link :href="route('cursos.misInscripciones')"
                            class="inline-flex items-center justify-center rounded-xl bg-orange-600 px-6 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-700">
                            Ver mis inscripciones
                        </Link>

                        <Link :href="route('cursos.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 shadow-xs">
                            Volver a cursos
                        </Link>
                    </div>

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