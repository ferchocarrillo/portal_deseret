<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref } from 'vue';

const props = defineProps({
    curso: {
        type: Object,
        required: true,
    },

    participante: {
        type: Object,
        required: true,
    },
});

const procesando = ref(false);

// ==========================================================
// ESTADO REACTIVO DE FAVORITOS (CORREGIDO Y SINCRONIZADO)
// ==========================================================
const esFavorito = ref(Boolean(props.curso?.es_favorito));
const procesandoFavorito = ref(false);

function alternarFavorito() {
    if (procesandoFavorito.value) return;

    procesandoFavorito.value = true;

    // Actualización optimista inmediata en la interfaz
    const nuevoEstado = !esFavorito.value;
    esFavorito.value = nuevoEstado;
    if (props.curso) {
        props.curso.es_favorito = nuevoEstado;
    }

    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: props.curso.id,
            modulo: 'cursos',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onError: (errores) => {
                console.error('Error al alternar favorito:', errores);
                // Revertir en caso de error en el servidor
                esFavorito.value = !nuevoEstado;
                if (props.curso) {
                    props.curso.es_favorito = esFavorito.value;
                }
            },
            onFinish: () => {
                procesandoFavorito.value = false;
            },
        }
    );
}

function confirmarInscripcion() {
    if (procesando.value) {
        return;
    }

    procesando.value = true;

    router.post(
        route('cursos.inscribirse', props.curso.id),
        {},
        {
            preserveScroll: true,

            onError: () => {
                procesando.value = false;
            },

            onFinish: () => {
                procesando.value = false;
            },
        }
    );
}

function formatoFecha(fecha) {
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

    <Head title="Confirmar inscripción | Portal Deseret" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- =========================================================
                 ENCABEZADO DE SECCIÓN CON TONO NARANJA TRANSLÚCIDO
            ========================================================== -->
            <section
                class="border-b border-orange-200/80 bg-orange-100/25 backdrop-blur-md shadow-lg shadow-orange-500/10">
                <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-orange-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-orange-950">
                                    Cursos y Formación
                                </span>
                            </div>

                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Confirmar inscripción
                            </h1>

                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Revisa la información antes de enviar tu solicitud de inscripción.
                            </p>
                        </div>

                        <!-- BOTÓN DE FAVORITO EN EL ENCABEZADO -->
                        <button type="button" @click="alternarFavorito" :disabled="procesandoFavorito"
                            class="inline-flex items-center justify-center gap-2.5 rounded-xl px-4 py-3 text-xs sm:text-sm font-black transition shadow-md disabled:opacity-60"
                            :class="esFavorito
                                ? 'bg-amber-400 text-[#172A3D] shadow-amber-400/30 hover:bg-amber-500'
                                : 'bg-amber-100/70 text-[#172A3D] border border-amber-300 hover:bg-amber-200/80'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="h-5 w-5 shrink-0 transition-transform duration-200"
                                :class="esFavorito ? 'fill-red-600 text-red-600 scale-110' : 'fill-none text-slate-600'">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                            <span>{{ esFavorito ? 'Favorito!!' : 'Guardar en favoritos' }}</span>
                        </button>
                    </div>
                </div>
            </section>

            <!-- =========================================================
                 CONTENIDO PRINCIPAL REDISTRIBUIDO EN DOS COLUMNAS
            ========================================================== -->
            <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,1fr)_360px]">

                    <!-- COLUMNA IZQUIERDA: CURSO Y DATOS DEL PARTICIPANTE -->
                    <div class="space-y-6">

                        <!-- Información principal del curso -->
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                            <div class="border-b border-slate-100 bg-orange-50/40 px-6 py-5">
                                <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                    Información del curso
                                </h2>
                            </div>

                            <div class="space-y-6 p-6">
                                <!-- Título -->
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Curso
                                    </p>

                                    <h3 class="mt-1 text-xl sm:text-2xl font-black text-[#172A3D]">
                                        {{ curso.titulo }}
                                    </h3>
                                </div>

                                <!-- Datos del curso -->
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
                                            {{ formatoFecha(curso.fecha_inicio) }}
                                        </p>
                                    </div>

                                    <div v-if="curso.fecha_fin">
                                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                            Fecha de finalización
                                        </p>

                                        <p class="mt-1 text-sm sm:text-base font-bold text-slate-700">
                                            {{ formatoFecha(curso.fecha_fin) }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                            Cupos
                                        </p>

                                        <p v-if="curso.cupos_ilimitados"
                                            class="mt-1 text-sm sm:text-base font-bold text-emerald-700">
                                            Cupos ilimitados
                                        </p>

                                        <p v-else-if="curso.cupos_disponibles !== null"
                                            class="mt-1 text-sm sm:text-base font-bold text-slate-700">
                                            {{ curso.cupos_disponibles }}
                                            {{ curso.cupos_disponibles === 1 ? 'cupo disponible' : 'cupos disponibles'
                                            }}
                                        </p>

                                        <p v-else class="mt-1 text-sm sm:text-base font-bold text-slate-700">
                                            Cupos limitados
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Datos del participante -->
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                            <div class="border-b border-slate-100 bg-orange-50/40 px-6 py-5">
                                <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                    Tus datos
                                </h2>
                            </div>

                            <div class="grid gap-6 p-6 sm:grid-cols-2">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Nombre
                                    </p>

                                    <p class="mt-1 text-sm sm:text-base font-bold text-slate-800">
                                        {{ participante.nombre }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Teléfono
                                    </p>

                                    <p class="mt-1 text-sm sm:text-base font-bold text-slate-800">
                                        {{ participante.telefono || 'No registrado' }}
                                    </p>
                                </div>

                                <div class="sm:col-span-2">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Correo electrónico
                                    </p>

                                    <p class="mt-1 break-words text-xs sm:text-sm font-bold text-slate-800">
                                        {{ participante.email }}
                                    </p>
                                </div>
                            </div>
                        </section>

                    </div>

                    <!-- COLUMNA DERECHA: PANEL LATERAL (AVISO, ACCIONES Y DENUNCIA) -->
                    <aside class="space-y-6">

                        <!-- Aviso importante -->
                        <div
                            class="rounded-2xl border border-orange-200 bg-orange-50/70 p-5 shadow-xs backdrop-blur-md">
                            <div class="flex gap-3">
                                <div class="mt-0.5 shrink-0 text-orange-600 font-bold">
                                    ℹ️
                                </div>

                                <div>
                                    <p class="font-bold text-orange-950">
                                        Revisa tus datos
                                    </p>

                                    <p class="mt-1 text-xs sm:text-sm leading-relaxed text-orange-900 font-medium">
                                        Al confirmar, enviarás una solicitud de inscripción. El creador del curso
                                        revisará tu solicitud antes de activar tu inscripción.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Panel de acciones de confirmación -->
                        <div
                            class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-orange-500/15 space-y-4">
                            <h3 class="text-base font-black text-[#172A3D]">
                                Finalizar inscripción
                            </h3>

                            <button type="button" :disabled="procesando" @click="confirmarInscripcion"
                                class="flex w-full items-center justify-center rounded-xl bg-orange-600 px-6 py-3.5 text-xs sm:text-sm font-black text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-60">
                                <svg v-if="procesando" class="mr-2 h-4 w-4 animate-spin"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                                </svg>

                                {{ procesando ? 'Enviando solicitud...' : 'Confirmar inscripción' }}
                            </button>

                            <Link :href="route('cursos.index')"
                                class="flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 shadow-xs">
                                Volver al listado
                            </Link>
                        </div>

                      <!-- ================================================= -->
                        <!-- BOTÓN DE DENUNCIA / REPORTE DE INCIDENTE -->
                        <!-- ================================================= -->
                        <div
                            class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-orange-500/15 sm:p-6">
                            <Link
                                :href="route('denuncias.create', { modulo: 'cursos', reportable_type: 'App\\Models\\Curso', reportable_id: curso.id })"
                                class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs sm:text-sm font-bold text-red-700 transition hover:bg-red-100 hover:text-red-900 shadow-sm">
                                🚨 Reportar este curso
                            </Link>
                        </div>

                    </aside>

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