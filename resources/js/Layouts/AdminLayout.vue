<script setup>

import Sidebar from '@/Components/Sidebar.vue';

</script>


<template>

    <div class="flex h-screen overflow-hidden bg-gray-50">


        <!-- ===================================================== -->
        <!-- SIDEBAR                                               -->
        <!-- ===================================================== -->

        <Sidebar />


        <!-- ===================================================== -->
        <!-- CONTENIDO PRINCIPAL                                   -->
        <!-- ===================================================== -->

        <main class="flex-1 overflow-y-auto">

            <slot />

        </main>


    </div>

</template>