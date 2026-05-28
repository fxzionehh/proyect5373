<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import { ref } from 'vue'

const page = usePage()

defineProps({
    pedidos: {
        type: Array,
        default: () => []
    }
})


const fecha = ref('')
const inventarioAbierto = ref(false)
const seccionActiva = ref('productos')
const roleAbierto = ref(false)
const openMenu = ref({
    inventario: false,
    pedidos: false,
    roles: false,
    usuarios: false,
})

const toggleMenu = (menu) => {
    openMenu.value[menu] = !openMenu.value[menu]
}



const buscar = () => {
    // Lógica para buscar pedidos por fecha
    console.log('Buscando pedidos para la fecha:', fecha.value)
    router.get('/dashboard/pedidos', { fecha: fecha.value }, {
        preserveState: true,
        preserveScroll: true,
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
                <!-- CONFIG -->

                <Link href="/dashboard/reportes"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg transition" :class="page.url.startsWith('/dashboard/reportes')
                        ? 'bg-zinc-900 text-white font-medium'
                        : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">

                    <i class="fa-solid fa-chart-bar text-sm"></i>

                    <span class="text-sm">
                        Reportes
                    </span>

                </Link>


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

    <!-- HEADER BONITO -->
    <div class="mb-6">
        <h1 class="text-3xl font-black text-zinc-900">
            Todos los pedidos
        </h1>
  
    </div>

    <!-- FILTRO CARD -->
    <div class="mb-6 bg-white p-4 rounded-xl shadow flex items-center justify-between">

        <div class="flex items-center gap-3">
            <input
                type="date"
                v-model="fecha"
                class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-zinc-400"
            />

            <button
                @click="buscar"
                class="bg-zinc-900 hover:bg-zinc-800 text-white px-5 py-2 rounded-lg transition"
            >
                Buscar
            </button>
        </div>


    </div>

    <!-- TABLA CARD -->
    <section class="bg-white rounded-xl shadow overflow-hidden">

        <table class="w-full">

            <!-- HEADER -->
            <thead class="bg-zinc-900 text-white text-sm uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-4 text-left">id</th>
                    <th class="px-6 py-4 text-left">Mesa</th>
                    <th class="px-6 py-4 text-left">Cliente</th>
                    <th class="px-6 py-4 text-left">Estado</th>
                    <th class="px-6 py-4 text-left">Origen</th>
                    <th class="px-6 py-4 text-right">Total</th>
                </tr>
            </thead>

            <tbody>

                <tr
                    v-for="pedido in pedidos"
                    :key="pedido.id"
                    class="border-b hover:bg-gray-50 transition"
                >

                    <td class="px-6 py-4 font-semibold text-zinc-800">
                        {{ pedido.id }}
                    </td>

                    <td class="px-6 py-4 text-gray-700">
                        Mesa {{ pedido.mesa_id }}
                    </td>

                      <td class="px-6 py-4 text-gray-700">
                        {{ pedido.nombre_cliente }}
                      </td>

                    <td class="px-6 py-4">

                        <span
                            class="px-3 py-1 rounded-full text-xs font-semibold"
                            :class="pedido.estado === 'pendiente'
                                ? 'bg-yellow-100 text-yellow-700'
                                : 'bg-green-100 text-green-700'"
                        >
                            {{ pedido.estado }}
                        </span>

                    </td>

                    <td class="px-6 py-4 text-gray-500">
                        {{ pedido.tipo_pedido }}
                    </td>

                    <td class="px-6 py-4 text-right font-bold text-zinc-900">
                        ${{ Number(pedido.total).toLocaleString('es-CL') }}
                    </td>

                </tr>

                <!-- VACÍO -->
                <tr v-if="pedidos.length === 0">
                    <td colspan="5" class="text-center py-12 text-gray-400">
                        No hay pedidos para esta fecha
                    </td>
                </tr>

            </tbody>

        </table>

    </section>

</main>

    </div>

</template>
