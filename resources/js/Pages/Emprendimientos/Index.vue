<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { route } from 'ziggy-js';

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/

const props = defineProps({
    emprendimientos: {
        type: Object,
        required: true,
    },

    categorias: {
        type: Array,
        default: () => [],
    },

    filtros: {
        type: Object,
        default: () => ({
            search: '',
            categoria_id: '',
            subcategoria_id: '',
        }),
    },
});

// Estado para controlar si el buscador/filtros están colapsados o abiertos
const mostrarFiltros = ref(false);

// Validación de bloqueo temporal para el usuario autenticado
const page = usePage();
const estaBloqueado = computed(() => {
    const usuario = page.props.auth?.user;
    if (!usuario?.bloqueado_hasta) return false;
    return new Date(usuario.bloqueado_hasta) > new Date();
});

/*
|--------------------------------------------------------------------------
| Filtros
|--------------------------------------------------------------------------
*/

const filtros = computed(() => ({
    search: props.filtros?.search ?? '',
    categoria_id: props.filtros?.categoria_id ?? '',
    subcategoria_id: props.filtros?.subcategoria_id ?? '',
}));

/*
|--------------------------------------------------------------------------
| Subcategorías disponibles
|--------------------------------------------------------------------------
*/

const subcategoriasDisponibles = computed(() => {
    if (!filtros.value.categoria_id) {
        return [];
    }

    const categoria = props.categorias.find(
        cat =>
            Number(cat.id) ===
            Number(filtros.value.categoria_id)
    );

    return categoria?.subcategorias ?? [];
});

/*
|--------------------------------------------------------------------------
| Favoritos Reactivos
|--------------------------------------------------------------------------
*/

const alternarFavorito = (emprendimiento) => {
    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: emprendimiento.id,
            modulo: 'emprendimientos',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                emprendimiento.es_favorito = !Boolean(emprendimiento.es_favorito);
            },
        }
    );
};

/*
|--------------------------------------------------------------------------
| Aplicar filtros
|--------------------------------------------------------------------------
*/

const aplicarFiltros = () => {
    router.get(
        route('emprendimientos.index'),
        {
            search: filtros.value.search || undefined,
            categoria_id: filtros.value.categoria_id || undefined,
            subcategoria_id: filtros.value.subcategoria_id || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

/*
|--------------------------------------------------------------------------
| Limpiar filtros
|--------------------------------------------------------------------------
*/

const limpiarFiltros = () => {
    router.get(
        route('emprendimientos.index'),
        {},
        {
            preserveState: false,
            preserveScroll: true,
        }
    );
};

/*
|--------------------------------------------------------------------------
| Miniatura de YouTube
|--------------------------------------------------------------------------
*/

const obtenerYoutubeThumbnail = (emprendimiento) => {
    if (!emprendimiento?.youtube_video_id) {
        return null;
    }

    return `https://img.youtube.com/vi/${emprendimiento.youtube_video_id}/hqdefault.jpg`;
};

/*
|--------------------------------------------------------------------------
| Fecha
|--------------------------------------------------------------------------
*/

const formatearFecha = (fecha) => {
    if (!fecha) {
        return '';
    }

    return new Date(fecha).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>

    <Head title="Emprendimientos | Portal Deseret" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="false">

        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo">

            <!-- =========================================================
                 ENCABEZADO DE SECCIÓN CON TONO VIOLETA TRANSLÚCIDO
            ========================================================== -->
            <section
                class="border-b border-purple-200/80 bg-purple-100/35 backdrop-blur-md shadow-xl shadow-purple-600/20">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="mb-2 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-purple-600"></span>
                                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-purple-950">
                                    Comunidad y negocios
                                </p>
                            </div>

                            <h1 class="text-3xl font-black tracking-tight text-[#172A3D] sm:text-4xl">
                                Emprendimientos
                            </h1>

                            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-700 font-medium sm:text-base">
                                Conoce emprendimientos, descubre oportunidades y encuentra personas con las que puedas
                                construir nuevas colaboraciones.
                            </p>
                        </div>

                        <!-- Botón superior protegido con estatus de bloqueo -->
                        <template v-if="estaBloqueado">
                            <span
                                class="inline-flex items-center justify-center rounded-xl bg-slate-400 px-5 py-3 text-xs sm:text-sm font-black text-white shadow-md cursor-not-allowed"
                                title="Tu cuenta tiene un bloqueo temporal activo y no puede crear publicaciones">
                                Publicación restringida
                            </span>
                        </template>
                        <template v-else>
                            <Link :href="route('emprendimientos.create')"
                                class="inline-flex items-center justify-center rounded-xl bg-purple-600 px-5 py-3 text-xs sm:text-sm font-black text-white shadow-2xl shadow-purple-600/40 transition hover:bg-purple-700">
                                <i class="pi pi-plus mr-2"></i>
                                + Crear emprendimiento
                            </Link>
                        </template>
                    </div>
                </div>
            </section>

            <!-- =========================================================
                 CONTENIDO PRINCIPAL
            ========================================================== -->
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 bg-purple-50 rounded-xl p-3 mt-2">

                <!-- FILTROS COLAPSABLE -->
                <div class="mb-8 rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-purple-600/25">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <button type="button" @click="mostrarFiltros = !mostrarFiltros"
                                class="inline-flex items-center gap-2 rounded-xl bg-purple-50 border border-purple-200 px-4 py-2.5 text-xs sm:text-sm font-black text-purple-900 transition hover:bg-purple-100 shadow-sm">
                                <i :class="mostrarFiltros ? 'pi pi-chevron-up' : 'pi pi-filter'"></i>
                                {{ mostrarFiltros ? 'Ocultar filtros' : 'Filtrar y buscar' }}
                                <span v-if="filtros.search || filtros.categoria_id || filtros.subcategoria_id"
                                    class="ml-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-purple-600 text-[10px] text-white font-bold">
                                    !
                                </span>
                            </button>
                            <div>
                                <h2 class="font-bold text-[#172A3D] text-sm sm:text-base">
                                    Buscar emprendimientos
                                </h2>
                                <p class="text-xs sm:text-sm text-slate-500 font-medium">
                                    Encuentra proyectos por nombre, descripción o categoría.
                                </p>
                            </div>
                        </div>

                        <button v-if="filtros.search || filtros.categoria_id || filtros.subcategoria_id" type="button"
                            class="text-xs font-bold text-purple-700 transition hover:text-purple-900 underline self-start sm:self-auto mt-2 sm:mt-0"
                            @click="limpiarFiltros">
                            Limpiar filtros
                        </button>
                    </div>

                    <!-- CONTENEDOR DESPLEGABLE -->
                    <div v-if="mostrarFiltros" class="mt-5 border-t border-slate-200 pt-5 transition-all">
                        <form class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
                            @submit.prevent="aplicarFiltros">
                            <!-- Buscar -->
                            <div class="lg:col-span-2">
                                <label for="search" class="mb-1.5 block text-xs font-bold text-slate-700">
                                    Buscar
                                </label>
                                <div class="relative">
                                    <i
                                        class="pi pi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <input id="search" v-model="filtros.search" type="text"
                                        placeholder="Nombre, descripción o palabra clave..."
                                        class="w-full h-11 rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 outline-none" />
                                </div>
                            </div>

                            <!-- Categoría -->
                            <div>
                                <label for="categoria_id" class="mb-1.5 block text-xs font-bold text-slate-700">
                                    Categoría
                                </label>
                                <select id="categoria_id" v-model="filtros.categoria_id"
                                    class="w-full h-11 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-semibold text-[#172A3D] focus:border-purple-600 outline-none">
                                    <option value="">Todas las categorías</option>
                                    <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
                                        {{ categoria.nombre }}
                                    </option>
                                </select>
                            </div>

                            <!-- Subcategoría -->
                            <div>
                                <label for="subcategoria_id" class="mb-1.5 block text-xs font-bold text-slate-700">
                                    Subcategoría
                                </label>
                                <select id="subcategoria_id" v-model="filtros.subcategoria_id"
                                    :disabled="!filtros.categoria_id"
                                    class="w-full h-11 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-semibold text-[#172A3D] disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 focus:border-purple-600 outline-none">
                                    <option value="">Todas las subcategorías</option>
                                    <option v-for="subcategoria in subcategoriasDisponibles" :key="subcategoria.id"
                                        :value="subcategoria.id">
                                        {{ subcategoria.nombre }}
                                    </option>
                                </select>
                            </div>

                            <!-- Botón -->
                            <div class="md:col-span-2 lg:col-span-4 flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-xl bg-purple-600 px-6 py-2.5 text-xs sm:text-sm font-black text-white transition hover:bg-purple-700 shadow-xl shadow-purple-600/35">
                                    <i class="pi pi-search mr-2"></i>
                                    Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ENCABEZADO DE RESULTADOS -->
                <div
                    class="mb-5 flex items-center justify-between px-1 bg-white rounded-2xl border border-slate-300 p-5 border-b border-purple-200/80 bg-purple-100/35 backdrop-blur-md shadow-xl shadow-purple-600/20">
                    <div>
                        <p class="text-xs sm:text-sm font-bold text-purple-800 uppercase tracking-wide">Iniciativas
                            comunitarias</p>
                        <h2 class="mt-1 text-xl sm:text-2xl font-black text-[#172A3D]">
                            Emprendimientos publicados
                        </h2>
                        <p v-if="emprendimientos.total !== undefined" class="mt-0.5 text-xs text-slate-600 font-medium">
                            {{ emprendimientos.total }} {{ emprendimientos.total === 1 ? 'resultado' : 'resultados' }}
                        </p>
                    </div>
                </div>

                <!-- SIN RESULTADOS -->
                <div v-if="!emprendimientos.data?.length"
                    class="rounded-2xl border border-slate-300 bg-white px-6 py-16 text-center shadow-2xl shadow-purple-600/25">
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-purple-100 border border-purple-200 text-purple-700 shadow-md">
                        <i class="pi pi-search text-2xl"></i>
                    </div>

                    <h3 class="mt-4 text-base sm:text-lg font-bold text-[#172A3D]">
                        No encontramos emprendimientos
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-xs sm:text-sm text-slate-500 font-medium">
                        Intenta modificar los filtros o sé el primero en publicar un emprendimiento.
                    </p>

                    <div class="mt-6">
                        <template v-if="estaBloqueado">
                            <span
                                class="inline-flex items-center rounded-xl bg-slate-400 px-5 py-3 text-xs sm:text-sm font-black text-white shadow-md cursor-not-allowed">
                                Publicación restringida
                            </span>
                        </template>
                        <template v-else>
                            <Link :href="route('emprendimientos.create')"
                                class="inline-flex items-center rounded-xl bg-purple-600 px-5 py-3 text-xs sm:text-sm font-black text-white hover:bg-purple-700 transition shadow-xl shadow-purple-600/35">
                                <i class="pi pi-plus mr-2"></i>
                                Crear emprendimiento
                            </Link>
                        </template>
                    </div>
                </div>

                <!-- TARJETAS CON SOMBRAS VIOLETAS INTENSAS -->
                <div v-else
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 border-b border-purple-200/80 bg-purple-100/35 backdrop-blur-md shadow-xl shadow-purple-600/20">
                    <article v-for="emprendimiento in emprendimientos.data" :key="emprendimiento.id"
                        class="group flex flex-col overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-purple-600/30 transition duration-300 hover:-translate-y-1 hover:border-purple-500 hover:shadow-purple-600/50">

                        <!-- VIDEO / MINIATURA -->
                        <div class="relative aspect-video overflow-hidden bg-slate-100">
                            <img v-if="obtenerYoutubeThumbnail(emprendimiento)"
                                :src="obtenerYoutubeThumbnail(emprendimiento)"
                                :alt="`Video de ${emprendimiento.nombre}`"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />

                            <div v-else
                                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-purple-900/15 to-indigo-900/15 text-slate-400">
                                <i class="pi pi-video text-4xl"></i>
                            </div>

                            <div v-if="emprendimiento.youtube_video_id"
                                class="absolute bottom-3 left-3 flex items-center rounded-full bg-black/75 px-3 py-1 text-[11px] font-bold text-white backdrop-blur shadow-md">
                                <i class="pi pi-play mr-1.5"></i>
                                Video
                            </div>
                        </div>

                        <!-- CONTENIDO -->
                        <div class="flex flex-1 flex-col p-5">
                            <div class="mb-3 flex items-start justify-between gap-3 ">
                                <div>
                                    <span v-if="emprendimiento.categoria"
                                        class="inline-flex rounded-full bg-purple-100 border border-purple-200 px-2.5 py-0.5 text-[11px] font-bold text-purple-900 shadow-2xs">
                                        {{ emprendimiento.categoria.nombre }}
                                    </span>

                                    <h3
                                        class="mt-2 line-clamp-2 text-base sm:text-lg font-black text-[#172A3D] transition group-hover:text-purple-700">
                                        {{ emprendimiento.nombre }}
                                    </h3>
                                </div>

                                <!-- BOTÓN DE FAVORITO FLOTANTE (ÍCONO SVG REACTIVO) -->
                                <button type="button" @click.stop="alternarFavorito(emprendimiento)"
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/95 shadow-md backdrop-blur transition hover:scale-110"
                                    :class="emprendimiento.es_favorito ? 'bg-red-50 ring-1 ring-red-200' : 'hover:bg-slate-50'"
                                    :title="emprendimiento.es_favorito ? 'Quitar de favoritos' : 'Guardar en favoritos'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" class="h-5 w-5 transition-colors"
                                        :class="emprendimiento.es_favorito ? 'fill-red-600 text-red-600' : 'fill-none text-slate-400 hover:text-red-500'">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Subcategoría -->
                            <p v-if="emprendimiento.subcategoria" class="mb-2 text-xs font-bold text-slate-500">
                                {{ emprendimiento.subcategoria.nombre }}
                            </p>

                            <!-- Descripción -->
                            <p class="line-clamp-3 text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                {{ emprendimiento.descripcion }}
                            </p>

                            <!-- Producción actual -->
                            <div v-if="emprendimiento.produccion_actual"
                                class="mt-3.5 rounded-xl bg-purple-50/70 border border-purple-200 p-3 shadow-2xs">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-purple-950">
                                    Producción actual
                                </p>
                                <p class="mt-0.5 line-clamp-2 text-xs text-slate-700 font-medium">
                                    {{ emprendimiento.produccion_actual }}
                                </p>
                            </div>

                            <!-- Propietario -->
                            <div class="mt-4 flex items-center gap-3 border-t border-slate-100 pt-3.5">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100 border border-purple-200 text-xs font-bold text-purple-800 shadow-2xs">
                                    {{ emprendimiento.usuario?.name?.charAt(0)?.toUpperCase() ?? '?' }}
                                </div>

                                <div class="min-w-0">
                                    <p class="truncate text-xs font-bold text-[#172A3D]">
                                        {{ emprendimiento.usuario?.name ?? 'Usuario' }}
                                    </p>
                                    <p v-if="emprendimiento.created_at" class="text-[10px] text-slate-400 font-medium">
                                        Publicado {{ formatearFecha(emprendimiento.created_at) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Acción -->
                            <div class="mt-auto pt-4">
                                <Link :href="route('emprendimientos.show', emprendimiento.id)"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-purple-600 px-4 py-2.5 text-xs sm:text-sm font-black text-white shadow-xl shadow-purple-600/35 transition hover:bg-purple-700">
                                    Ver emprendimiento
                                    <i class="pi pi-arrow-right text-xs"></i>
                                </Link>
                            </div>
                        </div>

                    </article>
                </div>

                <!-- PAGINACIÓN -->
                <div v-if="emprendimientos.links?.length > 3" class="mt-8 flex flex-wrap justify-center gap-2">
                    <template v-for="(link, index) in emprendimientos.links" :key="index">
                        <Link v-if="link.url" :href="link.url"
                            class="rounded-xl border px-3 py-1.5 text-xs font-bold transition shadow-sm"
                            :class="link.active
                                ? 'border-purple-600 bg-purple-600 text-white shadow-lg shadow-purple-600/40'
                                : 'border-slate-300 bg-white text-slate-700 hover:border-purple-400 hover:bg-purple-50'" v-html="link.label" />
                        <span v-else
                            class="rounded-xl border border-slate-200 bg-slate-100 px-3 py-1.5 text-xs text-slate-400"
                            v-html="link.label"></span>
                    </template>
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