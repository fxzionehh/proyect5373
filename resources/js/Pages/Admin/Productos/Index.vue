<script setup>
import { ref } from 'vue'
import { Link, useForm, router, usePage } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
const page = usePage()

defineProps({
    productos: Array
})

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

        <AppLayout />

        <main class="flex-1 p-8">

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-black text-zinc-900">
                        Productos
                    </h1>
                </div>

                <button @click="abrirCrear"
                    class="flex items-center gap-2 bg-amber-500 text-white px-4 py-1.5 rounded-lg font-semibold hover:bg-amber-600 transition-all shadow-sm text-sm">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Crear producto</span>
                </button>
            </div>

            <section class="bg-white rounded-xl overflow-hidden border border-gray-200">
                <table class="w-full">
                    <thead class="bg-zinc-900 text-white text-sm uppercase tracking-wide">
                        <tr>
                            <th class="px-6 py-4 text-left">Producto</th>
                            <th class="px-6 py-4 text-left">Precio</th>
                            <th class="px-6 py-4 text-left">Stock</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
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
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <button @click="abrirEditar(producto)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-500 text-white text-xs font-semibold hover:bg-sky-600 transition">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                        <span>Editar</span>
                                    </button>
                                    <button @click="eliminar(producto)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-500 text-white text-xs font-semibold hover:bg-red-600 transition">
                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                        <span>Eliminar</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <div v-if="modalAbierto"
                class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white w-full max-w-sm rounded-2xl p-6 shadow-2xl border border-zinc-100">
                    <h2 class="text-xl font-black text-zinc-900 mb-6">
                        {{ editando ? 'Editar producto' : 'Crear nuevo producto' }}
                    </h2>

                    <form @submit.prevent="guardar" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1 ml-1">Nombre</label>
                            <input v-model="form.nombre" type="text" placeholder="Ej: Americano" required
                                class="w-full border-zinc-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-zinc-500 uppercase mb-1 ml-1">Precio</label>
                                <input v-model="form.precio" type="number" placeholder="0" required
                                    class="w-full border-zinc-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-zinc-500 uppercase mb-1 ml-1">Stock</label>
                                <input v-model="form.stock" type="number" placeholder="0" required
                                    class="w-full border-zinc-200 rounded-xl p-3 focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-4">
                            <button type="button" @click="cerrarModal"
                                class="px-5 py-2.5 text-sm font-semibold text-zinc-600 hover:bg-zinc-100 rounded-xl transition">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="form.processing"
                                class="px-5 py-2.5 text-sm font-semibold bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition disabled:opacity-50 shadow-md">
                                {{ form.processing ? 'Guardando...' : 'Guardar producto' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</template>