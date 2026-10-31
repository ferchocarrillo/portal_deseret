<script setup>
import { computed, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();

const props = defineProps({
    roles: {
        type: Array,
        default: () => [],
    },

    permisos: {
        type: Array,
        default: () => [],
    },
});

const rolSeleccionado = ref(
    props.roles.length > 0 ? props.roles[0] : null
);

const permisosSeleccionados = ref(
    props.roles.length > 0
        ? props.roles[0].permissions.map(permission => permission.name)
        : []
);

const guardando = ref(false);

const mensajeExito = computed(() => {
    return page.props.flash?.success ?? null;
});


// ==========================================================
// SELECCIONAR ROL
// ==========================================================

const seleccionarRol = (rol) => {
    rolSeleccionado.value = rol;

    permisosSeleccionados.value = rol.permissions.map(
        permission => permission.name
    );
};


// ==========================================================
// AGRUPAR PERMISOS POR MÓDULO
// ==========================================================

const permisosAgrupados = computed(() => {
    const grupos = {};

    props.permisos.forEach((permiso) => {
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
// NOMBRE DEL MÓDULO
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
        compras: 'Compras',
        ventas: 'Ventas',
        calificaciones: 'Calificaciones',
        reportes: 'Reportes',
        perfil: 'Perfil / Mi cuenta',
        configuracion: 'Configuración',
        roles: 'Roles',
        permisos: 'Permisos',
    };

    return nombres[modulo] ?? modulo;
};


// ==========================================================
// NOMBRE DEL PERMISO
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


// ==========================================================
// COMPROBAR PERMISO
// ==========================================================

const tienePermiso = (nombre) => {
    return permisosSeleccionados.value.includes(nombre);
};


// ==========================================================
// CAMBIAR PERMISO
// ==========================================================

const cambiarPermiso = (nombre) => {
    if (tienePermiso(nombre)) {
        permisosSeleccionados.value =
            permisosSeleccionados.value.filter(
                permiso => permiso !== nombre
            );

        return;
    }

    permisosSeleccionados.value.push(nombre);
};


// ==========================================================
// SELECCIONAR TODOS LOS PERMISOS DE UN MÓDULO
// ==========================================================

const todosSeleccionados = (permisosModulo) => {
    return permisosModulo.every(
        permiso => tienePermiso(permiso.name)
    );
};


// ==========================================================
// CAMBIAR TODOS LOS PERMISOS DE UN MÓDULO
// ==========================================================

const cambiarModulo = (permisosModulo) => {
    const nombres = permisosModulo.map(
        permiso => permiso.name
    );

    if (todosSeleccionados(permisosModulo)) {
        permisosSeleccionados.value =
            permisosSeleccionados.value.filter(
                permiso => !nombres.includes(permiso)
            );

        return;
    }

    nombres.forEach((nombre) => {
        if (!permisosSeleccionados.value.includes(nombre)) {
            permisosSeleccionados.value.push(nombre);
        }
    });
};


// ==========================================================
// GUARDAR PERMISOS
// ==========================================================

const guardarPermisos = () => {
    if (!rolSeleccionado.value) {
        return;
    }

    guardando.value = true;

    router.put(
        route(
            'configuracion.roles-permisos.update',
            rolSeleccionado.value.id
        ),
        {
            permissions: permisosSeleccionados.value,
        },
        {
            preserveScroll: true,

            onFinish: () => {
                guardando.value = false;
            },
        }
    );
};
</script>


<template>

    <Head title="Roles y permisos" />

    <AuthenticatedLayout>

        <!-- ===================================================== -->
        <!-- CONTENIDO PRINCIPAL -->
        <!-- ===================================================== -->

        <div class="min-h-screen bg-gray-50 p-6 lg:p-8">

            <div class="mx-auto max-w-7xl">


                <!-- ================================================= -->
                <!-- ENCABEZADO -->
                <!-- ================================================= -->

                <div class="mb-8">

                    <h1 class="text-2xl font-bold text-[#263646]">
                        Roles y permisos
                    </h1>

                    <p class="mt-1 text-sm text-[#526174]">
                        Administra los permisos asignados a cada rol del sistema.
                    </p>

                </div>


                <!-- ================================================= -->
                <!-- MENSAJE DE ÉXITO -->
                <!-- ================================================= -->

                <div v-if="mensajeExito"
                    class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ mensajeExito }}
                </div>


                <!-- ================================================= -->
                <!-- CONTENIDO -->
                <!-- ================================================= -->

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">


                    <!-- ============================================= -->
                    <!-- ROLES -->
                    <!-- ============================================= -->

                    <div class="rounded-xl bg-white p-5 shadow-sm">

                        <div class="mb-4">

                            <h2 class="text-base font-semibold text-[#263646]">
                                Roles
                            </h2>

                            <p class="mt-1 text-xs text-gray-500">
                                Selecciona un rol para administrar sus permisos.
                            </p>

                        </div>


                        <div class="space-y-2">

                            <button v-for="rol in roles" :key="rol.id" type="button" @click="seleccionarRol(rol)"
                                class="w-full rounded-lg border px-4 py-3 text-left transition" :class="rolSeleccionado?.id === rol.id
                                        ? 'border-[#263646] bg-[#263646] text-white'
                                        : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'
                                    ">

                                <div class="font-medium">
                                    {{ rol.name }}
                                </div>

                                <div class="mt-1 text-xs" :class="rolSeleccionado?.id === rol.id
                                        ? 'text-gray-200'
                                        : 'text-gray-400'
                                    ">
                                    {{ rol.permissions.length }} permisos
                                </div>

                            </button>

                        </div>

                    </div>


                    <!-- ============================================= -->
                    <!-- PERMISOS -->
                    <!-- ============================================= -->

                    <div class="lg:col-span-3">

                        <div class="rounded-xl bg-white shadow-sm">


                            <!-- ===================================== -->
                            <!-- ENCABEZADO PERMISOS -->
                            <!-- ===================================== -->

                            <div class="border-b border-gray-200 p-6">

                                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

                                    <div>

                                        <h2 class="text-lg font-semibold text-[#263646]">
                                            Permisos
                                        </h2>

                                        <p v-if="rolSeleccionado" class="mt-1 text-sm text-[#526174]">
                                            Rol seleccionado:
                                            <span class="font-semibold">
                                                {{ rolSeleccionado.name }}
                                            </span>
                                        </p>

                                    </div>


                                    <div v-if="rolSeleccionado"
                                        class="rounded-lg bg-gray-100 px-4 py-2 text-sm text-gray-600">
                                        {{ permisosSeleccionados.length }}
                                        permisos seleccionados
                                    </div>

                                </div>

                            </div>


                            <!-- ===================================== -->
                            <!-- LISTA DE PERMISOS -->
                            <!-- ===================================== -->

                            <div v-if="rolSeleccionado" class="divide-y divide-gray-200">

                                <div v-for="(permisosModulo, modulo) in permisosAgrupados" :key="modulo" class="p-6">

                                    <!-- ============================= -->
                                    <!-- MÓDULO -->
                                    <!-- ============================= -->

                                    <div class="mb-4 flex items-center justify-between">

                                        <h3 class="text-sm font-semibold uppercase tracking-wide text-[#263646]">
                                            {{ nombreModulo(modulo) }}
                                        </h3>

                                        <button type="button" @click="cambiarModulo(permisosModulo)"
                                            class="text-xs font-medium text-[#526174] hover:text-[#263646]">
                                            {{
                                                todosSeleccionados(permisosModulo)
                                                    ? 'Quitar todos'
                                                    : 'Seleccionar todos'
                                            }}
                                        </button>

                                    </div>


                                    <!-- ============================= -->
                                    <!-- PERMISOS DEL MÓDULO -->
                                    <!-- ============================= -->

                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                                        <label v-for="permiso in permisosModulo" :key="permiso.id"
                                            class="flex cursor-pointer items-center rounded-lg border border-gray-200 px-4 py-3 transition hover:bg-gray-50">

                                            <input type="checkbox" :checked="tienePermiso(permiso.name)"
                                                @change="cambiarPermiso(permiso.name)"
                                                class="h-4 w-4 rounded border-gray-300 text-[#263646] focus:ring-[#263646]">

                                            <span class="ml-3 text-sm text-gray-700">
                                                {{ nombrePermiso(permiso.name) }}
                                            </span>

                                        </label>

                                    </div>

                                </div>

                            </div>


                            <!-- ===================================== -->
                            <!-- SIN ROL -->
                            <!-- ===================================== -->

                            <div v-else class="p-10 text-center">

                                <p class="text-sm text-gray-500">
                                    No hay roles disponibles.
                                </p>

                            </div>


                            <!-- ===================================== -->
                            <!-- GUARDAR -->
                            <!-- ===================================== -->

                            <div v-if="rolSeleccionado"
                                class="flex justify-end border-t border-gray-200 bg-gray-50 p-6">

                                <button type="button" @click="guardarPermisos" :disabled="guardando"
                                    class="rounded-lg bg-[#263646] px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#1d2936] disabled:cursor-not-allowed disabled:opacity-50">
                                    {{
                                        guardando
                                            ? 'Guardando...'
                                            : 'Guardar permisos'
                                    }}
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </AuthenticatedLayout>

</template>