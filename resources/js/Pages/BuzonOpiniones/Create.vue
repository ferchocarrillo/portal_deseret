<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    tipo: 'sugerencia',
    modulo: 'General',
    asunto: '',
    mensaje: '',
});

const submit = () => {
    form.post(route('buzon.store'), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Buzón de Opinión" />

    <AuthenticatedLayout>
        <div class="min-h-screen p-6 lg:p-8">
            <div class="mx-auto max-w-2xl space-y-6">

                <!-- ENCABEZADO -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                    <Link :href="route('dashboard')"
                        class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                        ← Volver al inicio
                    </Link>
                    <div class="flex items-center gap-3">
                        <span
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-2xl text-amber-600">
                            📬
                        </span>
                        <div>
                            <h1 class="text-xl font-bold text-slate-900">
                                Buzón de Sugerencias y Opiniones
                            </h1>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Comparte ideas de mejora, comentarios o felicitaciones con la administración.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FORMULARIO -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- TIPO DE MENSAJE -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                ¿Qué tipo de mensaje deseas enviar? <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                <label :class="[
                                    'flex flex-col items-center justify-center gap-1 p-3.5 rounded-xl border-2 cursor-pointer transition text-center',
                                    form.tipo === 'sugerencia' ? 'border-amber-500 bg-amber-50/40 text-amber-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-600'
                                ]">
                                    <input type="radio" v-model="form.tipo" value="sugerencia" class="sr-only" />
                                    <span class="text-xl">💡</span>
                                    <span class="text-xs">Sugerencia</span>
                                </label>

                                <label :class="[
                                    'flex flex-col items-center justify-center gap-1 p-3.5 rounded-xl border-2 cursor-pointer transition text-center',
                                    form.tipo === 'queja' ? 'border-rose-500 bg-rose-50/40 text-rose-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-600'
                                ]">
                                    <input type="radio" v-model="form.tipo" value="queja" class="sr-only" />
                                    <span class="text-xl">⚠️</span>
                                    <span class="text-xs">Queja / Inconformidad</span>
                                </label>

                                <label :class="[
                                    'flex flex-col items-center justify-center gap-1 p-3.5 rounded-xl border-2 cursor-pointer transition text-center',
                                    form.tipo === 'felicitacion' ? 'border-emerald-500 bg-emerald-50/40 text-emerald-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-600'
                                ]">
                                    <input type="radio" v-model="form.tipo" value="felicitacion" class="sr-only" />
                                    <span class="text-xl">👏</span>
                                    <span class="text-xs">Felicitación</span>
                                </label>
                            </div>
                        </div>

                        <!-- MÓDULO -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Módulo o área relacionada
                            </label>
                            <select v-model="form.modulo" required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 outline-none focus:border-slate-500">
                                <option value="General">Plataforma General</option>
                                <option value="Marketplace">Marketplace</option>
                                <option value="Servicios">Servicios</option>
                                <option value="Clasificados">Clasificados</option>
                                <option value="Emprendimientos">Emprendimientos</option>
                                <option value="Cursos">Cursos</option>
                                <option value="Empleos">Empleos</option>
                                <option value="Atención">Atención y Soporte</option>
                            </select>
                        </div>

                        <!-- ASUNTO -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Asunto <span class="text-rose-500">*</span>
                            </label>
                            <input v-model="form.asunto" type="text" required
                                placeholder="Ej: Idea para mejorar los filtros de búsqueda en servicios"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-slate-500" />
                            <p v-if="form.errors.asunto" class="mt-1 text-xs text-rose-600 font-medium">{{
                                form.errors.asunto }}</p>
                        </div>

                        <!-- MENSAJE -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Tu mensaje <span class="text-rose-500">*</span>
                            </label>
                            <textarea v-model="form.mensaje" rows="5" required
                                placeholder="Escribe aquí con libertad tus comentarios, sugerencias de nuevas funciones o tu experiencia..."
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-slate-500"></textarea>
                            <p v-if="form.errors.mensaje" class="mt-1 text-xs text-rose-600 font-medium">{{
                                form.errors.mensaje }}</p>
                        </div>

                        <!-- BOTONES -->
                        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                            <Link :href="route('dashboard')"
                                class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                Cancelar
                            </Link>
                            <button type="submit" :disabled="form.processing"
                                class="rounded-xl bg-[#263646] px-6 py-2.5 text-xs sm:text-sm font-bold text-white shadow-xs hover:bg-[#1d2936] transition disabled:opacity-50">
                                {{ form.processing ? 'Enviando...' : 'Enviar Mensaje' }}
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>