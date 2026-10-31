<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import axios from 'axios'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UbicacionSelector from '@/Components/UbicacionSelector.vue'

const props = defineProps({
    categorias: {
        type: Array,
        default: () => [],
    },
    palabrasProhibidas: {
        type: Array,
        default: () => [],
    },
    ubicacionDefecto: {
        type: Object,
        default: () => ({}),
    },
})

/*
|--------------------------------------------------------------------------
| Archivos
|--------------------------------------------------------------------------
*/

const inputImagenes = ref(null)
const inputVideo = ref(null)

const vistasPrevia = ref([])
const vistaPreviaVideo = ref(null)

/*
|--------------------------------------------------------------------------
| Ubicación Visual
|--------------------------------------------------------------------------
*/

const ubicacion = ref({
    pais: props.ubicacionDefecto?.pais || 'Colombia',
    departamento: props.ubicacionDefecto?.departamento || '',
    ciudad: props.ubicacionDefecto?.ciudad || '',
})

/*
|--------------------------------------------------------------------------
| Formulario
|--------------------------------------------------------------------------
*/

const form = useForm({
    categoria_id: '',
    subcategoria_id: '',
    titulo: '',
    descripcion: '',
    precio: '',
    es_gratis: false,
    estado_producto: 'nuevo',

    // Inicialización con los IDs resueltos del usuario autenticado
    pais_id: props.ubicacionDefecto?.pais_id || '',
    division_administrativa_id: props.ubicacionDefecto?.division_administrativa_id || '',
    ciudad_id: props.ubicacionDefecto?.ciudad_id || '',

    tipo_entrega: 'acordar',
    rango_cobertura: 'local',
    imagenes: [],
    video: null,
})

/*
|--------------------------------------------------------------------------
| Observadores de cambio manual de ubicación
|--------------------------------------------------------------------------
| Si el usuario cambia manualmente el departamento o la ciudad en el
| selector, reseteamos los IDs para obligar a resolverlos antes de publicar.
|--------------------------------------------------------------------------
*/

watch(() => ubicacion.value.departamento, (nuevo, viejo) => {
    if (viejo && nuevo !== viejo) {
        form.division_administrativa_id = ''
        form.ciudad_id = ''
    }
})

watch(() => ubicacion.value.ciudad, (nuevo, viejo) => {
    if (viejo && nuevo !== viejo) {
        form.ciudad_id = ''
    }
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
| MODERACIÓN EN TIEMPO REAL DE PALABRAS PROHIBIDAS
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
        const mensaje = `⚠️ Se ha detectado un término no permitido ("${encontrada}"). Por favor, modifícalo antes de publicar.`
        if (tipo === 'titulo') alertaTitulo.value = mensaje
        if (tipo === 'descripcion') alertaDescripcion.value = mensaje
    } else {
        if (tipo === 'titulo') alertaTitulo.value = ''
        if (tipo === 'descripcion') alertaDescripcion.value = ''
    }
}

watch(() => form.titulo, (nuevoValor) => {
    verificarPalabrasProhibidas(nuevoValor, 'titulo')
})

watch(() => form.descripcion, (nuevoValor) => {
    verificarPalabrasProhibidas(nuevoValor, 'descripcion')
})

/*
|--------------------------------------------------------------------------
| Categorías / subcategorías
|--------------------------------------------------------------------------
*/

const categoriaSeleccionada = computed(() => {
    return props.categorias.find(
        categoria => Number(categoria.id) === Number(form.categoria_id)
    ) || null
})

const subcategoriasFiltradas = computed(() => {
    return categoriaSeleccionada.value?.subcategorias || []
})

/*
|--------------------------------------------------------------------------
| Estados del producto
|--------------------------------------------------------------------------
*/

const estadosProducto = [
    {
        value: 'nuevo',
        label: 'Nuevo',
        descripcion: 'El producto no ha sido usado.',
    },
    {
        value: 'como_nuevo',
        label: 'Como nuevo',
        descripcion: 'Ha sido usado muy poco y está en excelente estado.',
    },
    {
        value: 'buen_estado',
        label: 'Buen estado',
        descripcion: 'Presenta señales normales de uso.',
    },
    {
        value: 'usado',
        label: 'Usado',
        descripcion: 'Tiene señales visibles de uso.',
    },
    {
        value: 'para_reparar',
        label: 'Para reparar',
        descripcion: 'Requiere reparación o mantenimiento.',
    },
]

/*
|--------------------------------------------------------------------------
| Tipo de entrega
|--------------------------------------------------------------------------
*/

const tiposEntrega = [
    {
        value: 'acordar',
        label: 'Acordar con el interesado',
    },
    {
        value: 'recogida',
        label: 'Recogida por el comprador',
    },
    {
        value: 'entrega',
        label: 'Entrega acordada',
    },
]

/*
|--------------------------------------------------------------------------
| Rangos de Cobertura / Exhibición
|--------------------------------------------------------------------------
*/

const rangosCobertura = [
    {
        value: 'local',
        label: 'Solo en mi ciudad / municipio',
        descripcion: 'Para entregas en mano o retiros presenciales en tu misma localidad.',
        icono: '📍',
    },
    {
        value: 'departamental',
        label: 'A nivel regional / departamental',
        descripcion: 'Disponible para despacho a ciudades y municipios vecinos.',
        icono: '🗺️',
    },
    {
        value: 'nacional',
        label: 'Todo el país',
        descripcion: 'Cuentas con disponibilidad de hacer envíos por transportadora nacional.',
        icono: '🇨🇴',
    },
    {
        value: 'internacional',
        label: 'Envíos internacionales',
        descripcion: 'Aplica para envíos al extranjero o productos sin barrera física.',
        icono: '🌍',
    },
]

/*
|--------------------------------------------------------------------------
| Precio
|--------------------------------------------------------------------------
*/

watch(
    () => form.es_gratis,
    gratuito => {
        if (gratuito) {
            form.precio = ''
            form.clearErrors('precio')
        }
    },
)

/*
|--------------------------------------------------------------------------
| Categoría
|--------------------------------------------------------------------------
*/

watch(
    () => form.categoria_id,
    () => {
        form.subcategoria_id = ''
        form.clearErrors('subcategoria_id')
    },
)

/*
|--------------------------------------------------------------------------
| Resolver IDs de Ubicación (en caso de modificación manual)
|--------------------------------------------------------------------------
*/

async function resolverUbicacionIds() {
    if (!ubicacion.value.pais) {
        return
    }

    try {
        const paisResponse = await fetch(
            route('ubicaciones.paises'),
            { headers: { Accept: 'application/json' } }
        )

        if (!paisResponse.ok) throw new Error('No fue posible consultar los países.')

        const paises = await paisResponse.json()
        const paisesData = Array.isArray(paises) ? paises : (paises.data || [])

        const paisEncontrado = paisesData.find(pais => {
            const nombre = (pais.nombre ?? pais.name ?? '').toLowerCase().trim()
            return nombre === ubicacion.value.pais.toLowerCase().trim()
        })

        if (!paisEncontrado) return
        form.pais_id = paisEncontrado.id

        if (!ubicacion.value.departamento) return

        const divisionesResponse = await fetch(
            route('ubicaciones.divisiones', paisEncontrado.id),
            { headers: { Accept: 'application/json' } }
        )

        if (!divisionesResponse.ok) throw new Error('No fue posible consultar los departamentos.')

        const divisiones = await divisionesResponse.json()
        const divisionesData = Array.isArray(divisiones) ? divisiones : (divisiones.data || [])

        const divisionEncontrada = divisionesData.find(division => {
            const nombre = (division.nombre ?? division.name ?? '').toLowerCase().trim()
            return nombre === ubicacion.value.departamento.toLowerCase().trim()
        })

        if (!divisionEncontrada) return
        form.division_administrativa_id = divisionEncontrada.id

        if (!ubicacion.value.ciudad) return

        const ciudadesResponse = await fetch(
            route('ubicaciones.ciudades', divisionEncontrada.id),
            { headers: { Accept: 'application/json' } }
        )

        if (!ciudadesResponse.ok) throw new Error('No fue posible consultar las ciudades.')

        const ciudades = await ciudadesResponse.json()
        const ciudadesData = Array.isArray(ciudades) ? ciudades : (ciudades.data || [])

        const ciudadEncontrada = ciudadesData.find(ciudad => {
            const nombre = (ciudad.nombre ?? ciudad.name ?? '').toLowerCase().trim()
            return nombre === ubicacion.value.ciudad.toLowerCase().trim()
        })

        if (!ciudadEncontrada) return
        form.ciudad_id = ciudadEncontrada.id

    } catch (error) {
        console.error('Error resolviendo ubicación:', error)
    }
}

/*
|--------------------------------------------------------------------------
| Imágenes
|--------------------------------------------------------------------------
*/

function seleccionarImagenes(event) {
    const archivos = Array.from(event.target.files || [])

    if (!archivos.length) {
        return
    }

    const disponibles = 8 - form.imagenes.length

    if (archivos.length > disponibles) {
        form.setError(
            'imagenes',
            `Solo puedes agregar ${disponibles} imagen(es) más. El máximo es de 8.`
        )
        event.target.value = ''
        return
    }

    const archivosValidos = []

    for (const archivo of archivos) {
        if (!archivo.type.startsWith('image/')) {
            form.setError(
                'imagenes',
                'Todos los archivos deben ser imágenes.'
            )
            event.target.value = ''
            return
        }

        if (archivo.size > 5 * 1024 * 1024) {
            form.setError(
                'imagenes',
                'Cada imagen no puede superar los 5 MB.'
            )
            event.target.value = ''
            return
        }

        archivosValidos.push(archivo)
    }

    form.clearErrors('imagenes')
    form.imagenes.push(...archivosValidos)

    for (const archivo of archivosValidos) {
        vistasPrevia.value.push({
            archivo,
            url: URL.createObjectURL(archivo),
        })
    }

    event.target.value = ''
}

function eliminarImagen(index) {
    const preview = vistasPrevia.value[index]

    if (preview?.url) {
        URL.revokeObjectURL(preview.url)
    }

    vistasPrevia.value.splice(index, 1)
    form.imagenes.splice(index, 1)
    form.clearErrors('imagenes')
}

/*
|--------------------------------------------------------------------------
| Video
|--------------------------------------------------------------------------
*/

function seleccionarVideo(event) {
    const archivo = event.target.files?.[0]

    if (!archivo) {
        return
    }

    const tiposPermitidos = [
        'video/mp4',
        'video/webm',
        'video/quicktime',
    ]

    if (!tiposPermitidos.includes(archivo.type)) {
        form.setError(
            'video',
            'El video debe estar en formato MP4, WebM o MOV.'
        )
        event.target.value = ''
        return
    }

    if (archivo.size > 50 * 1024 * 1024) {
        form.setError(
            'video',
            'El video no puede superar los 50 MB.'
        )
        event.target.value = ''
        return
    }

    form.clearErrors('video')
    form.video = archivo

    if (vistaPreviaVideo.value) {
        URL.revokeObjectURL(vistaPreviaVideo.value)
    }

    vistaPreviaVideo.value = URL.createObjectURL(archivo)
    event.target.value = ''
}

function eliminarVideo() {
    form.video = null

    if (vistaPreviaVideo.value) {
        URL.revokeObjectURL(vistaPreviaVideo.value)
        vistaPreviaVideo.value = null
    }

    if (inputVideo.value) {
        inputVideo.value.value = ''
    }

    form.clearErrors('video')
}

/*
|--------------------------------------------------------------------------
| Publicar
|--------------------------------------------------------------------------
*/

async function publicar() {
    // Si no existen IDs, se intenta resolverlos
    if (!form.ciudad_id || !form.division_administrativa_id || !form.pais_id) {
        await resolverUbicacionIds()
    }

    // Validación estricta para evitar guardar publicaciones sin ciudad
    if (!form.ciudad_id) {
        alert('Debes seleccionar un País, Departamento y Ciudad válidos antes de publicar.')
        return
    }

    form.post(
        route('marketplace.publicar.store'),
        {
            forceFormData: true,
            preserveScroll: true,
            onError: errors => {
                console.error('Errores al publicar:', errors)
            },
        }
    )
}
</script>

<template>

    <Head title="Publicar en Marketplace | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">



            <!-- ENCABEZADO TRANSLÚCIDO CON IDENTIDAD AZUL -->
            <section class="border-b border-blue-200/80 bg-blue-50 backdrop-blur-md shadow-lg shadow-blue-500/10">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <Link :href="route('marketplace.index')"
                        class="mb-4 inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-blue-600">
                        ← Volver al Marketplace
                    </Link>

                    <div class="mt-2">
                        <div class="mb-1 flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                            <span class="text-[10px] font-black uppercase tracking-[0.22em] text-blue-950">
                                Comercio Comunitario
                            </span>
                        </div>

                        <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                            Publicar en Marketplace
                        </h1>

                        <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                            Publica un producto para que otros miembros de la comunidad puedan conocerlo y ponerse en
                            contacto contigo.
                        </p>
                    </div>
                </div>
            </section>
           <!-- Muestra una alerta general si existen errores en el formulario -->
            <div v-if="Object.keys(form.errors).length > 0"
                class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <p class="font-bold">Por favor corrige los siguientes errores:</p>
                <ul class="list-disc list-inside mt-2 text-sm">
                    <template v-for="(error, key) in form.errors" :key="key">
                        <li>{{ error }}</li>
                    </template>
                </ul>
            </div>
            <!-- CONTENIDO PRINCIPAL -->
            <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 bg-blue-50 rounded-xl p-3 mt-2">
                <form @submit.prevent="publicar" class="space-y-6">

                    <!-- ===================================================== -->
                    <!-- 1. INFORMACIÓN DEL PRODUCTO -->
                    <!-- ===================================================== -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15">
                        <div class="border-b border-slate-100 bg-blue-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                1. Información del producto
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                Describe claramente lo que deseas publicar.
                            </p>
                        </div>

                        <div class="space-y-5 px-5 py-6 sm:px-6">
                            <!-- CATEGORÍA -->
                            <div>
                                <label for="categoria_id"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Categoría <span class="text-red-500">*</span>
                                </label>
                                <select id="categoria_id" v-model="form.categoria_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                                    :class="{ 'border-red-500': form.errors.categoria_id }">
                                    <option value="">Selecciona una categoría</option>
                                    <option v-for="categoria in props.categorias" :key="categoria.id"
                                        :value="categoria.id">
                                        {{ categoria.nombre }}
                                    </option>
                                </select>
                                <p v-if="form.errors.categoria_id" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.categoria_id }}
                                </p>
                            </div>

                            <!-- SUBCATEGORÍA -->
                            <div v-if="subcategoriasFiltradas.length">
                                <label for="subcategoria_id"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Subcategoría
                                </label>
                                <select id="subcategoria_id" v-model="form.subcategoria_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                                    :class="{ 'border-red-500': form.errors.subcategoria_id }">
                                    <option value="">Selecciona una subcategoría</option>
                                    <option v-for="subcategoria in subcategoriasFiltradas" :key="subcategoria.id"
                                        :value="subcategoria.id">
                                        {{ subcategoria.nombre }}
                                    </option>
                                </select>
                                <p v-if="form.errors.subcategoria_id" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.subcategoria_id }}
                                </p>
                            </div>

                            <!-- TÍTULO -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="titulo"
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Título <span class="text-red-500">*</span>
                                    </label>
                                    <button type="button" @click="aplicarCorreccionMagica('titulo')"
                                        :disabled="cargandoIA.titulo || !form.titulo.trim()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition disabled:opacity-50">
                                        <span v-if="cargandoIA.titulo" class="animate-spin">⏳</span>
                                        <span v-else>✨</span>
                                        <span>Corrección Mágica</span>
                                    </button>
                                </div>
                                <input id="titulo" v-model="form.titulo" type="text" maxlength="255"
                                    placeholder="Ej. Computador portátil Lenovo"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                                    :class="{ 'border-red-500': form.errors.titulo || alertaTitulo }" />

                                <p v-if="alertaTitulo" class="mt-1.5 text-xs text-amber-600 font-bold">
                                    {{ alertaTitulo }}
                                </p>
                                <p v-if="form.errors.titulo" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.titulo }}
                                </p>
                            </div>

                            <!-- DESCRIPCIÓN -->
                            <div>
                                <div class="mb-1.5 flex items-center justify-between gap-3">
                                    <label for="descripcion"
                                        class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Descripción <span class="text-red-500">*</span>
                                    </label>
                                    <button type="button" @click="aplicarCorreccionMagica('descripcion')"
                                        :disabled="cargandoIA.descripcion || !form.descripcion.trim()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition disabled:opacity-50">
                                        <span v-if="cargandoIA.descripcion" class="animate-spin">⏳</span>
                                        <span v-else>✨</span>
                                        <span>Corrección Mágica</span>
                                    </button>
                                </div>
                                <textarea id="descripcion" v-model="form.descripcion" rows="6"
                                    placeholder="Describe el producto, sus características, estado y cualquier información importante."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm leading-relaxed text-[#172A3D] outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                                    :class="{ 'border-red-500': form.errors.descripcion || alertaDescripcion }"></textarea>

                                <p v-if="alertaDescripcion" class="mt-1.5 text-xs text-amber-600 font-bold">
                                    {{ alertaDescripcion }}
                                </p>
                                <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.descripcion }}
                                </p>
                            </div>

                            <!-- ESTADO -->
                            <div>
                                <label class="mb-3 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Estado del producto <span class="text-red-500">*</span>
                                </label>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label v-for="estado in estadosProducto" :key="estado.value"
                                        class="cursor-pointer rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.estado_producto === estado.value
                                            ? 'border-blue-500 bg-blue-50/60 ring-2 ring-blue-200 shadow-md'
                                            : 'border-slate-300 hover:border-blue-300'">
                                        <div class="flex items-start gap-3">
                                            <input v-model="form.estado_producto" type="radio" :value="estado.value"
                                                class="mt-1 accent-blue-600" />
                                            <div>
                                                <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">
                                                    {{ estado.label }}
                                                </span>
                                                <span
                                                    class="mt-0.5 block text-xs leading-relaxed text-slate-500 font-medium">
                                                    {{ estado.descripcion }}
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <p v-if="form.errors.estado_producto" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.estado_producto }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- ===================================================== -->
                    <!-- 2. PRECIO -->
                    <!-- ===================================================== -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15">
                        <div class="border-b border-slate-100 bg-blue-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                2. Precio
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                Define si el producto es gratuito o establece un precio.
                            </p>
                        </div>

                        <div class="space-y-5 px-5 py-6 sm:px-6">
                            <label class="flex cursor-pointer items-center gap-3">
                                <input v-model="form.es_gratis" type="checkbox"
                                    class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                                <span class="text-xs sm:text-sm font-bold text-[#172A3D]">
                                    Este producto es gratuito
                                </span>
                            </label>

                            <div v-if="!form.es_gratis">
                                <label for="precio"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Precio <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">
                                        $
                                    </span>
                                    <input id="precio" v-model="form.precio" type="number" min="0" step="0.01"
                                        placeholder="0"
                                        class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-9 pr-4 text-xs sm:text-sm font-semibold text-[#172A3D] outline-none transition focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                                        :class="{ 'border-red-500': form.errors.precio }" />
                                </div>
                                <p v-if="form.errors.precio" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.precio }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- ===================================================== -->
                    <!-- 3. UBICACIÓN -->
                    <!-- ===================================================== -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15">
                        <div class="border-b border-slate-100 bg-blue-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                3. Ubicación del producto
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                Indica dónde se encuentra el producto físicamente.
                            </p>
                        </div>

                        <div class="space-y-5 px-5 py-6 sm:px-6">
                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <UbicacionSelector v-model:pais="ubicacion.pais"
                                    v-model:departamento="ubicacion.departamento" v-model:ciudad="ubicacion.ciudad" />
                            </div>

                            <p class="text-xs leading-relaxed text-slate-500 font-medium">
                                La ubicación seleccionada se utilizará como punto de partida para determinar la
                                cobertura y calcular la disponibilidad hacia los compradores.
                            </p>
                        </div>
                    </section>

                    <!-- ===================================================== -->
                    <!-- 4. FORMA DE ENTREGA Y ALCANCE DE DESPACHO -->
                    <!-- ===================================================== -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15">
                        <div class="border-b border-slate-100 bg-blue-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                4. Forma de entrega y alcance de despacho
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                Define la modalidad de entrega y hasta dónde tienes disponibilidad de despachar.
                            </p>
                        </div>

                        <div class="space-y-6 px-5 py-6 sm:px-6">
                            <!-- MODALIDAD DE ENTREGA -->
                            <div>
                                <label class="mb-2 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Modalidad de entrega <span class="text-red-500">*</span>
                                </label>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <label v-for="tipo in tiposEntrega" :key="tipo.value"
                                        class="cursor-pointer rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.tipo_entrega === tipo.value
                                            ? 'border-blue-500 bg-blue-50/60 ring-2 ring-blue-200 shadow-md'
                                            : 'border-slate-300 hover:border-blue-300'">
                                        <div class="flex items-center gap-3">
                                            <input v-model="form.tipo_entrega" type="radio" :value="tipo.value"
                                                class="accent-blue-600" />
                                            <span class="text-xs sm:text-sm font-bold text-[#172A3D]">
                                                {{ tipo.label }}
                                            </span>
                                        </div>
                                    </label>
                                </div>
                                <p v-if="form.errors.tipo_entrega" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.tipo_entrega }}
                                </p>
                            </div>

                            <!-- RANGO DE COBERTURA / EXHIBICIÓN -->
                            <div>
                                <label class="mb-2 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Disponibilidad de despacho / Rango de exhibición <span class="text-red-500">*</span>
                                </label>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label v-for="rango in rangosCobertura" :key="rango.value"
                                        class="cursor-pointer rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.rango_cobertura === rango.value
                                            ? 'border-blue-500 bg-blue-50/60 ring-2 ring-blue-200 shadow-md'
                                            : 'border-slate-300 hover:border-blue-300'">
                                        <div class="flex items-start gap-3">
                                            <input v-model="form.rango_cobertura" type="radio" :value="rango.value"
                                                class="mt-1 accent-blue-600" />
                                            <div>
                                                <span
                                                    class="flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#172A3D]">
                                                    <span>{{ rango.icono }}</span>
                                                    <span>{{ rango.label }}</span>
                                                </span>
                                                <span
                                                    class="mt-1 block text-xs leading-relaxed text-slate-500 font-medium">
                                                    {{ rango.descripcion }}
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <p v-if="form.errors.rango_cobertura" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.rango_cobertura }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- ===================================================== -->
                    <!-- 5. IMÁGENES -->
                    <!-- ===================================================== -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15">
                        <div class="border-b border-slate-100 bg-blue-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                5. Imágenes
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                Puedes agregar hasta 8 imágenes. Máximo 5 MB por imagen.
                            </p>
                        </div>

                        <div class="space-y-5 px-5 py-6 sm:px-6">
                            <input ref="inputImagenes" type="file" accept="image/jpeg,image/png,image/webp" multiple
                                class="hidden" @change="seleccionarImagenes" />

                            <button type="button"
                                class="rounded-xl border border-dashed border-slate-300 px-5 py-4 text-xs sm:text-sm font-bold text-slate-700 transition hover:border-blue-500 hover:bg-blue-50 shadow-xs"
                                :disabled="form.imagenes.length >= 8" @click="inputImagenes?.click()">
                                + Agregar imágenes
                            </button>

                            <div v-if="vistasPrevia.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                <div v-for="(imagen, index) in vistasPrevia" :key="imagen.url"
                                    class="group relative overflow-hidden rounded-xl border border-slate-200 bg-slate-50 shadow-xs">
                                    <img :src="imagen.url" alt="Vista previa" class="h-40 w-full object-cover" />
                                    <button type="button"
                                        class="absolute right-2 top-2 rounded-full bg-white px-2.5 py-1.5 text-xs font-bold text-red-600 shadow-sm transition hover:bg-red-50"
                                        @click="eliminarImagen(index)">
                                        Eliminar
                                    </button>
                                </div>
                            </div>

                            <p v-if="form.errors.imagenes" class="text-xs text-red-600 font-medium">
                                {{ form.errors.imagenes }}
                            </p>
                        </div>
                    </section>

                    <!-- ===================================================== -->
                    <!-- 6. VIDEO -->
                    <!-- ===================================================== -->
                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-blue-500/15">
                        <div class="border-b border-slate-100 bg-blue-50/40 px-5 py-4 sm:px-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                                6. Video
                            </h2>
                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                Opcional. MP4, WebM o MOV. Máximo 50 MB.
                            </p>
                        </div>

                        <div class="space-y-5 px-5 py-6 sm:px-6">
                            <input ref="inputVideo" type="file" accept="video/mp4,video/webm,video/quicktime"
                                class="hidden" @change="seleccionarVideo" />

                            <button v-if="!form.video" type="button"
                                class="rounded-xl border border-dashed border-slate-300 px-5 py-4 text-xs sm:text-sm font-bold text-slate-700 transition hover:border-blue-500 hover:bg-blue-50 shadow-xs"
                                @click="inputVideo?.click()">
                                + Agregar video
                            </button>

                            <div v-if="vistaPreviaVideo"
                                class="relative overflow-hidden rounded-xl border border-slate-200 bg-black shadow-sm">
                                <video :src="vistaPreviaVideo" controls class="max-h-[450px] w-full"></video>
                                <button type="button"
                                    class="absolute right-3 top-3 rounded-lg bg-white px-3 py-2 text-xs font-bold text-red-600 shadow-sm"
                                    @click="eliminarVideo">
                                    Eliminar video
                                </button>
                            </div>

                            <p v-if="form.errors.video" class="text-xs text-red-600 font-medium">
                                {{ form.errors.video }}
                            </p>
                        </div>
                    </section>

                    <!-- ===================================================== -->
                    <!-- AVISO -->
                    <!-- ===================================================== -->
                    <div class="rounded-2xl border border-blue-200 bg-blue-50/70 p-5 shadow-xs backdrop-blur-md">
                        <h3 class="font-bold text-blue-950">
                            Importante
                        </h3>
                        <p class="mt-1 text-xs sm:text-sm leading-relaxed text-blue-900 font-medium">
                            Portal Deseret no procesa ni intermedia pagos, entregas o transacciones. El acuerdo
                            comercial se realiza directamente entre las partes.
                        </p>
                    </div>

                    <!-- ===================================================== -->
                    <!-- BOTONES -->
                    <!-- ===================================================== -->
                    <div class="flex flex-col-reverse gap-3 pb-12 sm:flex-row sm:justify-end">
                        <Link :href="route('marketplace.index')"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 shadow-xs">
                            Cancelar
                        </Link>

                        <button type="submit"
                            :disabled="form.processing || Boolean(alertaTitulo) || Boolean(alertaDescripcion)"
                            class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-7 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-blue-500/25 transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
                            {{ form.processing ? 'Publicando...' : 'Publicar producto' }}
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