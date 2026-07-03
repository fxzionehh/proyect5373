<script setup>
import navSito from '@/Components/Nav.vue'
</script>

<template>
<div class="min-h-screen bg-amber-50">

    <navSito />

  
    <section class="bg-[#2f2a25] py-16 px-6 shadow-lg">
        <div class="max-w-5xl mx-auto text-center">

            <p class="text-amber-300 tracking-widest uppercase text-sm mb-3">
                SteamCup Coffee
            </p>

            <h1
                class="text-5xl md:text-7xl text-white mb-4"
                style="font-family: 'Caveat', cursive;"
            >
                Bienvenido
            </h1>

            <p class="text-zinc-300 max-w-xl mx-auto">
                Café de especialidad preparado al momento.
            </p>

        </div>
    </section>

    <!-- Menú -->
    <section class="max-w-5xl mx-auto px-6 py-12">

        <div class="text-center mb-12">
            <h2
                class="text-5xl text-zinc-800"
                style="font-family: 'Caveat', cursive;"
            >
                Nuestro Menú
            </h2>

            <div class="w-24 h-1 bg-amber-500 mx-auto mt-3 rounded-full"></div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">

            <!-- Cafés -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-amber-100">

                <h3
                    class="text-4xl mb-6 text-zinc-800"
                    style="font-family: 'Caveat', cursive;"
                >
                    Cafés
                </h3>

                <div class="space-y-4">

                    <div class="flex justify-between border-b border-dashed pb-2">
                        <span>Espresso</span>
                        <span>$1.800</span>
                    </div>

                    <div class="flex justify-between border-b border-dashed pb-2">
                        <span>Americano</span>
                        <span>$2.000</span>
                    </div>

                    <div class="flex justify-between border-b border-dashed pb-2">
                        <span>Latte</span>
                        <span>$2.500</span>
                    </div>

                    <div class="flex justify-between border-b border-dashed pb-2">
                        <span>Cappuccino</span>
                        <span>$2.800</span>
                    </div>

                </div>

            </div>

            <!-- Fríos -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-amber-100">

                <h3
                    class="text-4xl mb-6 text-zinc-800"
                    style="font-family: 'Caveat', cursive;"
                >
                    Fríos
                </h3>

                <div class="space-y-4">

                    <div class="flex justify-between border-b border-dashed pb-2">
                        <span>Cold Brew</span>
                        <span>$3.200</span>
                    </div>

                    <div class="flex justify-between border-b border-dashed pb-2">
                        <span>Frappé</span>
                        <span>$3.500</span>
                    </div>

                    <div class="flex justify-between border-b border-dashed pb-2">
                        <span>Ice Latte</span>
                        <span>$3.000</span>
                    </div>

                </div>

            </div>

        </div>

        <!-- Destacado -->
        <div class="mt-12 bg-[#2f2a25] rounded-xl p-8 text-center">

            <p
                class="text-4xl text-white mb-3"
                style="font-family: 'Caveat', cursive;"
            >
                Café del Día
            </p>

            <h3 class="text-2xl font-semibold text-amber-300">
                Latte Vainilla
            </h3>

            <p class="text-zinc-300 mt-2">
                Preparado con granos seleccionados y leche espumada.
            </p>

        </div>

    </section>

</div>
</template>