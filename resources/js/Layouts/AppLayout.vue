<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()

const inventarioAbierto = ref(false)
const roleAbierto = ref(false)

// 👇 NUEVO

</script>
<template>
    <div class="flex min-h-screen bg-zinc-100/90">
        <aside class="w-64 min-h-screen bg-zinc-950 flex flex-col shadow-xl">

            <div class="px-6 py-7 border-zinc-900">
               <p class="text-lg font-bold text-white">
    Panel de Admin
</p>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-3">

                <Link href="/dashboard/pedidos" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg transition"
                    :class="page.url.startsWith('/dashboard/pedidos')
                        ? 'bg-zinc-900 text-white font-medium'
                        : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                    <i class="fa-solid fa-receipt text-sm"></i>
                    <span class="text-sm font-semibold">Pedidos</span>
                </Link>

                <Link href="/dashboard/preparacion" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg transition"
                    :class="page.url.startsWith('/dashboard/preparacion')
                        ? 'bg-zinc-900 text-white font-medium'
                        : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                    <i class="fa-solid fa-utensils text-sm"></i>
                    <span class="text-sm font-semibold">Preparación</span>
                </Link>

                <div>
                    <button @click="inventarioAbierto = !inventarioAbierto"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition" :class="page.url.startsWith('/dashboard/productos')
                            ? 'bg-zinc-900 text-white font-medium'
                            : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">

                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-boxes-stacked text-sm"></i>
                            <span class="text-sm font-semibold">Inventario</span>
                        </div>

                        <i class="fa-solid text-xs transition-all duration-200"
                            :class="inventarioAbierto ? 'fa-chevron-down' : 'fa-chevron-right'">
                        </i>
                    </button>

                    <div v-if="inventarioAbierto" class="ml-6 mt-2 flex flex-col gap-1">
                        <Link href="/dashboard/productos" class="text-left px-3 py-2 rounded-md text-sm transition"
                            :class="page.url === '/dashboard/productos'
                                ? 'bg-zinc-900/60 text-white font-medium'
                                : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                            Productos
                        </Link>

                        <Link href="/dashboard/insumos" class="text-left px-3 py-2 rounded-md text-sm transition"
                            :class="page.url === '/dashboard/insumos'
                                ? 'bg-zinc-900/60 text-white font-medium'
                                : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                            Insumos
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
                            <span class="text-sm font-semibold">Roles</span>
                        </div>

                        <i class="fa-solid text-xs transition-all duration-200"
                            :class="roleAbierto ? 'fa-chevron-down' : 'fa-chevron-right'">
                        </i>
                    </button>

                    <div v-if="roleAbierto" class="ml-6 mt-2 flex flex-col gap-1">
                        <Link href="/dashboard/roles"
                            class="text-left px-3 py-2 rounded-md text-sm transition text-zinc-400 hover:bg-zinc-900 hover:text-white">
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
                    <span class="text-sm font-semibold">Reportes</span>
                </Link>

                <Link href="/dashboard/mesas" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg transition"
                    :class="page.url.startsWith('/dashboard/mesas')
                        ? 'bg-zinc-900 text-white font-medium'
                        : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                    <i class="fa-solid fa-table text-sm"></i>
                    <span class="text-sm font-semibold">Mesas</span>
                </Link>



            </nav>
        </aside>

        <main class="flex-1 ">
            <slot />
        </main>
    </div>
</template>