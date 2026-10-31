<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import { route } from 'ziggy-js'

const props = defineProps({
    curso: {
        type: Object,
        required: true,
    },
})

// ==========================================================
// ESTADO REACTIVO DE FAVORITOS
// ==========================================================
const esFavorito = ref(Boolean(props.curso.es_favorito))
const procesandoFavorito = ref(false)

// Sincronizar si las props cambian desde el servidor
watch(
    () => props.curso.es_favorito,
    (nuevoValor) => {
        esFavorito.value = Boolean(nuevoValor)
    }
)

const alternarFavorito = () => {
    if (procesandoFavorito.value) return

    procesandoFavorito.value = true

    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: props.curso.id,
            modulo: 'cursos',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                esFavorito.value = !esFavorito.value
                props.curso.es_favorito = esFavorito.value
            },
            onFinish: () => {
                procesandoFavorito.value = false
            },
        }
    )
}

// ==========================================================
// LÓGICA DE INSCRIPCIÓN Y PERMISOS
// ==========================================================
const estaInscrito = computed(() => {
    return !!props.curso.mi_inscripcion
})

const puedeInscribirse = computed(() => {
    if (props.curso.estado !== 'publicado') {
        return false
    }

    if (props.curso.fecha_limite_inscripcion) {
        const limite = new Date(props.curso.fecha_limite_inscripcion)
        const hoy = new Date()

        if (limite < hoy) {
            return false
        }
    }

    if (
        props.curso.capacidad &&
        props.curso.inscritos_activos >= props.curso.capacidad
    ) {
        return false
    }

    return !estaInscrito.value
})

const esPropietario = computed(() => {
    return props.curso.es_propietario === true
})

const formatoFecha = (fecha) => {
    if (!fecha) {
        return 'No definida'
    }

    return new Date(fecha).toLocaleDateString('es-CO', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}

const formatoPrecio = (precio) => {
    if (!precio || Number(precio) === 0) {
        return 'Gratis'
    }

    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
    }).format(precio)
}

const inscribirse = () => {
    if (!puedeInscribirse.value) {
        return
    }

    router.post(
        route('cursos.inscribirse', props.curso.id),
        {},
        {
            preserveScroll: true,
        }
    )
}
</script>

<template>

    <Head :title="`${curso.titulo} | Portal Deseret`" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-transparent text-[#172A3D]">

            <!-- =========================================================
                 ENCABEZADO DE SECCIÓN CON TONO NARANJA TRANSLÚCIDO
            ========================================================== -->
            <section
                class="border-b border-orange-200/80 bg-orange-100/25 backdrop-blur-md shadow-lg shadow-orange-500/10">
                <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-orange-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-orange-950">
                                    Detalle de formación
                                </span>
                            </div>
                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Información completa del curso
                            </h1>
                        </div>

                        <Link :href="route('cursos.index')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition hover:bg-orange-50 hover:border-orange-300">
                            ← Volver a cursos
                        </Link>
                    </div>
                </div>
            </section>

            <!-- =========================================================
                 CONTENIDO PRINCIPAL
            ========================================================== -->
            <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 bg-orange-50 rounded-xl p-3 mt-2">

                <!-- TARJETA PRINCIPAL DEL CURSO -->
                <div
                    class="overflow-hidden rounded-3xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">

                    <!-- Imagen principal -->
                    <div v-if="curso.imagen" class="max-h-[420px] w-full overflow-hidden bg-slate-100">
                        <img :src="curso.imagen" :alt="curso.titulo" class="max-h-[420px] w-full object-contain" />
                    </div>

                    <div class="p-6 sm:p-8">

                        <!-- Encabezado del curso -->
                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <div class="mb-3 flex flex-wrap gap-2">
                                    <span v-if="curso.tipo"
                                        class="rounded-full bg-orange-100 px-3 py-1 text-xs font-bold text-orange-950 border border-orange-200">
                                        {{ curso.tipo === 'externo' ? 'Curso externo' : 'Curso propio' }}
                                    </span>

                                    <span v-if="curso.modalidad"
                                        class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-800 border border-blue-200">
                                        {{ curso.modalidad }}
                                    </span>

                                    <span v-if="curso.estado"
                                        class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800 border border-emerald-200">
                                        {{ curso.estado }}
                                    </span>
                                </div>

                                <h2 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-4xl">
                                    {{ curso.titulo }}
                                </h2>

                                <p v-if="curso.descripcion_corta"
                                    class="mt-3 text-sm sm:text-base text-slate-600 font-medium">
                                    {{ curso.descripcion_corta }}
                                </p>
                            </div>

                            <!-- Columna Derecha Superior: Precio + Botón de Favorito Rápido -->
                            <div class="flex shrink-0 flex-col gap-3 sm:flex-row lg:flex-col lg:items-end">
                                <div
                                    class="w-full rounded-2xl border border-orange-200 bg-orange-50/50 p-4 sm:p-5 lg:text-right">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">
                                        Inversión
                                    </p>
                                    <p class="mt-1 text-2xl font-black text-orange-700">
                                        {{ formatoPrecio(curso.precio) }}
                                    </p>
                                </div>

                                <!-- BOTÓN DE FAVORITO EN CABECERA (MARCAR / DESMARCAR) -->
                                <button type="button" @click="alternarFavorito" :disabled="procesandoFavorito"
                                    class="inline-flex w-full items-center justify-center gap-2.5 rounded-xl px-4 py-3 text-xs sm:text-sm font-black transition shadow-md disabled:opacity-60"
                                    :class="esFavorito
                                        ? 'bg-amber-400 text-[#172A3D] shadow-amber-400/30 hover:bg-amber-500'
                                        : 'bg-amber-100/70 text-[#172A3D] border border-amber-300 hover:bg-amber-200/80'"
                                    :title="esFavorito ? 'Haz clic para quitar de favoritos' : 'Haz clic para guardar en favoritos'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" class="h-5 w-5 shrink-0 transition-transform duration-200"
                                        :class="esFavorito ? 'fill-red-600 text-red-600 scale-110' : 'fill-none text-slate-600'">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                    <span>{{ esFavorito ? 'Guardado en favoritos' : 'Guardar en favoritos' }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Grid de información rápida -->
                        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Modalidad
                                </p>
                                <p class="mt-1 font-bold text-[#172A3D]">
                                    {{ curso.modalidad || 'No definida' }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Intensidad
                                </p>
                                <p class="mt-1 font-bold text-[#172A3D]">
                                    {{ curso.intensidad_horaria ? `${curso.intensidad_horaria} horas` : 'No definida' }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Cupos
                                </p>
                                <p class="mt-1 font-bold text-[#172A3D]">
                                    {{ curso.capacidad ? `${curso.inscritos_activos || 0} / ${curso.capacidad}` :
                                        'Ilimitados' }}
                                </p>
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                                    Inscripción hasta
                                </p>
                                <p class="mt-1 font-bold text-[#172A3D]">
                                    {{ formatoFecha(curso.fecha_limite_inscripcion) }}
                                </p>
                            </div>
                        </div>

                        <!-- Sección de contenido detallado y barra lateral -->
                        <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-3">

                            <!-- Descripción -->
                            <div class="lg:col-span-2">
                                <h3 class="text-xl font-black text-[#172A3D]">
                                    Sobre este curso
                                </h3>
                                <div
                                    class="mt-4 whitespace-pre-line text-sm sm:text-base leading-relaxed text-slate-600 font-medium">
                                    {{ curso.descripcion || 'El creador aún no ha agregado una descripción detallada.'
                                    }}
                                </div>
                            </div>

                            <!-- Información lateral -->
                            <aside class="space-y-6">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50/90 p-5 shadow-xs">
                                    <h3 class="font-black text-[#172A3D]">
                                        Detalles clave
                                    </h3>
                                    <dl class="mt-4 space-y-4 text-sm">
                                        <div>
                                            <dt class="text-xs font-bold uppercase text-slate-400">Publicado desde</dt>
                                            <dd class="mt-0.5 font-bold text-slate-800">{{
                                                formatoFecha(curso.publicado_desde) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-bold uppercase text-slate-400">Publicado hasta</dt>
                                            <dd class="mt-0.5 font-bold text-slate-800">{{
                                                formatoFecha(curso.publicado_hasta) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-bold uppercase text-slate-400">Fecha límite de
                                                inscripción</dt>
                                            <dd class="mt-0.5 font-bold text-slate-800">{{
                                                formatoFecha(curso.fecha_limite_inscripcion) }}</dd>
                                        </div>
                                        <div v-if="curso.url_externa">
                                            <dt class="text-xs font-bold uppercase text-slate-400">Información externa
                                            </dt>
                                            <dd class="mt-1">
                                                <a :href="curso.url_externa" target="_blank" rel="noopener noreferrer"
                                                    class="font-bold text-orange-600 hover:text-orange-800 underline">
                                                    Ver sitio externo ↗
                                                </a>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- BOTÓN DE DENUNCIA / REPORTE DE INCIDENTE -->
                                <div class="rounded-2xl border border-red-200 bg-red-50/50 p-4 text-center shadow-xs">
                                    <Link
                                        :href="route('denuncias.create', { modulo: 'cursos', reportable_type: 'App\\Models\\Curso', reportable_id: curso.id })"
                                        class="inline-flex items-center justify-center gap-2 text-xs font-bold text-red-700 transition hover:text-red-900 w-full py-1">
                                        🚨 Reportar este curso
                                    </Link>
                                </div>
                            </aside>
                        </div>

                        <!-- Instructor / Creador -->
                        <div v-if="curso.usuario" class="mt-10 border-t border-slate-200 pt-8">
                            <h3 class="text-xl font-black text-[#172A3D]">
                                Instructor / creador
                            </h3>
                            <div class="mt-4 flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-lg font-black text-orange-800">
                                    {{ (curso.usuario.name || 'U').charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="font-bold text-[#172A3D]">
                                        {{ curso.usuario.name }}
                                    </p>
                                    <p v-if="curso.usuario.email" class="text-xs sm:text-sm text-slate-500 font-medium">
                                        {{ curso.usuario.email }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Estado de inscripción del usuario -->
                        <div v-if="estaInscrito"
                            class="mt-8 rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-xs">
                            <div class="flex items-start gap-3">
                                <div
                                    class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-white font-bold">
                                    ✓
                                </div>
                                <div>
                                    <p class="font-bold text-emerald-900">
                                        Ya estás inscrito en este curso
                                    </p>
                                    <p class="mt-1 text-xs sm:text-sm text-emerald-700 font-medium">
                                        Estado de inscripción: <strong class="capitalize">{{ curso.mi_inscripcion.estado
                                        }}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- =========================================================
                             BARRA INFERIOR DE ACCIONES
                        ========================================================== -->
                        <div
                            class="mt-8 flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:flex-wrap sm:items-center">

                            <!-- Inscribirme -->
                            <button v-if="puedeInscribirse" type="button"
                                class="inline-flex items-center justify-center rounded-xl bg-orange-600 px-7 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-orange-500/20 transition hover:bg-orange-700"
                                @click="inscribirse">
                                Inscribirme al curso
                            </button>

                            <!-- Sin cupos -->
                            <span
                                v-else-if="curso.capacidad && curso.inscritos_activos >= curso.capacidad && !estaInscrito && !esPropietario"
                                class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-6 py-3 text-xs sm:text-sm font-bold text-slate-400">
                                No hay cupos disponibles
                            </span>

                            <!-- Inscripciones cerradas por fecha -->
                            <span
                                v-else-if="!estaInscrito && !esPropietario && curso.fecha_limite_inscripcion && new Date(curso.fecha_limite_inscripcion) < new Date()"
                                class="inline-flex items-center justify-center rounded-xl bg-slate-100 px-6 py-3 text-xs sm:text-sm font-bold text-slate-400">
                                Inscripciones cerradas
                            </span>

                            <!-- BOTÓN DE FAVORITO EN BARRA DE ACCIONES (MARCAR / DESMARCAR) -->
                            <button type="button" @click="alternarFavorito" :disabled="procesandoFavorito"
                                class="inline-flex items-center justify-center gap-2.5 rounded-xl px-5 py-3 text-xs sm:text-sm font-black transition shadow-md disabled:opacity-60"
                                :class="esFavorito
                                    ? 'bg-amber-400 text-[#172A3D] shadow-amber-400/30 hover:bg-amber-500'
                                    : 'bg-amber-100/70 text-[#172A3D] border border-amber-300 hover:bg-amber-200/80'">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="h-5 w-5 shrink-0 transition-transform duration-200"
                                    :class="esFavorito ? 'fill-red-600 text-red-600 scale-110' : 'fill-none text-slate-600'">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>
                                <span>{{ esFavorito ? 'Guardado en favoritos (Quitar)' : 'Guardar en favoritos'
                                }}</span>
                            </button>

                            <!-- Ver inscritos (para el propietario) -->
                            <Link v-if="esPropietario" :href="route('cursos.inscripciones.index', curso.id)"
                                class="inline-flex items-center justify-center rounded-xl border border-orange-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-orange-950 transition hover:bg-orange-50 shadow-xs">
                                Ver inscritos
                            </Link>

                            <!-- Editar curso (para el propietario) -->
                            <Link v-if="esPropietario" :href="route('cursos.edit', curso.id)"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 shadow-xs">
                                Editar curso
                            </Link>

                            <!-- Volver -->
                            <Link :href="route('cursos.index')"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 shadow-xs">
                                Volver al listado
                            </Link>
                        </div>

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