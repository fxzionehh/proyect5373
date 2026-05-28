<script setup>
import { ref } from 'vue'
import { Link, useForm, router, usePage } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'

const page = usePage()

defineProps({
    productos: Array,
    insumos: Array
})

const seccionActiva = ref('productos')

const inventarioAbierto = ref(true)
const roleAbierto = ref(false)
const modalAbierto = ref(false)
const editando = ref(false)
const productoEditando = ref(null)

const form = useForm({
    nombre: '',
    precio: '',
    stock: ''
})

const abrirCrear = () => {
    limpiar()
    modalAbierto.value = true
}

const abrirEditar = (producto) => {
    editando.value = true
    productoEditando.value = producto

    form.nombre = producto.nombre
    form.precio = producto.precio
    form.stock = producto.stock

    modalAbierto.value = true
}

const cerrarModal = () => {
    modalAbierto.value = false
    limpiar()
}

const guardar = () => {
    if (editando.value) {
        form.put(`/dashboard/productos/${productoEditando.value.id}`, {
            onSuccess: cerrarModal
        })
    } else {
        form.post('/dashboard/productos', {
            onSuccess: cerrarModal
        })
    }
}

const eliminar = (producto) => {
    if (confirm(`¿Eliminar ${producto.nombre}?`)) {
        router.delete(`/dashboard/productos/${producto.id}`)
    }
}

const limpiar = () => {
    editando.value = false
    productoEditando.value = null
    form.reset()
    form.clearErrors()
}
</script>

<template>

    <navSito />

    <div class="flex min-h-screen bg-zinc-100/90">

        <!-- SIDEBAR -->
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

                        <button @click="seccionActiva = 'productos'"
                            class="text-left px-3 py-2 rounded-md text-sm transition" :class="seccionActiva === 'productos'
                                ? 'bg-zinc-900/60 text-white'
                                : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                            Productos
                        </button>

                        <button @click="seccionActiva = 'insumos'"
                            class="text-left px-3 py-2 rounded-md text-sm transition" :class="seccionActiva === 'insumos'
                                ? 'bg-zinc-900/60 text-white'
                                : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                            Insumos
                        </button>

                        <button @click="seccionActiva = 'controlstock'"
                            class="text-left px-3 py-2 rounded-md text-sm transition" :class="seccionActiva === 'controlstock'
                                ? 'bg-zinc-900/60 text-white'
                                : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                            Control Stock
                        </button>

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

        <!-- CONTENIDO -->
        <main class="flex-1 p-8">

            <div class="flex justify-between items-center mb-6">

                <div>

                    <h1 class="text-3xl font-black text-zinc-900">
                        Productos
                    </h1>


                </div>

                <button v-if="seccionActiva === 'productos'" @click="abrirCrear"
                    class="flex items-center gap-2 bg-zinc-950 text-white px-5 py-2 rounded-md font-semibold hover:bg-zinc-800 transition-all duration-200 shadow-sm">

                    <i class="fa-solid fa-plus text-sm"></i>

                    <span class="text-sm">
                        Crear producto
                    </span>

                </button>

            </div>

            <!-- PRODUCTOS -->
            <section v-if="seccionActiva === 'productos'"
                class="bg-white rounded-xl overflow-hidden border border-gray-200">

                <table class="w-full">

                   <thead class="bg-zinc-900 text-white text-sm uppercase tracking-wide">

                        <tr>

                            <th class="px-6 py-4 text-left">
                                Producto
                            </th>

                            <th class="px-6 py-4 text-left">
                                Precio
                            </th>

                            <th class="px-6 py-4 text-left">
                                Stock
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr v-for="producto in productos" :key="producto.id"
                            class="border-t hover:bg-gray-50 transition">

                            <td class="px-6 py-4">

                                <p class="font-medium text-zinc-800">
                                    {{ producto.nombre }}
                                </p>

                            </td>

                            <td class="px-6 py-4 font-semibold text-gray-700">
                                ${{ Number(producto.precio).toLocaleString('es-CL') }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ producto.stock }}
                            </td>

                        </tr>

                    </tbody>

                </table>

            </section>

            <!-- INSUMOS -->
            <section v-if="seccionActiva === 'insumos'"
                class="bg-white rounded-xl overflow-hidden border border-gray-200">

                <table class="w-full">

                <thead class="bg-zinc-900 text-white text-sm uppercase tracking-wide">

                        <tr>

                            <th class="px-6 py-4 text-left">
                                Insumo
                            </th>

                            <th class="px-6 py-4 text-left">
                                Stock
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr v-for="insumo in insumos" :key="insumo.id" class="border-t hover:bg-gray-50 transition">

                            <td class="px-6 py-4 font-medium text-zinc-800">
                                {{ insumo.nombre }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ insumo.stock }}
                            </td>

                        </tr>

                    </tbody>

                </table>

            </section>

            <!-- CONTROL STOCK -->
            <section v-if="seccionActiva === 'controlstock'"
                class="bg-white rounded-xl overflow-hidden border border-gray-200">

                <table class="w-full">

                  <thead class="bg-zinc-900 text-white text-sm uppercase tracking-wide">

                        <tr>

                            <th class="px-6 py-4 text-left">
                                Producto / Insumo
                            </th>

                            <th class="px-6 py-4 text-left">
                                Stock
                            </th>

                            <th class="px-6 py-4 text-right">
                                Acción
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr v-for="insumo in insumos" :key="insumo.id" class="border-t hover:bg-gray-50 transition">

                            <td class="px-6 py-4 font-medium text-zinc-800">
                                {{ insumo.nombre }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                {{ insumo.stock }}
                            </td>

                            <td class="px-6 py-4">

                                <div class="flex justify-end gap-2">

                                    <button @click="abrirEditar(insumo)"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-zinc-900 text-white text-sm font-semibold hover:bg-zinc-800 transition">

                                        <i class="fa-solid fa-pen text-xs"></i>

                                    </button>

                                    <button @click="eliminar(insumo)"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-700 text-sm font-semibold hover:bg-red-200 transition">

                                        <i class="fa-solid fa-trash text-xs"></i>

                                    </button>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </section>

            <!-- MODAL -->
            <div v-if="modalAbierto" class="fixed inset-0 bg-black/40 flex items-center justify-center">

                <div class="bg-white w-96 rounded-xl p-6">

                    <h2 class="text-xl font-bold mb-4">
                        {{ editando ? 'Editar producto' : 'Crear producto' }}
                    </h2>

                    <form @submit.prevent="guardar" class="space-y-4">

                        <input v-model="form.nombre" type="text" placeholder="Nombre"
                            class="w-full border rounded-lg p-3">

                        <input v-model="form.precio" type="number" placeholder="Precio"
                            class="w-full border rounded-lg p-3">

                        <input v-model="form.stock" type="number" placeholder="Stock"
                            class="w-full border rounded-lg p-3">

                        <div class="flex justify-end gap-2">

                            <button type="button" @click="cerrarModal" class="px-4 py-2 bg-gray-200 rounded-lg">
                                Cancelar
                            </button>

                            <button type="submit" class="px-4 py-2 bg-zinc-900 text-white rounded-lg">
                                Guardar
                            </button>

                        </div>

                    </form>

                </div>

            </div>
        </main>

    </div>

</template>