<script setup>
import { computed, onBeforeUnmount, ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { route } from 'ziggy-js'
import UbicacionSelector from '@/Components/UbicacionSelector.vue'

const props = defineProps({
    servicio: {
        type: Object,
        required: true,
    },
})

const inputImagen = ref(null)
const vistaPrevia = ref(null)

const servicio = props.servicio

const form = useForm({
    titulo: servicio.titulo ?? '',
    descripcion: servicio.descripcion ?? '',
    categoria: servicio.categoria ?? '',

    modalidad: servicio.modalidad ?? 'presencial',

    precio_tipo: servicio.precio_tipo ?? 'a_convenir',
    precio: servicio.precio ?? '',
    moneda: servicio.moneda ?? 'COP',

    imagen_tipo: servicio.imagen_tipo ?? 'perfil',
    imagen: null,

    pais: servicio.pais ?? '',
    departamento: servicio.departamento ?? '',
    ciudad: servicio.ciudad ?? '',
    direccion: servicio.direccion ?? '',
    mostrar_direccion: Boolean(servicio.mostrar_direccion),
    radio_alcance_km: servicio.radio_alcance_km ?? 15,
})

const categorias = [
    'Tecnología e informática',
    'Diseño y creatividad',
    'Educación y formación',
    'Reparaciones y mantenimiento',
    'Construcción y hogar',
    'Belleza y cuidado personal',
    'Salud y bienestar',
    'Transporte y logística',
    'Eventos y fotografía',
    'Servicios profesionales',
    'Servicios para empresas',
    'Otros servicios',
]

const modalidadesPrecio = [
    { value: 'hora', label: 'Por hora' },
    { value: 'dia', label: 'Por día' },
    { value: 'servicio', label: 'Por servicio' },
    { value: 'proyecto', label: 'Por proyecto' },
    { value: 'fijo', label: 'Precio fijo' },
    { value: 'a_convenir', label: 'A convenir' },
]

const modalidades = [
    {
        value: 'presencial',
        titulo: 'Presencial',
        descripcion: 'Atención en una ubicación física',
    },
    {
        value: 'remoto',
        titulo: 'Remoto',
        descripcion: 'Atención a distancia',
    },
    {
        value: 'hibrido',
        titulo: 'Híbrido',
        descripcion: 'Presencial y remoto',
    },
]

const precioEsConvenido = computed(
    () => form.precio_tipo === 'a_convenir',
)

const imagenActual = computed(() => {
    if (
        form.imagen_tipo === 'archivo' &&
        servicio.imagen
    ) {
        return `/storage/${servicio.imagen}`
    }

    if (
        form.imagen_tipo === 'perfil' &&
        servicio.user?.foto
    ) {
        return servicio.user.foto.startsWith('http')
            ? servicio.user.foto
            : `/storage/${servicio.user.foto}`
    }

    return null
})

const imagenVisible = computed(
    () => vistaPrevia.value || imagenActual.value,
)

function seleccionarImagen(event) {
    const archivo = event.target.files?.[0]

    if (!archivo) return

    if (!archivo.type.startsWith('image/')) {
        form.setError(
            'imagen',
            'Selecciona un archivo de imagen válido.',
        )

        event.target.value = ''
        return
    }

    if (archivo.size > 5 * 1024 * 1024) {
        form.setError(
            'imagen',
            'La imagen no puede superar los 5 MB.',
        )

        event.target.value = ''
        return
    }

    form.clearErrors('imagen')

    form.imagen_tipo = 'archivo'
    form.imagen = archivo

    liberarVistaPrevia()
    vistaPrevia.value = URL.createObjectURL(archivo)
}

function liberarVistaPrevia() {
    if (vistaPrevia.value) {
        URL.revokeObjectURL(vistaPrevia.value)
        vistaPrevia.value = null
    }
}

function usarFotoPerfil() {
    form.imagen_tipo = 'perfil'
    form.imagen = null

    liberarVistaPrevia()

    if (inputImagen.value) {
        inputImagen.value.value = ''
    }

    form.clearErrors('imagen')
}

function conservarImagenActual() {
    form.imagen = null
    liberarVistaPrevia()

    if (inputImagen.value) {
        inputImagen.value.value = ''
    }
}

function guardarCambios() {
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('servicios.update', servicio.id), {
        forceFormData: true,
        preserveScroll: true,
    })
}

function formatoTamano(bytes) {
    if (!bytes) return ''

    return `${(bytes / 1024 / 1024).toFixed(2)} MB`
}

onBeforeUnmount(() => {
    liberarVistaPrevia()
})
</script>

<template>

    <Head :title="`Editar: ${servicio.titulo} | Servicios`" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">

        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- =====================================================
                 ENCABEZADO CON TONO VERDE / ESMERALDA TRANSLÚCIDO
            ====================================================== -->
            <section
                class="border-b border-emerald-200/80 bg-emerald-100/25 backdrop-blur-md shadow-lg shadow-emerald-500/10">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <Link :href="route('servicios.show', servicio.id)"
                        class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-emerald-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                        Volver al servicio
                    </Link>

                    <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2 ">
                                <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-emerald-900">
                                    Gestionar publicación
                                </span>
                            </div>
                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Editar servicio
                            </h1>
                            <p class="mt-1 text-sm text-slate-600 font-medium max-w-2xl">
                                Actualiza la información de tu servicio. Los cambios se reflejarán en la publicación.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =====================================================
                 CONTENIDO PRINCIPAL / FORMULARIO
            ====================================================== -->
            <div class="py-8">
                <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 bg-emerald-50 rounded-xl p-3 mt-2">

                    <form class="space-y-6" @submit.prevent="guardarCambios">

                        <!-- 1. INFORMACIÓN -->
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-emerald-500/15">
                            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                                <h2 class="text-lg font-bold text-[#172A3D]">
                                    1. Información del servicio
                                </h2>

                                <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                    Modifica los datos principales.
                                </p>
                            </div>

                            <div class="space-y-5 p-5 sm:p-6">
                                <div>
                                    <label for="titulo"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Nombre del servicio <span class="text-red-500">*</span>
                                    </label>

                                    <input id="titulo" v-model="form.titulo" type="text" maxlength="150" required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100" />

                                    <p v-if="form.errors.titulo" class="mt-1 text-xs text-red-600 font-medium">
                                        {{ form.errors.titulo }}
                                    </p>
                                </div>

                                <div>
                                    <label for="categoria"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Categoría <span class="text-red-500">*</span>
                                    </label>

                                    <select id="categoria" v-model="form.categoria" required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                        <option v-if="!categorias.includes(form.categoria)" :value="form.categoria">
                                            {{ form.categoria }}
                                        </option>

                                        <option v-for="categoria in categorias" :key="categoria" :value="categoria">
                                            {{ categoria }}
                                        </option>
                                    </select>

                                    <p v-if="form.errors.categoria" class="mt-1 text-xs text-red-600 font-medium">
                                        {{ form.errors.categoria }}
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

                                    <textarea id="descripcion" v-model="form.descripcion" rows="6" maxlength="5000"
                                        required
                                        class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-relaxed text-[#172A3D] outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"></textarea>

                                    <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-600 font-medium">
                                        {{ form.errors.descripcion }}
                                    </p>
                                </div>

                                <div>
                                    <label class="mb-3 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Modalidad
                                    </label>

                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                        <label v-for="opcion in modalidades" :key="opcion.value"
                                            class="flex cursor-pointer gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                            :class="form.modalidad === opcion.value
                                                ? 'border-emerald-600 bg-emerald-50/60 ring-2 ring-emerald-200 shadow-md'
                                                : 'border-slate-300 hover:border-emerald-300'">
                                            <input v-model="form.modalidad" type="radio" :value="opcion.value"
                                                class="mt-1 h-4 w-4 accent-emerald-700" />

                                            <span>
                                                <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">
                                                    {{ opcion.titulo }}
                                                </span>

                                                <span class="mt-0.5 block text-xs text-slate-500 font-medium">
                                                    {{ opcion.descripcion }}
                                                </span>
                                            </span>
                                        </label>
                                    </div>

                                    <p v-if="form.errors.modalidad" class="mt-1 text-xs text-red-600 font-medium">
                                        {{ form.errors.modalidad }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <!-- 2. PRECIO -->
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-emerald-500/15">
                            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                                <h2 class="text-lg font-bold text-[#172A3D]">
                                    2. Precio del servicio
                                </h2>
                            </div>

                            <div class="space-y-5 p-5 sm:p-6">
                                <div>
                                    <label for="precio_tipo"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Modalidad de cobro
                                    </label>

                                    <select id="precio_tipo" v-model="form.precio_tipo" required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                        <option v-for="opcion in modalidadesPrecio" :key="opcion.value"
                                            :value="opcion.value">
                                            {{ opcion.label }}
                                        </option>
                                    </select>
                                </div>

                                <div v-if="!precioEsConvenido" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="precio"
                                            class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Valor <span class="text-red-500">*</span>
                                        </label>

                                        <input id="precio" v-model="form.precio" type="number" min="0" step="0.01"
                                            required
                                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100" />

                                        <p v-if="form.errors.precio" class="mt-1 text-xs text-red-600 font-medium">
                                            {{ form.errors.precio }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="moneda"
                                            class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Moneda
                                        </label>

                                        <select id="moneda" v-model="form.moneda"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                            <option value="COP">
                                                COP — Peso colombiano
                                            </option>

                                            <option value="USD">
                                                USD — Dólar estadounidense
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div v-if="precioEsConvenido"
                                    class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs sm:text-sm text-amber-900 font-medium shadow-xs">
                                    El precio se mostrará como «A convenir».
                                </div>
                            </div>
                        </section>

                        <!-- 3. IMAGEN -->
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-emerald-500/15">
                            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                                <h2 class="text-lg font-bold text-[#172A3D]">
                                    3. Imagen del servicio
                                </h2>

                                <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                    Puedes conservar la imagen actual, reemplazarla
                                    o volver a usar tu foto de perfil.
                                </p>
                            </div>

                            <div class="space-y-5 p-5 sm:p-6">
                                <div v-if="imagenVisible"
                                    class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                                    <img :src="imagenVisible" alt="Imagen actual del servicio"
                                        class="max-h-96 w-full object-contain" />

                                    <div class="border-t border-slate-100 px-4 py-3 bg-white">
                                        <p class="text-xs sm:text-sm font-bold text-slate-700">
                                            {{
                                                vistaPrevia
                                                    ? 'Nueva imagen seleccionada'
                                                    : 'Imagen actual'
                                            }}
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.imagen_tipo === 'perfil'
                                            ? 'border-emerald-600 bg-emerald-50/60 ring-2 ring-emerald-200 shadow-md'
                                            : 'border-slate-300 hover:border-emerald-300'">
                                        <input v-model="form.imagen_tipo" type="radio" value="perfil"
                                            class="mt-1 h-4 w-4 accent-emerald-700" @change="usarFotoPerfil" />

                                        <span>
                                            <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">
                                                Usar foto de perfil
                                            </span>

                                            <span class="mt-0.5 block text-xs text-slate-500 font-medium">
                                                No se duplica la imagen.
                                            </span>
                                        </span>
                                    </label>

                                    <label
                                        class="flex cursor-pointer items-start gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.imagen_tipo === 'archivo'
                                            ? 'border-emerald-600 bg-emerald-50/60 ring-2 ring-emerald-200 shadow-md'
                                            : 'border-slate-300 hover:border-emerald-300'">
                                        <input v-model="form.imagen_tipo" type="radio" value="archivo"
                                            class="mt-1 h-4 w-4 accent-emerald-700" />

                                        <span>
                                            <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">
                                                Imagen personalizada
                                            </span>

                                            <span class="mt-0.5 block text-xs text-slate-500 font-medium">
                                                Logo, tarjeta o promoción.
                                            </span>
                                        </span>
                                    </label>
                                </div>

                                <div v-if="form.imagen_tipo === 'archivo'">
                                    <label for="imagen"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        {{ servicio.imagen ? 'Reemplazar imagen' : 'Seleccionar imagen' }}
                                    </label>

                                    <input id="imagen" ref="inputImagen" type="file" accept="image/*"
                                        class="block w-full rounded-xl border border-slate-300 bg-white text-xs sm:text-sm text-slate-600 file:mr-4 file:border-0 file:bg-emerald-50 file:px-4 file:py-3 file:font-bold file:text-emerald-900 hover:file:bg-emerald-100"
                                        @change="seleccionarImagen" />

                                    <p class="mt-2 text-xs text-slate-500 font-medium">
                                        Tamaño máximo: 5 MB.
                                        Si no seleccionas otra imagen, se conservará la actual.
                                    </p>

                                    <p v-if="form.imagen" class="mt-2 text-xs sm:text-sm text-slate-700 font-bold">
                                        {{ form.imagen.name }}
                                        ({{ formatoTamano(form.imagen.size) }})
                                    </p>

                                    <p v-if="form.errors.imagen" class="mt-1 text-xs text-red-600 font-medium">
                                        {{ form.errors.imagen }}
                                    </p>

                                    <button v-if="vistaPrevia" type="button"
                                        class="mt-3 text-xs font-bold text-red-600 hover:text-red-800 transition"
                                        @click="conservarImagenActual">
                                        Deshacer cambio de imagen
                                    </button>
                                </div>
                            </div>
                        </section>

                        <!-- 4. UBICACIÓN -->
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-emerald-500/15">
                            <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                                <h2 class="text-lg font-bold text-[#172A3D]">
                                    4. Ubicación
                                </h2>
                            </div>

                            <div class="p-5 sm:p-6">
                                <UbicacionSelector v-model:pais="form.pais" v-model:departamento="form.departamento"
                                    v-model:ciudad="form.ciudad" />
                            </div>
                        </section>

                        <!-- ESTADO ACTUAL -->
                        <section
                            class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-emerald-500/15 sm:p-6">
                            <h2 class="font-bold text-[#172A3D]">
                                Estado de la publicación
                            </h2>

                            <p class="mt-2 text-xs sm:text-sm text-slate-600 font-medium">
                                Estado actual:
                                <span class="ml-1 inline-flex rounded-full px-3 py-1 text-xs font-bold" :class="servicio.estado === 'activo'
                                    ? 'bg-emerald-100 text-emerald-800 border border-emerald-200'
                                    : 'bg-amber-100 text-amber-800 border border-amber-200'">
                                    {{ servicio.estado === 'activo' ? 'Activo' : 'Pausado' }}
                                </span>
                            </p>

                            <p class="mt-1 text-xs text-slate-500 font-medium">
                                El estado se puede cambiar desde las acciones de la publicación.
                            </p>
                        </section>

                        <!-- ERRORES -->
                        <div v-if="form.hasErrors"
                            class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-lg shadow-red-500/10">
                            <p class="font-bold text-red-900">
                                Revisa la información del formulario.
                            </p>

                            <p class="mt-1 text-xs sm:text-sm text-red-700 font-medium">
                                Hay campos que necesitan corrección antes de guardar los cambios.
                            </p>
                        </div>

                        <!-- ACCIONES -->
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end pb-12">
                            <Link :href="route('servicios.show', servicio.id)"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                Cancelar
                            </Link>

                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-7 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-emerald-500/20 transition hover:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-60">
                                <svg v-if="form.processing" class="h-5 w-5 animate-spin"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />

                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 0 1 8-8V0C5.37 0 0 5.37 0 12h4z" />
                                </svg>

                                {{
                                    form.processing
                                        ? 'Guardando cambios...'
                                        : 'Guardar cambios'
                                }}
                            </button>
                        </div>
                    </form>

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