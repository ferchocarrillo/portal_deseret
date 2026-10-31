<script setup>
import { ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    reportes: {
        type: Object,
        required: true,
    },
    filtros: {
        type: Object,
        default: () => ({ estado: 'todos', modulo: 'todos', severidad: 'todos', search: '' }),
    },
    conteos: {
        type: Object,
        default: () => ({ abiertos: 0, en_revision: 0, resueltos: 0, total: 0 }),
    },
});

const filtroEstado = ref(props.filtros.estado || 'todos');
const filtroModulo = ref(props.filtros.modulo || 'todos');
const filtroSeveridad = ref(props.filtros.severidad || 'todos');
const busqueda = ref(props.filtros.search || '');

const aplicarFiltros = () => {
    router.get(
        route('admin.reportes-errores.index'),
        {
            estado: filtroEstado.value,
            modulo: filtroModulo.value,
            severidad: filtroSeveridad.value,
            search: busqueda.value,
            page: 1,
        },
        { preserveState: true, replace: true }
    );
};

// Modal de gestión
const reporteSeleccionado = ref(null);
const modalAbierto = ref(false);

const formGestion = useForm({
    estado: 'abierto',
    notas_solucion: '',
});

const abrirModal = (rep) => {
    reporteSeleccionado.value = rep;
    formGestion.estado = rep.estado;
    formGestion.notas_solucion = rep.notas_solucion || '';
    modalAbierto.value = true;
};

const cerrarModal = () => {
    modalAbierto.value = false;
    reporteSeleccionado.value = null;
};

const guardarEstado = () => {
    if (!reporteSeleccionado.value) return;

    formGestion.patch(route('admin.reportes-errores.estado', reporteSeleccionado.value.id), {
        preserveScroll: true,
        onSuccess: () => cerrarModal(),
    });
};

const badgeSeveridad = (sev) => {
    switch (sev) {
        case 'critica': return 'bg-rose-100 text-rose-800 border-rose-200';
        case 'alta': return 'bg-orange-100 text-orange-800 border-orange-200';
        case 'media': return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'baja': return 'bg-slate-100 text-slate-700 border-slate-200';
        default: return 'bg-slate-100 text-slate-700';
    }
};

const badgeEstado = (est) => {
    switch (est) {
        case 'abierto': return 'bg-rose-50 text-rose-700 border border-rose-200';
        case 'en_revision': return 'bg-amber-50 text-amber-700 border border-amber-200';
        case 'resuelto': return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
        case 'descartado': return 'bg-slate-100 text-slate-600 border border-slate-200';
        default: return 'bg-slate-50 text-slate-600';
    }
};
</script>

<template>

    <Head title="Gestión de Reportes de Errores" />

    <AuthenticatedLayout>
        <div class="min-h-screen p-6 lg:p-8">
            <div class="mx-auto max-w-7xl space-y-6 bg-[#7CCF35] rounded-xl border border-slate-200 p-3">

                <!-- ENCABEZADO -->
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-[#263646]">
                            Incidencias y Reportes de Errores
                        </h1>
                        <p class="mt-1 text-sm text-[#526174]">
                            Panel de seguimiento técnico de problemas reportados por los usuarios en toda la plataforma.
                        </p>
                    </div>
                </div>

                <!-- CARDS DE CONTEO -->
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
                        <p class="text-xs font-semibold uppercase text-slate-400">Total Casos</p>
                        <p class="mt-1 text-2xl font-bold text-slate-800">{{ conteos.total }}</p>
                    </div>
                    <div class="rounded-xl border border-rose-200 bg-rose-50/40 p-4 shadow-xs">
                        <p class="text-xs font-semibold uppercase text-rose-600">Abiertos</p>
                        <p class="mt-1 text-2xl font-bold text-rose-900">{{ conteos.abiertos }}</p>
                    </div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50/40 p-4 shadow-xs">
                        <p class="text-xs font-semibold uppercase text-amber-600">En Revisión</p>
                        <p class="mt-1 text-2xl font-bold text-amber-900">{{ conteos.en_revision }}</p>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/40 p-4 shadow-xs">
                        <p class="text-xs font-semibold uppercase text-emerald-600">Resueltos</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-900">{{ conteos.resueltos }}</p>
                    </div>
                </div>

                <!-- FILTROS -->
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <input v-model="busqueda" @keyup.enter="aplicarFiltros" type="text"
                            placeholder="Buscar por código (BUG-...), título o descripción..."
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-slate-400" />
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <select v-model="filtroEstado" @change="aplicarFiltros"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 outline-none">
                            <option value="todos">Todos los estados</option>
                            <option value="abierto">Abiertos</option>
                            <option value="en_revision">En Revisión</option>
                            <option value="resuelto">Resueltos</option>
                            <option value="descartado">Descartados</option>
                        </select>

                        <select v-model="filtroSeveridad" @change="aplicarFiltros"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 outline-none">
                            <option value="todos">Todas las severidades</option>
                            <option value="critica">Crítica</option>
                            <option value="alta">Alta</option>
                            <option value="media">Media</option>
                            <option value="baja">Baja</option>
                        </select>

                        <button type="button" @click="aplicarFiltros"
                            class="rounded-xl bg-[#263646] px-4 py-2 text-xs sm:text-sm font-bold text-white shadow-xs hover:bg-[#1d2936] transition">
                            Filtrar
                        </button>
                    </div>
                </div>

                <!-- TABLA -->
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm text-slate-600">
                            <thead
                                class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-5 py-3.5">Ticket</th>
                                    <th class="px-5 py-3.5">Módulo</th>
                                    <th class="px-5 py-3.5">Título</th>
                                    <th class="px-5 py-3.5">Severidad</th>
                                    <th class="px-5 py-3.5">Reportado por</th>
                                    <th class="px-5 py-3.5">Estado</th>
                                    <th class="px-5 py-3.5 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="rep in reportes.data" :key="rep.id" class="hover:bg-slate-50/60 transition">
                                    <td class="px-5 py-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                        {{ rep.codigo_ticket }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap font-medium text-slate-700">
                                        {{ rep.modulo }}
                                    </td>
                                    <td class="px-5 py-4 max-w-xs truncate font-semibold text-slate-800"
                                        :title="rep.titulo">
                                        {{ rep.titulo }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex rounded-md border px-2 py-0.5 text-[11px] font-bold capitalize"
                                            :class="badgeSeveridad(rep.severidad)">
                                            {{ rep.severidad }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-800">{{ rep.usuario?.name ?? 'Anónimo' }}</div>
                                        <div class="text-[11px] text-slate-400">{{ rep.usuario?.email }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-bold capitalize"
                                            :class="badgeEstado(rep.estado)">
                                            {{ rep.estado.replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <button type="button" @click="abrirModal(rep)"
                                            class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-[#263646] hover:text-white transition">
                                            Gestionar
                                        </button>
                                    </td>
                                </tr>

                                <tr v-if="reportes.data.length === 0">
                                    <td colspan="7" class="px-5 py-12 text-center text-slate-400 font-medium">
                                        No hay reportes de incidencias con los criterios aplicados.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- MODAL DETALLE Y RESOLUCIÓN -->
        <div v-if="modalAbierto && reporteSeleccionado" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" @click="cerrarModal"></div>

            <div
                class="relative z-10 flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-900">
                                Incidencia {{ reporteSeleccionado.codigo_ticket }}
                            </h2>
                            <span class="rounded-md border px-2 py-0.5 text-[11px] font-bold capitalize"
                                :class="badgeSeveridad(reporteSeleccionado.severidad)">
                                {{ reporteSeleccionado.severidad }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">Módulo: {{ reporteSeleccionado.modulo }}</p>
                    </div>
                    <button type="button" @click="cerrarModal"
                        class="text-slate-400 hover:text-slate-700 text-xl font-bold">×</button>
                </div>

                <div class="flex-1 overflow-y-auto p-6 space-y-4 text-xs sm:text-sm">
                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase">Título</span>
                        <p class="font-bold text-slate-800 text-sm mt-0.5">{{ reporteSeleccionado.titulo }}</p>
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase">Descripción detallada</span>
                        <p
                            class="text-slate-700 mt-1 whitespace-pre-line bg-slate-50 p-3 rounded-xl border border-slate-100">
                            {{ reporteSeleccionado.descripcion }}
                        </p>
                    </div>

                    <div v-if="reporteSeleccionado.pasos_reproducir">
                        <span class="block text-xs font-bold text-slate-400 uppercase">Pasos para reproducir</span>
                        <p
                            class="text-slate-700 mt-1 whitespace-pre-line bg-slate-50 p-3 rounded-xl border border-slate-100">
                            {{ reporteSeleccionado.pasos_reproducir }}
                        </p>
                    </div>

                    <div v-if="reporteSeleccionado.url_afectada">
                        <span class="block text-xs font-bold text-slate-400 uppercase">URL reportada</span>
                        <a :href="reporteSeleccionado.url_afectada" target="_blank"
                            class="text-blue-600 hover:underline break-all mt-0.5 inline-block">
                            {{ reporteSeleccionado.url_afectada }} ↗
                        </a>
                    </div>

                    <div v-if="reporteSeleccionado.captura_path">
                        <span class="block text-xs font-bold text-slate-400 uppercase mb-1">Captura adjunta</span>
                        <a :href="`/storage/${reporteSeleccionado.captura_path}`" target="_blank">
                            <img :src="`/storage/${reporteSeleccionado.captura_path}`" alt="Captura del error"
                                class="max-h-48 rounded-xl border border-slate-200 object-contain hover:opacity-90 transition" />
                        </a>
                    </div>

                    <div class="border-t border-slate-200 pt-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Estado del Ticket
                        </label>
                        <select v-model="formGestion.estado"
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-slate-800 outline-none">
                            <option value="abierto">Abierto</option>
                            <option value="en_revision">En Revisión</option>
                            <option value="resuelto">Resuelto</option>
                            <option value="descartado">Descartado</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                            Notas internas o solución técnica
                        </label>
                        <textarea v-model="formGestion.notas_solucion" rows="3"
                            placeholder="Anota la causa raíz, commit o solución aplicada..."
                            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-800 outline-none"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                    <button type="button" @click="cerrarModal"
                        class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                        Cerrar
                    </button>
                    <button type="button" @click="guardarEstado" :disabled="formGestion.processing"
                        class="rounded-xl bg-[#263646] px-5 py-2 text-xs font-bold text-white shadow-xs hover:bg-[#1d2936] transition disabled:opacity-50">
                        {{ formGestion.processing ? 'Guardando...' : 'Guardar Estado' }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>