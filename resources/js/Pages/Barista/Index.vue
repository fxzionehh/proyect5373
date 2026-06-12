<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
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
const sidebarColapsado = ref(true)

// 🟡 RELOJ EN TIEMPO REAL
const horaActual = ref('')

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
  

        <!-- MAIN -->
        <section class="flex-1">

            <!-- 🔥 TOP BAR CON HORA -->
            <div class="mb-5 flex items-center justify-between bg-zinc-950/90 px-4 ">

                <div class="flex items-center gap-1">

                    <button @click="filtroEstado = 'todos'"
                        class="px-3 py-4 text-[11px] font-bold transition-all"
                        :class="filtroEstado === 'todos'
                            ? 'bg-zinc-900 text-white'
                            : 'text-zinc-500 hover:bg-zinc-100'">
                        Todos
                    </button>

                    <button @click="filtroEstado = 'pendiente'"
                        class="px-3 py-4 text-[11px] font-bold transition-all"
                        :class="filtroEstado === 'pendiente'
                            ? 'bg-orange-400 text-white'
                            : 'text-white hover:bg-orange-400'">
                        Pendientes
                    </button>

                    <button @click="filtroEstado = 'en_preparacion'"
                        class="px-3 py-4 text-[11px] font-bold transition-all"
                        :class="filtroEstado === 'en_preparacion'
                            ? 'bg-sky-500 text-white'
                            : 'text-white hover:bg-sky-500'">
                        Preparando
                    </button>

                    <button @click="filtroEstado = 'listo'"
                        class="px-3 py-4 text-[11px] font-bold transition-all"
                        :class="filtroEstado === 'listo'
                            ? 'bg-emerald-500 text-white'
                            : 'text-white hover:bg-emerald-500'">
                        Listos
                    </button>

                </div>

                <!-- 🟡 HORA EN VIVO -->
                <div class="text-white text-[20px] font-black tracking-widest">
                    {{ horaActual }}
                </div>

            </div>

            <!-- EMPTY -->
            <div v-if="pedidosFiltrados.length === 0"
                class="bg-white border border-zinc-200 rounded-xl p-12 text-center text-sm text-zinc-400 max-w-md mx-auto mt-12 shadow-sm">

                <i class="fa-solid fa-inbox block text-zinc-300 text-xl mb-3"></i>
                No hay pedidos en esta sección.

            </div>

            <!-- CARDS -->
            <div v-else class="flex flex-wrap gap-3 px-7 py-2">

                <div v-for="pedido in pedidosFiltrados" :key="pedido.id"
                    class="relative overflow-hidden cursor-pointer bg-white rounded-3xl border border-zinc-200 p-4 flex flex-col justify-between w-[280px] transition-all duration-300 hover:shadow-xl hover:-translate-y-1">

                    <div :class="{
                        'bg-orange-400': pedido.estado === 'pendiente',
                        'bg-sky-500': pedido.estado === 'en_preparacion',
                        'bg-emerald-500': pedido.estado === 'listo'
                    }"
                        class="absolute top-0 left-0 w-full h-7 rounded-t-3xl flex items-center px-4">

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

                    <div class="p-2">

                        <p class="text-[11px] font-bold text-zinc-500 pt-5">
                            Pedido #{{ pedido.id }}
                        </p>

                        <h2 class="text-3xl font-black text-zinc-950">
                            Mesa {{ pedido.mesa?.numero ?? '--' }}
                        </h2>

                        <div class="flex items-center gap-2 mt-3 text-[11px] text-zinc-500">
                            <i class="fa-regular fa-clock"></i>

                            {{ new Date(pedido.created_at).toLocaleTimeString('es-CL', {
                                hour: '2-digit',
                                minute: '2-digit'
                            }) }} hrs
                        </div>

                    </div>

                    <div class="flex justify-between">
                        <p class="text-[10px] text-zinc-400 font-black uppercase">
                            Cliente
                        </p>
                        <p class="text-sm font-black text-zinc-900">
                            {{ pedido.nombre_cliente || 'Sin nombre' }}
                        </p>
                    </div>

                    <div class="mt-3 space-y-1">
                        <div v-for="detalle in pedido.detalles.slice(0, 4)" :key="detalle.id">

                            <p class="text-sm font-black">
                                {{ detalle.producto.nombre }}
                            </p>

                        </div>
                    </div>

                    <div class="mt-4">

                        <button v-if="pedido.estado === 'pendiente'"
                            @click="cambiarEstado(pedido, 'en_preparacion')"
                            class="w-full bg-orange-500 text-white font-black text-sm py-2 rounded-md">
                            Iniciar preparación
                        </button>

                        <button v-if="pedido.estado === 'en_preparacion'"
                            @click="cambiarEstado(pedido, 'listo')"
                            class="w-full bg-sky-500 text-white font-black text-sm py-2 rounded-md">
                            Marcar listo
                        </button>

                    </div>

                </div>

            </div>

        </section>

    </main>

</template>