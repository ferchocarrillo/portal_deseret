<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref } from 'vue';
import Swal from 'sweetalert2';

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

const procesando = ref(false);
const errores = ref({});

function estadoLabel(estado) {
    switch (estado) {
        case 'activa':
            return 'Inscripción activa';

        case 'cancelada':
            return 'Cancelada por el participante';

        case 'rechazada':
            return 'Rechazada';

        case 'pendiente':
        default:
            return 'Solicitud pendiente';
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

function formatoFecha(fecha) {
    if (!fecha) {
        return '—';
    }

    return new Date(fecha).toLocaleDateString('es-CO', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
}

/*
|--------------------------------------------------------------------------
| REGISTRAR OFICIALMENTE
|--------------------------------------------------------------------------
*/

async function activarInscripcion() {
    if (procesando.value) {
        return;
    }

    if (props.inscripcion.estado !== 'pendiente') {
        return;
    }

    const resultado = await Swal.fire({
        icon: 'question',
        title: '¿Registrar inscripción?',
        text: `¿Deseas registrar oficialmente a ${props.inscripcion.nombre_inscrito} en este curso?`,
        showCancelButton: true,
        confirmButtonText: 'Sí, registrar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#D99A24',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
    });

    if (!resultado.isConfirmed) {
        return;
    }

    procesando.value = true;
    errores.value = {};

    router.patch(
        route('cursos.inscripciones.activar', {
            curso: props.curso.id,
            inscripcion: props.inscripcion.id,
        }),
        {},
        {
            preserveScroll: true,

            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: '¡Inscripción registrada!',
                    text: 'El participante ha sido registrado oficialmente en el curso.',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#D99A24',
                });
            },

            onError: (errors) => {
                errores.value = errors ?? {};

                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo registrar',
                    text:
                        errors?.error ??
                        'No fue posible registrar oficialmente la inscripción.',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#D99A24',
                });
            },

            onFinish: () => {
                procesando.value = false;
            },
        }
    );
}

/*
|--------------------------------------------------------------------------
| RECHAZAR INSCRIPCIÓN
|--------------------------------------------------------------------------
*/

async function rechazarInscripcion() {
    if (procesando.value) {
        return;
    }

    if (props.inscripcion.estado !== 'pendiente') {
        return;
    }

    const resultado = await Swal.fire({
        icon: 'warning',
        title: '¿Rechazar inscripción?',
        text: `Indica por qué no se acepta la solicitud de ${props.inscripcion.nombre_inscrito}.`,
        input: 'textarea',
        inputPlaceholder: 'Escribe brevemente el motivo del rechazo...',
        inputAttributes: {
            maxlength: 1000,
            'aria-label': 'Motivo del rechazo',
        },
        showCancelButton: true,
        confirmButtonText: 'Sí, rechazar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,

        inputValidator: (value) => {
            if (!value || !value.trim()) {
                return 'Debes indicar el motivo del rechazo.';
            }

            return undefined;
        },
    });

    if (!resultado.isConfirmed) {
        return;
    }

    procesando.value = true;
    errores.value = {};

    router.patch(
        route('cursos.inscripciones.rechazar', {
            curso: props.curso.id,
            inscripcion: props.inscripcion.id,
        }),
        {
            observacion_rechazo: resultado.value.trim(),
        },
        {
            preserveScroll: true,

            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Inscripción rechazada',
                    text: 'La solicitud ha sido rechazada correctamente.',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#D99A24',
                });
            },

            onError: (errors) => {
                errores.value = errors ?? {};

                Swal.fire({
                    icon: 'error',
                    title: 'No se pudo rechazar',
                    text:
                        errors?.observacion_rechazo ??
                        errors?.error ??
                        'No fue posible rechazar la inscripción.',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#D99A24',
                });
            },

            onFinish: () => {
                procesando.value = false;
            },
        }
    );
}
</script>

<template>

    <Head :title="`Inscripción - ${inscripcion.nombre_inscrito} | Portal Deseret`" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ENCABEZADO TRANSLÚCIDO CON IDENTIDAD NARANJA -->
            <section
                class="border-b border-orange-200/80 bg-orange-100/25 backdrop-blur-md shadow-lg shadow-orange-500/10">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-orange-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-orange-950">
                                    Gestión de inscripciones
                                </span>
                            </div>

                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Detalle de inscripción
                            </h1>

                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Administra la solicitud y revisa la información del participante.
                            </p>
                        </div>

                        <Link :href="route('cursos.inscripciones.index', curso.id)"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition hover:bg-orange-50 hover:border-orange-300">
                            ← Volver a inscripciones
                        </Link>
                    </div>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL REORGANIZADO -->
            <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 space-y-6">

                <!-- 1. BLOQUE PRINCIPAL: ESTADO DE LA SOLICITUD Y ACCIONES -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                    <div class="flex flex-col gap-5 p-6 sm:p-7 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Estado de la solicitud
                            </p>

                            <div class="mt-2 flex items-center gap-3">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs sm:text-sm font-bold shadow-xs"
                                    :class="estadoClasses(inscripcion.estado)">
                                    <span class="h-2 w-2 rounded-full bg-current"></span>
                                    {{ estadoLabel(inscripcion.estado) }}
                                </span>
                            </div>
                        </div>

                        <!-- ACCIONES DE GESTIÓN (SI ESTÁ PENDIENTE) -->
                        <div v-if="inscripcion.estado === 'pendiente'" class="flex flex-col gap-2.5 sm:flex-row">
                            <button type="button" :disabled="procesando" @click="activarInscripcion"
                                class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-xs sm:text-sm font-black text-white shadow-md transition hover:bg-emerald-700 disabled:opacity-50">
                                <span v-if="!procesando">✓ Registrar oficialmente</span>
                                <span v-else>Procesando...</span>
                            </button>

                            <button type="button" :disabled="procesando" @click="rechazarInscripcion"
                                class="inline-flex items-center justify-center rounded-xl border border-red-300 bg-red-50 px-5 py-3 text-xs sm:text-sm font-bold text-red-700 transition hover:bg-red-100 disabled:opacity-50 shadow-xs">
                                Rechazar inscripción
                            </button>
                        </div>
                    </div>
                </section>

                <!-- 2. DATOS DEL PARTICIPANTE (PROTAGONISTA PRINCIPAL) -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                    <div class="border-b border-slate-100 bg-orange-50/40 px-6 py-5">
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                            Información del participante
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500 font-medium">
                            Datos registrados al momento de solicitar la inscripción.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 p-6 sm:p-7 md:grid-cols-2">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Nombre completo
                            </p>
                            <p class="mt-1 text-sm sm:text-base font-bold text-slate-800">
                                {{ inscripcion.nombre_inscrito }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Correo electrónico
                            </p>
                            <p class="mt-1 break-all text-xs sm:text-sm font-bold text-slate-800">
                                {{ inscripcion.email_inscrito }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Teléfono
                            </p>
                            <p class="mt-1 text-sm sm:text-base font-bold text-slate-800">
                                {{ inscripcion.telefono_inscrito || 'No registrado' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Fecha de solicitud
                            </p>
                            <p class="mt-1 text-sm sm:text-base font-bold text-slate-800">
                                {{ formatoFecha(inscripcion.created_at) }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- 3. INFORMACIÓN DEL CURSO -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                    <div class="p-6 sm:p-7">
                        <p class="text-xs font-bold uppercase tracking-wider text-orange-600">
                            Curso asociado
                        </p>

                        <div class="mt-2 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-xl sm:text-2xl font-black text-[#172A3D]">
                                    {{ curso.titulo || curso.nombre }}
                                </h2>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span v-if="curso.modalidad"
                                        class="rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-xs font-bold text-slate-700">
                                        {{ curso.modalidad }}
                                    </span>

                                    <span v-if="curso.fecha_inicio"
                                        class="rounded-full bg-slate-100 border border-slate-200 px-3 py-1 text-xs font-bold text-slate-700">
                                        Inicio: {{ formatoFecha(curso.fecha_inicio) }}
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

                <!-- 4. INFORMACIÓN DE LA GESTIÓN (SI APLICA) -->
                <section v-if="inscripcion.estado === 'cancelada' || inscripcion.estado === 'rechazada'"
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                    <div class="border-b border-slate-100 px-6 py-5">
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                            Información de la gestión
                        </h2>
                    </div>

                    <div class="space-y-5 p-6 sm:p-7">
                        <div v-if="inscripcion.motivo_cancelacion">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Motivo de cancelación
                            </p>
                            <p class="mt-1 text-xs sm:text-sm font-bold text-slate-700">
                                {{ inscripcion.motivo_cancelacion }}
                            </p>
                        </div>

                        <div v-if="inscripcion.observacion_cancelacion">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Observación
                            </p>
                            <p class="mt-1 whitespace-pre-line text-xs sm:text-sm text-slate-700 font-medium">
                                {{ inscripcion.observacion_cancelacion }}
                            </p>
                        </div>

                        <div v-if="inscripcion.cancelada_at">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Fecha de cancelación
                            </p>
                            <p class="mt-1 text-xs sm:text-sm font-bold text-slate-700">
                                {{ formatoFecha(inscripcion.cancelada_at) }}
                            </p>
                        </div>

                        <div v-if="inscripcion.observacion_rechazo">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Motivo del rechazo
                            </p>
                            <p class="mt-1 whitespace-pre-line text-xs sm:text-sm text-slate-700 font-medium">
                                {{ inscripcion.observacion_rechazo }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- 5. SEGUIMIENTO (LÍNEA DE TIEMPO / REGISTRO DE FECHAS) -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                    <div class="border-b border-slate-100 px-6 py-5">
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                            Seguimiento
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 gap-6 p-6 sm:p-7 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Solicitud recibida
                            </p>
                            <p class="mt-1 text-xs sm:text-sm font-bold text-slate-700">
                                {{ formatoFecha(inscripcion.created_at) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                Activación
                            </p>
                            <p class="mt-1 text-xs sm:text-sm font-bold text-slate-700">
                                {{ formatoFecha(inscripcion.activado_at) }}
                            </p>
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