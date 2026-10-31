<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { route } from 'ziggy-js'

const props = defineProps({
    conversacion: {
        type: Object,
        required: true,
    },
})

const page = usePage()

const mensaje = ref('')
const mensajesContainer = ref(null)

const usuarioActual = computed(() => page.props.auth?.user || null)

const mensajes = computed(() => {
    return props.conversacion?.mensajes || []
})

const otroUsuario = computed(() => {
    if (!usuarioActual.value) {
        return null
    }

    if (Number(props.conversacion.vendedor_id) === Number(usuarioActual.value.id)) {
        return props.conversacion.comprador
    }

    return props.conversacion.vendedor
})

const avatarOtroUsuario = computed(() => {
    const usuario = otroUsuario.value
    if (!usuario) {
        return null
    }

    const foto = usuario.foto ?? usuario.profile_photo_url ?? usuario.avatar ?? usuario.imagen

    if (!foto) {
        return null
    }

    if (
        foto.startsWith('http://') ||
        foto.startsWith('https://') ||
        foto.startsWith('/')
    ) {
        return foto
    }

    return `/storage/${foto}`
})

const scrollAlFinal = async () => {
    await nextTick()

    if (mensajesContainer.value) {
        mensajesContainer.value.scrollTop = mensajesContainer.value.scrollHeight
    }
}

const estaCercaDelFinal = () => {
    if (!mensajesContainer.value) {
        return true
    }

    const elemento = mensajesContainer.value
    const distancia = elemento.scrollHeight - elemento.scrollTop - elemento.clientHeight

    return distancia < 120
}

const enviarMensaje = () => {
    const texto = mensaje.value.trim()

    if (!texto) {
        return
    }

    router.post(
        route('clasificados.mensajes.enviar', props.conversacion.id),
        {
            mensaje: texto,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                mensaje.value = ''
                scrollAlFinal()
            },
        }
    )
}

const marcarLeidos = async () => {
    try {
        await fetch(
            route('clasificados.mensajes.leidos', props.conversacion.id),
            {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content'),
                },
                credentials: 'same-origin',
            }
        )
    } catch (error) {
        console.error('Error al marcar mensajes como leídos:', error)
    }
}

const esMio = (item) => {
    return Number(item.user_id) === Number(usuarioActual.value?.id)
}

const nombreUsuario = (usuario) => {
    if (!usuario) {
        return 'Usuario'
    }

    if (usuario.name) {
        return usuario.name
    }

    return `${usuario.nombres || ''} ${usuario.apellidos || ''}`.trim() || 'Usuario'
}

const horaMensaje = (item) => {
    if (!item.created_at) {
        return ''
    }

    return new Date(item.created_at).toLocaleTimeString('es-CO', {
        hour: '2-digit',
        minute: '2-digit',
    })
}

onMounted(() => {
    marcarLeidos()
    scrollAlFinal()

    document.addEventListener('visibilitychange', manejarVisibilidad)
    iniciarActualizacionMensajes()
})

onBeforeUnmount(() => {
    detenerActualizacionMensajes()
    document.removeEventListener('visibilitychange', manejarVisibilidad)
})

watch(
    () => props.conversacion?.mensajes,
    async () => {
        await nextTick()

        if (estaCercaDelFinal()) {
            await scrollAlFinal()
        }
    },
    {
        deep: true,
    }
)

let intervaloMensajes = null

const actualizarMensajes = () => {
    if (document.visibilityState === 'hidden') {
        return
    }

    router.reload({
        only: ['conversacion'],
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            if (estaCercaDelFinal()) {
                scrollAlFinal()
            }
        },
    })
}

const iniciarActualizacionMensajes = () => {
    if (intervaloMensajes) {
        clearInterval(intervaloMensajes)
    }

    intervaloMensajes = window.setInterval(actualizarMensajes, 10000)
}

const detenerActualizacionMensajes = () => {
    if (intervaloMensajes) {
        clearInterval(intervaloMensajes)
        intervaloMensajes = null
    }
}

const manejarVisibilidad = () => {
    if (document.visibilityState === 'visible') {
        actualizarMensajes()
        iniciarActualizacionMensajes()
    } else {
        detenerActualizacionMensajes()
    }
}

const imagenClasificado = computed(() => {
    const clasificado = props.conversacion.clasificado
    const imagenes = clasificado?.imagenes ?? []
    if (!imagenes.length) return null

    const principal = imagenes.find((img) => Boolean(img.es_principal)) ?? imagenes[0]
    const ruta = principal?.ruta ?? principal?.url ?? principal?.path ?? null

    if (!ruta) return null
    if (ruta.startsWith('http://') || ruta.startsWith('https://') || ruta.startsWith('/')) {
        return ruta
    }
    return `/storage/${ruta}`
})
</script>

<template>
    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

                <!-- NAVEGACIÓN SUPERIOR -->
                <div class="mb-4 flex items-center justify-between">
                    <button type="button" @click="router.visit(route('clasificados.mensajes'))"
                        class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-700 transition hover:text-amber-900">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Volver a mensajes
                    </button>

                    <a v-if="conversacion.clasificado?.id"
                        :href="route('clasificados.show', conversacion.clasificado.id)"
                        class="text-xs sm:text-sm font-bold text-amber-900 hover:underline">
                        Ver publicación del clasificado →
                    </a>
                </div>

                <!-- CONTENEDOR PRINCIPAL DEL CHAT (GRID DE 2 COLUMNAS) -->
                <div
                    class="grid grid-cols-1 overflow-hidden rounded-3xl border border-slate-300 bg-white shadow-2xl shadow-amber-950/15 lg:grid-cols-4">

                    <!-- RESUMEN DEL CLASIFICADO Y CONTRAPARTE (PANEL IZQUIERDO) -->
                    <div
                        class="border-b border-slate-200 bg-amber-950/5 p-4 lg:border-b-0 lg:border-r lg:p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <!-- AVATAR REAL O INICIAL -->
                                <div
                                    class="flex h-12 w-12 shrink-0 overflow-hidden rounded-2xl bg-amber-900 text-lg font-black text-white shadow-xs items-center justify-center">
                                    <img v-if="avatarOtroUsuario" :src="avatarOtroUsuario" alt="Avatar"
                                        class="h-full w-full object-cover" />
                                    <span v-else>
                                        {{ nombreUsuario(otroUsuario).charAt(0).toUpperCase() }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Conversando con
                                    </p>
                                    <p class="truncate font-black text-[#172A3D]">
                                        {{ nombreUsuario(otroUsuario) }}
                                    </p>
                                </div>
                            </div>

                            <div class="my-5 border-t border-slate-200"></div>

                            <!-- Miniatura del clasificado -->
                            <div v-if="conversacion.clasificado" class="space-y-3">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Clasificado vinculado
                                </p>
                                <div class="flex items-start gap-3">
                                    <div
                                        class="h-12 w-12 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xs">
                                        <img v-if="imagenClasificado" :src="imagenClasificado"
                                            :alt="conversacion.clasificado.titulo" class="h-full w-full object-cover" />
                                        <div v-else class="flex h-full w-full items-center justify-center text-lg">
                                            🏷️
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="line-clamp-2 text-xs font-bold text-[#172A3D]">
                                            {{ conversacion.clasificado.titulo }}
                                        </p>
                                        <span
                                            class="mt-1 inline-block rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-[10px] font-bold text-amber-900">
                                            Clasificado
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
                            <div v-if="mensajes.length === 0"
                                class="flex h-full items-center justify-center text-center">
                                <div class="p-6">
                                    <div class="text-3xl">👋</div>
                                    <p class="mt-2 text-sm font-bold text-[#172A3D]">Inicia la conversación</p>
                                    <p class="mt-1 text-xs text-slate-500 font-medium">Escribe un mensaje para contactar
                                        al vendedor.</p>
                                </div>
                            </div>

                            <template v-else>
                                <div v-for="item in mensajes" :key="item.id" class="flex w-full"
                                    :class="esMio(item) ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-[78%] rounded-2xl px-4 py-3 shadow-sm sm:max-w-[68%]"
                                        :class="esMio(item)
                                            ? 'bg-amber-600 text-white rounded-br-none shadow-md shadow-amber-500/10'
                                            : 'bg-white text-slate-800 rounded-bl-none border border-slate-200 shadow-xs'">

                                        <p v-if="!esMio(item)" class="mb-1 text-xs font-bold text-amber-800">
                                            {{ nombreUsuario(item.usuario) }}
                                        </p>

                                        <p
                                            class="whitespace-pre-wrap break-words text-xs sm:text-sm leading-relaxed font-medium">
                                            {{ item.mensaje }}
                                        </p>

                                        <div class="mt-1 flex items-center justify-end gap-1 text-[10px] font-bold"
                                            :class="esMio(item) ? 'text-amber-100' : 'text-slate-400'">
                                            <span>{{ horaMensaje(item) }}</span>
                                            <span v-if="esMio(item)">
                                                {{ item.leido_at ? '✓✓' : '✓' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- CAJA DE TEXTO PARA RESPONDER -->
                        <div class="border-t border-slate-200 bg-white p-3 sm:p-4">
                            <form @submit.prevent="enviarMensaje" class="flex items-end gap-2">
                                <textarea v-model="mensaje" rows="2" maxlength="2000"
                                    placeholder="Escribe un mensaje... (Presiona Enter para enviar)"
                                    class="w-full resize-none rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-xs sm:text-sm text-[#172A3D] placeholder-slate-400 focus:border-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-100 font-medium"
                                    @keydown.enter.exact.prevent="enviarMensaje"></textarea>

                                <button type="submit" :disabled="!mensaje.trim()"
                                    class="flex h-11 shrink-0 items-center justify-center rounded-xl bg-amber-600 px-5 text-xs sm:text-sm font-black text-white transition hover:bg-amber-700 disabled:opacity-50 shadow-xs">
                                    <svg class="h-4 w-4 rotate-90" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    <span class="ml-1.5 hidden sm:inline">Enviar</span>
                                </button>
                            </form>
                            <p class="mt-1.5 text-[11px] text-slate-400 font-medium">
                                Presiona Enter para enviar.
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