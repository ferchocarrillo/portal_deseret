<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { route } from 'ziggy-js';

// Los enlaces se activan únicamente cuando sus rutas existen en Laravel.
const tieneRutaMisCursos = route().has('cursos.misCursos');
const tieneRutaMisInscripciones = route().has('cursos.misInscripciones');

const props = defineProps({
    cursos: {
        type: Object,
        required: true,
    },
    filtros: {
        type: Object,
        default: () => ({
            buscar: '',
            modalidad: '',
        }),
    },
});

// Validación de bloqueo temporal para el usuario autenticado
const page = usePage();
const estaBloqueado = computed(() => {
    const usuario = page.props.auth?.user;
    if (!usuario?.bloqueado_hasta) return false;
    return new Date(usuario.bloqueado_hasta) > new Date();
});

const buscar = ref(props.filtros.buscar ?? '');
const modalidad = ref(props.filtros.modalidad ?? '');
let temporizador = null;

// ==========================================================
// FAVORITOS REACTIVOS
// ==========================================================
function alternarFavorito(curso) {
    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: curso.id,
            modulo: 'cursos',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                curso.es_favorito = !Boolean(curso.es_favorito);
            },
        }
    );
}

function filtrar() {
    clearTimeout(temporizador);
    temporizador = setTimeout(() => {
        router.get(
            route('cursos.index'),
            {
                buscar: buscar.value || undefined,
                modalidad: modalidad.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    }, 350);
}

watch([buscar, modalidad], filtrar);

/**
 * Solicitar inscripción en un curso de la comunidad.
 */
function inscribirse(curso) {
    if (curso.es_mi_curso) {
        return;
    }

    if (curso.ya_inscrito) {
        router.visit(route('cursos.misInscripciones'));
        return;
    }

    if (
        !curso.cupos_ilimitados &&
        curso.cupos_disponibles !== null &&
        curso.cupos_disponibles !== undefined &&
        Number(curso.cupos_disponibles) <= 0
    ) {
        return;
    }

    router.get(route('cursos.inscripcion.confirmar', curso.id));
}

function limpiarFiltros() {
    buscar.value = '';
    modalidad.value = '';
}

function formatoPrecio(precio) {
    if (precio === null || precio === undefined || precio === '') {
        return 'Precio no definido';
    }
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
    }).format(Number(precio));
}

function etiquetaModalidad(valor) {
    const modalidades = {
        virtual: 'Virtual',
        presencial: 'Presencial',
        hibrido: 'Híbrido',
    };
    return modalidades[valor] ?? valor ?? 'Sin modalidad';
}

function imagenCurso(imagen) {
    if (!imagen) return null;
    if (
        imagen.startsWith('http://') ||
        imagen.startsWith('https://')
    ) {
        return imagen;
    }
    return `/storage/${imagen.replace(/^\/+/, '')}`;
}

function etiquetaCupos(curso) {
    if (curso.cupos_ilimitados) {
        return 'Inscripción abierta';
    }
    const disponibles = curso.cupos_disponibles;
    if (
        disponibles !== null &&
        disponibles !== undefined &&
        disponibles !== ''
    ) {
        const cantidad = Number(disponibles);
        if (cantidad <= 0) return 'Cupos agotados';
        if (cantidad <= 5) return '¡Últimos cupos!';
        if (cantidad <= 10) return 'Pocos cupos disponibles';
        return 'Cupos disponibles';
    }
    const capacidad = curso.cantidad_cupos;
    if (
        capacidad === null ||
        capacidad === undefined ||
        capacidad === ''
    ) {
        return 'Cupos por confirmar';
    }
    const total = Number(capacidad);
    if (total <= 0) return 'Cupos agotados';
    if (total <= 5) return 'Últimos cupos';
    return 'Cupos disponibles';
}

function etiquetaDuracion(curso) {
    const horas =
        curso.intensidad_horaria ??
        curso.horas ??
        curso.duracion_horas;
    if (horas === null || horas === undefined || horas === '') {
        return null;
    }
    return `${horas} ${Number(horas) === 1 ? 'hora' : 'horas'}`;
}
</script>

<template>

    <Head title="Cursos | Portal Deseret" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- =========================================================
                 ENCABEZADO DE SECCIÓN CON TONO NARANJA TRANSLÚCIDO
            ========================================================== -->
            <section
                class="border-b border-orange-200/80 bg-orange-100/25 backdrop-blur-md shadow-lg shadow-orange-500/10">
                <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-3xl">
                            <!-- BADGE IDENTIFICADOR NARANJA -->
                            <div
                                class="mb-3 inline-flex items-center gap-2 rounded-full border border-orange-300/80 bg-white/90 px-3.5 py-1 shadow-sm">
                                <span class="text-base">📚</span>
                                <span class="text-xs font-black uppercase tracking-wider text-orange-950">
                                    Oportunidades de Formación
                                </span>
                            </div>
                            <h1 class="text-3xl font-black tracking-tight text-[#172A3D] sm:text-4xl">
                                Encuentra tu próximo curso
                            </h1>
                            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-700 font-medium sm:text-base">
                                Explora cursos de la comunidad, inscríbete en nuevas experiencias de aprendizaje
                                y administra los cursos que has publicado.
                            </p>
                        </div>

                        <!-- ACCIONES DE CABECERA -->
                        <div class="flex flex-wrap gap-3">
                            <Link v-if="tieneRutaMisInscripciones" :href="route('cursos.misInscripciones')"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300/90 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-[#172A3D] shadow-sm transition hover:bg-orange-50">
                                Mis inscripciones
                            </Link>
                            <button v-else type="button" disabled
                                title="La ruta de Mis inscripciones aún no está registrada."
                                class="inline-flex cursor-not-allowed items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-400">
                                Mis inscripciones
                            </button>

                            <Link v-if="tieneRutaMisCursos" :href="route('cursos.misCursos')"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300/90 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-[#172A3D] shadow-sm transition hover:bg-orange-50">
                                Mis cursos
                            </Link>
                            <button v-else type="button" disabled title="La ruta de Mis cursos aún no está registrada."
                                class="inline-flex cursor-not-allowed items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-400">
                                Mis cursos
                            </button>

                            <!-- Botón de Publicación protegido con estatus de bloqueo -->
                            <template v-if="estaBloqueado">
                                <span
                                    class="inline-flex items-center justify-center rounded-xl bg-slate-400 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md cursor-not-allowed"
                                    title="Tu cuenta tiene un bloqueo temporal activo y no puede crear publicaciones">
                                    Publicación restringida
                                </span>
                            </template>
                            <template v-else>
                                <Link :href="route('cursos.create')"
                                    class="inline-flex items-center justify-center rounded-xl bg-[#172A3D] px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md shadow-[#172A3D]/20 transition hover:bg-orange-600 hover:text-white">
                                    + Publicar curso
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =========================================================
                 CONTENIDO PRINCIPAL
            ========================================================== -->
            <main class="mx-auto max-w-7xl px-6 py-8 lg:px-8 space-y-8 bg-orange-50 rounded-xl p-3 mt-2">

                <!-- BUSCADOR -->
                <section class="rounded-2xl border border-slate-300 bg-white p-4 sm:p-5 shadow-xl shadow-orange-500/10">
                    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="font-bold text-[#172A3D]">
                                Buscar cursos
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-500">
                                Encuentra cursos por título o modalidad.
                            </p>
                        </div>
                        <button v-if="buscar || modalidad" type="button"
                            class="text-xs font-bold text-orange-600 transition hover:text-orange-800 underline"
                            @click="limpiarFiltros">
                            Limpiar filtros
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="buscar" class="mb-1.5 block text-xs font-bold text-slate-700">
                                Nombre del curso
                            </label>
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                                <input id="buscar" v-model="buscar" type="search" placeholder="Buscar por título..."
                                    class="w-full h-11 rounded-xl border border-slate-300 bg-white py-2.5 pl-11 pr-4 text-sm font-semibold text-[#172A3D] placeholder-slate-400 outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100" />
                            </div>
                        </div>

                        <div>
                            <label for="modalidad" class="mb-1.5 block text-xs font-bold text-slate-700">
                                Modalidad
                            </label>
                            <select id="modalidad" v-model="modalidad"
                                class="w-full h-11 rounded-xl border border-slate-300 bg-white px-3 text-xs sm:text-sm font-medium text-[#172A3D] outline-none transition focus:border-orange-500">
                                <option value="">Todas las modalidades</option>
                                <option value="virtual">Virtual</option>
                                <option value="presencial">Presencial</option>
                                <option value="hibrido">Híbrido</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- RESUMEN DE RESULTADOS -->
                <div
                    class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-1 rounded-2xl border border-slate-300 bg-white p-5 shadow-xl shadow-orange-500/10">
                    <p class="text-xs sm:text-sm text-slate-600 p-3">
                        <span class="font-bold text-[#172A3D]">
                            {{ cursos.total ?? 0 }}
                        </span>
                        {{ cursos.total === 1 ? 'curso encontrado' : 'cursos encontrados' }}
                    </p>
                    <p class="text-[11px] text-slate-500">
                        Cursos publicados por la comunidad.
                    </p>
                </div>

                <!-- ============================================================
                     TARJETAS DE CURSOS
                ============================================================ -->
                <div v-if="cursos.data && cursos.data.length"
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <article v-for="curso in cursos.data" :key="curso.id"
                        class="group relative flex h-full flex-col overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-xl shadow-orange-500/10 transition duration-300 hover:-translate-y-1 hover:border-orange-400 hover:shadow-2xl hover:shadow-orange-500/20">

                        <!-- BOTÓN FLOTANTE DE FAVORITOS (SVG REACTIVO) -->
                        <button type="button" @click.stop="alternarFavorito(curso)"
                            class="absolute right-3 top-3 z-20 flex h-9 w-9 items-center justify-center rounded-full bg-white/95 shadow-md backdrop-blur transition hover:scale-110"
                            :class="curso.es_favorito ? 'bg-red-50 ring-1 ring-red-200' : 'hover:bg-slate-50'"
                            :title="curso.es_favorito ? 'Quitar de favoritos' : 'Guardar en favoritos'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="h-5 w-5 transition-colors"
                                :class="curso.es_favorito ? 'fill-red-600 text-red-600' : 'fill-none text-slate-400 hover:text-red-500'">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                        </button>

                        <!-- PORTADA DEL CURSO -->
                        <div class="relative h-48 overflow-hidden bg-slate-100">
                            <img v-if="imagenCurso(curso.imagen)" :src="imagenCurso(curso.imagen)" :alt="curso.titulo"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                            <div v-else
                                class="flex h-full w-full flex-col items-center justify-center bg-gradient-to-br from-orange-100 to-amber-50 text-orange-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                                </svg>
                                <span class="mt-2 text-xs font-bold uppercase tracking-wider">
                                    Curso comunitario
                                </span>
                            </div>

                            <div class="absolute left-3 top-3">
                                <span
                                    class="inline-flex items-center rounded-full bg-white/95 px-3 py-1 text-xs font-bold text-[#172A3D] shadow-sm backdrop-blur">
                                    {{ curso.tipo === 'externo' ? 'Curso externo' : 'Curso propio' }}
                                </span>
                            </div>

                            <div class="absolute right-14 top-3">
                                <span
                                    class="inline-flex items-center rounded-full bg-slate-900/85 px-3 py-1 text-xs font-bold text-white shadow-sm backdrop-blur">
                                    {{ etiquetaModalidad(curso.modalidad) }}
                                </span>
                            </div>
                        </div>

                        <!-- CONTENIDO DEL CURSO -->
                        <div class="flex flex-1 flex-col p-5">
                            <div class="flex-1">
                                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                    <span v-if="curso.es_gratuito"
                                        class="rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                                        Gratis
                                    </span>
                                    <span v-else
                                        class="rounded-md bg-orange-50 px-2.5 py-1 text-xs font-bold text-orange-950 border border-orange-200">
                                        {{ formatoPrecio(curso.precio) }}
                                    </span>

                                    <span class="text-xs font-semibold text-slate-500">
                                        {{ etiquetaCupos(curso) }}
                                    </span>
                                </div>

                                <Link :href="route('cursos.show', curso.id)">
                                    <h3
                                        class="line-clamp-2 text-lg font-black text-[#172A3D] transition group-hover:text-orange-600">
                                        {{ curso.titulo }}
                                    </h3>
                                </Link>

                                <p class="mt-2 line-clamp-3 text-xs sm:text-sm leading-relaxed text-slate-600">
                                    {{ curso.descripcion || 'Este curso no tiene una descripción disponible por el momento.' }}
                                </p>

                                <div
                                    class="mt-4 space-y-2 border-t border-slate-100 pt-3 text-xs text-slate-600 font-medium">
                                    <div v-if="etiquetaDuracion(curso)" class="flex items-center gap-2">
                                        <span>⏱️</span>
                                        <span>Duración: <strong>{{ etiquetaDuracion(curso) }}</strong></span>
                                    </div>
                                    <div v-if="curso.usuario?.name" class="flex items-center gap-2">
                                        <span>👤</span>
                                        <span class="truncate">Publicado por: <strong class="text-slate-800">{{
                                            curso.usuario.name }}</strong></span>
                                    </div>
                                </div>
                            </div>

                            <!-- ACCIONES -->
                            <div class="mt-5 border-t border-slate-100 pt-4">
                                <!-- Curso externo -->
                                <a v-if="curso.tipo === 'externo' && curso.url_externa" :href="curso.url_externa"
                                    target="_blank" rel="noopener noreferrer"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white transition hover:bg-orange-700 shadow-xs">
                                    Ver curso externo ↗
                                </a>

                                <!-- Curso creado por el usuario -->
                                <div v-else-if="curso.es_mi_curso"
                                    class="rounded-xl bg-slate-100 px-4 py-2.5 text-center text-xs sm:text-sm font-bold text-slate-700">
                                    Tu publicación
                                </div>

                                <!-- Inscripción ya activada -->
                                <div v-else-if="curso.estado_inscripcion === 'activa'"
                                    class="rounded-xl bg-emerald-100 px-4 py-2.5 text-center text-xs sm:text-sm font-bold text-emerald-800">
                                    ✓ Inscripción activa
                                </div>

                                <!-- Inscripción pendiente -->
                                <div v-else-if="curso.ya_inscrito"
                                    class="rounded-xl bg-amber-100 px-4 py-2.5 text-center text-xs sm:text-sm font-bold text-amber-800">
                                    Inscripción pendiente
                                </div>

                                <!-- Sin cupos -->
                                <button
                                    v-else-if="!curso.cupos_ilimitados && curso.cupos_disponibles !== null && Number(curso.cupos_disponibles) <= 0"
                                    type="button" disabled
                                    class="inline-flex w-full cursor-not-allowed items-center justify-center rounded-xl bg-slate-100 px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-400">
                                    Cupos agotados
                                </button>

                                <!-- Inscribirme -->
                                <button v-else type="button" @click="inscribirse(curso)"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#172A3D] px-4 py-2.5 text-xs sm:text-sm font-bold text-white transition hover:bg-orange-600 shadow-xs">
                                    Inscribirme
                                </button>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- SIN RESULTADOS -->
                <div v-else
                    class="rounded-2xl border border-slate-300 bg-white px-6 py-14 text-center shadow-xl shadow-orange-500/10">
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 text-orange-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-bold text-[#172A3D]">
                        No encontramos cursos
                    </h3>
                    <p class="mx-auto mt-2 max-w-md text-xs sm:text-sm leading-6 text-slate-500">
                        <span v-if="buscar || modalidad">
                            Prueba cambiando los filtros para encontrar otros cursos.
                        </span>
                        <span v-else>
                            Todavía no hay cursos publicados. Vuelve pronto para descubrir nuevas oportunidades de
                            aprendizaje.
                        </span>
                    </p>
                    <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                        <button v-if="buscar || modalidad" type="button"
                            class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50 transition"
                            @click="limpiarFiltros">
                            Limpiar filtros
                        </button>

                        <!-- Botón de estado vacío protegido con estatus de bloqueo -->
                        <template v-if="estaBloqueado">
                            <span
                                class="inline-flex items-center justify-center rounded-xl bg-slate-400 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md cursor-not-allowed">
                                Publicación restringida
                            </span>
                        </template>
                        <template v-else>
                            <Link :href="route('cursos.create')"
                                class="inline-flex items-center justify-center rounded-xl bg-orange-600 px-5 py-2.5 text-xs sm:text-sm font-bold text-white transition hover:bg-orange-700 shadow-md">
                                Publicar un curso
                            </Link>
                        </template>
                    </div>
                </div>

                <!-- PAGINACIÓN -->
                <div v-if="cursos.links && cursos.links.length > 3" class="mt-8 flex flex-wrap justify-center gap-2">
                    <template v-for="(link, index) in cursos.links" :key="index">
                        <Link v-if="link.url" :href="link.url" preserve-scroll
                            class="rounded-lg border px-3 py-2 text-xs sm:text-sm font-bold transition shadow-sm"
                            :class="link.active
                                ? 'border-[#172A3D] bg-[#172A3D] text-white'
                                : 'border-slate-300 bg-white text-[#172A3D] hover:border-orange-500 hover:bg-orange-50'"
                            v-html="link.label" />
                        <span v-else
                            class="rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-xs sm:text-sm font-medium text-slate-400"
                            v-html="link.label" />
                    </template>
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