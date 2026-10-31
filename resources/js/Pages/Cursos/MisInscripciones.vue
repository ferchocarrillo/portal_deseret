<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref, computed } from 'vue';

const props = defineProps({
    inscripciones: {
        type: Object,
        required: true,
    },
});

/*
|--------------------------------------------------------------------------
| Estado de cancelación
|--------------------------------------------------------------------------
*/

const inscripcionACancelar = ref(null);
const cancelando = ref(false);

const motivoCancelacion = ref('');
const observacionCancelacion = ref('');

const erroresCancelacion = ref({});

/*
|--------------------------------------------------------------------------
| Motivos de cancelación
|--------------------------------------------------------------------------
*/

const motivosCancelacion = [
    {
        value: 'ya_no_puedo_asistir',
        label: 'Ya no puedo asistir al curso',
    },
    {
        value: 'horario_no_disponible',
        label: 'El horario no se ajusta a mi disponibilidad',
    },
    {
        value: 'modalidad_no_conveniente',
        label: 'La modalidad no es conveniente para mí',
    },
    {
        value: 'inscripcion_por_error',
        label: 'Me inscribí por error',
    },
    {
        value: 'otra_alternativa',
        label: 'Encontré otra alternativa de formación',
    },
    {
        value: 'motivos_personales',
        label: 'Motivos personales',
    },
    {
        value: 'motivos_laborales',
        label: 'Motivos laborales',
    },
    {
        value: 'motivos_economicos',
        label: 'Motivos económicos',
    },
    {
        value: 'otro',
        label: 'Otro motivo',
    },
];

/*
|--------------------------------------------------------------------------
| Estados
|--------------------------------------------------------------------------
*/

function etiquetaEstado(estado) {
    if (estado === 'activa') {
        return 'Inscripción activa';
    }

    if (estado === 'cancelada') {
        return 'Inscripción cancelada';
    }

    return 'Pendiente de revisión';
}

function claseEstado(estado) {
    if (estado === 'activa') {
        return 'bg-emerald-50 text-emerald-800 border border-emerald-200';
    }

    if (estado === 'cancelada') {
        return 'bg-red-50 text-red-800 border border-red-200';
    }

    return 'bg-amber-50 text-amber-900 border border-amber-200';
}

function clasePuntoEstado(estado) {
    if (estado === 'activa') {
        return 'bg-emerald-500';
    }

    if (estado === 'cancelada') {
        return 'bg-red-500';
    }

    return 'bg-amber-500';
}

/*
|--------------------------------------------------------------------------
| Formatos
|--------------------------------------------------------------------------
*/

function formatoFecha(fecha) {
    if (!fecha) {
        return 'No disponible';
    }

    return new Intl.DateTimeFormat('es-CO', {
        dateStyle: 'long',
    }).format(new Date(fecha));
}

function etiquetaModalidad(modalidad) {
    const modalidades = {
        virtual: 'Virtual',
        presencial: 'Presencial',
        hibrido: 'Híbrido',
    };

    return modalidades[modalidad] ?? modalidad ?? 'No especificada';
}

function etiquetaMotivo(motivo) {
    const encontrado = motivosCancelacion.find(
        (item) => item.value === motivo
    );

    return encontrado?.label ?? motivo ?? 'No especificado';
}

/*
|--------------------------------------------------------------------------
| Cancelación de inscripción
|--------------------------------------------------------------------------
*/

function abrirConfirmacionCancelacion(inscripcion) {
    if (inscripcion.estado !== 'pendiente') {
        return;
    }

    inscripcionACancelar.value = inscripcion;

    motivoCancelacion.value = '';
    observacionCancelacion.value = '';
    erroresCancelacion.value = {};
}

function cerrarConfirmacionCancelacion() {
    if (cancelando.value) {
        return;
    }

    inscripcionACancelar.value = null;
    motivoCancelacion.value = '';
    observacionCancelacion.value = '';
    erroresCancelacion.value = {};
}

const puedeConfirmarCancelacion = computed(() => {
    if (!motivoCancelacion.value) {
        return false;
    }

    if (
        motivoCancelacion.value === 'otro' &&
        !observacionCancelacion.value.trim()
    ) {
        return false;
    }

    return true;
});

function cancelarInscripcion() {
    if (
        !inscripcionACancelar.value ||
        cancelando.value ||
        !puedeConfirmarCancelacion.value
    ) {
        return;
    }

    cancelando.value = true;
    erroresCancelacion.value = {};

    router.delete(
        route('cursos.inscripciones.cancelar', {
            curso: inscripcionACancelar.value.curso.id,
            inscripcion: inscripcionACancelar.value.id,
        }),
        {
            data: {
                motivo_cancelacion: motivoCancelacion.value,
                observacion_cancelacion:
                    observacionCancelacion.value.trim() || null,
            },

            preserveScroll: true,

            onError: (errors) => {
                erroresCancelacion.value = errors ?? {};
            },

            onFinish: () => {
                cancelando.value = false;
            },

            onSuccess: () => {
                inscripcionACancelar.value = null;
                motivoCancelacion.value = '';
                observacionCancelacion.value = '';
                erroresCancelacion.value = {};
            },
        }
    );
}
</script>

<template>

    <Head title="Mis inscripciones | Portal Deseret" />

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
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-orange-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-orange-950">
                                    Seguimiento de formación
                                </span>
                            </div>

                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Mis inscripciones
                            </h1>

                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Consulta las solicitudes de inscripción que has realizado y su estado actual.
                            </p>
                        </div>

                        <Link :href="route('cursos.index')"
                            class="inline-flex items-center justify-center rounded-xl bg-orange-600 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md shadow-orange-500/20 transition hover:bg-orange-700">
                            Explorar cursos
                        </Link>
                    </div>
                </div>
            </section>

            <!-- =========================================================
                 CONTENIDO PRINCIPAL
            ========================================================== -->
            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-6">

                <!-- RESUMEN -->
                <section class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-orange-500/15">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-orange-50 border border-orange-200 text-orange-600 font-bold">
                            📚
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">
                                Tus inscripciones
                            </p>

                            <p class="text-2xl font-black text-[#172A3D]">
                                {{ inscripciones.total ?? 0 }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- ESTADO VACÍO -->
                <section v-if="!inscripciones.data?.length"
                    class="rounded-3xl border border-slate-300 bg-white px-6 py-16 text-center shadow-2xl shadow-orange-500/15">
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 text-3xl text-orange-600">
                        📖
                    </div>

                    <h3 class="mt-4 text-lg font-bold text-[#172A3D]">
                        Aún no tienes inscripciones
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-xs sm:text-sm leading-relaxed text-slate-500 font-medium">
                        Explora los cursos disponibles en la comunidad y encuentra una oportunidad de formación que sea
                        de tu interés.
                    </p>

                    <div class="mt-6">
                        <Link :href="route('cursos.index')"
                            class="inline-flex items-center justify-center rounded-xl bg-orange-600 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md transition hover:bg-orange-700">
                            Explorar cursos
                        </Link>
                    </div>
                </section>

                <!-- LISTA DE INSCRIPCIONES -->
                <section v-else class="space-y-4">
                    <article v-for="inscripcion in inscripciones.data" :key="inscripcion.id"
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15 transition duration-300 hover:border-orange-400">
                        <div class="p-6">

                            <!-- Cabecera -->
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0">
                                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-orange-900">
                                        Curso
                                    </p>

                                    <h3 class="mt-1 text-lg sm:text-xl font-black text-[#172A3D]">
                                        {{ inscripcion.curso?.titulo ?? 'Curso no disponible' }}
                                    </h3>
                                </div>

                                <!-- Estado -->
                                <span
                                    class="inline-flex w-fit shrink-0 items-center rounded-full px-3 py-1 text-xs font-bold shadow-xs"
                                    :class="claseEstado(inscripcion.estado)">
                                    <span class="mr-2 h-2 w-2 rounded-full"
                                        :class="clasePuntoEstado(inscripcion.estado)"></span>
                                    {{ etiquetaEstado(inscripcion.estado) }}
                                </span>
                            </div>

                            <!-- Información -->
                            <div class="mt-6 grid gap-5 border-t border-slate-100 pt-5 sm:grid-cols-2 lg:grid-cols-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Modalidad
                                    </p>
                                    <p class="mt-1 text-xs sm:text-sm font-bold text-slate-700">
                                        {{ etiquetaModalidad(inscripcion.curso?.modalidad) }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Solicitud enviada
                                    </p>
                                    <p class="mt-1 text-xs sm:text-sm font-bold text-slate-700">
                                        {{ formatoFecha(inscripcion.created_at) }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Activación
                                    </p>
                                    <p v-if="inscripcion.activado_at"
                                        class="mt-1 text-xs sm:text-sm font-bold text-emerald-700">
                                        {{ formatoFecha(inscripcion.activado_at) }}
                                    </p>
                                    <p v-else class="mt-1 text-xs sm:text-sm font-medium text-slate-500">
                                        {{ inscripcion.estado === 'cancelada' ? 'No activada' : 'Pendiente' }}
                                    </p>
                                </div>

                                <div v-if="inscripcion.estado === 'cancelada'">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Cancelada
                                    </p>
                                    <p class="mt-1 text-xs sm:text-sm font-bold text-red-700">
                                        {{ formatoFecha(inscripcion.cancelada_at) }}
                                    </p>
                                </div>
                            </div>

                            <!-- MENSAJE PENDIENTE -->
                            <div v-if="inscripcion.estado === 'pendiente'"
                                class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                                <div class="flex gap-3">
                                    <span class="text-amber-600 font-bold">⏳</span>
                                    <div>
                                        <p class="text-xs sm:text-sm font-bold text-amber-900">
                                            Solicitud pendiente
                                        </p>
                                        <p class="mt-0.5 text-xs sm:text-sm text-amber-800 font-medium">
                                            Tu solicitud fue enviada y está pendiente de revisión por el creador del
                                            curso.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- MENSAJE ACTIVA -->
                            <div v-else-if="inscripcion.estado === 'activa'"
                                class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3">
                                <div class="flex gap-3">
                                    <span class="text-emerald-600 font-bold">✓</span>
                                    <div>
                                        <p class="text-xs sm:text-sm font-bold text-emerald-900">
                                            Inscripción activa
                                        </p>
                                        <p class="mt-0.5 text-xs sm:text-sm text-emerald-800 font-medium">
                                            Tu inscripción fue aprobada por el creador del curso.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- MENSAJE CANCELADA -->
                            <div v-else-if="inscripcion.estado === 'cancelada'"
                                class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-4">
                                <div class="flex gap-3">
                                    <span class="text-red-600 font-bold">✕</span>
                                    <div class="min-w-0">
                                        <p class="text-xs sm:text-sm font-bold text-red-900">
                                            Inscripción cancelada
                                        </p>
                                        <p class="mt-0.5 text-xs sm:text-sm text-red-800 font-medium">
                                            Esta solicitud fue cancelada. Puedes volver a solicitar la inscripción si el
                                            curso continúa disponible.
                                        </p>

                                        <div v-if="inscripcion.motivo_cancelacion"
                                            class="mt-3 rounded-lg border border-red-200 bg-white/80 px-3 py-2.5">
                                            <p class="text-[10px] font-black uppercase tracking-wider text-red-600">
                                                Motivo
                                            </p>
                                            <p class="mt-0.5 text-xs sm:text-sm font-bold text-red-950">
                                                {{ etiquetaMotivo(inscripcion.motivo_cancelacion) }}
                                            </p>
                                            <p v-if="inscripcion.observacion_cancelacion"
                                                class="mt-1 text-xs text-slate-600 font-medium">
                                                {{ inscripcion.observacion_cancelacion }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ACCIONES -->
                            <div
                                class="mt-6 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                                <button v-if="inscripcion.estado === 'pendiente'" type="button"
                                    @click="abrirConfirmacionCancelacion(inscripcion)"
                                    class="inline-flex items-center justify-center rounded-xl border border-red-300 bg-white px-5 py-2.5 text-xs sm:text-sm font-bold text-red-600 transition hover:bg-red-50 shadow-xs">
                                    Cancelar inscripción
                                </button>

                                <Link v-if="inscripcion.estado === 'cancelada' && inscripcion.curso?.id"
                                    :href="route('cursos.inscripcion.confirmar', inscripcion.curso.id)"
                                    class="inline-flex items-center justify-center rounded-xl border border-orange-300 bg-white px-5 py-2.5 text-xs sm:text-sm font-bold text-orange-900 transition hover:bg-orange-50 shadow-xs">
                                    Volver a inscribirme
                                </Link>

                                <Link v-if="inscripcion.curso?.id" :href="route('cursos.show', inscripcion.curso.id)"
                                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 shadow-xs">
                                    Ver curso
                                </Link>

                                <Link :href="route('cursos.index')"
                                    class="inline-flex items-center justify-center rounded-xl bg-[#172A3D] px-5 py-2.5 text-xs sm:text-sm font-bold text-white transition hover:bg-orange-600 shadow-xs">
                                    Explorar cursos
                                </Link>
                            </div>
                        </div>
                    </article>
                </section>

                <!-- PAGINACIÓN -->
                <nav v-if="inscripciones.links && inscripciones.links.length > 3"
                    class="flex flex-wrap items-center justify-center gap-2 pt-4" aria-label="Paginación">
                    <template v-for="(link, index) in inscripciones.links" :key="index">
                        <Link v-if="link.url" :href="link.url" preserve-scroll
                            class="rounded-lg border px-3 py-2 text-xs sm:text-sm font-bold transition shadow-xs"
                            :class="link.active
                                ? 'border-[#172A3D] bg-[#172A3D] text-white'
                                : 'border-slate-300 bg-white text-slate-700 hover:border-orange-500 hover:bg-orange-50'"
                            v-html="link.label" />
                        <span v-else
                            class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2 text-xs sm:text-sm text-slate-400 font-medium"
                            v-html="link.label"></span>
                    </template>
                </nav>

            </main>
        </div>

        <!-- ============================================================= -->
        <!-- MODAL DE CANCELACIÓN -->
        <!-- ============================================================= -->
        <div v-if="inscripcionACancelar"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-900/60 px-4 py-6 backdrop-blur-xs"
            @click.self="cerrarConfirmacionCancelacion">
            <div class="w-full max-w-lg rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl sm:p-8">

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-100 text-red-600 font-bold">
                    ✕
                </div>

                <h3 class="mt-4 text-xl font-black text-[#172A3D]">
                    ¿Cancelar inscripción?
                </h3>

                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                    Estás a punto de cancelar tu solicitud de inscripción al curso:
                </p>

                <p class="mt-1 font-bold text-[#172A3D]">
                    {{ inscripcionACancelar.curso?.titulo ?? 'este curso' }}
                </p>

                <!-- MOTIVO -->
                <div class="mt-6">
                    <label class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider">
                        ¿Cuál es el motivo de la cancelación? <span class="text-red-500">*</span>
                    </label>

                    <div class="mt-3 max-h-56 overflow-y-auto space-y-2 pr-1">
                        <label v-for="motivo in motivosCancelacion" :key="motivo.value"
                            class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3 transition hover:border-orange-300 hover:bg-orange-50/50"
                            :class="motivoCancelacion === motivo.value
                                ? 'border-orange-500 bg-orange-50 ring-1 ring-orange-500 shadow-xs'
                                : ''">
                            <input v-model="motivoCancelacion" type="radio" name="motivo_cancelacion"
                                :value="motivo.value"
                                class="mt-0.5 h-4 w-4 border-slate-300 text-orange-600 focus:ring-orange-500" />
                            <span class="text-xs sm:text-sm font-medium text-slate-700">
                                {{ motivo.label }}
                            </span>
                        </label>
                    </div>

                    <p v-if="erroresCancelacion.motivo_cancelacion" class="mt-2 text-xs text-red-600 font-medium">
                        {{ erroresCancelacion.motivo_cancelacion }}
                    </p>
                </div>

                <!-- OBSERVACIÓN -->
                <div class="mt-5">
                    <label for="observacion_cancelacion"
                        class="block text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider">
                        Cuéntanos brevemente el motivo
                        <span v-if="motivoCancelacion === 'otro'" class="text-red-500">*</span>
                        <span v-else class="font-normal text-slate-400">(opcional)</span>
                    </label>

                    <textarea id="observacion_cancelacion" v-model="observacionCancelacion" rows="3" maxlength="1000"
                        placeholder="Puedes agregar una explicación adicional..."
                        class="mt-2 block w-full rounded-xl border-slate-300 text-xs sm:text-sm shadow-xs focus:border-orange-500 focus:ring-2 focus:ring-orange-100 outline-none"></textarea>

                    <div class="mt-1.5 flex justify-between text-xs text-slate-400 font-medium">
                        <span>
                            {{ motivoCancelacion === 'otro' ? 'La explicación es obligatoria.' : 'Información opcional.'
                            }}
                        </span>
                        <span>{{ observacionCancelacion.length }}/1000</span>
                    </div>

                    <p v-if="erroresCancelacion.observacion_cancelacion" class="mt-2 text-xs text-red-600 font-medium">
                        {{ erroresCancelacion.observacion_cancelacion }}
                    </p>
                </div>

                <!-- ACCIONES DE MODAL -->
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" :disabled="cancelando" @click="cerrarConfirmacionCancelacion"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 disabled:opacity-50 shadow-xs">
                        Volver
                    </button>

                    <button type="button" :disabled="cancelando || !puedeConfirmarCancelacion"
                        @click="cancelarInscripcion"
                        class="inline-flex items-center justify-center rounded-xl bg-red-600 px-5 py-2.5 text-xs sm:text-sm font-black text-white transition hover:bg-red-700 disabled:opacity-50 shadow-md">
                        <svg v-if="cancelando" class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                        {{ cancelando ? 'Cancelando...' : 'Confirmar cancelación' }}
                    </button>
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