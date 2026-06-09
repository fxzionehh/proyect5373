<script setup>
import { ref } from 'vue'
import { Link, useForm, router, usePage } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
const page = usePage()

// Props recibidas del controlador
defineProps({
    pedidos: Array,
    productos: Array // Para el select del modal
})

// Estados
const inventarioAbierto = ref(false)
const roleAbierto = ref(false)
const modalAbierto = ref(false)

// Formulario (Ajustado para productos)
const form = useForm({
    mesa_id: '',
    nombre_cliente: '',
    estado: 'pendiente',
    tipo_pedido: 'presencial',
    productos: [] 
})

// Lógica de gestión de items en el pedido
const prodSeleccionado = ref(null)

const agregarProducto = () => {
    if (!prodSeleccionado.value) return
    form.productos.push({
        id: prodSeleccionado.value.id,
        nombre: prodSeleccionado.value.nombre,
        precio: prodSeleccionado.value.precio,
        cantidad: 1
    })
    prodSeleccionado.value = null
}

const abrirCrear = () => {
    form.reset()
    modalAbierto.value = true
}

const guardar = () => {
    form.post('/dashboard/pedidos', {
        onSuccess: () => {
            modalAbierto.value = false
            form.reset()
        }
    })
}
</script>

<template>
    <navSito />

    <div class="flex min-h-screen bg-zinc-100/90">
       <AppLayout />

        <main class="flex-1 p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-black text-zinc-900">Pedidos</h1>
                <button @click="abrirCrear"
                    class="flex items-center gap-2 bg-amber-500 text-white px-4 py-1.5 rounded-lg font-semibold hover:bg-amber-600 transition shadow-sm text-sm">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Nuevo Pedido</span>
                </button>
            </div>

            <section class="bg-white rounded-xl overflow-hidden border border-gray-200">
                <table class="w-full">
                   <thead class="bg-zinc-900 text-white text-sm uppercase tracking-wide">
    <tr>
        <th class="px-6 py-4 text-left">ID</th> <th class="px-6 py-4 text-left">Mesa</th>
        <th class="px-6 py-4 text-left">Cliente</th>
        <th class="px-6 py-4 text-left">Estado</th>
        <th class="px-6 py-4 text-left">Total</th>
      
    </tr>
</thead>
               <tbody>
    <tr v-for="p in pedidos" :key="p.id" class="border-t hover:bg-gray-50 transition">
        <td class="px-6 py-4 font-bold text-zinc-500">#{{ p.id }}</td>
        
        <td class="px-6 py-4">
            <span class="bg-zinc-100 text-zinc-800 font-bold px-2 py-1 rounded text-xs">
                Mesa {{ p.mesa_id }}
            </span>
        </td>
        
        <td class="px-6 py-4 font-medium text-zinc-900">
            {{ p.nombre_cliente }}
        </td>

        <td class="px-6 py-4 text-gray-700 capitalize">{{ p.estado }}</td>
        
        <td class="px-6 py-4 font-semibold text-gray-700">
            ${{ Number(p.total).toLocaleString('es-CL') }}
        </td>
        
       
    </tr>
</tbody>
                </table>
            </section>
        </main>
    </div>

    <div v-if="modalAbierto" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-md rounded-2xl p-6 shadow-2xl border border-zinc-100">
            <h2 class="text-xl font-black text-zinc-900 mb-6">Nuevo Pedido</h2>

            <form @submit.prevent="guardar" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <input v-model="form.mesa_id" placeholder="N° Mesa" class="border-zinc-200 rounded-xl p-3 w-full" />
                    <input v-model="form.nombre_cliente" placeholder="Cliente" class="border-zinc-200 rounded-xl p-3 w-full" />
                </div>

                <select v-model="prodSeleccionado" @change="agregarProducto" class="w-full border-zinc-200 rounded-xl p-3">
                    <option :value="null">Seleccionar producto...</option>
                    <option v-for="prod in productos" :key="prod.id" :value="prod">{{ prod.nombre }} (${{ prod.precio }})</option>
                </select>

                <div class="bg-zinc-50 p-3 rounded-xl max-h-40 overflow-y-auto">
                    <p v-for="(item, index) in form.productos" :key="index" class="text-sm border-b py-1">
                        {{ item.nombre }} - ${{ item.precio }}
                    </p>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" @click="modalAbierto = false" class="px-5 py-2.5 text-sm font-semibold text-zinc-600 hover:bg-zinc-100 rounded-xl">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold bg-amber-500 text-white rounded-xl hover:bg-amber-600 shadow-md">Guardar Pedido</button>
                </div>
            </form>
        </div>
    </div>
</template>