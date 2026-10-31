<script setup>
import { ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import axios from 'axios'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    moduloPreseleccionado: {
        type: String,
        default: '',
    },
    reportableType: {
        type: String,
        default: null,
    },
    reportableId: {
        type: [Number, String],
        default: null,
    },
    palabrasProhibidas: {
        type: Array,
        default: () => [],
    },
})

// Formulario reactivo para la creación de la denuncia
const form = useForm({
    modulo: props.moduloPreseleccionado || 'marketplace',
    reportable_type: props.reportableType,
    reportable_id: props.reportableId,
    motivo: '',
    descripcion: '',
    evidencia: null,
})

// Lista de motivos comunes de denuncia
const motivosComunes = [
    'Contenido fraudulento o estafa',
    'Información falsa o engañosa',
    'Lenguaje ofensivo o inapropiado',
    'Suplantación de identidad',
    'Artículo o servicio prohibido',
    'Otro motivo',
]

/*
|--------------------------------------------------------------------------
| CORRECCIÓN MÁGICA CON IA
|--------------------------------------------------------------------------
*/
const cargandoIA = ref(false)

async function aplicarCorreccionMagica() {
    const textoActual = form.descripcion
    if (!textoActual || !textoActual.trim()) return

    cargandoIA.value = true

    try {
        const response = await axios.post('/ai/correct-text', {
            text: textoActual,
            context_type: 'denuncia_descripcion',
        })

        if (response.data && response.data.improved_text) {
            form.descripcion = response.data.improved_text
        }
    } catch (error) {
        console.error('Error al aplicar la corrección mágica:', error)
        alert('No fue posible conectar con el servicio de IA en este momento.')
    } finally {
        cargandoIA.value = false
    }
}

/*
|--------------------------------------------------------------------------
| MODERACIÓN EN TIEMPO REAL DE PALABRAS PROHIBIDAS
|--------------------------------------------------------------------------
*/
const alertaDescripcion = ref('')

function verificarPalabrasProhibidas(texto) {
    if (!texto || !props.palabrasProhibidas || props.palabrasProhibidas.length === 0) {
        alertaDescripcion.value = ''
        return
    }

    const textoLower = texto.toLowerCase()
    const encontrada = props.palabrasProhibidas.find(palabra => {
        const palabraClean = palabra.trim().toLowerCase()
        return palabraClean.length > 0 && textoLower.includes(palabraClean)
    })

    if (encontrada) {
        alertaDescripcion.value = `⚠️ Se ha detectado un término no permitido ("${encontrada}"). Por favor, modifícalo antes de enviar.`
    } else {
        alertaDescripcion.value = ''
    }
}

watch(() => form.descripcion, (nuevoValor) => {
    verificarPalabrasProhibidas(nuevoValor)
})

// Envío del formulario
const enviarDenuncia = () => {
    if (alertaDescripcion.value) return

    form.post(route('denuncias.store'), {
        forceFormData: true,
        preserveScroll: true,
    })
}
</script>

<template>

    <Head title="Reportar Incidente o Denuncia | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ENCABEZADO -->
            <section class="border-b border-red-200/80 bg-red-100/25 backdrop-blur-md shadow-lg shadow-red-500/10">
                <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="mb-1 flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-red-600"></span>
                        <span class="text-[10px] font-black uppercase tracking-[0.22em] text-red-950">
                            Seguridad y Confianza
                        </span>
                    </div>
                    <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                        Crear una denuncia
                    </h1>
                    <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                        Ayúdanos a mantener una comunidad segura. Tu reporte será tratado con total confidencialidad.
                    </p>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
            <main class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
                <form @submit.prevent="enviarDenuncia" class="space-y-6">

                    <section
                        class="overflow-hidden rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-red-500/15 sm:p-8 space-y-5">

                        <!-- Módulo afectado -->
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Módulo o Sección a Reportar <span class="text-red-500">*</span>
                            </label>
                            <select v-model="form.modulo"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] outline-none transition focus:border-red-600 focus:ring-2 focus:ring-red-100">
                                <option value="empleos">Empleos</option>
                                <option value="servicios">Servicios</option>
                                <option value="cursos">Cursos y Formación</option>
                                <option value="marketplace">Marketplace</option>
                                <option value="clasificados">Clasificados</option>
                                <option value="emprendimientos">Emprendimientos</option>
                            </select>
                            <p v-if="form.errors.modulo" class="mt-1 text-xs text-red-600 font-medium">
                                {{ form.errors.modulo }}
                            </p>
                        </div>

                        <!-- Motivo -->
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Motivo del Reporte <span class="text-red-500">*</span>
                            </label>
                            <select v-model="form.motivo"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm font-semibold text-[#172A3D] outline-none transition focus:border-red-600 focus:ring-2 focus:ring-red-100">
                                <option value="" disabled>Selecciona el motivo principal</option>
                                <option v-for="motivo in motivosComunes" :key="motivo" :value="motivo">
                                    {{ motivo }}
                                </option>
                            </select>
                            <p v-if="form.errors.motivo" class="mt-1 text-xs text-red-600 font-medium">
                                {{ form.errors.motivo }}
                            </p>
                        </div>

                        <!-- Descripción detallada con Corrección Mágica y Alerta -->
                        <div>
                            <div class="mb-1.5 flex items-center justify-between gap-3">
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Descripción Detallada del Incidente <span class="text-red-500">*</span>
                                </label>
                                <button type="button" @click="aplicarCorreccionMagica"
                                    :disabled="cargandoIA || !form.descripcion.trim()"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition disabled:opacity-50">
                                    <span v-if="cargandoIA" class="animate-spin">⏳</span>
                                    <span v-else>✨</span>
                                    <span>Corrección Mágica</span>
                                </button>
                            </div>
                            <textarea v-model="form.descripcion" rows="5" maxlength="2000"
                                placeholder="Explica detalladamente qué sucedió, incluye contexto o información que ayude a los moderadores..."
                                class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs sm:text-sm leading-relaxed text-[#172A3D] outline-none transition focus:border-red-600 focus:ring-2 focus:ring-red-100"
                                :class="{ 'border-red-500': form.errors.descripcion || alertaDescripcion }"></textarea>

                            <!-- Alerta de palabras prohibidas -->
                            <p v-if="alertaDescripcion" class="mt-1.5 text-xs text-amber-600 font-bold">
                                {{ alertaDescripcion }}
                            </p>

                            <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-600 font-medium">
                                {{ form.errors.descripcion }}
                            </p>
                        </div>

                        <!-- Evidencia (Archivo adjunto) -->
                        <div>
                            <label class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Adjuntar Evidencia (Opcional)
                            </label>
                            <input type="file" accept="image/jpeg,image/png,image/webp,application/pdf"
                                @input="form.evidencia = $event.target.files[0]"
                                class="block w-full rounded-xl border border-slate-300 bg-white text-xs sm:text-sm text-slate-600 file:mr-4 file:border-0 file:bg-red-50 file:px-4 file:py-3 file:font-bold file:text-red-900 hover:file:bg-red-100" />
                            <p class="mt-1.5 text-xs text-slate-500 font-medium">
                                Puedes subir una captura de pantalla (JPG, PNG, WebP o PDF) de máximo 5 MB.
                            </p>
                            <p v-if="form.errors.evidencia" class="mt-1 text-xs text-red-600 font-medium">
                                {{ form.errors.evidencia }}
                            </p>
                        </div>

                    </section>

                    <!-- ACCIONES -->
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end pb-12">
                        <Link :href="route('dashboard')"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50 shadow-xs">
                            Cancelar
                        </Link>

                        <button type="submit" :disabled="form.processing || Boolean(alertaDescripcion)"
                            class="inline-flex items-center justify-center rounded-xl bg-red-600 px-7 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-red-500/25 transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50">
                            {{ form.processing ? 'Enviando reporte...' : 'Enviar Denuncia' }}
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