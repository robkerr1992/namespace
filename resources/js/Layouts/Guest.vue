<script setup>
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/inertia-vue3';
import FlashMessages from "@/Components/FlashMessages";
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <Link href="/">
                <h1 class="font-bold text-6xl"><span class="text-red-400">Name</span><span class="text-gray-400">space</span></h1>
            </Link>
        </div>

        <FlashMessages/>

        <div class="w-full sm:max-w-md mt-3 px-6 py-4 overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>
