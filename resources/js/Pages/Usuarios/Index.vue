<script setup>
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const page = usePage();

const props = defineProps({
    usuarios: {
        type: Array,
        default: () => [],
    },

    roles: {
        type: Array,
        default: () => [],
    },
});

const mensajeExito = computed(() => {
    return page.props.flash?.success ?? null;
});


// ==========================================================
// NOMBRE DEL ROL
// ==========================================================

const nombreRol = (usuario) => {
    if (!usuario.roles || usuario.roles.length === 0) {
        return 'Sin rol';
    }

    return usuario.roles[0].name;
};


// ==========================================================
// FORMATO DE FECHA
// ==========================================================

const formatoFecha = (fecha) => {

    if (!fecha) {
        return '—';
    }

    return new Date(fecha).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    });
};
</script>


<template>

    <Head title="Usuarios" />

    <AuthenticatedLayout>

        <div class="min-h-screen p-6 lg:p-8">

            <div class="mx-auto max-w-7xl">


                <!-- ================================================= -->
                <!-- ENCABEZADO -->
                <!-- ================================================= -->

                <div class="mb-8">

                    <h1 class="text-2xl font-bold text-[#263646]">
                        Usuarios
                    </h1>

                    <p class="mt-1 text-sm text-[#526174]">
                        Administra los usuarios registrados en el Portal Deseret.
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
                <!-- TABLA -->
                <!-- ================================================= -->

                <div class="overflow-hidden rounded-xl bg-white shadow-sm">

                    <!-- ============================================= -->
                    <!-- CABECERA -->
                    <!-- ============================================= -->

                    <div
                        class="flex flex-col justify-between gap-4 border-b border-gray-200 p-6 sm:flex-row sm:items-center">

                        <div>

                            <h2 class="text-lg font-semibold text-[#263646]">
                                Usuarios registrados
                            </h2>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ usuarios.length }}
                                usuario{{ usuarios.length === 1 ? '' : 's' }}
                                registrado{{ usuarios.length === 1 ? '' : 's' }}.
                            </p>

                        </div>

                    </div>


                    <!-- ============================================= -->
                    <!-- TABLA -->
                    <!-- ============================================= -->

                    <div v-if="usuarios.length > 0" class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Usuario
                                    </th>

                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Correo
                                    </th>

                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Rol
                                    </th>

                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Registro
                                    </th>

                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Acciones
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-gray-200 bg-white">

                                <tr v-for="usuario in usuarios" :key="usuario.id" class="transition hover:bg-gray-50">

                                    <!-- ================================= -->
                                    <!-- USUARIO -->
                                    <!-- ================================= -->

                                    <td class="whitespace-nowrap px-6 py-4">

                                        <div class="flex items-center">

                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-[#263646]">
                                                {{ usuario.name?.charAt(0)?.toUpperCase() ?? '?' }}
                                            </div>

                                            <div class="ml-3">

                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ usuario.name }}
                                                </div>

                                                <div class="text-xs text-gray-400">
                                                    ID: {{ usuario.id }}
                                                </div>

                                            </div>

                                        </div>

                                    </td>


                                    <!-- ================================= -->
                                    <!-- CORREO -->
                                    <!-- ================================= -->

                                    <td class="whitespace-nowrap px-6 py-4">

                                        <span class="text-sm text-gray-700">
                                            {{ usuario.email }}
                                        </span>

                                    </td>


                                    <!-- ================================= -->
                                    <!-- ROL -->
                                    <!-- ================================= -->

                                    <td class="whitespace-nowrap px-6 py-4">

                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium" :class="nombreRol(usuario) === 'Administrador General'
                                                ? 'bg-blue-100 text-blue-700'
                                                : 'bg-gray-100 text-gray-700'
                                            ">
                                            {{ nombreRol(usuario) }}
                                        </span>

                                    </td>


                                    <!-- ================================= -->
                                    <!-- FECHA -->
                                    <!-- ================================= -->

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">

                                        {{ formatoFecha(usuario.created_at) }}

                                    </td>


                                    <!-- ================================= -->
                                    <!-- ACCIONES -->
                                    <!-- ================================= -->

                                    <td class="whitespace-nowrap px-6 py-4 text-right">

                                        <Link :href="route('usuarios.show', usuario.id)"
                                            class="mr-3 text-sm font-medium text-[#526174] hover:text-[#263646]">
                                            Ver
                                        </Link>

                                        <Link :href="route('usuarios.edit', usuario.id)"
                                            class="text-sm font-medium text-[#263646] hover:underline">
                                            Editar
                                        </Link>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>


                    <!-- ============================================= -->
                    <!-- SIN USUARIOS -->
                    <!-- ============================================= -->

                    <div v-else class="p-12 text-center">

                        <div class="mb-4 text-4xl">
                            👥
                        </div>

                        <h3 class="text-base font-semibold text-[#263646]">
                            No hay usuarios registrados
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Actualmente no existen usuarios registrados en el sistema.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </AuthenticatedLayout>

</template>