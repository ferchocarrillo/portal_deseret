<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    servicio: {
        type: Object,
        required: true,
    },
    conversacion: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const usuarioActual = computed(() => page.props.auth?.user ?? null);
const contenedorMensajes = ref(null);

// ==========================================================
// IDENTIFICAR A LA CONTRAPARTE
// ==========================================================
const otroUsuario = computed(() => {
    const miId = Number(usuarioActual.value?.id);
    if (Number(props.conversacion.usuario_interesado_id) === miId) {
        return props.conversacion.propietario ?? props.servicio?.user;
    }
    return props.conversacion.interesado;
});

// ==========================================================
// AVATAR DE LA CONTRAPARTE
// ==========================================================
const avatarOtroUsuario = computed(() => {
    const usuario = otroUsuario.value;
    if (!usuario) {
        return null;
    }

    const foto = usuario.foto ?? usuario.profile_photo_url ?? usuario.avatar ?? usuario.imagen;

    if (!foto) {
        return null;
    }

    if (
        foto.startsWith('http://') ||
        foto.startsWith('https://') ||
        foto.startsWith('/')
    ) {
        return foto;
    }

    return `/storage/${foto}`;
});

const esMensajePropio = (mensaje) => {
    return Number(mensaje.user_id) === Number(usuarioActual.value?.id);
};

// ==========================================================
// FORMULARIO DE ENVÍO DE MENSAJE
// ==========================================================
const form = useForm({
    mensaje: '',
});

const scrollAlFondo = () => {
    nextTick(() => {
        if (contenedorMensajes.value) {
            contenedorMensajes.value.scrollTop = contenedorMensajes.value.scrollHeight;
        }
    });
};

const enviarMensaje = () => {
    if (!form.mensaje.trim() || form.processing || !props.conversacion?.id) return;

    form.post(route('servicios.mensajes.store', props.conversacion.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('mensaje');
            scrollAlFondo();
        },
    });
};

const manejarEnter = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        enviarMensaje();
    }
};

// ==========================================================
// POLLING Y CICLO DE VIDA
// ==========================================================
let intervalPolling = null;

const recargarConversacion = () => {
    if (document.visibilityState === 'hidden' || form.processing) {
        return;
    }

    router.reload({
        only: ['conversacion'],
        preserveScroll: true,
        preserveState: true,
    });
};

onMounted(() => {
    scrollAlFondo();
    intervalPolling = window.setInterval(recargarConversacion, 3500);
});

onBeforeUnmount(() => {
    if (intervalPolling) {
        window.clearInterval(intervalPolling);
        intervalPolling = null;
    }
});

watch(() => props.conversacion.mensajes?.length, () => {
    scrollAlFondo();
});

// ==========================================================
// FORMATOS
// ==========================================================
const formatearHora = (fechaRaw) => {
    if (!fechaRaw) return '';
    const fecha = new Date(fechaRaw);
    if (Number.isNaN(fecha.getTime())) return '';
    return fecha.toLocaleTimeString('es-CO', {
        hour: '2-digit',
        minute: '2-digit',
    });
};

const imagenServicio = computed(() => {
    const s = props.servicio;
    if (!s?.imagen) return null;
    if (s.imagen.startsWith('http://') || s.imagen.startsWith('https://') || s.imagen.startsWith('/')) {
        return s.imagen;
    }
    return `/storage/${s.imagen}`;
});
</script>

<template>

    <Head :title="`Chat con ${otroUsuario?.name ?? 'Usuario'} | ${servicio.titulo}`" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">

        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

                <!-- NAVEGACIÓN SUPERIOR -->
                <div class="mb-4 flex items-center justify-between">
                    <Link :href="route('mensajes.index')"
                        class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-emerald-950">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Volver a mensajes
                    </Link>

                    <Link :href="route('servicios.show', servicio.id)"
                        class="text-xs sm:text-sm font-bold text-emerald-900 hover:underline">
                        Ver publicación del servicio →
                    </Link>
                </div>

                <!-- CONTENEDOR PRINCIPAL DEL CHAT -->
                <div
                    class="grid grid-cols-1 overflow-hidden rounded-3xl border border-slate-300 bg-white shadow-2xl shadow-emerald-950/15 lg:grid-cols-4">

                    <!-- RESUMEN DEL SERVICIO Y CONTRAPARTE (LATERAL / SUPERIOR EN MÓVIL) -->
                    <div
                        class="border-b border-slate-200 bg-emerald-950/5 p-4 lg:border-b-0 lg:border-r lg:p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <!-- AVATAR DE LA FOTO REAL O INICIAL -->
                                <div
                                    class="flex h-12 w-12 shrink-0 overflow-hidden rounded-2xl bg-emerald-900 text-lg font-black text-white shadow-xs items-center justify-center">
                                    <img v-if="avatarOtroUsuario" :src="avatarOtroUsuario" alt="Avatar"
                                        class="h-full w-full object-cover" />
                                    <span v-else>
                                        {{ otroUsuario?.name?.charAt(0)?.toUpperCase() ?? 'U' }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Conversando con
                                    </p>
                                    <p class="truncate font-black text-[#172A3D]">
                                        {{ otroUsuario?.name ?? 'Usuario' }}
                                    </p>
                                </div>
                            </div>

                            <div class="my-5 border-t border-slate-200"></div>

                            <!-- Miniatura del servicio -->
                            <div class="space-y-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Servicio vinculado
                                </p>
                                <div class="flex items-start gap-3">
                                    <div
                                        class="h-12 w-12 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs">
                                        <img v-if="imagenServicio" :src="imagenServicio" :alt="servicio.titulo"
                                            class="h-full w-full object-cover" />
                                        <div v-else class="flex h-full w-full items-center justify-center text-lg">
                                            🛠️
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="line-clamp-2 text-xs font-bold text-[#172A3D]">
                                            {{ servicio.titulo }}
                                        </p>
                                        <span
                                            class="mt-1 inline-block rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-[10px] font-bold text-emerald-900">
                                            {{ servicio.categoria }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-200 hidden lg:block">
                            <p class="text-[11px] leading-relaxed text-slate-400 font-medium">
                                Mantén la comunicación dentro de Portal Deseret para garantizar una experiencia segura.
                            </p>
                        </div>
                    </div>

                    <!-- ÁREA DE CONVERSACIÓN -->
                    <div class="flex flex-col lg:col-span-3 bg-white">

                        <!-- LISTA DE MENSAJES -->
                        <div ref="contenedorMensajes" class="flex-1 space-y-4 overflow-y-auto p-4 sm:p-6 bg-slate-50/50"
                            style="max-height: 520px; min-height: 380px;">
                            <div v-if="!conversacion.mensajes?.length"
                                class="flex h-full items-center justify-center text-center">
                                <div class="p-6">
                                    <div class="text-3xl">👋</div>
                                    <p class="mt-2 text-sm font-bold text-[#172A3D]">Comienza la conversación</p>
                                    <p class="mt-1 text-xs text-slate-500 font-medium">Pregunta por disponibilidad,
                                        tiempos de entrega o alcance del servicio.</p>
                                </div>
                            </div>

                            <template v-else>
                                <div v-for="msg in conversacion.mensajes" :key="msg.id" class="flex w-full"
                                    :class="esMensajePropio(msg) ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-[78%] rounded-2xl px-4 py-3 shadow-sm sm:max-w-[68%]"
                                        :class="esMensajePropio(msg)
                                            ? 'bg-emerald-950 text-white rounded-br-none shadow-md shadow-emerald-950/10'
                                            : 'bg-white text-slate-800 rounded-bl-none border border-slate-200 shadow-xs'">
                                        <p
                                            class="whitespace-pre-wrap break-words text-xs sm:text-sm leading-relaxed font-medium">
                                            {{ msg.mensaje }}
                                        </p>
                                        <div class="mt-1 flex items-center justify-end gap-1 text-[10px] font-bold"
                                            :class="esMensajePropio(msg) ? 'text-emerald-300' : 'text-slate-400'">
                                            <span>{{ formatearHora(msg.created_at) }}</span>
                                            <span v-if="esMensajePropio(msg)">
                                                {{ msg.leido_at ? '✓✓' : '✓' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- CAJA DE TEXTO PARA RESPONDER -->
                        <div class="border-t border-slate-200 bg-white p-3 sm:p-4">
                            <form @submit.prevent="enviarMensaje" class="flex items-end gap-2">
                                <textarea v-model="form.mensaje" @keydown="manejarEnter" rows="2" maxlength="2000"
                                    placeholder="Escribe un mensaje... (Presiona Enter para enviar)"
                                    class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-xs sm:text-sm text-[#172A3D] placeholder-slate-400 focus:border-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-100 font-medium"></textarea>

                                <button type="submit" :disabled="form.processing || !form.mensaje.trim()"
                                    class="flex h-11 shrink-0 items-center justify-center rounded-xl bg-emerald-950 px-5 text-xs sm:text-sm font-black text-white transition hover:bg-emerald-900 disabled:opacity-50 shadow-xs">
                                    <svg class="h-4 w-4 rotate-90" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <span class="ml-1.5 hidden sm:inline">Enviar</span>
                                </button>
                            </form>
                            <p class="mt-1.5 text-[11px] text-slate-400 font-medium">
                                Presiona Enter para enviar, Shift + Enter para salto de línea.
                            </p>
                        </div>
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