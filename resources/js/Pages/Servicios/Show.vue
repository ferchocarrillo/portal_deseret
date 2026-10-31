<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { route } from 'ziggy-js'
import Swal from 'sweetalert2'

// ==========================================================
// PROPS
// ==========================================================

const props = defineProps({
    servicio: {
        type: Object,
        required: true,
    },

    contactosUsuario: {
        type: Array,
        default: () => [],
    },

    resenasPublicas: {
        type: Array,
        default: () => [],
    },

    resumenCalificaciones: {
        type: Object,
        default: () => ({}),
    },
})

// ==========================================================
// USUARIO AUTENTICADO
// ==========================================================

const page = usePage()
const usuarioActual = computed(() => page.props.auth?.user ?? null)

const esPropietario = computed(() => {
    if (!usuarioActual.value || !props.servicio.user_id) {
        return false
    }
    return Number(usuarioActual.value.id) === Number(props.servicio.user_id)
})

// ==========================================================
// PROVEEDOR
// ==========================================================

const nombreProveedor = computed(() => {
    const usuario = props.servicio.user
    if (!usuario) {
        return 'Proveedor de servicios'
    }

    const nombreCompleto = [usuario.nombres, usuario.apellidos]
        .filter(Boolean)
        .join(' ')
        .trim()

    return nombreCompleto || usuario.name || 'Proveedor de servicios'
})

const fotoProveedor = computed(() => {
    const foto = props.servicio.user?.foto
    if (!foto) return null

    if (foto.startsWith('http://') || foto.startsWith('https://')) {
        return foto
    }
    if (foto.startsWith('/')) {
        return foto
    }
    return `/storage/${foto}`
})

// ==========================================================
// IMAGEN DEL SERVICIO
// ==========================================================

const imagenServicio = computed(() => {
    const servicio = props.servicio

    if (servicio.imagen_tipo === 'archivo' && servicio.imagen) {
        if (servicio.imagen.startsWith('http://') || servicio.imagen.startsWith('https://')) {
            return servicio.imagen
        }
        if (servicio.imagen.startsWith('/')) {
            return servicio.imagen
        }
        return `/storage/${servicio.imagen}`
    }

    return fotoProveedor.value
})

// ==========================================================
// PRECIO
// ==========================================================

const precioTexto = computed(() => {
    const servicio = props.servicio

    if (
        servicio.precio_tipo === 'a_convenir' ||
        servicio.precio === null ||
        servicio.precio === undefined ||
        servicio.precio === ''
    ) {
        return 'A convenir'
    }

    const precio = Number(servicio.precio)
    if (!Number.isFinite(precio)) {
        return 'A convenir'
    }

    const moneda = servicio.moneda || 'COP'
    const valor = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: moneda,
        maximumFractionDigits: 2,
    }).format(precio)

    const sufijos = {
        hora: ' por hora',
        dia: ' por día',
        servicio: ' por servicio',
        proyecto: ' por proyecto',
        fijo: '',
    }

    return `${valor}${sufijos[servicio.precio_tipo] ?? ''}`
})

// ==========================================================
// MODALIDAD Y UBICACIÓN
// ==========================================================

const modalidadTexto = computed(() => {
    const modalidades = {
        presencial: 'Presencial',
        remoto: 'Remoto',
        hibrido: 'Híbrido',
    }
    return modalidades[props.servicio.modalidad] ?? props.servicio.modalidad ?? 'No especificada'
})

const ubicacionTexto = computed(() => {
    return [props.servicio.ciudad, props.servicio.departamento, props.servicio.pais]
        .filter(Boolean)
        .join(', ')
})

const fechaPublicacion = computed(() => {
    if (!props.servicio.created_at) return null
    const fecha = new Date(props.servicio.created_at)
    if (Number.isNaN(fecha.getTime())) return null

    return new Intl.DateTimeFormat('es-CO', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(fecha)
})

const servicioActivo = computed(() => props.servicio.estado === 'activo')

// ==========================================================
// FAVORITO (UNIFICADO Y OPTIMISTA)
// ==========================================================

const esFavorito = ref(Boolean(props.servicio?.es_favorito))
const procesandoFavorito = ref(false)

watch(
    () => props.servicio?.es_favorito,
    (nuevoValor) => {
        esFavorito.value = Boolean(nuevoValor)
    }
)

const toggleFavorito = () => {
    if (procesandoFavorito.value) {
        return
    }

    procesandoFavorito.value = true

    const nuevoEstado = !esFavorito.value
    esFavorito.value = nuevoEstado
    if (props.servicio) {
        props.servicio.es_favorito = nuevoEstado
    }

    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: props.servicio.id,
            modulo: 'servicios',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onError: (errores) => {
                console.error('Error al alternar favorito:', errores)
                esFavorito.value = !nuevoEstado
                if (props.servicio) {
                    props.servicio.es_favorito = esFavorito.value
                }
            },
            onFinish: () => {
                procesandoFavorito.value = false
            },
        }
    )
}

// ==========================================================
// ACCIONES PROPIETARIO (CON SWEETALERT2)
// ==========================================================

const estadoForm = useForm({})
const eliminarForm = useForm({})

function cambiarEstado() {
    if (!esPropietario.value) return

    const esActivo = servicioActivo.value

    Swal.fire({
        title: esActivo ? '¿Pausar publicación?' : '¿Reactivar publicación?',
        text: esActivo
            ? 'Dejará de aparecer temporalmente en el catálogo de servicios.'
            : 'Volverá a estar visible para toda la comunidad.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#059669', // Esmeralda / Verde institucional
        cancelButtonColor: '#64748b',
        confirmButtonText: esActivo ? 'Sí, pausar' : 'Sí, reactivar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            estadoForm.patch(route('servicios.estado', props.servicio.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: '¡Actualizado!',
                        text: esActivo ? 'El servicio ha sido pausado.' : 'El servicio ha sido reactivado.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                    })
                },
            })
        }
    })
}

function eliminarServicio() {
    if (!esPropietario.value) return

    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción eliminará permanentemente este servicio y no se puede deshacer.',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc2626', // Rojo alerta
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    }).then((result) => {
        if (result.isConfirmed) {
            eliminarForm.delete(route('servicios.destroy', props.servicio.id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: 'El servicio ha sido eliminado exitosamente.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                    })
                },
            })
        }
    })
}

// ==========================================================
// MENSAJERÍA / CHAT INTERNO
// ==========================================================

const mensajeProcesando = ref(false)
const errorMensaje = ref('')

const textoBotonContacto = computed(() => {
    return mensajeProcesando.value ? 'Iniciando conversación...' : 'Enviar mensaje al proveedor'
})

function abrirConversacion() {
    if (mensajeProcesando.value || esPropietario.value) return

    mensajeProcesando.value = true
    errorMensaje.value = ''

    router.post(
        route('servicios.conversacion.store', props.servicio.id),
        {},
        {
            preserveScroll: true,
            onError: (errors) => {
                errorMensaje.value =
                    errors?.servicio ??
                    errors?.message ??
                    'No fue posible abrir la conversación con el proveedor.'
            },
            onFinish: () => {
                mensajeProcesando.value = false
            },
        }
    )
}

// ==========================================================
// RESEÑAS Y CALIFICACIONES
// ==========================================================

const contactoActual = ref(
    props.contactosUsuario.length ? props.contactosUsuario[0].id : null
)

const contactoActualData = computed(() => {
    return (
        props.contactosUsuario.find(
            (contacto) => Number(contacto.id) === Number(contactoActual.value)
        ) ?? null
    )
})

const resenasContacto = computed(() => contactoActualData.value?.resenas ?? [])

const yaResenoAtencion = computed(() => {
    return resenasContacto.value.some((resena) => resena.tipo === 'atencion')
})

const yaResenoServicio = computed(() => {
    return resenasContacto.value.some((resena) => resena.tipo === 'servicio')
})

const resenaForm = useForm({
    contacto_servicio_id: null,
    tipo: 'atencion',
    calificacion: null,
    comentario: '',
    recibio_servicio: false,
})

function etiquetaTipoResena(tipo) {
    return tipo === 'atencion' ? 'Atención del proveedor' : 'Servicio recibido'
}

function enviarResena() {
    if (!contactoActual.value || resenaForm.processing || !resenaForm.calificacion) return

    if (resenaForm.tipo === 'atencion' && yaResenoAtencion.value) return
    if (resenaForm.tipo === 'servicio' && yaResenoServicio.value) return

    resenaForm.contacto_servicio_id = contactoActual.value

    resenaForm.post(route('servicios.resenas.store', props.servicio.id), {
        preserveScroll: true,
        onSuccess: () => {
            resenaForm.reset()
            resenaForm.tipo = 'atencion'
            resenaForm.calificacion = null
            resenaForm.recibio_servicio = false
        },
    })
}

// ==========================================================
// RESPUESTA DEL PROPIETARIO A RESEÑAS
// ==========================================================

const respuestaForm = useForm({
    respuesta_propietario: '',
})

const resenaEnRespuesta = ref(null)

function iniciarRespuesta(resena) {
    if (!esPropietario.value || resena.respuesta_propietario) return
    resenaEnRespuesta.value = resena.id
    respuestaForm.reset()
    respuestaForm.clearErrors()
}

function cancelarRespuesta() {
    resenaEnRespuesta.value = null
    respuestaForm.reset()
    respuestaForm.clearErrors()
}

function enviarRespuesta(resena) {
    if (!esPropietario.value || respuestaForm.processing || resena.respuesta_propietario) return

    respuestaForm.post(
        route('servicios.resenas.responder', {
            servicio: props.servicio.id,
            resena: resena.id,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                respuestaForm.reset()
                resenaEnRespuesta.value = null
            },
        }
    )
}

function formatearAntiguedad(fechaCreacion) {
    if (!fechaCreacion) return ''
    const fecha = new Date(fechaCreacion)
    if (Number.isNaN(fecha.getTime())) return ''

    const ahora = new Date()
    const diferenciaSegundos = Math.floor((ahora.getTime() - fecha.getTime()) / 1000)

    if (diferenciaSegundos < 60) return 'Hace unos segundos'

    const formatoRelativo = new Intl.RelativeTimeFormat('es-CO', { numeric: 'always' })
    const minutos = Math.floor(diferenciaSegundos / 60)
    if (minutos < 60) return formatoRelativo.format(-minutos, 'minute')

    const horas = Math.floor(minutos / 60)
    if (horas < 24) return formatoRelativo.format(-horas, 'hour')

    const dias = Math.floor(horas / 24)
    if (dias < 7) return formatoRelativo.format(-dias, 'day')

    const semanas = Math.floor(dias / 7)
    if (dias < 30) return formatoRelativo.format(-semanas, 'week')

    const meses = Math.floor(dias / 30)
    if (dias < 365) return formatoRelativo.format(-meses, 'month')

    const años = Math.floor(dias / 365)
    return formatoRelativo.format(-años, 'year')
}

// ==========================================================
// CALIFICACIÓN GLOBAL PONDERADA
// ==========================================================
const calificacionGlobal = computed(() => {
    const totalAtencion = Number(props.resumenCalificaciones.atencion?.total || 0)
    const promAtencion = Number(props.resumenCalificaciones.atencion?.promedio || 0)

    const totalServicio = Number(props.resumenCalificaciones.servicio?.total || 0)
    const promServicio = Number(props.resumenCalificaciones.servicio?.promedio || 0)

    const totalOpiniones = totalAtencion + totalServicio

    if (totalOpiniones === 0) {
        return {
            promedio: null,
            total: 0,
        }
    }

    const sumaPuntos = (promAtencion * totalAtencion) + (promServicio * totalServicio)
    const promedioPonderado = sumaPuntos / totalOpiniones

    return {
        promedio: promedioPonderado.toFixed(1),
        total: totalOpiniones,
    }
})
</script>

<template>

    <Head :title="`${servicio.titulo} | Servicios`" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">

        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- =====================================================
                 ENCABEZADO CON TONO VERDE / ESMERALDA TRANSLÚCIDO
            ================================================ ==> -->
            <section
                class="border-b border-emerald-200/80 bg-emerald-100/25 backdrop-blur-md shadow-lg shadow-emerald-500/10">
                <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <Link :href="route('servicios.index')" :preserve-scroll="false" :preserve-state="false"
                            class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6" />
                            </svg>
                            Volver al catálogo
                        </Link>

                        <div class="flex items-center gap-3">
                            <!-- BOTÓN DE FAVORITO UNIFICADO -->
                            <button type="button" :disabled="procesandoFavorito" @click="toggleFavorito"
                                class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-1.5 text-xs font-bold text-slate-700 shadow-xs transition hover:bg-slate-50 disabled:opacity-60">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor" class="h-4 w-4 transition-colors"
                                    :class="esFavorito ? 'fill-red-600 text-red-600' : 'fill-none text-slate-400'">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>
                                <span>{{ esFavorito ? 'Guardado en favoritos' : 'Agregar a favoritos' }}</span>
                            </button>

                            <span
                                class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-bold shadow-xs"
                                :class="servicioActivo ? 'bg-emerald-100 text-emerald-900 border border-emerald-200' : 'bg-amber-100 text-amber-900 border border-amber-200'">
                                <span class="h-2 w-2 rounded-full"
                                    :class="servicioActivo ? 'bg-emerald-600' : 'bg-amber-500'"></span>
                                {{ servicioActivo ? 'Publicación activa' : 'Publicación pausada' }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =====================================================
                 CONTENIDO PRINCIPAL
            ====================================================== -->
            <div class="py-8">
                <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 bg-emerald-50 rounded-xl p-3 mt-2">
                    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">

                        <!-- DETALLES (COLUMNA IZQUIERDA) -->
                        <div class="space-y-6 lg:col-span-2">
                            <section
                                class="overflow-hidden rounded-2xl border border-slate-300 bg-white shadow-2xl shadow-emerald-500/15">
                                <div v-if="imagenServicio"
                                    class="flex max-h-[520px] items-center justify-center overflow-hidden bg-slate-100">
                                    <img :src="imagenServicio" :alt="servicio.titulo"
                                        class="max-h-[520px] w-full object-contain" />
                                </div>

                                <div v-else
                                    class="flex h-64 items-center justify-center bg-gradient-to-br from-emerald-100 via-teal-50 to-cyan-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-emerald-700/40"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2 3 7l9 5 9-5-9-5Z" />
                                        <path d="m3 12 9 5 9-5" />
                                        <path d="m3 17 9 5 9-5" />
                                    </svg>
                                </div>

                                <div class="p-5 sm:p-8">
                                    <div class="mb-4 flex flex-wrap gap-2">
                                        <span
                                            class="rounded-full bg-emerald-50 border border-emerald-200 px-3.5 py-1 text-xs font-bold text-emerald-900">
                                            {{ servicio.categoria }}
                                        </span>
                                        <span
                                            class="rounded-full bg-slate-100 border border-slate-200 px-3.5 py-1 text-xs font-bold text-slate-700">
                                            {{ modalidadTexto }}
                                        </span>
                                    </div>

                                    <h1
                                        class="break-words text-2xl font-black leading-tight text-[#172A3D] sm:text-3xl">
                                        {{ servicio.titulo }}
                                    </h1>

                                    <div
                                        class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-3 text-xs sm:text-sm text-slate-600 font-medium">
                                        <span v-if="ubicacionTexto" class="inline-flex items-center gap-1.5">
                                            📍 <span>{{ ubicacionTexto }}</span>
                                        </span>

                                        <span v-if="fechaPublicacion" class="inline-flex items-center gap-1.5">
                                            📅 Publicado el {{ fechaPublicacion }}
                                        </span>
                                    </div>

                                    <div class="my-6 border-t border-slate-200"></div>

                                    <!-- PRECIO -->
                                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5 shadow-xs">
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-900">
                                            Precio del servicio
                                        </p>
                                        <p class="mt-1 break-words text-2xl font-black text-emerald-950 sm:text-3xl">
                                            {{ precioTexto }}
                                        </p>
                                        <p v-if="servicio.precio_tipo === 'a_convenir'"
                                            class="mt-1.5 text-xs sm:text-sm text-emerald-800 font-medium">
                                            Inicia una conversación por el chat para acordar el valor y las condiciones
                                            con el proveedor.
                                        </p>
                                    </div>
                                </div>
                            </section>

                            <!-- DESCRIPCIÓN -->
                            <section
                                class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-emerald-500/15 sm:p-8">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 font-bold">
                                        📝
                                    </div>
                                    <h2 class="text-lg sm:text-xl font-black text-[#172A3D]">
                                        Acerca de este servicio
                                    </h2>
                                </div>

                                <div
                                    class="mt-4 whitespace-pre-line break-words text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                    {{ servicio.descripcion }}
                                </div>
                            </section>

                            <!-- UBICACIÓN Y MODALIDAD -->
                            <section v-if="ubicacionTexto || servicio.modalidad"
                                class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-emerald-500/15 sm:p-8">
                                <h2 class="text-lg sm:text-xl font-black text-[#172A3D]">
                                    Ubicación y cobertura
                                </h2>

                                <div class="mt-5 space-y-4">
                                    <div v-if="ubicacionTexto" class="flex items-start gap-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 font-bold">
                                            📍
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm font-bold text-[#172A3D]">
                                                {{ ubicacionTexto }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                                Ubicación registrada por el proveedor.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700 font-bold">
                                            🌐
                                        </div>
                                        <div>
                                            <p class="text-xs sm:text-sm font-bold text-[#172A3D]">
                                                Modalidad {{ modalidadTexto.toLowerCase() }}
                                            </p>
                                            <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                                Coordina los detalles de atención y disponibilidad a través del chat
                                                interno.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- LATERAL (CONTACTO Y RESEÑAS) -->
                        <aside class="space-y-6">

                            <!-- CAJA DE CONTACTO / CHAT -->
                            <section
                                class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-emerald-500/15 sm:p-6">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 font-bold">
                                        💬
                                    </div>

                                    <div>
                                        <h2 class="text-base sm:text-lg font-black text-[#172A3D]">
                                            Contactar al proveedor
                                        </h2>
                                        <p class="text-xs text-slate-500 font-medium">
                                            Chat directo en la plataforma
                                        </p>
                                    </div>
                                </div>

                                <p class="mt-4 text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                    Inicia una conversación segura para consultar disponibilidad, resolver dudas y
                                    acordar los términos del servicio.
                                </p>

                                <div class="mt-5 space-y-3">
                                    <button v-if="!esPropietario" type="button"
                                        :disabled="mensajeProcesando || !servicioActivo" @click="abrirConversacion"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-3.5 text-xs sm:text-sm font-black text-white shadow-md transition hover:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-50">
                                        <span v-if="!mensajeProcesando">💬</span>
                                        <span>{{ textoBotonContacto }}</span>
                                    </button>

                                    <p v-if="errorMensaje"
                                        class="rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-700 font-medium">
                                        {{ errorMensaje }}
                                    </p>
                                </div>

                                <p
                                    class="mt-5 border-t border-slate-200 pt-4 text-[11px] leading-relaxed text-slate-400 font-medium">
                                    La mensajería es gestionada internamente en Portal Deseret para proteger la
                                    privacidad de los miembros.
                                </p>
                            </section>

                          <!-- ================================================= -->
                            <!-- BOTÓN DE DENUNCIA / REPORTE DE INCIDENTE -->
                            <!-- ================================================= -->
                            <div
                                class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-emerald-500/15 sm:p-6">
                                <Link
                                    :href="route('denuncias.create', { modulo: 'servicios', reportable_type: 'App\\Models\\Servicio', reportable_id: servicio.id })"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs sm:text-sm font-bold text-red-700 transition hover:bg-red-100 hover:text-red-900 shadow-sm">
                                    🚨 Reportar este servicio
                                </Link>
                            </div>

                            <!-- RESEÑAS PÚBLICAS Y CALIFICACIONES -->
                            <section
                                class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-emerald-500/15 sm:p-6 space-y-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <h2 class="text-sm font-black text-[#172A3D]">
                                            Calificaciones y reseñas
                                        </h2>
                                        <p class="text-xs text-slate-500 font-medium">
                                            Opiniones de la comunidad
                                        </p>
                                    </div>

                                    <div v-if="calificacionGlobal.total > 0"
                                        class="flex items-center gap-1.5 rounded-xl border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-slate-900 shadow-xs">
                                        <span class="text-sm leading-none text-amber-500">★</span>
                                        <span class="text-sm font-black text-[#172A3D]">{{ calificacionGlobal.promedio
                                        }}</span>
                                        <span class="text-[11px] font-medium text-slate-500">({{
                                            calificacionGlobal.total }})</span>
                                    </div>

                                    <div v-else
                                        class="flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-400">
                                        <span>☆</span>
                                        <span>Sin calificar</span>
                                    </div>
                                </div>

                                <!-- RESUMEN -->
                                <div class="space-y-2 rounded-xl bg-slate-50 border border-slate-200 px-3.5 py-3">
                                    <div class="flex items-center justify-between gap-2 text-xs font-medium">
                                        <span class="text-slate-600">Atención del proveedor</span>
                                        <span class="shrink-0 font-bold text-slate-700">
                                            <template v-if="Number(resumenCalificaciones.atencion?.total) > 0">
                                                {{ Number(resumenCalificaciones.atencion.promedio).toFixed(1) }}
                                                <span class="text-amber-500">★</span>
                                                <span class="text-slate-400">({{
                                                    resumenCalificaciones.atencion.total
                                                }})</span>
                                            </template>
                                            <template v-else>
                                                <span class="text-slate-400">Sin calificar (0)</span>
                                            </template>
                                        </span>
                                    </div>

                                    <div class="border-t border-slate-200"></div>

                                    <div class="flex items-center justify-between gap-2 text-xs font-medium">
                                        <span class="text-slate-600">Servicio recibido</span>
                                        <span class="shrink-0 font-bold text-slate-700">
                                            <template v-if="Number(resumenCalificaciones.servicio?.total) > 0">
                                                {{ Number(resumenCalificaciones.servicio.promedio).toFixed(1) }}
                                                <span class="text-amber-500">★</span>
                                                <span class="text-slate-400">({{ resumenCalificaciones.servicio.total
                                                }})</span>
                                            </template>
                                            <template v-else>
                                                <span class="text-slate-400">Sin calificar (0)</span>
                                            </template>
                                        </span>
                                    </div>
                                </div>

                                <!-- LISTA DE RESEÑAS -->
                                <div v-if="resenasPublicas.length" class="max-h-[220px] space-y-3 overflow-y-auto pr-1">
                                    <article v-for="resena in resenasPublicas" :key="resena.id"
                                        class="border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-xs font-bold text-slate-700">
                                                {{ etiquetaTipoResena(resena.tipo) }}
                                            </span>
                                            <span class="shrink-0 whitespace-nowrap text-xs text-amber-500 font-bold">
                                                <span v-for="estrella in 5" :key="estrella">
                                                    {{ estrella <= resena.calificacion ? '★' : '☆' }} </span>
                                                </span>
                                        </div>

                                        <div
                                            class="mt-1 flex flex-wrap items-center gap-x-2 text-[11px] text-slate-400 font-medium">
                                            <span>{{ resena.resenador_nombre || 'Usuario' }}</span>
                                            <span v-if="resena.created_at">·</span>
                                            <span v-if="resena.created_at">
                                                Calificó {{ formatearAntiguedad(resena.created_at) }}
                                            </span>
                                        </div>

                                        <p v-if="resena.comentario?.trim()"
                                            class="mt-1.5 break-words text-xs leading-relaxed text-slate-600 font-medium">
                                            {{ resena.comentario }}
                                        </p>

                                        <!-- RESPUESTA PROPIETARIO -->
                                        <div v-if="resena.respuesta_propietario"
                                            class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50/70 p-3">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="text-xs font-bold text-emerald-900">
                                                    Respuesta del proveedor
                                                </p>
                                                <span v-if="resena.respuesta_at"
                                                    class="shrink-0 text-[10px] text-emerald-700 font-medium">
                                                    {{ formatearAntiguedad(resena.respuesta_at) }}
                                                </span>
                                            </div>
                                            <p
                                                class="mt-1 whitespace-pre-line break-words text-xs leading-relaxed text-emerald-950 font-medium">
                                                {{ resena.respuesta_propietario }}
                                            </p>
                                        </div>

                                        <!-- BOTÓN RESPONDER (PROPIETARIO) -->
                                        <div v-else-if="esPropietario" class="mt-2">
                                            <button v-if="resenaEnRespuesta !== resena.id" type="button"
                                                @click="iniciarRespuesta(resena)"
                                                class="text-xs font-bold text-emerald-700 transition hover:text-emerald-900">
                                                Responder reseña
                                            </button>

                                            <form v-else class="mt-2 space-y-2"
                                                @submit.prevent="enviarRespuesta(resena)">
                                                <textarea v-model="respuestaForm.respuesta_propietario" rows="3"
                                                    maxlength="2000" required
                                                    placeholder="Escribe una respuesta respetuosa..."
                                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs leading-relaxed text-slate-700 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100"></textarea>

                                                <div class="flex gap-2">
                                                    <button type="button" @click="cancelarRespuesta"
                                                        class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                                                        Cancelar
                                                    </button>
                                                    <button type="submit"
                                                        :disabled="respuestaForm.processing || !respuestaForm.respuesta_propietario.trim()"
                                                        class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-800 disabled:opacity-50 transition">
                                                        Publicar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </article>
                                </div>

                                <div v-else
                                    class="rounded-xl border border-dashed border-slate-200 px-3 py-4 text-center">
                                    <p class="text-xs font-bold text-slate-500">Aún no hay reseñas</p>
                                    <p class="mt-0.5 text-[11px] text-slate-400 font-medium">Las opiniones aparecerán
                                        y tras contactar al proveedor.</p>
                                </div>
                            </section>

                            <!-- CALIFICAR EXPERIENCIA -->
                            <section v-if="!esPropietario && usuarioActual"
                                class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-emerald-500/15 sm:p-6 space-y-4">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 font-bold">
                                        ⭐
                                    </div>
                                    <div>
                                        <h2 class="text-base font-black text-[#172A3D]">
                                            Calificar experiencia
                                        </h2>
                                        <p class="text-xs text-slate-500 font-medium">
                                            Comparte tu experiencia con este proveedor.
                                        </p>
                                    </div>
                                </div>

                                <div v-if="!contactoActual"
                                    class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-xs">
                                    <p class="text-xs sm:text-sm font-bold text-amber-900">
                                        Inicia una conversación primero
                                    </p>
                                    <p class="mt-1 text-xs leading-relaxed text-amber-800 font-medium">
                                        Una vez que envíes un mensaje al proveedor a través de la plataforma, podrás
                                        calificar su atención y
                                        servicio.
                                    </p>
                                </div>

                                <form v-else class="space-y-4" @submit.prevent="enviarResena">
                                    <div>
                                        <label for="tipo-resena"
                                            class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            ¿Qué deseas calificar?
                                        </label>
                                        <select id="tipo-resena" v-model="resenaForm.tipo"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                                            <option value="atencion">Atención del proveedor</option>
                                            <option value="servicio">Servicio recibido</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label
                                            class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Tu calificación
                                        </label>
                                        <div class="flex flex-wrap items-center gap-1">
                                            <button v-for="estrella in 5" :key="estrella" type="button"
                                                @click="resenaForm.calificacion = estrella"
                                                class="rounded-md p-1 text-2xl transition hover:scale-110 focus:outline-none"
                                                :class="resenaForm.calificacion && estrella <= Number(resenaForm.calificacion) ? 'text-amber-400' : 'text-slate-300'">
                                                ★
                                            </button>
                                            <span class="ml-2 text-xs font-bold text-slate-700">
                                                {{ resenaForm.calificacion ? `${resenaForm.calificacion} de 5` :
                                                    'Selecciona una puntuación' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div v-if="resenaForm.tipo === 'servicio'"
                                        class="rounded-xl border border-slate-200 bg-slate-50 p-3.5">
                                        <label class="flex cursor-pointer items-start gap-3">
                                            <input v-model="resenaForm.recibio_servicio" type="checkbox"
                                                class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600" />
                                            <span class="text-xs leading-relaxed text-slate-700 font-medium">
                                                Confirmo que recibí el servicio de este proveedor.
                                            </span>
                                        </label>
                                    </div>

                                    <div>
                                        <label for="comentario-resena"
                                            class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Comentario <span class="font-normal text-slate-400">(opcional)</span>
                                        </label>
                                        <textarea id="comentario-resena" v-model="resenaForm.comentario" rows="3"
                                            maxlength="2000" placeholder="Escribe tu opinión..."
                                            class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm text-[#172A3D] focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100"></textarea>
                                    </div>

                                    <button type="submit"
                                        :disabled="resenaForm.processing || !resenaForm.calificacion || (resenaForm.tipo === 'servicio' && !resenaForm.recibio_servicio)"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-3 text-xs sm:text-sm font-black text-white transition hover:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-50">
                                        {{ resenaForm.processing ? 'Guardando...' : 'Enviar calificación' }}
                                    </button>
                                </form>
                            </section>

                            <!-- PERFIL PROVEEDOR -->
                            <section
                                class="rounded-2xl border border-slate-300 bg-white p-5 shadow-2xl shadow-emerald-500/15 sm:p-6">
                                <h2 class="text-base sm:text-lg font-black text-[#172A3D]">
                                    Publicado por
                                </h2>
                                <div class="mt-4 flex items-center gap-3">
                                    <div
                                        class="h-12 w-12 shrink-0 overflow-hidden rounded-full border border-slate-200 bg-slate-100">
                                        <img v-if="fotoProveedor" :src="fotoProveedor" :alt="nombreProveedor"
                                            class="h-full w-full object-cover" />
                                        <div v-else
                                            class="flex h-full w-full items-center justify-center text-lg font-bold text-slate-500">
                                            {{ nombreProveedor.charAt(0).toUpperCase() }}
                                        </div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="break-words font-bold text-[#172A3D]">
                                            {{ nombreProveedor }}
                                        </p>
                                        <p class="mt-0.5 text-xs text-slate-500 font-medium">
                                            Proveedor de servicios
                                        </p>
                                    </div>
                                </div>
                            </section>

                            <!-- ADMINISTRAR (SOLO PROPIETARIO) -->
                            <section v-if="esPropietario"
                                class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-5 sm:p-6 shadow-2xl shadow-emerald-500/15 space-y-3">
                                <h2 class="text-base sm:text-lg font-black text-[#172A3D]">
                                    Administrar publicación
                                </h2>

                                <Link :href="route('servicios.edit', servicio.id)"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 px-4 py-3 text-xs sm:text-sm font-black text-white transition hover:bg-emerald-800 shadow-xs">
                                    Editar servicio
                                </Link>

                                <button type="button" :disabled="estadoForm.processing" @click="cambiarEstado"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 disabled:opacity-50 shadow-xs">
                                    {{ servicioActivo ? 'Pausar publicación' : 'Reactivar publicación' }}
                                </button>

                                <button type="button" :disabled="eliminarForm.processing" @click="eliminarServicio"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-3 text-xs sm:text-sm font-bold text-red-600 transition hover:bg-red-50 disabled:opacity-50 shadow-xs">
                                    Eliminar publicación
                                </button>
                            </section>
                        </aside>
                    </div>

                    <!-- PIE -->
                    <div class="mt-8 border-t border-slate-200 pt-5 text-center bg-white gap-2 rounded-xl border border-slate-300 ">
                        <p class="text-xs leading-relaxed text-slate-400 font-medium">
                            Portal Deseret facilita el contacto entre miembros a través de su chat interno. Las
                            condiciones se acuerdan
                            directamente entre ambas partes.
                        </p>
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