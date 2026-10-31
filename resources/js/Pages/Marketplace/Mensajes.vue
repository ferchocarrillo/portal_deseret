<script setup>
import { computed, onMounted } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    conversaciones: {
        type: Array,
        default: () => [],
    },
});

onMounted(() => {
    console.log("Datos de conversaciones recibidos del Backend:", props.conversaciones);
});
const page = usePage();
const usuarioActual = computed(() => page.props.auth?.user ?? null);

const obtenerOtroUsuario = (conversacion) => {
    const usuarioId = Number(usuarioActual.value?.id);

    if (conversacion.tipo === 'servicio') {
        if (Number(conversacion.usuario_interesado_id) === usuarioId) {
            return conversacion.propietario ?? conversacion.servicio?.user;
        }
        return conversacion.interesado;
    }

    if (conversacion.tipo === 'emprendimiento') {
        if (Number(conversacion.usuario_iniciador_id) === usuarioId) {
            return conversacion.usuarioReceptor;
        }
        return conversacion.usuarioIniciador;
    }

    // CORRECCIÓN: Manejo exacto de las llaves foráneas de Eventos
    if (conversacion.tipo === 'evento') {
        if (Number(conversacion.comprador_id) === usuarioId) {
            return conversacion.organizador;
        }
        return conversacion.comprador;
    }

    // Fallback del Marketplace original y Clasificados
    if (Number(conversacion.comprador_id) === usuarioId) {
        return conversacion.vendedor;
    }

    return conversacion.comprador;
};

const obtenerAvatarUsuario = (conversacion) => {
    const otroUsuario = obtenerOtroUsuario(conversacion);
    if (!otroUsuario) return null;

    const foto = otroUsuario.foto ?? otroUsuario.profile_photo_url ?? otroUsuario.avatar ?? otroUsuario.imagen;
    if (!foto) return null;

    if (foto.startsWith('http://') || foto.startsWith('https://') || foto.startsWith('/')) {
        return foto;
    }

    return `/storage/${foto}`;
};

const obtenerInicialesUsuario = (conversacion) => {
    const nombre = obtenerOtroUsuario(conversacion)?.name ?? 'Usuario';
    return nombre.charAt(0).toUpperCase();
};

const obtenerMensajes = (conversacion) => {
    if (conversacion.tipo === 'clasificado' || conversacion.tipo === 'evento') {
        return conversacion.ultimoMensaje
            ? [conversacion.ultimoMensaje]
            : (conversacion.mensajes ?? []);
    }

    return conversacion.mensajes ?? [];
};

const obtenerUltimoMensaje = (conversacion) => {
    const mensajes = obtenerMensajes(conversacion);

    if (!mensajes || !mensajes.length) {
        return 'Aún no hay mensajes.';
    }

    // CORRECCIÓN: Leer 'cuerpo' para los eventos y 'mensaje' para el resto
    return mensajes[0]?.cuerpo ?? mensajes[0]?.mensaje ?? 'Aún no hay mensajes.';
};

const obtenerFecha = (conversacion) => {
    const mensajes = obtenerMensajes(conversacion);

    const fecha =
        conversacion.ultimo_mensaje_at ??
        mensajes[0]?.created_at ??
        conversacion.created_at;

    if (!fecha) {
        return '';
    }

    const fechaObj = new Date(fecha);

    if (Number.isNaN(fechaObj.getTime())) {
        return '';
    }

    return fechaObj.toLocaleDateString('es-CO', {
        day: '2-digit',
        month: 'short',
    });
};

const obtenerTitulo = (conversacion) => {
    if (conversacion.tipo === 'servicio') return conversacion.servicio?.titulo ?? 'Servicio';
    if (conversacion.tipo === 'emprendimiento') return conversacion.emprendimiento?.nombre ?? 'Emprendimiento';
    if (conversacion.tipo === 'clasificado') return conversacion.clasificado?.titulo ?? 'Clasificado';

    // CORRECCIÓN: Título del evento
    if (conversacion.tipo === 'evento') return conversacion.evento?.titulo ?? 'Evento';

    return conversacion.publicacion?.titulo ?? 'Publicación';
};

const obtenerEstado = (conversacion) => {
    if (conversacion.tipo === 'servicio') return conversacion.servicio?.estado ?? '';
    if (conversacion.tipo === 'emprendimiento') return conversacion.emprendimiento?.activo ? 'Activo' : 'Inactivo';
    if (conversacion.tipo === 'clasificado') return conversacion.clasificado?.estado ?? '';

    // CORRECCIÓN: Estado del evento
    if (conversacion.tipo === 'evento') return conversacion.evento?.estado ?? 'Activo';

    return conversacion.publicacion?.estado_publicacion ?? '';
};

const obtenerModulo = (conversacion) => {
    if (conversacion.tipo === 'servicio') return 'Servicio';
    if (conversacion.tipo === 'emprendimiento') return 'Emprendimiento';
    if (conversacion.tipo === 'clasificado') return 'Clasificado';
    if (conversacion.tipo === 'evento') return 'Evento';

    return 'Marketplace';
};

const obtenerRutaConversacion = (conversacion) => {
    if (conversacion.tipo === 'servicio') return route('servicios.conversacion.show', conversacion.id);
    if (conversacion.tipo === 'emprendimiento') return route('emprendimientos.mensajes.show', conversacion.id);
    if (conversacion.tipo === 'clasificado') return route('clasificados.mensajes.show', conversacion.id);

    // CORRECCIÓN: Ruta de eventos
    if (conversacion.tipo === 'evento') return route('eventos.mensajes.show', conversacion.id);

    return route('marketplace.mensajes.show', conversacion.id);
};

const mensajesNoLeidos = (conversacion) => {
    return Number(conversacion.mensajes_no_leidos ?? 0);
};

const totalMensajesNoLeidos = computed(() => {
    return props.conversaciones.reduce(
        (total, conversacion) =>
            total + mensajesNoLeidos(conversacion),
        0
    );
});
</script>

<template>

    <Head title="Centro de Mensajes | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo">

            <!-- ENCABEZADO TRANSLÚCIDO -->
            <section
                class="border-b border-indigo-200/80 bg-indigo-100/35 backdrop-blur-md shadow-lg shadow-indigo-500/10">
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="mb-1 flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.22em] text-indigo-950">
                                    Centro de mensajes transversal
                                </span>
                            </div>

                            <div class="mt-1 flex items-center gap-3">
                                <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl">
                                    Mensajes
                                </h1>

                                <span v-if="totalMensajesNoLeidos > 0"
                                    class="inline-flex min-w-6 items-center justify-center rounded-full bg-red-600 px-2 py-1 text-xs font-black text-white shadow-xs">
                                    {{ totalMensajesNoLeidos }}
                                </span>
                            </div>

                            <p class="mt-1 text-xs sm:text-sm text-slate-600 font-medium">
                                Tus conversaciones con proveedores, clientes, compradores, vendedores y emprendedores.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CONTENIDO PRINCIPAL -->
           <main
                class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 rounded-xl p-3 mt-2 bg-[linear-gradient(180deg,var(--tw-gradient-stops))] from-[#27F5BB]/50 to-[#4A8574]/50">

                <!-- ESTADO VACÍO -->
                <div v-if="!conversaciones.length"
                    class="rounded-3xl border border-slate-300 bg-white p-10 text-center shadow-2xl shadow-indigo-500/15">
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-3xl border border-indigo-200 text-indigo-600">
                        💬
                    </div>

                    <h2 class="mt-4 text-lg font-bold text-[#172A3D]">
                        No tienes conversaciones
                    </h2>

                    <p class="mx-auto mt-2 max-w-md text-xs sm:text-sm leading-relaxed text-slate-500 font-medium">
                        Cuando contactes a un proveedor, te escriban sobre un servicio o publicación, la conversación
                        aparecerá aquí.
                    </p>
                </div>

                <!-- LISTADO DE CONVERSACIONES -->
                <div v-else class="space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <h2 class="font-bold text-xs sm:text-sm text-[#172A3D] uppercase tracking-wider">
                            Tus conversaciones ({{ conversaciones.length }})
                        </h2>

                        <span v-if="totalMensajesNoLeidos > 0"
                            class="text-xs font-black text-red-600 bg-red-50 border border-red-200 px-3 py-1 rounded-full shadow-xs">
                            {{ totalMensajesNoLeidos }} sin leer
                        </span>
                    </div>

                    <div class="space-y-3">
                        <Link v-for="conversacion in conversaciones" :key="`${conversacion.tipo}-${conversacion.id}`"
                            :href="obtenerRutaConversacion(conversacion)"
                            class="group relative flex gap-4 px-5 py-4 rounded-2xl border transition-all duration-300 hover:scale-[1.01] shadow-xl bg-blue-50/90 border-blue-300 shadow-blue-500/10 hover:bg-blue-50">

                            <!-- Avatar -->
                            <div
                                class="h-14 w-14 shrink-0 overflow-hidden rounded-2xl bg-purple-100 border border-purple-200 text-purple-800 shadow-xs flex items-center justify-center font-bold text-base">
                                <img v-if="obtenerAvatarUsuario(conversacion)" :src="obtenerAvatarUsuario(conversacion)"
                                    alt="Avatar de usuario" class="h-full w-full object-cover" />
                                <span v-else>
                                    {{ obtenerInicialesUsuario(conversacion) }}
                                </span>
                            </div>

                            <!-- Información de la fila -->
                            <!-- Información de la fila -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <!-- NUEVO: Etiqueta indicadora de módulo -->
                                        <div class="mb-1 flex items-center gap-2">
                                            <span
                                                class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                                                :class="{
                                                    'bg-blue-100 text-blue-800 border border-blue-200': obtenerModulo(conversacion) === 'Servicio',
                                                    'bg-purple-100 text-purple-800 border border-purple-200': obtenerModulo(conversacion) === 'Evento',
                                                    'bg-green-100 text-green-800 border border-green-200': obtenerModulo(conversacion) === 'Emprendimiento',
                                                    'bg-orange-100 text-orange-800 border border-orange-200': obtenerModulo(conversacion) === 'Clasificado',
                                                    'bg-slate-100 text-slate-800 border border-slate-200': obtenerModulo(conversacion) === 'Marketplace'
                                                }">
                                                {{ obtenerModulo(conversacion) }}
                                            </span>
                                        </div>

                                        <h3 class="truncate text-xs sm:text-sm font-bold text-[#172A3D]">
                                            {{ obtenerOtroUsuario(conversacion)?.name ?? 'Usuario' }}
                                        </h3>

                                        <p class="mt-0.5 truncate text-xs font-semibold text-slate-700">
                                            {{ obtenerTitulo(conversacion) }}
                                        </p>
                                    </div>

                                    <span class="shrink-0 text-[11px] font-bold text-slate-500">
                                        {{ obtenerFecha(conversacion) }}
                                    </span>
                                </div>

                                <p class="mt-1.5 truncate text-xs sm:text-sm font-medium text-slate-600">
                                    {{ obtenerUltimoMensaje(conversacion) }}
                                </p>
                            </div>
                        </Link>
                    </div>
                </div>

            </main>
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