<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import navSito from '@/Components/Nav.vue'

const page = usePage()

const props = defineProps({
    pedidos: {
        type: Array,
        default: () => []
    }
})

const filtroEstado = ref('todos')
const modalPedido = ref(null)
const sidebarColapsado = ref(false)

/*
|--------------------------------------------------------------------------
| FILTROS
|--------------------------------------------------------------------------
*/

const pedidosFiltrados = computed(() => {

    if (filtroEstado.value === 'todos') {
        return props.pedidos
    }

    return props.pedidos.filter(
        pedido => pedido.estado === filtroEstado.value
    )

})

/*
|--------------------------------------------------------------------------
| MODAL
|--------------------------------------------------------------------------
*/

const abrirModal = (pedido) => {
    modalPedido.value = pedido
}

/*
|--------------------------------------------------------------------------
| CAMBIAR ESTADO
|--------------------------------------------------------------------------
*/

const cambiarEstado = (pedido, estado) => {

    router.post(`/barista/pedidos/${pedido.id}/estado`, {

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

        <aside class="bg-zinc-950 flex flex-col shadow-xl transition-all duration-300" :class="sidebarColapsado
            ? 'w-20'
            : 'w-64'">

            <div class="px-4 py-6 flex items-center justify-between">

                <p v-if="!sidebarColapsado" class="text-lg font-bold text-white mt-0.5">

                    Panel Barista

                </p>

                <button @click="sidebarColapsado = !sidebarColapsado"
                    class="w-10 h-10 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-white flex items-center justify-center transition-all">

                    <i class="fa-solid" :class="sidebarColapsado
                        ? 'fa-chevron-right'
                        : 'fa-chevron-left'"></i>

                </button>

            </div>

            <nav class="flex-1 px-4 py-6">

                <div class="space-y-1.5">

                    <Link href="/barista/pedidos"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200" :class="page.url.startsWith('/barista/pedidos')
                            ? 'bg-zinc-900 text-white font-medium shadow-sm'
                            : 'text-zinc-500 hover:bg-zinc-900 hover:text-white'">

                        <i class="fa-solid fa-clipboard-list text-sm"></i>

                        <span v-if="!sidebarColapsado" class="text-sm flex-1">

                            Preparación

                        </span>

                    </Link>

                </div>

                <div class="space-y-1.5 mt-2">

                    <Link href="/barista/mesas"
                        class="group flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200" :class="page.url.startsWith('/barista/mesas')
                            ? 'bg-zinc-900 text-white font-medium shadow-sm'
                            : 'text-zinc-500 hover:bg-zinc-900 hover:text-white'">

                        <i class="fa-solid fa-table text-sm"></i>

                        <span v-if="!sidebarColapsado" class="text-sm">

                            Mesas

                        </span>

                    </Link>

                </div>

            </nav>

        </aside>

        <section class="flex-1">



            <div class="mb-5 flex items-center justify-between bg-zinc-950/90 ">

                <div class="flex items-center gap-6">

                    <div class="flex items-center ">

                        <button @click="filtroEstado = 'todos'"
                            class="px-3 py-4  text-[11px] font-bold transition-all" :class="filtroEstado === 'todos'
                                ? 'bg-zinc-900 text-white'
                                : 'text-zinc-500 hover:bg-zinc-100'">

                            Todos

                        </button>

                        <button @click="filtroEstado = 'pendiente'"
                            class="px-3 py-4 text-[11px] font-bold transition-all" :class="filtroEstado === 'pendiente'
                                ? 'bg-orange-400 text-white'
                                : 'text-white hover:bg-orange-400'">

                            Pendientes

                        </button>

                        <button @click="filtroEstado = 'en_preparacion'"
                            class="px-3 py-4 text-[11px] font-bold transition-all" :class="filtroEstado === 'en_preparacion'
                                ? 'bg-sky-500 text-white'
                                : 'text-white hover:bg-sky-500'">

                            Preparando

                        </button>

                        <button @click="filtroEstado = 'listo'"
                            class="px-3 py-4  text-[11px] font-bold transition-all" :class="filtroEstado === 'listo'
                                ? 'bg-emerald-500 text-white'
                                : 'text-white hover:bg-emerald-500'">

                            Listos

                        </button>

                    </div>

                </div>


            </div>

            <div v-if="pedidosFiltrados.length === 0"
                class="bg-white border border-zinc-200 rounded-xl p-12 text-center text-sm text-zinc-400 max-w-md mx-auto mt-12 shadow-sm">

                <i class="fa-solid fa-inbox block text-zinc-300 text-xl mb-3"></i>

                No hay pedidos en esta sección.

            </div>

            <div v-else class="flex flex-wrap gap-3 px-7 py-2">

                <div v-for="pedido in pedidosFiltrados" :key="pedido.id"
                    class="relative overflow-hidden cursor-pointer bg-white rounded-3xl border border-zinc-200 p-4 flex flex-col justify-between w-[280px] transition-all duration-300 hover:shadow-xl hover:-translate-y-1">

                    <div :class="{
                        'bg-orange-400': pedido.estado === 'pendiente',
                        'bg-sky-500': pedido.estado === 'en_preparacion',
                        'bg-emerald-500': pedido.estado === 'listo'
                    }" class="absolute top-0 left-0 w-full h-7 rounded-t-3xl flex items-center px-4">

                        <p class="text-[10px] font-black uppercase tracking-wider text-white">

                            {{
                                pedido.estado === 'pendiente'
                                    ? 'Pendiente'
                                    : pedido.estado === 'en_preparacion'
                                        ? 'En preparación'
                            : 'Listo'
                            }}

                        </p>

                    </div>


                    <div class="rounded-2xl p-2 ">

                        <div class="flex items-start justify-between">

                            <div>

                                <p class="text-[11px] font-bold text-zinc-500 pt-5">
                                    Pedido #{{ pedido.id }}
                                </p>

                                <h2 class="text-3xl font-black text-zinc-950 leading-none mt-1">
                                    Mesa {{ pedido.mesa?.numero ?? '--' }}
                                </h2>

                            </div>



                        </div>

                        <div class="flex items-center gap-2 mt-4">

                            <i class="fa-regular fa-clock text-zinc-400 text-xs"></i>

                            <span class="text-[11px] font-bold text-zinc-500">

                                {{
                                    new Date(pedido.created_at).toLocaleTimeString('es-CL', {
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })
                                }} hrs

                            </span>

                        </div>

                    </div>

                    <!-- CLIENTE -->
                    <div class="flex justify-between ">

                        <p class="text-[10px] uppercase tracking-wide text-zinc-400 font-black">
                            Cliente
                        </p>

                        <p class="text-sm font-black text-zinc-900 mt-1">
                            {{ pedido.nombre_cliente || 'Sin nombre' }}
                        </p>

                    </div>

                    <!-- PRODUCTOS -->
                    <div class="flex flex-col gap-2">

                        <div v-for="detalle in pedido.detalles.slice(0, 4)" :key="detalle.id">

                            <div class="flex items-center justify-between">



                                <p class="text-sm font-black text-zinc-950">
                                    {{ detalle.producto.nombre }}
                                </p>

                            </div>


                            <div>
                                <p class="text-[11px] text-zinc-500 mt-1">
                                    Tamaño:
                                    <span class="font-bold">
                                        {{ detalle.tamano }}
                                    </span>
                                </p>
                            </div>



                        </div>

                    </div>

                    <!-- FOOTER -->
                    <div class="py-3">


                        <div @click.stop>

                            <button v-if="pedido.estado === 'pendiente'"
                                @click="cambiarEstado(pedido, 'en_preparacion')"
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black text-sm py-2 rounded-md transition-all duration-200 active:scale-95 shadow-lg shadow-orange-500/30">

                                Iniciar preparación

                            </button>

                            <button v-if="pedido.estado === 'en_preparacion'" @click="cambiarEstado(pedido, 'listo')"
                                class="w-full bg-sky-500 hover:bg-sky-600 text-white font-black text-sm py-2 rounded-md transition-all duration-200 active:scale-95 shadow-lg shadow-sky-500/30">

                                Registrar pedido listo

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </section>

    </main>

</template>