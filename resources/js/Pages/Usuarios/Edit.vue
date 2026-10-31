<script setup>
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    usuario: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        default: () => [],
    },
});

const rolActual = computed(() => {
    return props.usuario.roles?.[0]?.name ?? '';
});

// Comprobar si actualmente tiene una suspensión activa
const tieneSuspensionActiva = computed(() => {
    if (!props.usuario.bloqueado_hasta) return false;
    return new Date(props.usuario.bloqueado_hasta) > new Date();
});

const formatoFechaHora = (fecha) => {
    if (!fecha) return 'Nunca';
    return new Date(fecha).toLocaleString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

// Si tiene suspensión activa, por defecto inicia en 'mantener'.
// Si no tiene suspensión activa, por defecto inicia en 'mantener' (sin acción).
const form = useForm({
    name: props.usuario.name ?? '',
    email: props.usuario.email ?? '',
    role: rolActual.value,
    activo: props.usuario.activo === 1 || props.usuario.activo === true || props.usuario.activo === '1',
    accion_bloqueo: 'mantener', // 'mantener' | 'quitar' | 'aplicar'
    dias_bloqueo: 7,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        activo: data.activo ? 1 : 0,
    })).put(route('usuarios.update', props.usuario.id), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head :title="`Editar Usuario - ${usuario.name}`" />

    <AuthenticatedLayout>
        <div class="min-h-screen p-6 lg:p-8">
            <div class="mx-auto max-w-4xl space-y-6 bg-[#C27AFF]/50 p-3 rounded-xl">

                <!-- ENCABEZADO -->
                <div
                    class="flex flex-col bg-white border border-slate-200 p-6 rounded-xl justify-between gap-4 sm:flex-row sm:items-center shadow-xs">
                    <div>
                        <Link :href="route('usuarios.show', usuario.id)"
                            class="mb-2 inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-500 hover:text-[#263646] transition">
                            ← Volver al detalle del usuario
                        </Link>
                        <h1 class="text-2xl font-bold text-[#263646]">
                            Editar usuario: {{ usuario.name }}
                        </h1>
                        <p class="mt-1 text-sm text-[#526174]">
                            Modifica información de contacto, asignación de rol y el estado de moderación o activación
                            de la cuenta.
                        </p>
                    </div>
                </div>

                <!-- FORMULARIO -->
                <div class="rounded-xl bg-white p-6 sm:p-8 shadow-xs border border-slate-200">
                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- NOMBRE COMPLETO -->
                        <div>
                            <label for="name"
                                class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Nombre completo <span class="text-red-500">*</span>
                            </label>
                            <input id="name" v-model="form.name" type="text" required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#263646] outline-none transition focus:border-red-600 focus:ring-2 focus:ring-red-100"
                                placeholder="Ej. Juan Pérez" />
                            <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-600 font-medium">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- CORREO ELECTRÓNICO -->
                        <div>
                            <label for="email"
                                class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Correo electrónico <span class="text-red-500">*</span>
                            </label>
                            <input id="email" v-model="form.email" type="email" required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#263646] outline-none transition focus:border-red-600 focus:ring-2 focus:ring-red-100"
                                placeholder="correo@ejemplo.com" />
                            <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-600 font-medium">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- ROL DEL SISTEMA -->
                        <div>
                            <label for="role"
                                class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Rol asignado <span class="text-red-500">*</span>
                            </label>
                            <select id="role" v-model="form.role" required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-[#263646] outline-none transition focus:border-red-600 focus:ring-2 focus:ring-red-100">
                                <option value="" disabled>Selecciona un rol...</option>
                                <option v-for="rol in roles" :key="rol.id" :value="rol.name">
                                    {{ rol.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.role" class="mt-1.5 text-xs text-red-600 font-medium">
                                {{ form.errors.role }}
                            </p>
                        </div>

                        <!-- ESTADO GENERAL DE LA CUENTA -->
                        <div class="rounded-xl border p-4 transition-colors"
                            :class="form.activo ? 'border-emerald-200 bg-emerald-50/30' : 'border-rose-200 bg-rose-50/30'">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-bold"
                                        :class="form.activo ? 'text-emerald-900' : 'text-rose-900'">
                                        Estado de la cuenta: {{ form.activo ? 'Habilitada' : 'Inhabilitada' }}
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ form.activo
                                            ? 'El usuario puede iniciar sesión e interactuar en la plataforma.'
                                            : 'El acceso al sistema queda revocado y sus publicaciones no serán públicas.'
                                        }}
                                    </p>
                                </div>

                                <button type="button" @click="form.activo = !form.activo"
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                    :class="form.activo ? 'bg-emerald-600' : 'bg-slate-300'">
                                    <span
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-xs ring-0 transition duration-200 ease-in-out"
                                        :class="form.activo ? 'translate-x-5' : 'translate-x-0'" />
                                </button>
                            </div>
                            <p v-if="form.errors.activo" class="mt-2 text-xs text-red-600 font-medium">
                                {{ form.errors.activo }}
                            </p>
                        </div>

                        <!-- GESTIÓN DE SUSPENSIÓN / BLOQUEO TEMPORAL -->
                        <div class="rounded-xl border p-5 transition-all"
                            :class="tieneSuspensionActiva ? 'border-amber-300 bg-amber-50/40' : 'border-slate-200 bg-slate-50/60'">

                            <!-- ENCABEZADO DE SUSPENSIÓN -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b pb-3 mb-4"
                                :class="tieneSuspensionActiva ? 'border-amber-200' : 'border-slate-200'">
                                <div>
                                    <h3 class="text-sm font-bold flex items-center gap-2"
                                        :class="tieneSuspensionActiva ? 'text-amber-950' : 'text-slate-800'">
                                        <span>{{ tieneSuspensionActiva ? '⏳ Suspensión Temporal Activa' : '🛡️ Estado de Moderación' }}</span>
                                    </h3>
                                    <p class="text-xs text-slate-600 mt-0.5">
                                        {{ tieneSuspensionActiva
                                            ? 'Este usuario tiene restringida la creación de nuevas publicaciones hasta la fecha indicada.' : 'El usuario actualmente no posee sanciones ni restricciones temporales activas.' }}
                                    </p>
                                </div>

                                <span v-if="tieneSuspensionActiva"
                                    class="inline-flex self-start sm:self-auto rounded-full bg-amber-200/90 px-3 py-1 text-[11px] font-bold text-amber-950 shadow-2xs">
                                    Vence: {{ formatoFechaHora(usuario.bloqueado_hasta) }}
                                </span>
                            </div>

                            <!-- CASO A: USUARIO CON SUSPENSIÓN ACTIVA -->
                            <div v-if="tieneSuspensionActiva" class="grid gap-3 sm:grid-cols-3">

                                <!-- Opción 1: Mantener -->
                                <label
                                    class="relative flex flex-col justify-between rounded-xl border p-4 cursor-pointer transition"
                                    :class="form.accion_bloqueo === 'mantener'
                                        ? 'border-amber-500 bg-white ring-2 ring-amber-100 shadow-xs'
                                        : 'border-slate-200 bg-white/70 hover:bg-white'">
                                    <div class="flex items-start gap-2.5">
                                        <input type="radio" v-model="form.accion_bloqueo" value="mantener"
                                            class="mt-0.5 text-amber-600 focus:ring-amber-500" />
                                        <div>
                                            <span class="block text-xs font-bold text-slate-800">Mantener
                                                suspensión</span>
                                            <span class="block text-[11px] text-slate-500 mt-0.5 leading-tight">
                                                Conservar la fecha de sanción programada.
                                            </span>
                                        </div>
                                    </div>
                                </label>

                                <!-- Opción 2: Quitar / Desbloquear -->
                                <label
                                    class="relative flex flex-col justify-between rounded-xl border p-4 cursor-pointer transition"
                                    :class="form.accion_bloqueo === 'quitar'
                                        ? 'border-emerald-600 bg-emerald-50/60 ring-2 ring-emerald-100 shadow-xs'
                                        : 'border-slate-200 bg-white/70 hover:bg-white'">
                                    <div class="flex items-start gap-2.5">
                                        <input type="radio" v-model="form.accion_bloqueo" value="quitar"
                                            class="mt-0.5 text-emerald-600 focus:ring-emerald-500" />
                                        <div>
                                            <span class="block text-xs font-bold text-emerald-900">Levantar
                                                sanción</span>
                                            <span class="block text-[11px] text-emerald-700/80 mt-0.5 leading-tight">
                                                Restaurar permisos y reactivar todas sus publicaciones.
                                            </span>
                                        </div>
                                    </div>
                                </label>

                                <!-- Opción 3: Extender / Redefinir -->
                                <label
                                    class="relative flex flex-col justify-between rounded-xl border p-4 cursor-pointer transition"
                                    :class="form.accion_bloqueo === 'aplicar'
                                        ? 'border-rose-600 bg-white ring-2 ring-rose-100 shadow-xs'
                                        : 'border-slate-200 bg-white/70 hover:bg-white'">
                                    <div class="flex items-start gap-2.5">
                                        <input type="radio" v-model="form.accion_bloqueo" value="aplicar"
                                            class="mt-0.5 text-rose-600 focus:ring-rose-500" />
                                        <div>
                                            <span class="block text-xs font-bold text-rose-900">Redefinir días</span>
                                            <span class="block text-[11px] text-slate-500 mt-0.5 leading-tight">
                                                Ajustar una nueva duración a partir de hoy.
                                            </span>
                                        </div>
                                    </div>
                                </label>

                            </div>

                            <!-- CASO B: USUARIO SIN SUSPENSIÓN ACTIVA -->
                            <div v-else class="grid gap-3 sm:grid-cols-2">

                                <!-- Opción 1: Sin suspensión -->
                                <label
                                    class="relative flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition"
                                    :class="form.accion_bloqueo === 'mantener'
                                        ? 'border-slate-400 bg-white ring-2 ring-slate-100 shadow-xs'
                                        : 'border-slate-200 bg-white/70 hover:bg-white'">
                                    <input type="radio" v-model="form.accion_bloqueo" value="mantener"
                                        class="mt-0.5 text-slate-800 focus:ring-slate-500" />
                                    <div>
                                        <span class="block text-xs font-bold text-slate-800">Sin sanción (Normal)</span>
                                        <span class="block text-[11px] text-slate-500 mt-0.5 leading-tight">
                                            El usuario publica libremente sin restricciones de moderación.
                                        </span>
                                    </div>
                                </label>

                                <!-- Opción 2: Aplicar nueva suspensión -->
                                <label
                                    class="relative flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition"
                                    :class="form.accion_bloqueo === 'aplicar'
                                        ? 'border-rose-600 bg-rose-50/40 ring-2 ring-rose-100 shadow-xs'
                                        : 'border-slate-200 bg-white/70 hover:bg-white'">
                                    <input type="radio" v-model="form.accion_bloqueo" value="aplicar"
                                        class="mt-0.5 text-rose-600 focus:ring-rose-500" />
                                    <div>
                                        <span class="block text-xs font-bold text-rose-900">Aplicar suspensión
                                            temporal</span>
                                        <span class="block text-[11px] text-rose-700/80 mt-0.5 leading-tight">
                                            Bloquear temporalmente permisos y pausar publicaciones.
                                        </span>
                                    </div>
                                </label>

                            </div>

                            <!-- SELECTOR DE DÍAS (SOLO SI SE SELECCIONÓ 'APLICAR') -->
                            <div v-if="form.accion_bloqueo === 'aplicar'"
                                class="mt-4 pt-4 border-t border-slate-200/80 animate-in fade-in duration-150">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                    Cantidad de días de suspensión:
                                </label>
                                <div class="flex items-center gap-3 max-w-xs">
                                    <input v-model.number="form.dias_bloqueo" type="number" min="1" max="365" required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-800 outline-none focus:border-rose-500 focus:ring-2 focus:ring-rose-100 shadow-2xs" />
                                    <span class="text-xs text-slate-500 font-semibold shrink-0">días desde hoy</span>
                                </div>
                                <p v-if="form.errors.dias_bloqueo" class="mt-1 text-xs text-red-600 font-medium">
                                    {{ form.errors.dias_bloqueo }}
                                </p>
                            </div>

                        </div>

                        <!-- BOTONES DE ACCIÓN -->
                        <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                            <Link :href="route('usuarios.show', usuario.id)"
                                class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                                Cancelar
                            </Link>

                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center justify-center rounded-xl bg-[#263646] px-6 py-2.5 text-sm font-bold text-white shadow-md transition hover:bg-[#1d2936] disabled:opacity-50">
                                {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>