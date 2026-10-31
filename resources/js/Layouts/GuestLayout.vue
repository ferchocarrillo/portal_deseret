<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    minWidth: {
        type: String,
        default: 'sm:min-w-0',
    },

    maxWidth: {
        type: String,
        default: 'sm:max-w-md',
    },

    backgroundImage: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <div class="relative flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:pt-0"
        :class="backgroundImage ? 'bg-cover bg-center bg-fixed' : ''" :style="backgroundImage
                ? { backgroundImage: `url('${backgroundImage}')` }
                : {}
            ">
        <div v-if="backgroundImage" class="pointer-events-none absolute inset-0 bg-white/20"></div>

        <!-- LOGO PORTAL DESERET -->
        <div class="relative z-10">
            <Link href="/">
                <img src="/images/landing/logo.png" alt="Portal Deseret" class="h-auto w-[90px]" />
            </Link>
        </div>

        <!-- CONTENIDO -->
        <div class="relative z-10 mt-6 w-full px-6 py-4" :class="[minWidth, maxWidth]">
            <slot />
        </div>
    </div>
</template>