<script setup>
import { computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    conversaciones: {
        type: Array,
        default: () => [],
    },
});

const conversacionesOrdenadas = computed(() => {
    return [...props.conversaciones].sort((a, b) => {
        const fechaA = new Date(a.ultimo_mensaje_at || a.created_at || 0);
        const fechaB = new Date(b.ultimo_mensaje_at || b.created_at || 0);

        return fechaB - fechaA;
    });
});

const abrirConversacion = (conversacion) => {
    router.visit(
        route('servicios.conversacion.show', conversacion.id)
    );
};

const obtenerOtraPersona = (conversacion) => {
    if (
        conversacion.usuario_actual_id &&
        conversacion.usuario_actual_id === conversacion.usuario_interesado_id
    ) {
        return conversacion.propietario;
    }

    return conversacion.usuario_interesado;
};

const obtenerUltimoMensaje = (conversacion) => {
    if (!conversacion.ultimo_mensaje) {
        return 'Sin mensajes todavía';
    }

    return conversacion.ultimo_mensaje.mensaje || 'Sin mensajes';
};

const formatearFecha = (fecha) => {
    if (!fecha) {
        return '';
    }

    const fechaMensaje = new Date(fecha);
    const ahora = new Date();

    const mismoDia =
        fechaMensaje.toDateString() === ahora.toDateString();

    if (mismoDia) {
        return fechaMensaje.toLocaleTimeString('es-CO', {
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    return fechaMensaje.toLocaleDateString('es-CO', {
        day: '2-digit',
        month: '2-digit',
    });
};

const volverInicio = () => {
    router.visit(route('dashboard'));
};
</script>

<template>

    <Head title="Centro de Mensajes | Portal Deseret" />

    <!-- 1. ACTIVAR CARBONCILLO EN EL LAYOUT -->
    <AuthenticatedLayout :carboncillo="true">

        <!-- 2. CONTENEDOR CON FONDO CARBONCILLO CLARO Y SUAVIZADO -->
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo py-8">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

                <!-- Encabezado con identidad Índigo Transversal -->
                <div class="mb-6 flex items-center gap-3">
                    <button type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-300 bg-white text-slate-700 transition hover:bg-indigo-50 hover:border-indigo-300 font-bold shadow-xs"
                        @click="volverInicio">
                        ←
                    </button>

                    <div>
                        <div class="mb-1 flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
                            <span class="text-[10px] font-black uppercase tracking-[0.22em] text-indigo-900">
                                Módulo transversal
                            </span>
                        </div>
                        <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                            Centro de mensajes
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-600 font-medium">
                            Tus conversaciones y canales de comunicación en Portal Deseret.
                        </p>
                    </div>
                </div>

                <!-- Bandeja -->
                <div
                    class="overflow-hidden rounded-3xl bg-white shadow-2xl shadow-indigo-950/15 border border-slate-300">

                    <!-- Sin conversaciones -->
                    <div v-if="conversacionesOrdenadas.length === 0"
                        class="flex min-h-[350px] flex-col items-center justify-center px-6 text-center py-12">
                        <div
                            class="mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-3xl border border-indigo-200">
                            💬
                        </div>

                        <h2 class="text-lg font-bold text-[#172A3D]">
                            No tienes conversaciones
                        </h2>

                        <p class="mt-1 max-w-md text-xs sm:text-sm text-slate-500 font-medium">
                            Cuando interactúes con otros miembros de la comunidad, tus chats aparecerán en este espacio
                            centralizado.
                        </p>

                        <button type="button"
                            class="mt-5 rounded-xl bg-indigo-600 px-5 py-3 text-xs sm:text-sm font-black text-white transition hover:bg-indigo-700 shadow-md"
                            @click="volverInicio">
                            Ir al inicio
                        </button>
                    </div>

                    <!-- Lista -->
                    <div v-else class="divide-y divide-slate-100">
                        <button v-for="conversacion in conversacionesOrdenadas" :key="conversacion.id" type="button"
                            class="flex w-full items-center gap-4 px-4 py-4 text-left transition hover:bg-indigo-50/40 md:px-6"
                            @click="abrirConversacion(conversacion)">

                            <!-- Imagen o ícono representativo -->
                            <div class="flex-shrink-0">
                                <img v-if="conversacion.servicio?.imagen"
                                    :src="`/storage/${conversacion.servicio.imagen}`"
                                    :alt="conversacion.servicio?.titulo || 'Conversación'"
                                    class="h-14 w-14 rounded-xl object-cover border border-slate-200 shadow-xs">

                                <div v-else
                                    class="flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-50 text-2xl border border-indigo-200">
                                    💬
                                </div>
                            </div>

                            <!-- Información -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h2 class="truncate font-bold text-sm sm:text-base text-[#172A3D]">
                                            {{ obtenerOtraPersona(conversacion)?.name || 'Usuario' }}
                                        </h2>

                                        <p class="truncate text-xs sm:text-sm font-semibold text-slate-600">
                                            {{ conversacion.servicio?.titulo || 'Conversación general' }}
                                        </p>
                                    </div>

                                    <span v-if="conversacion.ultimo_mensaje_at"
                                        class="flex-shrink-0 text-[11px] font-medium text-slate-400">
                                        {{ formatearFecha(conversacion.ultimo_mensaje_at) }}
                                    </span>
                                </div>

                                <div class="mt-1.5 flex items-center gap-2">
                                    <p class="min-w-0 flex-1 truncate text-xs sm:text-sm text-slate-500 font-medium">
                                        {{ obtenerUltimoMensaje(conversacion) }}
                                    </p>

                                    <span v-if="conversacion.mensajes_no_leidos > 0"
                                        class="flex h-5 min-w-5 items-center justify-center rounded-full bg-indigo-600 px-1.5 text-[10px] font-black text-white shadow-xs">
                                        {{ conversacion.mensajes_no_leidos }}
                                    </span>
                                </div>
                            </div>

                            <!-- Flecha -->
                            <div class="flex-shrink-0 text-slate-400 font-bold">
                                →
                            </div>
                        </button>
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