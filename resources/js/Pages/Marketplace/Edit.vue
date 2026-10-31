<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import axios from 'axios'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UbicacionSelector from '@/Components/UbicacionSelector.vue'

const props = defineProps({
    publicacion: {
        type: Object,
        required: true,
    },
    categorias: {
        type: Array,
        default: () => [],
    },
    palabrasProhibidas: {
        type: Array,
        default: () => [],
    },
})

const inputImagenes = ref(null)
const inputVideo = ref(null)

const imagenesExistentes = ref(props.publicacion.imagenes ?? [])
const vistasPreviaNuevas = ref([])

const videoExistente = ref(props.publicacion.video ?? null)
const vistaPreviaVideoNuevo = ref(null)

/*
|--------------------------------------------------------------------------
| Ubicación corregida y enlazada correctamente
|--------------------------------------------------------------------------
*/

const ubicacion = ref({
    pais: props.publicacion.pais?.nombre || props.publicacion.pais || 'Colombia',
    departamento: props.publicacion.division_administrativa?.nombre || props.publicacion.departamento || '',
    ciudad: props.publicacion.ciudad?.nombre || props.publicacion.ciudad || '',
})

const form = useForm({
    _method: 'PUT',
    categoria_id: props.publicacion.categoria_id ?? '',
    subcategoria_id: props.publicacion.subcategoria_id ?? '',
    titulo: props.publicacion.titulo ?? '',
    descripcion: props.publicacion.descripcion ?? '',
    precio: props.publicacion.precio ?? '',
    es_gratis: Boolean(props.publicacion.es_gratis),
    estado_producto: props.publicacion.estado_producto ?? 'nuevo',

    pais_id: props.publicacion.pais_id ?? '',
    division_administrativa_id: props.publicacion.division_administrativa_id ?? '',
    ciudad_id: props.publicacion.ciudad_id ?? '',

    tipo_entrega: props.publicacion.tipo_entrega ?? 'acordar',
    rango_cobertura: props.publicacion.rango_cobertura ?? 'local',

    imagenes: [],
    imagenes_a_eliminar: [],
    video: null,
    eliminar_video: false,
})

/*
|--------------------------------------------------------------------------
| CORRECCIÓN MÁGICA CON IA
|--------------------------------------------------------------------------
*/
const cargandoIA = ref({
    titulo: false,
    descripcion: false,
})

async function aplicarCorreccionMagica(campo) {
    const textoActual = form[campo]
    if (!textoActual || !textoActual.trim()) return

    cargandoIA.value[campo] = true

    try {
        const response = await axios.post('/ai/correct-text', {
            text: textoActual,
            context_type: `marketplace_${campo}`,
        })

        if (response.data && response.data.improved_text) {
            form[campo] = response.data.improved_text
        }
    } catch (error) {
        console.error('Error al aplicar la corrección mágica:', error)
        alert('No fue posible conectar con el servicio de IA en este momento.')
    } finally {
        cargandoIA.value[campo] = false
    }
}

/*
|--------------------------------------------------------------------------
| MODERACIÓN
|--------------------------------------------------------------------------
*/
const alertaTitulo = ref('')
const alertaDescripcion = ref('')

function verificarPalabrasProhibidas(texto, tipo) {
    if (!texto || !props.palabrasProhibidas || props.palabrasProhibidas.length === 0) {
        if (tipo === 'titulo') alertaTitulo.value = ''
        if (tipo === 'descripcion') alertaDescripcion.value = ''
        return
    }

    const textoLower = texto.toLowerCase()
    const encontrada = props.palabrasProhibidas.find(palabra => {
        const palabraClean = palabra.trim().toLowerCase()
        return palabraClean.length > 0 && textoLower.includes(palabraClean)
    })

    if (encontrada) {
        const mensaje = `⚠️ Se ha detectado un término no permitido ("${encontrada}"). Por favor, modifícalo antes de actualizar.`
        if (tipo === 'titulo') alertaTitulo.value = mensaje
        if (tipo === 'descripcion') alertaDescripcion.value = mensaje
    } else {
        if (tipo === 'titulo') alertaTitulo.value = ''
        if (tipo === 'descripcion') alertaDescripcion.value = ''
    }
}

watch(() => form.titulo, (v) => verificarPalabrasProhibidas(v, 'titulo'))
watch(() => form.descripcion, (v) => verificarPalabrasProhibidas(v, 'descripcion'))

const categoriaSeleccionada = computed(() => {
    return props.categorias.find(c => Number(c.id) === Number(form.categoria_id)) || null
})

const subcategoriasFiltradas = computed(() => {
    return categoriaSeleccionada.value?.subcategorias || []
})

const estadosProducto = [
    { value: 'nuevo', label: 'Nuevo', descripcion: 'El producto no ha sido usado.' },
    { value: 'como_nuevo', label: 'Como nuevo', descripcion: 'Ha sido usado muy poco y está en excelente estado.' },
    { value: 'buen_estado', label: 'Buen estado', descripcion: 'Presenta señales normales de uso.' },
    { value: 'usado', label: 'Usado', descripcion: 'Tiene señales visibles de uso.' },
    { value: 'para_reparar', label: 'Para reparar', descripcion: 'Requiere reparación o mantenimiento.' },
]

const tiposEntrega = [
    { value: 'acordar', label: 'Acordar con el interesado' },
    { value: 'recogida', label: 'Recogida por el comprador' },
    { value: 'entrega', label: 'Entrega acordada' },
]

const rangosCobertura = [
    { value: 'local', label: 'Solo en mi ciudad / municipio', descripcion: 'Para entregas en mano o retiros presenciales en tu misma localidad.', icono: '📍' },
    { value: 'departamental', label: 'A nivel regional / departamental', descripcion: 'Disponible para despacho a ciudades y municipios vecinos.', icono: '🗺️' },
    { value: 'nacional', label: 'Todo el país', descripcion: 'Cuentas con disponibilidad de hacer envíos por transportadora nacional.', icono: '🇨🇴' },
    { value: 'internacional', label: 'Envíos internacionales', descripcion: 'Aplica para envíos al extranjero o productos sin barrera física.', icono: '🌍' },
]

watch(() => form.es_gratis, (gratuito) => {
    if (gratuito) {
        form.precio = ''
        form.clearErrors('precio')
    }
})

watch(() => form.categoria_id, () => {
    form.subcategoria_id = ''
    form.clearErrors('subcategoria_id')
})

/*
|--------------------------------------------------------------------------
| Gestión de Archivos
|--------------------------------------------------------------------------
*/

function eliminarImagenExistente(id) {
    form.imagenes_a_eliminar.push(id)
    imagenesExistentes.value = imagenesExistentes.value.filter(img => img.id !== id)
}

function seleccionarImagenes(event) {
    const archivos = Array.from(event.target.files || [])
    if (!archivos.length) return

    const totalActual = imagenesExistentes.value.length + form.imagenes.length
    const disponibles = 8 - totalActual

    if (archivos.length > disponibles) {
        form.setError('imagenes', `Solo puedes agregar ${disponibles} imagen(es) más. El máximo total es de 8.`)
        event.target.value = ''
        return
    }

    for (const archivo of archivos) {
        if (!archivo.type.startsWith('image/') || archivo.size > 5 * 1024 * 1024) {
            form.setError('imagenes', 'Cada archivo debe ser una imagen menor a 5 MB.')
            event.target.value = ''
            return
        }
        form.imagenes.push(archivo)
        vistasPreviaNuevas.value.push({ url: URL.createObjectURL(archivo) })
    }
    event.target.value = ''
}

function eliminarNuevaImagen(index) {
    URL.revokeObjectURL(vistasPreviaNuevas.value[index].url)
    vistasPreviaNuevas.value.splice(index, 1)
    form.imagenes.splice(index, 1)
}

function seleccionarVideo(event) {
    const archivo = event.target.files?.[0]
    if (!archivo) return

    if (!['video/mp4', 'video/webm', 'video/quicktime'].includes(archivo.type) || archivo.size > 50 * 1024 * 1024) {
        form.setError('video', 'El video debe ser MP4, WebM o MOV y pesar menos de 50 MB.')
        event.target.value = ''
        return
    }

    form.video = archivo
    form.eliminar_video = false
    videoExistente.value = null
    vistaPreviaVideoNuevo.value = URL.createObjectURL(archivo)
    event.target.value = ''
}

function eliminarVideo() {
    form.video = null
    form.eliminar_video = true
    videoExistente.value = null
    if (vistaPreviaVideoNuevo.value) {
        URL.revokeObjectURL(vistaPreviaVideoNuevo.value)
        vistaPreviaVideoNuevo.value = null
    }
}

/*
|--------------------------------------------------------------------------
| Actualización directa
|--------------------------------------------------------------------------
*/

function actualizar() {
    form.post(route('marketplace.update', props.publicacion.id), {
        forceFormData: true,
        preserveScroll: true,
        onError: errors => {
            console.error('Errores al actualizar:', errors)
        },
    })
}
</script>

<template>

    <Head :title="`Editar: ${publicacion.titulo} | Marketplace`" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">
            <section class="border-b border-blue-200/80 bg-blue-50 backdrop-blur-md shadow-lg shadow-blue-500/10">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <Link :href="route('marketplace.show', publicacion.id)"
                        class="mb-4 inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-blue-600">
                        ← Cancelar y volver al producto
                    </Link>
                    <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl mt-2">
                        Editar publicación
                    </h1>
                </div>
            </section>

            <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 bg-blue-50 rounded-xl p-3 mt-2">
                <form @submit.prevent="actualizar" class="space-y-6">

                    <!-- INFORMACIÓN -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15">
                        <div class="border-b border-slate-100 bg-blue-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">1. Información del producto</h2>
                        </div>
                        <div class="space-y-5 px-5 py-6 sm:px-6">
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Categoría
                                    *</label>
                                <select v-model="form.categoria_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D]">
                                    <option value="">Selecciona una categoría</option>
                                    <option v-for="cat in props.categorias" :key="cat.id" :value="cat.id">{{ cat.nombre
                                        }}</option>
                                </select>
                            </div>

                            <div v-if="subcategoriasFiltradas.length">
                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Subcategoría</label>
                                <select v-model="form.subcategoria_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D]">
                                    <option value="">Selecciona una subcategoría</option>
                                    <option v-for="sub in subcategoriasFiltradas" :key="sub.id" :value="sub.id">{{
                                        sub.nombre }}</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Título
                                    *</label>
                                <input v-model="form.titulo" type="text" maxlength="255"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D]" />
                                <p v-if="alertaTitulo" class="mt-1 text-xs text-amber-600 font-bold">{{ alertaTitulo }}
                                </p>
                            </div>

                            <div>
                                <label
                                    class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5 block">Descripción
                                    *</label>
                                <textarea v-model="form.descripcion" rows="6"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm text-[#172A3D]"></textarea>
                                <p v-if="alertaDescripcion" class="mt-1 text-xs text-amber-600 font-bold">{{
                                    alertaDescripcion }}</p>
                            </div>

                            <div>
                                <label
                                    class="mb-3 block text-xs font-bold text-slate-700 uppercase tracking-wider">Estado
                                    del producto *</label>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label v-for="estado in estadosProducto" :key="estado.value"
                                        class="cursor-pointer rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.estado_producto === estado.value ? 'border-blue-500 bg-blue-50/60' : 'border-slate-300'">
                                        <div class="flex items-start gap-3">
                                            <input v-model="form.estado_producto" type="radio" :value="estado.value"
                                                class="mt-1 accent-blue-600" />
                                            <div>
                                                <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">{{
                                                    estado.label }}</span>
                                                <span class="mt-0.5 block text-xs text-slate-500">{{ estado.descripcion
                                                    }}</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- PRECIO -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15">
                        <div class="border-b border-slate-100 bg-blue-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">2. Precio</h2>
                        </div>
                        <div class="space-y-5 px-5 py-6 sm:px-6">
                            <label class="flex cursor-pointer items-center gap-3">
                                <input v-model="form.es_gratis" type="checkbox"
                                    class="h-5 w-5 rounded border-slate-300 text-blue-600" />
                                <span class="text-xs sm:text-sm font-bold text-[#172A3D]">Este producto es
                                    gratuito</span>
                            </label>
                            <div v-if="!form.es_gratis">
                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">Precio
                                    *</label>
                                <input v-model="form.precio" type="number" min="0" step="0.01"
                                    class="w-full rounded-xl border border-slate-300 bg-white py-3 px-4 text-xs sm:text-sm font-semibold text-[#172A3D]" />
                            </div>
                        </div>
                    </section>

                    <!-- UBICACIÓN (CORREGIDA CON EL V-MODEL DE UBICACIÓN) -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15">
                        <div class="border-b border-slate-100 bg-blue-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">3. Ubicación del producto</h2>
                        </div>
                        <div class="space-y-5 px-5 py-6 sm:px-6">
                            <UbicacionSelector v-model:ubicacion="ubicacion" v-model:pais-id="form.pais_id"
                                v-model:division-id="form.division_administrativa_id"
                                v-model:ciudad-id="form.ciudad_id" />
                        </div>
                    </section>

                    <!-- FORMA DE ENTREGA -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15">
                        <div class="border-b border-slate-100 bg-blue-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">4. Forma de entrega</h2>
                        </div>
                        <div class="space-y-6 px-5 py-6 sm:px-6">
                            <div>
                                <label
                                    class="mb-2 block text-xs font-bold text-slate-700 uppercase tracking-wider">Modalidad
                                    de entrega *</label>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <label v-for="tipo in tiposEntrega" :key="tipo.value"
                                        class="cursor-pointer rounded-xl border p-4 bg-white"
                                        :class="form.tipo_entrega === tipo.value ? 'border-blue-500 bg-blue-50/60' : 'border-slate-300'">
                                        <div class="flex items-center gap-3">
                                            <input v-model="form.tipo_entrega" type="radio" :value="tipo.value"
                                                class="accent-blue-600" />
                                            <span class="text-xs sm:text-sm font-bold text-[#172A3D]">{{ tipo.label
                                                }}</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-xs font-bold text-slate-700 uppercase tracking-wider">Disponibilidad
                                    de despacho *</label>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label v-for="rango in rangosCobertura" :key="rango.value"
                                        class="cursor-pointer rounded-xl border p-4 bg-white"
                                        :class="form.rango_cobertura === rango.value ? 'border-blue-500 bg-blue-50/60' : 'border-slate-300'">
                                        <div class="flex items-start gap-3">
                                            <input v-model="form.rango_cobertura" type="radio" :value="rango.value"
                                                class="mt-1 accent-blue-600" />
                                            <div>
                                                <span class="text-xs sm:text-sm font-bold text-[#172A3D]">{{ rango.icono
                                                    }} {{ rango.label }}</span>
                                                <span class="mt-1 block text-xs text-slate-500">{{ rango.descripcion
                                                    }}</span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- IMÁGENES -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15">
                        <div class="border-b border-slate-100 bg-blue-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">5. Imágenes</h2>
                        </div>
                        <div class="space-y-5 px-5 py-6 sm:px-6">
                            <input ref="inputImagenes" type="file" accept="image/*" multiple class="hidden"
                                @change="seleccionarImagenes" />

                            <div v-if="imagenesExistentes.length" class="space-y-2">
                                <p class="text-xs font-bold text-slate-600 uppercase">Actuales:</p>
                                <div class="grid gap-4 sm:grid-cols-4">
                                    <div v-for="img in imagenesExistentes" :key="img.id"
                                        class="relative overflow-hidden rounded-xl border bg-slate-50">
                                        <img :src="`/storage/${img.ruta}`" class="h-40 w-full object-cover" />
                                        <button type="button"
                                            class="absolute right-2 top-2 rounded-full bg-white px-2.5 py-1 text-xs font-bold text-red-600 shadow"
                                            @click="eliminarImagenExistente(img.id)">Eliminar</button>
                                    </div>
                                </div>
                            </div>

                            <button type="button"
                                class="rounded-xl border border-dashed border-slate-300 p-4 text-xs sm:text-sm font-bold text-slate-700 w-full hover:bg-blue-50"
                                @click="inputImagenes?.click()">
                                + Agregar más imágenes
                            </button>

                            <div v-if="vistasPreviaNuevas.length" class="grid gap-4 sm:grid-cols-4">
                                <div v-for="(img, idx) in vistasPreviaNuevas" :key="idx"
                                    class="relative overflow-hidden rounded-xl border bg-blue-50">
                                    <img :src="img.url" class="h-40 w-full object-cover" />
                                    <button type="button"
                                        class="absolute right-2 top-2 rounded-full bg-white px-2 py-1 text-xs font-bold text-red-600 shadow"
                                        @click="eliminarNuevaImagen(idx)">Quitar</button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- VIDEO -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15">
                        <div class="border-b border-slate-100 bg-blue-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">6. Video</h2>
                        </div>
                        <div class="space-y-5 px-5 py-6 sm:px-6">
                            <input ref="inputVideo" type="file" accept="video/mp4,video/webm,video/quicktime"
                                class="hidden" @change="seleccionarVideo" />

                            <div v-if="videoExistente && !form.eliminar_video"
                                class="relative overflow-hidden rounded-xl border bg-black">
                                <video :src="`/storage/${videoExistente.ruta}`" controls
                                    class="max-h-[300px] w-full"></video>
                                <button type="button"
                                    class="absolute right-3 top-3 rounded bg-white px-3 py-1 text-xs font-bold text-red-600 shadow"
                                    @click="eliminarVideo">Eliminar video</button>
                            </div>

                            <button v-if="(!videoExistente || form.eliminar_video) && !form.video" type="button"
                                class="rounded-xl border border-dashed border-slate-300 p-4 text-xs sm:text-sm font-bold text-slate-700 w-full hover:bg-blue-50"
                                @click="inputVideo?.click()">
                                + Agregar video
                            </button>

                            <div v-if="vistaPreviaVideoNuevo"
                                class="relative overflow-hidden rounded-xl border bg-black">
                                <video :src="vistaPreviaVideoNuevo" controls class="max-h-[300px] w-full"></video>
                                <button type="button"
                                    class="absolute right-3 top-3 rounded bg-white px-3 py-1 text-xs font-bold text-red-600 shadow"
                                    @click="eliminarVideo">Quitar video</button>
                            </div>
                        </div>
                    </section>

                    <div class="flex justify-end gap-3 pb-12">
                        <Link :href="route('marketplace.show', publicacion.id)"
                            class="rounded-xl border bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700">
                            Cancelar</Link>
                        <button type="submit" :disabled="form.processing"
                            class="rounded-xl bg-blue-600 px-7 py-3 text-xs sm:text-sm font-black text-white hover:bg-blue-700">
                            {{ form.processing ? 'Actualizando...' : 'Actualizar publicación' }}
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