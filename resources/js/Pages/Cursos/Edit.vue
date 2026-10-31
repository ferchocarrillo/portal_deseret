<script setup>
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { route } from 'ziggy-js'
import UbicacionSelector from '@/Components/UbicacionSelector.vue'

const props = defineProps({
    curso: {
        type: Object,
        required: true,
    },
})

const inputImagen = ref(null)
const vistaPrevia = ref(null)

const imagenOriginal = computed(() => {
    if (!props.curso.imagen) return null

    if (
        props.curso.imagen.startsWith('http://') ||
        props.curso.imagen.startsWith('https://')
    ) {
        return props.curso.imagen
    }

    return `/storage/${props.curso.imagen.replace(/^\/+/, '')}`
})

const form = useForm({
    titulo: props.curso.titulo ?? '',
    descripcion: props.curso.descripcion ?? '',
    imagen: null,

    tipo: props.curso.tipo ?? 'propio',
    url_externa: props.curso.url_externa ?? '',

    es_gratuito: Boolean(props.curso.es_gratuito),
    precio: props.curso.precio ?? '',

    modalidad: props.curso.modalidad ?? 'virtual',

    pais: props.curso.pais ?? 'Colombia',
    departamento: props.curso.departamento ?? '',
    ciudad: props.curso.ciudad ?? '',
    direccion: props.curso.direccion ?? '',
    enlace_virtual: props.curso.enlace_virtual ?? '',

    // Duración e intensidad horaria
    frecuencia_clases: props.curso.frecuencia_clases ?? '',
    duracion_sesion_minutos: props.curso.duracion_sesion_minutos ?? '',
    intensidad_horaria: props.curso.intensidad_horaria ?? '',

    fecha_inicio: props.curso.fecha_inicio
        ? String(props.curso.fecha_inicio).substring(0, 10)
        : '',
    fecha_fin: props.curso.fecha_fin
        ? String(props.curso.fecha_fin).substring(0, 10)
        : '',
    fecha_limite_inscripcion: props.curso.fecha_limite_inscripcion
        ? String(props.curso.fecha_limite_inscripcion).substring(0, 16)
        : '',
    publicado_desde: props.curso.publicado_desde
        ? String(props.curso.publicado_desde).substring(0, 16)
        : '',
    publicado_hasta: props.curso.publicado_hasta
        ? String(props.curso.publicado_hasta).substring(0, 16)
        : '',

    cupos_ilimitados: Boolean(props.curso.cupos_ilimitados),
    cantidad_cupos: props.curso.cantidad_cupos ?? '',
    permite_lista_espera: Boolean(props.curso.permite_lista_espera),

    estado: props.curso.estado ?? 'publicado',
})

const esExterno = computed(() => form.tipo === 'externo')
const esGratuito = computed(() => form.es_gratuito)
const esVirtual = computed(() => form.modalidad === 'virtual')
const tieneUbicacionPresencial = computed(() =>
    ['presencial', 'hibrido'].includes(form.modalidad),
)

const opcionesModalidad = [
    {
        value: 'virtual',
        titulo: 'Virtual',
        descripcion: 'Los participantes acceden desde internet.',
    },
    {
        value: 'presencial',
        titulo: 'Presencial',
        descripcion: 'Se realiza en una ubicación física.',
    },
    {
        value: 'hibrido',
        titulo: 'Híbrido',
        descripcion: 'Combina sesiones virtuales y presenciales.',
    },
]

const opcionesEstado = [
    {
        value: 'publicado',
        titulo: 'Publicado',
        descripcion: 'El curso estará disponible según sus fechas de publicación.',
    },
    {
        value: 'pausado',
        titulo: 'Pausado',
        descripcion: 'La publicación permanecerá temporalmente inactiva.',
    },
    {
        value: 'cancelado',
        titulo: 'Cancelado',
        descripcion: 'El curso se marcará como cancelado.',
    },
]

function seleccionarImagen(event) {
    const archivo = event.target.files?.[0]

    if (!archivo) return

    if (!archivo.type.startsWith('image/')) {
        form.setError('imagen', 'Selecciona un archivo de imagen válido.')
        event.target.value = ''
        return
    }

    if (archivo.size > 5 * 1024 * 1024) {
        form.setError('imagen', 'La imagen no puede superar los 5 MB.')
        event.target.value = ''
        return
    }

    form.clearErrors('imagen')
    form.imagen = archivo

    if (vistaPrevia.value) {
        URL.revokeObjectURL(vistaPrevia.value)
    }

    vistaPrevia.value = URL.createObjectURL(archivo)
}

function quitarImagenNueva() {
    form.imagen = null

    if (vistaPrevia.value) {
        URL.revokeObjectURL(vistaPrevia.value)
        vistaPrevia.value = null
    }

    if (inputImagen.value) {
        inputImagen.value.value = ''
    }

    form.clearErrors('imagen')
}

function formatoTamano(bytes) {
    if (!bytes) return ''
    return `${(bytes / 1024 / 1024).toFixed(2)} MB`
}

watch(
    () => form.tipo,
    (tipo) => {
        if (tipo === 'propio') {
            form.url_externa = ''
            form.clearErrors('url_externa')
        }
    },
)

watch(
    () => form.es_gratuito,
    (gratuito) => {
        if (gratuito) {
            form.precio = ''
            form.clearErrors('precio')
        }
    },
)

watch(
    () => form.modalidad,
    (modalidad) => {
        if (modalidad !== 'virtual') {
            form.enlace_virtual = ''
            form.clearErrors('enlace_virtual')
        }

        if (!['presencial', 'hibrido'].includes(modalidad)) {
            form.pais = 'Colombia'
            form.departamento = ''
            form.ciudad = ''
            form.direccion = ''
            form.clearErrors([
                'pais',
                'departamento',
                'ciudad',
                'direccion',
            ])
        }
    },
)

watch(
    () => form.cupos_ilimitados,
    (ilimitados) => {
        if (ilimitados) {
            form.cantidad_cupos = ''
            form.clearErrors('cantidad_cupos')
        }
    },
)

function actualizarCurso() {
    /*
     * Se utiliza POST con _method=put para que Laravel procese
     * correctamente la actualización multipart cuando hay imagen.
     */
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('cursos.update', props.curso.id), {
        forceFormData: true,
        preserveScroll: true,
    })
}

onBeforeUnmount(() => {
    if (vistaPrevia.value) {
        URL.revokeObjectURL(vistaPrevia.value)
    }
})

const fechaInicioVisual = ref(formatearFechaParaMostrar(props.curso.fecha_inicio))
const fechaFinVisual = ref(formatearFechaParaMostrar(props.curso.fecha_fin))

function formatearFechaParaMostrar(fecha) {
    if (!fecha) return '';

    const valor = String(fecha).substring(0, 10);
    const [anio, mes, dia] = valor.split('-');

    if (!anio || !mes || !dia) {
        return '';
    }

    return `${dia}/${mes}/${anio}`;
}

function fechaParaBackend(valor) {
    const limpio = String(valor ?? '')
        .replace(/\D/g, '')
        .slice(0, 8);

    if (limpio.length !== 8) {
        return '';
    }

    return `${limpio.slice(4, 8)}-${limpio.slice(2, 4)}-${limpio.slice(0, 2)}`;
}

function actualizarFecha(campo, valor) {
    const limpio = String(valor ?? '')
        .replace(/\D/g, '')
        .slice(0, 8);

    let mostrado = limpio;

    if (limpio.length > 2) {
        mostrado = `${limpio.slice(0, 2)}/${limpio.slice(2)}`;
    }

    if (limpio.length > 4) {
        mostrado =
            `${limpio.slice(0, 2)}/${limpio.slice(2, 4)}/${limpio.slice(4)}`;
    }

    if (campo === 'fecha_inicio') {
        fechaInicioVisual.value = mostrado;
        form.fecha_inicio = fechaParaBackend(mostrado);
    }

    if (campo === 'fecha_fin') {
        fechaFinVisual.value = mostrado;
        form.fecha_fin = fechaParaBackend(mostrado);
    }
}
</script>

<template>

    <Head :title="`Editar curso: ${curso.titulo} | Portal Deseret`" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- =========================================================
                 ENCABEZADO DE SECCIÓN CON TONO NARANJA TRANSLÚCIDO
            ========================================================== -->
            <section
                class="border-b border-orange-200/80 bg-orange-100/25 backdrop-blur-md shadow-lg shadow-orange-500/10">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <Link :href="route('cursos.index')"
                        class="mb-4 inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-orange-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                        Volver a mis cursos
                    </Link>

                    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-orange-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-orange-950">
                                    Portal Deseret
                                </span>
                            </div>
                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Editar curso
                            </h1>
                            <p
                                class="mt-2 max-w-2xl break-words text-xs leading-relaxed text-slate-600 font-medium sm:text-sm">
                                {{ curso.titulo }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =========================================================
                 CONTENIDO PRINCIPAL Y FORMULARIO
            ========================================================== -->
            <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 bg-orange-50 rounded-xl p-3 mt-2">
                <form class="space-y-6" @submit.prevent="actualizarCurso">

                    <!-- 1. INFORMACIÓN GENERAL -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">
                                1. Información general
                            </h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                Actualiza la información principal del curso.
                            </p>
                        </div>

                        <div class="space-y-5 p-5 sm:p-6">
                            <div>
                                <label for="titulo"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Nombre del curso <span class="text-red-500">*</span>
                                </label>
                                <input id="titulo" v-model="form.titulo" type="text" maxlength="180" required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.titulo }" />
                                <p v-if="form.errors.titulo" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.titulo }}
                                </p>
                            </div>

                            <div>
                                <div class="mb-1.5 flex justify-between gap-3">
                                    <label for="descripcion"
                                        class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Descripción <span class="text-red-500">*</span>
                                    </label>
                                    <span class="text-xs text-slate-400 font-medium">
                                        {{ form.descripcion.length }}/5000
                                    </span>
                                </div>
                                <textarea id="descripcion" v-model="form.descripcion" rows="6" maxlength="5000" required
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-relaxed text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.descripcion }"></textarea>
                                <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.descripcion }}
                                </p>
                            </div>

                            <div>
                                <label class="mb-3 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Tipo de curso <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.tipo === 'propio' ? 'border-orange-500 bg-orange-50/60 ring-2 ring-orange-200 shadow-md' : 'border-slate-300 hover:border-orange-300'">
                                        <input v-model="form.tipo" type="radio" value="propio"
                                            class="mt-1 h-4 w-4 accent-orange-600" />
                                        <span>
                                            <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">Curso
                                                propio</span>
                                            <span class="mt-0.5 block text-xs text-slate-500 font-medium">Una formación
                                                que organizas o impartes.</span>
                                        </span>
                                    </label>

                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.tipo === 'externo' ? 'border-orange-500 bg-orange-50/60 ring-2 ring-orange-200 shadow-md' : 'border-slate-300 hover:border-orange-300'">
                                        <input v-model="form.tipo" type="radio" value="externo"
                                            class="mt-1 h-4 w-4 accent-orange-600" />
                                        <span>
                                            <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">Curso
                                                externo</span>
                                            <span class="mt-0.5 block text-xs text-slate-500 font-medium">Una formación
                                                de otra plataforma.</span>
                                        </span>
                                    </label>
                                </div>
                                <p v-if="form.errors.tipo" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.tipo }}
                                </p>
                            </div>

                            <div v-if="esExterno">
                                <label for="url_externa"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Enlace del curso <span class="text-red-500">*</span>
                                </label>
                                <input id="url_externa" v-model="form.url_externa" type="url" maxlength="2048" required
                                    placeholder="https://..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.url_externa }" />
                                <p v-if="form.errors.url_externa" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.url_externa }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- 2. IMAGEN -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">
                                2. Imagen del curso
                            </h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                Puedes conservar la imagen actual o reemplazarla.
                            </p>
                        </div>

                        <div class="space-y-5 p-5 sm:p-6">
                            <div v-if="vistaPrevia || imagenOriginal"
                                class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                                <img :src="vistaPrevia || imagenOriginal" alt="Imagen del curso"
                                    class="max-h-96 w-full object-contain" />
                            </div>

                            <div v-else
                                class="flex min-h-40 flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="3" width="18" height="18" rx="2" />
                                    <circle cx="8.5" cy="8.5" r="1.5" />
                                    <path d="m21 15-5-5L5 21" />
                                </svg>
                                <span class="mt-2 text-xs font-semibold">No hay una imagen disponible</span>
                            </div>

                            <div>
                                <label for="imagen"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Reemplazar imagen
                                </label>
                                <input id="imagen" ref="inputImagen" type="file" accept="image/*"
                                    class="block w-full rounded-xl border border-slate-300 bg-white text-xs sm:text-sm text-slate-600 file:mr-4 file:border-0 file:bg-orange-50 file:px-4 file:py-3 file:font-bold file:text-orange-900 hover:file:bg-orange-100"
                                    @change="seleccionarImagen" />

                                <p class="mt-2 text-xs text-slate-500 font-medium">
                                    Si no seleccionas otra imagen, se conservará la actual. Tamaño máximo: 5 MB.
                                </p>

                                <p v-if="form.imagen" class="mt-2 text-xs sm:text-sm text-slate-700 font-bold">
                                    {{ form.imagen.name }} ({{ formatoTamano(form.imagen.size) }})
                                </p>

                                <p v-if="form.errors.imagen" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.imagen }}
                                </p>

                                <button v-if="vistaPrevia" type="button"
                                    class="mt-3 text-xs font-bold text-red-600 hover:text-red-800 transition"
                                    @click="quitarImagenNueva">
                                    Descartar nueva imagen
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- 3. MODALIDAD Y UBICACIÓN -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">
                                3. Modalidad y ubicación
                            </h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                Ajusta la modalidad y los datos de acceso.
                            </p>
                        </div>

                        <div class="space-y-5 p-5 sm:p-6">
                            <div>
                                <label class="mb-3 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Modalidad <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <label v-for="opcion in opcionesModalidad" :key="opcion.value"
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.modalidad === opcion.value ? 'border-orange-500 bg-orange-50/60 ring-2 ring-orange-200 shadow-md' : 'border-slate-300 hover:border-orange-300'">
                                        <input v-model="form.modalidad" type="radio" :value="opcion.value"
                                            class="mt-1 h-4 w-4 accent-orange-600" />
                                        <span>
                                            <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">
                                                {{ opcion.titulo }}
                                            </span>
                                            <span
                                                class="mt-0.5 block text-xs leading-relaxed text-slate-500 font-medium">
                                                {{ opcion.descripcion }}
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <p v-if="form.errors.modalidad" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.modalidad }}
                                </p>
                            </div>

                            <div v-if="esVirtual">
                                <label for="enlace_virtual"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Enlace de acceso
                                </label>
                                <input id="enlace_virtual" v-model="form.enlace_virtual" type="url" maxlength="2048"
                                    placeholder="https://..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.enlace_virtual }" />
                                <p v-if="form.errors.enlace_virtual" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.enlace_virtual }}
                                </p>
                            </div>

                            <div v-if="tieneUbicacionPresencial" class="space-y-4">
                                <div>
                                    <h3 class="text-xs sm:text-sm font-bold text-[#172A3D]">
                                        Ubicación del curso
                                    </h3>
                                    <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                        Selecciona el país, departamento o región y ciudad.
                                    </p>
                                </div>

                                <div class="overflow-hidden rounded-xl border border-slate-200">
                                    <UbicacionSelector :pais="form.pais" :departamento="form.departamento"
                                        :ciudad="form.ciudad" />
                                </div>

                                <div>
                                    <label for="direccion"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Dirección o lugar
                                    </label>
                                    <input id="direccion" v-model="form.direccion" type="text" maxlength="255"
                                        placeholder="Ej. Salón comunal, auditorio o dirección"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                        :class="{ 'border-red-500': form.errors.direccion }" />
                                    <p v-if="form.errors.direccion" class="mt-1 text-xs text-red-600 font-medium">
                                        {{ form.errors.direccion }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 4. PRECIO Y CUPOS -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">
                                4. Precio e inscripción
                            </h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                Configura el costo y la capacidad del curso.
                            </p>
                        </div>

                        <div class="space-y-5 p-5 sm:p-6">
                            <div class="flex items-start justify-between gap-4 rounded-xl border p-4 shadow-xs"
                                :class="form.es_gratuito ? 'border-orange-300 bg-orange-50/50' : 'border-slate-300 bg-white'">
                                <div>
                                    <p class="font-bold text-[#172A3D]">
                                        Curso gratuito
                                    </p>
                                    <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                        Activa si no tiene costo de inscripción.
                                    </p>
                                </div>
                                <input v-model="form.es_gratuito" type="checkbox"
                                    class="mt-1 h-5 w-5 rounded accent-orange-600" />
                            </div>

                            <div v-if="!esGratuito">
                                <label for="precio"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Precio en COP <span class="text-red-500">*</span>
                                </label>
                                <input id="precio" v-model="form.precio" type="number" min="0" step="0.01" required
                                    placeholder="Ej. 50000"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.precio }" />
                                <p v-if="form.errors.precio" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.precio }}
                                </p>
                            </div>

                            <div class="flex items-start justify-between gap-4 rounded-xl border p-4 shadow-xs"
                                :class="form.cupos_ilimitados ? 'border-orange-300 bg-orange-50/50' : 'border-slate-300 bg-white'">
                                <div>
                                    <p class="font-bold text-[#172A3D]">
                                        Cupos ilimitados
                                    </p>
                                    <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                        Desactiva si existe un máximo de participantes.
                                    </p>
                                </div>
                                <input v-model="form.cupos_ilimitados" type="checkbox"
                                    class="mt-1 h-5 w-5 rounded accent-orange-600" />
                            </div>

                            <div v-if="!form.cupos_ilimitados">
                                <label for="cantidad_cupos"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Cantidad de cupos <span class="text-red-500">*</span>
                                </label>
                                <input id="cantidad_cupos" v-model="form.cantidad_cupos" type="number" min="1" step="1"
                                    required placeholder="Ej. 30"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.cantidad_cupos }" />
                                <p v-if="form.errors.cantidad_cupos" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.cantidad_cupos }}
                                </p>
                            </div>

                            <div
                                class="flex items-start justify-between gap-4 rounded-xl border border-slate-300 bg-white p-4 shadow-xs">
                                <div>
                                    <p class="font-bold text-[#172A3D]">
                                        Permitir lista de espera
                                    </p>
                                    <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                        Permite registrar interesados cuando se llenen los cupos.
                                    </p>
                                </div>
                                <input v-model="form.permite_lista_espera" type="checkbox"
                                    class="mt-1 h-5 w-5 rounded accent-orange-600" />
                            </div>
                        </div>
                    </section>

                    <!-- 5. DURACIÓN E INTENSIDAD HORARIA -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">
                                5. Duración e intensidad horaria
                            </h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                Actualiza la frecuencia de las clases y la duración de la formación.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-3 sm:p-6">
                            <div>
                                <label for="frecuencia_clases"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Clases por semana
                                </label>
                                <input id="frecuencia_clases" v-model="form.frecuencia_clases" type="number" min="1"
                                    max="255" step="1" placeholder="Ej. 2"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.frecuencia_clases }" />
                                <p v-if="form.errors.frecuencia_clases" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.frecuencia_clases }}
                                </p>
                            </div>

                            <div>
                                <label for="duracion_sesion_minutos"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Minutos por clase
                                </label>
                                <input id="duracion_sesion_minutos" v-model="form.duracion_sesion_minutos" type="number"
                                    min="1" max="1440" step="1" placeholder="Ej. 90"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.duracion_sesion_minutos }" />
                                <p v-if="form.errors.duracion_sesion_minutos"
                                    class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.duracion_sesion_minutos }}
                                </p>
                            </div>

                            <div>
                                <label for="intensidad_horaria"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Total de horas
                                </label>
                                <input id="intensidad_horaria" v-model="form.intensidad_horaria" type="number" min="1"
                                    max="65535" step="1" placeholder="Ej. 40"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.intensidad_horaria }" />
                                <p v-if="form.errors.intensidad_horaria" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.intensidad_horaria }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- 6. FECHAS DEL CURSO -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">
                                6. Fechas del curso
                            </h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                Estas fechas corresponden al desarrollo de la formación.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                            <div>
                                <label for="fecha_inicio"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Fecha de inicio
                                </label>
                                <input id="fecha_inicio" :value="fechaInicioVisual" type="text" inputmode="numeric"
                                    maxlength="10" placeholder="dd/mm/yyyy" autocomplete="off"
                                    @input="actualizarFecha('fecha_inicio', $event.target.value)"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.fecha_inicio }" />
                                <p v-if="form.errors.fecha_inicio" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.fecha_inicio }}
                                </p>
                            </div>

                            <div>
                                <label for="fecha_fin"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Fecha de finalización
                                </label>
                                <input id="fecha_fin" :value="fechaFinVisual" type="text" inputmode="numeric"
                                    maxlength="10" placeholder="dd/mm/yyyy" autocomplete="off"
                                    @input="actualizarFecha('fecha_fin', $event.target.value)"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.fecha_fin }" />
                                <p v-if="form.errors.fecha_fin" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.fecha_fin }}
                                </p>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="fecha_limite_inscripcion"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Fecha límite de inscripción
                                </label>
                                <input id="fecha_limite_inscripcion" v-model="form.fecha_limite_inscripcion"
                                    type="datetime-local"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.fecha_limite_inscripcion }" />
                                <p v-if="form.errors.fecha_limite_inscripcion"
                                    class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.fecha_limite_inscripcion }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- 7. VIGENCIA DE PUBLICACIÓN -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">
                                7. Vigencia de la publicación
                            </h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                Controla el periodo en que la publicación puede mostrarse. No modifica las fechas del
                                curso.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                            <div>
                                <label for="publicado_desde"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Mostrar desde
                                </label>
                                <input id="publicado_desde" v-model="form.publicado_desde" type="datetime-local"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.publicado_desde }" />
                                <p v-if="form.errors.publicado_desde" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.publicado_desde }}
                                </p>
                            </div>

                            <div>
                                <label for="publicado_hasta"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Mostrar hasta
                                </label>
                                <input id="publicado_hasta" v-model="form.publicado_hasta" type="datetime-local"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-100"
                                    :class="{ 'border-red-500': form.errors.publicado_hasta }" />
                                <p v-if="form.errors.publicado_hasta" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.publicado_hasta }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- 8. ESTADO -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-orange-500/15">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <h2 class="text-lg font-bold text-[#172A3D]">
                                8. Estado del curso
                            </h2>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                Modifica el estado de la publicación.
                            </p>
                        </div>

                        <div class="space-y-3 p-5 sm:p-6">
                            <label v-for="opcion in opcionesEstado" :key="opcion.value"
                                class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                :class="form.estado === opcion.value ? 'border-orange-500 bg-orange-50/60 ring-2 ring-orange-200 shadow-md' : 'border-slate-300 hover:border-orange-300'">
                                <input v-model="form.estado" type="radio" :value="opcion.value"
                                    class="mt-1 h-4 w-4 accent-orange-600" />
                                <span>
                                    <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">{{ opcion.titulo
                                        }}</span>
                                    <span class="mt-0.5 block text-xs text-slate-500 font-medium">{{ opcion.descripcion
                                        }}</span>
                                </span>
                            </label>

                            <p v-if="form.errors.estado" class="mt-1 text-xs text-red-600 font-medium">
                                {{ form.errors.estado }}
                            </p>

                            <div
                                class="rounded-xl border border-orange-200 bg-orange-50 p-4 text-xs sm:text-sm leading-6 text-orange-950 font-medium shadow-xs">
                                <strong>Importante:</strong> «Finalizado» no se puede seleccionar manualmente. El
                                sistema debe asignarlo cuando se cumpla la fecha de finalización.
                            </div>
                        </div>
                    </section>

                    <!-- ERRORES GENERALES -->
                    <div v-if="form.hasErrors"
                        class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-2xl shadow-red-500/15">
                        <p class="font-bold text-red-900">
                            Revisa la información del formulario.
                        </p>
                        <p class="mt-1 text-xs sm:text-sm text-red-700 font-medium">
                            Hay campos que necesitan corrección antes de guardar.
                        </p>
                    </div>

                    <!-- ACCIONES -->
                    <div class="flex flex-col-reverse gap-3 pb-12 sm:flex-row sm:justify-end">
                        <Link :href="route('cursos.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                            Cancelar
                        </Link>

                        <button type="submit" :disabled="form.processing"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-600 px-7 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-orange-500/20 transition hover:bg-orange-700 disabled:cursor-not-allowed disabled:opacity-60">
                            <svg v-if="form.processing" class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4z" />
                            </svg>
                            <span>{{ form.processing ? 'Guardando cambios...' : 'Guardar cambios' }}</span>
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