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
    productos: []
})

const prodSeleccionado = ref(null)

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


const agregarProducto = () => {
    if (!prodSeleccionado.value) return

    const existe = form.productos.find(
        p => p.id === prodSeleccionado.value.id
    )

    if (existe) {
        existe.cantidad++
    } else {
        form.productos.push({
            id: prodSeleccionado.value.id,
            nombre: prodSeleccionado.value.nombre,
            precio: prodSeleccionado.value.precio,
            cantidad: 1
        })
    }

    prodSeleccionado.value = null
}

const abrirCrear = () => {
    form.reset()
    form.productos = []
    modalAbierto.value = true
}

const guardar = () => {
    form.post('/dashboard/pedidos', {
        onSuccess: () => {
            modalAbierto.value = false
            form.reset()
            form.productos = []
        }
    })
}
</script>

<template>
    <navSito />

    <div class="flex min-h-screen bg-zinc-100/90">
        <AppLayout />

        <main class="flex-1 p-8">

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-black text-zinc-900">Pedidos</h1>

                <div class="flex gap-2">
                    <button @click="exportarExcel"
                        class="flex items-center gap-2 bg-green-500 text-white px-4 py-1.5 rounded-lg font-semibold hover:bg-green-600 transition shadow-sm text-sm">
                        <i class="fa-solid fa-file-excel text-[10px]"></i>
                        <span>Exportar Excel</span>
                    </button>
                    <button @click="abrirCrear"
                        class="flex items-center gap-2 bg-amber-500 text-white px-4 py-1.5 rounded-lg font-semibold hover:bg-amber-600 transition shadow-sm text-sm">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Nuevo Pedido</span>
                    </button>
                </div>
            </div>
            <!-- 🔵 TU TABLA ORIGINAL (NO TOCADA) -->
            <section class="bg-white rounded-xl overflow-hidden border border-gray-200">
                <table class="w-full">

                    <thead class="bg-zinc-900 text-white text-sm uppercase tracking-wide">
                        <tr>
                            <th class="px-6 py-4 text-left">ID</th>
                            <th class="px-6 py-4 text-left">Mesa</th>
                            <th class="px-6 py-4 text-left">Cliente</th>
                            <th class="px-6 py-4 text-left">Estado de entrega</th>
                            <th class="px-6 py-4 text-left">Origen</th>
                            <th class="px-6 py-4 text-left">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="p in pedidos" :key="p.id" class="border-t hover:bg-gray-50 transition">

                            <td class="px-6 py-4 font-bold text-zinc-500">
                                #{{ p.id }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="bg-zinc-100 text-zinc-800 font-bold px-2 py-1 rounded text-xs">
                                    Mesa {{ p.mesa_id }}
                                </span>
                            </td>

                            <td class="px-6 py-4 font-medium text-zinc-900">
                                {{ p.nombre_cliente }}
                            </td>

                            <td class="px-6 py-4 text-gray-700 capitalize">
                                <span
        :class="{
            'bg-yellow-100 text-yellow-800': p.estado === 'pendiente',
            'bg-blue-100 text-blue-800': p.estado === 'en_preparacion',
            'bg-green-100 text-green-800': p.estado === 'listo',
            'bg-red-100 text-red-800': p.estado === 'cancelado'
        }"
        class="px-3 py-1 rounded-full text-xs font-bold capitalize"
    >
        {{ p.estado }}
    </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700 capitalize">
                                {{ p.tipo_pedido }}
                            </td>

                            <td class="px-6 py-4 font-semibold text-gray-700">
                                ${{ Number(p.total).toLocaleString('es-CL') }}
                            </td>

                        </tr>
                    </tbody>

                </table>
            </section>

        </main>
    </div>

    <!-- 🟡 MODAL POS MEJORADO -->
    <div v-if="modalAbierto" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">

        <div class="bg-white w-full max-w-4xl rounded-2xl shadow-xl overflow-hidden">

            <!-- HEADER -->
            <div class="bg-zinc-900 text-white p-4 font-bold">
                Nuevo Pedido
            </div>

            <div class="grid grid-cols-2 gap-6 p-6">

                <!-- FORM -->
                <div class="space-y-4">

                    <input v-model="form.nombre_cliente" placeholder="Nombre del cliente"
                        class="w-full border rounded-xl p-3" />

                    <select v-model="form.mesa_id" class="w-full border rounded-xl p-3">
                        <option value="">Seleccionar mesa</option>
                        <option v-for="m in props.mesas" :key="m.id" :value="m.id">
                            Mesa {{ m.numero }}
                        </option>
                    </select>

                    <select v-model="prodSeleccionado" @change="agregarProducto" class="w-full border rounded-xl p-3">
                        <option :value="null">Agregar producto</option>
                        <option v-for="prod in props.productos" :key="prod.id" :value="prod">
                            {{ prod.nombre }} (${{ prod.precio }})
                        </option>
                    </select>

                </div>

                <!-- CARRITO -->
                <div class="bg-zinc-50 rounded-xl p-4">

                    <h3 class="font-bold mb-3">Productos</h3>

                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        <div v-for="(item, i) in form.productos" :key="i"
                            class="flex justify-between bg-white p-2 rounded-lg text-sm">

                            <span>{{ item.nombre }} x{{ item.cantidad }}</span>

                            <span class="font-semibold">
                                ${{ item.precio * item.cantidad }}
                            </span>

                        </div>

                        <p v-if="form.productos.length === 0" class="text-gray-400 text-sm">
                            Sin productos
                        </p>
                    </div>

                    <div class="mt-4 border-t pt-3 flex justify-between font-bold">
                        <span>Total</span>

                        <span>
                            ${{form.productos.reduce((t, p) => t + p.precio * p.cantidad, 0)}}
                        </span>
                    </div>

                </div>

            </div>

            <!-- BOTONES -->
            <div class="flex justify-end gap-2 p-4 border-t">

                <button @click="modalAbierto = false" class="px-4 py-2 border rounded-lg">
                    Cancelar
                </button>

                <button @click="guardar" class="px-5 py-2 bg-amber-500 text-white rounded-lg">
                    Guardar Pedido
                </button>

            </div>

        </div>

    </div>

</template>