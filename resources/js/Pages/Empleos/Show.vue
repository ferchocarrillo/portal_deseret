<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    oferta: {
        type: Object,
        required: true,
    },
    esPropietario: {
        type: Boolean,
        default: false,
    },
    yaPostulado: {
        type: Boolean,
        default: false,
    },
    puedePostularse: {
        type: Boolean,
        default: true,
    },
    requisitosPostulacion: {
        type: Object,
        default: () => ({}),
    },
})

const mostrarFormularioPostulacion = ref(false)
const enviandoPostulacion = ref(false)
const respuestas = ref({})

const nombreEmpresa = computed(() => {
    if (props.oferta.empresa?.nombre) {
        return props.oferta.empresa.nombre
    }

    if (props.oferta.usuario?.name) {
        return props.oferta.usuario.name
    }

    return 'Publicador independiente'
})

const inicialEmpresa = computed(() => {
    return nombreEmpresa.value.charAt(0).toUpperCase()
})

const preguntas = computed(() => {
    return props.oferta.preguntas ?? []
})

const tienePreguntas = computed(() => {
    return preguntas.value.length > 0
})

// ==========================================================
// FAVORITOS
// ==========================================================

const alternarFavorito = () => {
    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: props.oferta.id,
            modulo: 'empleos',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                props.oferta.es_favorito = !props.oferta.es_favorito
            },
        }
    )
}

// ==========================================================
// REQUISITOS DEL PERFIL PARA POSTULARSE
// ==========================================================

const requisitosPerfil = computed(() => {
    const requisitos = props.requisitosPostulacion ?? {}

    return [
        {
            id: 'formacion',
            titulo: 'Formación académica',
            descripcion: 'Registra al menos una formación académica completa.',
            cumplido: Boolean(requisitos.tieneFormacion),
        },
        {
            id: 'experiencia',
            titulo: 'Experiencia laboral',
            descripcion: 'Registra una experiencia laboral completa o indica que no tienes experiencia.',
            cumplido: Boolean(requisitos.tieneExperiencia),
        },
        {
            id: 'hojaVida',
            titulo: 'Hoja de vida',
            descripcion: 'Carga tu hoja de vida y asegúrate de que esté marcada como principal.',
            cumplido: Boolean(requisitos.tieneHojaVida),
        },
    ]
})

const requisitosPerfilPendientes = computed(() => {
    return requisitosPerfil.value.filter(requisito => !requisito.cumplido)
})

const perfilCompleto = computed(() => {
    return requisitosPerfilPendientes.value.length === 0
})

// ==========================================================
// FORMATOS
// ==========================================================

const formatearFecha = (fecha) => {
    if (!fecha) {
        return ''
    }

    return new Intl.DateTimeFormat('es-CO', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date(fecha))
}

const textoExperienciaRequerida = computed(() => {
    const meses = Number(props.oferta.experiencia_minima_meses ?? 0)

    if (meses <= 0) {
        return 'Sin experiencia requerida'
    }

    if (meses < 12) {
        return `${meses} ${meses === 1 ? 'mes' : 'meses'} de experiencia requerida`
    }

    const anios = Math.floor(meses / 12)
    const mesesRestantes = meses % 12

    let texto = `${anios} ${anios === 1 ? 'año' : 'años'}`
    if (mesesRestantes > 0) {
        texto += ` y ${mesesRestantes} ${mesesRestantes === 1 ? 'mes' : 'meses'}`
    }

    return `${texto} (${meses} meses) de experiencia requerida`
})

const formatearSalario = () => {
    if (!props.oferta.mostrar_salario) {
        return 'Salario no especificado'
    }

    const tipo = String(props.oferta.tipo_salario ?? '')
        .trim()
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]/g, '')

    if (['smmlv', 'smlmv', 'slmv'].includes(tipo)) {
        return 'Salario mínimo legal mensual vigente (SMMLV)'
    }

    if (tipo === 'aconvenir') {
        return 'Salario a convenir'
    }

    const monto = props.oferta.salario_monto ?? props.oferta.monto_salario ?? props.oferta.salario

    if (['salarioespecifico', 'especifico'].includes(tipo) && monto !== null && monto !== undefined && monto !== '') {
        const valor = Number(monto)

        if (Number.isFinite(valor)) {
            return valor.toLocaleString('es-CO', {
                style: 'currency',
                currency: 'COP',
                maximumFractionDigits: 0,
            })
        }
    }

    return 'Salario no especificado'
}

// ==========================================================
// ACCIONES DE POSTULACIÓN
// ==========================================================

const abrirPostulacion = () => {
    if (props.esPropietario || !props.puedePostularse || props.yaPostulado) {
        return
    }

    if (tienePreguntas.value) {
        mostrarFormularioPostulacion.value = true
        return
    }

    enviarPostulacion()
}

const cerrarPostulacion = () => {
    if (enviandoPostulacion.value) {
        return
    }

    mostrarFormularioPostulacion.value = false
}

const enviarPostulacion = () => {
    if (props.esPropietario || !props.puedePostularse || props.yaPostulado) {
        return
    }

    enviandoPostulacion.value = true

    router.post(
        route('empleos.postular', props.oferta.id),
        {
            respuestas: respuestas.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                mostrarFormularioPostulacion.value = false
            },
            onFinish: () => {
                enviandoPostulacion.value = false
            },
        }
    )
}
</script>

<template>

    <Head :title="`${oferta.titulo} | Empleos`" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- =====================================================
                ENCABEZADO
            ====================================================== -->
            <section
                class="border-b border-amber-200/80 bg-amber-100/25 backdrop-blur-md shadow-lg shadow-amber-500/10">
                <div class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
                    <Link :href="route('empleos.index')"
                        class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-[#D99A24]">
                        ← Volver a ofertas
                    </Link>

                    <div class="mt-6 flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                        <div class="flex min-w-0 gap-5">
                            <div
                                class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl border border-amber-200 bg-amber-50 text-2xl font-black text-amber-900 shadow-xs">
                                {{ inicialEmpresa }}
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-[#172A3D]">
                                        {{ oferta.titulo }}
                                    </h1>

                                    <span v-if="esPropietario"
                                        class="rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-[10px] font-bold text-amber-900 uppercase">
                                        Tu oferta publicada
                                    </span>
                                </div>

                                <p class="mt-1 text-sm font-bold text-slate-600">
                                    {{ nombreEmpresa }}
                                </p>

                                <div
                                    class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-xs sm:text-sm text-slate-600 font-medium">
                                    <span v-if="oferta.ciudad || oferta.departamento"
                                        class="inline-flex items-center gap-1.5">
                                        📍 {{ oferta.ciudad }}
                                        <span v-if="oferta.departamento">· {{ oferta.departamento }}</span>
                                    </span>

                                    <span v-if="oferta.modalidad" class="inline-flex items-center gap-1.5">
                                        💼 {{ oferta.modalidad }}
                                    </span>

                                    <span v-if="oferta.tipo_contrato" class="inline-flex items-center gap-1.5">
                                        📄 {{ oferta.tipo_contrato }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div v-if="oferta.mostrar_salario"
                            class="shrink-0 rounded-2xl bg-white/90 border border-amber-200 px-5 py-4 shadow-md backdrop-blur">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-amber-900">
                                Salario
                            </p>
                            <p class="mt-1 text-base sm:text-lg font-black text-[#172A3D]">
                                {{ formatearSalario() }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =====================================================
                CONTENIDO PRINCIPAL
            ====================================================== -->
            <main class="mx-auto max-w-7xl px-6 py-8 lg:px-8 bg-[#D99A24]/20 rounded-xl p-3 mt-2">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-[minmax(0,1fr)_340px]">

                    <!-- COLUMNA IZQUIERDA: INFORMACIÓN DE LA OFERTA -->
                    <div class="space-y-6">

                        <!-- Descripción -->
                        <section
                            class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15">
                            <h2 class="text-lg sm:text-xl font-black text-[#172A3D]">
                                Descripción de la oferta
                            </h2>
                            <div class="prose prose-slate mt-4 max-w-none text-xs sm:text-sm leading-relaxed text-slate-600 font-medium"
                                v-html="oferta.descripcion" />
                        </section>

                        <!-- Responsabilidades -->
                        <section v-if="oferta.responsabilidades"
                            class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15">
                            <h2 class="text-lg sm:text-xl font-black text-[#172A3D]">
                                Responsabilidades
                            </h2>
                            <div class="prose prose-slate mt-4 max-w-none text-xs sm:text-sm leading-relaxed text-slate-600 font-medium"
                                v-html="oferta.responsabilidades" />
                        </section>

                        <!-- Requisitos adicionales -->
                        <section v-if="oferta.requisitos"
                            class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15">
                            <h2 class="text-lg sm:text-xl font-black text-[#172A3D]">
                                Requisitos adicionales
                            </h2>
                            <div class="prose prose-slate mt-4 max-w-none text-xs sm:text-sm leading-relaxed text-slate-600 font-medium"
                                v-html="oferta.requisitos" />
                        </section>

                        <!-- Formación requerida -->
                        <section v-if="oferta.formacion"
                            class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15">
                            <h2 class="text-lg sm:text-xl font-black text-[#172A3D]">
                                Formación requerida
                            </h2>
                            <div class="mt-4 text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                {{ oferta.formacion }}
                            </div>
                        </section>

                        <!-- Experiencia Requerida -->
                        <section
                            class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15">
                            <h2 class="text-lg sm:text-xl font-black text-[#172A3D]">
                                Experiencia
                            </h2>
                            <div class="mt-3 space-y-1">
                                <p class="text-sm sm:text-base font-bold text-[#172A3D]">
                                    {{ textoExperienciaRequerida }}
                                </p>
                                <p v-if="oferta.nivel_experiencia"
                                    class="text-xs sm:text-sm text-slate-500 font-medium">
                                    Nivel de perfil sugerido: <span class="font-bold text-slate-700">{{
                                        oferta.nivel_experiencia }}</span>
                                </p>
                            </div>
                        </section>

                        <!-- Habilidades requeridas -->
                        <section v-if="oferta.skills?.length"
                            class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15">
                            <h2 class="text-lg sm:text-xl font-black text-[#172A3D]">
                                Habilidades requeridas
                            </h2>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span v-for="skill in oferta.skills" :key="skill.id"
                                    class="rounded-full bg-amber-50 border border-amber-200 px-3 py-1 text-xs font-bold text-amber-900">
                                    {{ skill.name }}
                                </span>
                            </div>
                        </section>

                    </div>

                    <!-- COLUMNA DERECHA: PANEL LATERAL -->
                    <aside>
                        <div class="sticky top-6 space-y-6">

                            <!-- VISTA PARA EL PUBLICADOR / DUEÑO DE LA OFERTA -->
                            <section v-if="esPropietario"
                                class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15">
                                <p class="text-xs font-bold uppercase tracking-widest text-[#D99A24]">
                                    Gestión de la oferta
                                </p>
                                <h2 class="mt-1 text-lg font-black text-[#172A3D]">
                                    Eres el publicador
                                </h2>
                                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                    Como creador de esta oferta puedes actualizar su información o revisar los
                                    candidatos que se han postulado.
                                </p>

                                <div class="mt-6 flex flex-col gap-3">
                                    <Link :href="route('empleos.edit', oferta.id)"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-bold text-[#172A3D] shadow-sm transition hover:bg-slate-50">
                                        ✏️ Editar esta oferta
                                    </Link>

                                    <Link :href="route('empleos.postulantes', oferta.id)"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#172A3D] px-4 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-[#172A3D]/20 transition hover:bg-[#D99A24] hover:text-[#172A3D]">
                                        👥 Ver postulantes recibidos
                                    </Link>

                                    <!-- Botón de Favorito Global Interactivo -->
                                    <button type="button" @click="alternarFavorito"
                                        class="flex w-full items-center justify-center gap-2.5 rounded-xl px-4 py-3 text-xs sm:text-sm font-black transition shadow-md"
                                        :class="oferta.es_favorito
                                            ? 'bg-amber-400 text-[#172A3D] shadow-amber-400/30 hover:bg-amber-500'
                                            : 'bg-amber-100/70 text-[#172A3D] border border-amber-300 hover:bg-amber-200/80'">
                                        <span class="text-base"
                                            :class="oferta.es_favorito ? 'text-red-600' : 'text-slate-600'">
                                            {{ oferta.es_favorito ? '❤️' : '♡' }}
                                        </span>
                                        <span>{{ oferta.es_favorito ? 'Guardado en favoritos' : 'Guardar en favoritos'
                                        }}</span>
                                    </button>
                                </div>
                            </section>

                            <!-- VISTA PARA CANDIDATOS / POSTULANTES -->
                            <section v-else
                                class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15">
                                <h2 class="text-lg font-black text-[#172A3D]">
                                    ¿Te interesa esta oportunidad?
                                </h2>
                                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-600 font-medium">
                                    Revisa la información de la oferta y presenta tu perfil.
                                </p>

                                <!-- ESTADO: YA POSTULADO -->
                                <div v-if="yaPostulado"
                                    class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-xs">
                                    <p class="text-xs sm:text-sm font-bold text-emerald-800">
                                        ✓ Ya estás postulado
                                    </p>
                                    <p class="mt-1 text-xs leading-5 text-emerald-700 font-medium">
                                        Tu postulación ya fue registrada para esta oferta.
                                    </p>
                                </div>

                                <!-- REQUISITOS DE EXPERIENCIA -->
                                <div v-if="!yaPostulado && requisitosPostulacion"
                                    class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <h3 class="text-xs sm:text-sm font-bold text-[#172A3D]">
                                        Requisitos de la oferta
                                    </h3>

                                    <div class="mt-3 space-y-2.5">
                                        <div>
                                            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                                Experiencia que tienes registrada
                                            </p>
                                            <p class="text-xs sm:text-sm font-bold text-[#172A3D]">
                                                {{ requisitosPostulacion.experienciaMeses ?? 0 }} meses
                                            </p>
                                        </div>

                                        <div>
                                            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                                Experiencia que solicita la empresa
                                            </p>
                                            <p class="text-xs sm:text-sm font-bold text-[#172A3D]">
                                                {{ requisitosPostulacion.experienciaRequerida ?? 0 }} meses
                                            </p>
                                        </div>

                                        <div class="flex items-start gap-2 text-xs sm:text-sm font-bold pt-1 border-t border-slate-200"
                                            :class="requisitosPostulacion.cumpleExperiencia ? 'text-emerald-700' : 'text-red-600'">
                                            <span>
                                                {{ requisitosPostulacion.cumpleExperiencia ? '✓' : '✕' }}
                                            </span>
                                            <span>
                                                {{
                                                    requisitosPostulacion.cumpleExperiencia
                                                        ? 'Cumples la experiencia requerida'
                                                        : 'No cumples la experiencia mínima solicitada'
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- ESTADO DEL PERFIL -->
                                <div v-if="!yaPostulado && requisitosPostulacion"
                                    class="mt-5 rounded-xl border p-4 shadow-xs"
                                    :class="perfilCompleto ? 'border-emerald-200 bg-emerald-50' : 'border-amber-200 bg-amber-50'">
                                    <div class="flex items-start gap-3">
                                        <span class="mt-0.5 text-base font-bold"
                                            :class="perfilCompleto ? 'text-emerald-600' : 'text-amber-600'">
                                            {{ perfilCompleto ? '✓' : '!' }}
                                        </span>

                                        <div>
                                            <h3 class="text-xs sm:text-sm font-bold"
                                                :class="perfilCompleto ? 'text-emerald-800' : 'text-amber-900'">
                                                {{
                                                    perfilCompleto
                                                        ? 'Tu perfil está completo'
                                                        : 'Necesitas completar tu perfil'
                                                }}
                                            </h3>
                                            <p class="mt-1 text-xs leading-5 font-medium"
                                                :class="perfilCompleto ? 'text-emerald-700' : 'text-amber-800'">
                                                {{
                                                    perfilCompleto
                                                        ? 'Ya registraste la información necesaria de tu perfil.'
                                                        : 'Antes de postularte, debes completar la siguiente información:'
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Lista de requisitos del perfil -->
                                    <div class="mt-4 space-y-3">
                                        <div v-for="requisito in requisitosPerfil" :key="requisito.id"
                                            class="flex items-start gap-3">
                                            <span
                                                class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-bold"
                                                :class="requisito.cumplido ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-200 text-amber-800'">
                                                {{ requisito.cumplido ? '✓' : '!' }}
                                            </span>

                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs sm:text-sm font-bold"
                                                    :class="requisito.cumplido ? 'text-emerald-800' : 'text-amber-900'">
                                                    {{ requisito.titulo }}
                                                </p>
                                                <p class="mt-0.5 text-xs leading-5 text-slate-600 font-medium">
                                                    {{
                                                        requisito.cumplido
                                                            ? 'Información registrada correctamente.'
                                                            : requisito.descripcion
                                                    }}
                                                </p>
                                                <span class="mt-0.5 inline-block text-[11px] font-bold"
                                                    :class="requisito.cumplido ? 'text-emerald-700' : 'text-amber-700'">
                                                    {{ requisito.cumplido ? 'Completado' : 'Pendiente' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <Link v-if="!perfilCompleto" href="/perfil"
                                        class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl bg-[#172A3D] px-4 py-3 text-xs sm:text-sm font-black text-white shadow-md transition hover:bg-[#D99A24] hover:text-[#172A3D]">
                                        Completar mi perfil
                                        <span aria-hidden="true">→</span>
                                    </Link>
                                </div>

                                <!-- BOTÓN DE POSTULACIÓN -->
                                <button v-if="!yaPostulado" type="button" :disabled="!puedePostularse"
                                    @click="abrirPostulacion"
                                    class="mt-5 w-full rounded-xl bg-[#172A3D] px-5 py-3.5 text-xs sm:text-sm font-black text-white shadow-md shadow-[#172A3D]/20 transition hover:bg-[#D99A24] hover:text-[#172A3D] disabled:cursor-not-allowed disabled:opacity-50">
                                    Postularme
                                </button>

                                <!-- BOTÓN DE FAVORITO GLOBAL INTERACTIVO -->
                                <button type="button" @click="alternarFavorito"
                                    class="mt-3 flex w-full items-center justify-center gap-2.5 rounded-xl px-4 py-3 text-xs sm:text-sm font-black transition shadow-md"
                                    :class="oferta.es_favorito
                                        ? 'bg-amber-400 text-[#172A3D] shadow-amber-400/30 hover:bg-amber-500'
                                        : 'bg-amber-100/70 text-[#172A3D] border border-amber-300 hover:bg-amber-200/80'">
                                    <span class="text-base"
                                        :class="oferta.es_favorito ? 'text-red-600' : 'text-slate-600'">
                                        {{ oferta.es_favorito ? '❤️' : '♡' }}
                                    </span>
                                    <span>{{ oferta.es_favorito ? 'Guardado en favoritos' : 'Guardar en favoritos'
                                    }}</span>
                                </button>

                                <!-- MENSAJE SI NO PUEDE POSTULARSE -->
                                <div v-if="!puedePostularse && !yaPostulado"
                                    class="mt-3 rounded-xl bg-slate-50 p-3 border border-slate-200">
                                    <p v-if="requisitosPerfilPendientes.length > 0"
                                        class="text-center text-xs leading-5 text-slate-600 font-medium">
                                        Completa la información pendiente de tu perfil para habilitar tu postulación.
                                    </p>
                                    <p v-else-if="requisitosPostulacion && !requisitosPostulacion.cumpleExperiencia"
                                        class="text-center text-xs leading-5 text-slate-600 font-medium">
                                        Tu perfil está completo, pero no cumples la experiencia mínima solicitada para
                                        esta oferta.
                                    </p>
                                    <p v-else class="text-center text-xs leading-5 text-slate-600 font-medium">
                                        En este momento no es posible postularte a esta oferta.
                                    </p>
                                </div>
                            </section>

                            <!-- ================================================= -->
                            <!-- BOTÓN DE DENUNCIA / REPORTE DE INCIDENTE -->
                            <!-- ================================================= -->
                           <!-- ================================================= -->
                            <!-- BOTÓN DE DENUNCIA / REPORTE DE INCIDENTE -->
                            <!-- ================================================= -->
                            <div
                                class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15">
                                <Link
                                    :href="route('denuncias.create', { modulo: 'empleos', reportable_type: 'App\\Models\\OfertaEmpleo', reportable_id: oferta.id })"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs sm:text-sm font-bold text-red-700 transition hover:bg-red-100 hover:text-red-900 shadow-sm">
                                    🚨 Reportar esta oferta de empleo
                                </Link>
                            </div>

                            <!-- RESUMEN DE INFORMACIÓN -->
                            <section
                                class="rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15">
                                <h2 class="text-lg font-black text-[#172A3D]">
                                    Información de la oferta
                                </h2>

                                <dl class="mt-5 space-y-4">
                                    <div>
                                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Publicada
                                        </dt>
                                        <dd class="mt-0.5 text-xs sm:text-sm font-bold text-[#172A3D]">
                                            {{ formatearFecha(oferta.fecha_publicacion) }}
                                        </dd>
                                    </div>

                                    <div v-if="oferta.modalidad">
                                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Modalidad
                                        </dt>
                                        <dd class="mt-0.5 text-xs sm:text-sm font-bold text-[#172A3D]">
                                            {{ oferta.modalidad }}
                                        </dd>
                                    </div>

                                    <div v-if="oferta.tipo_contrato">
                                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Contrato
                                        </dt>
                                        <dd class="mt-0.5 text-xs sm:text-sm font-bold text-[#172A3D]">
                                            {{ oferta.tipo_contrato }}
                                        </dd>
                                    </div>

                                    <div v-if="oferta.nivel_experiencia">
                                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Nivel
                                        </dt>
                                        <dd class="mt-0.5 text-xs sm:text-sm font-bold text-[#172A3D]">
                                            {{ oferta.nivel_experiencia }}
                                        </dd>
                                    </div>

                                    <div v-if="oferta.ciudad">
                                        <dt class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            Ubicación
                                        </dt>
                                        <dd class="mt-0.5 text-xs sm:text-sm font-bold text-[#172A3D]">
                                            {{ oferta.ciudad }}
                                            <span v-if="oferta.departamento">· {{ oferta.departamento }}</span>
                                        </dd>
                                    </div>
                                </dl>
                            </section>

                        </div>
                    </aside>

                </div>
            </main>

            <!-- =====================================================
                MODAL DE POSTULACIÓN (PREGUNTAS)
            ====================================================== -->
            <div v-if="mostrarFormularioPostulacion"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 backdrop-blur-xs px-4 py-8">
                <div
                    class="max-h-full w-full max-w-2xl overflow-y-auto rounded-3xl bg-white shadow-2xl border border-slate-200">

                    <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-[#D99A24]">
                                Postulación
                            </p>
                            <h2 class="mt-1 text-lg sm:text-xl font-black text-[#172A3D]">
                                {{ oferta.titulo }}
                            </h2>
                            <p class="mt-0.5 text-xs sm:text-sm text-slate-600 font-semibold">
                                {{ nombreEmpresa }}
                            </p>
                        </div>

                        <button type="button" @click="cerrarPostulacion"
                            class="rounded-xl px-3 py-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 font-bold">
                            ✕
                        </button>
                    </div>

                    <form @submit.prevent="enviarPostulacion" class="space-y-6 p-6">
                        <div>
                            <h3 class="text-base font-bold text-[#172A3D]">
                                Preguntas del empleador
                            </h3>
                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                Responde las siguientes preguntas para completar tu postulación.
                            </p>
                        </div>

                        <div v-for="pregunta in preguntas" :key="pregunta.id" class="space-y-2">
                            <label class="block text-xs sm:text-sm font-bold text-slate-700">
                                {{ pregunta.pregunta }}
                                <span v-if="pregunta.obligatoria" class="text-red-500">*</span>
                            </label>

                            <input v-if="pregunta.tipo === 'texto'" v-model="respuestas[pregunta.id]" type="text"
                                :required="pregunta.obligatoria"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-[#172A3D] outline-none transition focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100" />

                            <textarea v-else-if="pregunta.tipo === 'textarea'" v-model="respuestas[pregunta.id]"
                                rows="4" :required="pregunta.obligatoria"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-[#172A3D] outline-none transition focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100"></textarea>

                            <select v-else-if="pregunta.opciones?.length" v-model="respuestas[pregunta.id]"
                                :required="pregunta.obligatoria"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100">
                                <option value="">Selecciona una opción</option>
                                <option v-for="opcion in pregunta.opciones" :key="opcion.id" :value="opcion.opcion">
                                    {{ opcion.opcion }}
                                </option>
                            </select>

                            <input v-else v-model="respuestas[pregunta.id]" type="text" :required="pregunta.obligatoria"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-[#172A3D] outline-none transition focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100" />
                        </div>

                        <div
                            class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                            <button type="button" :disabled="enviandoPostulacion" @click="cerrarPostulacion"
                                class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 disabled:opacity-50">
                                Cancelar
                            </button>

                            <button type="submit" :disabled="enviandoPostulacion"
                                class="rounded-xl bg-[#172A3D] px-6 py-3 text-xs sm:text-sm font-black text-white shadow-md transition hover:bg-[#D99A24] hover:text-[#172A3D] disabled:cursor-not-allowed disabled:opacity-50">
                                {{ enviandoPostulacion ? 'Enviando...' : 'Enviar postulación' }}
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