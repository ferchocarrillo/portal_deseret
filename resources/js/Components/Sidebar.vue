<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const page = usePage();

// ==========================================================
// ESTADO DEL SIDEBAR
// ==========================================================
const sidebarColapsado = ref(true);
const menuMovilAbierto = ref(false);

const alternarMenuMovil = () => {
    menuMovilAbierto.value = !menuMovilAbierto.value;
    if (menuMovilAbierto.value) {
        sidebarColapsado.value = false;
    } else {
        sidebarColapsado.value = true;
    }
};

const cerrarMenuMovil = () => {
    menuMovilAbierto.value = false;
    sidebarColapsado.value = true;
};

// ==========================================================
// USUARIO & PERMISOS
// ==========================================================
const user = computed(() => page.props.auth?.user);

const permisos = computed(() => {
    return page.props.auth?.permissions ?? [];
});

const tienePermiso = (permiso) => {
    return permisos.value.includes(permiso);
};

// ==========================================================
// SOLICITUDES PENDIENTES
// ==========================================================
const solicitudesPendientes = computed(() => {
    return page.props.solicitudesPendientes ?? 0;
});

// ==========================================================
// DENUNCIAS PENDIENTES (BADGE Y POLLING)
// ==========================================================
const denunciasPendientes = ref(0);
let intervaloDenunciasPendientes = null;

const actualizarDenunciasPendientes = async () => {
    if (document.visibilityState === 'hidden') return;
    try {
        const response = await fetch(route('admin.denuncias.pendientes-count'), {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            cache: 'no-store',
        });

        if (!response.ok) return;

        const data = await response.json();
        denunciasPendientes.value = Number(data.denunciasPendientes ?? 0);
    } catch (error) {
        // Silenciamos errores de red temporales en el polling
    }
};

// ==========================================================
// MENSAJES NO LEÍDOS (POLLING)
// ==========================================================
const mensajesNoLeidos = ref(Number(page.props.mensajesNoLeidos ?? 0));
let intervaloMensajesNoLeidos = null;

const actualizarMensajesNoLeidos = async () => {
    if (document.visibilityState === 'hidden') return;
    try {
        const response = await fetch(route('marketplace.mensajes.no-leidos'), {
            method: 'GET',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            cache: 'no-store',
        });

        if (!response.ok) return;

        const data = await response.json();
        mensajesNoLeidos.value = Number(data.mensajesNoLeidos ?? 0);
    } catch (error) {
        // Silenciamos errores de red temporales en el polling
    }
};

onMounted(() => {
    actualizarMensajesNoLeidos();
    actualizarDenunciasPendientes();
    intervaloMensajesNoLeidos = window.setInterval(actualizarMensajesNoLeidos, 10000);
    intervaloDenunciasPendientes = window.setInterval(actualizarDenunciasPendientes, 15000);
});

onBeforeUnmount(() => {
    if (intervaloMensajesNoLeidos !== null) {
        window.clearInterval(intervaloMensajesNoLeidos);
        intervaloMensajesNoLeidos = null;
    }
    if (intervaloDenunciasPendientes !== null) {
        window.clearInterval(intervaloDenunciasPendientes);
        intervaloDenunciasPendientes = null;
    }
});

// ==========================================================
// FOTOGRAFÍA DEL USUARIO
// ==========================================================
const tieneFoto = computed(() => !!user.value?.foto);

const urlFoto = computed(() => {
    if (!user.value?.foto) return null;
    if (
        user.value.foto.startsWith('http://') ||
        user.value.foto.startsWith('https://') ||
        user.value.foto.startsWith('/')
    ) {
        return user.value.foto;
    }
    return `/storage/${user.value.foto}`;
});

const inicialUsuario = computed(() => {
    return user.value?.name?.charAt(0)?.toUpperCase() ?? '?';
});
</script>

<template>
    <!-- TELÓN OSCURO PARA MÓVIL CUANDO ESTÁ ABIERTO -->
    <div v-if="menuMovilAbierto" class="fixed inset-0 z-40 bg-slate-950/40 backdrop-blur-xs lg:hidden"
        @click="cerrarMenuMovil"></div>

    <aside
        class="fixed left-0 top-0 z-50 flex h-16 w-full shrink-0 flex-col border-b border-gray-200 bg-white transition-all duration-300 ease-in-out lg:sticky lg:h-screen lg:border-b-0 lg:border-r lg:shadow-none"
        :class="sidebarColapsado ? 'lg:w-[76px]' : 'lg:w-64'">
        <!-- CABECERA DEL SIDEBAR -->
        <div class="relative flex h-16 shrink-0 items-center justify-between border-b border-gray-100 bg-white px-4 lg:h-20"
            :class="sidebarColapsado ? 'lg:justify-center lg:px-2' : 'lg:px-5'">
            <Link href="/" class="flex min-w-0 items-center overflow-hidden">
                <img src="/images/landing/logo.png" alt="Portal Deseret" class="block transition-all duration-300"
                    :class="sidebarColapsado ? 'h-9 w-9 object-contain' : 'h-9 w-auto object-contain'" />
            </Link>

            <button type="button" @click="alternarMenuMovil"
                class="ml-3 inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-200 lg:hidden"
                :aria-expanded="menuMovilAbierto" aria-label="Menú">
                <span>{{ menuMovilAbierto ? '✕' : '☰' }}</span>
                <span>{{ menuMovilAbierto ? 'Cerrar' : 'Menú' }}</span>
            </button>

            <button type="button" @click="sidebarColapsado = !sidebarColapsado"
                class="absolute -right-3.5 top-1/2 z-50 hidden h-7 w-7 -translate-y-1/2 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 shadow-md transition hover:bg-slate-50 hover:text-slate-900 lg:flex"
                :title="sidebarColapsado ? 'Expandir menú' : 'Contraer menú'">
                <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': !sidebarColapsado }"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>

        <!-- CONTENIDO Y NAVEGACIÓN -->
        <div class="lg:min-h-0 lg:flex-1 lg:flex lg:flex-col lg:overflow-hidden" :class="menuMovilAbierto
            ? 'absolute left-0 right-0 top-full flex max-h-[calc(100vh-4rem)] flex-col overflow-y-auto border-b border-gray-200 bg-white shadow-xl lg:static lg:max-h-none'
            : 'hidden lg:flex'
            ">
            <nav class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden py-3"
                :class="sidebarColapsado ? 'px-2' : 'px-3'">

                <!-- SECCIÓN GENERAL -->
                <div v-if="tienePermiso('dashboard.ver')" class="mb-5">
                    <p v-if="!sidebarColapsado || menuMovilAbierto"
                        class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 whitespace-nowrap">
                        General
                    </p>

                    <Link href="/dashboard"
                        class="group flex items-center rounded-xl py-2 text-sm font-semibold text-gray-700 transition hover:bg-slate-100 hover:text-slate-900"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'px-3'" title="Inicio">
                        <span class="text-base shrink-0">🏠</span>
                        <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                            Inicio
                        </span>
                    </Link>

                    <!-- ENLACE AL CENTRO DE AYUDA Y TUTORIALES (VISIBLE PARA TODOS) -->
                    <Link :href="route('tutorials.index')"
                        class="mt-1 group flex items-center rounded-xl py-2 text-sm font-semibold text-gray-700 transition hover:bg-slate-100 hover:text-slate-900"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'px-3'" title="Ayuda y Tutoriales">
                        <span class="text-base shrink-0">🎥</span>
                        <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                            Ayuda y Tutoriales
                        </span>
                    </Link>
                </div>

                <!-- SECCIÓN ADMINISTRACIÓN -->
                <div class="mb-5">
                    <p v-if="!sidebarColapsado || menuMovilAbierto"
                        class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 whitespace-nowrap">
                        Administración
                    </p>

                    <!-- Solicitudes de ingreso -->
                    <Link v-if="tienePermiso('solicitudes.ver')" href="/admin/solicitudes"
                        class="group relative flex items-center rounded-xl py-2 text-sm font-semibold text-gray-700 transition hover:bg-slate-100 hover:text-slate-900"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'justify-between px-3'"
                        title="Solicitudes de ingreso">
                        <div class="flex items-center min-w-0">
                            <span class="text-base shrink-0">📥</span>
                            <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                                Solicitudes de ingreso
                            </span>
                        </div>

                        <span v-if="solicitudesPendientes > 0"
                            class="inline-flex items-center justify-center rounded-full bg-red-500 font-bold text-white"
                            :class="sidebarColapsado && !menuMovilAbierto
                                ? 'absolute -top-1 -right-1 h-4 w-4 text-[9px]'
                                : 'h-5 min-w-[20px] px-1 text-[11px]'
                                ">
                            {{ solicitudesPendientes }}
                        </span>
                    </Link>

                    <!-- Usuarios -->
                    <Link v-if="tienePermiso('usuarios.ver')" href="/admin/usuarios"
                        class="mt-1 flex items-center rounded-xl py-2 text-sm font-semibold text-gray-700 transition hover:bg-slate-100 hover:text-slate-900"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'px-3'" title="Usuarios">
                        <span class="text-base shrink-0">👥</span>
                        <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                            Usuarios
                        </span>
                    </Link>

                    <!-- Publicaciones de toda la comunidad (ADMIN) -->
                    <Link v-if="tienePermiso('publicaciones.editar')" :href="route('admin.publicaciones.index')"
                        class="mt-1 flex items-center rounded-xl py-2 text-sm font-semibold text-gray-700 transition hover:bg-slate-100 hover:text-slate-900"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'px-3'" title="Publicaciones">
                        <span class="text-base shrink-0">📄</span>
                        <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                            Publicaciones
                        </span>
                    </Link>

                    <!-- GESTIÓN DE ERRORES (ADMIN) -->
                    <Link v-if="tienePermiso('configuracion.ver')" :href="route('admin.reportes-errores.index')"
                        class="mt-1 flex items-center rounded-xl py-2 text-sm font-semibold text-gray-700 transition hover:bg-slate-100 hover:text-slate-900"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'px-3'" title="Reportes de Errores">
                        <span class="text-base shrink-0">🐛</span>
                        <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                            Reportes de Errores
                        </span>
                    </Link>
                    <Link v-if="tienePermiso('configuracion.ver')" :href="route('admin.buzon.index')"
                        class="mt-1 flex items-center rounded-xl py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'px-3'" title="Buzón de Opinión">
                        <span class="text-base shrink-0">📬</span>
                        <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                            Buzón de Opiniones
                        </span>
                    </Link>

                    <!-- Gestión de Denuncias -->
                    <Link v-if="tienePermiso('denuncias.ver')" :href="route('admin.denuncias.index')"
                        class="group relative mt-1 flex items-center rounded-xl py-2 text-sm font-semibold text-gray-700 transition hover:bg-slate-100 hover:text-slate-900"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'justify-between px-3'" title="Denuncias">
                        <div class="flex items-center min-w-0">
                            <span class="text-base shrink-0">📢</span>
                            <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                                Denuncias
                            </span>
                        </div>

                        <span v-if="denunciasPendientes > 0"
                            class="inline-flex items-center justify-center rounded-full bg-red-600 font-bold text-white"
                            :class="sidebarColapsado && !menuMovilAbierto
                                ? 'absolute -top-1 -right-1 h-4 w-4 text-[9px]'
                                : 'h-5 min-w-[20px] px-1 text-[11px]'
                                ">
                            {{ denunciasPendientes }}
                        </span>
                    </Link>
                </div>

                <!-- SECCIÓN CONFIGURACIÓN -->
                <div v-if="
                    tienePermiso('configuracion.ver') ||
                    tienePermiso('roles.ver') ||
                    tienePermiso('permisos.ver')
                " class="mb-5">
                    <p v-if="!sidebarColapsado || menuMovilAbierto"
                        class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 whitespace-nowrap">
                        Configuración
                    </p>

                    <Link v-if="tienePermiso('configuracion.ver')" :href="route('admin.configuracion.index')"
                        class="flex items-center rounded-xl py-2 text-sm font-semibold text-gray-700 transition hover:bg-slate-100 hover:text-slate-900"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'px-3'" title="Configuraciones">
                        <span class="text-base shrink-0">⚙️</span>
                        <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                            Configuraciones
                        </span>
                    </Link>

                    <Link v-if="tienePermiso('roles.ver') || tienePermiso('permisos.ver')"
                        href="/configuracion/roles-permisos"
                        class="mt-1 flex items-center rounded-xl py-2 text-sm font-semibold text-gray-700 transition hover:bg-slate-100 hover:text-slate-900"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'px-3'" title="Roles y permisos">
                        <span class="text-base shrink-0">🔐</span>
                        <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                            Roles y permisos
                        </span>
                    </Link>
                </div>

                <!-- SECCIÓN MI CUENTA -->
                <div v-if="
                    tienePermiso('publicaciones.ver') ||
                    tienePermiso('favoritos.ver') ||
                    tienePermiso('mensajes.ver') ||
                    tienePermiso('compras.ver_propias') ||
                    tienePermiso('ventas.ver_propias') ||
                    tienePermiso('perfil.ver')
                " class="mb-4">
                    <p v-if="!sidebarColapsado || menuMovilAbierto"
                        class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 whitespace-nowrap">
                        Mi cuenta
                    </p>

                    <!-- Mis publicaciones propias -->
                    <Link v-if="tienePermiso('publicaciones.ver')" :href="route('publicaciones.mis-publicaciones')"
                        class="flex items-center rounded-xl py-2 text-sm font-semibold text-gray-700 transition hover:bg-slate-100 hover:text-slate-900"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'px-3'" title="Mis publicaciones">
                        <span class="text-base shrink-0">📄</span>
                        <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                            Mis publicaciones
                        </span>
                    </Link>

                    <!-- Favoritos -->
                    <Link v-if="tienePermiso('favoritos.ver')" :href="route('favoritos.index')"
                        class="mt-1 flex items-center rounded-xl py-2 text-sm font-semibold text-gray-700 transition hover:bg-slate-100 hover:text-slate-900"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'px-3'" title="Favoritos">
                        <span class="text-base shrink-0">⭐</span>
                        <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                            Favoritos
                        </span>
                    </Link>

                    <!-- Mensajes -->
                    <Link v-if="tienePermiso('mensajes.ver')" :href="route('mensajes.index')"
                        class="group relative mt-1 flex items-center rounded-xl py-2 text-sm font-semibold text-gray-700 transition hover:bg-slate-100 hover:text-slate-900"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'justify-between px-3'" title="Mensajes">
                        <div class="flex items-center min-w-0">
                            <span class="text-base shrink-0">💬</span>
                            <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                                Mensajes
                            </span>
                        </div>

                        <span v-if="mensajesNoLeidos > 0"
                            class="inline-flex items-center justify-center rounded-full bg-red-500 font-bold text-white"
                            :class="sidebarColapsado && !menuMovilAbierto
                                ? 'absolute -top-1 -right-1 h-4 w-4 text-[9px]'
                                : 'h-5 min-w-[20px] px-1 text-[11px]'
                                ">
                            {{ mensajesNoLeidos }}
                        </span>
                    </Link>

                    <!-- Mi perfil -->
                    <Link v-if="tienePermiso('perfil.ver')" href="/perfil"
                        class="mt-1 flex items-center rounded-xl py-2 text-sm font-semibold text-gray-700 transition hover:bg-slate-100 hover:text-slate-900"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'px-3'" title="Mi perfil">
                        <span class="text-base shrink-0">👤</span>
                        <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                            Mi perfil
                        </span>
                    </Link>

                    <Link :href="route('buzon.create')"
                        class="mt-1 flex items-center rounded-xl py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'px-3'" title="Buzón de Opinión">
                        <span class="text-base shrink-0">📬</span>
                        <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-3 truncate whitespace-nowrap">
                            Buzón de Opinión
                        </span>
                    </Link>

                    <!-- REPORTAR ERROR (USUARIO GENERAL) -->
                    <Link :href="route('reportes-errores.create')"
                        class="mt-1 flex items-center rounded-xl py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50"
                        :class="sidebarColapsado ? 'justify-center px-2' : 'px-3'" title="Reportar un problema">
                        <span class="text-base shrink-0">🛠️</span>
                        <span v-if="!sidebarColapsado || menuMovilAbierto"
                            class="ml-3 truncate whitespace-nowrap font-bold">
                            Reportar Problema
                        </span>
                    </Link>
                </div>
            </nav>

            <!-- USUARIO Y CERRAR SESIÓN -->
            <div class="shrink-0 border-t border-gray-200 bg-white"
                :class="sidebarColapsado && !menuMovilAbierto ? 'p-2' : 'p-3'">
                <div class="mb-2 flex items-center"
                    :class="sidebarColapsado && !menuMovilAbierto ? 'justify-center' : ''">
                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-100 ring-1 ring-slate-200">
                        <img v-if="tieneFoto" :src="urlFoto" :alt="`Foto de ${user?.name}`"
                            class="h-full w-full object-cover" />
                        <span v-else class="text-xs font-bold text-slate-800">
                            {{ inicialUsuario }}
                        </span>
                    </div>

                    <div v-if="!sidebarColapsado || menuMovilAbierto" class="ml-2.5 min-w-0 flex-1 overflow-hidden">
                        <p class="truncate text-xs font-bold text-slate-900 whitespace-nowrap">
                            {{ user?.name }}
                        </p>
                        <p class="truncate text-[11px] text-gray-400 whitespace-nowrap">
                            {{ user?.role ?? 'Usuario' }}
                        </p>
                    </div>
                </div>

                <Link href="/logout" method="post" as="button"
                    class="flex w-full items-center rounded-xl py-1.5 text-xs font-bold text-rose-600 transition hover:bg-rose-50"
                    :class="sidebarColapsado && !menuMovilAbierto ? 'justify-center px-2' : 'px-3'"
                    title="Cerrar sesión">
                    <span class="text-base shrink-0">🚪</span>
                    <span v-if="!sidebarColapsado || menuMovilAbierto" class="ml-2.5 whitespace-nowrap">
                        Cerrar sesión
                    </span>
                </Link>
            </div>
        </div>
    </aside>
</template>