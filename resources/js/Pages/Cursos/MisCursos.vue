<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    cursos: {
        type: Object,
        required: true,
    },
});

// Validación de bloqueo temporal para el usuario autenticado
const page = usePage();
const estaBloqueado = computed(() => {
    const usuario = page.props.auth?.user;
    if (!usuario?.bloqueado_hasta) return false;
    return new Date(usuario.bloqueado_hasta) > new Date();
});

function formatoFecha(fecha) {
    if (!fecha) return 'Sin fecha';

    const soloFecha = String(fecha).substring(0, 10);
    const [anio, mes, dia] = soloFecha.split('-');

    if (!anio || !mes || !dia) return fecha;

    return `${dia}/${mes}/${anio}`;
}

function formatoFechaHora(fecha) {
    if (!fecha) return 'Sin fecha';

    const valor = String(fecha).replace('T', ' ');
    const partes = valor.split(' ');

    const fechaParte = partes[0];
    const horaParte = partes[1] ?? '';

    const [anio, mes, dia] = fechaParte.split('-');

    if (!anio || !mes || !dia) return fecha;

    return `${dia}/${mes}/${anio}${horaParte ? ` ${horaParte.substring(0, 5)}` : ''}`;
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

function etiquetaEstado(estado) {
    const estados = {
        publicado: 'Publicado',
        borrador: 'Borrador',
        finalizado: 'Finalizado',
        cancelado: 'Cancelado',
    };

    return estados[estado] ?? estado ?? 'Sin estado';
}

function claseEstado(estado) {
    const clases = {
        publicado: 'bg-emerald-50 text-emerald-800 border border-emerald-200',
        borrador: 'bg-amber-50 text-amber-900 border border-amber-200',
        finalizado: 'bg-slate-100 text-slate-700 border border-slate-200',
        cancelado: 'bg-red-50 text-red-800 border border-red-200',
    };

    return clases[estado] ?? 'bg-slate-100 text-slate-700 border border-slate-200';
}

function etiquetaCupos(curso) {
    if (curso.cupos_ilimitados) {
        return 'Ilimitados';
    }

    if (
        curso.cupos_disponibles !== null &&
        curso.cupos_disponibles !== undefined
    ) {
        if (Number(curso.cupos_disponibles) <= 0) {
            return 'Agotados';
        }

        return `${curso.cupos_disponibles} disponibles`;
    }

    if (
        curso.cantidad_cupos !== null &&
        curso.cantidad_cupos !== undefined
    ) {
        return `${curso.cantidad_cupos} cupos`;
    }

    return 'Sin límite definido';
}

function claseCupos(curso) {
    if (curso.cupos_ilimitados) {
        return 'text-emerald-700';
    }

    if (
        curso.cupos_disponibles !== null &&
        curso.cupos_disponibles !== undefined
    ) {
        if (Number(curso.cupos_disponibles) <= 0) {
            return 'text-red-700';
        }

        if (Number(curso.cupos_disponibles) <= 5) {
            return 'text-amber-700';
        }
    }

    return 'text-slate-700';
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
</script>

<template>

    <Head title="Mis cursos | Portal Deseret" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- =========================================================
                 ENCABEZADO DE SECCIÓN CON TONO NARANJA TRANSLÚCIDO
            ========================================================== -->
            <section
                class="border-b border-orange-200/80 bg-orange-100/25 backdrop-blur-md shadow-lg shadow-orange-500/10">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="max-w-3xl">
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-orange-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-orange-950">
                                    Administración de formación
                                </span>
                            </div>

                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Mis cursos
                            </h1>

                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Administra los cursos que has publicado, consulta sus inscripciones y revisa la
                                disponibilidad de cupos.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <Link :href="route('cursos.index')"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition hover:bg-orange-50 hover:border-orange-300">
                                Ver cursos
                            </Link>

                            <!-- Botón superior protegido con estatus de bloqueo -->
                            <template v-if="estaBloqueado">
                                <span
                                    class="inline-flex items-center justify-center rounded-xl bg-slate-400 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md cursor-not-allowed"
                                    title="Tu cuenta tiene un bloqueo temporal activo y no puede crear publicaciones">
                                    Publicación restringida
                                </span>
                            </template>
                            <template v-else>
                                <Link :href="route('cursos.create')"
                                    class="inline-flex items-center justify-center rounded-xl bg-orange-600 px-5 py-2.5 text-xs sm:text-sm font-black text-white shadow-md shadow-orange-500/25 transition hover:bg-orange-700">
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
            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-6">

                <!-- Resumen -->
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-1">
                    <p class="text-xs sm:text-sm text-slate-600 font-medium">
                        <span class="font-bold text-[#172A3D]">
                            {{ cursos.total ?? 0 }}
                        </span>
                        {{ cursos.total === 1 ? 'curso publicado' : 'cursos publicados' }}
                    </p>

                    <p class="text-[11px] text-slate-500 font-medium">
                        Estos cursos pertenecen a tu cuenta.
                    </p>
                </div>

                <!-- Cursos -->
                <div v-if="cursos.data && cursos.data.length"
                    class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <article v-for="curso in cursos.data" :key="curso.id"
                        class="group flex h-full flex-col overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15 transition duration-300 hover:-translate-y-1 hover:border-orange-400">

                        <!-- Imagen -->
                        <div class="relative h-48 overflow-hidden bg-gradient-to-br from-orange-100 to-slate-50">
                            <img v-if="imagenCurso(curso.imagen)" :src="imagenCurso(curso.imagen)" :alt="curso.titulo"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />

                            <div v-else class="flex h-full w-full flex-col items-center justify-center text-orange-700">
                                <span class="text-3xl">📚</span>
                                <span class="mt-2 text-xs font-bold uppercase tracking-wider text-slate-600">
                                    Curso propio
                                </span>
                            </div>

                            <!-- Estado -->
                            <div class="absolute left-3 top-3">
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold shadow-xs backdrop-blur"
                                    :class="claseEstado(curso.estado)">
                                    {{ etiquetaEstado(curso.estado) }}
                                </span>
                            </div>

                            <!-- Modalidad -->
                            <div class="absolute right-3 top-3">
                                <span
                                    class="inline-flex items-center rounded-full bg-slate-900/85 px-3 py-1 text-xs font-bold text-white shadow-xs backdrop-blur">
                                    {{ etiquetaModalidad(curso.modalidad) }}
                                </span>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="flex flex-1 flex-col p-5">
                            <div class="flex-1">
                                <!-- Tipo / precio -->
                                <div class="mb-3 flex flex-wrap items-center gap-2">
                                    <span
                                        class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 border border-slate-200">
                                        {{ curso.tipo === 'externo' ? 'Curso externo' : 'Curso propio' }}
                                    </span>

                                    <span v-if="curso.es_gratuito"
                                        class="rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 border border-emerald-200">
                                        Gratis
                                    </span>

                                    <span v-else
                                        class="rounded-md bg-orange-50 px-2.5 py-1 text-xs font-bold text-orange-950 border border-orange-200">
                                        {{ formatoPrecio(curso.precio) }}
                                    </span>
                                </div>

                                <!-- Título -->
                                <h3
                                    class="line-clamp-2 text-lg font-black text-[#172A3D] transition group-hover:text-orange-600">
                                    {{ curso.titulo }}
                                </h3>

                                <!-- Descripción -->
                                <p class="mt-2 line-clamp-3 text-xs sm:text-sm leading-relaxed text-slate-600">
                                    {{ curso.descripcion || 'Este curso no tiene una descripción.' }}
                                </p>

                                <!-- Información -->
                                <div
                                    class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-xs text-slate-600 font-medium">
                                    <!-- Inscripciones -->
                                    <div class="flex items-start gap-2">
                                        <span class="text-orange-600 font-bold">👥</span>
                                        <span>
                                            <strong class="text-slate-800">Inscritos:</strong>
                                            {{ curso.inscripciones_count }}
                                            <span class="text-slate-400">
                                                ({{ curso.inscripciones_activas_count }} activas)
                                            </span>
                                        </span>
                                    </div>

                                    <!-- Cupos -->
                                    <div class="flex items-start gap-2" :class="claseCupos(curso)">
                                        <span class="font-bold">🏷️</span>
                                        <span>
                                            <strong class="text-slate-800">Cupos:</strong>
                                            {{ etiquetaCupos(curso) }}
                                        </span>
                                    </div>

                                    <!-- Fecha límite -->
                                    <div v-if="curso.fecha_limite_inscripcion" class="flex items-start gap-2">
                                        <span>📅</span>
                                        <span>
                                            <strong class="text-slate-800">Inscripciones hasta:</strong>
                                            {{ formatoFechaHora(curso.fecha_limite_inscripcion) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="mt-5 space-y-2.5 border-t border-slate-100 pt-4">
                                <Link :href="route('cursos.inscripciones.index', curso.id)"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-xs sm:text-sm font-black text-white transition hover:bg-orange-700 shadow-xs">
                                    Ver inscripciones
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>

                                <div class="grid grid-cols-2 gap-2">
                                    <Link :href="route('cursos.edit', curso.id)"
                                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 shadow-xs">
                                        Editar
                                    </Link>

                                    <Link :href="route('cursos.show', curso.id)"
                                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 shadow-xs">
                                        Ver curso
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Sin cursos -->
                <div v-else
                    class="rounded-3xl border border-slate-300 bg-white px-6 py-16 text-center shadow-2xl shadow-orange-500/15">
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 text-3xl text-orange-600">
                        📚
                    </div>

                    <h3 class="mt-4 text-lg font-bold text-[#172A3D]">
                        Todavía no has publicado cursos
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-xs sm:text-sm leading-relaxed text-slate-500 font-medium">
                        Publica tu primer curso para comenzar a compartir conocimientos con la comunidad.
                    </p>

                    <div class="mt-6">
                        <!-- Botón de estado vacío protegido con estatus de bloqueo -->
                        <template v-if="estaBloqueado">
                            <span
                                class="inline-flex items-center justify-center rounded-xl bg-slate-400 px-5 py-3 text-xs sm:text-sm font-black text-white shadow-md cursor-not-allowed">
                                Publicación restringida
                            </span>
                        </template>
                        <template v-else>
                            <Link :href="route('cursos.create')"
                                class="inline-flex items-center justify-center rounded-xl bg-orange-600 px-5 py-3 text-xs sm:text-sm font-black text-white shadow-md transition hover:bg-orange-700">
                                Publicar mi primer curso
                            </Link>
                        </template>
                    </div>
                </div>

                <!-- Paginación -->
                <div v-if="cursos.links && cursos.links.length > 3"
                    class="flex flex-wrap items-center justify-center gap-2 pt-4">
                    <template v-for="(link, index) in cursos.links" :key="index">
                        <Link v-if="link.url" :href="link.url" preserve-scroll
                            class="rounded-lg border px-3 py-2 text-xs sm:text-sm font-bold transition shadow-xs"
                            :class="link.active
                                ? 'border-[#172A3D] bg-[#172A3D] text-white'
                                : 'border-slate-300 bg-white text-slate-700 hover:border-orange-500 hover:bg-orange-50'"
                            v-html="link.label" />

                        <span v-else
                            class="cursor-not-allowed rounded-lg border border-slate-100 bg-slate-50 px-3 py-2 text-xs text-slate-400 font-medium"
                            v-html="link.label"></span>
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