<script setup>
import { usePage, router } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()

const props = defineProps({
    pedidos: {
        type: Array,
        default: () => []
    }
})

const filtroEstado = ref('todos')
const modalPedido = ref(null)



const actualizarHora = () => {
    const now = new Date()

    horaActual.value = now.toLocaleTimeString('es-CL', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    })
}

let interval = null

onMounted(() => {
    actualizarHora()
    interval = setInterval(actualizarHora, 1000)
})

onUnmounted(() => {
    clearInterval(interval)
})

const pedidosFiltrados = computed(() => {

    if (filtroEstado.value === 'todos') {
        return props.pedidos
    }

    return props.pedidos.filter(
        pedido => pedido.estado === filtroEstado.value
    )
})

const abrirModal = (pedido) => {
    modalPedido.value = pedido
}

const cambiarEstado = (pedido, estado) => {

    router.post(`/dashboard/preparacion/${pedido.id}/estado`, {
        estado
    }, {
        preserveScroll: true,
        onSuccess: () => {

            pedido.estado = estado

            if (modalPedido.value) {
                modalPedido.value.estado = estado
            }

        }
    })
}
</script>

<template>

    <navSito />

    <main class="flex min-h-screen bg-zinc-100/90">

        <AppLayout />

        <section class="flex-1 min-w-0">

            <!-- TOP BAR -->
            <div
                class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-zinc-950/90 px-4 py-2">

                <!-- FILTROS -->
                <div class="flex items-center gap-1 overflow-x-auto whitespace-nowrap pb-1">

                    <button @click="filtroEstado = 'todos'"
                        class="px-3 py-3 text-[11px] font-bold transition-all rounded-lg"
                        :class="filtroEstado === 'todos'
                            ? 'bg-zinc-900 text-white'
                            : 'text-zinc-300 hover:bg-zinc-800'">
                        Todos
                    </button>

                    <button @click="filtroEstado = 'pendiente'"
                        class="px-3 py-3 text-[11px] font-bold transition-all rounded-lg"
                        :class="filtroEstado === 'pendiente'
                            ? 'bg-orange-400 text-white'
                            : 'text-white hover:bg-orange-400'">
                        Pendientes
                    </button>

                    <button @click="filtroEstado = 'en_preparacion'"
                        class="px-3 py-3 text-[11px] font-bold transition-all rounded-lg"
                        :class="filtroEstado === 'en_preparacion'
                            ? 'bg-sky-500 text-white'
                            : 'text-white hover:bg-sky-500'">
                        Preparando
                    </button>

                    <button @click="filtroEstado = 'listo'"
                        class="px-3 py-3 text-[11px] font-bold transition-all rounded-lg"
                        :class="filtroEstado === 'listo'
                            ? 'bg-emerald-500 text-white'
                            : 'text-white hover:bg-emerald-500'">
                        Listos
                    </button>

                </div>

      

            </div>

            <!-- EMPTY -->
            <div v-if="pedidosFiltrados.length === 0"
                class="bg-white border border-zinc-200 rounded-xl p-8 sm:p-12 text-center text-sm text-zinc-400 max-w-md mx-auto mt-12 shadow-sm">

                <i class="fa-solid fa-inbox block text-zinc-300 text-3xl mb-3"></i>

                No hay pedidos en esta sección.

            </div>

            <!-- CARDS -->
            <div
                v-else
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 px-4 sm:px-6 py-2"
            >

                <div
                    v-for="pedido in pedidosFiltrados"
                    :key="pedido.id"
                    class="relative overflow-hidden bg-white rounded-3xl border border-zinc-200 p-4 flex flex-col justify-between w-full transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
                >

                    <!-- ESTADO -->
                    <div
                        :class="{
                            'bg-orange-400': pedido.estado === 'pendiente',
                            'bg-sky-500': pedido.estado === 'en_preparacion',
                            'bg-emerald-500': pedido.estado === 'listo'
                        }"
                        class="absolute top-0 left-0 w-full h-7 rounded-t-3xl flex items-center px-4"
                    >

                        <p class="text-[10px] font-black uppercase text-white">
                            {{
                                pedido.estado === 'pendiente'
                                    ? 'Pendiente'
                                    : pedido.estado === 'en_preparacion'
                                        ? 'En preparación'
                                        : 'Listo'
                            }}
                        </p>

                    </div>

                    <!-- CONTENIDO -->
                    <div class="p-2">

                        <p class="text-[11px] font-bold text-zinc-500 pt-5">
                            Pedido #{{ pedido.id }}
                        </p>

                        <h2 class="text-2xl sm:text-3xl font-black text-zinc-950">
                            Mesa {{ pedido.mesa?.numero ?? '--' }}
                        </h2>

                        <div class="flex items-center gap-2 mt-3 text-[11px] text-zinc-500">
                            <i class="fa-regular fa-clock"></i>

                            {{
                                new Date(pedido.created_at).toLocaleTimeString('es-CL', {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })
                            }}
                            hrs
                        </div>

                    </div>

                    <!-- CLIENTE -->
                    <div class="flex flex-col sm:flex-row sm:justify-between gap-1 mt-2">

                        <p class="text-[10px] text-zinc-400 font-black uppercase">
                            Cliente
                        </p>

                        <p class="text-sm font-black text-zinc-900">
                            {{ pedido.nombre_cliente || 'Sin nombre' }}
                        </p>

                    </div>

                    <!-- PRODUCTOS -->
                    <div class="mt-3 space-y-1">

                        <div
                            v-for="detalle in pedido.detalles.slice(0, 4)"
                            :key="detalle.id"
                        >
                            <p class="text-sm font-black text-zinc-800">
                                {{ detalle.producto.nombre }}
                            </p>
                        </div>

                        <p
                            v-if="pedido.detalles.length > 4"
                            class="text-xs text-zinc-400"
                        >
                            +{{ pedido.detalles.length - 4 }} productos más
                        </p>

                    </div>

                    <!-- ACCIONES -->
                    <div class="mt-4">

                        <button
                            v-if="pedido.estado === 'pendiente'"
                            @click="cambiarEstado(pedido, 'en_preparacion')"
                            class="w-full bg-orange-500 text-white font-black text-sm py-3 rounded-xl hover:bg-orange-600 transition"
                        >
                            Iniciar preparación
                        </button>

                        <button
                            v-if="pedido.estado === 'en_preparacion'"
                            @click="cambiarEstado(pedido, 'listo')"
                            class="w-full bg-sky-500 text-white font-black text-sm py-3 rounded-xl hover:bg-sky-600 transition"
                        >
                            Marcar listo
                        </button>

                        <div
                            v-if="pedido.estado === 'listo'"
                            class="w-full bg-emerald-500 text-white text-center font-black text-sm py-3 rounded-xl"
                        >
                            Pedido listo
                        </div>

                    </div>

                </div>

            </div>

        </section>

    </main>

</template>