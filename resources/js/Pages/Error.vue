<script setup>
import { Link } from '@inertiajs/vue3';
import Nav from '@/Components/Nav.vue';

defineProps({
    status: {
        type: Number,
        required: true
    },
    message: {
        type: String,
        required: true
    }
});
</script>

<template>
    <div class="min-h-screen bg-zinc-50 flex flex-col">
        <Nav />

        <main class="flex-1 flex items-center justify-center p-6">
            <div class="text-center max-w-lg w-full">
                <div class="text-zinc-300 mb-6">
                    <i class="fa-solid fa-triangle-exclamation text-8xl"></i>
                </div>

                <h1 class="text-9xl font-black text-zinc-950 tracking-tighter">
                    {{ status }}
                </h1>
                <p class="text-2xl font-semibold text-zinc-800 mt-4">
                    Algo salio mal
                </p>
                <p class="text-zinc-500 mt-2 text-lg">
                    {{ message }}
                </p>

          
            </div>
        </main>
    </div>
</template>