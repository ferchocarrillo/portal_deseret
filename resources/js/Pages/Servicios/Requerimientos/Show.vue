<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { route } from 'ziggy-js'

const props = defineProps({
    requerimiento: {
        type: Object,
        required: true,
    },
    esPropietario: {
        type: Boolean,
        default: false,
    },
})

const page = usePage()
const usuarioActual = computed(() => page.props.auth?.user ?? null)

// ==========================================================
// FAVORITO (UNIFICADO Y OPTIMISTA)
// ==========================================================
const esFavorito = ref(Boolean(props.requerimiento?.es_favorito))
const procesandoFavorito = ref(false)

watch(
    () => props.requerimiento?.es_favorito,
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
    if (props.requerimiento) {
        props.requerimiento.es_favorito = nuevoEstado
    }

    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: props.requerimiento.id,
            modulo: 'servicios_requerimientos',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onError: (errores) => {
                console.error('Error al alternar favorito:', errores)
                esFavorito.value = !nuevoEstado
                if (props.requerimiento) {
                    props.requerimiento.es_favorito = esFavorito.value
                }
            },
            onFinish: () => {
                procesandoFavorito.value = false
            },
        }
    )
}

// Formulario para cambiar el estado (Resolver / Cancelar / Reabrir)
const estadoForm = useForm({
    estado: props.requerimiento.estado,
})

function actualizarEstado(nuevoEstado) {
    let mensaje = '¿Deseas cambiar el estado de esta solicitud?'
    if (nuevoEstado === 'resuelto') {
        mensaje = '¿Deseas marcar esta solicitud como resuelta? Ya no aparecerá como pendiente en la comunidad.'
    } else if (nuevoEstado === 'cancelado') {
        mensaje = '¿Deseas cancelar esta solicitud?'
    } else if (nuevoEstado === 'abierto') {
        mensaje = '¿Deseas volver a abrir esta solicitud para recibir ofertas?'
    }

    if (!window.confirm(mensaje)) return

    estadoForm.estado = nuevoEstado
    estadoForm.patch(route('servicios.requerimientos.estado', props.requerimiento.id), {
        preserveScroll: true,
    })
}

// Nombre del solicitante
const nombreSolicitante = computed(() => {
    const user = props.requerimiento.usuario
    if (!user) return 'Miembro de la comunidad'
    const completo = [user.nombres, user.apellidos].filter(Boolean).join(' ')
    return completo || user.name || 'Miembro de la comunidad'
})

// Foto del solicitante
const fotoSolicitante = computed(() => {
    const foto = props.requerimiento.usuario?.foto
    if (!foto) return null
    if (foto.startsWith('http://') || foto.startsWith('https://') || foto.startsWith('/')) {
        return foto
    }
    return `/storage/${foto}`
})

// Ubicación formateada
const ubicacionTexto = computed(() => {
    return [
        props.requerimiento.ciudad,
        props.requerimiento.departamento,
        props.requerimiento.pais,
    ].filter(Boolean).join(', ') || 'Ubicación no especificada'
})

// Presupuesto formateado
const presupuestoTexto = computed(() => {
    const req = props.requerimiento
    if (req.presupuesto_tipo === 'a_convenir' || (!req.presupuesto_min && !req.presupuesto_max)) {
        return 'Precio / Valor a convenir'
    }

    const formato = (val) => new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: req.moneda || 'COP',
        maximumFractionDigits: 0,
    }).format(Number(val))

    if (req.presupuesto_tipo === 'fijo') {
        return formato(req.presupuesto_min)
    }

    if (req.presupuesto_tipo === 'rango') {
        if (req.presupuesto_min && req.presupuesto_max) {
            return `${formato(req.presupuesto_min)} - ${formato(req.presupuesto_max)}`
        }
        if (req.presupuesto_min) return `Desde ${formato(req.presupuesto_min)}`
        if (req.presupuesto_max) return `Hasta ${formato(req.presupuesto_max)}`
    }

    return 'A convenir'
})

// Fecha de publicación formateada
const fechaPublicacion = computed(() => {
    if (!props.requerimiento.created_at) return ''
    return new Intl.DateTimeFormat('es-CO', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(props.requerimiento.created_at))
})

// Modalidad formateada
const modalidadTexto = computed(() => {
    const modalidades = {
        presencial: 'Presencial',
        remoto: 'Remoto / Virtual',
        hibrido: 'Híbrido',
    }
    return modalidades[props.requerimiento.modalidad] ?? props.requerimiento.modalidad
})

// Contactar al solicitante por chat
function contactarSolicitante() {
    router.visit(route('mensajes.index'), {
        data: {
            destinatario_id: props.requerimiento.user_id,
            asunto: `Requerimiento: ${props.requerimiento.titulo}`,
        },
    })
}
</script>

<template>

    <Head :title="`${requerimiento.titulo} | Servicios`" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">

        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- =====================================================
                 ENCABEZADO CON TONO VERDE OSCURO / ESMERALDA PROFUNDO TRANSLÚCIDO
            ====================================================== -->
            <section
                class="border-b border-emerald-900/30 bg-emerald-950/15 backdrop-blur-md shadow-lg shadow-emerald-950/10">
                <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <Link :href="route('servicios.index')" :preserve-scroll="false" :preserve-state="false"
                            class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-emerald-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6" />
                            </svg>
                            Volver al catálogo de servicios
                        </Link>

                        <div class="flex flex-wrap items-center gap-2.5">
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

                            <span class="rounded-full px-3 py-1 text-xs font-bold capitalize shadow-xs" :class="requerimiento.estado === 'abierto'
                                ? 'bg-emerald-100 text-emerald-900 border border-emerald-200'
                                : requerimiento.estado === 'resuelto'
                                    ? 'bg-blue-100 text-blue-900 border border-blue-200'
                                    : 'bg-slate-100 text-slate-700 border border-slate-200'">
                                Estado: {{ requerimiento.estado }}
                            </span>

                            <span class="rounded-full px-3 py-1 text-xs font-bold shadow-xs" :class="requerimiento.urgencia === 'urgente'
                                ? 'bg-rose-100 text-rose-900 border border-rose-200'
                                : requerimiento.urgencia === 'alta'
                                    ? 'bg-amber-100 text-amber-900 border border-amber-200'
                                    : 'bg-slate-100 text-slate-700 border border-slate-200'">
                                {{ requerimiento.urgencia === 'urgente' ? '🔥 Muy Urgente' : `Urgencia:
                                ${requerimiento.urgencia}` }}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =====================================================
                 CONTENIDO PRINCIPAL
            ====================================================== -->
            <div class="py-8">
                <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 bg-emerald-50 rounded-xl p-3">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                        <!-- COLUMNA PRINCIPAL (DETALLES) -->
                        <div class="space-y-6 lg:col-span-2">
                            <section
                                class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-emerald-950/15 sm:p-8">
                                <div class="mb-4 flex flex-wrap gap-2">
                                    <span v-if="requerimiento.categoria"
                                        class="rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-bold text-emerald-900">
                                        {{ requerimiento.categoria }}
                                    </span>
                                    <span
                                        class="rounded-full bg-slate-100 border border-slate-200 px-3.5 py-1 text-xs font-bold text-slate-700">
                                        {{ modalidadTexto }}
                                    </span>
                                </div>

                                <h1 class="text-2xl font-black text-[#172A3D] sm:text-3xl">
                                    {{ requerimiento.titulo }}
                                </h1>

                                <p class="mt-2 text-xs text-slate-400 font-medium">
                                    Publicado el {{ fechaPublicacion }}
                                </p>

                                <div class="my-6 border-t border-slate-200"></div>

                                <!-- PRESUPUESTO DESTACADO -->
                                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/70 p-5 shadow-xs">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-900">
                                        Presupuesto estimado del solicitante
                                    </p>
                                    <p class="mt-2 text-2xl font-black text-emerald-950 sm:text-3xl">
                                        {{ presupuestoTexto }}
                                    </p>
                                </div>

                                <!-- DESCRIPCIÓN -->
                                <div class="mt-8">
                                    <h2 class="text-lg font-bold text-[#172A3D]">Detalles de lo que se necesita</h2>
                                    <div
                                        class="mt-3 whitespace-pre-line text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                        {{ requerimiento.descripcion }}
                                    </div>
                                </div>

                                <!-- UBICACIÓN Y MODALIDAD -->
                                <div class="mt-8 border-t border-slate-200 pt-6">
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                        Ubicación y cobertura
                                    </h3>
                                    <div
                                        class="mt-3 flex items-center gap-2 text-xs sm:text-sm font-bold text-[#172A3D]">
                                        <span class="text-base">📍</span>
                                        <span>{{ ubicacionTexto }} ({{ modalidadTexto }})</span>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- COLUMNA LATERAL (ACCIONES Y CONTACTO) -->
                        <aside class="space-y-6">

                            <!-- CONTACTAR AL SOLICITANTE -->
                            <section v-if="!esPropietario"
                                class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-emerald-950/15">
                                <h2 class="text-base sm:text-lg font-black text-[#172A3D]">
                                    Ofrecer este servicio
                                </h2>
                                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                    Si tienes la capacidad de prestar este servicio, ponte en contacto directo con el
                                    solicitante para presentarle tu propuesta.
                                </p>

                                <button type="button" @click="contactarSolicitante"
                                    class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-900 px-4 py-3.5 text-xs sm:text-sm font-black text-white shadow-md transition hover:bg-emerald-950">
                                    💬 Contactar solicitante
                                </button>
                            </section>

                            <!-- ADMINISTRAR SOLICITUD (SOLO PROPIETARIO) -->
                            <section v-else
                                class="rounded-2xl border border-emerald-900/30 bg-emerald-950/5 p-6 shadow-2xl shadow-emerald-950/15 space-y-3">
                                <h2 class="text-base sm:text-lg font-black text-[#172A3D]">
                                    Administrar tu solicitud
                                </h2>
                                <p class="text-xs text-slate-600 font-medium">
                                    Actualiza el estado cuando ya hayas encontrado a alguien o no requieras el servicio.
                                </p>

                                <div class="space-y-2.5 pt-2">
                                    <button v-if="requerimiento.estado === 'abierto'" type="button"
                                        :disabled="estadoForm.processing" @click="actualizarEstado('resuelto')"
                                        class="w-full rounded-xl bg-emerald-900 px-4 py-3 text-xs sm:text-sm font-black text-white transition hover:bg-emerald-950 disabled:opacity-50 shadow-xs">
                                        ✓ Marcar como resuelto
                                    </button>

                                    <button v-if="requerimiento.estado === 'abierto'" type="button"
                                        :disabled="estadoForm.processing" @click="actualizarEstado('cancelado')"
                                        class="w-full rounded-xl border border-rose-300 bg-white px-4 py-3 text-xs sm:text-sm font-bold text-rose-700 transition hover:bg-rose-50 disabled:opacity-50 shadow-xs">
                                        Cancelar solicitud
                                    </button>

                                    <button v-if="requerimiento.estado !== 'abierto'" type="button"
                                        :disabled="estadoForm.processing" @click="actualizarEstado('abierto')"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 disabled:opacity-50 shadow-xs">
                                        Reabrir solicitud
                                    </button>
                                </div>
                            </section>

                            <!-- SOLICITADO POR -->
                            <section
                                class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-emerald-950/15">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                                    Solicitado por
                                </h3>
                                <div class="mt-4 flex items-center gap-3">
                                    <div
                                        class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-emerald-900 text-sm font-black text-white shadow-xs">
                                        <img v-if="fotoSolicitante" :src="fotoSolicitante" :alt="nombreSolicitante"
                                            class="h-full w-full object-cover" />
                                        <span v-else>{{ nombreSolicitante.charAt(0).toUpperCase() }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate font-bold text-[#172A3D]">
                                            {{ nombreSolicitante }}
                                        </p>
                                        <p class="text-xs text-slate-500 font-medium">
                                            Miembro de la comunidad
                                        </p>
                                    </div>
                                </div>
                            </section>

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