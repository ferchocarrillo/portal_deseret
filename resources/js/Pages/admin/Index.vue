<script setup>
import Sidebar from '@/Components/Sidebar.vue';
</script>

<template>

    <div class="flex h-screen overflow-hidden bg-gray-50">

        <!-- ================================================== -->
        <!-- PANEL LATERAL -->
        <!-- ================================================== -->

        <Sidebar />


        <!-- ================================================== -->
        <!-- CONTENIDO ADMINISTRATIVO -->
        <!-- ================================================== -->

        <main class="flex-1 overflow-y-auto">

            <div class="min-h-full">

                <slot />

            </div>

        </main>

    </div>

</template>