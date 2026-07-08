

<script setup>
import { ref } from 'vue'
import { useForm, router, usePage } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()

const props = defineProps({
    productos: Array
})

const modalAbierto = ref(false)
const editando = ref(false)
const productoEditando = ref(null)

const form = useForm({
    id: null,
    nombre: '',
    precio_nano: '',
    precio_mini: '',
    precio_normal: '',
    precio_max: '',
    stock: ''
})

const can = (permiso) => {
    return page.props.auth?.permissions?.includes(permiso)
}

const abrirCrear = () => {
    limpiar()
    modalAbierto.value = true
}

const abrirEditar = (producto) => {
    editando.value = true
    productoEditando.value = producto

    form.id = producto.id
    form.nombre = producto.nombre
    form.precio_nano = producto.precio_nano
    form.precio_mini = producto.precio_mini
    form.precio_normal = producto.precio_normal
    form.precio_max = producto.precio_max
    form.stock = producto.stock

    modalAbierto.value = true
}

const cerrarModal = () => {
    modalAbierto.value = false
    limpiar()
}

const guardar = () => {
  form.post('/dashboard/productos', {
            onSuccess: cerrarModal
        })
}

const eliminar = (producto) => {
    if (confirm(`¿Eliminar el producto ${producto.nombre}?`)) {
        router.delete(`/dashboard/productos/${producto.id}`, {
            preserveScroll: true
        })
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

    <div class="min-h-screen bg-zinc-100/90 md:flex">
        <AppLayout />

        <main class="flex-1 p-8 w-full overflow-hidden">
     
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-black text-zinc-900">Productos</h1>

                <button
                    @click="abrirCrear"
                    class="flex items-center gap-2 bg-amber-500 text-white px-4 py-1.5 rounded-lg font-semibold hover:bg-amber-600 transition-all shadow-sm text-sm"
                >
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Crear producto</span>
                </button>
            </div>

        
            <section class="bg-white rounded-xl border border-gray-200 shadow-sm w-full overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[950px]">
                        <thead class="bg-zinc-900 text-white text-sm  ">
                            <tr>
                                <th class="px-6 py-4">Producto</th>
                                <th class="px-6 py-4 text-center">Nano</th>
                                <th class="px-6 py-4 text-center">Mini</th>
                                <th class="px-6 py-4 text-center">Normal</th>
                                <th class="px-6 py-4 text-center">Max</th>
                                <th class="px-6 py-4 text-center">Stock</th>
                                <th class="px-6 py-4 text-right">Acción</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="p in productos"
                                :key="p.id"
                                class="hover:bg-gray-50 transition"
                            >
                                <td class="px-6 py-4 font-medium text-zinc-800 whitespace-nowrap">
                                    {{ p.nombre }}
                                </td>

                                <td class="px-6 py-4 text-center text-gray-700 whitespace-nowrap">
                                    ${{ p.precio_nano }}
                                </td>

                                <td class="px-6 py-4 text-center text-gray-700 whitespace-nowrap">
                                    ${{ p.precio_mini }}
                                </td>

                                <td class="px-6 py-4 text-center text-gray-700 whitespace-nowrap">
                                    ${{ p.precio_normal }}
                                </td>

                                <td class="px-6 py-4 text-center text-gray-700 whitespace-nowrap">
                                    ${{ p.precio_max }}
                                </td>

                                <td class="px-6 py-4 text-center text-gray-700 whitespace-nowrap">
                                    {{ p.stock }}
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            v-if="can('editar producto')"
                                            @click="abrirEditar(p)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-500 text-white text-xs font-semibold hover:bg-sky-600 transition"
                                        >
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                            <span>Editar</span>
                                        </button>

                                        <button
                                            v-if="can('eliminar producto')"
                                            @click="eliminar(p)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-500 text-white text-xs font-semibold hover:bg-red-600 transition"
                                        >
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                            <span>Eliminar</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="!productos.length">
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    No hay productos registrados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

       
<div
    v-if="modalAbierto"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-3 sm:p-4"
>
    <div class="bg-white w-full max-w-md sm:max-w-lg md:max-w-xl mx-auto rounded-2xl shadow-2xl overflow-hidden">

      
        <div class="bg-zinc-900 text-white px-5 py-4 flex justify-between items-center">
            <h2 class="font-bold text-lg">
                {{ editando ? 'Editar producto' : 'Nuevo producto' }}
            </h2>

            <button @click="cerrarModal" class="text-white/70 hover:text-white">
                ✕
            </button>
        </div>

        
        <form @submit.prevent="guardar" class="p-5 sm:p-6 space-y-5">

           
            <div>
                <label class="text-xs font-bold text-zinc-500 uppercase">Nombre</label>
                <input
                    v-model="form.nombre"
                    type="text"
                    placeholder="Ej: Cappuccino"
                    class="w-full mt-1 border rounded-xl p-3 focus:ring-2 focus:ring-amber-500 outline-none"
                />
                <div v-if="form.errors.nombre" class="text-red-500 text-[10px] font-bold mt-1">
                    {{ form.errors.nombre }}
                </div>
            </div>

           
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="text-xs font-bold text-zinc-500 uppercase">Precio Nano</label>
                    <input
                        v-model="form.precio_nano"
                        type="number"
                        class="w-full mt-1 border rounded-xl p-3 focus:ring-2 focus:ring-amber-500 outline-none"
                    />
                    <div v-if="form.errors.precio_nano" class="text-red-500 text-[10px] font-bold mt-1">
                        {{ form.errors.precio_nano }}
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-zinc-500 uppercase">Precio Mini</label>
                    <input
                        v-model="form.precio_mini"
                        type="number"
                        class="w-full mt-1 border rounded-xl p-3 focus:ring-2 focus:ring-amber-500 outline-none"
                    />
                    <div v-if="form.errors.precio_mini" class="text-red-500 text-[10px] font-bold mt-1">
                        {{ form.errors.precio_mini }}
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-zinc-500 uppercase">Precio Normal</label>
                    <input
                        v-model="form.precio_normal"
                        type="number"
                        class="w-full mt-1 border rounded-xl p-3 focus:ring-2 focus:ring-amber-500 outline-none"
                    />
                    <div v-if="form.errors.precio_normal" class="text-red-500 text-[10px] font-bold mt-1">
                        {{ form.errors.precio_normal }}
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-zinc-500 uppercase">Precio Max</label>
                    <input
                        v-model="form.precio_max"
                        type="number"
                        class="w-full mt-1 border rounded-xl p-3 focus:ring-2 focus:ring-amber-500 outline-none"
                    />
                    <div v-if="form.errors.precio_max" class="text-red-500 text-[10px] font-bold mt-1">
                        {{ form.errors.precio_max }}
                    </div>
                </div>

            </div>

         
            <div>
                <label class="text-xs font-bold text-zinc-500 uppercase">Stock</label>
                <input
                    v-model="form.stock"
                    type="number"
                    class="w-full mt-1 border rounded-xl p-3 focus:ring-2 focus:ring-amber-500 outline-none"
                />
                <div v-if="form.errors.stock" class="text-red-500 text-[10px] font-bold mt-1">
                    {{ form.errors.stock }}
                </div>
            </div>

           
            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-2">

                <button
                    type="button"
                    @click="cerrarModal"
                    class="px-4 py-2 border rounded-xl text-sm hover:bg-gray-50"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-5 py-2 bg-amber-500 text-white rounded-xl text-sm font-semibold hover:bg-amber-600 disabled:opacity-50"
                >
                    {{ form.processing ? 'Guardando...' : 'Guardar' }}
                </button>

            </div>

        </form>
    </div>
</div>
        </main>
    </div>
</template>