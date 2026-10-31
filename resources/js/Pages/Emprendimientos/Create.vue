<script setup>
import { computed, watch, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { route } from 'ziggy-js';
import axios from 'axios';

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/

const props = defineProps({
    categorias: {
        type: Array,
        default: () => [],
    },
    palabrasProhibidas: {
        type: Array,
        default: () => [],
    },
});

/*
|--------------------------------------------------------------------------
| Formulario
|--------------------------------------------------------------------------
*/

const form = useForm({
    categoria_id: '',
    subcategoria_id: '',

    nombre: '',
    descripcion: '',
    produccion_actual: '',
    proyeccion: '',

    necesidad: '',
    propuesta_colaboracion: '',
    tipo_colaboracion: '',

    /*
    |--------------------------------------------------------------------------
    | Video de YouTube
    |--------------------------------------------------------------------------
    */

    youtube_url: '',
});

/*
|--------------------------------------------------------------------------
| CORRECCIÓN MÁGICA CON IA
|--------------------------------------------------------------------------
*/
const cargandoIA = ref({
    nombre: false,
    descripcion: false,
    produccion_actual: false,
    proyeccion: false,
    necesidad: false,
    propuesta_colaboracion: false,
});

async function aplicarCorreccionMagica(campo) {
    const textoActual = form[campo];
    if (!textoActual || !textoActual.trim()) return;

    cargandoIA.value[campo] = true;

    try {
        const response = await axios.post('/ai/correct-text', {
            text: textoActual,
            context_type: `emprendimiento_${campo}`,
        });

        if (response.data && response.data.improved_text) {
            form[campo] = response.data.improved_text;
        }
    } catch (error) {
        console.error('Error al aplicar la corrección mágica:', error);
        alert('No fue posible conectar con el servicio de IA en este momento.');
    } finally {
        cargandoIA.value[campo] = false;
    }
}

/*
|--------------------------------------------------------------------------
| MODERACIÓN EN TIEMPO REAL DE PALABRAS PROHIBIDAS
|--------------------------------------------------------------------------
*/
const alertaNombre = ref('');
const alertaDescripcion = ref('');
const alertaProduccionActual = ref('');
const alertaProyeccion = ref('');
const alertaNecesidad = ref('');
const alertaPropuestaColaboracion = ref('');

function verificarPalabrasProhibidas(texto, tipo) {
    if (!texto || !props.palabrasProhibidas || props.palabrasProhibidas.length === 0) {
        if (tipo === 'nombre') alertaNombre.value = '';
        if (tipo === 'descripcion') alertaDescripcion.value = '';
        if (tipo === 'produccion_actual') alertaProduccionActual.value = '';
        if (tipo === 'proyeccion') alertaProyeccion.value = '';
        if (tipo === 'necesidad') alertaNecesidad.value = '';
        if (tipo === 'propuesta_colaboracion') alertaPropuestaColaboracion.value = '';
        return;
    }

    const textoLower = texto.toLowerCase();
    const encontrada = props.palabrasProhibidas.find(palabra => {
        const palabraClean = palabra.trim().toLowerCase();
        return palabraClean.length > 0 && textoLower.includes(palabraClean);
    });

    const mensaje = encontrada ? `⚠️ Se ha detectado un término no permitido ("${encontrada}"). Por favor, modifícalo antes de publicar.` : '';

    if (tipo === 'nombre') alertaNombre.value = mensaje;
    if (tipo === 'descripcion') alertaDescripcion.value = mensaje;
    if (tipo === 'produccion_actual') alertaProduccionActual.value = mensaje;
    if (tipo === 'proyeccion') alertaProyeccion.value = mensaje;
    if (tipo === 'necesidad') alertaNecesidad.value = mensaje;
    if (tipo === 'propuesta_colaboracion') alertaPropuestaColaboracion.value = mensaje;
}

watch(() => form.nombre, (v) => verificarPalabrasProhibidas(v, 'nombre'));
watch(() => form.descripcion, (v) => verificarPalabrasProhibidas(v, 'descripcion'));
watch(() => form.produccion_actual, (v) => verificarPalabrasProhibidas(v, 'produccion_actual'));
watch(() => form.proyeccion, (v) => verificarPalabrasProhibidas(v, 'proyeccion'));
watch(() => form.necesidad, (v) => verificarPalabrasProhibidas(v, 'necesidad'));
watch(() => form.propuesta_colaboracion, (v) => verificarPalabrasProhibidas(v, 'propuesta_colaboracion'));

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
];

/*
|--------------------------------------------------------------------------
| Subcategorías
|--------------------------------------------------------------------------
*/

const subcategoriasDisponibles = computed(() => {
    if (!form.categoria_id) {
        return [];
    }

    const categoriaSeleccionada = props.categorias.find(
        categoria =>
            Number(categoria.id) === Number(form.categoria_id)
    );

    return categoriaSeleccionada?.subcategorias ?? [];
});

/*
|--------------------------------------------------------------------------
| Reiniciar subcategoría cuando cambia la categoría
|--------------------------------------------------------------------------
*/

watch(
    () => form.categoria_id,
    () => {
        form.subcategoria_id = '';
    }
);

/*
|--------------------------------------------------------------------------
| Extraer ID de YouTube
|--------------------------------------------------------------------------
*/

const extraerYoutubeVideoId = (url) => {
    if (!url) {
        return null;
    }

    try {
        const parsedUrl = new URL(url.trim());
        const host = parsedUrl.hostname.toLowerCase();

        if (
            host === 'youtube.com' ||
            host === 'www.youtube.com' ||
            host === 'm.youtube.com'
        ) {
            if (parsedUrl.pathname === '/watch') {
                return parsedUrl.searchParams.get('v');
            }

            const embedMatch = parsedUrl.pathname.match(
                /^\/embed\/([^/]+)/
            );

            if (embedMatch) {
                return embedMatch[1];
            }

            const shortsMatch = parsedUrl.pathname.match(
                /^\/shorts\/([^/]+)/
            );

            if (shortsMatch) {
                return shortsMatch[1];
            }
        }

        if (host === 'youtu.be') {
            return parsedUrl.pathname
                .replace(/^\/+|\/+$/g, '');
        }

        return null;
    } catch {
        return null;
    }
};

/*
|--------------------------------------------------------------------------
| Vista previa de YouTube
|--------------------------------------------------------------------------
*/

const youtubePreviewUrl = computed(() => {
    const videoId = extraerYoutubeVideoId(
        form.youtube_url
    );

    if (!videoId) {
        return null;
    }

    return `https://www.youtube.com/embed/${videoId}`;
});

/*
|--------------------------------------------------------------------------
| Limpiar error de YouTube cuando cambia el enlace
|--------------------------------------------------------------------------
*/

watch(
    () => form.youtube_url,
    () => {
        if (form.errors.youtube_url) {
            form.clearErrors('youtube_url');
        }
    }
);

/*
|--------------------------------------------------------------------------
| Cancelar
|--------------------------------------------------------------------------
*/

const cancelar = () => {
    window.history.back();
};

/*
|--------------------------------------------------------------------------
| Guardar
|--------------------------------------------------------------------------
*/

const guardar = () => {
    if (!form.youtube_url) {
        form.setError(
            'youtube_url',
            'Debes proporcionar el enlace de YouTube del video de presentación.'
        );

        return;
    }

    const videoId = extraerYoutubeVideoId(
        form.youtube_url
    );

    if (!videoId) {
        form.setError(
            'youtube_url',
            'El enlace proporcionado no corresponde a un video válido de YouTube.'
        );

        return;
    }

    form.post(route('emprendimientos.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">

        <Head title="Crear emprendimiento" />

        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- =========================================================
                ENCABEZADO
            ========================================================== -->
            <section
                class="border-b border-purple-200/80 bg-purple-100/35 backdrop-blur-md shadow-xl shadow-purple-600/20">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex items-start gap-4">
                        <Link :href="route('emprendimientos.index')"
                            class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-purple-600"
                            title="Volver">
                            <i class="pi pi-arrow-left"></i>
                        </Link>

                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-purple-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-purple-950">
                                    Nuevo proyecto
                                </span>
                            </div>

                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Crear emprendimiento
                            </h1>

                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Presenta tu emprendimiento, muestra lo que estás haciendo actualmente y explica qué tipo
                                de colaboración estás buscando.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =========================================================
                FORMULARIO
            ========================================================== -->
            <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 bg-purple-50 rounded-xl p-3 mt-2">

                <form class="space-y-6" @submit.prevent="guardar">

                    <!-- ================================================= -->
                    <!-- INFORMACIÓN GENERAL -->
                    <!-- ================================================= -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-purple-600/30">
                        <div class="border-b border-slate-100 bg-purple-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                Información del emprendimiento
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                Cuéntanos qué haces y cómo funciona tu emprendimiento.
                            </p>
                        </div>

                        <div class="p-5 sm:p-6 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <!-- Nombre -->
                            <div class="md:col-span-2">
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="nombre"
                                        class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-0">
                                        Nombre del emprendimiento <span class="text-red-500">*</span>
                                    </label>
                                    <button type="button" @click="aplicarCorreccionMagica('nombre')"
                                        :disabled="cargandoIA.nombre || !form.nombre.trim()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition disabled:opacity-50">
                                        <span v-if="cargandoIA.nombre" class="animate-spin">⏳</span>
                                        <span v-else>✨</span>
                                        <span>Corrección Mágica</span>
                                    </button>
                                </div>
                                <input id="nombre" v-model="form.nombre" type="text" maxlength="255"
                                    placeholder="Ejemplo: Panadería El Buen Sabor"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.nombre || alertaNombre }" />

                                <p v-if="alertaNombre" class="mt-1.5 text-xs text-amber-600 font-bold">
                                    {{ alertaNombre }}
                                </p>

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
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] shadow-xs outline-none transition disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400 focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
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
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="descripcion"
                                        class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-0">
                                        Descripción del emprendimiento <span class="text-red-500">*</span>
                                    </label>
                                    <button type="button" @click="aplicarCorreccionMagica('descripcion')"
                                        :disabled="cargandoIA.descripcion || !form.descripcion.trim()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition disabled:opacity-50">
                                        <span v-if="cargandoIA.descripcion" class="animate-spin">⏳</span>
                                        <span v-else>✨</span>
                                        <span>Corrección Mágica</span>
                                    </button>
                                </div>
                                <textarea id="descripcion" v-model="form.descripcion" rows="4"
                                    placeholder="Describe brevemente qué haces, qué productos o servicios ofreces y qué hace diferente a tu emprendimiento."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.descripcion || alertaDescripcion }"></textarea>

                                <p v-if="alertaDescripcion" class="mt-1.5 text-xs text-amber-600 font-bold">
                                    {{ alertaDescripcion }}
                                </p>

                                <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.descripcion }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- ================================================= -->
                    <!-- SITUACIÓN ACTUAL Y PROYECCIÓN -->
                    <!-- ================================================= -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-purple-600/30">
                        <div class="border-b border-slate-100 bg-purple-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                Situación y proyección
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                Ayuda a quienes vean tu propuesta a entender dónde estás y hacia dónde quieres llegar.
                            </p>
                        </div>

                        <div class="p-5 sm:p-6 space-y-5">
                            <!-- Producción actual con Corrección Mágica y Alerta -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="produccion_actual"
                                        class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-0">
                                        ¿Qué produces actualmente?
                                    </label>
                                    <button type="button" @click="aplicarCorreccionMagica('produccion_actual')"
                                        :disabled="cargandoIA.produccion_actual || !form.produccion_actual.trim()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition disabled:opacity-50">
                                        <span v-if="cargandoIA.produccion_actual" class="animate-spin">⏳</span>
                                        <span v-else>✨</span>
                                        <span>Corrección Mágica</span>
                                    </button>
                                </div>
                                <textarea id="produccion_actual" v-model="form.produccion_actual" rows="4"
                                    placeholder="Explica qué produces actualmente, cuánto produces aproximadamente, qué servicios prestas o qué actividad realizas."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.produccion_actual || alertaProduccionActual }"></textarea>

                                <p v-if="alertaProduccionActual" class="mt-1.5 text-xs text-amber-600 font-bold">
                                    {{ alertaProduccionActual }}
                                </p>

                                <p v-if="form.errors.produccion_actual" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.produccion_actual }}
                                </p>
                            </div>

                            <!-- Proyección con Corrección Mágica y Alerta -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="proyeccion"
                                        class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-0">
                                        ¿Cuál es tu proyección?
                                    </label>
                                    <button type="button" @click="aplicarCorreccionMagica('proyeccion')"
                                        :disabled="cargandoIA.proyeccion || !form.proyeccion.trim()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition disabled:opacity-50">
                                        <span v-if="cargandoIA.proyeccion" class="animate-spin">⏳</span>
                                        <span v-else>✨</span>
                                        <span>Corrección Mágica</span>
                                    </button>
                                </div>
                                <textarea id="proyeccion" v-model="form.proyeccion" rows="4"
                                    placeholder="Cuéntanos qué quieres lograr, cómo quieres crecer o qué esperas alcanzar con tu emprendimiento."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.proyeccion || alertaProyeccion }"></textarea>

                                <p v-if="alertaProyeccion" class="mt-1.5 text-xs text-amber-600 font-bold">
                                    {{ alertaProyeccion }}
                                </p>

                                <p v-if="form.errors.proyeccion" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.proyeccion }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- ================================================= -->
                    <!-- PROPUESTA DE COLABORACIÓN -->
                    <!-- ================================================= -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-purple-600/30">
                        <div class="border-b border-slate-100 bg-purple-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                Propuesta de colaboración
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                Explica qué necesitas y de qué manera otras personas podrían colaborar contigo.
                            </p>
                        </div>

                        <div class="p-5 sm:p-6 space-y-5">
                            <!-- Necesidad con Corrección Mágica y Alerta -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="necesidad"
                                        class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-0">
                                        ¿Qué necesitas?
                                    </label>
                                    <button type="button" @click="aplicarCorreccionMagica('necesidad')"
                                        :disabled="cargandoIA.necesidad || !form.necesidad.trim()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition disabled:opacity-50">
                                        <span v-if="cargandoIA.necesidad" class="animate-spin">⏳</span>
                                        <span v-else>✨</span>
                                        <span>Corrección Mágica</span>
                                    </button>
                                </div>
                                <textarea id="necesidad" v-model="form.necesidad" rows="4"
                                    placeholder="Ejemplo: equipos, materias primas, capacitación, contactos, comercialización, espacio, proveedores o capital."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.necesidad || alertaNecesidad }"></textarea>

                                <p v-if="alertaNecesidad" class="mt-1.5 text-xs text-amber-600 font-bold">
                                    {{ alertaNecesidad }}
                                </p>

                                <p v-if="form.errors.necesidad" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.necesidad }}
                                </p>
                            </div>

                            <!-- Propuesta con Corrección Mágica y Alerta -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="propuesta_colaboracion"
                                        class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-0">
                                        ¿Qué propones a quienes quieran colaborar?
                                    </label>
                                    <button type="button" @click="aplicarCorreccionMagica('propuesta_colaboracion')"
                                        :disabled="cargandoIA.propuesta_colaboracion || !form.propuesta_colaboracion.trim()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition disabled:opacity-50">
                                        <span v-if="cargandoIA.propuesta_colaboracion" class="animate-spin">⏳</span>
                                        <span v-else>✨</span>
                                        <span>Corrección Mágica</span>
                                    </button>
                                </div>
                                <textarea id="propuesta_colaboracion" v-model="form.propuesta_colaboracion" rows="4"
                                    placeholder="Explica cómo imaginas la colaboración y qué puedes aportar a cambio."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.propuesta_colaboracion || alertaPropuestaColaboracion }"></textarea>

                                <p v-if="alertaPropuestaColaboracion" class="mt-1.5 text-xs text-amber-600 font-bold">
                                    {{ alertaPropuestaColaboracion }}
                                </p>

                                <p v-if="form.errors.propuesta_colaboracion"
                                    class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.propuesta_colaboracion }}
                                </p>
                            </div>

                            <!-- Tipo de colaboración -->
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

                    <!-- ================================================= -->
                    <!-- VIDEO -->
                    <!-- ================================================= -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-purple-600/30">
                        <div class="border-b border-slate-100 bg-purple-50/40 px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-100 border border-purple-200 text-purple-700 shadow-xs">
                                    <i class="pi pi-video text-sm"></i>
                                </div>
                                <div>
                                    <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                        Video del emprendimiento <span class="text-red-500">*</span>
                                    </h2>
                                    <p class="text-xs text-slate-500 font-medium">
                                        Presenta tu emprendimiento de una manera clara y cercana.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6 space-y-5">
                            <!-- Instrucciones -->
                            <div class="rounded-2xl border border-purple-200 bg-purple-50/60 p-5 backdrop-blur-md">
                                <h3 class="text-xs font-black uppercase tracking-wider text-purple-950">
                                    ¿Cómo agregar tu video?
                                </h3>
                                <ol
                                    class="mt-2.5 space-y-1.5 text-xs sm:text-sm leading-relaxed text-purple-900 font-medium">
                                    <li><strong>1.</strong> Sube tu video a YouTube.</li>
                                    <li><strong>2.</strong> Configúralo como <strong>“No listado”</strong>.</li>
                                    <li><strong>3.</strong> Copia el enlace del video.</li>
                                    <li><strong>4.</strong> Pega aquí el enlace.</li>
                                </ol>
                                <p class="mt-3 text-[11px] leading-relaxed text-purple-800 font-medium">
                                    El video no debe configurarse como privado, ya que las personas que consulten tu
                                    emprendimiento necesitan poder reproducirlo.
                                </p>
                            </div>

                            <!-- Enlace de YouTube -->
                            <div>
                                <label for="youtube_url"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Enlace del video de YouTube <span class="text-red-500">*</span>
                                </label>
                                <input id="youtube_url" v-model="form.youtube_url" type="url"
                                    placeholder="https://www.youtube.com/watch?v=..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] placeholder-slate-400 shadow-xs outline-none transition focus:border-purple-600 focus:ring-2 focus:ring-purple-100"
                                    :class="{ 'border-red-500': form.errors.youtube_url }" />
                                <p v-if="form.errors.youtube_url" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.youtube_url }}
                                </p>
                            </div>

                            <!-- Vista previa -->
                            <div v-if="youtubePreviewUrl"
                                class="overflow-hidden rounded-xl border border-slate-200 bg-black shadow-md">
                                <div class="aspect-video w-full">
                                    <iframe :src="youtubePreviewUrl" title="Vista previa del video del emprendimiento"
                                        class="h-full w-full" frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen></iframe>
                                </div>
                            </div>

                            <!-- Enlace inválido -->
                            <p v-else-if="form.youtube_url" class="text-xs font-medium text-red-600">
                                Introduce un enlace válido de YouTube para mostrar la vista previa.
                            </p>
                        </div>
                    </section>

                    <!-- ================================================= -->
                    <!-- INFORMACIÓN FINAL -->
                    <!-- ================================================= -->
                    <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-5 shadow-lg backdrop-blur-md">
                        <div class="flex gap-3">
                            <i class="pi pi-info-circle mt-0.5 text-amber-600"></i>
                            <div class="text-xs sm:text-sm leading-relaxed text-amber-900 font-medium">
                                <p class="font-bold uppercase tracking-wider text-amber-950">
                                    Antes de publicar
                                </p>
                                <p class="mt-1">
                                    Asegúrate de que la información sea clara, verdadera y que la propuesta de
                                    colaboración represente realmente lo que estás buscando.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ================================================= -->
                    <!-- ACCIONES -->
                    <!-- ================================================= -->
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button type="button"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition"
                            @click="cancelar">
                            Cancelar
                        </button>

                        <button type="submit"
                            :disabled="form.processing || Boolean(alertaNombre) || Boolean(alertaDescripcion) || Boolean(alertaProduccionActual) || Boolean(alertaProyeccion) || Boolean(alertaNecesidad) || Boolean(alertaPropuestaColaboracion)"
                            class="inline-flex items-center justify-center rounded-xl bg-purple-600 px-6 py-3 text-xs sm:text-sm font-black text-white shadow-xl shadow-purple-600/35 transition hover:bg-purple-700 disabled:cursor-not-allowed disabled:opacity-60">
                            <i v-if="form.processing" class="pi pi-spin pi-spinner mr-2"></i>
                            <i v-else class="pi pi-check mr-2"></i>
                            {{ form.processing ? 'Guardando...' : 'Publicar emprendimiento' }}
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