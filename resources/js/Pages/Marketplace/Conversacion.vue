<script setup>
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue';

import {
    Head,
    Link,
    router,
} from '@inertiajs/vue3';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/

const props = defineProps({
    conversacion: {
        type: Object,
        required: false,
        default: () => ({}),
    },

    usuarioActual: {
        type: [Number, String],
        required: false,
        default: null,
    },
});

/*
|--------------------------------------------------------------------------
| Estado
|--------------------------------------------------------------------------
*/

const mensaje = ref('');

const enviando = ref(false);

const eliminando = ref(null);

const mensajesContainer = ref(null);

/*
|--------------------------------------------------------------------------
| Estado de edición
|--------------------------------------------------------------------------
*/

const mensajeEditando = ref(null);

const textoEditando = ref('');

const editandoMensaje = ref(false);

/*
|--------------------------------------------------------------------------
| Actualización automática
|--------------------------------------------------------------------------
*/

const pollingActivo = ref(false);

const pollingTimer = ref(null);

const POLLING_INTERVALO = 10000; // 10 segundos

/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/

const usuarioActualId = computed(() => {
    return Number(props.usuarioActual);
});

const esComprador = computed(() => {
    return Number(props.conversacion.comprador_id) === usuarioActualId.value;
});

const otroUsuario = computed(() => {
    if (esComprador.value) {
        return props.conversacion.vendedor;
    }

    return props.conversacion.comprador;
});

/*
|--------------------------------------------------------------------------
| Avatar del otro usuario (Blindado)
|--------------------------------------------------------------------------
*/

const avatarOtroUsuario = computed(() => {
    const usuario = otroUsuario.value;
    if (!usuario) {
        return null;
    }

    const foto =
        usuario.foto ??
        usuario.profile_photo_url ??
        usuario.avatar ??
        usuario.imagen ??
        props.conversacion.vendedor?.foto ??
        props.conversacion.comprador?.foto;

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

const publicacion = computed(() => {
    return props.conversacion.publicacion;
});

const mensajes = computed(() => {
    return props.conversacion.mensajes ?? [];
});

/*
|--------------------------------------------------------------------------
| Formatear imagen
|--------------------------------------------------------------------------
*/

const imagenPublicacion = computed(() => {
    const imagenes = publicacion.value?.imagenes ?? [];

    if (!imagenes.length) {
        return null;
    }

    const principal = imagenes.find(
        imagen => Boolean(imagen.es_principal)
    );

    const ruta =
        principal?.ruta ??
        principal?.url ??
        imagenes[0]?.ruta ??
        imagenes[0]?.url ??
        null;

    if (!ruta) {
        return null;
    }

    if (
        ruta.startsWith('http://') ||
        ruta.startsWith('https://') ||
        ruta.startsWith('/')
    ) {
        return ruta;
    }

    return `/storage/${ruta}`;
});

/*
|--------------------------------------------------------------------------
| Fecha
|--------------------------------------------------------------------------
*/

const formatearHora = (fecha) => {
    if (!fecha) {
        return '';
    }

    const fechaObj = new Date(fecha);

    if (Number.isNaN(fechaObj.getTime())) {
        return '';
    }

    return fechaObj.toLocaleTimeString(
        'es-CO',
        {
            hour: '2-digit',
            minute: '2-digit',
        }
    );
};

/*
|--------------------------------------------------------------------------
| Precio
|--------------------------------------------------------------------------
*/

const formatearPrecio = (precio, esGratis = false) => {
    if (esGratis) {
        return 'Gratis';
    }

    if (
        precio === null
        || precio === undefined
        || precio === ''
    ) {
        return 'Precio no especificado';
    }

    return new Intl.NumberFormat(
        'es-CO',
        {
            style: 'currency',
            currency: 'COP',
            maximumFractionDigits: 0,
        }
    ).format(Number(precio));
};

/*
|--------------------------------------------------------------------------
| Scroll
|--------------------------------------------------------------------------
*/

const scrollAlFinal = async () => {
    await nextTick();

    if (!mensajesContainer.value) {
        return;
    }

    mensajesContainer.value.scrollTop =
        mensajesContainer.value.scrollHeight;
};

const estaCercaDelFinal = () => {
    if (!mensajesContainer.value) {
        return true;
    }

    const elemento = mensajesContainer.value;

    const distancia =
        elemento.scrollHeight
        - elemento.scrollTop
        - elemento.clientHeight;

    return distancia < 120;
};

/*
|--------------------------------------------------------------------------
| Marcar mensajes como leídos
|--------------------------------------------------------------------------
*/

const marcarLeidos = () => {
    router.post(
        route(
            'marketplace.mensajes.leidos',
            props.conversacion.id
        ),
        {},
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        }
    );
};

/*
|--------------------------------------------------------------------------
| Actualización automática con Inertia
|--------------------------------------------------------------------------
*/

const actualizarConversacion = () => {
    if (pollingActivo.value) {
        return;
    }

    if (document.visibilityState !== 'visible') {
        return;
    }

    pollingActivo.value = true;

    const estabaCercaDelFinal = estaCercaDelFinal();

    router.reload({
        only: ['conversacion'],
        preserveScroll: true,
        preserveState: true,
        replace: true,

        onSuccess: () => {
            if (estabaCercaDelFinal) {
                scrollAlFinal();
            }

            marcarLeidos();
        },

        onError: (errors) => {
            console.error(
                'Error actualizando conversación:',
                errors
            );
        },

        onFinish: () => {
            pollingActivo.value = false;
        },
    });
};

const programarActualizacion = () => {
    if (pollingTimer.value) {
        clearTimeout(pollingTimer.value);

        pollingTimer.value = null;
    }

    if (document.visibilityState !== 'visible') {
        return;
    }

    pollingTimer.value = setTimeout(
        async () => {
            actualizarConversacion();
            programarActualizacion();
        },
        POLLING_INTERVALO
    );
};

const manejarVisibilidad = () => {
    if (document.visibilityState === 'visible') {
        actualizarConversacion();
        programarActualizacion();
    } else {
        if (pollingTimer.value) {
            clearTimeout(pollingTimer.value);

            pollingTimer.value = null;
        }
    }
};

/*
|--------------------------------------------------------------------------
| Enviar mensaje (Blindado contra bloqueos)
|--------------------------------------------------------------------------
*/

const enviarMensaje = () => {
    const texto = mensaje.value.trim();

    if (!texto || enviando.value) {
        return;
    }

    enviando.value = true;

    router.post(
        route(
            'marketplace.mensajes.enviar',
            props.conversacion.id
        ),
        {
            mensaje: texto,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: async () => {
                mensaje.value = '';
                await scrollAlFinal();
                programarActualizacion();
            },

            onError: (errors) => {
                console.error('Error enviando mensaje:', errors);
            },

            onFinish: () => {
                enviando.value = false; // Se libera siempre la caja de texto
            },
        }
    );
};

/*
|--------------------------------------------------------------------------
| Estado de edición
|--------------------------------------------------------------------------
*/

const iniciarEdicion = (item) => {
    if (Number(item.user_id) !== usuarioActualId.value) {
        return;
    }

    if (item.leido_at) {
        return;
    }

    if (
        editandoMensaje.value
        || mensajeEditando.value !== null
    ) {
        return;
    }

    mensajeEditando.value = item.id;

    textoEditando.value = item.mensaje;

    nextTick(() => {
        const textarea = document.getElementById(
            `editar-mensaje-${item.id}`
        );

        if (!textarea) {
            return;
        }

        textarea.focus();

        textarea.setSelectionRange(
            textarea.value.length,
            textarea.value.length
        );
    });
};

const cancelarEdicion = () => {
    mensajeEditando.value = null;

    textoEditando.value = '';

    editandoMensaje.value = false;
};

const guardarEdicion = (item) => {
    const texto = textoEditando.value.trim();

    if (!texto) {
        return;
    }

    if (editandoMensaje.value) {
        return;
    }

    if (Number(item.user_id) !== usuarioActualId.value) {
        cancelarEdicion();

        return;
    }

    if (item.leido_at) {
        cancelarEdicion();

        return;
    }

    if (texto === item.mensaje) {
        cancelarEdicion();

        return;
    }

    editandoMensaje.value = true;

    router.put(
        route(
            'marketplace.mensaje.update',
            item.id
        ),
        {
            mensaje: texto,
        },
        {
            preserveScroll: true,
            preserveState: true,

            onSuccess: async () => {
                cancelarEdicion();

                await scrollAlFinal();

                programarActualizacion();
            },

            onError: (errors) => {
                console.error(
                    'Error editando mensaje:',
                    errors
                );
            },

            onFinish: () => {
                editandoMensaje.value = false;
            },
        }
    );
};

const eliminarMensaje = (id) => {
    if (!id) {
        return;
    }

    if (eliminando.value === id) {
        return;
    }

    if (mensajeEditando.value === id) {
        cancelarEdicion();
    }

    eliminando.value = id;

    router.delete(
        route(
            'marketplace.mensaje.destroy',
            id
        ),
        {
            preserveScroll: true,
            preserveState: false,

            onSuccess: async () => {
                await scrollAlFinal();
            },

            onError: (errors) => {
                console.error(
                    'Error eliminando mensaje:',
                    errors
                );
            },

            onFinish: () => {
                eliminando.value = null;
            },
        }
    );
};

const volverPublicacion = () => {
    if (!publicacion.value?.id) {
        return;
    }

    router.visit(
        route(
            'marketplace.show',
            publicacion.value.id
        )
    );
};

const manejarEnterEdicion = (event, item) => {
    if (event.shiftKey) {
        return;
    }

    event.preventDefault();

    guardarEdicion(item);
};

watch(
    () => props.conversacion?.mensajes,
    async () => {
        await nextTick();

        if (estaCercaDelFinal()) {
            await scrollAlFinal();
        }
    },
    {
        deep: true,
    }
);

onMounted(async () => {
    marcarLeidos();

    await scrollAlFinal();

    document.addEventListener(
        'visibilitychange',
        manejarVisibilidad
    );

    programarActualizacion();
});

onBeforeUnmount(() => {
    if (pollingTimer.value) {
        clearTimeout(pollingTimer.value);

        pollingTimer.value = null;
    }

    document.removeEventListener(
        'visibilitychange',
        manejarVisibilidad
    );
});
</script>

<template>

    <Head :title="`Chat con ${otroUsuario?.name ?? 'Usuario'} | ${publicacion?.titulo ?? 'Marketplace'}`" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">
        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

                <!-- NAVEGACIÓN SUPERIOR -->
                <div class="mb-4 flex items-center justify-between">
                    <Link :href="route('mensajes.index')"
                        class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-blue-900">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Volver a mensajes
                    </Link>

                    <button type="button" @click="volverPublicacion"
                        class="text-xs sm:text-sm font-bold text-blue-900 hover:underline">
                        Ver publicación del artículo →
                    </button>
                </div>

                <!-- CONTENEDOR PRINCIPAL DEL CHAT (GRID DE 2 COLUMNAS) -->
                <div
                    class="grid grid-cols-1 overflow-hidden rounded-3xl border border-slate-300 bg-white shadow-2xl shadow-blue-950/15 lg:grid-cols-4">

                    <!-- RESUMEN DE LA PUBLICACIÓN Y CONTRAPARTE (PANEL LATERAL IZQUIERDO) -->
                    <div
                        class="border-b border-slate-200 bg-blue-950/5 p-4 lg:border-b-0 lg:border-r lg:p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <!-- AVATAR DE LA FOTO REAL O INICIAL -->
                                <div
                                    class="flex h-12 w-12 shrink-0 overflow-hidden rounded-2xl bg-blue-900 text-lg font-black text-white shadow-xs items-center justify-center">
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

                            <!-- Miniatura de la publicación -->
                            <div class="space-y-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Artículo en negociación
                                </p>
                                <div class="flex items-start gap-3">
                                    <div
                                        class="h-12 w-12 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs">
                                        <img v-if="imagenPublicacion" :src="imagenPublicacion"
                                            :alt="publicacion?.titulo" class="h-full w-full object-cover" />
                                        <div v-else class="flex h-full w-full items-center justify-center text-lg">
                                            📦
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="line-clamp-2 text-xs font-bold text-[#172A3D]">
                                            {{ publicacion?.titulo }}
                                        </p>
                                        <p class="mt-1 text-xs font-black text-blue-900">
                                            {{ formatearPrecio(publicacion?.precio, publicacion?.es_gratis) }}
                                        </p>
                                        <span v-if="publicacion?.estado_publicacion"
                                            class="mt-1 inline-block rounded-full bg-blue-50 border border-blue-200 px-2.5 py-0.5 text-[10px] font-bold text-blue-900">
                                            {{ publicacion.estado_publicacion }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-200 hidden lg:block">
                            <p class="text-[11px] leading-relaxed text-slate-400 font-medium">
                                Mantén la comunicación dentro de Portal Deseret para garantizar una transacción segura.
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
                                    <p class="mt-2 text-sm font-bold text-[#172A3D]">Comienza la negociación</p>
                                    <p class="mt-1 text-xs text-slate-500 font-medium">Pregunta al vendedor sobre
                                        detalles, estado o puntos de entrega.</p>
                                </div>
                            </div>

                            <template v-else>
                                <div v-for="item in mensajes" :key="item.id" class="flex w-full"
                                    :class="Number(item.user_id) === usuarioActualId ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-[78%] rounded-2xl px-4 py-3 shadow-sm sm:max-w-[68%]"
                                        :class="Number(item.user_id) === usuarioActualId
                                            ? 'bg-blue-600 text-white rounded-br-none shadow-md shadow-blue-500/10'
                                            : 'bg-white text-slate-800 rounded-bl-none border border-slate-200 shadow-xs'">

                                        <div v-if="Number(item.user_id) !== usuarioActualId"
                                            class="mb-1 text-[11px] font-bold text-blue-900">
                                            {{ item.usuario?.name ?? 'Usuario' }}
                                        </div>

                                        <!-- MODO EDICIÓN -->
                                        <div v-if="mensajeEditando === item.id">
                                            <textarea :id="`editar-mensaje-${item.id}`" v-model="textoEditando" rows="2"
                                                maxlength="5000"
                                                class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm text-slate-800 shadow-sm focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                                :disabled="editandoMensaje"
                                                @keydown.enter.exact.prevent="manejarEnterEdicion($event, item)"
                                                @keydown.escape="cancelarEdicion"></textarea>

                                            <div class="mt-2 flex items-center justify-end gap-2">
                                                <button type="button"
                                                    class="rounded-lg px-2.5 py-1 text-xs font-bold text-white/80 transition hover:bg-white/10 hover:text-white disabled:opacity-50"
                                                    :disabled="editandoMensaje" @click="cancelarEdicion">
                                                    Cancelar
                                                </button>
                                                <button type="button"
                                                    class="rounded-lg bg-white px-3 py-1.5 text-xs font-black text-blue-600 transition hover:bg-slate-100 disabled:opacity-50 shadow-xs"
                                                    :disabled="editandoMensaje || !textoEditando.trim()"
                                                    @click="guardarEdicion(item)">
                                                    {{ editandoMensaje ? 'Guardando...' : 'Guardar' }}
                                                </button>
                                            </div>
                                        </div>

                                        <!-- MODO NORMAL -->
                                        <div v-else>
                                            <p class="whitespace-pre-wrap break-words text-xs sm:text-sm leading-relaxed font-medium"
                                                :class="Number(item.user_id) === usuarioActualId ? 'text-white' : 'text-slate-800'">
                                                {{ item.mensaje }}
                                            </p>
                                        </div>

                                        <!-- INFORMACIÓN DEL MENSAJE -->
                                        <div class="mt-1 flex flex-wrap items-center gap-2 text-[10px] font-semibold"
                                            :class="Number(item.user_id) === usuarioActualId ? 'justify-end text-blue-200' : 'justify-start text-slate-400'">
                                            <span>{{ formatearHora(item.created_at) }}</span>
                                            <span v-if="Number(item.user_id) === usuarioActualId">
                                                {{ item.leido_at ? '✓✓' : '✓' }}
                                            </span>
                                            <span v-if="item.editado_at" class="italic">Editado</span>

                                            <button
                                                v-if="Number(item.user_id) === usuarioActualId && !item.leido_at && mensajeEditando !== item.id"
                                                type="button"
                                                class="text-blue-200 transition hover:text-white font-bold"
                                                @click="iniciarEdicion(item)">
                                                Editar
                                            </button>

                                            <button
                                                v-if="Number(item.user_id) === usuarioActualId && mensajeEditando !== item.id"
                                                type="button"
                                                class="text-red-300 transition hover:text-red-100 font-bold disabled:opacity-50"
                                                :disabled="eliminando === item.id" @click="eliminarMensaje(item.id)">
                                                {{ eliminando === item.id ? 'Eliminando...' : 'Eliminar' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- CAJA DE TEXTO PARA RESPONDER -->
                        <div class="border-t border-slate-200 bg-white p-3 sm:p-4">
                            <form @submit.prevent="enviarMensaje" class="flex items-end gap-2">
                                <textarea v-model="mensaje" @keydown.enter.exact.prevent="enviarMensaje" rows="2"
                                    maxlength="5000" placeholder="Escribe un mensaje... (Presiona Enter para enviar)"
                                    class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-xs sm:text-sm text-[#172A3D] placeholder-slate-400 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100 font-medium"></textarea>

                                <button type="submit" :disabled="enviando || !mensaje.trim()"
                                    class="flex h-11 shrink-0 items-center justify-center rounded-xl bg-blue-600 px-5 text-xs sm:text-sm font-black text-white transition hover:bg-blue-700 disabled:opacity-50 shadow-xs">
                                    <svg class="h-4 w-4 rotate-90" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <span class="ml-1.5 hidden sm:inline">{{ enviando ? 'Enviando...' : 'Enviar'
                                    }}</span>
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