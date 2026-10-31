<script setup>
import Sidebar from '@/Components/Sidebar.vue';
</script>

<template>
    <div class="flex min-h-screen bg-[#F3F0E8]">

        <!-- ===================================================== -->
        <!-- PANEL LATERAL -->
        <!-- ===================================================== -->

        <Sidebar />


        <!-- ===================================================== -->
        <!-- CONTENIDO PRINCIPAL -->
        <!-- ===================================================== -->

        <main class="min-w-0 flex-1 overflow-y-auto">

            <slot />

        </main>

    </div>
</template>