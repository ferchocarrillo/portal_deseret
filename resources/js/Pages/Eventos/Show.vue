<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    evento: {
        type: Object,
        required: true,
    },
    esFavorito: {
        type: Boolean,
        default: false,
    },
});

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);
const isOwner = computed(() => currentUser.value && currentUser.value.id === props.evento.user_id);

// ==========================================
// FAVORITOS
// ==========================================
const favoritoActivo = ref(Boolean(props.esFavorito ?? props.evento.es_favorito ?? false));

const toggleFavorito = () => {
    const estadoPrevio = favoritoActivo.value;
    favoritoActivo.value = !favoritoActivo.value;

    router.post(
        route('favoritos.toggle'),
        {
            publicacion_id: props.evento.id,
            modulo: 'eventos',
        },
        {
            preserveScroll: true,
            preserveState: true,
            onError: () => {
                favoritoActivo.value = estadoPrevio;
            },
        }
    );
};

// ==========================================
// ELIMINAR EVENTO (PROPIETARIO)
// ==========================================
const deleteForm = useForm({});
const eliminarEvento = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este evento de forma permanente?')) {
        deleteForm.delete(route('eventos.destroy', props.evento.id));
    }
};

// ==========================================
// DENUNCIAR / REPORTAR EVENTO
// ==========================================
const mostrarModalDenuncia = ref(false);
const denunciaEnviada = ref(false);

const motivosDenuncia = [
    'Información falsa o engañosa',
    'Posible fraude o estafa con entradas',
    'Contenido inapropiado u ofensivo',
    'Evento duplicado o spam',
    'El evento ya no existe o fue cancelado sin aviso',
    'Otro motivo',
];

const denunciaForm = useForm({
    publicacion_id: props.evento.id,
    evento_id: props.evento.id,
    modulo: 'eventos',
    motivo: '',
    descripcion: '',
});

const enviarDenuncia = () => {
    let urlDestino = '/denuncias';
    if (typeof route === 'function') {
        if (route().has('eventos.denunciar')) {
            urlDestino = route('eventos.denunciar', props.evento.id);
        } else if (route().has('denuncias.store')) {
            urlDestino = route('denuncias.store');
        } else if (route().has('reportes.store')) {
            urlDestino = route('reportes.store');
        }
    }

    denunciaForm.post(urlDestino, {
        preserveScroll: true,
        onSuccess: () => {
            denunciaEnviada.value = true;
            setTimeout(() => {
                mostrarModalDenuncia.value = false;
                denunciaEnviada.value = false;
                denunciaForm.reset('motivo', 'descripcion');
            }, 1800);
        },
    });
};

// ==========================================
// FORMATOS Y UTILIDADES
// ==========================================
const imagenUrl = computed(() => {
    const img = props.evento.imagen;
    if (!img) return null;

    if (img.startsWith('http')) {
        return img;
    }

    const rutaLimpiada = img.replace(/^\/+/, '').replace(/^storage\//, '');
    return `${window.location.origin}/storage/${rutaLimpiada}`;
});

const formatearFecha = (fecha) => {
    if (!fecha) return 'Por confirmar';
    return new Date(fecha).toLocaleString('es-CO', {
        dateStyle: 'long',
        timeStyle: 'short',
    });
};
</script>

<template>

    <Head :title="`${evento.titulo} | Eventos Portal Deseret`" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo pb-16">

            <!-- ================================================= -->
            <!-- ENCABEZADO SUPERIOR DE SECCIÓN                    -->
            <!-- ================================================= -->
            <section class="border-b border-rose-200/80 bg-rose-100/25 backdrop-blur-md shadow-lg shadow-rose-500/10">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <!-- BOTÓN VOLVER DISCRETO A LA IZQUIERDA Y ACCESO A MENSAJES -->
                    <div class="mb-4 flex items-center justify-between">
                        <Link :href="route('eventos.index')"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-rose-700 transition">
                            ← Volver a eventos
                        </Link>

                        <Link v-if="currentUser" :href="route('eventos.mensajes.index')"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-800 hover:text-rose-950 transition bg-white/80 px-3 py-1.5 rounded-xl border border-rose-200 shadow-xs">
                            💬 Ver mis mensajes de eventos
                        </Link>
                    </div>

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div
                                class="mb-2 inline-flex items-center gap-2 rounded-full border border-rose-300/80 bg-white/90 px-3.5 py-1 shadow-sm">
                                <span class="text-xs">🎤</span>
                                <span class="text-xs font-black uppercase tracking-wider text-rose-950">
                                    Agenda Comunitaria
                                </span>
                            </div>
                            <h1 class="text-2xl font-black tracking-tight text-[#172A3D] sm:text-3xl line-clamp-1">
                                {{ evento.titulo }}
                            </h1>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ================================================= -->
            <!-- CONTENIDO PRINCIPAL (GRID 2 COLUMNAS)             -->
            <!-- ================================================= -->
            <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 items-start">

                    <!-- ============================================= -->
                    <!-- COLUMNA IZQUIERDA (2/3): DETALLE DEL EVENTO   -->
                    <!-- ============================================= -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- TARJETA PRINCIPAL DE PORTADA Y DESCRIPCIÓN -->
                        <div
                            class="overflow-hidden rounded-3xl border border-slate-300 bg-white shadow-2xl shadow-rose-500/10">

                            <!-- Imagen Banner -->
                            <div v-if="evento.imagen"
                                class="relative aspect-[16/9] w-full bg-slate-100 overflow-hidden">
                                <img :src="imagenUrl" :alt="evento.titulo" class="h-full w-full object-cover" />
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-[#172A3D]/70 via-transparent to-transparent">
                                </div>

                                <!-- Badges Superiores Izquierdos -->
                                <div class="absolute left-4 top-4 flex flex-wrap gap-2">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full border px-3.5 py-1 text-xs font-black uppercase tracking-wider shadow-md backdrop-blur"
                                        :class="{
                                            'bg-blue-600/95 text-white border-blue-400': evento.modalidad === 'presencial',
                                            'bg-emerald-600/95 text-white border-emerald-400': evento.modalidad === 'virtual',
                                            'bg-purple-600/95 text-white border-purple-400': evento.modalidad === 'hibrido',
                                        }">
                                        <span>{{ evento.modalidad === 'virtual' ? '💻' : '📍' }}</span>
                                        <span>{{ evento.modalidad }}</span>
                                    </span>

                                    <span
                                        class="inline-flex items-center rounded-full border px-3.5 py-1 text-xs font-black uppercase tracking-wider shadow-md backdrop-blur"
                                        :class="{
                                            'bg-emerald-100/95 text-emerald-950 border-emerald-300': evento.estado === 'activo',
                                            'bg-amber-100/95 text-amber-950 border-amber-300': evento.estado === 'suspendido',
                                            'bg-slate-200/95 text-slate-800 border-slate-400': evento.estado === 'finalizado',
                                        }">
                                        {{ evento.estado }}
                                    </span>
                                </div>

                                <!-- Botón Flotante de Favorito -->
                                <button v-if="currentUser" type="button" @click="toggleFavorito"
                                    class="absolute right-4 top-4 flex h-11 w-11 items-center justify-center rounded-full bg-white/95 shadow-lg ring-1 ring-slate-200 backdrop-blur transition hover:scale-110"
                                    :title="favoritoActivo ? 'Quitar de favoritos' : 'Guardar en favoritos'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" class="h-6 w-6 transition"
                                        :class="favoritoActivo ? 'fill-red-600 text-red-600' : 'fill-transparent text-slate-600'">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </button>

                                <div v-if="evento.ciudad" class="absolute bottom-4 left-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-black/60 px-3 py-1.5 text-xs font-bold text-white backdrop-blur">
                                        🏙️ {{ evento.ciudad.nombre }}
                                    </span>
                                </div>
                            </div>

                            <!-- Cuerpo del Evento -->
                            <div class="p-6 sm:p-8 space-y-8">
                                <div v-if="!evento.imagen"
                                    class="flex items-center justify-between flex-wrap gap-4 pb-2 border-b border-slate-100">
                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full border px-3.5 py-1 text-xs font-black uppercase tracking-wider"
                                            :class="{
                                                'bg-blue-600 text-white border-blue-400': evento.modalidad === 'presencial',
                                                'bg-emerald-600 text-white border-emerald-400': evento.modalidad === 'virtual',
                                                'bg-purple-600 text-white border-purple-400': evento.modalidad === 'hibrido',
                                            }">
                                            <span>{{ evento.modalidad === 'virtual' ? '💻' : '📍' }}</span>
                                            <span>{{ evento.modalidad }}</span>
                                        </span>

                                        <span
                                            class="inline-flex items-center rounded-full border px-3.5 py-1 text-xs font-black uppercase tracking-wider"
                                            :class="{
                                                'bg-emerald-100 text-emerald-950 border-emerald-300': evento.estado === 'activo',
                                                'bg-amber-100 text-amber-950 border-amber-300': evento.estado === 'suspendido',
                                                'bg-slate-200 text-slate-800 border-slate-400': evento.estado === 'finalizado',
                                            }">
                                            {{ evento.estado }}
                                        </span>
                                    </div>

                                    <button v-if="currentUser" type="button" @click="toggleFavorito"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-black shadow-sm transition hover:bg-slate-50"
                                        :title="favoritoActivo ? 'Quitar de favoritos' : 'Guardar en favoritos'">
                                        <span>{{ favoritoActivo ? '❤️' : '🤍' }}</span>
                                        <span>{{ favoritoActivo ? 'Guardado' : 'Guardar favorito' }}</span>
                                    </button>
                                </div>

                                <div>
                                    <span class="text-xs font-black uppercase tracking-widest text-rose-600">
                                        Detalles del encuentro
                                    </span>
                                    <h2 class="mt-1 text-2xl sm:text-3xl font-black text-[#172A3D]">
                                        {{ evento.titulo }}
                                    </h2>
                                </div>

                                <!-- Tarjetas de Fecha y Ubicación -->
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div
                                        class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-2xl text-rose-700 border border-rose-200">
                                            📅
                                        </div>
                                        <div class="space-y-1">
                                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-500">
                                                Fecha y Hora
                                            </h3>
                                            <p class="text-sm font-bold text-[#172A3D]">
                                                <span class="text-slate-500 font-medium">Inicio:</span> {{
                                                    formatearFecha(evento.fecha_inicio) }}
                                            </p>
                                            <p v-if="evento.fecha_fin" class="text-sm font-bold text-[#172A3D]">
                                                <span class="text-slate-500 font-medium">Fin:</span> {{
                                                    formatearFecha(evento.fecha_fin) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                                        <div
                                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-2xl text-amber-700 border border-amber-200">
                                            📍
                                        </div>
                                        <div class="space-y-1">
                                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-500">
                                                Ubicación y Acceso
                                            </h3>
                                            <p v-if="evento.ubicacion" class="text-sm font-bold text-[#172A3D]">
                                                {{ evento.ubicacion }}
                                            </p>
                                            <p v-if="evento.ciudad" class="text-xs font-semibold text-slate-500">
                                                Ciudad: {{ evento.ciudad.nombre }}
                                            </p>
                                            <div v-if="evento.enlace_virtual" class="pt-1">
                                                <a :href="evento.enlace_virtual" target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="inline-flex items-center gap-1 text-xs font-black text-rose-600 underline hover:text-rose-700">
                                                    🔗 Ir al enlace de transmisión →
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Descripción Completa -->
                                <div class="border-t border-slate-200 pt-6">
                                    <h3 class="text-lg font-black text-[#172A3D] mb-3">
                                        Acerca del Evento
                                    </h3>
                                    <div
                                        class="text-sm sm:text-base text-slate-700 whitespace-pre-line leading-relaxed font-medium">
                                        {{ evento.descripcion }}
                                    </div>
                                </div>

                                <!-- Localidades / Zonas específicas -->
                                <div v-if="!evento.es_gratuito && evento.precios_localidades && evento.precios_localidades.length > 0"
                                    class="border-t border-slate-200 pt-6">
                                    <h3 class="text-lg font-black text-[#172A3D] mb-4">
                                        🎟️ Localidades y Zonas Disponibles
                                    </h3>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div v-for="(loc, idx) in evento.precios_localidades" :key="idx"
                                            class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 shadow-sm">
                                            <span class="text-sm font-bold text-[#172A3D]">{{ loc.zona }}</span>
                                            <span
                                                class="rounded-lg bg-rose-100 px-3 py-1 text-sm font-black text-rose-900 border border-rose-200">
                                                ${{ Number(loc.precio).toLocaleString('es-CO') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ============================================= -->
                    <!-- COLUMNA DERECHA (1/3): ENTRADAS, CONTACTO, ETC -->
                    <!-- ============================================= -->
                    <div class="space-y-6 lg:sticky lg:top-6">

                        <!-- 1. TARJETA DE ENTRADAS Y CONTACTO AL ORGANIZADOR -->
                        <div
                            class="rounded-3xl border border-slate-300 bg-white p-6 shadow-2xl shadow-rose-500/10 space-y-6">

                            <!-- Resumen de Precio / Entrada -->
                            <div class="border-b border-slate-200 pb-5">
                                <span class="text-xs font-black uppercase tracking-wider text-slate-400">
                                    Valor de la entrada
                                </span>

                                <div v-if="evento.es_gratuito"
                                    class="mt-2 rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-center">
                                    <span class="text-2xl font-black text-emerald-800 block">🎉 Entrada Libre</span>
                                    <span class="text-xs font-bold text-emerald-600 mt-0.5 block">Evento 100%
                                        Gratuito</span>
                                </div>

                                <div v-else class="mt-2 space-y-3">
                                    <div class="flex items-baseline justify-between">
                                        <span class="text-xs font-bold text-slate-500">Precio General:</span>
                                        <span class="text-3xl font-black text-[#172A3D]">
                                            ${{ Number(evento.precio).toLocaleString('es-CO') }}
                                        </span>
                                    </div>

                                    <div v-if="evento.precio_preventa"
                                        class="rounded-2xl bg-amber-50 border border-amber-200 p-3.5 flex items-center justify-between">
                                        <div>
                                            <span
                                                class="text-xs font-black uppercase tracking-wider text-amber-900 block">
                                                🔥 Preventa Especial
                                            </span>
                                            <span v-if="evento.fecha_limite_preventa"
                                                class="text-[11px] font-semibold text-amber-700">
                                                Hasta el {{ new
                                                    Date(evento.fecha_limite_preventa).toLocaleDateString('es-CO') }}
                                            </span>
                                        </div>
                                        <span class="text-xl font-black text-amber-900">
                                            ${{ Number(evento.precio_preventa).toLocaleString('es-CO') }}
                                        </span>
                                    </div>
                                </div>

                                <div v-if="evento.capacidad"
                                    class="mt-3 flex items-center justify-between text-xs font-bold text-slate-500 pt-2">
                                    <span>👥 Aforo máximo:</span>
                                    <span class="text-[#172A3D] font-black">{{ evento.capacidad }} personas</span>
                                </div>
                            </div>

                            <!-- Información del Organizador -->
                            <div class="flex items-center gap-3.5">
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#172A3D] text-lg font-black text-[#F5E4BC] shadow-md">
                                    {{ (evento.usuario?.name || 'O').charAt(0).toUpperCase() }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                        Organizado por
                                    </p>
                                    <p class="truncate text-base font-black text-[#172A3D]">
                                        {{ evento.usuario?.name || 'Miembro de la Comunidad' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Acciones para Visitantes: Contactar (vía eventos.mensajes.store) y Favoritos -->
                            <div v-if="!isOwner && currentUser" class="space-y-3 pt-2">
                                <!-- Botón de Contacto que DIRECCIONA A LA CAJA DE CONVERSACIONES -->
                                <Link :href="route('eventos.mensajes.store', evento.id)" method="post" as="button"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-[#172A3D] px-5 py-3.5 text-sm font-black text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-slate-800">
                                    <span>💬</span>
                                    <span>Contactar al organizador</span>
                                </Link>

                                <!-- Botón Guardar en Favoritos -->
                                <button type="button" @click="toggleFavorito"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border px-5 py-3 text-xs font-black transition"
                                    :class="favoritoActivo
                                        ? 'border-red-300 bg-red-50 text-red-700 hover:bg-red-100'
                                        : 'border-slate-300 bg-slate-50 text-slate-700 hover:bg-slate-100'">
                                    <span>{{ favoritoActivo ? '❤️' : '🤍' }}</span>
                                    <span>{{ favoritoActivo ? 'Guardado en tus favoritos' : 'Guardar en mis favoritos'
                                        }}</span>
                                </button>
                            </div>

                            <!-- Panel de Gestión (Solo si es el dueño del evento) -->
                            <div v-if="isOwner" class="space-y-3 border-t border-slate-200 pt-4">
                                <p class="text-xs font-black uppercase tracking-wider text-slate-400">
                                    Administración de tu publicación
                                </p>
                                <div class="grid grid-cols-2 gap-3">
                                    <Link :href="route('eventos.edit', evento.id)"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-black text-white shadow-sm transition hover:bg-indigo-700">
                                        ✏️ Editar
                                    </Link>
                                    <button type="button" @click="eliminarEvento"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-red-600 px-4 py-2.5 text-xs font-black text-white shadow-sm transition hover:bg-red-700">
                                        🗑️ Eliminar
                                    </button>
                                </div>
                            </div>

                        </div>

                        <!-- 2. TARJETA DE SEGURIDAD Y BOTÓN DE DENUNCIA -->
                        <div v-if="!isOwner && currentUser"
                            class="rounded-3xl border border-slate-300 bg-white p-5 shadow-xl shadow-rose-500/5 space-y-4">
                            <div class="flex items-start gap-3">
                                <span class="text-xl">🛡️</span>
                                <div>
                                    <h4 class="text-xs font-black uppercase tracking-wider text-[#172A3D]">
                                        Comunidad Segura
                                    </h4>
                                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                                        Verifica siempre los detalles con el organizador antes de realizar pagos
                                        anticipados fuera del portal.
                                    </p>
                                </div>
                            </div>

                            <div class="border-t border-slate-100 pt-3">
                                <button type="button" @click="mostrarModalDenuncia = true"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50/70 px-4 py-2.5 text-xs font-black text-rose-700 transition hover:bg-rose-100 hover:border-rose-300">
                                    <span>🚩</span>
                                    <span>Denunciar o reportar este evento</span>
                                </button>
                            </div>
                        </div>

                    </div>

                </div>
            </main>

            <!-- ================================================= -->
            <!-- MODAL DE DENUNCIA DE EVENTO                       -->
            <!-- ================================================= -->
            <div v-if="mostrarModalDenuncia"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm">
                <div class="w-full max-w-md overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl">

                    <div class="flex items-center justify-between border-b border-slate-100 bg-rose-50/60 px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🚩</span>
                            <h3 class="text-base font-black text-[#172A3D]">
                                Reportar Evento
                            </h3>
                        </div>
                        <button type="button" @click="mostrarModalDenuncia = false"
                            class="rounded-lg p-1 text-slate-400 transition hover:bg-slate-200 hover:text-slate-700">
                            ✕
                        </button>
                    </div>

                    <form @submit.prevent="enviarDenuncia" class="p-6 space-y-4">
                        <div v-if="denunciaEnviada"
                            class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-center">
                            <p class="text-sm font-black text-emerald-800">
                                ✅ Reporte enviado correctamente
                            </p>
                            <p class="mt-1 text-xs font-medium text-emerald-600">
                                Nuestro equipo revisará esta publicación a la brevedad.
                            </p>
                        </div>

                        <template v-else>
                            <p class="text-xs font-medium text-slate-600">
                                Selecciona el motivo por el cual consideras que <strong>"{{ evento.titulo }}"</strong>
                                incumple las normas de la comunidad:
                            </p>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1.5">
                                    Motivo del reporte *
                                </label>
                                <select v-model="denunciaForm.motivo" required
                                    class="w-full rounded-xl border-slate-300 text-sm font-medium text-[#172A3D] focus:border-rose-500 focus:ring-rose-500">
                                    <option value="" disabled>Selecciona una opción...</option>
                                    <option v-for="motivo in motivosDenuncia" :key="motivo" :value="motivo">
                                        {{ motivo }}
                                    </option>
                                </select>
                                <p v-if="denunciaForm.errors.motivo" class="mt-1 text-xs font-bold text-red-600">
                                    {{ denunciaForm.errors.motivo }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1.5">
                                    Detalles adicionales (opcional)
                                </label>
                                <textarea v-model="denunciaForm.descripcion" rows="3"
                                    placeholder="Explica brevemente qué sucede con este evento..."
                                    class="w-full rounded-xl border-slate-300 text-sm font-medium text-[#172A3D] focus:border-rose-500 focus:ring-rose-500"></textarea>
                                <p v-if="denunciaForm.errors.descripcion" class="mt-1 text-xs font-bold text-red-600">
                                    {{ denunciaForm.errors.descripcion }}
                                </p>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button type="button" @click="mostrarModalDenuncia = false"
                                    class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition hover:bg-slate-100">
                                    Cancelar
                                </button>
                                <button type="submit" :disabled="denunciaForm.processing"
                                    class="rounded-xl bg-rose-600 px-5 py-2.5 text-xs font-black text-white shadow-md transition hover:bg-rose-700 disabled:opacity-50">
                                    {{ denunciaForm.processing ? 'Enviando...' : 'Enviar denuncia' }}
                                </button>
                            </div>
                        </template>
                    </form>

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