<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    publicaciones: {
        type: Object,
        required: true,
    },
    esModoPropio: {
        type: Boolean,
        default: false,
    },
    filtros: {
        type: Object,
        default: () => ({ modulo: 'todos', search: '', estado: 'todos', per_page: 15 }),
    },
    totalesPorModulo: {
        type: Object,
        default: () => ({}),
    },
});

const items = computed(() => props.publicaciones.data ?? []);
const moduloSeleccionado = ref(props.filtros.modulo || 'todos');
const busqueda = ref(props.filtros.search || '');
const estadoSeleccionado = ref(props.filtros.estado || 'todos');
const porPagina = ref(props.filtros.per_page || 15);

const aplicarFiltros = () => {
    const rutaDestino = props.esModoPropio ? 'publicaciones.mis-publicaciones' : 'admin.publicaciones.index';

    router.get(
        route(rutaDestino),
        {
            modulo: moduloSeleccionado.value,
            search: busqueda.value,
            estado: estadoSeleccionado.value,
            per_page: porPagina.value,
            page: 1,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
};

const cambiarModulo = (modulo) => {
    moduloSeleccionado.value = modulo;
    aplicarFiltros();
};

// Función para alternar el destaque de una publicación desde el checkbox al final de la fila
const toggleDestacado = (pub) => {
    router.post(
        route('admin.destacados.toggle'),
        {
            id: pub.id,
            model: pub.model_class,
            modulo: pub.modulo_key,
        },
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
};

const colorModulo = (moduloKey) => {
    switch (moduloKey) {
        case 'marketplace': return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'servicios': return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'clasificados': return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'emprendimientos': return 'bg-purple-50 text-purple-700 border-purple-200';
        case 'cursos': return 'bg-cyan-50 text-cyan-700 border-cyan-200';
        case 'empleos': return 'bg-rose-50 text-rose-700 border-rose-200';
        case 'eventos': return 'bg-violet-50 text-violet-700 border-violet-200';
        case 'requerimientos': return 'bg-orange-50 text-orange-700 border-orange-200';
        default: return 'bg-slate-50 text-slate-700 border-slate-200';
    }
};

const colorEstado = (estado) => {
    const est = String(estado).toLowerCase();
    if (['activo', 'abierto', 'publicado', 'activa'].includes(est)) return 'bg-emerald-100 text-emerald-800';
    if (['suspendido', 'bloqueado', 'rechazado'].includes(est)) return 'bg-red-100 text-red-800';
    if (['pausado', 'cerrado', 'inactivo'].includes(est)) return 'bg-amber-100 text-amber-800';
    return 'bg-slate-100 text-slate-700';
};
</script>

<template>

    <Head :title="esModoPropio ? 'Mis Publicaciones' : 'Administración de Publicaciones'" />

    <AuthenticatedLayout>
        <div class="min-h-screen p-6 lg:p-8">
            <div class="mx-auto max-w-7xl space-y-6 bg-[#A65F1B]/50 p-3 rounded-xl">

                <!-- ENCABEZADO -->
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                    <div>
                        <Link href="/dashboard"
                            class="mb-2 inline-flex items-center gap-1.5 text-xs font-bold text-[#263646]/80 hover:text-[#263646] transition">
                            <span>←</span> Volver al inicio
                        </Link>
                        <h1 class="text-2xl font-bold text-[#263646]">
                            {{ esModoPropio ? 'Mis Publicaciones' : 'Publicaciones de la Comunidad' }}
                        </h1>
                        <p class="mt-1 text-xl text-[#FFF7ED]">
                            {{ esModoPropio
                                ? 'Listado consolidado de todo el contenido que has publicado en la plataforma.'
                                : 'Panel unificado de moderación y consulta de contenidos en todos los módulos.' }}
                        </p>
                    </div>
                </div>

                <!-- PESTAÑAS DE MÓDULOS -->
                <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-3">
                    <button type="button" @click="cambiarModulo('todos')"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition" :class="moduloSeleccionado === 'todos'
                            ? 'bg-[#263646] text-white shadow-sm'
                            : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'">
                        Todos ({{ totalesPorModulo.todos ?? 0 }})
                    </button>
                    <button type="button" @click="cambiarModulo('eventos')"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition" :class="moduloSeleccionado === 'eventos'
                            ? 'bg-[#263646] text-white shadow-sm'
                            : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'">
                        Eventos ({{ totalesPorModulo.eventos ?? 0 }})
                    </button>
                    <button type="button" @click="cambiarModulo('marketplace')"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition" :class="moduloSeleccionado === 'marketplace'
                            ? 'bg-[#263646] text-white shadow-sm'
                            : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'">
                        Marketplace ({{ totalesPorModulo.marketplace ?? 0 }})
                    </button>
                    <button type="button" @click="cambiarModulo('servicios')"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition" :class="moduloSeleccionado === 'servicios'
                            ? 'bg-[#263646] text-white shadow-sm'
                            : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'">
                        Servicios ({{ totalesPorModulo.servicios ?? 0 }})
                    </button>
                    <button type="button" @click="cambiarModulo('requerimientos')"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition" :class="moduloSeleccionado === 'requerimientos'
                            ? 'bg-[#263646] text-white shadow-sm'
                            : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'">
                        Requerimientos ({{ totalesPorModulo.requerimientos ?? 0 }})
                    </button>
                    <button type="button" @click="cambiarModulo('clasificados')"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition" :class="moduloSeleccionado === 'clasificados'
                            ? 'bg-[#263646] text-white shadow-sm'
                            : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'">
                        Clasificados ({{ totalesPorModulo.clasificados ?? 0 }})
                    </button>
                    <button type="button" @click="cambiarModulo('emprendimientos')"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition" :class="moduloSeleccionado === 'emprendimientos'
                            ? 'bg-[#263646] text-white shadow-sm'
                            : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'">
                        Emprendimientos ({{ totalesPorModulo.emprendimientos ?? 0 }})
                    </button>
                    <button type="button" @click="cambiarModulo('cursos')"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition" :class="moduloSeleccionado === 'cursos'
                            ? 'bg-[#263646] text-white shadow-sm'
                            : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'">
                        Cursos ({{ totalesPorModulo.cursos ?? 0 }})
                    </button>
                    <button type="button" @click="cambiarModulo('empleos')"
                        class="rounded-xl px-4 py-2 text-xs font-bold transition" :class="moduloSeleccionado === 'empleos'
                            ? 'bg-[#263646] text-white shadow-sm'
                            : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'">
                        Empleos ({{ totalesPorModulo.empleos ?? 0 }})
                    </button>
                </div>

                <!-- BÚSQUEDA, ESTADO Y CANTIDAD POR PÁGINA -->
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <input v-model="busqueda" @keyup.enter="aplicarFiltros" type="text"
                            :placeholder="esModoPropio ? 'Buscar en mis publicaciones...' : 'Buscar por título, autor o correo...'"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-slate-400" />
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <select v-model="estadoSeleccionado" @change="aplicarFiltros"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs sm:text-sm font-semibold text-slate-700 outline-none focus:border-slate-400">
                            <option value="todos">Todos los estados</option>
                            <option value="activo">Activos</option>
                            <option value="suspendido">Suspendidos</option>
                            <option value="pausado">Pausados</option>
                        </select>

                        <select v-model="porPagina" @change="aplicarFiltros"
                            class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs sm:text-sm font-semibold text-slate-700 outline-none focus:border-slate-400"
                            title="Elementos por página">
                            <option :value="15">15 por pág.</option>
                            <option :value="25">25 por pág.</option>
                            <option :value="50">50 por pág.</option>
                            <option :value="100">100 por pág.</option>
                        </select>

                        <button type="button" @click="aplicarFiltros"
                            class="rounded-xl bg-[#263646] px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm transition hover:bg-[#1d2936]">
                            Filtrar
                        </button>
                    </div>
                </div>

                <!-- TABLA DE RESULTADOS -->
                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs sm:text-sm text-slate-600">
                            <thead
                                class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-5 py-3.5">Módulo</th>
                                    <th class="px-5 py-3.5">Título</th>
                                    <th v-if="!esModoPropio" class="px-5 py-3.5">Publicado por</th>
                                    <th class="px-5 py-3.5">Estado</th>
                                    <th class="px-5 py-3.5">Fecha</th>
                                    <th class="px-5 py-3.5 text-right">Acción</th>
                                    <!-- Columna Destacado oculta para usuarios normales -->
                                    <th v-if="!esModoPropio" class="px-5 py-3.5 text-center">Destacado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="pub in items" :key="`${pub.modulo_key}-${pub.id}`"
                                    class="hover:bg-slate-50/60 transition">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex rounded-lg border px-2.5 py-1 text-[11px] font-bold"
                                            :class="colorModulo(pub.modulo_key)">
                                            {{ pub.modulo }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 font-semibold text-slate-800 max-w-xs truncate"
                                        :title="pub.titulo">
                                        {{ pub.titulo }}
                                    </td>
                                    <td v-if="!esModoPropio" class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-bold text-slate-800">{{ pub.autor }}</div>
                                        <div class="text-[11px] text-slate-400">{{ pub.autor_email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-bold capitalize"
                                            :class="colorEstado(pub.estado)">
                                            {{ pub.estado }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-slate-400 text-xs">
                                        {{ new Date(pub.fecha).toLocaleDateString('es-CO') }}
                                    </td>

                                    <td class="px-5 py-4 text-right whitespace-nowrap">
                                        <a :href="pub.url_ver" target="_blank"
                                            class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 transition">
                                            Ver publicación ↗
                                        </a>
                                    </td>

                                    <!-- Celda del Checkbox oculta para usuarios normales -->
                                    <td v-if="!esModoPropio" class="px-5 py-4 whitespace-nowrap text-center">
                                        <input type="checkbox" :checked="pub.es_destacado"
                                            @change="toggleDestacado(pub)"
                                            class="h-4 w-4 rounded border-slate-300 text-[#263646] focus:ring-[#263646] cursor-pointer"
                                            title="Marcar o desmarcar como destacado" />
                                    </td>
                                </tr>

                                <tr v-if="items.length === 0">
                                    <td :colspan="esModoPropio ? 5 : 7"
                                        class="px-5 py-12 text-center text-slate-400 font-medium">
                                        No se encontraron publicaciones con los criterios seleccionados.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- BARRA DE PAGINACIÓN COMPLETA -->
                    <div v-if="publicaciones.total > publicaciones.per_page"
                        class="flex flex-col items-center justify-between gap-4 border-t border-slate-200 bg-slate-50/50 px-5 py-4 sm:flex-row">
                        <p class="text-xs text-slate-500">
                            Mostrando del
                            <span class="font-bold text-slate-700">{{ publicaciones.from ?? 0 }}</span> al
                            <span class="font-bold text-slate-700">{{ publicaciones.to ?? 0 }}</span> de
                            <span class="font-bold text-slate-700">{{ publicaciones.total }}</span> resultados
                        </p>

                        <div class="flex flex-wrap items-center gap-1">
                            <template v-for="(link, index) in publicaciones.links" :key="index">
                                <Link v-if="link.url" :href="link.url" v-html="link.label"
                                    class="rounded-lg px-3 py-1.5 text-xs font-semibold transition" :class="link.active
                                        ? 'bg-[#263646] text-white shadow-xs'
                                        : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-100'"
                                    preserve-scroll />
                                <span v-else v-html="link.label"
                                    class="rounded-lg border border-transparent px-3 py-1.5 text-xs font-medium text-slate-300" />
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>