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
    producto_id: '',
})


const exportarExcel = () => {
    const data = props.pedidos.map(p => ({
        ID: p.id,
        Mesa: p.mesa_id ? `Mesa ${p.mesa_id}` : 'N/A',
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


const limpiar = () => {
    form.reset()
    form.clearErrors()
}

const abrirCrear = () => {
    limpiar()
    modalAbierto.value = true
}


const guardar = () => {
    form.post('/dashboard/pedidos', {
        preserveScroll: true,
        preserveState: true, 
        onSuccess: () => {
            modalAbierto.value = false
            limpiar()
        }
    })
}
</script>

<template>
    <navSito />

    <div class="min-h-screen bg-zinc-100/90 md:flex">
        <AppLayout />

        <main class="flex-1 p-4 md:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h1 class="text-3xl font-black text-zinc-900">Pedidos</h1>

                <div class="flex gap-2 w-full sm:w-auto">
                    <button @click="exportarExcel" class="flex-1 sm:flex-none bg-green-500 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-green-600 transition">
                        Exportar Excel
                    </button>
                    <button @click="abrirCrear" class="flex-1 sm:flex-none bg-amber-500 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-amber-600 transition">
                        Nuevo Pedido
                    </button>
                </div>
            </div>

            <section class="bg-white rounded-xl overflow-hidden border shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-900 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left">ID</th>
                                <th class="px-6 py-4 text-left">Mesa</th>
                                <th class="px-6 py-4 text-left">Cliente</th>
                                <th class="px-6 py-4 text-left">Producto</th>
                                <th class="px-6 py-4 text-left">Estado</th>
                                <th class="px-6 py-4 text-left">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            <tr v-for="p in pedidos" :key="p.id" class="hover:bg-zinc-50 transition">
                                <td class="px-6 py-4 font-medium">#{{ p.id }}</td>
                                <td class="px-6 py-4">{{ p.mesa_id ? 'Mesa ' + p.mesa_id : 'Sin mesa' }}</td>
                                <td class="px-6 py-4">{{ p.nombre_cliente }}</td>
                                <td class="px-6 py-4">
                                    {{ p.detalles?.[0]?.producto?.nombre || 'Sin producto' }}
                                </td>
                                <td class="px-6 py-4"><span class="px-2 py-1 bg-zinc-100 rounded-full text-xs font-semibold uppercase">{{ p.estado }}</span></td>
                                <td class="px-6 py-4 font-bold">${{ Number(p.total).toLocaleString('es-CL') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <div v-if="modalAbierto" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-zinc-900 text-white p-4 font-bold flex justify-between items-center">
                <span>Nuevo Pedido</span>
                <button @click="modalAbierto = false" class="text-white/70 hover:text-white">✕</button>
            </div>

            <form @submit.prevent="guardar" class="p-6 space-y-4">
                <div>
                    <input v-model="form.nombre_cliente" placeholder="Nombre del cliente" class="w-full border rounded-xl p-3 text-sm focus:ring-1 focus:ring-amber-500 outline-none" />
                    <p v-if="form.errors.nombre_cliente" class="text-red-500 text-xs mt-1">{{ form.errors.nombre_cliente }}</p>
                </div>

                <div>
                    <select v-model="form.mesa_id" class="w-full border rounded-xl p-3 text-sm focus:ring-1 focus:ring-amber-500 outline-none">
                        <option value="">Sin mesa (Para llevar / Delivery)</option>
                        <option v-for="m in props.mesas" :key="m.id" :value="m.id">Mesa {{ m.numero }}</option>
                    </select>
                </div>

                <div>
                    <select v-model="form.producto_id" class="w-full border rounded-xl p-3 text-sm focus:ring-1 focus:ring-amber-500 outline-none" :class="{'border-red-500': form.errors.producto_id}">
                        <option value="">Seleccionar producto</option>
                        <option v-for="prod in props.productos" :key="prod.id" :value="prod.id">
                            {{ prod.nombre }} - ${{ prod.precio_normal }}
                        </option>
                    </select>
                    <p v-if="form.errors.producto_id" class="text-red-500 text-xs mt-1">{{ form.errors.producto_id }}</p>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t mt-4">
                    <button type="button" @click="modalAbierto = false" class="px-4 py-2 border rounded-xl text-sm hover:bg-zinc-100">Cancelar</button>
                    <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-amber-500 text-white rounded-xl text-sm font-semibold hover:bg-amber-600 disabled:opacity-50">
                        {{ form.processing ? 'Guardando...' : 'Guardar Pedido' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>