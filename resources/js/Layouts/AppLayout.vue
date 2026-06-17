<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()

const inventarioAbierto = ref(false)
const roleAbierto = ref(false)

const permissions = computed(() => page.props.auth.permissions || [])
const user = computed(() => page.props.auth.user || {})

const isAdmin = computed(() => {
    return user.value.role?.nombre === 'administrador'
})

const can = (perm) => {
    return permissions.value.includes(perm)
}
</script>

<template>
<aside class="w-full md:w-64 md:min-h-screen bg-zinc-950 flex flex-row md:flex-col shadow-xl fixed bottom-0 md:static z-50 overflow-x-auto">

    <div class="px-6 py-7 border-zinc-900 hidden md:block">
        <p class="text-lg font-bold text-white">Panel de Admin</p>
    </div>

    <nav class="flex flex-row md:flex-col flex-1 w-full p-2 md:p-4 gap-1 md:gap-3 justify-around md:justify-start">

        <Link v-if="can('ver pedido')" href="/dashboard/pedidos"
            class="flex flex-col md:flex-row items-center gap-1 md:gap-3 px-2 md:px-4 py-2 rounded-lg transition"
            :class="page.url.startsWith('/dashboard/pedidos') ? 'bg-zinc-900 text-white' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
            <i class="fa-solid fa-receipt text-lg md:text-sm"></i>
            <span class="text-[10px] md:text-sm font-semibold hidden md:block">Pedidos</span>
        </Link>

        <Link v-if="can('ver preparacion')" href="/dashboard/preparacion"
            class="flex flex-col md:flex-row items-center gap-1 md:gap-3 px-2 md:px-4 py-2 rounded-lg transition"
            :class="page.url.startsWith('/dashboard/preparacion') ? 'bg-zinc-900 text-white' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
            <i class="fa-solid fa-utensils text-lg md:text-sm"></i>
            <span class="text-[10px] md:text-sm font-semibold hidden md:block">Preparación</span>
        </Link>

        <div v-if="can('ver producto')" class="hidden md:block">
            <button @click="inventarioAbierto = !inventarioAbierto"
                class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition text-zinc-400 hover:bg-zinc-900 hover:text-white">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-boxes-stacked text-sm"></i>
                    <span class="text-sm font-semibold">Inventario</span>
                </div>
                <i :class="inventarioAbierto ? 'fa-chevron-down' : 'fa-chevron-right'" class="fa-solid text-xs"></i>
            </button>
            <div v-if="inventarioAbierto" class="ml-6 mt-2 flex flex-col gap-1">
                <Link href="/dashboard/productos" class="px-3 py-2 rounded-md text-sm text-zinc-400 hover:bg-zinc-900 hover:text-white">Productos</Link>
                <Link href="/dashboard/insumos" class="px-3 py-2 rounded-md text-sm text-zinc-400 hover:bg-zinc-900 hover:text-white">Insumos</Link>
            </div>
        </div>

        <div v-if="can('ver rol')" class="hidden md:block">
            <button @click="roleAbierto = !roleAbierto"
                class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition text-zinc-400 hover:bg-zinc-900 hover:text-white">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-user-shield text-sm"></i>
                    <span class="text-sm font-semibold">Roles</span>
                </div>
                <i :class="roleAbierto ? 'fa-chevron-down' : 'fa-chevron-right'" class="fa-solid text-xs"></i>
            </button>
            <div v-if="roleAbierto" class="ml-6 mt-2 flex flex-col gap-1">
                <Link href="/dashboard/roles" class="px-3 py-2 rounded-md text-sm text-zinc-400 hover:bg-zinc-900 hover:text-white">Roles</Link>
                <Link href="/dashboard/roles" class="px-3 py-2 rounded-md text-sm text-zinc-400 hover:bg-zinc-900 hover:text-white">Usuarios</Link>
            </div>
        </div>

        <Link v-if="can('ver reporte')" href="/dashboard/reportes"
            class="flex flex-col md:flex-row items-center gap-1 md:gap-3 px-2 md:px-4 py-2 rounded-lg transition"
            :class="page.url.startsWith('/dashboard/reportes') ? 'bg-zinc-900 text-white' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
            <i class="fa-solid fa-chart-bar text-lg md:text-sm"></i>
            <span class="text-[10px] md:text-sm font-semibold hidden md:block">Reportes</span>
        </Link>

        <Link v-if="can('ver mesa')" href="/dashboard/mesas"
            class="flex flex-col md:flex-row items-center gap-1 md:gap-3 px-2 md:px-4 py-2 rounded-lg transition"
            :class="page.url.startsWith('/dashboard/mesas') ? 'bg-zinc-900 text-white' : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
            <i class="fa-solid fa-table text-lg md:text-sm"></i>
            <span class="text-[10px] md:text-sm font-semibold hidden md:block">Mesas</span>
        </Link>
    </nav>
</aside>
</template>