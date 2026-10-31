<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Swal from 'sweetalert2'

defineProps({
    tutorials: Array
})

const deleteTutorial = (id) => {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción eliminará el videotutorial de forma permanente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#172A3D',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('admin.tutorials.destroy', id), {
                onSuccess: () => {
                    Swal.fire(
                        '¡Eliminado!',
                        'El videotutorial ha sido eliminado con éxito.',
                        'success'
                    )
                }
            })
        }
    })
}
</script>

<template>

    <Head title="Gestión de Tutoriales | Administración" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo py-10">
            <main class="mx-auto max-w-5xl px-6 space-y-6">
                <!-- Cabecera -->
                <div
                    class="bg-white p-6 rounded-2xl border border-slate-300 shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <p class="text-xs font-bold text-[#D99A24] uppercase tracking-wider">Administración</p>
                        <h1 class="text-2xl font-black text-[#172A3D]">Gestión de Videotutoriales</h1>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium">Administra los videos de ayuda del
                            centro de aprendizaje.</p>
                    </div>
                    <Link :href="route('admin.tutorials.create')"
                        class="bg-[#172A3D] hover:bg-[#203852] text-[#D99A24] px-5 py-2.5 rounded-xl font-bold text-xs transition shadow-md border border-[#D99A24]/40 flex items-center gap-2">
                        <span>➕ Nuevo Tutorial</span>
                    </Link>
                </div>

                <!-- Tabla de Tutoriales -->
                <div class="bg-white rounded-2xl border border-slate-300 shadow-xl overflow-hidden">
                    <div v-if="tutorials.length > 0" class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-600 uppercase">
                                    <th class="p-4">Orden</th>
                                    <th class="p-4">Título</th>
                                    <th class="p-4">Categoría</th>
                                    <th class="p-4 text-center">Estado</th>
                                    <th class="p-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                                <tr v-for="tutorial in tutorials" :key="tutorial.id"
                                    class="hover:bg-slate-50/80 transition">
                                    <td class="p-4 font-bold text-slate-500">{{ tutorial.order }}</td>
                                    <td class="p-4 font-semibold text-[#172A3D]">{{ tutorial.title }}</td>
                                    <td class="p-4">
                                        <span
                                            class="bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full font-bold text-[10px]">
                                            {{ tutorial.category }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span
                                            :class="['px-2 py-0.5 rounded-full font-bold text-[10px]', tutorial.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800']">
                                            {{ tutorial.is_active ? 'Activo' : 'Oculto' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right space-x-2">
                                        <Link :href="route('admin.tutorials.edit', tutorial.id)"
                                            class="text-indigo-600 hover:text-indigo-900 font-bold">Editar</Link>
                                        <button @click="deleteTutorial(tutorial.id)"
                                            class="text-rose-600 hover:text-rose-900 font-bold">Eliminar</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="p-12 text-center text-slate-500">
                        <p class="text-sm font-semibold">No hay videotutoriales registrados todavía.</p>
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