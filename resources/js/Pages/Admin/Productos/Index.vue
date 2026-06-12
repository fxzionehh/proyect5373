<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    productos: Array
})

const modalAbierto = ref(false)
const editando = ref(false)
const productoEditando = ref(null)

const form = useForm({
    nombre: '',
    precio_nano: '',
    precio_mini: '',
    precio_normal: '',
    precio_max: '',
    stock: '',
})

const abrirCrear = () => {
    limpiar()
    modalAbierto.value = true
}

const abrirEditar = (p) => {
    editando.value = true
    productoEditando.value = p
    form.nombre = p.nombre
    form.precio_nano = p.precio_nano
    form.precio_mini = p.precio_mini
    form.precio_normal = p.precio_normal
    form.precio_max = p.precio_max
    form.stock = p.stock
    modalAbierto.value = true
}

const cerrarModal = () => { modalAbierto.value = false; limpiar(); }

const guardar = () => {
    if (editando.value) {
        form.put(`/dashboard/productos/${productoEditando.value.id}`, { onSuccess: cerrarModal })
    } else {
        form.post('/dashboard/productos', { onSuccess: cerrarModal })
    }
}

const eliminar = (p) => {
    if (confirm(`¿Eliminar ${p.nombre}?`)) router.delete(`/dashboard/productos/${p.id}`)
}

const limpiar = () => { editando.value = false; productoEditando.value = null; form.reset(); form.clearErrors(); }
</script>

<template>
    <navSito />
    <div class="flex min-h-screen bg-zinc-100/90">
        <AppLayout />
        <main class="flex-1 p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-black text-zinc-900">Productos</h1>
                <button @click="abrirCrear" class="flex items-center gap-2 bg-amber-500 text-white px-4 py-1.5 rounded-lg font-semibold hover:bg-amber-600 transition-all shadow-sm text-sm">
                    <i class="fa-solid fa-plus text-[10px]"></i> <span>Crear producto</span>
                </button>
            </div>

         <section class="bg-white rounded-xl overflow-hidden border border-gray-200">
    <table class="w-full">
        <thead class="bg-zinc-900 text-white text-sm uppercase tracking-wide">
            <tr>
                <th class="px-6 py-4 text-left">Producto</th>
                <th class="px-6 py-4 text-center">Nano</th>
                <th class="px-6 py-4 text-center">Mini</th>
                <th class="px-6 py-4 text-center">Normal</th>
                <th class="px-6 py-4 text-center">Max</th>
                <th class="px-6 py-4 text-center">Stock</th>
                <th class="px-6 py-4 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="p in productos" :key="p.id" class="border-t hover:bg-gray-50 transition">
                <td class="px-6 py-4 font-medium text-zinc-800">
                    {{ p.nombre }}
                </td>
                <td class="px-6 py-4 text-center text-gray-700">${{ p.precio_nano }}</td>
                <td class="px-6 py-4 text-center text-gray-700">${{ p.precio_mini }}</td>
                <td class="px-6 py-4 text-center text-gray-700">${{ p.precio_normal }}</td>
                <td class="px-6 py-4 text-center text-gray-700">${{ p.precio_max }}</td>
                <td class="px-6 py-4 text-center text-gray-700">
                    {{ p.stock }}
                </td>
                <td class="px-6 py-4">
                    <div class="flex justify-end gap-2">
                        <button @click="abrirEditar(p)"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-500 text-white text-xs font-semibold hover:bg-sky-600 transition">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                            <span>Editar</span>
                        </button>
                        <button @click="eliminar(p)"
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

            <div v-if="modalAbierto" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">
                    <div class="bg-zinc-900 text-white px-5 py-4 flex justify-between items-center">
                        <h2 class="font-bold text-lg">{{ editando ? 'Editar producto' : 'Nuevo producto' }}</h2>
                        <button @click="cerrarModal" class="text-white/70 hover:text-white">✕</button>
                    </div>
                    <form @submit.prevent="guardar" class="p-6 space-y-4">
                        <div>
                            <label class="text-[10px] font-bold text-zinc-500 uppercase">Nombre</label>
                            <input v-model="form.nombre" type="text" class="w-full mt-1 border rounded-xl p-3 focus:ring-2 focus:ring-amber-500 outline-none" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="text-[10px] font-bold text-zinc-500 uppercase">Precio Nano</label><input v-model="form.precio_nano" type="number" class="w-full mt-1 border rounded-xl p-3" /></div>
                            <div><label class="text-[10px] font-bold text-zinc-500 uppercase">Precio Mini</label><input v-model="form.precio_mini" type="number" class="w-full mt-1 border rounded-xl p-3" /></div>
                            <div><label class="text-[10px] font-bold text-zinc-500 uppercase">Precio Normal</label><input v-model="form.precio_normal" type="number" class="w-full mt-1 border rounded-xl p-3" /></div>
                            <div><label class="text-[10px] font-bold text-zinc-500 uppercase">Precio Max</label><input v-model="form.precio_max" type="number" class="w-full mt-1 border rounded-xl p-3" /></div>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-500 uppercase">Stock</label>
                            <input v-model="form.stock" type="number" class="w-full mt-1 border rounded-xl p-3" />
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="cerrarModal" class="px-4 py-2 border rounded-xl text-sm">Cancelar</button>
                            <button type="submit" class="px-5 py-2 bg-amber-500 text-white rounded-xl text-sm font-semibold hover:bg-amber-600">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</template>