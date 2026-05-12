<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'

defineProps({
    productos: Array
})

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

    <div class="flex min-h-screen bg-gray-100">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-zinc-950 text-white p-5 shadow-lg">
            <h2 class="text-lg font-bold mb-8 tracking-wide">
                Panel Admin
            </h2>

            <nav class="space-y-2">
                <Link
                    href="/dashboard/productos"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600 shadow hover:bg-blue-700 transition"
                >
                    <i class="fa-solid fa-box"></i>
                    <span>Módulo de Inventario</span>
                </Link>
            </nav>
        </aside>

        <!-- CONTENIDO -->
        <main class="flex-1 p-8">

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Productos</h1>
            
                </div>

                <button
                    @click="abrirCrear"
                    class="flex items-center gap-2 bg-blue-600 text-white px-5 py-3 rounded-xl shadow hover:bg-blue-700 transition"
                >
                    <i class="fa-solid fa-plus"></i>
                    Crear producto
                </button>
            </div>

            <!-- CARD TABLA -->
            <section class="bg-white rounded-2xl shadow overflow-hidden border border-gray-200">
                <div class="px-6 py-4 border-b bg-gray-50">
                    <h2 class="font-semibold text-gray-700">
                        Listado de productos
                    </h2>
                </div>

                <table class="w-full">
                    <thead class="bg-gray-100 text-gray-600 text-sm uppercase">
                        <tr>
                            <th class="px-6 py-4 text-left">Producto</th>
                            <th class="px-6 py-4 text-left">Precio</th>
                            <th class="px-6 py-4 text-left">Stock</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="producto in productos"
                            :key="producto.id"
                            class="border-t hover:bg-gray-50 transition"
                        >
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ producto.nombre }}
                            </td>

                            <td class="px-6 py-4 text-gray-700">
                                ${{ Number(producto.precio).toLocaleString('es-CL') }}
                            </td>

                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 rounded-full text-sm font-semibold"
                                    :class="producto.stock > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                >
                                    {{ producto.stock }} unidades
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <button
                                        @click="abrirEditar(producto)"
                                        class="px-3 py-2 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <button
                                        @click="eliminar(producto)"
                                        class="px-3 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="productos.length === 0">
                            <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                No hay productos registrados.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <!-- MODAL -->
    <div
        v-if="modalAbierto"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4"
    >
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

            <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                <h2 class="text-xl font-bold text-gray-800">
                    {{ editando ? 'Editar producto' : 'Crear producto' }}
                </h2>

                <button
                    @click="cerrarModal"
                    class="text-gray-400 hover:text-gray-700 text-2xl"
                >
                    &times;
                </button>
            </div>

            <form @submit.prevent="guardar" class="p-6 space-y-5">
                <div>
                    <label class="block mb-1 font-semibold text-gray-700">Nombre</label>
                    <input
                        v-model="form.nombre"
                        type="text"
                        placeholder="Ej: Café Americano"
                        class="border border-gray-300 p-3 rounded-xl w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <p v-if="form.errors.nombre" class="text-red-500 text-sm mt-1">
                        {{ form.errors.nombre }}
                    </p>
                </div>

                <div>
                    <label class="block mb-1 font-semibold text-gray-700">Precio</label>
                    <input
                        v-model="form.precio"
                        type="number"
                        placeholder="Ej: 2500"
                        class="border border-gray-300 p-3 rounded-xl w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <p v-if="form.errors.precio" class="text-red-500 text-sm mt-1">
                        {{ form.errors.precio }}
                    </p>
                </div>

                <div>
                    <label class="block mb-1 font-semibold text-gray-700">Stock</label>
                    <input
                        v-model="form.stock"
                        type="number"
                        placeholder="Ej: 10"
                        class="border border-gray-300 p-3 rounded-xl w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <p v-if="form.errors.stock" class="text-red-500 text-sm mt-1">
                        {{ form.errors.stock }}
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-3">
                    <button
                        type="button"
                        @click="cerrarModal"
                        class="px-5 py-2 rounded-xl bg-gray-200 text-gray-700 hover:bg-gray-300 transition"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="px-5 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ editando ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>