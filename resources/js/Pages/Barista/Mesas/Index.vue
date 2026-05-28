<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'

const page = usePage()

defineProps({
    mesas: Array,
})

/*
|--------------------------------------------------------------------------
| LIBERAR MESA
|--------------------------------------------------------------------------
*/

const liberarMesa = (mesa) => {

    router.post(`/barista/mesas/${mesa.id}/liberar`, {}, {

        preserveScroll: true,

        onSuccess: () => {

            mesa.estado = 'libre'

        }

    })

}
</script>

<template>

    <navSito />

    <main class="flex min-h-screen bg-zinc-100/90">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-zinc-950 flex flex-col shadow-xl">

            <div class="px-6 py-7">

                <p class="text-lg font-bold text-white mt-0.5">
                    Panel Barista
                </p>

            </div>

            <!-- NAV -->
            <nav class="flex-1 px-4 py-6">

                <!-- PREPARACION -->
                <div class="space-y-1.5">

                    <Link
                        href="/barista/pedidos"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200"
                        :class="page.url.startsWith('/barista/pedidos')
                            ? 'bg-zinc-900 text-white font-medium shadow-sm'
                            : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900'"
                    >

                        <i class="fa-solid fa-clipboard-list text-sm"></i>

                        <span class="text-sm flex-1">
                            Preparación
                        </span>

                    </Link>

                </div>

                <!-- MESAS -->
                <div class="space-y-1.5 mt-2">

                    <Link
                        href="/barista/mesas"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200"
                        :class="page.url.startsWith('/barista/mesas')
                            ? 'bg-zinc-900 text-white font-medium shadow-sm'
                            : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900'"
                    >

                        <i class="fa-solid fa-table text-sm"></i>

                        <span class="text-sm">
                            Mesas
                        </span>

                    </Link>

                </div>

            </nav>

        </aside>

        <!-- CONTENIDO -->
        <section class="flex-1 py-12 px-10">

            <!-- HEADER -->
            <div class="mb-10">

                <h1 class="text-2xl font-bold tracking-tight text-zinc-900">
                    Mesas
                </h1>

                <p class="text-sm text-zinc-500 mt-1">
                    Listado de mesas activas.
                </p>

            </div>

            <!-- VACIO -->
            <div
                v-if="mesas.length === 0"
                class="bg-white border border-zinc-200/60 rounded-xl p-12 text-center text-sm text-zinc-400 max-w-md mx-auto mt-12 shadow-sm"
            >

                <i class="fa-solid fa-circle-info block text-zinc-300 text-xl mb-3"></i>

                No hay mesas registradas.

            </div>

            <!-- GRID -->
            <div
                v-else
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
            >

                <!-- CARD -->
                <div
                    v-for="mesa in mesas"
                    :key="mesa.id"
                    class="rounded-xl p-5 flex flex-col justify-between h-44 transition-all border shadow-sm hover:shadow-md"
                    :class="mesa.estado === 'ocupada'
                        ? 'bg-orange-200 border-orange-200/80'
                        : 'bg-white border-zinc-900/20 hover:border-zinc-300'"
                >

                    <!-- TOP -->
                    <div class="flex items-start justify-between">

                        <div>

                            <!-- NUMERO -->
                            <h2 class="text-lg font-bold text-zinc-900">
                                Mesa {{ mesa.numero }}
                            </h2>

                            <!-- INFO -->
                            <p class="text-xs text-black-400 mt-1.5 flex items-center gap-1.5">

                                <span
                                    class="w-2 h-2 rounded-full"
                                    :class="mesa.estado === 'ocupada'
                                        ? 'bg-orange-400'
                                        : 'bg-zinc-300'"
                                ></span>

                                Solicitado por QR

                            </p>

                        </div>

                        <!-- ESTADO -->
                        <div class="flex flex-col items-end gap-2">

                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold transition-colors"
                                :class="mesa.estado === 'ocupada'
                                    ? 'bg-orange-600 text-white'
                                    : 'bg-zinc-100 text-zinc-600'"
                            >

                                {{ mesa.estado === 'ocupada'
                                    ? 'Ocupada'
                                    : 'Libre'
                                }}

                            </span>

                            <!-- BOTON -->
                            <button
                                v-if="mesa.estado === 'ocupada'"
                                @click="liberarMesa(mesa)"
                                class="bg-zinc-950 hover:bg-zinc-800 text-white text-[11px] font-bold px-3 py-2 rounded-lg transition-all"
                            >
                                Liberar Mesa
                            </button>

                        </div>

                    </div>

                    <!-- FOOTER -->
                    <div class="text-right">

                        <span class="text-[11px] text-zinc-400 font-mono">
                            #{{ mesa.id }}
                        </span>

                    </div>

                </div>

            </div>

        </section>

    </main>

</template>