<script setup>
import * as XLSX from 'xlsx'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    pedidos: Array,
    productos: Array,
    mesas: Array
})

const modalAbierto = ref(false)

const form = useForm({
    mesa_id: '',
    nombre_cliente: '',
    estado: 'pendiente',
    tipo_pedido: 'presencial',
    producto_id: null,
    productos: [] // Inicializado para evitar errores al agregar
})

const exportarExcel = () => {
    const data = props.pedidos.map(p => ({
        ID: p.id,
        Mesa: p.mesa_id,
        Cliente: p.nombre_cliente,
        Estado: p.estado,
        Origen: p.tipo_pedido,
        Total: p.total
    }))

    const worksheet = XLSX.utils.json_to_sheet(data)
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Pedidos')
    XLSX.writeFile(workbook, 'pedidos.xlsx')
}

const agregarProducto = (id) => {
    const producto = props.productos.find(p => p.id == id)
    if (!producto) return

    const existe = form.productos.find(p => p.id == id)
    if (existe) {
        existe.cantidad++
    } else {
        form.productos.push({ id: producto.id, cantidad: 1 })
    }
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

    <div class="min-h-screen bg-zinc-100/90 md:flex">
        <AppLayout />

        <main class="flex-1 p-4 md:p-8">
            <!-- HEADER -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h1 class="text-3xl font-black text-zinc-900">Pedidos</h1>

                <div class="flex gap-2 w-full sm:w-auto">
                    <button 
                        @click="exportarExcel"
                        class="flex-1 sm:flex-none bg-green-500 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-green-600 transition"
                    >
                        Exportar Excel
                    </button>
                    <button 
                        @click="abrirCrear"
                        class="flex-1 sm:flex-none bg-amber-500 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-amber-600 transition"
                    >
                        Nuevo Pedido
                    </button>
                </div>
            </div>

            <!-- TABLA RESPONSIVA -->
            <section class="bg-white rounded-xl overflow-hidden border shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-900 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left">ID</th>
                                <th class="px-6 py-4 text-left">Mesa</th>
                                <th class="px-6 py-4 text-left">Cliente</th>
                                <th class="px-6 py-4 text-left">Estado</th>
                                <th class="px-6 py-4 text-left">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            <tr v-for="p in pedidos" :key="p.id" class="hover:bg-zinc-50 transition">
                                <td class="px-6 py-4 font-medium">#{{ p.id }}</td>
                                <td class="px-6 py-4">Mesa {{ p.mesa_id }}</td>
                                <td class="px-6 py-4">{{ p.nombre_cliente }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-zinc-100 rounded-full text-xs font-semibold uppercase">
                                        {{ p.estado }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold">
                                    ${{ Number(p.total).toLocaleString('es-CL') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- MODAL -->
    <div v-if="modalAbierto" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl max-h-[90vh] flex flex-col">
            <div class="bg-zinc-900 text-white p-4 font-bold rounded-t-2xl">
                Nuevo Pedido
            </div>

            <div class="p-6 overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- FORMULARIO -->
                <div class="space-y-4">
                    <input v-model="form.nombre_cliente" placeholder="Nombre del cliente" class="w-full border rounded-xl p-3" />
                    <select v-model="form.mesa_id" class="w-full border rounded-xl p-3">
                        <option value="">Seleccionar mesa</option>
                        <option v-for="m in props.mesas" :key="m.id" :value="m.id">Mesa {{ m.numero }}</option>
                    </select>
                </div>

                <!-- PRODUCTOS -->
                <div>
                    <select @change="agregarProducto($event.target.value)" class="w-full border rounded-xl p-3">
                        <option value="">Añadir producto...</option>
                        <option v-for="prod in props.productos" :key="prod.id" :value="prod.id">
                            {{ prod.nombre }} - ${{ prod.precio_normal }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- FOOTER MODAL -->
            <div class="flex justify-end gap-2 p-4 border-t bg-zinc-50 rounded-b-2xl">
                <button @click="modalAbierto = false" class="px-4 py-2 border rounded-lg hover:bg-zinc-200">Cancelar</button>
                <button @click="guardar" class="px-5 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">Guardar Pedido</button>
            </div>
        </div>
    </div>
</template>