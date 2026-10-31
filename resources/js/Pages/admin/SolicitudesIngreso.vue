<script setup>
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

// ============================================================
// PROPS
// ============================================================
const props = defineProps({
    solicitudes: {
        type: Array,
        default: () => [],
    },
    pendientes: {
        type: Number,
        default: 0,
    },
    aprobadas: {
        type: Number,
        default: 0,
    },
    rechazadas: {
        type: Number,
        default: 0,
    },
    total: {
        type: Number,
        default: 0,
    },
});

// ============================================================
// FILTROS
// ============================================================
const busqueda = ref('');
const filtroEstado = ref('todas');

// ============================================================
// SOLICITUD SELECCIONADA Y MODAL
// ============================================================
const solicitudSeleccionada = ref(null);
const modalAbierto = ref(false);

// ============================================================
// EVALUACIÓN ADMINISTRATIVA
// ============================================================
const decision = ref('');
const observacionRevision = ref('');
const guardandoDecision = ref(false);

const verSolicitud = (solicitud) => {
    solicitudSeleccionada.value = solicitud;
    decision.value = '';
    observacionRevision.value = '';
    modalAbierto.value = true;
};

const cerrarModal = () => {
    modalAbierto.value = false;
    solicitudSeleccionada.value = null;
    decision.value = '';
    observacionRevision.value = '';
    guardandoDecision.value = false;
};

// ============================================================
// SOLICITUDES FILTRADAS
// ============================================================
const solicitudesFiltradas = computed(() => {
    let resultado = [...props.solicitudes];

    if (filtroEstado.value !== 'todas') {
        resultado = resultado.filter(
            solicitud => solicitud.estado === filtroEstado.value
        );
    }

    const texto = busqueda.value.trim().toLowerCase();
    if (texto !== '') {
        resultado = resultado.filter(solicitud => {
            const nombre = `${solicitud.nombres ?? ''} ${solicitud.apellidos ?? ''}`.toLowerCase();
            const documento = `${solicitud.tipo_documento ?? ''} ${solicitud.documento ?? ''}`.toLowerCase();
            const email = (solicitud.email ?? '').toLowerCase();
            const ciudad = (solicitud.ciudad ?? '').toLowerCase();

            return (
                nombre.includes(texto) ||
                documento.includes(texto) ||
                email.includes(texto) ||
                ciudad.includes(texto)
            );
        });
    }

    return resultado;
});

const formatearFecha = (fecha) => {
    if (!fecha) return '—';
    const fechaObj = new Date(fecha);
    if (Number.isNaN(fechaObj.getTime())) return fecha;

    return fechaObj.toLocaleDateString('es-CO', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    });
};

const nombreEstado = (estado) => {
    switch (estado) {
        case 'pendiente': return 'Pendiente';
        case 'aprobada': return 'Aprobada';
        case 'rechazada': return 'Rechazada';
        default: return 'Sin estado';
    }
};

const claseEstado = (estado) => {
    switch (estado) {
        case 'pendiente': return 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20';
        case 'aprobada': return 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20';
        case 'rechazada': return 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20';
        default: return 'bg-gray-50 text-gray-600 ring-1 ring-inset ring-gray-500/20';
    }
};

const tipoSolicitante = (solicitud) => {
    return solicitud.es_miembro ? 'Miembro' : 'No miembro';
};

const guardarDecision = () => {
    if (!solicitudSeleccionada.value || !decision.value) return;

    guardandoDecision.value = true;

    router.post(
        `/admin/solicitudes/${solicitudSeleccionada.value.id}/evaluar`,
        {
            decision: decision.value,
            observacion_revision: observacionRevision.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => cerrarModal(),
            onError: () => { guardandoDecision.value = false; },
            onFinish: () => { guardandoDecision.value = false; },
        }
    );
};
</script>

<template>
    <AuthenticatedLayout>

        <Head title="Solicitudes de ingreso" />

        <!-- CONTENEDOR CON LA CLASE DEL FONDO CARBONCILLO DIRECTA -->
        <div class="min-h-screen fondo-carboncillo bg-cover bg-center bg-no-repeat bg-fixed py-8 ">
            <main class="mx-auto max-w-7xl px-6 lg:px-8 bg-[#0D542B]/50 p-3 rounded-xl">

                <!-- ENCABEZADO -->
                <div class="mb-8">
                    <div class="mb-2 flex items-center gap-2 text-sm text-gray-500">
                        <span>Administración</span>
                        <span>/</span>
                        <span class="font-medium text-gray-700">Solicitudes de ingreso</span>
                    </div>

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between text-[#460809]-70 bg-white/50 rounded-xl p-3">
                        <div class="">
                            <h1 class="text-2xl font-semibold tracking-tight">
                                Solicitudes de ingreso
                            </h1>
                            <p class="mt-1 text-sm">
                                Revisa y gestiona las solicitudes recibidas.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- TARJETAS DE RESUMEN (con fondos blancos/translúcidos legibles) -->
                <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <button type="button" @click="filtroEstado = 'todas'"
                        class="rounded-xl border border-gray-200/80 bg-white/95 p-5 text-left shadow-sm backdrop-blur-xs transition hover:border-gray-300 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total</p>
                                <p class="mt-2 text-2xl font-semibold text-gray-900">{{ props.total }}</p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 text-lg">📋
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Solicitudes recibidas</p>
                    </button>

                    <button type="button" @click="filtroEstado = 'pendiente'"
                        class="rounded-xl border border-gray-200/80 bg-white/95 p-5 text-left shadow-sm backdrop-blur-xs transition hover:border-amber-200 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Pendientes</p>
                                <p class="mt-2 text-2xl font-semibold text-gray-900">{{ props.pendientes }}</p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-lg">🟡
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Requieren revisión</p>
                    </button>

                    <button type="button" @click="filtroEstado = 'aprobada'"
                        class="rounded-xl border border-gray-200/80 bg-white/95 p-5 text-left shadow-sm backdrop-blur-xs transition hover:border-emerald-200 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Aprobadas</p>
                                <p class="mt-2 text-2xl font-semibold text-gray-900">{{ props.aprobadas }}</p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-lg">🟢
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Solicitudes aprobadas</p>
                    </button>

                    <button type="button" @click="filtroEstado = 'rechazada'"
                        class="rounded-xl border border-gray-200/80 bg-white/95 p-5 text-left shadow-sm backdrop-blur-xs transition hover:border-red-200 hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Rechazadas</p>
                                <p class="mt-2 text-2xl font-semibold text-gray-900">{{ props.rechazadas }}</p>
                            </div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-lg">🔴
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Solicitudes rechazadas</p>
                    </button>
                </div>

                <!-- PANEL DE SOLICITUDES -->
                <section
                    class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white/95 shadow-sm backdrop-blur-xs">
                    <div class="border-b border-gray-200 px-6 py-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h2 class="text-base font-semibold text-gray-900">Solicitudes</h2>
                                <p class="mt-1 text-sm text-gray-500">Lista de solicitudes recibidas.</p>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row">
                                <div class="relative">
                                    <span
                                        class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">🔎</span>
                                    <input v-model="busqueda" type="text" placeholder="Buscar..."
                                        class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-500 focus:ring-1 focus:ring-gray-500 sm:w-64">
                                </div>

                                <select v-model="filtroEstado"
                                    class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 outline-none transition focus:border-gray-500 focus:ring-1 focus:ring-gray-500">
                                    <option value="todas">Todos los estados</option>
                                    <option value="pendiente">Pendientes</option>
                                    <option value="aprobada">Aprobadas</option>
                                    <option value="rechazada">Rechazadas</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50/80">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Solicitante</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Documento</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Tipo</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Ubicación</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Fecha</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Estado</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Acción</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 bg-white">
                                <tr v-for="solicitud in solicitudesFiltradas" :key="solicitud.id"
                                    class="transition hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="whitespace-nowrap">
                                            <div class="font-medium text-gray-900">{{ solicitud.nombres }} {{
                                                solicitud.apellidos }}</div>
                                            <div class="mt-0.5 text-xs text-gray-500">{{ solicitud.email }}</div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm text-gray-700">{{ solicitud.tipo_documento }} {{
                                            solicitud.documento }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="text-sm text-gray-700">{{ tipoSolicitante(solicitud) }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <div class="text-sm text-gray-700">{{ solicitud.ciudad }}</div>
                                        <div class="text-xs text-gray-500">{{ solicitud.region }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="text-sm text-gray-500">{{ formatearFecha(solicitud.created_at)
                                            }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                            :class="claseEstado(solicitud.estado)">
                                            {{ nombreEstado(solicitud.estado) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <button type="button" @click="verSolicitud(solicitud)"
                                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 hover:text-gray-900">
                                            <span class="mr-2">👁</span>
                                            Ver solicitud
                                        </button>
                                    </td>
                                </tr>

                                <tr v-if="solicitudesFiltradas.length === 0">
                                    <td colspan="7" class="px-6 py-16 text-center">
                                        <div class="mx-auto max-w-sm">
                                            <div class="text-4xl">📋</div>
                                            <h3 class="mt-4 text-sm font-semibold text-gray-900">No hay solicitudes</h3>
                                            <p class="mt-1 text-sm text-gray-500">No encontramos solicitudes que
                                                coincidan con los filtros seleccionados.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-gray-200 bg-gray-50/80 px-6 py-4">
                        <p class="text-sm text-gray-500">
                            Mostrando <span class="font-medium text-gray-700">{{ solicitudesFiltradas.length }}</span>
                            de <span class="font-medium text-gray-700">{{ props.total }}</span> solicitudes.
                        </p>
                    </div>
                </section>
            </main>
        </div>

        <!-- MODAL DETALLE DE SOLICITUD -->
        <div v-if="modalAbierto && solicitudSeleccionada"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="cerrarModal"></div>

            <div
                class="relative z-10 flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                <!-- CABECERA MODAL -->
                <div class="flex shrink-0 items-center justify-between border-b border-gray-200 px-6 py-5">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-semibold text-gray-900">Detalle de la solicitud</h2>
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                :class="claseEstado(solicitudSeleccionada.estado)">
                                {{ nombreEstado(solicitudSeleccionada.estado) }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Solicitud #{{ solicitudSeleccionada.id }}</p>
                    </div>

                    <button type="button" @click="cerrarModal"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-xl text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
                        aria-label="Cerrar">×</button>
                </div>

                <!-- CONTENIDO MODAL -->
                <div class="flex-1 overflow-y-auto px-6 py-6">
                    <!-- DATOS PERSONALES -->
                    <section>
                        <div class="mb-4">
                            <h3 class="text-base font-semibold text-gray-900">Datos personales</h3>
                            <p class="mt-1 text-sm text-gray-500">Información proporcionada por el solicitante.</p>
                        </div>

                        <div
                            class="grid gap-5 rounded-xl border border-gray-200 bg-gray-50 p-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Nombres</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ solicitudSeleccionada.nombres ||
                                    '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Apellidos</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ solicitudSeleccionada.apellidos ||
                                    '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Documento</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{
                                    solicitudSeleccionada.tipo_documento }} {{ solicitudSeleccionada.documento }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Condición</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{
                                    tipoSolicitante(solicitudSeleccionada) }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Fecha de solicitud
                                </p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{
                                    formatearFecha(solicitudSeleccionada.created_at) }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- CONTACTO -->
                    <section class="mt-8">
                        <div class="mb-4">
                            <h3 class="text-base font-semibold text-gray-900">Información de contacto</h3>
                        </div>
                        <div class="grid gap-5 rounded-xl border border-gray-200 bg-white p-5 sm:grid-cols-2">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Teléfono</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ solicitudSeleccionada.telefono ||
                                    '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Correo electrónico
                                </p>
                                <p class="mt-1 break-all text-sm font-medium text-gray-900">{{
                                    solicitudSeleccionada.email || '—' }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- UBICACIÓN -->
                    <section class="mt-8">
                        <div class="mb-4">
                            <h3 class="text-base font-semibold text-gray-900">Ubicación</h3>
                        </div>
                        <div class="grid gap-5 rounded-xl border border-gray-200 bg-gray-50 p-5 sm:grid-cols-3">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">País</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ solicitudSeleccionada.pais || '—'
                                    }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Región</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ solicitudSeleccionada.region || '—'
                                    }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Ciudad</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ solicitudSeleccionada.ciudad || '—'
                                    }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- MIEMBRO / NO MIEMBRO -->
                    <section v-if="solicitudSeleccionada.es_miembro" class="mt-8">
                        <div class="mb-4">
                            <h3 class="text-base font-semibold text-gray-900">Información como miembro</h3>
                        </div>
                        <div
                            class="grid gap-5 rounded-xl border border-gray-200 bg-gray-50 p-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Estaca</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ solicitudSeleccionada.estaca || '—'
                                    }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Barrio</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ solicitudSeleccionada.barrio || '—'
                                    }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Obispo</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ solicitudSeleccionada.nombre_obispo
                                    || '—' }}</p>
                            </div>
                        </div>
                    </section>

                    <section v-else class="mt-8">
                        <div class="mb-4">
                            <h3 class="text-base font-semibold text-gray-900">Información de ingreso</h3>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Motivo de ingreso</p>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700">{{
                                solicitudSeleccionada.motivo_ingreso || '—' }}</p>
                        </div>
                    </section>

                    <!-- AVAL -->
                    <section v-if="!solicitudSeleccionada.es_miembro && solicitudSeleccionada.tiene_aval" class="mt-8">
                        <div class="mb-4">
                            <h3 class="text-base font-semibold text-gray-900">Información del aval</h3>
                        </div>
                        <div
                            class="grid gap-5 rounded-xl border border-gray-200 bg-gray-50 p-5 sm:grid-cols-2 lg:grid-cols-3">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Nombres</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ solicitudSeleccionada.aval_nombres
                                    || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Apellidos</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{
                                    solicitudSeleccionada.aval_apellidos || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Correo electrónico
                                </p>
                                <p class="mt-1 break-all text-sm font-medium text-gray-900">{{
                                    solicitudSeleccionada.aval_email || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Teléfono</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ solicitudSeleccionada.aval_telefono
                                    || '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Barrio</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ solicitudSeleccionada.aval_barrio
                                    || '—' }}</p>
                            </div>
                        </div>
                    </section>

                    <!-- EVALUACIÓN -->
                    <section v-if="solicitudSeleccionada.estado === 'pendiente'" class="mt-8">
                        <div class="mb-4">
                            <h3 class="text-base font-semibold text-gray-900">Evaluación de la solicitud</h3>
                            <p class="mt-1 text-sm text-gray-500">Selecciona la decisión correspondiente después de
                                revisar la información.</p>
                        </div>

                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Decisión</label>
                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 transition hover:border-emerald-300 hover:bg-emerald-50">
                                        <input v-model="decision" type="radio" value="aprobada"
                                            class="mt-1 h-4 w-4 border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Aprobar solicitud</p>
                                            <p class="mt-1 text-xs leading-5 text-gray-500">La solicitud cumple con los
                                                requisitos y puede ser aprobada.</p>
                                        </div>
                                    </label>

                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 transition hover:border-red-300 hover:bg-red-50">
                                        <input v-model="decision" type="radio" value="rechazada"
                                            class="mt-1 h-4 w-4 border-gray-300 text-red-600 focus:ring-red-500">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Rechazar solicitud</p>
                                            <p class="mt-1 text-xs leading-5 text-gray-500">La solicitud no cumple con
                                                los requisitos para ser aprobada.</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="mt-5">
                                <label for="observacion_revision" class="text-sm font-medium text-gray-700">Observación
                                    de la revisión</label>
                                <textarea id="observacion_revision" v-model="observacionRevision" rows="4"
                                    maxlength="1000" placeholder="Escribe una observación sobre la decisión..."
                                    class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-500 focus:ring-1 focus:ring-gray-500"></textarea>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- PIE DEL MODAL -->
                <div
                    class="flex shrink-0 items-center justify-between gap-4 border-t border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="text-sm text-gray-500">
                        Solicitud recibida el <span class="font-medium text-gray-700">{{
                            formatearFecha(solicitudSeleccionada.created_at) }}</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="cerrarModal"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-100">
                            Cerrar
                        </button>

                        <button v-if="solicitudSeleccionada.estado === 'pendiente'" type="button"
                            @click="guardarDecision" :disabled="!decision || guardandoDecision"
                            class="rounded-lg px-5 py-2.5 text-sm font-semibold text-white transition disabled:cursor-not-allowed disabled:opacity-50"
                            :class="decision === 'aprobada' ? 'bg-emerald-600 hover:bg-emerald-700' : decision === 'rechazada' ? 'bg-red-600 hover:bg-red-700' : 'bg-gray-400'">
                            <span v-if="guardandoDecision">Guardando...</span>
                            <span v-else-if="decision === 'aprobada'">Aprobar solicitud</span>
                            <span v-else-if="decision === 'rechazada'">Rechazar solicitud</span>
                            <span v-else>Selecciona una decisión</span>
                        </button>
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