<script setup>
import * as XLSX from 'xlsx'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import QrcodeVue from 'qrcode.vue'


const props = defineProps({
    pedidos: Array,
    productos: Array,
    mesas: Array,
    appUrl: String
})
const urlBase = window.location.origin
const modalAbierto = ref(false)

const form = useForm({
    mesa_id: '',
    nombre_cliente: '',
    estado: 'pendiente',
    tipo_pedido: 'presencial',
    producto_id: '',
    tamano: ''
})


const exportarExcel = () => {
    const data = props.pedidos.map(p => ({
        ID: p.id,
        Mesa: p.mesa_id ? `Mesa ${p.mesa_id}` : 'Sin mesa',
        Cliente: p.nombre_cliente,
        Producto: p.detalles?.[0]?.producto?.nombre || 'Sin producto',
        Vaso: p.detalles?.[0]?.tamano || 'N/A',
        Estado: p.estado,
        Origen: p.tipo_pedido,
        Fecha: new Date(p.created_at).toLocaleDateString('es-CL'),
        Hora: new Date(p.created_at).toLocaleTimeString('es-CL', {
            hour: '2-digit',
            minute: '2-digit'
        }),
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

<main class="flex-1 min-w-0 p-4 md:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <h1 class="text-3xl font-black text-zinc-900">Pedidos</h1>

                <div class="flex gap-2 w-full sm:w-auto">
                    <button @click="exportarExcel"
                        class="flex-1 sm:flex-none bg-green-500 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-green-600 transition">
                        Exportar Excel
                    </button>
                    <button @click="abrirCrear"
                        class="flex-1 sm:flex-none bg-amber-500 text-white px-4 py-2 rounded-lg font-semibold text-sm hover:bg-amber-600 transition">
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
                
                                <th class="px-6 py-4 text-left">Origen</th>
                                <th class="px-6 py-4 text-left">Fecha/Hora</th>
                                <th class="px-6 py-4 text-left">QR</th>
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
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase" :class="{
                                        'bg-yellow-100 text-yellow-800': p.estado === 'pendiente',
                                        'bg-blue-100 text-blue-800': p.estado === 'en_preparacion',
                                        'bg-green-100 text-green-800': p.estado === 'listo',
                                        'bg-gray-200 text-gray-700': p.estado === 'entregado',
                                        'bg-red-100 text-red-800': p.estado === 'cancelado'
                                    }">
                                        {{ p.estado.replace('_', ' ') }}
                                    </span>
                                </td>
                        

                        <td class="px-6 py-4 capitalize">
                            {{ p.tipo_pedido }}
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col leading-tight">
                                <span class="font-medium">
                                    {{ new Date(p.created_at).toLocaleDateString('es-CL') }}
                                </span>

                                <span class="text-xs leading-tight ">
                                    {{ new Date(p.created_at).toLocaleTimeString('es-CL', {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }) }}
                                </span>
                            </div>
                        </td>
                                <td class="px-6 py-4">
                                    <QrcodeVue
                                    :value="`${urlBase}/pedido/${p.codigo}`"
                                    :size="80"
                                    level="H"
                                    />
                                    </td>
                                <td class="px-6 py-4 font-bold">${{ Number(p.total).toLocaleString('es-CL') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <div v-if="modalAbierto"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-zinc-900 text-white p-4 font-bold flex justify-between items-center">
                <span>Nuevo Pedido</span>
                <button @click="modalAbierto = false" class="text-white/70 hover:text-white">✕</button>
            </div>
            <form @submit.prevent="guardar" class="p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-zinc-700 mb-1">
                            Nombre del cliente
                        </label>

                        <input v-model="form.nombre_cliente" placeholder="Ingrese el nombre del cliente"
                            class="w-full rounded-xl border border-zinc-300 p-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none" />

                        <p v-if="form.errors.nombre_cliente" class="text-red-500 text-[10px] font-bold mt-1">
                            {{ form.errors.nombre_cliente }}
                        </p>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 mb-1">
                            Mesa
                        </label>

                        <select v-model="form.mesa_id"
                            class="w-full rounded-xl border border-zinc-300 p-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                            <option value="">Sin mesa</option>

                            <option v-for="m in mesas" :key="m.id" :value="m.id">
                                Mesa {{ m.numero }}
                            </option>

                        </select>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 mb-1">
                            Producto
                        </label>

                        <select v-model="form.producto_id"
                            class="w-full rounded-xl border border-zinc-300 p-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                            <option value="">Seleccione un producto</option>

                            <option v-for="prod in productos" :key="prod.id" :value="prod.id">
                                {{ prod.nombre }}
                            </option>

                        </select>

                        <p v-if="form.errors.producto_id" class="text-red-500 text-[10px] font-bold mt-1">
                            {{ form.errors.producto_id }}
                        </p>
                    </div>


                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-zinc-700 mb-1">
                            Tamaño del vaso
                        </label>

                        <select v-model="form.tamano"
                            class="w-full rounded-xl border border-zinc-300 p-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
                            :class="{ 'border-red-500': form.errors.tamano }">
                            <option value="">Seleccione un tamaño</option>
                            <option value="nano">🥤 Nano</option>
                            <option value="mini">🥛 Mini</option>
                            <option value="normal">☕ Normal</option>
                            <option value="max">🧋 Max</option>
                        </select>

                        <p v-if="form.errors.tamano" class="text-red-500 text-[10px] font-bold mt-1">
                            {{ form.errors.tamano }}
                        </p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-zinc-700 mb-1">
                            Origen del pedido
                        </label>

                        <select v-model="form.tipo_pedido"
                            class="w-full rounded-xl border border-zinc-300 p-3 focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                            <option value="presencial">Presencial</option>

                        </select>
                    </div>

                </div>

                <div class="mt-8 border-t pt-5 flex justify-end gap-3">

                    <button type="button" @click="modalAbierto = false"
                        class="px-5 py-2 rounded-xl border border-zinc-300 hover:bg-zinc-100">
                        Cancelar
                    </button>

                    <button type="submit" :disabled="form.processing"
                        class="px-6 py-2 rounded-xl bg-amber-500 text-white font-semibold hover:bg-amber-600 disabled:opacity-50">
                        {{ form.processing ? 'Guardando...' : 'Guardar pedido' }}
                    </button>

                </div>

            </form>
        </div>
    </div>
</template>