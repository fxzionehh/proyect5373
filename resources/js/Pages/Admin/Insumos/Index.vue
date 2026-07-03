<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    insumos: Array
})

const modalAbierto = ref(false)
const editando = ref(false)


const form = useForm({
    id: null,
    nombre: '',
    stock: ''
})


const limpiar = () => {
    editando.value = false
    form.reset()
    form.clearErrors()
}

const abrirCrear = () => {
    limpiar()
    modalAbierto.value = true
}

const abrirEditar = (insumo) => {
    limpiar()
    editando.value = true
    

    form.id = insumo.id
    form.nombre = insumo.nombre
    form.stock = insumo.stock
    
    modalAbierto.value = true
}

const cerrarModal = () => {
    modalAbierto.value = false
    limpiar() 
}

const guardar = () => {
    form.post('/dashboard/insumos', {
            onSuccess: cerrarModal
        })
}

const eliminar = (insumo) => {
    if (confirm(`¿Seguro que deseas eliminar ${insumo.nombre}?`)) {
        router.delete(`/dashboard/insumos/${insumo.id}`, {
            preserveScroll: true
        })
    }
}
</script>

<template>
    <navSito />
    <div class="min-h-screen bg-zinc-100/90 md:flex">
        <AppLayout />

        <main class="flex-1 p-8 w-full overflow-hidden">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-black text-zinc-900">Insumos</h1>
                <button @click="abrirCrear" class="flex items-center gap-2 bg-amber-500 text-white px-4 py-1.5 rounded-lg font-semibold hover:bg-amber-600 transition-all shadow-sm text-sm">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Crear insumo</span>
                </button>
            </div>

            <section class="bg-white rounded-xl border border-gray-200 shadow-sm w-full overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-zinc-900 text-white text-sm uppercase tracking-wide">
                            <tr>
                                <th class="px-6 py-4">Insumo</th>
                                <th class="px-6 py-4">Stock</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="insumo in insumos" :key="insumo.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-zinc-800">{{ insumo.nombre }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ insumo.stock }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button @click="abrirEditar(insumo)" class="px-3 py-1.5 rounded-lg bg-sky-500 text-white text-xs font-semibold hover:bg-sky-600 transition">Editar</button>
                                        <button @click="eliminar(insumo)" class="px-3 py-1.5 rounded-lg bg-red-500 text-white text-xs font-semibold hover:bg-red-600 transition">Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <div v-if="modalAbierto" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-md rounded-xl shadow-2xl overflow-hidden">
            <div class="bg-zinc-900 text-white px-5 py-4 flex justify-between items-center">
                <h2 class="font-bold text-sm uppercase">{{ editando ? 'Editar insumo' : 'Nuevo insumo' }}</h2>
                <button @click="cerrarModal" class="text-white/70 hover:text-white">✕</button>
            </div>
            
            <form @submit.prevent="guardar" class="p-6 space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-zinc-500 uppercase">Nombre</label>
                    <input v-model="form.nombre" type="text" class="w-full mt-1 border rounded-lg p-2.5 text-sm outline-none focus:ring-1 focus:ring-amber-500" :class="{'border-red-500': form.errors.nombre}" />
                    <p v-if="form.errors.nombre" class="text-xs text-red-500 mt-1">{{ form.errors.nombre }}</p>
                </div>
                
                <div>
                    <label class="text-[10px] font-bold text-zinc-500 uppercase">Stock</label>
                    <input v-model="form.stock" type="number" class="w-full mt-1 border rounded-lg p-2.5 text-sm outline-none focus:ring-1 focus:ring-amber-500" :class="{'border-red-500': form.errors.stock}" />
                    <p v-if="form.errors.stock" class="text-xs text-red-500 mt-1">{{ form.errors.stock }}</p>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t mt-4">
                    <button type="button" @click="cerrarModal" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-100">Cancelar</button>
                    <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-semibold hover:bg-amber-600 disabled:opacity-50">
                        {{ form.processing ? 'Guardando...' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>