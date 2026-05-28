<script setup>
import navSito from '@/Components/Nav.vue'


import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
</script>

<template>
    <navSito />

    <div class="flex h-screen">
        
       <aside class="w-64 bg-zinc-950 text-zinc-300 border-r border-zinc-800 flex flex-col">

   
    <div class="px-6 py-6 border-b border-zinc-800">
        <h2 class="text-xl font-semibold tracking-wide text-white">
            Panel Admin
        </h2>

        <p class="text-sm text-zinc-500 mt-1">
            SteamCup
        </p>
    </div>

    
    <nav class="flex-1 px-3 py-5 space-y-1">

      
        <Link
            href="/dashboard/productos"
            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 relative"
            :class="page.url.startsWith('/dashboard/productos')
                ? 'bg-zinc-900 text-yellow-400'
                : 'hover:bg-zinc-900/60 hover:text-white'"
        >

            <!-- INDICADOR -->
            <div
                v-if="page.url.startsWith('/dashboard/productos')"
                class="absolute left-0 top-2 bottom-2 w-1 bg-yellow-400 rounded-r-full"
            ></div>

            <i class="fa-solid fa-box text-sm"></i>

            <span class="text-sm font-medium">
                Inventario
            </span>
        </Link>

        <!-- PEDIDOS -->
        <Link
            href="/dashboard/pedidos"
            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 relative"
            :class="page.url.startsWith('/dashboard/pedidos')
                ? 'bg-zinc-900 text-yellow-400'
                : 'hover:bg-zinc-900/60 hover:text-white'"
        >

            <div
                v-if="page.url.startsWith('/dashboard/pedidos')"
                class="absolute left-0 top-2 bottom-2 w-1 bg-yellow-400 rounded-r-full"
            ></div>

            <i class="fa-solid fa-shopping-cart text-sm"></i>

            <span class="text-sm font-medium">
                Pedidos
            </span>
        </Link>


          <Link
            href="/dashboard/pedidos"
            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 relative"
          
        >

            <i class="fa-solid fa-user-tag text-sm"></i>

            <span class="text-sm font-medium">
                Roles de usuario
            </span>
        </Link>

        <Link
            href="/dashboard/pedidos"
            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 relative"
          
        >

            <i class="fa-solid fa-users text-sm"></i>

            <span class="text-sm font-medium">
                Usuarios
            </span>
        </Link>
    </nav>

        </aside>

        <main class="flex px-6  py-6 ">
            <h2 class="text-xl font-semibold">Bienvenido Admin</h2>
        </main>
    </div>
</template>