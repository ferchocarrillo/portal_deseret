<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    clasificado: {
        type: Object,
        required: true,
    },
    esPropietario: {
        type: Boolean,
        default: false,
    },
    puedeDenunciar: {
        type: Boolean,
        default: true,
    },
})

/*
|--------------------------------------------------------------------------
| Estado de Galería
|--------------------------------------------------------------------------
*/

const imagenSeleccionada = ref(0)

const imagenes = computed(() => props.clasificado?.imagenes ?? [])

const imagenActual = computed(() => {
    if (!imagenes.value.length) return null
    return imagenes.value[imagenSeleccionada.value] ?? imagenes.value[0]
})

const obtenerUrlImagen = imagen => {
    if (!imagen?.ruta) return null
    if (imagen.ruta.startsWith('http://') || imagen.ruta.startsWith('https://') || imagen.ruta.startsWith('/')) {
        return imagen.ruta
    }
    return `/storage/${imagen.ruta}`
}

const seleccionarImagen = index => {
    imagenSeleccionada.value = index
}

const siguienteImagen = () => {
    if (!imagenes.value.length) return
    imagenSeleccionada.value = (imagenSeleccionada.value + 1) % imagenes.value.length
}

const anteriorImagen = () => {
    if (!imagenes.value.length) return
    imagenSeleccionada.value = imagenSeleccionada.value === 0 ? imagenes.value.length - 1 : imagenSeleccionada.value - 1
}

/*
|--------------------------------------------------------------------------
| Tipo y Estado
|--------------------------------------------------------------------------
*/

const tipoPublicacion = computed(() => {
    const intencion = props.clasificado?.intencion
    const operacion = props.clasificado?.operacion

    if (intencion === 'buscar' && operacion === 'venta') {
        return { texto: 'Busco comprar', clase: 'bg-orange-100 text-orange-900 border border-orange-200', icono: '🔎' }
    }
    if (intencion === 'buscar' && operacion === 'arriendo') {
        return { texto: 'Busco arrendar', clase: 'bg-purple-100 text-purple-900 border border-purple-200', icono: '🔎' }
    }
    if (intencion === 'ofrecer' && operacion === 'arriendo') {
        return { texto: 'En arriendo', clase: 'bg-blue-100 text-blue-900 border border-blue-200', icono: '🏠' }
    }
    return { texto: 'En venta', clase: 'bg-emerald-100 text-emerald-900 border border-emerald-200', icono: '🏷️' }
})

const esBusqueda = computed(() => props.clasificado?.intencion === 'buscar')
const esVehiculo = computed(() => Boolean(props.clasificado?.vehiculo))
const esPropiedad = computed(() => Boolean(props.clasificado?.propiedad))

const formatearMoneda = valor => {
    if (valor === null || valor === undefined || valor === '') return null
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0,
    }).format(Number(valor))
}

const precioFormateado = computed(() => formatearMoneda(props.clasificado?.precio))

const etiquetaPrecio = computed(() => {
    if (esBusqueda.value) return 'Presupuesto máximo'
    if (props.clasificado?.operacion === 'arriendo') return 'Canon de arriendo'
    return 'Precio'
})

const ubicacion = computed(() => {
    const partes = []
    if (props.clasificado?.ciudad?.nombre) partes.push(props.clasificado.ciudad.nombre)
    if (props.clasificado?.division_administrativa?.nombre) partes.push(props.clasificado.division_administrativa.nombre)
    if (props.clasificado?.pais?.nombre) partes.push(props.clasificado.pais.nombre)
    return partes.join(', ')
})

const estadoPublicacion = computed(() => {
    const estado = props.clasificado?.estado
    const estados = {
        publicado: { texto: 'Publicado', clase: 'bg-emerald-100 text-emerald-800 border border-emerald-300' },
        pausado: { texto: 'Pausado', clase: 'bg-amber-100 text-amber-800 border border-amber-300' },
        vendido: { texto: 'Vendido', clase: 'bg-slate-100 text-slate-800 border border-slate-300' },
        arrendado: { texto: 'Arrendado', clase: 'bg-slate-100 text-slate-800 border border-slate-300' },
        cerrado: { texto: 'Cerrado', clase: 'bg-slate-100 text-slate-800 border border-slate-300' },
        suspendido: { texto: 'Suspendido', clase: 'bg-red-100 text-red-800 border border-red-300' },
    }
    return estados[estado] ?? { texto: estado ?? 'Publicado', clase: 'bg-slate-100 text-slate-800 border border-slate-300' }
})

/*
|--------------------------------------------------------------------------
| Agrupación de Amenidades de Propiedad
|--------------------------------------------------------------------------
*/

const prop = computed(() => props.clasificado?.propiedad || {})

const serviciosActivos = computed(() => {
    const lista = [
        { label: 'Servicios públicos', activo: prop.value.tiene_servicios_publicos, icon: '💡' },
        { label: 'Agua potable', activo: prop.value.tiene_agua, icon: '💧' },
        { label: 'Energía eléctrica', activo: prop.value.tiene_energia, icon: '⚡' },
        { label: 'Gas natural', activo: prop.value.tiene_gas, icon: '🔥' },
        { label: 'Alcantarillado', activo: prop.value.tiene_alcantarillado, icon: '🚰' },
        { label: 'Internet / Fibra', activo: prop.value.tiene_internet, icon: '🌐' },
        { label: 'Televisión', activo: prop.value.tiene_tv, icon: '📺' },
        { label: 'Papeles al día', activo: prop.value.papeles_al_dia, icon: '📄' },
        { label: 'Libre de gravamen', activo: prop.value.libre_gravamen, icon: '✅' },
    ]
    return lista.filter(item => item.activo)
})

const zonasComunesActivas = computed(() => {
    const lista = [
        { label: 'Ascensor', activo: prop.value.tiene_ascensor, icon: '🛗' },
        { label: 'Parqueadero propio', activo: prop.value.tiene_parqueadero, icon: '🚗' },
        { label: 'Gimnasio', activo: prop.value.tiene_gimnasio, icon: '🏋️' },
        { label: 'Piscina', activo: prop.value.tiene_piscina, icon: '🏊' },
        { label: 'Zona BBQ', activo: prop.value.tiene_bbq, icon: '🍖' },
        { label: 'Salón comunal', activo: prop.value.tiene_salon_comunal, icon: '🎉' },
        { label: 'Parque infantil', activo: prop.value.tiene_zona_infantil, icon: '🛝' },
        { label: 'Zonas verdes', activo: prop.value.tiene_zonas_verdes, icon: '🌳' },
        { label: 'Cancha múltiple', activo: prop.value.tiene_cancha_multiple, icon: '⚽' },
        { label: 'Sauna / Turco', activo: prop.value.tiene_sauna_turco, icon: '🧖' },
        { label: 'Zona Coworking', activo: prop.value.tiene_coworking, icon: '💻' },
        { label: 'Parqueadero visitantes', activo: prop.value.tiene_parqueadero_visitantes, icon: '🅿️' },
    ]
    return lista.filter(item => item.activo)
})

const comodidadesInterioresActivas = computed(() => {
    const lista = [
        { label: 'Vigilancia 24/7', activo: prop.value.tiene_porteria_24_7, icon: '👮' },
        { label: 'CCTV / Cámaras', activo: prop.value.tiene_circuito_cerrado, icon: '📹' },
        { label: 'Permite mascotas', activo: prop.value.permite_mascotas, icon: '🐾' },
        { label: 'Balcón', activo: prop.value.tiene_balcon, icon: '🌅' },
        { label: 'Terraza', activo: prop.value.tiene_terraza, icon: '☀️' },
        { label: 'Depósito / Bodega', activo: prop.value.tiene_deposito, icon: '📦' },
        { label: 'Cocina integral', activo: prop.value.cocina_integral, icon: '🍳' },
        { label: 'Zona de ropas', activo: prop.value.zona_lavanderia, icon: '🧺' },
        { label: 'Calentador de agua', activo: prop.value.tiene_calentador, icon: '♨️' },
        { label: 'Aire acondicionado', activo: prop.value.tiene_aire_acondicionado, icon: '❄️' },
        { label: 'Remodelada', activo: prop.value.remodelada, icon: '✨' },
    ]
    return lista.filter(item => item.activo)
})

/*
|--------------------------------------------------------------------------
| Información del Propietario
|--------------------------------------------------------------------------
*/

const propietario = computed(() => props.clasificado?.usuario ?? null)

const nombrePropietario = computed(() => {
    if (!propietario.value) return 'Miembro de Portal Deseret'
    const nombre = [propietario.value.nombres, propietario.value.apellidos].filter(Boolean).join(' ')
    return nombre || propietario.value.name || 'Miembro de Portal Deseret'
})

const fotoPropietario = computed(() => {
    if (!propietario.value?.foto) return null
    if (propietario.value.foto.startsWith('http://') || propietario.value.foto.startsWith('https://') || propietario.value.foto.startsWith('/')) {
        return propietario.value.foto
    }
    return `/storage/${propietario.value.foto}`
})

/*
|--------------------------------------------------------------------------
| Acciones del Propietario
|--------------------------------------------------------------------------
*/

const pausar = () => {
    router.post(route('clasificados.pausar', props.clasificado.id), {}, { preserveScroll: true })
}

const activar = () => {
    router.post(route('clasificados.activar', props.clasificado.id), {}, { preserveScroll: true })
}

const eliminar = () => {
    if (!confirm('¿Estás seguro de que deseas eliminar este clasificado? Esta acción no se puede deshacer.')) return
    router.delete(route('clasificados.destroy', props.clasificado.id), { preserveScroll: true })
}

const contactarPropietario = () => {
    router.post(route('clasificados.mensajes.store', props.clasificado.id), {}, { preserveScroll: true })
}

/*
|--------------------------------------------------------------------------
| Favorito Interactivo
|--------------------------------------------------------------------------
*/

const esFavoritoActual = ref(Boolean(props.clasificado?.es_favorito))

watch(() => props.clasificado?.es_favorito, (val) => {
    esFavoritoActual.value = Boolean(val)
})

const alternarFavorito = () => {
    const nuevo = !esFavoritoActual.value
    esFavoritoActual.value = nuevo
    if (props.clasificado) props.clasificado.es_favorito = nuevo

    router.post(
        route('favoritos.toggle'),
        { publicacion_id: props.clasificado.id, modulo: 'clasificados' },
        {
            preserveScroll: true,
            preserveState: true,
            onError: () => {
                esFavoritoActual.value = !nuevo
                if (props.clasificado) props.clasificado.es_favorito = !nuevo
            },
        }
    )
}
</script>

<template>

    <Head :title="clasificado.titulo" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ENCABEZADO -->
            <section
                class="border-b border-amber-200/80 bg-amber-100/25 backdrop-blur-md shadow-lg shadow-amber-500/10">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <Link :href="route('clasificados.index')"
                        class="mb-4 inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-xs sm:text-sm font-bold text-slate-700 shadow-sm border border-slate-300 transition hover:bg-amber-50 hover:text-amber-700">
                        ← Volver a clasificados
                    </Link>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="mb-2 flex flex-wrap items-center gap-2">
                                <span class="rounded-full px-3 py-1 text-xs font-bold shadow-xs"
                                    :class="tipoPublicacion.clase">
                                    {{ tipoPublicacion.icono }} {{ tipoPublicacion.texto }}
                                </span>
                                <span class="rounded-full px-3 py-1 text-xs font-bold shadow-xs"
                                    :class="estadoPublicacion.clase">
                                    {{ estadoPublicacion.texto }}
                                </span>
                                <span
                                    class="rounded-full bg-slate-100 border border-slate-300 px-3 py-1 text-xs font-bold text-slate-700 capitalize">
                                    📍 Cobertura {{ clasificado.rango_cobertura || 'departamental' }}
                                </span>
                            </div>

                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                {{ clasificado.titulo }}
                            </h1>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
            <div class="py-8">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6 bg-amber-50 rounded-xl p-3 mt-0">

                    <!-- GALERÍA Y RESUMEN LATERAL -->
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">

                        <!-- Galería -->
                        <div
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-amber-500/15">
                            <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                                <img v-if="imagenActual" :src="obtenerUrlImagen(imagenActual)" :alt="clasificado.titulo"
                                    class="h-full w-full object-cover" />
                                <div v-else
                                    class="flex h-full w-full flex-col items-center justify-center text-slate-400">
                                    <span class="text-6xl">{{ esVehiculo ? '🚗' : '🏠' }}</span>
                                    <p class="mt-3 text-sm font-medium">Esta publicación no tiene imágenes.</p>
                                </div>

                                <button v-if="imagenes.length > 1" type="button" @click="anteriorImagen"
                                    class="absolute left-4 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/50 text-xl text-white backdrop-blur transition hover:bg-black/70">
                                    ‹
                                </button>
                                <button v-if="imagenes.length > 1" type="button" @click="siguienteImagen"
                                    class="absolute right-4 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-black/50 text-xl text-white backdrop-blur transition hover:bg-black/70">
                                    ›
                                </button>

                                <div v-if="imagenes.length > 1"
                                    class="absolute bottom-4 right-4 rounded-full bg-black/60 px-3 py-1.5 text-xs font-bold text-white backdrop-blur">
                                    {{ imagenSeleccionada + 1 }} / {{ imagenes.length }}
                                </div>
                            </div>

                            <div v-if="imagenes.length > 1"
                                class="grid grid-cols-4 gap-3 p-4 sm:grid-cols-6 lg:grid-cols-8">
                                <button v-for="(imagen, index) in imagenes" :key="imagen.id ?? index" type="button"
                                    @click="seleccionarImagen(index)"
                                    class="overflow-hidden rounded-xl border-2 transition shadow-xs"
                                    :class="imagenSeleccionada === index ? 'border-amber-600 ring-2 ring-amber-200' : 'border-slate-200 hover:border-amber-300'">
                                    <img :src="obtenerUrlImagen(imagen)" :alt="`Imagen ${index + 1}`"
                                        class="aspect-square w-full object-cover" />
                                </button>
                            </div>
                        </div>

                        <!-- Resumen Lateral -->
                        <div
                            class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15 flex flex-col justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full px-3 py-1 text-xs font-bold"
                                        :class="tipoPublicacion.clase">
                                        {{ tipoPublicacion.icono }} {{ tipoPublicacion.texto }}
                                    </span>
                                    <span v-if="clasificado.categoria" class="text-xs font-bold text-slate-500">
                                        {{ clasificado.categoria.nombre }}
                                    </span>
                                </div>

                                <h2 class="mt-3 text-xl font-black leading-tight text-[#172A3D]">
                                    {{ clasificado.titulo }}
                                </h2>

                                <!-- PRECIO PRINCIPAL -->
                                <div v-if="precioFormateado"
                                    class="mt-5 rounded-2xl bg-amber-50/50 border border-amber-200 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wider text-amber-900">
                                        {{ etiquetaPrecio }}
                                    </p>
                                    <p class="mt-1 text-2xl font-black text-amber-950">
                                        {{ precioFormateado }}
                                    </p>

                                    <!-- DETALLE DE ADMINISTRACIÓN -->
                                    <div v-if="esPropiedad && clasificado.operacion === 'arriendo'"
                                        class="mt-2 pt-2 border-t border-amber-200/60 text-xs font-medium">
                                        <span v-if="prop.incluye_administracion"
                                            class="inline-flex items-center text-emerald-800 font-bold">
                                            ✓ Incluye administración
                                        </span>
                                        <span v-else-if="prop.precio_administracion" class="text-slate-600">
                                            + {{ formatearMoneda(prop.precio_administracion) }} de administración
                                        </span>
                                        <span v-else class="text-slate-500">
                                            Administración no incluida / a consultar
                                        </span>
                                    </div>
                                </div>

                                <div v-else class="mt-5 rounded-2xl bg-slate-50 border border-slate-200 p-4">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Precio</p>
                                    <p class="mt-1 text-base font-bold text-slate-700">Precio a consultar</p>
                                </div>

                                <!-- UBICACIÓN -->
                                <div v-if="ubicacion" class="mt-5 flex items-start gap-3">
                                    <div
                                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-amber-100 border border-amber-200 text-amber-700">
                                        📍
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Ubicación
                                        </p>
                                        <p class="mt-0.5 text-xs sm:text-sm font-bold text-[#172A3D]">{{ ubicacion }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- ACCIONES -->
                            <div class="mt-6 border-t border-slate-100 pt-5 space-y-2.5">
                                <div v-if="esPropietario" class="space-y-2">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                        Administrar publicación</p>
                                    <Link :href="route('clasificados.edit', clasificado.id)"
                                        class="inline-flex w-full items-center justify-center rounded-xl bg-amber-600 px-4 py-2.5 text-xs sm:text-sm font-black text-white shadow-md shadow-amber-500/25 transition hover:bg-amber-700">
                                        Editar publicación
                                    </Link>
                                    <button v-if="clasificado.estado === 'publicado'" type="button" @click="pausar"
                                        class="w-full rounded-xl border border-amber-300 bg-amber-50 px-4 py-2 text-xs sm:text-sm font-bold text-amber-900 hover:bg-amber-100">
                                        Pausar publicación
                                    </button>
                                    <button v-if="clasificado.estado === 'pausado'" type="button" @click="activar"
                                        class="w-full rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-2 text-xs sm:text-sm font-bold text-emerald-900 hover:bg-emerald-100">
                                        Reactivar publicación
                                    </button>
                                    <button type="button" @click="eliminar"
                                        class="w-full rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-xs sm:text-sm font-bold text-red-700 hover:bg-red-100">
                                        Eliminar publicación
                                    </button>
                                </div>

                                <div v-else-if="clasificado.estado === 'publicado'" class="space-y-2">
                                    <button type="button" @click="contactarPropietario"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-green-600 px-4 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-green-500/25 transition hover:bg-green-700">
                                        💬 Contactar al anunciante
                                    </button>
                                </div>

                                <button type="button" @click="alternarFavorito"
                                    class="flex w-full items-center justify-center gap-2.5 rounded-xl px-4 py-3 text-xs sm:text-sm font-black transition shadow-md"
                                    :class="esFavoritoActual
                                        ? 'bg-amber-400 text-[#172A3D] shadow-amber-400/30 hover:bg-amber-500'
                                        : 'bg-amber-100/70 text-[#172A3D] border border-amber-300 hover:bg-amber-200/80'">
                                    <span class="text-base"
                                        :class="esFavoritoActual ? 'text-red-600' : 'text-slate-600'">
                                        {{ esFavoritoActual ? '❤️' : '♡' }}
                                    </span>
                                    <span>{{ esFavoritoActual ? '¡En tus favoritos!' : 'Guardar en favoritos' }}</span>
                                </button>
                            </div>
                        </div>

                    </div>

                    <!-- DESCRIPCIÓN Y ESPECIFICACIONES -->
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
                        <div class="space-y-6">

                            <!-- FICHA TÉCNICA: VEHÍCULO -->
                            <section v-if="esVehiculo"
                                class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15">
                                <h2 class="text-base sm:text-lg font-bold text-[#172A3D] mb-4">🚗 Ficha técnica del
                                    vehículo</h2>
                                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                                    <div v-if="clasificado.vehiculo.marca" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Marca</p>
                                        <p class="font-bold text-[#172A3D]">{{ clasificado.vehiculo.marca }}</p>
                                    </div>
                                    <div v-if="clasificado.vehiculo.modelo" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Modelo</p>
                                        <p class="font-bold text-[#172A3D]">{{ clasificado.vehiculo.modelo }}</p>
                                    </div>
                                    <div v-if="clasificado.vehiculo.anio" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Año</p>
                                        <p class="font-bold text-[#172A3D]">{{ clasificado.vehiculo.anio }}</p>
                                    </div>
                                    <div v-if="clasificado.vehiculo.kilometraje !== null"
                                        class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Kilometraje</p>
                                        <p class="font-bold text-[#172A3D]">{{
                                            Number(clasificado.vehiculo.kilometraje).toLocaleString('es-CO') }} km</p>
                                    </div>
                                    <div v-if="clasificado.vehiculo.cilindraje" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Cilindraje</p>
                                        <p class="font-bold text-[#172A3D]">{{ clasificado.vehiculo.cilindraje }} cc</p>
                                    </div>
                                    <div v-if="clasificado.vehiculo.combustible" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Combustible</p>
                                        <p class="font-bold text-[#172A3D] capitalize">{{
                                            clasificado.vehiculo.combustible }}</p>
                                    </div>
                                    <div v-if="clasificado.vehiculo.transmision" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Transmisión</p>
                                        <p class="font-bold text-[#172A3D] capitalize">{{
                                            clasificado.vehiculo.transmision }}</p>
                                    </div>
                                    <div v-if="clasificado.vehiculo.color" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Color</p>
                                        <p class="font-bold text-[#172A3D]">{{ clasificado.vehiculo.color }}</p>
                                    </div>
                                    <div v-if="clasificado.vehiculo.placa" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Placa</p>
                                        <p class="font-bold text-[#172A3D] uppercase">{{ clasificado.vehiculo.placa }}
                                        </p>
                                    </div>
                                </div>

                                <div v-if="clasificado.vehiculo.observaciones"
                                    class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-4">
                                    <p class="text-xs font-bold text-slate-700 uppercase mb-1">Observaciones</p>
                                    <p class="text-xs sm:text-sm text-slate-600 font-medium">{{
                                        clasificado.vehiculo.observaciones }}</p>
                                </div>
                            </section>

                            <!-- FICHA TÉCNICA: PROPIEDAD -->
                            <section v-if="esPropiedad"
                                class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15 space-y-6">
                                <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">🏠 Características del
                                    inmueble</h2>

                                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                                    <div v-if="prop.area_total" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Área total</p>
                                        <p class="font-black text-[#172A3D]">{{ prop.area_total }} m²</p>
                                    </div>
                                    <div v-if="prop.area_construida" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Construida</p>
                                        <p class="font-black text-[#172A3D]">{{ prop.area_construida }} m²</p>
                                    </div>
                                    <div v-if="prop.habitaciones" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Habitaciones</p>
                                        <p class="font-black text-[#172A3D]">{{ prop.habitaciones }}</p>
                                    </div>
                                    <div v-if="prop.banos" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Baños</p>
                                        <p class="font-black text-[#172A3D]">{{ prop.banos }}</p>
                                    </div>
                                    <div v-if="prop.garajes" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Garajes</p>
                                        <p class="font-black text-[#172A3D]">{{ prop.garajes }}</p>
                                    </div>
                                    <div v-if="prop.estrato" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Estrato</p>
                                        <p class="font-black text-[#172A3D]">{{ prop.estrato }}</p>
                                    </div>
                                    <div v-if="prop.piso" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Piso</p>
                                        <p class="font-black text-[#172A3D]">{{ prop.piso }}</p>
                                    </div>
                                    <div v-if="prop.antiguedad" class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] font-bold text-slate-500 uppercase">Antigüedad</p>
                                        <p class="font-black text-[#172A3D]">{{ prop.antiguedad }}</p>
                                    </div>
                                </div>

                                <!-- SERVICIOS BÁSICOS Y PAPELES -->
                                <div v-if="serviciosActivos.length" class="border-t border-slate-100 pt-4">
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">💡
                                        Servicios y documentación</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="item in serviciosActivos" :key="item.label"
                                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700">
                                            <span>{{ item.icon }}</span> {{ item.label }}
                                        </span>
                                    </div>
                                </div>

                                <!-- ZONAS COMUNES Y AMENIDADES -->
                                <div v-if="zonasComunesActivas.length" class="border-t border-slate-100 pt-4">
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">🏊‍♂️
                                        Zonas comunes y amenidades</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="item in zonasComunesActivas" :key="item.label"
                                            class="inline-flex items-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50/60 px-3 py-1.5 text-xs font-semibold text-amber-950">
                                            <span>{{ item.icon }}</span> {{ item.label }}
                                        </span>
                                    </div>
                                </div>

                                <!-- COMODIDADES INTERIORES Y SEGURIDAD -->
                                <div v-if="comodidadesInterioresActivas.length" class="border-t border-slate-100 pt-4">
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">🛋️
                                        Interiores y seguridad</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="item in comodidadesInterioresActivas" :key="item.label"
                                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700">
                                            <span>{{ item.icon }}</span> {{ item.label }}
                                        </span>
                                    </div>
                                </div>
                            </section>

                            <!-- DESCRIPCIÓN -->
                            <section
                                class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15">
                                <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">Descripción detallada</h2>
                                <div
                                    class="mt-3 whitespace-pre-line text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                    {{ clasificado.descripcion }}
                                </div>
                            </section>

                        </div>

                        <!-- COLUMNA LATERAL (PROPIETARIO / DENUNCIAS) -->
                        <aside class="space-y-6">
                            <section
                                class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15">
                                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">Publicado
                                    por</p>
                                <div class="mt-4 flex items-center gap-3">
                                    <div
                                        class="flex h-12 w-12 flex-shrink-0 items-center justify-center overflow-hidden rounded-full bg-amber-100 border border-amber-200 text-base font-bold text-amber-800">
                                        <img v-if="fotoPropietario" :src="fotoPropietario" :alt="nombrePropietario"
                                            class="h-full w-full object-cover" />
                                        <span v-else>{{ nombrePropietario.charAt(0).toUpperCase() }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-xs sm:text-sm font-black text-[#172A3D]">{{
                                            nombrePropietario }}</p>
                                        <p class="text-xs text-slate-500 font-medium">Miembro de Portal Deseret</p>
                                    </div>
                                </div>
                            </section>

                            <!-- REPORTE DE DENUNCIA -->
                            <div v-if="!esPropietario && puedeDenunciar"
                                class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-amber-500/15 sm:p-6">
                                <Link
                                    :href="route('denuncias.create', { modulo: 'clasificados', reportable_type: 'App\\Models\\Clasificado', reportable_id: clasificado.id })"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs sm:text-sm font-bold text-red-700 transition hover:bg-red-100 hover:text-red-900 shadow-sm">
                                    🚨 Reportar este clasificado
                                </Link>
                            </div>
                        </aside>
                    </div>

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