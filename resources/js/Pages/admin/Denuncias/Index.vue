<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    denuncias: {
        type: Object,
        default: () => ({ data: [] }),
    },
    filtros: {
        type: Object,
        default: () => ({}),
    },
})

const estadoSeleccionado = ref(props.filtros.estado || '')
const moduloSeleccionado = ref(props.filtros.modulo || '')

const filtrar = () => {
    router.get(
        route('admin.denuncias.index'),
        {
            estado: estadoSeleccionado.value,
            modulo: moduloSeleccionado.value,
        },
        { preserveState: true, preserveScroll: true, replace: true }
    )
}

const obtenerColorEstado = (estado) => {
    switch (estado) {
        case 'pendiente': return 'bg-amber-100 text-amber-800 border-amber-200'
        case 'en_revision': return 'bg-blue-100 text-blue-800 border-blue-200'
        case 'resuelto': return 'bg-emerald-100 text-emerald-800 border-emerald-200'
        case 'descartado': return 'bg-slate-100 text-slate-700 border-slate-200'
        default: return 'bg-slate-100 text-slate-800 border-slate-200'
    }
}
</script>

<template>

    <Head title="Gestión de Incidencias | Panel Administrativo" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">
            <section class="border-b border-slate-200/80 bg-white/60 backdrop-blur-md shadow-sm">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                        Listado de Incidencias
                    </h1>
                    <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                        Monitorea y da seguimiento a las incidencias reportadas por la comunidad.
                    </p>
                </div>
            </section>

            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-6">
                <div class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-xl shadow-slate-500/10">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-600">
                                    <th class="px-6 py-4">Código</th>
                                    <th class="px-6 py-4">Módulo</th>
                                    <th class="px-6 py-4">Motivo</th>
                                    <th class="px-6 py-4">Reportado por</th>
                                    <th class="px-6 py-4">Fecha</th>
                                    <th class="px-6 py-4">Estado</th>
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs sm:text-sm font-medium text-slate-700">
                                <tr v-for="denuncia in denuncias.data" :key="denuncia.id"
                                    class="transition hover:bg-slate-50/80">
                                    <td class="px-6 py-4 font-bold text-[#172A3D]">{{ denuncia.codigo_unico }}</td>
                                    <td class="px-6 py-4 capitalize">
                                        <span
                                            class="rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                            {{ denuncia.modulo }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ denuncia.motivo }}</td>
                                    <td class="px-6 py-4">{{ denuncia.usuario?.name ?? 'Desconocido' }}</td>
                                    <td class="px-6 py-4 text-xs text-slate-500">{{ new
                                        Date(denuncia.created_at).toLocaleString() }}</td>
                                    <td class="px-6 py-4">
                                        <span
                                            :class="['inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider', obtenerColorEstado(denuncia.estado)]">
                                            {{ denuncia.estado.replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <Link :href="route('admin.denuncias.show', denuncia.id)"
                                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-slate-800">
                                            Gestionar
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="!denuncias.data.length">
                                    <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-500 font-medium">
                                        No se encontraron registros de incidencias.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
</style>