<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
</script>

<template>

    <Head title="Configuración del Sistema | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="true">
        <div class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo py-10">
            <main class="mx-auto max-w-4xl px-6 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-300 shadow-xl">
                    <p class="text-xs font-bold text-[#D99A24] uppercase tracking-wider">Administración</p>
                    <h1 class="text-2xl font-black text-[#172A3D]">Panel de Configuración</h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Ajusta los parámetros generales del sistema
                        y herramientas de moderación.</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Tarjeta de Palabras Prohibidas -->
                    <Link :href="route('admin.palabras.index')"
                        class="bg-white p-6 rounded-2xl border border-slate-300 shadow-lg hover:border-[#D99A24] transition group">
                        <div class="text-2xl mb-2">🚫</div>
                        <h2 class="font-bold text-[#172A3D] group-hover:text-[#D99A24] transition">Palabras Prohibidas
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">Gestiona el diccionario de términos bloqueados en los
                            formularios, chats y publicaciones.</p>
                    </Link>

                    <!-- Tarjeta de Gestión de Tutoriales -->
                    <Link :href="route('admin.tutorials.index')"
                        class="bg-white p-6 rounded-2xl border border-slate-300 shadow-lg hover:border-[#D99A24] transition group">
                        <div class="text-2xl mb-2">🎥</div>
                        <h2 class="font-bold text-[#172A3D] group-hover:text-[#D99A24] transition">Gestión de Tutoriales
                        </h2>
                        <p class="text-xs text-slate-500 mt-1">Administra los videos de ayuda y el centro de aprendizaje
                            para los usuarios.</p>
                    </Link>
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