<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    modulo: 'General',
    titulo: '',
    severidad: 'media',
    descripcion: '',
    pasos_reproducir: '',
    url_afectada: window.location.referrer || '',
    captura: null,
});

const submit = () => {
    form.post(route('reportes-errores.store'), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Reportar un Problema Técnico" />

    <AuthenticatedLayout>
        <div class="min-h-screen p-6 lg:p-8">
            <div class="mx-auto max-w-3xl space-y-6">

                <!-- ENCABEZADO -->
                <div class="flex flex-col bg-white border border-slate-200 p-6 rounded-2xl shadow-xs">
                    <Link :href="route('dashboard')"
                        class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                        ← Volver al inicio
                    </Link>
                    <div class="flex items-center gap-3">
                        <span
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-xl text-rose-600">
                            🛠️
                        </span>
                        <div>
                            <h1 class="text-xl font-bold text-slate-900">
                                Reportar una Incidencia o Error
                            </h1>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Describe la falla encontrada para que el equipo técnico pueda reproducirla y corregirla.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FORMULARIO -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- MÓDULO Y SEVERIDAD -->
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                    Módulo afectado <span class="text-rose-500">*</span>
                                </label>
                                <select v-model="form.modulo" required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 outline-none focus:border-slate-500">
                                    <option value="General">Plataforma General</option>
                                    <option value="Autenticación">Inicio de sesión / Registro</option>
                                    <option value="Marketplace">Marketplace</option>
                                    <option value="Servicios">Servicios</option>
                                    <option value="Clasificados">Clasificados</option>
                                    <option value="Emprendimientos">Emprendimientos</option>
                                    <option value="Cursos">Cursos</option>
                                    <option value="Empleos">Empleos</option>
                                    <option value="Mensajería">Mensajería / Chats</option>
                                    <option value="Perfil">Mi Perfil / Hoja de Vida</option>
                                </select>
                                <p v-if="form.errors.modulo" class="mt-1 text-xs text-rose-600 font-medium">{{
                                    form.errors.modulo }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                    Severidad estimada <span class="text-rose-500">*</span>
                                </label>
                                <select v-model="form.severidad" required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 outline-none focus:border-slate-500">
                                    <option value="baja">Baja (Visual, texto, ortografía)</option>
                                    <option value="media">Media (Función secundaria con fallas)</option>
                                    <option value="alta">Alta (Impide completar un proceso clave)</option>
                                    <option value="critica">Crítica (Bloqueo total o error de servidor)</option>
                                </select>
                                <p v-if="form.errors.severidad" class="mt-1 text-xs text-rose-600 font-medium">{{
                                    form.errors.severidad }}</p>
                            </div>
                        </div>

                        <!-- TÍTULO -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Título del problema <span class="text-rose-500">*</span>
                            </label>
                            <input v-model="form.titulo" type="text" required
                                placeholder="Ej: No se cargan las imágenes al publicar en Clasificados"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-slate-500" />
                            <p v-if="form.errors.titulo" class="mt-1 text-xs text-rose-600 font-medium">{{
                                form.errors.titulo }}</p>
                        </div>

                        <!-- DESCRIPCIÓN -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                ¿Qué sucedió? (Descripción) <span class="text-rose-500">*</span>
                            </label>
                            <textarea v-model="form.descripcion" rows="4" required
                                placeholder="Explica detalladamente qué estabas intentando hacer y qué error o comportamiento incorrecto obtuviste..."
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-slate-500"></textarea>
                            <p v-if="form.errors.descripcion" class="mt-1 text-xs text-rose-600 font-medium">{{
                                form.errors.descripcion }}</p>
                        </div>

                        <!-- PASOS PARA REPRODUCIR -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Pasos para reproducirlo (Opcional)
                            </label>
                            <textarea v-model="form.pasos_reproducir" rows="3"
                                placeholder="1. Fui a la sección X&#10;2. Hice clic en el botón Y&#10;3. Apareció la alerta de error..."
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-slate-500"></textarea>
                        </div>

                        <!-- URL AFECTADA -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Enlace / URL donde ocurrió (Opcional)
                            </label>
                            <input v-model="form.url_afectada" type="text"
                                placeholder="https://ejemplo.com/clasificados/crear"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-slate-500" />
                        </div>

                        <!-- CAPTURA DE PANTALLA -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Captura de pantalla o evidencia (Opcional, máx 4MB)
                            </label>
                            <input type="file" accept="image/*" @input="form.captura = $event.target.files[0]"
                                class="w-full rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-xs text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-800 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-white hover:file:bg-slate-900" />
                            <p v-if="form.errors.captura" class="mt-1 text-xs text-rose-600 font-medium">{{
                                form.errors.captura }}</p>
                        </div>

                        <!-- BOTONES -->
                        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                            <Link :href="route('dashboard')"
                                class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                Cancelar
                            </Link>

                            <button type="submit" :disabled="form.processing"
                                class="rounded-xl bg-[#263646] px-6 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm hover:bg-[#1d2936] transition disabled:opacity-50">
                                {{ form.processing ? 'Enviando...' : 'Enviar Reporte' }}
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>