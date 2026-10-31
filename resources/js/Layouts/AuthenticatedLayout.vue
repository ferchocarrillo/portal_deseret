<script setup>
import Sidebar from '@/Components/Sidebar.vue';
// Importa el componente del widget de donaciones
import DonationWidget from '@/Components/DonationWidget.vue';
</script>

<template>
    <div class="relative flex min-h-screen bg-[#FFFDF9] text-[#172A3D]">

        <!-- ===================================================== -->
        <!-- FONDO ILUSTRADO GENERAL (RESPONSIVE + LUMINOSO)      -->
        <!-- ===================================================== -->
        <div class="fixed inset-0 z-0 pointer-events-none select-none overflow-hidden">
            <!-- 1. Imagen base responsive -->
            <picture class="block h-full w-full">
                <!-- Móviles (< 768px) -->
                <source media="(max-width: 767px)" srcset="/images/fondos/fondo-formularios-mobile.jpg" />
                <!-- Escritorio (>= 768px) -->
                <source media="(min-width: 768px)" srcset="/images/fondos/fondo-formularios-desktop.jpg" />
                <!-- Fallback -->
                <img src="/images/fondos/fondo-formularios-desktop.jpg" alt="Fondo Portal Deseret"
                    class="h-full w-full object-cover object-center filter brightness-[1.08] saturate-[0.9]" />
            </picture>

            <!-- 2. Velo blanco-marfil translúcido -->
            <div class="absolute inset-0 bg-white/80 backdrop-blur-[0.5px]"></div>
        </div>

        <!-- ===================================================== -->
        <!-- MENÚ LATERAL (SIDEBAR)                              -->
        <!-- ===================================================== -->
        <Sidebar />

        <!-- ===================================================== -->
        <!-- CONTENIDO PRINCIPAL                                   -->
        <!-- ===================================================== -->
        <main class="relative z-10 min-w-0 flex-1 overflow-y-auto bg-transparent">
            <slot />
        </main>

        <!-- ===================================================== -->
        <!-- WIDGET FLOTANTE DE DONACIONES (GLOBAL)                -->
        <!-- ===================================================== -->
        <DonationWidget />

    </div>
</template>

<style>
/* Permite que el Dashboard y las vistas no tapen el fondo con fondos sólidos propios */
main>.deseret-home,
main>.perfil-page,
main>[class*="create"],
main>[class*="form"] {
    background-color: transparent !important;
}

/* Oculta los degradados o velos antiguos del dashboard para que no ensucien la imagen */
main .pointer-events-none.fixed.-z-10 {
    display: none !important;
}
</style>