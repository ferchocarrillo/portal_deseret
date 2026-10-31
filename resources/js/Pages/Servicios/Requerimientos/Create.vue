<script setup>
import { ref, computed, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UbicacionSelector from '@/Components/UbicacionSelector.vue'
import { route } from 'ziggy-js'
import axios from 'axios'

const props = defineProps({
    palabrasProhibidas: {
        type: Array,
        default: () => [],
    },
})

// ==========================================================
// CATÁLOGO OFICIAL DE CATEGORÍAS (IDÉNTICO A SERVICIOS)
// ==========================================================
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

const categoriaBase = ref('')
const especialidadDetalle = ref('')

const esOtro = computed(() => categoriaBase.value === 'Otros servicios')

const form = useForm({
    titulo: '',
    descripcion: '',
    categoria: '',
    modalidad: 'presencial',
    pais: 'Colombia',
    departamento: '',
    ciudad: '',
    presupuesto_tipo: 'a_convenir',
    presupuesto_min: null,
    presupuesto_max: null,
    moneda: 'COP',
    urgencia: 'media',
    fecha_limite: '',
})

// ==========================================================
// CORRECCIÓN MÁGICA CON IA
// ==========================================================
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
            context_type: `requerimiento_servicio_${campo}`,
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

// ==========================================================
// MODERACIÓN EN TIEMPO REAL DE PALABRAS PROHIBIDAS
// ==========================================================
const alertaTitulo = ref('')
const alertaDescripcion = ref('')

function verificarPalabrasProhibidas(texto, tipo) {
    if (!texto || !props.palabrasProhibidas || props.palabrasProhibidas.length === 0) {
        if (tipo === 'titulo') alertaTitulo.value = ''
        if (tipo === 'descripcion') alertaDescripcion.value = ''
        return
    }

    const textoLower = texto.toLowerCase()
    const encontrada = props.palabrasProhibidas.find((palabra) => {
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

// ==========================================================
// PUBLICACIÓN
// ==========================================================
function publicarRequerimiento() {
    if (especialidadDetalle.value.trim()) {
        form.categoria = `${categoriaBase.value}: ${especialidadDetalle.value.trim()}`
    } else {
        form.categoria = categoriaBase.value
    }

    form.post(route('servicios.requerimientos.store'), {
        preserveScroll: true,
    })
}
</script>

<template>

    <Head title="Solicitar un servicio | Servicios" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">
            <!-- =====================================================
                 ENCABEZADO CON TONO VERDE OSCURO / ESMERALDA PROFUNDO TRANSLÚCIDO
            ====================================================== -->
            <section
                class="border-b border-emerald-900/30 bg-emerald-950/15 backdrop-blur-md shadow-lg shadow-emerald-950/10">
                <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
                    <Link :href="route('servicios.index')"
                        class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-emerald-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                        Volver al catálogo
                    </Link>

                    <div
                        class="mt-6 rounded-2xl bg-gradient-to-r from-emerald-950 to-teal-900 p-6 text-white shadow-xl sm:p-8">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-900/60 text-2xl shadow-inner border border-emerald-700/40">
                                ⚡
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.22em] text-emerald-300">
                                    Demanda de la comunidad
                                </p>
                                <h1 class="mt-1 text-2xl font-black text-white sm:text-3xl">
                                    ¿Qué servicio necesitas?
                                </h1>
                                <p
                                    class="mt-2 max-w-2xl text-xs sm:text-sm leading-relaxed text-emerald-100 font-medium">
                                    Publica un requerimiento para aquellos servicios que no encuentras en el catálogo.
                                    Los miembros y profesionales disponibles se pondrán en contacto contigo.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- =====================================================
                 CONTENIDO PRINCIPAL / FORMULARIO
            ====================================================== -->
            <div class="py-8">
                <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
                    <form class="space-y-6" @submit.prevent="publicarRequerimiento">
                        <!-- 1. INFORMACIÓN DEL REQUERIMIENTO -->
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-emerald-950/15 sm:p-8 space-y-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D] border-b border-slate-100 pb-3">
                                1. Información de la solicitud
                            </h2>

                            <!-- TÍTULO CON VARITA MÁGICA Y ALERTA -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="titulo"
                                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        ¿Qué necesitas puntualmente? <span class="text-red-500">*</span>
                                    </label>
                                    <button type="button" @click="aplicarCorreccionMagica('titulo')"
                                        :disabled="cargandoIA.titulo || !form.titulo.trim()"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition disabled:opacity-50">
                                        <span v-if="cargandoIA.titulo" class="animate-spin">⏳</span>
                                        <span v-else>✨</span>
                                        <span>Corrección Mágica</span>
                                    </button>
                                </div>
                                <input id="titulo" v-model="form.titulo" type="text" maxlength="150" required
                                    placeholder="Ej. Reparación e impermeabilización de tejado en teja de barro"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition placeholder:text-slate-400 focus:border-emerald-900 focus:ring-2 focus:ring-emerald-100"
                                    :class="{ 'border-red-500': form.errors.titulo || alertaTitulo }" />

                                <!-- Alerta en tiempo real del Título -->
                                <p v-if="alertaTitulo" class="mt-1.5 text-xs text-amber-600 font-bold">
                                    {{ alertaTitulo }}
                                </p>

                                <p v-if="form.errors.titulo" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.titulo }}
                                </p>
                            </div>

                            <!-- CATEGORÍA Y ESPECIALIDAD -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="categoria"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Categoría principal <span class="text-red-500">*</span>
                                    </label>
                                    <select id="categoria" v-model="categoriaBase" required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-emerald-900 focus:ring-2 focus:ring-emerald-100">
                                        <option value="" disabled>Selecciona una categoría</option>
                                        <option v-for="cat in categorias" :key="cat" :value="cat">
                                            {{ cat }}
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <label for="especialidad"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Especialidad o detalle <span v-if="esOtro" class="text-red-500">*</span>
                                    </label>
                                    <input id="especialidad" v-model="especialidadDetalle" type="text" maxlength="100"
                                        :required="esOtro" placeholder="Ej. Plomería, Inglés intermedio, Tapicería..."
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition placeholder:text-slate-400 focus:border-emerald-900 focus:ring-2 focus:ring-emerald-100" />
                                </div>
                            </div>

                            <!-- DESCRIPCIÓN CON VARITA MÁGICA Y ALERTA -->
                            <div>
                                <div class="mb-1.5 flex items-center justify-between gap-3">
                                    <label for="descripcion"
                                        class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Descripción detallada <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="aplicarCorreccionMagica('descripcion')"
                                            :disabled="cargandoIA.descripcion || !form.descripcion.trim()"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition disabled:opacity-50">
                                            <span v-if="cargandoIA.descripcion" class="animate-spin">⏳</span>
                                            <span v-else>✨</span>
                                            <span>Corrección Mágica</span>
                                        </button>
                                        <span class="text-xs text-slate-400 font-medium">
                                            {{ form.descripcion.length }}/5000
                                        </span>
                                    </div>
                                </div>
                                <textarea id="descripcion" v-model="form.descripcion" rows="5" maxlength="5000" required
                                    placeholder="Indica condiciones, materiales requeridos, horarios de preferencia o cualquier aspecto necesario para entender tu solicitud..."
                                    class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-relaxed text-[#172A3D] outline-none transition placeholder:text-slate-400 focus:border-emerald-900 focus:ring-2 focus:ring-emerald-100"
                                    :class="{ 'border-red-500': form.errors.descripcion || alertaDescripcion }"></textarea>

                                <!-- Alerta en tiempo real de la Descripción -->
                                <p v-if="alertaDescripcion" class="mt-1.5 text-xs text-amber-600 font-bold">
                                    {{ alertaDescripcion }}
                                </p>

                                <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.descripcion }}
                                </p>
                            </div>
                        </section>

                        <!-- 2. MODALIDAD Y UBICACIÓN -->
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-emerald-950/15 sm:p-8 space-y-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D] border-b border-slate-100 pb-3">
                                2. Modalidad y ubicación
                            </h2>

                            <div>
                                <label class="mb-3 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    ¿Cómo debe prestarse el servicio? <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <label v-for="opcion in [
                                        { value: 'presencial', titulo: 'Presencial', desc: 'Requiere visita o presencia física' },
                                        { value: 'remoto', titulo: 'Remoto', desc: 'Atención 100% digital o a distancia' },
                                        { value: 'hibrido', titulo: 'Híbrido', desc: 'Combina trabajo físico y remoto' },
                                    ]" :key="opcion.value"
                                        class="flex cursor-pointer gap-3 rounded-xl border p-4 transition bg-white shadow-xs"
                                        :class="form.modalidad === opcion.value
                                            ? 'border-emerald-900 bg-emerald-950/5 ring-2 ring-emerald-900/20 shadow-md'
                                            : 'border-slate-300 hover:border-emerald-800'">
                                        <input v-model="form.modalidad" type="radio" :value="opcion.value"
                                            class="mt-1 h-4 w-4 accent-emerald-900" />
                                        <span>
                                            <span class="block text-xs sm:text-sm font-bold text-[#172A3D]">{{
                                                opcion.titulo }}</span>
                                            <span class="mt-0.5 block text-xs text-slate-500 font-medium">{{ opcion.desc
                                            }}</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- SELECTOR DE UBICACIÓN REUTILIZABLE -->
                            <div v-if="form.modalidad !== 'remoto'" class="pt-2">
                                <UbicacionSelector v-model:pais="form.pais" v-model:departamento="form.departamento"
                                    v-model:ciudad="form.ciudad" />
                            </div>
                        </section>

                        <!-- 3. PRESUPUESTO Y TIEMPOS -->
                        <section
                            class="overflow-hidden rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-emerald-950/15 sm:p-8 space-y-6">
                            <h2 class="text-base sm:text-lg font-bold text-[#172A3D] border-b border-slate-100 pb-3">
                                3. Presupuesto y tiempos
                            </h2>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                                <div>
                                    <label for="presupuesto_tipo"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Presupuesto estimado
                                    </label>
                                    <select id="presupuesto_tipo" v-model="form.presupuesto_tipo"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-emerald-900 focus:ring-2 focus:ring-emerald-100">
                                        <option value="a_convenir">A convenir con el prestador</option>
                                        <option value="fijo">Presupuesto fijo</option>
                                        <option value="rango">Rango de presupuesto</option>
                                    </select>
                                </div>

                                <div v-if="form.presupuesto_tipo !== 'a_convenir'">
                                    <label for="presupuesto_min"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        {{ form.presupuesto_tipo === 'fijo' ? 'Valor propuesto' : 'Presupuesto mínimo'
                                        }}
                                    </label>
                                    <input id="presupuesto_min" v-model="form.presupuesto_min" type="number" min="0"
                                        placeholder="$ 0"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition placeholder:text-slate-400 focus:border-emerald-900 focus:ring-2 focus:ring-emerald-100" />
                                </div>

                                <div v-if="form.presupuesto_tipo === 'rango'">
                                    <label for="presupuesto_max"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Presupuesto máximo
                                    </label>
                                    <input id="presupuesto_max" v-model="form.presupuesto_max" type="number" min="0"
                                        placeholder="$ 0"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition placeholder:text-slate-400 focus:border-emerald-900 focus:ring-2 focus:ring-emerald-100" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="urgencia"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Nivel de urgencia
                                    </label>
                                    <select id="urgencia" v-model="form.urgencia"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-emerald-900 focus:ring-2 focus:ring-emerald-100">
                                        <option value="baja">Baja — Flexible</option>
                                        <option value="media">Media — Próximos días</option>
                                        <option value="alta">Alta — Esta semana</option>
                                        <option value="urgente">🔥 Muy urgente — De inmediato</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="fecha_limite"
                                        class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Fecha límite de atención (Opcional)
                                    </label>
                                    <input id="fecha_limite" v-model="form.fecha_limite" type="date"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none transition focus:border-emerald-900 focus:ring-2 focus:ring-emerald-100" />
                                </div>
                            </div>
                        </section>

                        <!-- ACCIONES -->
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end pb-12">
                            <Link :href="route('servicios.index')"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                Cancelar
                            </Link>

                            <button type="submit"
                                :disabled="form.processing || Boolean(alertaTitulo) || Boolean(alertaDescripcion)"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-900 px-7 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-emerald-950/20 transition hover:bg-emerald-950 disabled:cursor-not-allowed disabled:opacity-60">
                                {{ form.processing ? 'Publicando...' : 'Publicar solicitud' }}
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