<script setup>
import { ref } from 'vue'
import { Link, useForm, router, usePage } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
const page = usePage()

defineProps({
    mesas: Array,
})

const inventarioAbierto = ref(false)
const seccionActiva = ref('productos')

const roleAbierto = ref(false)
const toggleEstado = (mesaId) => {

    if (confirm('¿Cambiar estado de esta mesa?')) {

        router.patch(`/dashboard/configuracion/mesas/${mesaId}/toggle`, {}, {
            preserveScroll: true
        })
    }
}


const generarQr = (mesaId) => {

    router.post(`/dashboard/configuracion/mesas/${mesaId}/generar-qr`, {}, {


        preserveScroll: true
    })
}


</script>

<template>
    <navSito />

    <div class="flex min-h-screen bg-zinc-100/90">

        <aside class="w-52 min-h-screen bg-zinc-950 flex flex-col shadow-xl">

            <div class="px-6 py-7 border-zinc-900">
                <p class="text-lg font-bold text-white">
                    Panel Admin
                </p>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-3">

                <!-- PEDIDOS -->
                <Link href="/dashboard/pedidos" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg transition"
                    :class="page.url.startsWith('/dashboard/pedidos')
                        ? 'bg-zinc-900 text-white font-medium'
                        : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">

                    <i class="fa-solid fa-receipt text-sm"></i>

                    <span class="text-sm">
                        Pedidos
                    </span>

                </Link>

                <!-- INVENTARIO -->
                <div>

                    <button @click="inventarioAbierto = !inventarioAbierto"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition" :class="page.url.startsWith('/dashboard/productos')
                            ? 'bg-zinc-900 text-white font-medium'
                            : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">

                        <div class="flex items-center gap-3">

                            <i class="fa-solid fa-boxes-stacked text-sm"></i>

                            <span class="text-sm">
                                Inventario
                            </span>

                        </div>

                        <i class="fa-solid text-xs transition-all duration-200" :class="inventarioAbierto
                            ? 'fa-chevron-down'
                            : 'fa-chevron-right'"></i>

                    </button>

                    <!-- SUBMENU -->
                    <div v-if="inventarioAbierto" class="ml-6 mt-2 flex flex-col gap-1">

                        <Link href="/dashboard/productos" class="text-left px-3 py-2 rounded-md text-sm transition"
                            :class="page.url.startsWith('/dashboard/productos')
                                ? 'bg-zinc-900/60 text-white'
                                : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                            Productos
                        </Link>

                        <Link href="/dashboard/productos"
                            class="text-left px-3 py-2 rounded-md text-sm transition text-zinc-400 hover:bg-zinc-900 hover:text-white">
                            Insumos
                        </Link>

                        <Link href="/dashboard/productos"
                            class="text-left px-3 py-2 rounded-md text-sm transition text-zinc-400 hover:bg-zinc-900 hover:text-white">
                            Control Stock
                        </Link>

                    </div>
                </div>

                <div>

                    <button @click="roleAbierto = !roleAbierto"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition" :class="page.url.startsWith('/dashboard/roles')
                            ? 'bg-zinc-900 text-white font-medium'
                            : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">

                        <div class="flex items-center gap-3">

                            <i class="fa-solid fa-user-shield text-sm"></i>

                            <span class="text-sm">
                                Roles
                            </span>

                        </div>

                        <i class="fa-solid text-xs transition-all duration-200" :class="roleAbierto
                            ? 'fa-chevron-down'
                            : 'fa-chevron-right'"></i>

                    </button>

                    <div v-if="roleAbierto" class="ml-6 mt-2 flex flex-col gap-1">

                        <Link href="/dashboard/roles" class="text-left px-3 py-2 rounded-md text-sm transition" :class="page.url.startsWith('/dashboard/roles')
                            ? 'bg-zinc-900/60 text-white'
                            : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                            Roles
                        </Link>

                        <Link href="/dashboard/roles"
                            class="text-left px-3 py-2 rounded-md text-sm transition text-zinc-400 hover:bg-zinc-900 hover:text-white">
                            Usuarios
                        </Link>



                    </div>
                </div>


                 <Link href="/dashboard/reportes"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg transition" :class="page.url.startsWith('/dashboard/reportes')
                        ? 'bg-zinc-900 text-white font-medium'
                        : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">

                    <i class="fa-solid fa-chart-bar text-sm"></i>

                    <span class="text-sm">
                        Reportes
                    </span>

                </Link>
                
                <!-- CONFIG -->
                <Link href="/dashboard/configuracion"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg transition" :class="page.url.startsWith('/dashboard/configuracion')
                        ? 'bg-zinc-900 text-white font-medium'
                        : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">

                    <i class="fa-solid fa-gear text-sm"></i>

                    <span class="text-sm">
                        Configuración
                    </span>

                </Link>

            </nav>

        </aside>

        <main class="flex-1 ">
            <div class="min-h-screen bg-zinc-100 p-8">
                <div class="mb-8">
                    <h1 class="text-3xl font-black text-zinc-900">Cartas QR</h1>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div v-for="mesa in mesas" :key="mesa.id" class="bg-white border border-zinc-200 rounded-2xl p-6">

                        <h2 class="text-2xl font-black text-zinc-900 mb-5">
                            Mesa {{ mesa.numero }}
                            <span class="text-sm font-normal text-gray-500">
                                ({{ mesa.activa ? 'Activa' : 'Inactiva' }})
                            </span>
                        </h2>

                        <div class="flex justify-center">
                            <img v-if="mesa.qr && mesa.activa" :src="`data:image/svg+xml;base64,${mesa.qr}`" alt="QR"
                                class="w-52 h-52">
                            <div v-else
                                class="w-52 h-52 flex items-center justify-center border bg-gray-50 text-gray-400 text-sm text-center p-4">
                                {{ !mesa.activa ? 'Mesa Desactivada' : 'Sin código QR' }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-4">
                            <button @click="generarQr(mesa.id)" :disabled="!mesa.activa"
                                class="bg-white border border-zinc-300 text-zinc-700 text-xs px-3 py-2 rounded-lg font-medium hover:bg-zinc-50 disabled:opacity-50">
                                Generar QR
                            </button>

                            <button @click="toggleEstado(mesa.id)"
                                class="text-white text-xs px-3 py-2 rounded-lg font-medium"
                                :class="mesa.activa ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'">
                                {{ mesa.activa ? 'Desactivar' : 'Activar' }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </main>

    </div>


</template>