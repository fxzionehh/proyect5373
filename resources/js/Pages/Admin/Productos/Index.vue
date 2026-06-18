<script setup>
import { ref, computed } from 'vue'
import { useForm, router, usePage } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    productos: Array
})


const page = usePage()
const permissions = computed(() => page.props.auth.permissions || [])
const can = (perm) => {
    //if (isAdmin.value) return true
    return permissions.value.includes(perm)
}

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
        form.id = productoEditando.value.id
    }

    form.post('/dashboard/productos', {
        onSuccess: cerrarModal
    })
}

const eliminar = (p) => {
    if (confirm(`¿Eliminar ${p.nombre}?`)) router.delete(`/dashboard/productos/${p.id}`)
}

const limpiar = () => { editando.value = false; productoEditando.value = null; form.reset(); form.clearErrors(); }
</script>

<template>
    <navSito />
    <div class="min-h-screen bg-zinc-100/90 md:flex">
        <AppLayout />
        <main class="flex-1 p-4 md:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
                <h1 class="text-3xl font-black text-zinc-900">Productos</h1>
                <button @click="abrirCrear"
                    class="w-full sm:w-auto bg-amber-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-amber-600 transition">
                    <i class="fa-solid fa-plus text-[10px]"></i> <span>Crear producto</span>
                </button>
            </div>

            <section class="bg-white rounded-xl overflow-hidden border shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[850px] text-sm">
                        <thead class="bg-zinc-900 text-white">
                            <tr>
                                <th class="px-4 py-4 text-left">Producto</th>
                                <th class="px-4 py-4 text-center">Nano</th>
                                <th class="px-4 py-4 text-center">Mini</th>
                                <th class="px-4 py-4 text-center">Normal</th>
                                <th class="px-4 py-4 text-center">Max</th>
                                <th class="px-4 py-4 text-center">Stock</th>
                                <th class="px-4 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-100">
                            <tr v-for="p in productos" :key="p.id" class="hover:bg-zinc-50 transition">
                                <td class="px-4 py-4 font-medium whitespace-nowrap">
                                    {{ p.nombre }}
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    ${{ p.precio_nano }}
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    ${{ p.precio_mini }}
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    ${{ p.precio_normal }}
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    ${{ p.precio_max }}
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    {{ p.stock }}
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex justify-end gap-2">
                                        <button v-if="can('editar producto')" @click="abrirEditar(p)"
                                            class="px-3 py-2 rounded-lg bg-sky-500 text-white hover:bg-sky-600">
                                            Editar
                                        </button>

                                        <button v-if="can('eliminar producto')" @click="eliminar(p)"
                                            class="px-3 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
       
        </main>
    </div>


         <div v-if="modalAbierto"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">

                <div class="bg-white w-full max-w-md rounded-xl shadow-2xl overflow-hidden">

                    <!-- HEADER -->
                    <div class="bg-zinc-900 text-white px-4 py-3 flex justify-between items-center">
                        <h2 class="font-bold text-sm uppercase tracking-wide">
                            {{ editando ? 'Editar producto' : 'Nuevo producto' }}
                        </h2>

                        <button @click="cerrarModal" class="text-white/70 hover:text-white text-sm">
                            ✕
                        </button>
                    </div>

                    <!-- FORM -->
                    <form @submit.prevent="guardar" class="p-4 space-y-3">

                        <!-- Nombre -->
                        <div>
                            <label class="text-[10px] font-bold text-zinc-500 uppercase">
                                Nombre
                            </label>

                            <input v-model="form.nombre" type="text"
                                class="w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-amber-500 outline-none" />
                        </div>

                        <!-- Precios -->
                        <div class="grid grid-cols-2 gap-3">

                            <div>
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">
                                    Nano
                                </label>
                                <input v-model="form.precio_nano" type="number"
                                    class="w-full mt-1 px-3 py-2 text-sm border rounded-lg" />
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">
                                    Mini
                                </label>
                                <input v-model="form.precio_mini" type="number"
                                    class="w-full mt-1 px-3 py-2 text-sm border rounded-lg" />
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">
                                    Normal
                                </label>
                                <input v-model="form.precio_normal" type="number"
                                    class="w-full mt-1 px-3 py-2 text-sm border rounded-lg" />
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-zinc-500 uppercase">
                                    Max
                                </label>
                                <input v-model="form.precio_max" type="number"
                                    class="w-full mt-1 px-3 py-2 text-sm border rounded-lg" />
                            </div>

                        </div>

                        <!-- Stock -->
                        <div>
                            <label class="text-[10px] font-bold text-zinc-500 uppercase">
                                Stock
                            </label>

                            <input v-model="form.stock" type="number"
                                class="w-full mt-1 px-3 py-2 text-sm border rounded-lg" />
                        </div>

                        <!-- FOOTER -->
                        <div class="flex justify-end gap-2 pt-2 border-t mt-3">

                            <button type="button" @click="cerrarModal"
                                class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-100">
                                Cancelar
                            </button>

                            <button type="submit"
                                class="px-4 py-1.5 text-sm bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-semibold">
                                Guardar
                            </button>

                        </div>

                    </form>

                </div>
            </div>
</template>