<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { route } from 'ziggy-js'

const props = defineProps({
    conversaciones: {
        type: Array,
        default: () => [],
    },
})

const conversacionesOrdenadas = computed(() => {
    return [...props.conversaciones].sort((a, b) => {
        const fechaA = new Date(a.ultimo_mensaje_at || a.updated_at || 0)
        const fechaB = new Date(b.ultimo_mensaje_at || b.updated_at || 0)

        return fechaB - fechaA
    })
})

const obtenerUsuarioContacto = (conversacion) => {
    if (conversacion.es_vendedor) {
        return conversacion.comprador
    }
    return conversacion.vendedor
}

const obtenerAvatarUsuario = (conversacion) => {
    const usuario = obtenerUsuarioContacto(conversacion)
    if (!usuario) return null

    const foto = usuario.foto ?? usuario.profile_photo_url ?? usuario.avatar ?? usuario.imagen
    if (!foto) return null

    if (foto.startsWith('http://') || foto.startsWith('https://') || foto.startsWith('/')) {
        return foto
    }

    return `/storage/${foto}`
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

const obtenerInicialesUsuario = (conversacion) => {
    const usuario = obtenerUsuarioContacto(conversacion)
    const nombre = nombreUsuario(usuario)
    return nombre.charAt(0).toUpperCase()
}

const nombreContacto = (conversacion) => {
    if (conversacion.es_vendedor) {
        return nombreUsuario(conversacion.comprador)
    }

    return nombreUsuario(conversacion.vendedor)
}

const ultimoMensaje = (conversacion) => {
    if (!conversacion.ultimo_mensaje) {
        return 'Sin mensajes'
    }

    return conversacion.ultimo_mensaje.mensaje
}

const fechaConversacion = (conversacion) => {
    const fecha = conversacion.ultimo_mensaje_at || conversacion.updated_at

    if (!fecha) {
        return ''
    }

    return new Date(fecha).toLocaleDateString('es-CO', {
        day: '2-digit',
        month: 'short',
    })
}
</script>

<template>

    <Head title="Mis mensajes | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

                <!-- ENCABEZADO -->
                <section
                    class="mb-6 rounded-3xl border border-amber-200/80 bg-amber-100/35 p-6 backdrop-blur-md shadow-xl shadow-amber-500/10 sm:p-8">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-amber-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-amber-950">
                                    Bandeja de entrada
                                </span>
                            </div>

                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                Mis mensajes de Clasificados
                            </h1>

                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Conversaciones de tus publicaciones y consultas realizadas.
                            </p>
                        </div>

                        <Link :href="route('clasificados.index')"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs sm:text-sm font-bold text-slate-700 shadow-sm transition hover:bg-amber-50 hover:border-amber-300">
                            <span>←</span>
                            <span>Ver clasificados</span>
                        </Link>
                    </div>
                </section>

                <!-- ESTADO VACÍO -->
                <div v-if="conversacionesOrdenadas.length === 0"
                    class="rounded-3xl border border-slate-300 bg-white p-10 text-center shadow-2xl shadow-amber-500/15">
                    <div
                        class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 border border-amber-200 text-3xl text-amber-600 shadow-xs">
                        💬
                    </div>

                    <h2 class="text-base sm:text-lg font-bold text-[#172A3D]">
                        No tienes conversaciones
                    </h2>

                    <p class="mt-2 text-xs sm:text-sm text-slate-500 font-medium">
                        Cuando contactes a un vendedor o alguien te escriba por uno de tus clasificados, aparecerá aquí.
                    </p>

                    <Link :href="route('clasificados.index')"
                        class="mt-6 inline-flex items-center rounded-xl bg-amber-600 px-5 py-3 text-xs sm:text-sm font-black text-white shadow-md shadow-amber-500/25 transition hover:bg-amber-700">
                        Ver clasificados
                    </Link>
                </div>

                <!-- LISTADO DE CONVERSACIONES -->
                <div v-else class="space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <h2 class="font-bold text-xs sm:text-sm text-[#172A3D] uppercase tracking-wider">
                            Conversaciones activas ({{ conversacionesOrdenadas.length }})
                        </h2>
                    </div>

                    <div class="space-y-3">
                        <Link v-for="conversacion in conversacionesOrdenadas" :key="conversacion.id"
                            :href="route('clasificados.mensajes.show', conversacion.id)"
                            class="group relative flex gap-4 px-5 py-4 rounded-2xl border transition-all duration-300 hover:scale-[1.01] shadow-xl bg-amber-50/90 border-amber-300 shadow-amber-500/10 hover:bg-amber-50"
                            :class="[
                                conversacion.mensajes_no_leidos > 0 ? 'ring-2 ring-offset-1 ring-red-500/50' : ''
                            ]">

                            <!-- Avatar -->
                            <div
                                class="h-14 w-14 shrink-0 overflow-hidden rounded-2xl bg-amber-100 border border-amber-200 text-amber-900 shadow-xs flex items-center justify-center font-bold text-base relative">
                                <img v-if="obtenerAvatarUsuario(conversacion)" :src="obtenerAvatarUsuario(conversacion)"
                                    alt="Avatar de usuario" class="h-full w-full object-cover" />
                                <span v-else>
                                    {{ obtenerInicialesUsuario(conversacion) }}
                                </span>

                                <span v-if="conversacion.mensajes_no_leidos > 0"
                                    class="absolute -right-0.5 -top-0.5 h-3.5 w-3.5 rounded-full border-2 border-white bg-red-600 shadow-sm"></span>
                            </div>

                            <!-- Información de la fila -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="mb-1 flex items-center gap-2">
                                            <span
                                                class="rounded-full px-2.5 py-0.5 text-[10px] font-bold border shadow-xs shrink-0 bg-amber-100 text-amber-950 border-amber-300">
                                                Clasificado
                                            </span>
                                        </div>

                                        <h3 class="truncate text-xs sm:text-sm font-bold text-[#172A3D]"
                                            :class="conversacion.mensajes_no_leidos > 0 ? 'font-black' : ''">
                                            {{ nombreContacto(conversacion) }}
                                        </h3>

                                        <p class="mt-0.5 truncate text-xs font-semibold text-slate-700">
                                            {{ conversacion.clasificado?.titulo || 'Clasificado' }}
                                        </p>
                                    </div>

                                    <span class="shrink-0 text-[11px] font-bold text-slate-500">
                                        {{ fechaConversacion(conversacion) }}
                                    </span>
                                </div>

                                <div class="mt-1.5 flex items-center justify-between gap-2">
                                    <p class="truncate text-xs sm:text-sm font-medium flex-1"
                                        :class="conversacion.mensajes_no_leidos > 0 ? 'font-extrabold text-slate-900' : 'text-slate-600'">
                                        {{ ultimoMensaje(conversacion) }}
                                    </p>

                                    <div v-if="conversacion.mensajes_no_leidos > 0"
                                        class="flex h-6 min-w-6 items-center justify-center rounded-full bg-red-600 px-2 text-xs font-black text-white shadow-xs">
                                        {{ conversacion.mensajes_no_leidos }}
                                    </div>
                                </div>
                            </div>

                            <!-- Flecha de navegación -->
                            <div
                                class="flex shrink-0 items-center text-slate-400 font-bold transition group-hover:translate-x-1 group-hover:text-amber-600">
                                →
                            </div>
                        </Link>
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