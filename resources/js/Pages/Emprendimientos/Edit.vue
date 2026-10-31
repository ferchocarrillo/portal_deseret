<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { route } from 'ziggy-js'

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/

const props = defineProps({
    emprendimiento: {
        type: Object,
        required: true,
    },

    categorias: {
        type: Array,
        default: () => [],
    },

    subcategorias: {
        type: Array,
        default: () => [],
    },
})

/*
|--------------------------------------------------------------------------
| Formulario
|--------------------------------------------------------------------------
*/

const form = useForm({
    categoria_id: props.emprendimiento.categoria_id ?? '',
    subcategoria_id: props.emprendimiento.subcategoria_id ?? '',

    nombre: props.emprendimiento.nombre ?? '',
    descripcion: props.emprendimiento.descripcion ?? '',
    produccion_actual: props.emprendimiento.produccion_actual ?? '',
    proyeccion: props.emprendimiento.proyeccion ?? '',

    necesidad: props.emprendimiento.necesidad ?? '',
    propuesta_colaboracion:
        props.emprendimiento.propuesta_colaboracion ?? '',
    tipo_colaboracion:
        props.emprendimiento.tipo_colaboracion ?? '',

    youtube_url: '',
})

/*
|--------------------------------------------------------------------------
| Tipos de colaboración
|--------------------------------------------------------------------------
*/

const tiposColaboracion = [
    'Inversión',
    'Alianza comercial',
    'Equipos o herramientas',
    'Proveedores o materias primas',
    'Comercialización',
    'Capacitación',
    'Socio o colaborador',
    'Espacio físico',
    'Contactos o conexiones',
    'Otro',
]

/*
|--------------------------------------------------------------------------
| Subcategorías disponibles
|--------------------------------------------------------------------------
*/

const subcategoriasDisponibles = computed(() => {
    if (!form.categoria_id) {
        return []
    }

    if (props.subcategorias && props.subcategorias.length > 0) {
        return props.subcategorias.filter(
            sub => Number(sub.categoria_id) === Number(form.categoria_id)
        )
    }

    const categoriaSeleccionada = props.categorias.find(
        cat => Number(cat.id) === Number(form.categoria_id)
    )

    return categoriaSeleccionada?.subcategorias ?? []
})

/*
|--------------------------------------------------------------------------
| Extraer ID de YouTube
|--------------------------------------------------------------------------
*/

const extraerYoutubeVideoId = (url) => {
    if (!url) {
        return null
    }

    try {
        const parsedUrl = new URL(url.trim())
        const host = parsedUrl.hostname.toLowerCase()

        if (
            host === 'youtube.com' ||
            host === 'www.youtube.com' ||
            host === 'm.youtube.com'
        ) {
            if (parsedUrl.pathname === '/watch') {
                return parsedUrl.searchParams.get('v')
            }

            const embedMatch = parsedUrl.pathname.match(
                /^\/embed\/([^/]+)/
            )

            if (embedMatch) {
                return embedMatch[1]
            }

            const shortsMatch = parsedUrl.pathname.match(
                /^\/shorts\/([^/]+)/
            )

            if (shortsMatch) {
                return shortsMatch[1]
            }
        }

        if (host === 'youtu.be') {
            return parsedUrl.pathname
                .replace(/^\/+|\/+$/g, '')
        }

        return null
    } catch {
        return null
    }
}

/*
|--------------------------------------------------------------------------
| Video actual de YouTube
|--------------------------------------------------------------------------
*/

const youtubeActualUrl = computed(() => {
    if (!props.emprendimiento.youtube_video_id) {
        return null
    }

    return `https://www.youtube.com/embed/${props.emprendimiento.youtube_video_id}`
})

/*
|--------------------------------------------------------------------------
| Vista previa del nuevo video
|--------------------------------------------------------------------------
*/

const youtubeNuevoPreviewUrl = computed(() => {
    const videoId = extraerYoutubeVideoId(
        form.youtube_url
    )

    if (!videoId) {
        return null
    }

    return `https://www.youtube.com/embed/${videoId}`
})

/*
|--------------------------------------------------------------------------
| Cancelar
|--------------------------------------------------------------------------
*/

const cancelar = () => {
    window.history.back()
}

/*
|--------------------------------------------------------------------------
| Actualizar
|--------------------------------------------------------------------------
*/

const actualizar = () => {
    if (form.youtube_url) {
        const videoId = extraerYoutubeVideoId(
            form.youtube_url
        )

        if (!videoId) {
            form.setError(
                'youtube_url',
                'El enlace proporcionado no corresponde a un video válido de YouTube.'
            )

            return
        }
    }

    form
        .transform(data => ({
            ...data,
            _method: 'PUT',
        }))
        .post(
            route(
                'emprendimientos.update',
                props.emprendimiento.id
            ),
            {
                preserveScroll: true,
            }
        )
}
</script>

<template>

    <Head :title="`Editar ${emprendimiento.nombre} | Portal Deseret`" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- =========================================================
            ENCABEZADO DE SECCIÓN
            ========================================================== -->
            <section
                class="border-b border-purple-200/80 bg-purple-100/35 backdrop-blur-md shadow-xl shadow-purple-600/20">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex items-start gap-4">
                        <Link :href="route('emprendimientos.show', emprendimiento.id)"
                            class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-purple-600"
                            title="Volver">
                            <i class="pi pi-arrow-left"></i>
                        </Link>

                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-purple-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-purple-950">
                                    Mi emprendimiento
                                </span>
                            </div>

                            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-[#172A3D]">
                                Editar emprendimiento
                            </h1>

                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Actualiza la información de tu emprendimiento y mantén vigente tu propuesta de
                                colaboración.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =========================================================
            FORMULARIO PRINCIPAL
            ========================================================== -->
            <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 bg-purple-50 rounded-xl p-3 mt-2">
                <form class="space-y-6" @submit.prevent="actualizar">

                    <!-- INFORMACIÓN GENERAL -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-purple-600/30">
                        <div class="border-b border-slate-100 bg-purple-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                Información del emprendimiento
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                Actualiza los datos principales de tu emprendimiento.
                            </p>
                        </div>

                        <div class="p-5 sm:p-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <!-- Nombre -->
                            <div class="md:col-span-2">
                                <label for="nombre"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Nombre del emprendimiento <span class="text-red-500">*</span>
                                </label>
                                <input id="nombre" v-model="form.nombre" type="text" maxlength="255"
                                    placeholder="Ejemplo: Panadería El Buen Sabor"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.nombre }" />
                                <p v-if="form.errors.nombre" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.nombre }}
                                </p>
                            </div>

                            <!-- Categoría -->
                            <div>
                                <label for="categoria_id"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Categoría <span class="text-red-500">*</span>
                                </label>
                                <select id="categoria_id" v-model="form.categoria_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.categoria_id }">
                                    <option value="">Selecciona una categoría</option>
                                    <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
                                        {{ categoria.nombre }}
                                    </option>
                                </select>
                                <p v-if="form.errors.categoria_id" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.categoria_id }}
                                </p>
                            </div>

                            <!-- Subcategoría -->
                            <div>
                                <label for="subcategoria_id"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Subcategoría
                                </label>
                                <select id="subcategoria_id" v-model="form.subcategoria_id"
                                    :disabled="!form.categoria_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] shadow-xs outline-none transition disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.subcategoria_id }">
                                    <option value="">Selecciona una subcategoría</option>
                                    <option v-for="subcategoria in subcategoriasDisponibles" :key="subcategoria.id"
                                        :value="subcategoria.id">
                                        {{ subcategoria.nombre }}
                                    </option>
                                </select>
                                <p v-if="form.errors.subcategoria_id" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.subcategoria_id }}
                                </p>
                            </div>

                            <!-- Descripción -->
                            <div class="md:col-span-2">
                                <label for="descripcion"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Descripción del emprendimiento <span class="text-red-500">*</span>
                                </label>
                                <textarea id="descripcion" v-model="form.descripcion" rows="4"
                                    placeholder="Describe brevemente qué haces, qué productos o servicios ofreces..."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.descripcion }"></textarea>
                                <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.descripcion }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- SITUACIÓN Y PROYECCIÓN -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-purple-600/30">
                        <div class="border-b border-slate-100 bg-purple-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                Situación y proyección
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                Mantén actualizada la información sobre tu producción y tus objetivos.
                            </p>
                        </div>

                        <div class="p-5 sm:p-6 space-y-5">
                            <div>
                                <label for="produccion_actual"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    ¿Qué produces actualmente?
                                </label>
                                <textarea id="produccion_actual" v-model="form.produccion_actual" rows="4"
                                    placeholder="Explica qué produces actualmente, cuánto produces aproximadamente..."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.produccion_actual }"></textarea>
                                <p v-if="form.errors.produccion_actual" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.produccion_actual }}
                                </p>
                            </div>

                            <div>
                                <label for="proyeccion"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    ¿Cuál es tu proyección?
                                </label>
                                <textarea id="proyeccion" v-model="form.proyeccion" rows="4"
                                    placeholder="Cuéntanos qué quieres lograr, cómo quieres crecer..."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.proyeccion }"></textarea>
                                <p v-if="form.errors.proyeccion" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.proyeccion }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- COLABORACIÓN -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-purple-600/30">
                        <div class="border-b border-slate-100 bg-purple-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                Propuesta de colaboración
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                Actualiza lo que necesitas y la forma en que propones trabajar.
                            </p>
                        </div>

                        <div class="p-5 sm:p-6 space-y-5">
                            <div>
                                <label for="necesidad"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    ¿Qué necesitas?
                                </label>
                                <textarea id="necesidad" v-model="form.necesidad" rows="4"
                                    placeholder="Ejemplo: equipos, materias primas, capacitación, capital..."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.necesidad }"></textarea>
                                <p v-if="form.errors.necesidad" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.necesidad }}
                                </p>
                            </div>

                            <div>
                                <label for="propuesta_colaboracion"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    ¿Qué propones a quienes quieran colaborar?
                                </label>
                                <textarea id="propuesta_colaboracion" v-model="form.propuesta_colaboracion" rows="4"
                                    placeholder="Explica cómo imaginas la colaboración y qué puedes aportar a cambio."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.propuesta_colaboracion }"></textarea>
                                <p v-if="form.errors.propuesta_colaboracion"
                                    class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.propuesta_colaboracion }}
                                </p>
                            </div>

                            <div>
                                <label for="tipo_colaboracion"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Tipo de colaboración que buscas
                                </label>
                                <select id="tipo_colaboracion" v-model="form.tipo_colaboracion"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                                    <option value="">Selecciona una opción</option>
                                    <option v-for="tipo in tiposColaboracion" :key="tipo" :value="tipo">
                                        {{ tipo }}
                                    </option>
                                </select>
                                <p v-if="form.errors.tipo_colaboracion" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.tipo_colaboracion }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- VIDEO DE YOUTUBE -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-purple-600/30">
                        <div class="border-b border-slate-100 bg-purple-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                Video del emprendimiento
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                Puedes conservar el video actual o reemplazarlo por otro enlace de YouTube.
                            </p>
                        </div>

                        <div class="p-5 sm:p-6 space-y-5">
                            <!-- Video actual -->
                            <div v-if="youtubeActualUrl && !form.youtube_url"
                                class="overflow-hidden rounded-xl border border-slate-200 bg-black shadow-md">
                                <div class="aspect-video w-full">
                                    <iframe :src="youtubeActualUrl" title="Video actual" class="h-full w-full"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen></iframe>
                                </div>
                                <div
                                    class="flex items-center justify-between border-t border-slate-800 bg-slate-900 p-4 text-xs text-slate-300 font-medium">
                                    <span>Video actual configurado</span>
                                </div>
                            </div>

                            <!-- Sin video actual -->
                            <div v-else-if="!youtubeActualUrl && !form.youtube_url"
                                class="rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-6 text-center text-xs text-slate-500 font-medium">
                                Este emprendimiento no tiene video configurado.
                            </div>

                            <!-- Nuevo video -->
                            <div v-if="form.youtube_url && youtubeNuevoPreviewUrl"
                                class="overflow-hidden rounded-xl border border-slate-200 bg-black shadow-md">
                                <div class="aspect-video w-full">
                                    <iframe :src="youtubeNuevoPreviewUrl" title="Vista previa del nuevo video"
                                        class="h-full w-full" frameborder="0" allowfullscreen></iframe>
                                </div>
                                <div
                                    class="flex items-center justify-between border-t border-slate-800 bg-slate-900 p-4 text-xs text-white font-medium">
                                    <span>Nuevo video seleccionado</span>
                                    <button type="button" class="text-red-400 hover:underline font-bold"
                                        @click="form.youtube_url = ''">Cancelar cambio</button>
                                </div>
                            </div>

                            <div>
                                <label for="youtube_url"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Nuevo enlace de YouTube (Opcional)
                                </label>
                                <input id="youtube_url" v-model="form.youtube_url" type="url"
                                    placeholder="https://www.youtube.com/watch?v=..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.youtube_url }" />
                                <p v-if="form.errors.youtube_url" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.youtube_url }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- ACCIONES -->
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end pb-10">
                        <button type="button"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-xs transition"
                            @click="cancelar">
                            Cancelar
                        </button>

                        <button type="submit" :disabled="form.processing"
                            class="inline-flex items-center justify-center rounded-xl bg-purple-600 px-6 py-3 text-xs sm:text-sm font-black text-white shadow-xl shadow-purple-600/35 transition hover:bg-purple-700 disabled:opacity-60">
                            <i v-if="form.processing" class="pi pi-spin pi-spinner mr-2"></i>
                            {{ form.processing ? 'Guardando cambios...' : 'Guardar cambios' }}
                        </button>
                    </div>

                </form>
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