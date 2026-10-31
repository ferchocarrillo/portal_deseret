<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    oferta: {
        type: Object,
        required: true,
    },
})

const form = useForm({
    titulo: props.oferta.titulo ?? '',
    descripcion: props.oferta.descripcion ?? '',
    modalidad: props.oferta.modalidad ?? '',

    tipo_salario: props.oferta.tipo_salario ?? 'a_convenir',
    salario_monto: props.oferta.salario_monto ?? '',
    mostrar_salario: Boolean(props.oferta.mostrar_salario),
})

const guardar = () => {
    form.put(route('empleos.update', props.oferta.id))
}
</script>

<template>

    <Head :title="`Editar: ${oferta.titulo} | Empleos`" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">

        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ======================================================
                 ENCABEZADO CON TONO AMARILLO/ÁMBAR TRANSLÚCIDO
            ======================================================= -->
            <section
                class="border-b border-amber-200/80 bg-amber-100/25 backdrop-blur-md shadow-lg shadow-amber-500/10">
                <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-[#D99A24]"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-900">
                                    Modificación de oferta
                                </span>
                            </div>
                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Editar oferta de empleo
                            </h1>
                            <p class="mt-1 text-sm text-slate-600 font-medium">
                                Actualiza la información de tu oferta publicada.
                            </p>
                        </div>

                        <Link :href="route('empleos.mis-ofertas')"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-[#172A3D] shadow-sm hover:bg-slate-50 transition">
                            ← Volver a mis ofertas
                        </Link>
                    </div>
                </div>
            </section>

            <!-- ======================================================
                 CONTENIDO PRINCIPAL
            ======================================================= -->
            <div class="py-8">
                <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6 bg-[#D99A24]/20 rounded-xl p-3 mt-2">

                    <form @submit.prevent="guardar"
                        class="space-y-6 rounded-2xl border border-slate-300 bg-white p-6 shadow-2xl shadow-amber-500/15 sm:p-8">

                        <!-- Título -->
                        <div>
                            <label for="titulo"
                                class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Título de la oferta <span class="text-red-500">*</span>
                            </label>

                            <input id="titulo" v-model="form.titulo" type="text" required maxlength="255"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none placeholder:text-slate-400 focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100 transition" />

                            <p v-if="form.errors.titulo" class="mt-1 text-xs text-red-600 font-medium">
                                {{ form.errors.titulo }}
                            </p>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion"
                                class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Descripción <span class="text-red-500">*</span>
                            </label>

                            <textarea id="descripcion" v-model="form.descripcion" rows="8" required
                                class="w-full resize-y rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-6 text-[#172A3D] outline-none placeholder:text-slate-400 focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100 transition"></textarea>

                            <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-600 font-medium">
                                {{ form.errors.descripcion }}
                            </p>
                        </div>

                        <!-- Modalidad -->
                        <div>
                            <label for="modalidad"
                                class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Modalidad de trabajo <span class="text-red-500">*</span>
                            </label>

                            <select id="modalidad" v-model="form.modalidad" required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100 transition">
                                <option value="" disabled>
                                    Selecciona una modalidad
                                </option>
                                <option value="Presencial">Presencial</option>
                                <option value="Híbrido">Híbrido</option>
                                <option value="Remoto">Remoto</option>
                            </select>

                            <p v-if="form.errors.modalidad" class="mt-1 text-xs text-red-600 font-medium">
                                {{ form.errors.modalidad }}
                            </p>
                        </div>

                        <!-- Configuración salarial -->
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 sm:p-6">
                            <h2 class="text-base font-bold text-[#172A3D]">
                                Información salarial
                            </h2>

                            <p class="mt-1 text-xs sm:text-sm text-slate-500 font-medium">
                                Configura cómo se manejará la remuneración de esta oferta.
                            </p>

                            <div class="mt-5">
                                <label for="tipo_salario"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Tipo de salario <span class="text-red-500">*</span>
                                </label>

                                <select id="tipo_salario" v-model="form.tipo_salario" required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100 transition">
                                    <option value="smlmv">
                                        Salario mínimo legal vigente (SMLMV)
                                    </option>
                                    <option value="salario_especifico">
                                        Salario específico
                                    </option>
                                    <option value="a_convenir">
                                        A convenir
                                    </option>
                                </select>

                                <p v-if="form.errors.tipo_salario" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.tipo_salario }}
                                </p>
                            </div>

                            <!-- Monto -->
                            <div v-if="form.tipo_salario === 'salario_especifico'" class="mt-5">
                                <label for="salario_monto"
                                    class="mb-1.5 block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Salario mensual (COP) <span class="text-red-500">*</span>
                                </label>

                                <input id="salario_monto" v-model="form.salario_monto" type="number" min="0" step="1"
                                    required placeholder="Ej. 3500000"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#172A3D] outline-none placeholder:text-slate-400 focus:border-[#D99A24] focus:ring-2 focus:ring-amber-100 transition" />

                                <p v-if="form.errors.salario_monto" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.salario_monto }}
                                </p>
                            </div>

                            <!-- Visibilidad del salario -->
                            <div class="mt-5">
                                <label
                                    class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white p-4">
                                    <input v-model="form.mostrar_salario" type="checkbox"
                                        class="mt-0.5 h-4 w-4 rounded border-slate-300 text-[#D99A24] focus:ring-[#D99A24]" />

                                    <span>
                                        <span class="block text-xs sm:text-sm font-bold text-slate-700">
                                            Mostrar información salarial
                                        </span>

                                        <span class="mt-0.5 block text-xs text-slate-500 font-medium">
                                            Permite que los candidatos vean la información salarial en la oferta
                                            publicada.
                                        </span>
                                    </span>
                                </label>

                                <p v-if="form.errors.mostrar_salario" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.mostrar_salario }}
                                </p>
                            </div>
                        </div>

                        <!-- Errores generales -->
                        <div v-if="form.hasErrors"
                            class="rounded-2xl border border-red-200 bg-red-50 p-5 shadow-lg shadow-red-500/10">
                            <h3 class="font-bold text-red-900">
                                Revisa la información
                            </h3>
                            <p class="mt-1 text-xs sm:text-sm text-red-700 font-medium">
                                Hay campos que requieren corrección antes de guardar los cambios.
                            </p>
                        </div>

                        <!-- Acciones -->
                        <div
                            class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">
                            <Link :href="route('empleos.mis-ofertas')"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-xs sm:text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                                Cancelar
                            </Link>

                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center justify-center rounded-xl bg-[#D99A24] px-7 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-amber-500/20 transition hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-50">
                                {{
                                    form.processing
                                        ? 'Guardando...'
                                        : 'Guardar cambios'
                                }}
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