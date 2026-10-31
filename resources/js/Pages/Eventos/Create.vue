<script setup>
import { computed, ref, watch, onMounted } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import axios from 'axios'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UbicacionSelector from '@/Components/UbicacionSelector.vue'

const props = defineProps({
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
| Estado inicial
|--------------------------------------------------------------------------
*/

const modalidad = ref('presencial')
const esGratuito = ref(false)

const previews = ref([])

/*
|--------------------------------------------------------------------------
| Ubicación y Rango de Cobertura
|--------------------------------------------------------------------------
*/

const ubicacion = ref({
    pais: props.ubicacionDefecto?.pais || 'Colombia',
    departamento: props.ubicacionDefecto?.departamento || '',
    ciudad: props.ubicacionDefecto?.ciudad || '',
})

const rangosCobertura = [
    {
        value: 'local',
        label: 'Solo en mi ciudad / municipio',
        descripcion: 'Visible únicamente para usuarios de tu misma localidad.',
        icono: '📍',
    },
    {
        value: 'departamental',
        label: 'A nivel regional / departamental',
        descripcion: 'Disponible para usuarios de ciudades vecinas en tu departamento.',
        icono: '🗺️',
    },
    {
        value: 'nacional',
        label: 'Todo el país',
        descripcion: 'Visible en todo el territorio nacional sin restricción geográfica.',
        icono: '🇨🇴',
    },
]

/*
|--------------------------------------------------------------------------
| Formulario
|--------------------------------------------------------------------------
*/

const form = useForm({
    titulo: '',
    descripcion: '',
    modalidad: 'presencial',
    ubicacion: '',
    enlace_virtual: '',
    fecha_inicio: '',
    fecha_fin: '',
    es_gratuito: false,
    precio: '',
    precio_preventa: '',
    fecha_limite_preventa: '',
    precios_localidades: [],
    capacidad: '',

    pais_id: props.ubicacionDefecto?.pais_id || '',
    division_administrativa_id: props.ubicacionDefecto?.division_administrativa_id || '',
    ciudad_id: props.ubicacionDefecto?.ciudad_id || '',
    rango_cobertura: 'local',

    imagen: null,
})

const addLocalidad = () => {
    form.precios_localidades.push({ zona: '', precio: '' })
}

const removeLocalidad = (index) => {
    form.precios_localidades.splice(index, 1)
}

/*
|--------------------------------------------------------------------------
| Helpers de Normalización y Resolución de IDs de Ubicación
|--------------------------------------------------------------------------
*/

const normalizar = (valor) => {
    return String(valor ?? '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .replace(/[.,;:]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim()
}

const ubicacionesCoinciden = (valorA, valorB) => {
    const a = normalizar(valorA)
    const b = normalizar(valorB)
    if (!a || !b) return false
    return a === b || a.includes(b) || b.includes(a)
}

const obtenerDatosRespuesta = (respuesta) => {
    if (Array.isArray(respuesta)) return respuesta
    if (Array.isArray(respuesta?.data)) return respuesta.data
    if (Array.isArray(respuesta?.results)) return respuesta.results
    if (Array.isArray(respuesta?.divisiones)) return respuesta.divisiones
    if (Array.isArray(respuesta?.ciudades)) return respuesta.ciudades
    if (Array.isArray(respuesta?.paises)) return respuesta.paises
    return []
}

async function resolverUbicacionIds() {
    if (!ubicacion.value.pais) return

    try {
        const paisResponse = await fetch(route('ubicaciones.paises'), {
            headers: { Accept: 'application/json' },
        })
        if (!paisResponse.ok) return
        const paises = await paisResponse.json()
        const paisesData = obtenerDatosRespuesta(paises)

        const paisEncontrado = paisesData.find(p => {
            const nom = p.nombre ?? p.name ?? p.label ?? ''
            return ubicacionesCoinciden(nom, ubicacion.value.pais)
        })

        if (!paisEncontrado) return
        form.pais_id = paisEncontrado.id

        if (!ubicacion.value.departamento) return

        const divisionesResponse = await fetch(route('ubicaciones.divisiones', paisEncontrado.id), {
            headers: { Accept: 'application/json' },
        })
        if (!divisionesResponse.ok) return
        const divisiones = await divisionesResponse.json()
        const divisionesData = obtenerDatosRespuesta(divisiones)

        const divisionEncontrada = divisionesData.find(d => {
            const nom = d.nombre ?? d.name ?? d.label ?? d.descripcion ?? ''
            return ubicacionesCoinciden(nom, ubicacion.value.departamento)
        })

        if (!divisionEncontrada) return
        form.division_administrativa_id = divisionEncontrada.id

        if (!ubicacion.value.ciudad) return

        const ciudadesResponse = await fetch(route('ubicaciones.ciudades', divisionEncontrada.id), {
            headers: { Accept: 'application/json' },
        })
        if (!ciudadesResponse.ok) return
        const ciudades = await ciudadesResponse.json()
        const ciudadesData = obtenerDatosRespuesta(ciudades)

        const ciudadEncontrada = ciudadesData.find(c => {
            const nom = c.nombre ?? c.name ?? c.label ?? c.descripcion ?? ''
            return ubicacionesCoinciden(nom, ubicacion.value.ciudad)
        })

        if (ciudadEncontrada) {
            form.ciudad_id = ciudadEncontrada.id
        }
    } catch (error) {
        console.error('Error sincronizando IDs de ubicación:', error)
    }
}

watch(() => ubicacion.value.departamento, async (nuevo, viejo) => {
    if (nuevo !== viejo) {
        form.division_administrativa_id = ''
        form.ciudad_id = ''
        await resolverUbicacionIds()
    }
})

watch(() => ubicacion.value.ciudad, async (nuevo, viejo) => {
    if (nuevo !== viejo) {
        form.ciudad_id = ''
        await resolverUbicacionIds()
    }
})

onMounted(async () => {
    await resolverUbicacionIds()
})

/*
|--------------------------------------------------------------------------
| CORRECCIÓN MÁGICA CON IA
|--------------------------------------------------------------------------
*/
const cargandoIA = ref({ titulo: false, descripcion: false })

async function aplicarCorreccionMagica(campo) {
    const textoActual = form[campo]
    if (!textoActual || !textoActual.trim()) return

    cargandoIA.value[campo] = true

    try {
        const response = await axios.post('/ai/correct-text', {
            text: textoActual,
            context_type: `evento_${campo}`,
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

function verificarPalabrasProhibidas(texto, tipoCampo) {
    if (!texto || !props.palabrasProhibidas || props.palabrasProhibidas.length === 0) {
        if (tipoCampo === 'titulo') alertaTitulo.value = ''
        if (tipoCampo === 'descripcion') alertaDescripcion.value = ''
        return
    }

    const textoLower = texto.toLowerCase()
    const encontrada = props.palabrasProhibidas.find(palabra => {
        const palabraClean = palabra.trim().toLowerCase()
        return palabraClean.length > 0 && textoLower.includes(palabraClean)
    })

    if (encontrada) {
        const mensaje = `⚠️ Se ha detectado un término no permitido ("${encontrada}"). Por favor, modifícalo antes de publicar.`
        if (tipoCampo === 'titulo') alertaTitulo.value = mensaje
        if (tipoCampo === 'descripcion') alertaDescripcion.value = mensaje
    } else {
        if (tipoCampo === 'titulo') alertaTitulo.value = ''
        if (tipoCampo === 'descripcion') alertaDescripcion.value = ''
    }
}

watch(() => form.titulo, (nuevoValor) => verificarPalabrasProhibidas(nuevoValor, 'titulo'))
watch(() => form.descripcion, (nuevoValor) => verificarPalabrasProhibidas(nuevoValor, 'descripcion'))

/*
|--------------------------------------------------------------------------
| Sincronización de Modalidad y Gratuidad
|--------------------------------------------------------------------------
*/
const seleccionarModalidad = (nuevaModalidad) => {
    modalidad.value = nuevaModalidad
    form.modalidad = nuevaModalidad
}

const toggleGratuito = (val) => {
    esGratuito.value = val
    form.es_gratuito = val
    if (val) {
        form.precio = ''
        form.precio_preventa = ''
        form.fecha_limite_preventa = ''
        form.precios_localidades = []
    }
}

/*
|--------------------------------------------------------------------------
| Manejo de Imagen Principal
|--------------------------------------------------------------------------
*/
const manejarImagen = event => {
    const archivo = event.target.files?.[0]
    if (!archivo || !archivo.type.startsWith('image/')) return

    form.imagen = archivo
    previews.value = [{ archivo, url: URL.createObjectURL(archivo) }]
    event.target.value = ''
}

const eliminarImagen = () => {
    if (previews.value[0]?.url) URL.revokeObjectURL(previews.value[0].url)
    form.imagen = null
    previews.value = []
}

/*
|--------------------------------------------------------------------------
| Validación y Publicación
|--------------------------------------------------------------------------
*/
const puedePublicar = computed(() => {
    return Boolean(
        form.titulo?.trim() &&
        !alertaTitulo.value &&
        form.descripcion?.trim() &&
        !alertaDescripcion.value &&
        form.fecha_inicio &&
        form.modalidad
    )
})

async function publicar() {
    if (!form.ciudad_id || !form.division_administrativa_id || !form.pais_id) {
        await resolverUbicacionIds()
    }

    form.post(route('eventos.store'), {
        forceFormData: true,
        preserveScroll: true,
    })
}
</script>

<template>

    <Head title="Publicar evento | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ENCABEZADO -->
            <section
                class="border-b border-cyan-200/80 bg-cyan-100/25 backdrop-blur-md shadow-lg shadow-cyan-500/10">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <Link :href="route('eventos.index')"
                        class="mb-4 inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-cyan-600">
                        ← Volver a eventos
                    </Link>

                    <div class="mt-2">
                        <div class="mb-1 flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-cyan-600"></span>
                            <span class="text-[10px] font-black uppercase tracking-[0.22em] text-cyan-950">
                                Talleres y Encuentros
                            </span>
                        </div>
                        <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                            Publicar nuevo evento
                        </h1>
                        <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                            Organiza un taller, conferencia o reunión para la comunidad.
                        </p>
                    </div>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 space-y-6 bg-cyan-50 rounded-xl p-3 mt-2">

                <!-- PASO 1 - MODALIDAD -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-cyan-500/15">
                    <div class="border-b border-slate-100 bg-cyan-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-cyan-900">Paso 1</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Modalidad del evento</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-3 sm:p-6">
                        <button type="button" @click="seleccionarModalidad('presencial')"
                            class="rounded-2xl border-2 p-5 text-left transition bg-white shadow-sm"
                            :class="modalidad === 'presencial' ? 'border-cyan-500 bg-cyan-50/60 ring-2 ring-cyan-200 shadow-md' : 'border-slate-300 hover:border-cyan-300'">
                            <div
                                class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 border border-cyan-200 text-2xl">
                                📍</div>
                            <h3 class="font-bold text-[#172A3D]">Presencial</h3>
                            <p class="mt-1 text-xs text-slate-500 font-medium">Reunión física en ubicación específica.
                            </p>
                        </button>

                        <button type="button" @click="seleccionarModalidad('virtual')"
                            class="rounded-2xl border-2 p-5 text-left transition bg-white shadow-sm"
                            :class="modalidad === 'virtual' ? 'border-cyan-500 bg-cyan-50/60 ring-2 ring-cyan-200 shadow-md' : 'border-slate-300 hover:border-cyan-300'">
                            <div
                                class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 border border-cyan-200 text-2xl">
                                💻</div>
                            <h3 class="font-bold text-[#172A3D]">Virtual</h3>
                            <p class="mt-1 text-xs text-slate-500 font-medium">Transmisión online o enlace web.</p>
                        </button>

                        <button type="button" @click="seleccionarModalidad('hibrido')"
                            class="rounded-2xl border-2 p-5 text-left transition bg-white shadow-sm"
                            :class="modalidad === 'hibrido' ? 'border-cyan-500 bg-cyan-50/60 ring-2 ring-cyan-200 shadow-md' : 'border-slate-300 hover:border-cyan-300'">
                            <div
                                class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-50 border border-cyan-200 text-2xl">
                                🔄</div>
                            <h3 class="font-bold text-[#172A3D]">Híbrido</h3>
                            <p class="mt-1 text-xs text-slate-500 font-medium">Presencial con transmisión online.</p>
                        </button>
                    </div>
                </section>

                <!-- PASO 2 - DETALLES Y ACCESO -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-cyan-500/15">
                    <div class="border-b border-slate-100 bg-cyan-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-cyan-900">Paso 2</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Ubicación o enlace de acceso</h2>
                    </div>

                    <div class="p-5 sm:p-6 space-y-4">
                        <div v-if="modalidad === 'presencial' || modalidad === 'hibrido'">
                            <label class="form-label">Dirección o sitio físico <span
                                    class="text-red-500">*</span></label>
                            <input v-model="form.ubicacion" type="text" class="form-input"
                                placeholder="Ej. Auditorio Central, Calle 45 # 12-30" />
                            <p v-if="form.errors.ubicacion" class="form-error">{{ form.errors.ubicacion }}</p>
                        </div>

                        <div v-if="modalidad === 'virtual' || modalidad === 'hibrido'">
                            <label class="form-label">Enlace de transmisión / Zoom <span
                                    class="text-red-500">*</span></label>
                            <input v-model="form.enlace_virtual" type="url" class="form-input"
                                placeholder="https://zoom.us/..." />
                            <p v-if="form.errors.enlace_virtual" class="form-error">{{ form.errors.enlace_virtual }}</p>
                        </div>
                    </div>
                </section>

                <!-- PASO 3 - FECHAS Y HORARIOS -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-cyan-500/15">
                    <div class="border-b border-slate-100 bg-cyan-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-cyan-900">Paso 3</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Fecha y hora</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">
                        <div>
                            <label class="form-label">Fecha y hora de inicio <span class="text-red-500">*</span></label>
                            <input v-model="form.fecha_inicio" type="datetime-local" class="form-input" />
                            <p v-if="form.errors.fecha_inicio" class="form-error">{{ form.errors.fecha_inicio }}</p>
                        </div>

                        <div>
                            <label class="form-label">Fecha y hora de finalización</label>
                            <input v-model="form.fecha_fin" type="datetime-local" class="form-input" />
                            <p v-if="form.errors.fecha_fin" class="form-error">{{ form.errors.fecha_fin }}</p>
                        </div>
                    </div>
                </section>

                <!-- PASO 4 - COSTOS Y ENTRADAS -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-cyan-500/15">
                    <div class="border-b border-slate-100 bg-cyan-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-cyan-900">Paso 4</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Costos y entradas</h2>
                    </div>

                    <div class="p-5 sm:p-6 space-y-6">
                        <label class="form-check">
                            <input type="checkbox" :checked="esGratuito"
                                @change="toggleGratuito($event.target.checked)" />
                            <span>Este evento es completamente gratuito</span>
                        </label>

                        <div v-if="!esGratuito" class="space-y-5 border-t border-slate-100 pt-5">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div>
                                    <label class="form-label">Precio General</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">$</span>
                                        <input v-model="form.precio" type="number" min="0" step="0.01"
                                            class="form-input pl-9 font-semibold" placeholder="0" />
                                    </div>
                                    <p v-if="form.errors.precio" class="form-error">{{ form.errors.precio }}</p>
                                </div>

                                <div>
                                    <label class="form-label">Precio Preventa (Opcional)</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">$</span>
                                        <input v-model="form.precio_preventa" type="number" min="0" step="0.01"
                                            class="form-input pl-9 font-semibold" placeholder="0" />
                                    </div>
                                </div>

                                <div>
                                    <label class="form-label">Límite de Preventa</label>
                                    <input v-model="form.fecha_limite_preventa" type="date" class="form-input" />
                                </div>
                            </div>

                            <!-- Zonas o Localidades -->
                            <div class="border-t border-slate-100 pt-5">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="form-label mb-0">Localidades o Zonas (Opcional)</label>
                                    <button type="button" @click="addLocalidad"
                                        class="text-xs bg-cyan-50 hover:bg-cyan-100 text-cyan-700 font-bold px-3 py-1.5 rounded-lg border border-cyan-200 transition">
                                        + Agregar zona
                                    </button>
                                </div>

                                <div v-for="(loc, index) in form.precios_localidades" :key="index"
                                    class="flex gap-3 items-center mb-2.5">
                                    <input v-model="loc.zona" type="text" placeholder="Ej. VIP, General, Palco"
                                        class="form-input flex-1" />
                                    <input v-model="loc.precio" type="number" min="0" step="0.01" placeholder="Precio"
                                        class="form-input w-36 font-semibold" />
                                    <button type="button" @click="removeLocalidad(index)"
                                        class="h-11 w-11 shrink-0 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 font-bold flex items-center justify-center border border-red-200">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="max-w-xs border-t border-slate-100 pt-5">
                            <label class="form-label">Capacidad Máxima de Asistentes</label>
                            <input v-model="form.capacidad" type="number" min="1" class="form-input"
                                placeholder="Ej. 100" />
                        </div>
                    </div>
                </section>

                <!-- PASO 5 - INFORMACIÓN GENERAL (TÍTULO Y DESCRIPCIÓN CON IA) -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-cyan-500/15">
                    <div class="border-b border-slate-100 bg-cyan-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-cyan-900">Paso 5</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Información del evento</h2>
                    </div>

                    <div class="p-5 sm:p-6 space-y-5">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="form-label mb-0">Título del evento <span
                                        class="text-red-500">*</span></label>
                                <button type="button" @click="aplicarCorreccionMagica('titulo')"
                                    :disabled="cargandoIA.titulo || !form.titulo.trim()"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-cyan-700 bg-cyan-50 hover:bg-cyan-100 rounded-lg border border-cyan-200 transition disabled:opacity-50">
                                    <span v-if="cargandoIA.titulo" class="animate-spin">⏳</span>
                                    <span v-else>✨</span>
                                    <span>Corrección Mágica</span>
                                </button>
                            </div>
                            <input v-model="form.titulo" type="text" maxlength="255"
                                placeholder="Ej. Taller de Emprendimiento Digital" class="form-input"
                                :class="{ 'border-red-500': form.errors.titulo || alertaTitulo }" />
                            <p v-if="alertaTitulo" class="mt-1.5 text-xs text-cyan-600 font-bold">{{ alertaTitulo }}
                            </p>
                            <p v-if="form.errors.titulo" class="form-error">{{ form.errors.titulo }}</p>
                        </div>

                        <div>
                            <div class="mb-1.5 flex items-center justify-between gap-3">
                                <label class="form-label mb-0">Descripción completa <span
                                        class="text-red-500">*</span></label>
                                <button type="button" @click="aplicarCorreccionMagica('descripcion')"
                                    :disabled="cargandoIA.descripcion || !form.descripcion.trim()"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-cyan-700 bg-cyan-50 hover:bg-cyan-100 rounded-lg border border-cyan-200 transition disabled:opacity-50">
                                    <span v-if="cargandoIA.descripcion" class="animate-spin">⏳</span>
                                    <span v-else>✨</span>
                                    <span>Corrección Mágica</span>
                                </button>
                            </div>
                            <textarea v-model="form.descripcion" rows="6"
                                placeholder="Describe los objetivos, ponentes y detalles de lo que aprenderán o vivirán en el evento..."
                                class="form-input"
                                :class="{ 'border-red-500': form.errors.descripcion || alertaDescripcion }"></textarea>
                            <p v-if="alertaDescripcion" class="mt-1.5 text-xs text-cyan-600 font-bold">{{
                                alertaDescripcion }}</p>
                            <p v-if="form.errors.descripcion" class="form-error">{{ form.errors.descripcion }}</p>
                        </div>
                    </div>
                </section>

                <!-- PASO 6 - UBICACIÓN Y COBERTURA -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-cyan-500/15">
                    <div class="border-b border-slate-100 bg-cyan-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-cyan-900">Paso 6</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Ciudad, Departamento y Cobertura</h2>
                        <p class="mt-0.5 text-xs text-slate-500 font-medium">Zona geográfica y alcance de visibilidad.
                        </p>
                    </div>

                    <div class="space-y-6 px-5 py-6 sm:px-6">
                        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                            <UbicacionSelector v-model:pais="ubicacion.pais"
                                v-model:departamento="ubicacion.departamento" v-model:ciudad="ubicacion.ciudad" />
                        </div>

                        <!-- Selector de Rango de Cobertura -->
                        <div v-if="modalidad === 'presencial' || modalidad === 'hibrido'" class="pt-2">
                            <label class="form-label mb-3">Rango de alcance del evento</label>
                            <div class="grid gap-3 sm:grid-cols-3">
                                <label v-for="rango in rangosCobertura" :key="rango.value"
                                    class="cursor-pointer rounded-xl border p-4 transition bg-white shadow-xs" :class="form.rango_cobertura === rango.value
                                        ? 'border-cyan-500 bg-cyan-50/60 ring-2 ring-cyan-200 shadow-md'
                                        : 'border-slate-300 hover:border-cyan-300'">
                                    <div class="flex items-start gap-3">
                                        <input v-model="form.rango_cobertura" type="radio" :value="rango.value"
                                            class="mt-1 accent-cyan-600" />
                                        <div>
                                            <span
                                                class="flex items-center gap-1.5 text-xs sm:text-sm font-bold text-[#172A3D]">
                                                <span>{{ rango.icono }}</span>
                                                <span>{{ rango.label }}</span>
                                            </span>
                                            <span class="mt-1 block text-xs leading-relaxed text-slate-500 font-medium">
                                                {{ rango.descripcion }}
                                            </span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <p v-if="form.errors.rango_cobertura" class="mt-1 text-xs text-red-600 font-medium">{{
                                form.errors.rango_cobertura }}</p>
                        </div>
                    </div>
                </section>

                <!-- PASO 7 - IMAGEN PROMOCIONAL -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-cyan-500/15">
                    <div class="border-b border-slate-100 bg-cyan-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-cyan-900">Paso 7</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Imagen promocional (Banner)</h2>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div v-if="!previews.length"
                            class="rounded-2xl border-2 border-dashed border-slate-300 p-8 text-center transition hover:border-cyan-400 hover:bg-cyan-50/30 bg-slate-50">
                            <input id="imagen" type="file" accept="image/*" class="hidden" @change="manejarImagen" />
                            <label for="imagen" class="cursor-pointer">
                                <div
                                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-cyan-100 border border-cyan-200 text-2xl">
                                    🖼️</div>
                                <p class="mt-4 font-bold text-[#172A3D]">Subir banner del evento</p>
                                <p class="mt-1 text-xs text-slate-500 font-medium">Formatos recomendados: JPG, PNG,
                                    WEBP.</p>
                            </label>
                        </div>

                        <div v-else
                            class="mt-4 flex items-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <img :src="previews[0].url" class="h-24 w-32 rounded-lg object-cover shadow-sm"
                                alt="Banner preview" />
                            <div>
                                <p class="text-xs font-bold text-[#172A3D]">Imagen seleccionada correctamente</p>
                                <button type="button" @click="eliminarImagen"
                                    class="mt-2 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-700 shadow-sm">
                                    Cambiar imagen
                                </button>
                            </div>
                        </div>
                        <p v-if="form.errors.imagen" class="form-error mt-3">{{ form.errors.imagen }}</p>
                    </div>
                </section>

                <!-- RESUMEN / PUBLICAR -->
                <section class="rounded-3xl border border-cyan-200 bg-cyan-50/80 p-6 shadow-xl backdrop-blur-md">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="font-bold text-cyan-950">¿Todo listo?</h2>
                            <p class="mt-1 text-xs sm:text-sm text-cyan-900 font-medium">Tu evento será publicado
                                inmediatamente para la comunidad.</p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <Link :href="route('eventos.index')"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm">
                                Cancelar
                            </Link>

                            <button type="button"
                                :disabled="form.processing || !puedePublicar || Boolean(alertaTitulo) || Boolean(alertaDescripcion)"
                                @click="publicar"
                                class="inline-flex items-center justify-center rounded-xl bg-cyan-600 px-6 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-cyan-500/25 transition hover:bg-cyan-700 disabled:cursor-not-allowed disabled:opacity-50">
                                <svg v-if="form.processing" class="mr-2 h-4 w-4 animate-spin"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                                </svg>
                                {{ form.processing ? 'Publicando...' : 'Publicar evento' }}
                            </button>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.form-label {
    @apply mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider;
}

.form-input {
    @apply w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] shadow-sm outline-none transition focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100;
}

.form-input::placeholder {
    @apply text-slate-400;
}

.form-check {
    @apply flex cursor-pointer items-center gap-2.5 text-xs sm:text-sm font-bold text-slate-700;
}

.form-check input {
    @apply h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500 accent-cyan-600;
}

.form-error {
    @apply mt-1 text-xs text-red-600 font-medium;
}

.fondo-carboncillo {
    background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.75)), url('/images/fondos/formularios-mobile.jpg');
}

@media (min-width: 768px) {
    .fondo-carboncillo {
        background-image: linear-gradient(rgba(255, 255, 255, 0.75), rgba(255, 255, 255, 0.75)), url('/images/fondos/formularios-desktop.jpg');
    }
}
</style>