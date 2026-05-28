<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const props = defineProps({
    roles: Array
})

console.log(props.roles)

const form = useForm({

})
const inventarioAbierto = ref(false)
const seccionActiva = ref('productos')


const roleAbierto = ref(true)
const openMenu = ref({
    inventario: false,
    pedidos: false,
    roles: false,
    usuarios: false,
})

const toggleMenu = (menu) => {
    openMenu.value[menu] = !openMenu.value[menu]
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

                        <Link href="/dashboard/roles" class="text-left px-3 py-2 rounded-md text-sm transition"
                            :class="page.url.startsWith('/dashboard/roles')
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


        <main class="flex-1 p-8">

            <div class="flex justify-between items-center mb-4">

                <div>
                    <h1 class="text-3xl font-black text-zinc-900">
                        Roles
                    </h1>

                    <p class="text-gray-500 mt-1">
                        Listados de roles.
                    </p>
                </div>

            </div>


            <section class="bg-white rounded-xl overflow-hidden border border-gray-200">

                <table class="w-full">

                    <thead class="bg-gray-100 text-gray-500 text-sm uppercase">
                        <tr>
                            <th class="px-6 py-4 text-left">Nombre Rol</th>
                            <th class="px-6 py-4 text-left">Fecha creación</th>
                            <th class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr v-for="rol in roles" :key="rol.id" class="border-t hover:bg-gray-50 transition">

                            <td class="px-6 py-4 font-medium text-zinc-800">
                                {{ rol.nombre }}
                            </td>

                            <td class="px-6 py-4 font-semibold text-gray-700">
                                {{ rol.created_at }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex justify-end">

                                    <Link :href="`/dashboard/roles/${rol.id}/edit`"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-zinc-900 text-white text-sm font-semibold hover:bg-zinc-800 transition">

                                        <i class="fa-solid fa-pen text-xs"></i>

                                    </Link>

                                </div>
                            </td>

                        </tr>

                        <tr v-if="roles.length === 0">
                            <td colspan="3" class="px-6 py-10 text-center text-gray-500">
                                No hay roles registrados.
                            </td>
                        </tr>

                    </tbody>

                </table>

            </section>

        </main>

    </div>
</template>