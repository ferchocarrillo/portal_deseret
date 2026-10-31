<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    opiniones: Object,
    filtros: Object,
    conteos: Object,
});

const filtroTipo = ref(props.filtros.tipo || 'todos');
const filtroEstado = ref(props.filtros.estado || 'todos');

const aplicarFiltros = () => {
    router.get(
        route('admin.buzon.index'),
        { tipo: filtroTipo.value, estado: filtroEstado.value, page: 1 },
        { preserveState: true, replace: true }
    );
};

const toggleLeido = (id) => {
    router.patch(route('admin.buzon.toggle-leido', id), {}, { preserveScroll: true });
};

// Modal de lectura
const mensajeSeleccionado = ref(null);
const abrirLectura = (op) => {
    mensajeSeleccionado.value = op;
    if (!op.leido) {
        toggleLeido(op.id);
        op.leido = true;
    }
};

const badgeTipo = (tipo) => {
    switch (tipo) {
        case 'sugerencia': return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'queja': return 'bg-rose-100 text-rose-800 border-rose-200';
        case 'felicitacion': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
        default: return 'bg-slate-100 text-slate-700 border-slate-200';
    }
};
</script>

<template>

    <Head title="Buzón de Opiniones" />

    <AuthenticatedLayout>
        <div class="min-h-screen p-6 lg:p-8">
            <div class="mx-auto max-w-7xl space-y-6">

                <!-- ENCABEZADO -->
                <div>
                    <h1 class="text-2xl font-bold text-[#263646]">
                        Buzón de Opinión y Sugerencias
                    </h1>
                    <p class="mt-1 text-sm text-[#526174]">
                        Retroalimentación directa enviada por los usuarios para optimizar la comunidad.
                    </p>
                </div>

                <!-- CARDS DE CONTEO -->
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs">
                        <p class="text-xs font-semibold uppercase text-slate-400">Por Leer</p>
                        <p class="mt-1 text-2xl font-bold text-slate-800">{{ conteos.pendientes }}</p>
                    </div>
                    <div class="rounded-xl border border-amber-200 bg-amber-50/30 p-4 shadow-xs">
                        <p class="text-xs font-semibold uppercase text-amber-600">Sugerencias</p>
                        <p class="mt-1 text-2xl font-bold text-amber-900">{{ conteos.sugerencias }}</p>
                    </div>
                    <div class="rounded-xl border border-rose-200 bg-rose-50/30 p-4 shadow-xs">
                        <p class="text-xs font-semibold uppercase text-rose-600">Quejas</p>
                        <p class="mt-1 text-2xl font-bold text-rose-900">{{ conteos.quejas }}</p>
                    </div>
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/30 p-4 shadow-xs">
                        <p class="text-xs font-semibold uppercase text-emerald-600">Felicitaciones</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-900">{{ conteos.felicitaciones }}</p>
                    </div>
                </div>

                <!-- FILTROS -->
                <div class="flex flex-wrap items-center gap-3">
                    <select v-model="filtroTipo" @change="aplicarFiltros"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 outline-none">
                        <option value="todos">Todos los tipos</option>
                        <option value="sugerencia">Sugerencias</option>
                        <option value="queja">Quejas</option>
                        <option value="felicitacion">Felicitaciones</option>
                    </select>

                    <select v-model="filtroEstado" @change="aplicarFiltros"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 outline-none">
                        <option value="todos">Todos los estados</option>
                        <option value="no_leido">Sin leer</option>
                        <option value="leido">Leídos</option>
                    </select>
                </div>

                <!-- LISTADO -->
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm text-slate-600">
                            <thead
                                class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-5 py-3.5">Tipo</th>
                                    <th class="px-5 py-3.5">Módulo</th>
                                    <th class="px-5 py-3.5">Asunto</th>
                                    <th class="px-5 py-3.5">Usuario</th>
                                    <th class="px-5 py-3.5">Fecha</th>
                                    <th class="px-5 py-3.5 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="op in opiniones.data" :key="op.id"
                                    :class="['transition hover:bg-slate-50/80 cursor-pointer', !op.leido ? 'bg-amber-50/20 font-semibold text-slate-900' : '']"
                                    @click="abrirLectura(op)">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex rounded-md border px-2 py-0.5 text-[11px] font-bold capitalize"
                                            :class="badgeTipo(op.tipo)">
                                            {{ op.tipo }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">{{ op.modulo }}</td>
                                    <td class="px-5 py-4 max-w-xs truncate">{{ op.asunto }}</td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        {{ op.usuario?.name ?? 'Anónimo' }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-slate-400 text-xs">
                                        {{ new Date(op.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="px-5 py-4 text-right whitespace-nowrap" @click.stop>
                                        <button type="button" @click="toggleLeido(op.id)"
                                            class="text-xs font-bold text-slate-500 hover:text-slate-800 transition">
                                            {{ op.leido ? 'Marcar no leído' : '✓ Marcar leído' }}
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="opiniones.data.length === 0">
                                    <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                                        No hay mensajes registrados con estos filtros.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- MODAL DETALLE -->
        <div v-if="mensajeSeleccionado" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-xs" @click="mensajeSeleccionado = null"></div>
            <div class="relative z-10 w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="rounded-md border px-2 py-0.5 text-[11px] font-bold capitalize"
                            :class="badgeTipo(mensajeSeleccionado.tipo)">
                            {{ mensajeSeleccionado.tipo }}
                        </span>
                        <h2 class="text-base font-bold text-slate-900 mt-2">{{ mensajeSeleccionado.asunto }}</h2>
                        <p class="text-xs text-slate-400">Por {{ mensajeSeleccionado.usuario?.name }} • {{
                            mensajeSeleccionado.modulo }}</p>
                    </div>
                    <button @click="mensajeSeleccionado = null"
                        class="text-slate-400 hover:text-slate-700 text-xl font-bold">×</button>
                </div>

                <div
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4 text-xs sm:text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                    {{ mensajeSeleccionado.mensaje }}
                </div>

                <div class="flex justify-end pt-2">
                    <button type="button" @click="mensajeSeleccionado = null"
                        class="rounded-xl bg-[#263646] px-5 py-2 text-xs font-bold text-white hover:bg-[#1d2936] transition">
                        Entendido
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>