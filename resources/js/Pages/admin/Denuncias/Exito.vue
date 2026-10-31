<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
</script>

<template>

    <Head title="Denuncia Registrada | Portal Deseret" />

    <AuthenticatedLayout :carboncillo="true">
        <div
            class="min-h-screen bg-cover bg-center bg-fixed text-[#172A3D] fondo-carboncillo flex items-center justify-center px-4">
            <div
                class="max-w-md w-full rounded-2xl border border-slate-300 bg-white p-8 shadow-2xl shadow-red-500/15 text-center space-y-5">
                <div
                    class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 border border-emerald-200 text-3xl">
                    ✓
                </div>

                <div>
                    <h1 class="text-xl font-black text-[#172A3D]">
                        ¡Denuncia enviada con éxito!
                    </h1>
                    <p class="mt-2 text-xs sm:text-sm text-slate-600 font-medium leading-relaxed">
                        Gracias por ayudarnos a mantener segura a nuestra comunidad. Tu reporte ha sido registrado y
                        será revisado por nuestro equipo de moderación.
                    </p>
                </div>

                <div class="pt-2">
                    <Link :href="route('dashboard')"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-6 py-3 text-xs sm:text-sm font-black text-white shadow-md transition hover:bg-slate-800">
                        Volver al inicio
                    </Link>
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