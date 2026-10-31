<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    favoritos: {
        type: Array,
        default: () => [],
    },
    moduloActivo: {
        type: String,
        default: '',
    },
})

const filtroModulo = ref(props.moduloActivo ?? '')
const listaFavoritos = ref([...props.favoritos])

const modulos = [
    { id: '', nombre: 'Todos', icono: '❤️' },
    { id: 'marketplace', nombre: 'Marketplace', icono: '🛍️' },
    { id: 'clasificados', nombre: 'Clasificados', icono: '🏷️' },
    { id: 'servicios', nombre: 'Servicios', icono: '🛠️' },
    { id: 'cursos', nombre: 'Cursos', icono: '📚' },
    { id: 'eventos', nombre: 'Eventos', icono: '🎤' },
    { id: 'empleos', nombre: 'Empleos', icono: '💼' },
    { id: 'emprendimientos', nombre: 'Emprendimientos', icono: '🚀' },
]

const favoritosFiltrados = computed(() => {
    if (!filtroModulo.value) {
        return listaFavoritos.value
    }
    if (filtroModulo.value === 'servicios') {
        return listaFavoritos.value.filter(
            item => item.modulo === 'servicios' || item.modulo === 'servicios_requerimientos'
        )
    }
    return listaFavoritos.value.filter(item => item.modulo === filtroModulo.value)
})

const quitarFavorito = (item) => {
    // Eliminación optimista inmediata de la vista
    const backup = [...listaFavoritos.value]
    listaFavoritos.value = listaFavoritos.value.filter(
        fav => !(fav.publicacion_id === item.publicacion_id && fav.modulo === item.modulo)
    )

    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: item.publicacion_id,
            modulo: item.modulo,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onError: () => {
                listaFavoritos.value = backup
            },
        }
    )
}

// Clases dinámicas según el color original de cada módulo
const estiloModulo = (color) => {
    const estilos = {
        blue: {
            sombra: 'shadow-2xl shadow-blue-500/15 hover:border-blue-400',
            badge: 'bg-blue-100 text-blue-900 border-blue-300',
            boton: 'bg-blue-600 hover:bg-blue-700 text-white',
            texto: 'group-hover:text-blue-600',
        },
        amber: {
            sombra: 'shadow-2xl shadow-amber-500/15 hover:border-amber-400',
            badge: 'bg-amber-100 text-amber-900 border-amber-300',
            boton: 'bg-amber-600 hover:bg-amber-700 text-white',
            texto: 'group-hover:text-amber-600',
        },
        emerald: {
            sombra: 'shadow-2xl shadow-emerald-500/15 hover:border-emerald-400',
            badge: 'bg-emerald-100 text-emerald-900 border-emerald-300',
            boton: 'bg-emerald-700 hover:bg-emerald-800 text-white',
            texto: 'group-hover:text-emerald-700',
        },
        orange: {
            sombra: 'shadow-2xl shadow-orange-500/15 hover:border-orange-400',
            badge: 'bg-orange-100 text-orange-950 border-orange-300',
            boton: 'bg-orange-600 hover:bg-orange-700 text-white',
            texto: 'group-hover:text-orange-600',
        },
        indigo: {
            sombra: 'shadow-2xl shadow-indigo-500/15 hover:border-indigo-400',
            badge: 'bg-indigo-100 text-indigo-950 border-indigo-300',
            boton: 'bg-indigo-600 hover:bg-indigo-700 text-white',
            texto: 'group-hover:text-indigo-700',
        },
        purple: {
            sombra: 'shadow-2xl shadow-purple-500/15 hover:border-purple-400',
            badge: 'bg-purple-100 text-purple-950 border-purple-300',
            boton: 'bg-purple-600 hover:bg-purple-700 text-white',
            texto: 'group-hover:text-purple-700',
        },
        rose: {
            sombra: 'shadow-2xl shadow-rose-500/15 hover:border-rose-400',
            badge: 'bg-rose-100 text-rose-950 border-rose-300',
            boton: 'bg-rose-600 hover:bg-rose-700 text-white',
            texto: 'group-hover:text-rose-700',
        },
        teal: {
            sombra: 'shadow-2xl shadow-teal-500/15 hover:border-teal-400',
            badge: 'bg-teal-100 text-teal-950 border-teal-300',
            boton: 'bg-teal-600 hover:bg-teal-700 text-white',
            texto: 'group-hover:text-teal-700',
        },
    }

    return estilos[color] ?? estilos.blue
}
</script>

<template>

    <Head title="Mis Favoritos | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ENCABEZADO DE SECCIÓN -->
            <section class="border-b border-rose-200/80 bg-rose-100/25 backdrop-blur-md shadow-lg shadow-rose-500/10">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div
                                class="mb-2 inline-flex items-center gap-2 rounded-full border border-rose-300/80 bg-white/90 px-3.5 py-1 shadow-sm">
                                <span class="text-xs">❤️</span>
                                <span class="text-xs font-black uppercase tracking-wider text-rose-950">
                                    Colección Personal
                                </span>
                            </div>
                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Mis Favoritos
                            </h1>
                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Accede rápidamente a todos los productos, servicios, cursos, eventos, empleos y
                                clasificados que
                                has guardado.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-6">

                <!-- FILTROS POR MÓDULO -->
                <section class="rounded-2xl border border-slate-300 bg-white p-4 shadow-2xl shadow-rose-500/10">
                    <div class="flex flex-wrap items-center gap-2">
                        <button v-for="mod in modulos" :key="mod.id" type="button" @click="filtroModulo = mod.id"
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs sm:text-sm font-bold transition"
                            :class="filtroModulo === mod.id
                                ? 'bg-[#172A3D] text-white shadow-md'
                                : 'bg-slate-100 text-slate-700 hover:bg-slate-200'">
                            <span>{{ mod.icono }}</span>
                            <span>{{ mod.nombre }}</span>
                        </button>
                    </div>
                </section>

                <!-- BARRA DE CONTEO -->
                <div
                    class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between bg-white rounded-2xl border border-slate-300 p-5 shadow-2xl shadow-rose-500/10">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-rose-600">
                            Elementos guardados
                        </p>
                        <h2 class="mt-0.5 text-xl font-black text-[#172A3D]">
                            Publicaciones seleccionadas
                        </h2>
                    </div>
                    <span
                        class="rounded-full bg-slate-100 border border-slate-200 px-3.5 py-1 text-xs sm:text-sm font-bold text-[#172A3D]">
                        {{ favoritosFiltrados.length }} {{ favoritosFiltrados.length === 1 ? 'favorito' : 'favoritos' }}
                    </span>
                </div>

                <!-- ESTADO VACÍO -->
                <div v-if="!favoritosFiltrados.length"
                    class="rounded-3xl border border-slate-300 bg-white px-6 py-16 text-center shadow-2xl shadow-rose-500/10">
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-rose-50 text-3xl text-rose-600 border border-rose-200">
                        ❤️
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-[#172A3D]">
                        Aún no tienes favoritos en esta categoría
                    </h3>
                    <p class="mx-auto mt-2 max-w-md text-xs sm:text-sm text-slate-500 font-medium">
                        Explora los módulos de la comunidad y pulsa el icono del corazón en las publicaciones que te
                        interesen para guardarlas aquí.
                    </p>
                    <button v-if="filtroModulo" type="button" @click="filtroModulo = ''"
                        class="mt-6 rounded-xl bg-[#172A3D] px-5 py-2.5 text-xs sm:text-sm font-bold text-white transition hover:bg-slate-800">
                        Ver todos mis favoritos
                    </button>
                </div>

                <!-- GRID DE TARJETAS CON CONTENEDOR BLANCO -->
                <div v-else
                    class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 bg-white rounded-2xl border border-slate-300 p-5 shadow-2xl shadow-rose-500/10">
                    <article v-for="item in favoritosFiltrados" :key="`${item.modulo}-${item.publicacion_id}`"
                        class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-300 bg-white transition duration-300 hover:-translate-y-1"
                        :class="estiloModulo(item.color).sombra">
                        <div>
                            <!-- Imagen o Placeholder del Módulo -->
                            <Link :href="item.url_destino"
                                class="relative aspect-[4/3] block overflow-hidden bg-slate-100">
                                <img v-if="item.imagen" :src="item.imagen" :alt="item.titulo"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                                <div v-else
                                    class="flex h-full w-full flex-col items-center justify-center text-slate-400">
                                    <span class="text-5xl">{{ item.icono }}</span>
                                    <span class="mt-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                                        {{ item.etiqueta }}
                                    </span>
                                </div>

                                <!-- Badge identificador del módulo -->
                                <div class="absolute left-3 top-3">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full border px-3 py-1 text-[11px] font-black shadow-sm backdrop-blur"
                                        :class="estiloModulo(item.color).badge">
                                        <span>{{ item.icono }}</span>
                                        <span>{{ item.etiqueta }}</span>
                                    </span>
                                </div>
                            </Link>

                            <!-- Botón para quitar de favoritos -->
                            <button type="button" @click.stop="quitarFavorito(item)"
                                class="absolute right-3 top-3 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-red-50 ring-1 ring-red-200 shadow-md backdrop-blur transition hover:scale-110"
                                title="Quitar de favoritos">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="h-5 w-5 fill-red-600 text-red-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>
                            </button>

                            <!-- Cuerpo de la tarjeta -->
                            <div class="p-4">
                                <p class="text-[11px] font-bold text-slate-500 truncate">
                                    {{ item.subtitulo }}
                                </p>

                                <Link :href="item.url_destino" class="mt-1 block">
                                    <h3 class="line-clamp-2 text-sm sm:text-base font-black text-[#172A3D] transition"
                                        :class="estiloModulo(item.color).texto">
                                        {{ item.titulo }}
                                    </h3>
                                </Link>

                                <p class="mt-2.5 text-sm sm:text-base font-black text-[#172A3D]">
                                    {{ item.precio }}
                                </p>
                            </div>
                        </div>

                        <!-- Pie con botón directo hacia el registro -->
                        <div class="border-t border-slate-100 p-4 pt-3">
                            <Link :href="item.url_destino"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-xs font-black transition shadow-sm"
                                :class="estiloModulo(item.color).boton">
                                Ir a {{ item.etiqueta.toLowerCase() }} →
                            </Link>
                        </div>
                    </article>
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