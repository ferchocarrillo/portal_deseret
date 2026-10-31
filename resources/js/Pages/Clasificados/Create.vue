<script setup>
import { computed, ref, watch, onMounted } from 'vue'
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
| Estado inicial
|--------------------------------------------------------------------------
*/

const tipo = ref('')
const intencion = ref('')
const operacion = ref('')

const categoriaPrincipalId = ref('')
const categoriaId = ref('')

const imagenes = ref([])
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
        descripcion: 'Disponible para usuarios de ciudades y municipios vecinos en tu departamento.',
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
    categoria_id: '',
    intencion: '',
    operacion: '',

    titulo: '',
    descripcion: '',
    precio: '',

    pais_id: props.ubicacionDefecto?.pais_id || '',
    division_administrativa_id: props.ubicacionDefecto?.division_administrativa_id || '',
    ciudad_id: props.ubicacionDefecto?.ciudad_id || '',
    rango_cobertura: 'local',

    imagenes: [],

    // Vehículo
    marca: '',
    modelo: '',
    anio: '',
    kilometraje: '',
    cilindraje: '',
    combustible: '',
    transmision: '',
    color: '',
    placa: '',
    unico_propietario: null,
    documentacion_al_dia: null,
    vehiculo_observaciones: '',

    // Propiedad - Generales
    area_total: '',
    area_construida: '',
    habitaciones: '',
    banos: '',
    garajes: '',
    pisos: '',
    piso: '',
    estrato: '',
    antiguedad: '',
    remodelada: null,
    propiedad_observaciones: '',

    // Propiedad - Administración (arriendo)
    incluye_administracion: false,
    precio_administracion: '',

    // Propiedad - Servicios básicos y documentos
    tiene_servicios_publicos: false,
    tiene_agua: false,
    tiene_energia: false,
    tiene_gas: false,
    tiene_alcantarillado: false,
    tiene_internet: false,
    tiene_tv: false,
    papeles_al_dia: false,
    libre_gravamen: false,

    // Propiedad - Zonas comunes y amenidades
    tiene_ascensor: false,
    tiene_parqueadero: false,
    tiene_gimnasio: false,
    tiene_piscina: false,
    tiene_bbq: false,
    tiene_salon_comunal: false,
    tiene_zona_infantil: false,
    tiene_zonas_verdes: false,
    tiene_cancha_multiple: false,
    tiene_sauna_turco: false,
    tiene_coworking: false,
    tiene_parqueadero_visitantes: false,

    // Propiedad - Seguridad, interiores y comodidades
    tiene_porteria_24_7: false,
    tiene_circuito_cerrado: false,
    permite_mascotas: false,
    tiene_balcon: false,
    tiene_terraza: false,
    tiene_deposito: false,
    tiene_calentador: false,
    tiene_aire_acondicionado: false,
    cocina_integral: false,
    zona_lavanderia: false,
})

/*
|--------------------------------------------------------------------------
| Helpers de Normalización y Extracción de Datos API
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

/*
|--------------------------------------------------------------------------
| Resolución Robusta de IDs
|--------------------------------------------------------------------------
*/

async function resolverUbicacionIds() {
    if (!ubicacion.value.pais) return

    try {
        // 1. País
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

        // 2. Departamento
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

        // 3. Ciudad / Municipio
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

// Observadores de cambios manuales en el selector
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

watch(() => form.incluye_administracion, (incluye) => {
    if (incluye) {
        form.precio_administracion = ''
    }
})

// Al montar la vista, resolvemos de inmediato la ubicación precargada
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
            context_type: `clasificado_${campo}`,
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
| Categorías
|--------------------------------------------------------------------------
*/

const categoriaVehiculos = computed(() => props.categorias.find(cat => cat.slug === 'vehiculos'))
const categoriaPropiedades = computed(() => props.categorias.find(cat => cat.slug === 'propiedades'))

const subcategoriasTipo = computed(() => {
    if (!tipo.value) return []
    const categoriaPadre = tipo.value === 'vehiculo' ? categoriaVehiculos.value : categoriaPropiedades.value
    if (!categoriaPadre) return []
    return categoriaPadre.hijas ?? categoriaPadre.children ?? []
})

const seleccionarTipo = nuevoTipo => {
    tipo.value = nuevoTipo
    categoriaPrincipalId.value = nuevoTipo === 'vehiculo' ? categoriaVehiculos.value?.id ?? '' : categoriaPropiedades.value?.id ?? ''
    categoriaId.value = ''
    form.categoria_id = ''

    if (nuevoTipo === 'vehiculo') {
        if (!intencion.value) intencion.value = 'ofrecer'
        operacion.value = 'venta'
    } else {
        intencion.value = ''
        operacion.value = ''
    }
}

const seleccionarIntencion = nuevaIntencion => {
    intencion.value = nuevaIntencion
    operacion.value = 'venta'
}

const seleccionarOperacion = nuevaOperacion => {
    operacion.value = nuevaOperacion
}

watch(categoriaId, valor => { form.categoria_id = valor })
watch(intencion, valor => { form.intencion = valor })
watch(operacion, valor => { form.operacion = valor })

/*
|--------------------------------------------------------------------------
| Imágenes
|--------------------------------------------------------------------------
*/

const manejarImagenes = event => {
    const archivos = Array.from(event.target.files || [])
    const disponibles = 8 - imagenes.value.length
    if (disponibles <= 0) { event.target.value = ''; return }

    const nuevos = archivos.slice(0, disponibles)
    nuevos.forEach(archivo => {
        if (!archivo.type.startsWith('image/')) return
        imagenes.value.push(archivo)
        previews.value.push({ archivo, url: URL.createObjectURL(archivo) })
    })

    form.imagenes = imagenes.value
    event.target.value = ''
}

const eliminarImagen = index => {
    const preview = previews.value[index]
    if (preview?.url) URL.revokeObjectURL(preview.url)
    imagenes.value.splice(index, 1)
    previews.value.splice(index, 1)
    form.imagenes = imagenes.value
}

const establecerPrincipal = index => {
    if (index === 0) return
    const imagen = imagenes.value.splice(index, 1)[0]
    const preview = previews.value.splice(index, 1)[0]
    imagenes.value.unshift(imagen)
    previews.value.unshift(preview)
    form.imagenes = imagenes.value
}

/*
|--------------------------------------------------------------------------
| Textos Dinámicos y Helpers
|--------------------------------------------------------------------------
*/

const tituloPlaceholder = computed(() => {
    if (tipo.value === 'vehiculo') {
        return intencion.value === 'buscar' ? 'Ej. Busco automóvil familiar automático' : 'Ej. Mazda 3 Touring 2022'
    }
    if (intencion.value === 'buscar') {
        return operacion.value === 'arriendo' ? 'Ej. Busco apartamento en arriendo' : 'Ej. Busco lote urbano con servicios'
    }
    return operacion.value === 'arriendo' ? 'Ej. Apartamento en arriendo' : 'Ej. Casa en venta'
})

const precioLabel = computed(() => {
    if (intencion.value === 'buscar') return 'Presupuesto máximo'
    if (operacion.value === 'arriendo') return 'Canon de arriendo'
    return 'Precio'
})

const descripcionPlaceholder = computed(() => {
    if (intencion.value === 'buscar') {
        return 'Describe exactamente lo que necesitas encontrar, las características y condiciones importantes.'
    }
    return 'Describe las características principales, condiciones, ubicación y cualquier información de interés.'
})

const tituloOperacionPropiedad = computed(() => {
    return intencion.value === 'buscar'
        ? '¿Qué deseas buscar?'
        : '¿Qué deseas hacer con la propiedad?'
})

const textoBotonVenta = computed(() => {
    return intencion.value === 'buscar' ? 'Busco comprar' : 'Quiero vender'
})

const textoBotonArriendo = computed(() => {
    return intencion.value === 'buscar' ? 'Busco arrendar' : 'Quiero arrendar'
})

const puedeContinuarTipo = computed(() => Boolean(tipo.value))
const puedeContinuarOperacion = computed(() => {
    if (!tipo.value || !intencion.value) return false
    if (tipo.value === 'vehiculo') return true
    return Boolean(operacion.value)
})

const puedePublicar = computed(() => {
    return Boolean(
        categoriaId.value &&
        intencion.value &&
        operacion.value &&
        form.titulo?.trim() &&
        !alertaTitulo.value &&
        form.descripcion?.trim() &&
        !alertaDescripcion.value
    )
})

/*
|--------------------------------------------------------------------------
| Publicar
|--------------------------------------------------------------------------
*/

async function publicar() {
    form.categoria_id = categoriaId.value
    form.intencion = intencion.value
    form.operacion = operacion.value

    if (!form.ciudad_id || !form.division_administrativa_id || !form.pais_id) {
        await resolverUbicacionIds()
    }

    if (!form.ciudad_id) {
        alert('Debes seleccionar un País, Departamento y Ciudad válidos antes de publicar.')
        return
    }

    form.post(route('clasificados.store'), {
        forceFormData: true,
        preserveScroll: true,
    })
}
</script>

<template>

    <Head title="Publicar clasificado | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ENCABEZADO -->
            <section
                class="border-b border-amber-200/80 bg-amber-100/25 backdrop-blur-md shadow-lg shadow-amber-500/10">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <Link :href="route('clasificados.index')"
                        class="mb-4 inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-amber-600">
                        ← Volver a clasificados
                    </Link>

                    <div class="mt-2">
                        <div class="mb-1 flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-amber-600"></span>
                            <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-950">
                                Avisos y Oportunidades
                            </span>
                        </div>
                        <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                            Publicar clasificado
                        </h1>
                        <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                            Publica una propiedad o vehículo, o indica lo que estás buscando.
                        </p>
                    </div>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 space-y-6 bg-amber-50 rounded-xl p-3 mt-2">

                <!-- PASO 1 -->
                <section
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">Paso 1</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">¿Qué deseas publicar?</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 sm:p-6">
                        <button type="button" @click="seleccionarTipo('vehiculo')"
                            class="rounded-2xl border-2 p-6 text-left transition bg-white shadow-sm"
                            :class="tipo === 'vehiculo' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-200 shadow-md' : 'border-slate-300 hover:border-amber-300'">
                            <div
                                class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 border border-amber-200 text-3xl">
                                🚗</div>
                            <h3 class="text-base sm:text-lg font-black text-[#172A3D]">Vehículo</h3>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500 font-medium">Automóviles, motos,
                                maquinaria y vehículos comerciales.</p>
                        </button>

                        <button type="button" @click="seleccionarTipo('propiedad')"
                            class="rounded-2xl border-2 p-6 text-left transition bg-white shadow-sm"
                            :class="tipo === 'propiedad' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-200 shadow-md' : 'border-slate-300 hover:border-amber-300'">
                            <div
                                class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 border border-amber-200 text-3xl">
                                🏠</div>
                            <h3 class="text-base sm:text-lg font-black text-[#172A3D]">Propiedad</h3>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500 font-medium">Casas, apartamentos,
                                lotes, fincas, locales y oficinas.</p>
                        </button>
                    </div>
                </section>

                <!-- PASO 2 -->
                <section v-if="puedeContinuarTipo"
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">Paso 2</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">¿Qué deseas hacer?</h2>
                    </div>

                    <div class="p-5 sm:p-6 space-y-6">
                        <template v-if="tipo === 'vehiculo'">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <button type="button" @click="seleccionarIntencion('ofrecer')"
                                    class="rounded-2xl border-2 p-5 text-left transition bg-white shadow-sm"
                                    :class="intencion === 'ofrecer' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-200 shadow-md' : 'border-slate-300 hover:border-amber-300'">
                                    <div class="text-2xl mb-2">🏷️</div>
                                    <h3 class="font-bold text-[#172A3D]">Vender mi vehículo</h3>
                                    <p class="mt-1 text-xs text-slate-500 font-medium">Publica un vehículo disponible.
                                    </p>
                                </button>

                                <button type="button" @click="seleccionarIntencion('buscar')"
                                    class="rounded-2xl border-2 p-5 text-left transition bg-white shadow-sm"
                                    :class="intencion === 'buscar' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-200 shadow-md' : 'border-slate-300 hover:border-amber-300'">
                                    <div class="text-2xl mb-2">🔎</div>
                                    <h3 class="font-bold text-[#172A3D]">Busco comprar un vehículo</h3>
                                    <p class="mt-1 text-xs text-slate-500 font-medium">Indica qué vehículo estás
                                        buscando.</p>
                                </button>
                            </div>
                        </template>

                        <template v-else>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <button type="button" @click="seleccionarIntencion('ofrecer')"
                                    class="rounded-2xl border-2 p-5 text-left transition bg-white shadow-sm"
                                    :class="intencion === 'ofrecer' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-200 shadow-md' : 'border-slate-300 hover:border-amber-300'">
                                    <div class="text-2xl mb-2">🏷️</div>
                                    <h3 class="font-bold text-[#172A3D]">Ofrecer una propiedad</h3>
                                    <p class="mt-1 text-xs text-slate-500 font-medium">Tengo una propiedad para venta o
                                        arriendo.</p>
                                </button>

                                <button type="button" @click="seleccionarIntencion('buscar')"
                                    class="rounded-2xl border-2 p-5 text-left transition bg-white shadow-sm"
                                    :class="intencion === 'buscar' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-200 shadow-md' : 'border-slate-300 hover:border-amber-300'">
                                    <div class="text-2xl mb-2">🔎</div>
                                    <h3 class="font-bold text-[#172A3D]">Buscar una propiedad</h3>
                                    <p class="mt-1 text-xs text-slate-500 font-medium">Busco una propiedad para compra o
                                        arriendo.</p>
                                </button>
                            </div>

                            <div v-if="intencion" class="mt-6 border-t border-slate-100 pt-6">
                                <label class="mb-3 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    {{ tituloOperacionPropiedad }}
                                </label>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <button type="button" @click="seleccionarOperacion('venta')"
                                        class="rounded-xl border-2 px-5 py-4 text-left transition bg-white shadow-sm"
                                        :class="operacion === 'venta' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-200 shadow-md' : 'border-slate-300 hover:border-amber-300'">
                                        <span class="font-bold text-[#172A3D]">{{ textoBotonVenta }}</span>
                                    </button>

                                    <button type="button" @click="seleccionarOperacion('arriendo')"
                                        class="rounded-xl border-2 px-5 py-4 text-left transition bg-white shadow-sm"
                                        :class="operacion === 'arriendo' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-200 shadow-md' : 'border-slate-300 hover:border-amber-300'">
                                        <span class="font-bold text-[#172A3D]">{{ textoBotonArriendo }}</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </section>

                <!-- PASO 3 - CATEGORÍA -->
                <section v-if="puedeContinuarOperacion"
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">Paso 3</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Selecciona la categoría</h2>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <button v-for="categoria in subcategoriasTipo" :key="categoria.id" type="button"
                                @click="categoriaId = categoria.id"
                                class="rounded-xl border-2 p-4 text-left transition bg-white shadow-sm"
                                :class="Number(categoriaId) === Number(categoria.id) ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-200 shadow-md' : 'border-slate-300 hover:border-amber-300'">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-[#172A3D]">{{ categoria.nombre }}</span>
                                    <span v-if="Number(categoriaId) === Number(categoria.id)"
                                        class="text-amber-600 font-bold">✓</span>
                                </div>
                            </button>
                        </div>
                        <p v-if="form.errors.categoria_id" class="mt-3 text-xs text-red-600 font-medium">{{
                            form.errors.categoria_id }}
                        </p>
                    </div>
                </section>

                <!-- PASO 4 - CARACTERÍSTICAS -->
                <section v-if="categoriaId"
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">Paso 4</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Características</h2>
                    </div>

                    <div class="p-5 sm:p-6">
                        <!-- VEHÍCULO -->
                        <div v-if="tipo === 'vehiculo'" class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div><label class="form-label">Marca</label><input v-model="form.marca" type="text"
                                    class="form-input" placeholder="Ej. Toyota" /></div>
                            <div><label class="form-label">Modelo</label><input v-model="form.modelo" type="text"
                                    class="form-input" placeholder="Ej. Corolla" /></div>
                            <div><label class="form-label">Año</label><input v-model="form.anio" type="number"
                                    min="1900" :max="new Date().getFullYear() + 1" class="form-input"
                                    placeholder="Ej. 2022" /></div>
                            <div><label class="form-label">Kilometraje</label><input v-model="form.kilometraje"
                                    type="number" min="0" class="form-input" placeholder="Ej. 45000" /></div>
                            <div><label class="form-label">Cilindraje</label><input v-model="form.cilindraje"
                                    type="number" min="0" class="form-input" placeholder="Ej. 1600" /></div>
                            <div>
                                <label class="form-label">Combustible</label>
                                <select v-model="form.combustible" class="form-input">
                                    <option value="">Seleccionar</option>
                                    <option value="gasolina">Gasolina</option>
                                    <option value="diesel">Diésel</option>
                                    <option value="hibrido">Híbrido</option>
                                    <option value="electrico">Eléctrico</option>
                                    <option value="gas">Gas</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Transmisión</label>
                                <select v-model="form.transmision" class="form-input">
                                    <option value="">Seleccionar</option>
                                    <option value="manual">Manual</option>
                                    <option value="automatica">Automática</option>
                                    <option value="cvt">CVT</option>
                                </select>
                            </div>
                            <div><label class="form-label">Color</label><input v-model="form.color" type="text"
                                    class="form-input" placeholder="Ej. Blanco" /></div>
                            <div><label class="form-label">Placa</label><input v-model="form.placa" type="text"
                                    class="form-input uppercase" placeholder="Ej. ABC123" /></div>

                            <div class="space-y-3 md:col-span-2 pt-2">
                                <label class="form-check"><input v-model="form.unico_propietario" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Soy el único
                                        propietario</span></label>
                                <label class="form-check"><input v-model="form.documentacion_al_dia" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>La documentación está al
                                        día</span></label>
                            </div>

                            <div class="md:col-span-2"><label class="form-label">Observaciones del
                                    vehículo</label><textarea v-model="form.vehiculo_observaciones" rows="3"
                                    class="form-input"></textarea></div>
                        </div>

                        <!-- PROPIEDAD -->
                        <div v-if="tipo === 'propiedad'" class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div><label class="form-label">Área total (m²)</label><input v-model="form.area_total"
                                    type="number" min="0" step="0.01" class="form-input" placeholder="Ej. 120" /></div>
                            <div><label class="form-label">Área construida (m²)</label><input
                                    v-model="form.area_construida" type="number" min="0" step="0.01" class="form-input"
                                    placeholder="Ej. 95" /></div>
                            <div><label class="form-label">Habitaciones</label><input v-model="form.habitaciones"
                                    type="number" min="0" class="form-input" /></div>
                            <div><label class="form-label">Baños</label><input v-model="form.banos" type="number"
                                    min="0" class="form-input" /></div>
                            <div><label class="form-label">Garajes</label><input v-model="form.garajes" type="number"
                                    min="0" class="form-input" /></div>
                            <div>
                                <label class="form-label">Estrato</label>
                                <select v-model="form.estrato" class="form-input">
                                    <option value="">Seleccionar</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="rural">Rural</option>
                                </select>
                            </div>
                            <div><label class="form-label">Pisos de la propiedad</label><input v-model="form.pisos"
                                    type="number" min="0" class="form-input" /></div>
                            <div><label class="form-label">Piso</label><input v-model="form.piso" type="number" min="0"
                                    class="form-input" placeholder="Ej. 3" /></div>
                            <div><label class="form-label">Antigüedad</label><input v-model="form.antiguedad"
                                    type="text" class="form-input" placeholder="Ej. 5 años" /></div>
                            <div class="flex items-center pt-6">
                                <label class="form-check"><input v-model="form.remodelada" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>Propiedad
                                        remodelada</span></label>
                            </div>

                            <!-- ADMINISTRACIÓN (ARRIENDOS) -->
                            <div v-if="operacion === 'arriendo'"
                                class="md:col-span-2 rounded-2xl border border-amber-200/80 bg-amber-50/30 p-4 space-y-4">
                                <label
                                    class="text-xs font-bold uppercase tracking-wider text-slate-700 block">Condiciones
                                    de
                                    administración</label>
                                <label class="form-check"><input v-model="form.incluye_administracion" type="checkbox"
                                        :true-value="true" :false-value="false" /><span>El canon incluye la
                                        administración</span></label>
                                <div v-if="!form.incluye_administracion" class="max-w-xs pt-1">
                                    <label class="form-label">Valor mensual de administración</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">$</span>
                                        <input v-model="form.precio_administracion" type="number" min="0" step="0.01"
                                            placeholder="Ej. 180000" class="form-input pl-9 font-semibold" />
                                    </div>
                                </div>
                            </div>

                            <!-- SERVICIOS BÁSICOS Y DOCUMENTACIÓN -->
                            <div class="space-y-3 md:col-span-2 pt-2 border-t border-slate-100">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-700">💡 Servicios
                                    básicos y
                                    documentación</p>
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                    <label class="form-check"><input v-model="form.tiene_servicios_publicos"
                                            type="checkbox" :true-value="true" :false-value="false" /><span>Servicios
                                            públicos</span></label>
                                    <label class="form-check"><input v-model="form.tiene_agua" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Agua</span></label>
                                    <label class="form-check"><input v-model="form.tiene_energia" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Energía</span></label>
                                    <label class="form-check"><input v-model="form.tiene_gas" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Gas natural</span></label>
                                    <label class="form-check"><input v-model="form.tiene_alcantarillado" type="checkbox"
                                            :true-value="true"
                                            :false-value="false" /><span>Alcantarillado</span></label>
                                    <label class="form-check"><input v-model="form.tiene_internet" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Internet /
                                            Fibra</span></label>
                                    <label class="form-check"><input v-model="form.tiene_tv" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Televisión</span></label>
                                    <label class="form-check"><input v-model="form.papeles_al_dia" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Papeles al
                                            día</span></label>
                                    <label class="form-check"><input v-model="form.libre_gravamen" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Libre de
                                            gravamen</span></label>
                                </div>
                            </div>

                            <!-- ZONAS COMUNES Y AMENIDADES -->
                            <div class="space-y-3 md:col-span-2 pt-2 border-t border-slate-100">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-700">🏊‍♂️ Zonas comunes
                                    y amenidades
                                </p>
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                    <label class="form-check"><input v-model="form.tiene_ascensor" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Ascensor</span></label>
                                    <label class="form-check"><input v-model="form.tiene_parqueadero" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Parqueadero
                                            propio</span></label>
                                    <label class="form-check"><input v-model="form.tiene_gimnasio" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Gimnasio</span></label>
                                    <label class="form-check"><input v-model="form.tiene_piscina" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Piscina</span></label>
                                    <label class="form-check"><input v-model="form.tiene_bbq" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Zona BBQ</span></label>
                                    <label class="form-check"><input v-model="form.tiene_salon_comunal" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Salón comunal</span></label>
                                    <label class="form-check"><input v-model="form.tiene_zona_infantil" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Parque
                                            infantil</span></label>
                                    <label class="form-check"><input v-model="form.tiene_zonas_verdes" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Zonas verdes</span></label>
                                    <label class="form-check"><input v-model="form.tiene_cancha_multiple"
                                            type="checkbox" :true-value="true" :false-value="false" /><span>Cancha
                                            múltiple</span></label>
                                    <label class="form-check"><input v-model="form.tiene_sauna_turco" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Sauna / Turco</span></label>
                                    <label class="form-check"><input v-model="form.tiene_coworking" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Zona
                                            Coworking</span></label>
                                    <label class="form-check"><input v-model="form.tiene_parqueadero_visitantes"
                                            type="checkbox" :true-value="true" :false-value="false" /><span>Parqueadero
                                            visitantes</span></label>
                                </div>
                            </div>

                            <!-- COMODIDADES INTERIORES, SEGURIDAD Y POLÍTICAS -->
                            <div class="space-y-3 md:col-span-2 pt-2 border-t border-slate-100">
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-700">🛋️ Interiores,
                                    seguridad y
                                    políticas</p>
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                    <label class="form-check"><input v-model="form.tiene_porteria_24_7" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Vigilancia
                                            24/7</span></label>
                                    <label class="form-check"><input v-model="form.tiene_circuito_cerrado"
                                            type="checkbox" :true-value="true" :false-value="false" /><span>Cámaras /
                                            CCTV</span></label>
                                    <label class="form-check"><input v-model="form.permite_mascotas" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Permite
                                            mascotas</span></label>
                                    <label class="form-check"><input v-model="form.tiene_balcon" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Balcón</span></label>
                                    <label class="form-check"><input v-model="form.tiene_terraza" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Terraza</span></label>
                                    <label class="form-check"><input v-model="form.tiene_deposito" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Depósito /
                                            Bodega</span></label>
                                    <label class="form-check"><input v-model="form.cocina_integral" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Cocina
                                            integral</span></label>
                                    <label class="form-check"><input v-model="form.zona_lavanderia" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Zona de ropas</span></label>
                                    <label class="form-check"><input v-model="form.tiene_calentador" type="checkbox"
                                            :true-value="true" :false-value="false" /><span>Calentador</span></label>
                                    <label class="form-check"><input v-model="form.tiene_aire_acondicionado"
                                            type="checkbox" :true-value="true" :false-value="false" /><span>Aire
                                            acondicionado</span></label>
                                </div>
                            </div>

                            <div class="md:col-span-2"><label class="form-label">Observaciones de la
                                    propiedad</label><textarea v-model="form.propiedad_observaciones" rows="3"
                                    class="form-input"></textarea></div>
                        </div>
                    </div>
                </section>

                <!-- PASO 5 - INFORMACIÓN GENERAL -->
                <section v-if="categoriaId"
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">Paso 5</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Información del clasificado</h2>
                    </div>

                    <div class="p-5 sm:p-6 space-y-5">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="form-label mb-0">Título <span class="text-red-500">*</span></label>
                                <button type="button" @click="aplicarCorreccionMagica('titulo')"
                                    :disabled="cargandoIA.titulo || !form.titulo.trim()"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg border border-amber-200 transition disabled:opacity-50">
                                    <span v-if="cargandoIA.titulo" class="animate-spin">⏳</span>
                                    <span v-else>✨</span>
                                    <span>Corrección Mágica</span>
                                </button>
                            </div>
                            <input v-model="form.titulo" type="text" maxlength="255" :placeholder="tituloPlaceholder"
                                class="form-input" :class="{ 'border-red-500': form.errors.titulo || alertaTitulo }" />
                            <p v-if="alertaTitulo" class="mt-1.5 text-xs text-amber-600 font-bold">{{ alertaTitulo }}
                            </p>
                            <p v-if="form.errors.titulo" class="form-error">{{ form.errors.titulo }}</p>
                        </div>

                        <div>
                            <div class="mb-1.5 flex items-center justify-between gap-3">
                                <label class="form-label mb-0">Descripción <span class="text-red-500">*</span></label>
                                <button type="button" @click="aplicarCorreccionMagica('descripcion')"
                                    :disabled="cargandoIA.descripcion || !form.descripcion.trim()"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg border border-amber-200 transition disabled:opacity-50">
                                    <span v-if="cargandoIA.descripcion" class="animate-spin">⏳</span>
                                    <span v-else>✨</span>
                                    <span>Corrección Mágica</span>
                                </button>
                            </div>
                            <textarea v-model="form.descripcion" rows="6" :placeholder="descripcionPlaceholder"
                                class="form-input"
                                :class="{ 'border-red-500': form.errors.descripcion || alertaDescripcion }"></textarea>
                            <p v-if="alertaDescripcion" class="mt-1.5 text-xs text-amber-600 font-bold">{{
                                alertaDescripcion }}</p>
                            <p v-if="form.errors.descripcion" class="form-error">{{ form.errors.descripcion }}</p>
                        </div>

                        <div class="max-w-md">
                            <label class="form-label">{{ precioLabel }}</label>
                            <div class="relative">
                                <span
                                    class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">$</span>
                                <input v-model="form.precio" type="number" min="0" step="0.01"
                                    class="form-input pl-9 font-semibold" placeholder="0" />
                            </div>
                            <p v-if="form.errors.precio" class="form-error">{{ form.errors.precio }}</p>
                        </div>
                    </div>
                </section>

                <!-- PASO 6 - UBICACIÓN (MARKETPLACE) -->
                <section v-if="categoriaId"
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">Paso 6</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Ubicación del clasificado</h2>
                        <p class="mt-0.5 text-xs text-slate-500 font-medium">Indica dónde se encuentra físicamente la
                            propiedad o
                            vehículo.</p>
                    </div>

                    <div class="space-y-5 px-5 py-6 sm:px-6">
                        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                            <UbicacionSelector v-model:pais="ubicacion.pais"
                                v-model:departamento="ubicacion.departamento" v-model:ciudad="ubicacion.ciudad" />
                        </div>
                        <p class="text-xs leading-relaxed text-slate-500 font-medium">
                            La ubicación seleccionada se utilizará como punto de partida para determinar la cobertura
                            geográfica.
                        </p>
                    </div>
                </section>

                <!-- PASO 7 - COBERTURA (MARKETPLACE) -->
                <section v-if="categoriaId"
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">Paso 7</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Disponibilidad / Rango de exhibición
                        </h2>
                        <p class="mt-0.5 text-xs text-slate-500 font-medium">Define hasta dónde deseas que sea visible
                            tu publicación.
                        </p>
                    </div>

                    <div class="space-y-6 px-5 py-6 sm:px-6">
                        <div class="grid gap-3 sm:grid-cols-3">
                            <label v-for="rango in rangosCobertura" :key="rango.value"
                                class="cursor-pointer rounded-xl border p-4 transition bg-white shadow-xs" :class="form.rango_cobertura === rango.value
                                    ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-200 shadow-md'
                                    : 'border-slate-300 hover:border-amber-300'">
                                <div class="flex items-start gap-3">
                                    <input v-model="form.rango_cobertura" type="radio" :value="rango.value"
                                        class="mt-1 accent-amber-600" />
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
                </section>

                <!-- PASO 8 - IMÁGENES -->
                <section v-if="categoriaId"
                    class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                    <div class="border-b border-slate-100 bg-amber-50/40 px-5 py-4 sm:px-6">
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">Paso 8</span>
                        <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Imágenes</h2>
                        <p class="mt-0.5 text-xs text-slate-500 font-medium">Puedes agregar hasta 8 imágenes. La primera
                            será la
                            principal.</p>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div
                            class="rounded-2xl border-2 border-dashed border-slate-300 p-8 text-center transition hover:border-amber-400 hover:bg-amber-50/30 bg-slate-50">
                            <input id="imagenes" type="file" accept="image/*" multiple class="hidden"
                                @change="manejarImagenes" />
                            <label for="imagenes" class="cursor-pointer">
                                <div
                                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-100 border border-amber-200 text-2xl">
                                    📷</div>
                                <p class="mt-4 font-bold text-[#172A3D]">Agregar imágenes</p>
                                <p class="mt-1 text-xs text-slate-500 font-medium">Selecciona hasta {{ 8 -
                                    imagenes.length }} imágenes
                                    más.</p>
                            </label>
                        </div>

                        <div v-if="previews.length" class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                            <div v-for="(preview, index) in previews" :key="preview.url"
                                class="group relative overflow-hidden rounded-xl border border-slate-200 bg-slate-50 shadow-sm">
                                <img :src="preview.url" class="aspect-square w-full object-cover" alt="Vista previa" />
                                <div v-if="index === 0"
                                    class="absolute left-2 top-2 rounded-full bg-amber-600 px-2.5 py-1 text-[10px] font-black text-white shadow-sm">
                                    Principal</div>
                                <div
                                    class="absolute inset-x-0 bottom-0 flex gap-2 bg-gradient-to-t from-black/80 to-transparent p-2 pt-8">
                                    <button v-if="index !== 0" type="button" @click="establecerPrincipal(index)"
                                        class="flex-1 rounded-lg bg-white px-2 py-1.5 text-xs font-bold text-slate-800 hover:bg-slate-100 shadow-sm">Principal</button>
                                    <button type="button" @click="eliminarImagen(index)"
                                        class="rounded-lg bg-red-600 px-2 py-1.5 text-xs font-bold text-white hover:bg-red-700 shadow-sm">Eliminar</button>
                                </div>
                            </div>
                        </div>
                        <p v-if="form.errors.imagenes" class="mt-3 text-xs text-red-600 font-medium">{{
                            form.errors.imagenes }}</p>
                    </div>
                </section>

                <!-- RESUMEN / PUBLICAR -->
                <section v-if="categoriaId"
                    class="rounded-3xl border border-amber-200 bg-amber-50/80 p-6 shadow-xl backdrop-blur-md">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="font-bold text-amber-950">¿Todo listo?</h2>
                            <p class="mt-1 text-xs sm:text-sm text-amber-900 font-medium">Tu clasificado será publicado
                                inmediatamente.
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <Link :href="route('clasificados.index')"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm">
                                Cancelar
                            </Link>

                            <button type="button"
                                :disabled="form.processing || !puedePublicar || Boolean(alertaTitulo) || Boolean(alertaDescripcion)"
                                @click="publicar"
                                class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-6 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-amber-500/25 transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-50">
                                <svg v-if="form.processing" class="mr-2 h-4 w-4 animate-spin"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                                </svg>
                                {{ form.processing ? 'Publicando...' : 'Publicar clasificado' }}
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
    @apply w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] shadow-sm outline-none transition focus:border-amber-600 focus:ring-2 focus:ring-amber-100;
}

.form-input::placeholder {
    @apply text-slate-400;
}

.form-check {
    @apply flex cursor-pointer items-center gap-2.5 text-xs sm:text-sm font-bold text-slate-700;
}

.form-check input {
    @apply h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500 accent-amber-600;
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