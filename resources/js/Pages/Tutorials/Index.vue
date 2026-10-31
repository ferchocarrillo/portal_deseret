<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    tutorials: Array
});

const selectedCategory = ref('Todas');

// Obtener categorías únicas
const categories = computed(() => {
    const cats = props.tutorials.map(t => t.category);
    return ['Todas', ...new Set(cats)];
});

// Filtrar tutoriales por categoría seleccionada
const filteredTutorials = computed(() => {
    if (selectedCategory.value === 'Todas') {
        return props.tutorials;
    }
    return props.tutorials.filter(t => t.category === selectedCategory.value);
});
</script>

<template>
    <AuthenticatedLayout>

        <Head title="Centro de Ayuda y Tutoriales" />

        <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <!-- Encabezado -->
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-[#172A3D] flex items-center justify-center gap-2">
                    Centro de Aprendizaje y Videotutoriales <span class="text-amber-500">🎥</span>
                </h1>
                <p class="text-sm text-gray-600 mt-2 max-w-2xl mx-auto">
                    Explora los videotutoriales paso a paso para aprovechar al máximo todas las herramientas y módulos
                    gratuitos de Portal Deseret.
                </p>
            </div>

            <!-- Filtros de Categorías -->
            <div class="flex flex-wrap justify-center gap-2 mb-8">
                <button v-for="category in categories" :key="category" @click="selectedCategory = category"
                    :class="['px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm', selectedCategory === category ? 'bg-[#172A3D] text-amber-400 border border-amber-400/40' : 'bg-white text-gray-700 border border-gray-200 hover:border-amber-400']">
                    {{ category }}
                </button>
            </div>

            <!-- Listado de Videos / Tarjetas -->
            <div v-if="filteredTutorials.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="tutorial in filteredTutorials" :key="tutorial.id"
                    class="bg-white rounded-2xl border border-amber-100 shadow-md overflow-hidden flex flex-col justify-between transition hover:shadow-lg">

                    <!-- Reproductor o Miniatura del Video -->
                    <div class="relative aspect-video bg-gray-900 flex items-center justify-center overflow-hidden">
                        <iframe v-if="tutorial.video_url.includes('http')" :src="tutorial.video_url"
                            class="w-full h-full object-cover" frameborder="0" allowfullscreen></iframe>
                        <div v-else class="text-white text-xs p-4 text-center">
                            [Video generado con Google Flow / Pendiente de enlace]
                        </div>
                    </div>

                    <!-- Información -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <span
                                class="inline-block bg-amber-100 text-amber-800 text-[10px] font-bold px-2.5 py-0.5 rounded-full mb-2 uppercase tracking-wide">
                                {{ tutorial.category }}
                            </span>
                            <h3 class="font-bold text-base text-[#172A3D] mb-1.5">
                                {{ tutorial.title }}
                            </h3>
                            <p class="text-xs text-gray-600 leading-relaxed">
                                {{ tutorial.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado Vacío -->
            <div v-else
                class="text-center py-16 bg-white rounded-2xl border border-gray-200 shadow-sm max-w-md mx-auto">
                <span class="text-3xl">📭</span>
                <p class="text-sm font-semibold text-gray-700 mt-2">No hay videotutoriales disponibles en esta
                    categoría.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>