<script setup>
import { ref } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    palabras: Object,
    filtros: Object,
})

const form = useForm({
    palabra: '',
    nivel: 'alta',
})

const busqueda = ref(props.filtros.busqueda || '')

function registrarPalabra() {
    form.post(route('admin.palabras.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('palabra'),
    })
}

function eliminar(id) {
    if (confirm('¿Estás seguro de eliminar esta palabra del filtro?')) {
        router.delete(route('admin.palabras.destroy', id), {
            preserveScroll: true,
        })
    }
}

function buscar() {
    router.get(route('admin.palabras.index'), { busqueda: busqueda.value }, { preserveState: true, replace: true })
}
</script>

<template>

    <Head title="Gestión de Palabras Prohibidas | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo py-10">
            <main class="mx-auto max-w-5xl px-6 space-y-6">

                <!-- ENCABEZADO -->
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white p-6 rounded-2xl border border-slate-300 shadow-xl">
                    <div>
                        <p class="text-xs font-bold text-[#D99A24] uppercase tracking-wider">Moderación del Sistema</p>
                        <h1 class="text-2xl font-black text-[#172A3D]">Banco de Palabras Prohibidas</h1>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium">Administra los términos bloqueados en
                            los formularios y chats de la plataforma.</p>
                    </div>
                </div>

                <!-- FORMULARIO DE REGISTRO RÁPIDO -->
                <div class="bg-white p-6 rounded-2xl border border-slate-300 shadow-xl">
                    <h2 class="text-base font-bold text-[#172A3D] mb-4">Agregar nueva palabra o término</h2>

                    <form @submit.prevent="registrarPalabra" class="grid gap-4 sm:grid-cols-3 items-end">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Palabra /
                                Término</label>
                            <input v-model="form.palabra" type="text" placeholder="Ej. groseria"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold outline-none focus:border-[#D99A24]" />
                            <p v-if="form.errors.palabra" class="mt-1 text-xs text-red-600 font-bold">{{
                                form.errors.palabra }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nivel de
                                severidad</label>
                            <select v-model="form.nivel"
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold outline-none focus:border-[#D99A24]">
                                <option value="alta">Alta (Bloqueo estricto)</option>
                                <option value="media">Media</option>
                                <option value="baja">Baja</option>
                            </select>
                        </div>

                        <div>
                            <button type="submit" :disabled="form.processing"
                                class="w-full rounded-xl bg-[#D99A24] px-5 py-2.5 text-sm font-black text-white shadow-md transition hover:bg-amber-600 disabled:opacity-50">
                                {{ form.processing ? 'Guardando...' : '+ Agregar al banco' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- LISTADO DE PALABRAS -->
                <div class="bg-white rounded-2xl border border-slate-300 shadow-xl overflow-hidden">
                    <div
                        class="p-5 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <h2 class="font-bold text-[#172A3D]">Términos registrados ({{ palabras.total }})</h2>

                        <div class="w-full sm:w-auto">
                            <input v-model="busqueda" @keyup.enter="buscar" type="text" placeholder="Buscar palabra..."
                                class="w-full sm:w-64 rounded-xl border border-slate-300 px-3 py-1.5 text-xs outline-none focus:border-[#D99A24]" />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-600 uppercase">
                                    <th class="p-4">Palabra</th>
                                    <th class="p-4">Nivel</th>
                                    <th class="p-4">Fecha de registro</th>
                                    <th class="p-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <!-- Volvemos a usar palabras.data -->
                                <tr v-for="item in palabras.data" :key="item.id"
                                    class="hover:bg-slate-50/50 transition">
                                    <td class="p-4 font-bold text-[#172A3D]">{{ item.palabra }}</td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold" :class="{
                                            'bg-red-100 text-red-700': item.nivel === 'alta',
                                            'bg-amber-100 text-amber-700': item.nivel === 'media',
                                            'bg-blue-100 text-blue-700': item.nivel === 'baja',
                                        }">
                                            {{ item.nivel.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-xs text-slate-500 font-medium">
                                        {{ new Date(item.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="p-4 text-right">
                                        <button @click="eliminar(item.id)"
                                            class="text-xs font-bold text-red-600 hover:text-red-800 transition bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="palabras.data.length === 0">
                                    <td colspan="4" class="p-8 text-center text-slate-400 text-sm font-medium">
                                        No hay palabras prohibidas registradas todavía.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINACIÓN TRADICIONAL -->
                    <div v-if="palabras.links && palabras.links.length > 3"
                        class="p-4 border-t border-slate-200 flex flex-wrap justify-center gap-1">
                        <template v-for="(link, i) in palabras.links" :key="i">
                            <component :is="link.url ? Link : 'span'" :href="link.url" v-html="link.label"
                                class="px-3 py-1.5 text-xs font-bold rounded-lg border transition" :class="{
                                    'bg-[#D99A24] text-white border-[#D99A24]': link.active,
                                    'bg-white text-slate-700 border-slate-300 hover:bg-slate-50': !link.active && link.url,
                                    'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed': !link.url
                                }" />
                        </template>
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