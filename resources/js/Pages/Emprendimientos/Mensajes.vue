<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { route } from 'ziggy-js';

const props = defineProps({
    conversacion: {
        type: Object,
        required: true,
    },

    mensajes: {
        type: Array,
        default: () => [],
    },

    usuarioActualId: {
        type: Number,
        default: null,
    },
});

const page = usePage();

const nuevoMensaje = useForm({
    mensaje: '',
});

const mensajesContainer = ref(null);
const enviando = ref(false);

const currentUserId = computed(() => {
    return props.usuarioActualId ?? page.props.auth?.user?.id ?? null;
});

const obtenerOtroUsuario = () => {
    if (
        currentUserId.value &&
        Number(props.conversacion.usuario_iniciador_id) ===
        Number(currentUserId.value)
    ) {
        return props.conversacion.usuarioReceptor ?? props.conversacion.usuario_receptor;
    }

    return props.conversacion.usuarioIniciador ?? props.conversacion.usuario_iniciador;
};

const avatarOtroUsuario = computed(() => {
    const usuario = obtenerOtroUsuario();
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

const nombreOtroUsuario = () => {
    const usuario = obtenerOtroUsuario();
    if (!usuario) {
        return 'Usuario';
    }

    if (usuario.name) {
        return usuario.name;
    }

    const nombreCompleto = `${usuario.nombres || ''} ${usuario.apellidos || ''}`.trim();
    return nombreCompleto || 'Usuario';
};
const inicialOtroUsuario = () => {
    return nombreOtroUsuario().charAt(0).toUpperCase();
};

const esMensajePropio = (mensaje) => {
    if (!currentUserId.value) {
        return false;
    }

    return Number(mensaje.user_id) === Number(currentUserId.value);
};

const formatearHora = (fecha) => {
    if (!fecha) {
        return '';
    }

    return new Date(fecha).toLocaleTimeString('es-CO', {
        hour: '2-digit',
        minute: '2-digit',
    });
};

const desplazarAlFinal = async (suave = false) => {
    await nextTick();

    if (!mensajesContainer.value) {
        return;
    }

    mensajesContainer.value.scrollTo({
        top: mensajesContainer.value.scrollHeight,
        behavior: suave ? 'smooth' : 'auto',
    });
};

const enviarMensaje = () => {
    const texto = nuevoMensaje.mensaje.trim();

    if (!texto || enviando.value) {
        return;
    }

    enviando.value = true;

    nuevoMensaje.mensaje = texto;

    nuevoMensaje.post(
        route(
            'emprendimientos.mensajes.enviar',
            props.conversacion.id
        ),
        {
            preserveScroll: true,
            onSuccess: async () => {
                nuevoMensaje.reset();
                await desplazarAlFinal(true);
            },
            onFinish: () => {
                enviando.value = false;
            },
        }
    );
};

const eliminarMensaje = (mensaje) => {
    if (!esMensajePropio(mensaje)) {
        return;
    }

    if (!confirm('¿Deseas eliminar este mensaje?')) {
        return;
    }

    router.delete(
        route(
            'emprendimientos.mensaje.destroy',
            mensaje.id
        ),
        {
            preserveScroll: true,
        }
    );
};

const marcarComoLeidos = () => {
    router.post(
        route(
            'emprendimientos.mensajes.leidos',
            props.conversacion.id
        ),
        {},
        {
            preserveScroll: true,
        }
    );
};

onMounted(() => {
    desplazarAlFinal();
    marcarComoLeidos();
});

onBeforeUnmount(() => {
    nuevoMensaje.reset();
});

const imagenEmprendimiento = computed(() => {
    const e = props.conversacion.emprendimiento;
    const logo = e?.logo ?? e?.imagen;
    if (!logo) return null;
    if (logo.startsWith('http://') || logo.startsWith('https://') || logo.startsWith('/')) {
        return logo;
    }
    return `/storage/${logo}`;
});
</script>

<template>

    <Head :title="`Chat con ${nombreOtroUsuario()} | ${conversacion.emprendimiento?.nombre ?? 'Emprendimiento'}`" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

                <!-- NAVEGACIÓN SUPERIOR -->
                <div class="mb-4 flex items-center justify-between">
                    <Link :href="route('emprendimientos.conversaciones')"
                        class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-purple-900">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Volver a conversaciones
                    </Link>

                    <Link v-if="conversacion.emprendimiento"
                        :href="route('emprendimientos.show', conversacion.emprendimiento.id)"
                        class="text-xs sm:text-sm font-bold text-purple-900 hover:underline">
                        Ver página del emprendimiento →
                    </Link>
                </div>

                <!-- CONTENEDOR PRINCIPAL DEL CHAT (GRID DE 2 COLUMNAS) -->
                <div
                    class="grid grid-cols-1 overflow-hidden rounded-3xl border border-slate-300 bg-white shadow-2xl shadow-purple-950/15 lg:grid-cols-4">

                    <!-- RESUMEN DEL EMPRENDIMIENTO Y CONTRAPARTE (PANEL IZQUIERDO) -->
                    <div
                        class="border-b border-slate-200 bg-purple-950/5 p-4 lg:border-b-0 lg:border-r lg:p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <!-- AVATAR REAL O INICIAL -->
                                <div
                                    class="flex h-12 w-12 shrink-0 overflow-hidden rounded-2xl bg-purple-900 text-lg font-black text-white shadow-xs items-center justify-center">
                                    <img v-if="avatarOtroUsuario" :src="avatarOtroUsuario" alt="Avatar"
                                        class="h-full w-full object-cover" />
                                    <span v-else>
                                        {{ inicialOtroUsuario() }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Conversando con
                                    </p>
                                    <p class="truncate font-black text-[#172A3D]">
                                        {{ nombreOtroUsuario() }}
                                    </p>
                                </div>
                            </div>

                            <div class="my-5 border-t border-slate-200"></div>

                            <!-- Miniatura del emprendimiento -->
                            <div v-if="conversacion.emprendimiento" class="space-y-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Emprendimiento vinculado
                                </p>
                                <div class="flex items-start gap-3">
                                    <div
                                        class="h-12 w-12 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs">
                                        <img v-if="imagenEmprendimiento" :src="imagenEmprendimiento"
                                            :alt="conversacion.emprendimiento.nombre"
                                            class="h-full w-full object-cover" />
                                        <div v-else class="flex h-full w-full items-center justify-center text-lg">
                                            🚀
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="line-clamp-2 text-xs font-bold text-[#172A3D]">
                                            {{ conversacion.emprendimiento.nombre }}
                                        </p>
                                        <span
                                            class="mt-1 inline-block rounded-full bg-purple-50 border border-purple-200 px-2.5 py-0.5 text-[10px] font-bold text-purple-900">
                                            Emprendimiento
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-200 hidden lg:block">
                            <p class="text-[11px] leading-relaxed text-slate-400 font-medium">
                                Comunícate de forma segura y coordina colaboraciones dentro de Portal Deseret.
                            </p>
                        </div>
                    </div>

                    <!-- ÁREA DE CONVERSACIÓN (DERECHA) -->
                    <div class="flex flex-col lg:col-span-3 bg-white">

                        <!-- LISTA DE MENSAJES -->
                        <div ref="mensajesContainer" class="flex-1 space-y-4 overflow-y-auto p-4 sm:p-6 bg-slate-50/50"
                            style="max-height: 520px; min-height: 380px;">
                            <div v-if="!mensajes.length" class="flex h-full items-center justify-center text-center">
                                <div class="p-6">
                                    <div class="text-3xl">👋</div>
                                    <p class="mt-2 text-sm font-bold text-[#172A3D]">Inicia la conversación</p>
                                    <p class="mt-1 text-xs text-slate-500 font-medium">Escribe un mensaje para comenzar
                                        a conversar sobre el emprendimiento y su propuesta de colaboración.</p>
                                </div>
                            </div>

                            <template v-else>
                                <div v-for="mensaje in mensajes" :key="mensaje.id" class="flex w-full"
                                    :class="esMensajePropio(mensaje) ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-[78%] rounded-2xl px-4 py-3 shadow-sm sm:max-w-[68%]"
                                        :class="esMensajePropio(mensaje)
                                            ? 'bg-purple-600 text-white rounded-br-none shadow-md shadow-purple-600/10'
                                            : 'bg-white text-slate-800 rounded-bl-none border border-slate-200 shadow-xs'">
                                        <p
                                            class="whitespace-pre-line break-words text-xs sm:text-sm leading-relaxed font-medium">
                                            {{ mensaje.mensaje }}
                                        </p>
                                        <div class="mt-1 flex items-center justify-end gap-2 text-[10px] font-bold"
                                            :class="esMensajePropio(mensaje) ? 'text-purple-200' : 'text-slate-400'">
                                            <span>{{ formatearHora(mensaje.created_at) }}</span>

                                            <button v-if="esMensajePropio(mensaje)" type="button"
                                                class="text-purple-200 transition hover:text-white font-bold"
                                                title="Eliminar mensaje" @click="eliminarMensaje(mensaje)">
                                                <i class="pi pi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- CAJA DE TEXTO PARA RESPONDER -->
                        <div class="border-t border-slate-200 bg-white p-3 sm:p-4">
                            <form @submit.prevent="enviarMensaje" class="flex items-end gap-2">
                                <textarea v-model="nuevoMensaje.mensaje" rows="2" maxlength="5000"
                                    placeholder="Escribe un mensaje..."
                                    class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-xs sm:text-sm text-[#172A3D] placeholder-slate-400 focus:border-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-100 font-medium"
                                    :disabled="enviando" @keydown.enter.exact.prevent="enviarMensaje"></textarea>

                                <button type="submit" :disabled="enviando || !nuevoMensaje.mensaje.trim()"
                                    class="flex h-11 shrink-0 items-center justify-center rounded-xl bg-purple-600 px-5 text-xs sm:text-sm font-black text-white transition hover:bg-purple-700 disabled:opacity-50 shadow-xs">
                                    <i v-if="enviando" class="pi pi-spin pi-spinner mr-1"></i>
                                    <i v-else class="pi pi-send mr-1"></i>
                                    <span class="hidden sm:inline">Enviar</span>
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