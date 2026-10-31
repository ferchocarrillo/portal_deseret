<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    usuario: {
        type: Object,
        required: true,
    },
});


// ==========================================================
// ROLES Y PERMISOS
// ==========================================================

const roles = computed(() => {
    return props.usuario.roles ?? [];
});

const permisos = computed(() => {
    return props.usuario.permissions ?? [];
});

const nombreRol = computed(() => {
    if (roles.value.length === 0) {
        return 'Sin rol';
    }

    return roles.value[0].name;
});


// ==========================================================
// FOTOGRAFÍA
// ==========================================================

const inicial = computed(() => {
    return props.usuario.name?.charAt(0)?.toUpperCase() ?? '?';
});

const tieneFoto = computed(() => {
    return !!props.usuario.foto;
});

const urlFoto = computed(() => {
    if (!props.usuario.foto) {
        return null;
    }

    if (
        props.usuario.foto.startsWith('http://') ||
        props.usuario.foto.startsWith('https://') ||
        props.usuario.foto.startsWith('/')
    ) {
        return props.usuario.foto;
    }

    return `/storage/${props.usuario.foto}`;
});


// ==========================================================
// FECHAS
// ==========================================================

const formatoFecha = (fecha) => {
    if (!fecha) {
        return 'Nunca';
    }

    return new Date(fecha).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatoFechaHora = (fecha) => {
    if (!fecha) {
        return 'Nunca';
    }

    return new Date(fecha).toLocaleString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};


// ==========================================================
// ESTADO DE LA CUENTA (ACTIVO / INACTIVO / BLOQUEADO)
// ==========================================================

const estaBloqueado = computed(() => {
    if (!props.usuario.bloqueado_hasta) return false;
    return new Date(props.usuario.bloqueado_hasta) > new Date();
});

const estaInactivo = computed(() => {
    return props.usuario.activo === 0 ||
        props.usuario.activo === false ||
        props.usuario.activo === '0' ||
        props.usuario.activo === null;
});

const estadoCuenta = computed(() => {
    // 1. Prioridad: Bloqueo/suspensión por fecha
    if (estaBloqueado.value) {
        return {
            label: 'Bloqueado / Suspendido',
            colorPunto: 'bg-red-500',
            colorTexto: 'text-red-800',
            colorCard: 'border-red-200 bg-red-50',
            colorTitulo: 'text-red-700',
            detalle: `Hasta: ${formatoFechaHora(props.usuario.bloqueado_hasta)}`,
        };
    }

    // 2. Inactivo en tabla users (columna activo = 0)
    if (estaInactivo.value) {
        return {
            label: 'Inactivo / Desactivado',
            colorPunto: 'bg-slate-400',
            colorTexto: 'text-slate-700',
            colorCard: 'border-slate-200 bg-slate-50',
            colorTitulo: 'text-slate-500',
            detalle: 'Cuenta deshabilitada en el sistema',
        };
    }

    // 3. Activo
    return {
        label: 'Activo',
        colorPunto: 'bg-green-500',
        colorTexto: 'text-green-700',
        colorCard: 'border-gray-200 bg-gray-50',
        colorTitulo: 'text-gray-400',
        detalle: null,
    };
});


// ==========================================================
// ACTIVIDAD
// ==========================================================

const actividad = computed(() => {
    return [
        {
            nombre: 'Publicaciones',
            valor: props.usuario.publicaciones_count ?? 0,
            icono: '📄',
        },
        {
            nombre: 'Favoritos',
            valor: props.usuario.favoritos_count ?? 0,
            icono: '⭐',
        },
        {
            nombre: 'Reacciones',
            valor: props.usuario.reacciones_count ?? 0,
            icono: '👍',
        },
        {
            nombre: 'Comentarios',
            valor: props.usuario.comentarios_count ?? 0,
            icono: '💬',
        },
        {
            nombre: 'Calificaciones realizadas',
            valor: props.usuario.calificaciones_count ?? 0,
            icono: '⭐',
        },
        {
            nombre: 'Calificaciones recibidas',
            valor: props.usuario.calificaciones_recibidas_count ?? 0,
            icono: '🌟',
        },
        {
            nombre: 'Interacciones',
            valor: props.usuario.interacciones_count ?? 0,
            icono: '🔄',
        },
        {
            nombre: 'Reportes enviados',
            valor: props.usuario.reportes_count ?? 0,
            icono: '🚩',
        },
        {
            nombre: 'Advertencias / Sanciones',
            valor: props.usuario.infractions_count ?? 0,
            icono: '⚠️',
            destacado: (props.usuario.infractions_count ?? 0) > 0,
        },
        {
            nombre: 'Invitaciones realizadas',
            valor: props.usuario.invitaciones_count ?? 0,
            icono: '✉️',
        },
        {
            nombre: 'Invitaciones recibidas',
            valor: props.usuario.invitaciones_recibidas_count ?? 0,
            icono: '📨',
        },
    ];
});


// ==========================================================
// PERMISOS AGRUPADOS
// ==========================================================

const permisosAgrupados = computed(() => {
    const grupos = {};

    permisos.value.forEach((permiso) => {
        const partes = permiso.name.split('.');
        const modulo = partes[0];

        if (!grupos[modulo]) {
            grupos[modulo] = [];
        }

        grupos[modulo].push(permiso);
    });

    return grupos;
});


// ==========================================================
// NOMBRE DE MÓDULOS
// ==========================================================

const nombreModulo = (modulo) => {
    const nombres = {
        dashboard: 'Dashboard',
        solicitudes: 'Solicitudes de ingreso',
        usuarios: 'Usuarios',
        publicaciones: 'Publicaciones',
        favoritos: 'Favoritos',
        reacciones: 'Reacciones',
        comentarios: 'Comentarios',
        mensajes: 'Mensajes',
        calificaciones: 'Calificaciones',
        reportes: 'Reportes',
        invitaciones: 'Invitaciones',
        perfil: 'Perfil / Mi cuenta',
        configuracion: 'Configuración',
        roles: 'Roles',
        permisos: 'Permisos',
    };

    return nombres[modulo] ?? modulo;
};


// ==========================================================
// NOMBRE DE PERMISOS
// ==========================================================

const nombrePermiso = (permiso) => {
    const partes = permiso.split('.');
    const accion = partes.slice(1).join('.');

    const nombres = {
        ver: 'Ver',
        ver_detalle: 'Ver detalle',
        crear: 'Crear',
        editar: 'Editar',
        editar_propias: 'Editar propias',
        eliminar: 'Eliminar',
        eliminar_propias: 'Eliminar propias',
        activar: 'Activar',
        desactivar: 'Desactivar',
        aprobar: 'Aprobar',
        rechazar: 'Rechazar',
        destacar: 'Destacar',
        agregar: 'Agregar',
        enviar: 'Enviar',
        cancelar_propias: 'Cancelar propias',
        gestionar_propias: 'Gestionar propias',
        resolver: 'Resolver',
        gestionar: 'Gestionar',
        ver_propios: 'Ver propios',
        cambiar_password: 'Cambiar contraseña',
        asignar: 'Asignar',
        restablecer_password: 'Restablecer contraseña',
    };

    return nombres[accion] ?? accion.replaceAll('_', ' ');
};
</script>


<template>

    <Head :title="`Usuario - ${usuario.name}`" />

    <AuthenticatedLayout>
        <div class="min-h-screen p-6 lg:p-8">
            <div class="mx-auto max-w-7xl">

                <!-- ENCABEZADO -->
                <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-[#263646]">
                            Información del usuario
                        </h1>
                        <p class="mt-1 text-sm text-[#526174]">
                            Consulta la información, actividad y permisos del usuario.
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <Link :href="route('usuarios.index')"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                            Volver
                        </Link>
                       <Link :href="route('usuarios.edit', { user: usuario.id })"
                            class="rounded-lg bg-[#263646] px-4 py-2.5 text-sm font-medium text-white transition hover:bg-[#1d2936]">
                            Editar usuario
                        </Link>
                    </div>
                </div>

                <!-- INFORMACIÓN PRINCIPAL -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                    <!-- PERFIL -->
                    <div class="rounded-xl bg-white p-6 shadow-sm">
                        <div class="flex flex-col items-center text-center">
                            <div
                                class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-full bg-gray-100">
                                <img v-if="tieneFoto" :src="urlFoto" :alt="`Fotografía de ${usuario.name}`"
                                    class="h-full w-full object-cover" />
                                <span v-else class="text-4xl font-semibold text-[#263646]">
                                    {{ inicial }}
                                </span>
                            </div>

                            <h2 class="mt-5 text-xl font-semibold text-[#263646]">
                                {{ usuario.name }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ usuario.email }}
                            </p>

                            <span class="mt-4 inline-flex rounded-full px-4 py-1.5 text-xs font-medium" :class="nombreRol === 'Administrador General'
                                ? 'bg-blue-100 text-blue-700'
                                : 'bg-gray-100 text-gray-700'
                                ">
                                {{ nombreRol }}
                            </span>
                        </div>

                        <div class="mt-8 space-y-5 border-t border-gray-200 pt-6">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    ID de usuario
                                </p>
                                <p class="mt-1 text-sm text-gray-700">
                                    {{ usuario.id }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Fecha de registro
                                </p>
                                <p class="mt-1 text-sm text-gray-700">
                                    {{ formatoFecha(usuario.created_at) }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Última actualización
                                </p>
                                <p class="mt-1 text-sm text-gray-700">
                                    {{ formatoFechaHora(usuario.updated_at) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ACCESO -->
                    <div class="rounded-xl bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-[#263646]">
                            Actividad de acceso
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Información relacionada con el acceso a la plataforma.
                        </p>

                        <div class="mt-6 space-y-6">
                            <!-- ÚLTIMA VISITA -->
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Última visita
                                </p>
                                <p class="mt-2 text-sm font-medium text-[#263646]">
                                    {{ formatoFechaHora(usuario.ultima_visita) }}
                                </p>
                            </div>

                            <!-- NÚMERO DE ACCESOS -->
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Número de accesos
                                </p>
                                <p class="mt-2 text-3xl font-bold text-[#263646]">
                                    {{ usuario.numero_accesos ?? 0 }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">
                                    ingresos registrados
                                </p>
                            </div>

                            <!-- ESTADO DE LA CUENTA -->
                            <div class="rounded-lg border p-4 transition-colors" :class="estadoCuenta.colorCard">
                                <p class="text-xs font-semibold uppercase tracking-wide"
                                    :class="estadoCuenta.colorTitulo">
                                    Estado
                                </p>

                                <div class="mt-2 flex items-center">
                                    <span class="mr-2 h-2.5 w-2.5 rounded-full" :class="estadoCuenta.colorPunto"></span>
                                    <span class="text-sm font-bold" :class="estadoCuenta.colorTexto">
                                        {{ estadoCuenta.label }}
                                    </span>
                                </div>

                                <p v-if="estadoCuenta.detalle" class="mt-1.5 text-xs font-medium"
                                    :class="estaBloqueado ? 'text-red-600' : 'text-slate-500'">
                                    {{ estadoCuenta.detalle }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- RESUMEN DE PERMISOS -->
                    <div class="rounded-xl bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-semibold text-[#263646]">
                            Permisos
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Permisos efectivos del usuario.
                        </p>

                        <div class="mt-6">
                            <div class="text-4xl font-bold text-[#263646]">
                                {{ permisos.length }}
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                permisos asignados
                            </p>
                        </div>

                        <div class="mt-6 border-t border-gray-200 pt-5">
                            <p class="text-xs text-gray-500">
                                Los permisos mostrados corresponden a los permisos efectivos proporcionados por los
                                roles asignados mediante Spatie.
                            </p>
                        </div>
                    </div>

                </div>

                <!-- ACTIVIDAD -->
                <div class="mt-6 rounded-xl bg-white shadow-sm">
                    <div class="border-b border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-[#263646]">
                            Actividad del usuario
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Resumen de la actividad registrada dentro de la plataforma.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <div v-for="item in actividad" :key="item.nombre" :class="[
                            'rounded-lg border p-5 transition',
                            item.destacado
                                ? 'border-red-300 bg-red-50/80 shadow-xs ring-1 ring-red-200'
                                : 'border-gray-200 bg-gray-50'
                        ]">
                            <div class="flex items-center justify-between">
                                <span class="text-xl">
                                    {{ item.icono }}
                                </span>
                                <span
                                    :class="['text-2xl font-bold', item.destacado ? 'text-red-700' : 'text-[#263646]']">
                                    {{ item.valor }}
                                </span>
                            </div>
                            <p
                                :class="['mt-3 text-sm font-medium', item.destacado ? 'text-red-800 font-semibold' : 'text-gray-600']">
                                {{ item.nombre }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ROLES -->
                <div class="mt-6 rounded-xl bg-white shadow-sm">
                    <div class="border-b border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-[#263646]">
                            Roles
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Roles asignados actualmente al usuario.
                        </p>
                    </div>

                    <div class="p-6">
                        <div v-if="roles.length > 0" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div v-for="rol in roles" :key="rol.id"
                                class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-4">
                                <div class="text-sm font-medium text-[#263646]">
                                    {{ rol.name }}
                                </div>
                                <div class="mt-1 text-xs text-gray-400">
                                    Rol del sistema
                                </div>
                            </div>
                        </div>

                        <div v-else class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-500">
                            Este usuario no tiene ningún rol asignado.
                        </div>
                    </div>
                </div>

                <!-- PERMISOS DETALLADOS -->
                <div class="mt-6 rounded-xl bg-white shadow-sm">
                    <div class="border-b border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-[#263646]">
                            Permisos del usuario
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Permisos efectivos proporcionados por sus roles.
                        </p>
                    </div>

                    <div v-if="permisos.length > 0" class="divide-y divide-gray-200">
                        <div v-for="(permisosModulo, modulo) in permisosAgrupados" :key="modulo" class="p-6">
                            <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-[#263646]">
                                {{ nombreModulo(modulo) }}
                            </h3>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                <div v-for="permiso in permisosModulo" :key="permiso.id"
                                    class="flex items-center rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                                    <span
                                        class="mr-3 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-green-100 text-xs text-green-700">
                                        ✓
                                    </span>
                                    <span class="text-sm text-gray-700">
                                        {{ nombrePermiso(permiso.name) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-10 text-center">
                        <p class="text-sm text-gray-500">
                            Este usuario no tiene permisos asignados.
                        </p>
                    </div>
                </div>

                <!-- VOLVER -->
                <div class="mt-6">
                    <Link :href="route('usuarios.index')"
                        class="text-sm font-medium text-[#526174] hover:text-[#263646]">
                        ← Volver al listado de usuarios
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>