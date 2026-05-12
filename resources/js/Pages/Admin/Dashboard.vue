<script setup>
import navSito from '@/Components/Nav.vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <navSito />

    <div class="flex h-screen">
        <aside class="bg-black text-white w-64 p-4">
            <h2 class="text-lg font-bold mb-6">Menú</h2>

            <Link
                href="/dashboard/productos"
                class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700"
            >
                <i class="fa-solid fa-box"></i>
                Módulo de Inventario
            </Link>
        </aside>

        <main class="flex-1 p-6 bg-gray-100">
            <h2 class="text-xl font-semibold">Bienvenido Admin</h2>
        </main>
    </div>
</template>