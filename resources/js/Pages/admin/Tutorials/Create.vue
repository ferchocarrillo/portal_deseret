<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Recibimos opcionalmente el tutorial si estamos en modo edición
const props = defineProps({
    tutorial: {
        type: Object,
        default: null
    }
})

// Inicializamos el formulario con los datos del tutorial (si existe) o vacíos
const form = useForm({
    title: props.tutorial?.title ?? '',
    category: props.tutorial?.category ?? 'Emprendimientos',
    description: props.tutorial?.description ?? '',
    video_url: props.tutorial?.video_url ?? '',
    order: props.tutorial?.order ?? 0,
    is_active: props.tutorial ? Boolean(props.tutorial.is_active) : true
})

// Función para asegurar que cualquier enlace de YouTube se guarde en formato embed válido
const formatYouTubeUrl = (url) => {
    if (!url) return '';
    if (url.includes('/embed/')) return url;

    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);

    return (match && match[2].length === 11)
        ? `https://www.youtube.com/embed/${match[2]}`
        : url;
}

const submit = () => {
    // Autoconvertir el enlace a formato embed antes de enviar
    form.video_url = formatYouTubeUrl(form.video_url);

    if (props.tutorial) {
        // Si hay tutorial, actualizamos (PUT)
        form.put(route('admin.tutorials.update', props.tutorial.id))
    } else {
        // Si no, creamos uno nuevo (POST)
        form.post(route('admin.tutorials.store'))
    }
}
</script>

<template>

    <Head :title="tutorial ? 'Editar Videotutorial | Administración' : 'Nuevo Videotutorial | Administración'" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo py-10">
            <main class="mx-auto max-w-2xl px-6 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-300 shadow-xl">
                    <p class="text-xs font-bold text-[#D99A24] uppercase tracking-wider">Administración</p>
                    <h1 class="text-2xl font-black text-[#172A3D]">
                        {{ tutorial ? 'Editar Videotutorial' : 'Nuevo Videotutorial' }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">
                        {{ tutorial ? 'Modifica los datos y el enlace del videotutorial.' : 'Agrega un enlace de video de YouTube para el centro de ayuda.' }}
                    </p>
                </div>

                <form @submit.prevent="submit"
                    class="bg-white p-6 rounded-2xl border border-slate-300 shadow-xl space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Título del Tutorial</label>
                        <input v-model="form.title" type="text"
                            class="w-full text-xs rounded-xl border-slate-300 focus:border-[#D99A24] focus:ring-[#D99A24]"
                            required />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Categoría</label>
                            <select v-model="form.category"
                                class="w-full text-xs rounded-xl border-slate-300 focus:border-[#D99A24] focus:ring-[#D99A24]">
                                <option value="Empleos">Empleos</option>
                                <option value="Servicios">Servicios</option>
                                <option value="Cursos">Cursos</option>
                                <option value="Marketplace">Marketplace</option>
                                <option value="Clasificados">Clasificados</option>
                                <option value="Emprendimientos">Emprendimientos</option>
                                <option value="Donaciones">Donaciones</option>
                                <option value="Denuncias">Denuncias</option>
                                <option value="General">General</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Orden de Visualización</label>
                            <input v-model.number="form.order" type="number"
                                class="w-full text-xs rounded-xl border-slate-300 focus:border-[#D99A24] focus:ring-[#D99A24]" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">URL de YouTube (Puedes pegar el
                            enlace normal o embed)</label>
                        <input v-model="form.video_url" type="text" placeholder="https://www.youtube.com/watch?v=..."
                            class="w-full text-xs rounded-xl border-slate-300 focus:border-[#D99A24] focus:ring-[#D99A24]"
                            required />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Descripción Breve</label>
                        <textarea v-model="form.description" rows="3"
                            class="w-full text-xs rounded-xl border-slate-300 focus:border-[#D99A24] focus:ring-[#D99A24]"></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input v-model="form.is_active" type="checkbox" id="is_active"
                            class="rounded border-slate-300 text-[#D99A24] focus:ring-[#D99A24]" />
                        <label for="is_active" class="text-xs font-bold text-slate-700">Tutorial activo y visible para
                            la comunidad</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                        <Link :href="route('admin.tutorials.index')"
                            class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">
                            Cancelar</Link>
                        <button type="submit"
                            class="px-5 py-2 text-xs font-bold text-[#D99A24] bg-[#172A3D] rounded-xl hover:bg-[#203852] transition shadow-md">
                            {{ tutorial ? 'Actualizar Tutorial' : 'Guardar Tutorial' }}
                        </button>
                    </div>
                </form>
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