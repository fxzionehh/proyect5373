<script setup>
import { ref, computed, onUpdated } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    pedidos: { type: Array, default: () => [] },
    mesas: { type: Array, default: () => [] }
})

const page = usePage()
const filtroEstado = ref('todos')
onUpdated(() => {
    if (page.props.flash?.error || page.props.flash?.success) {
        setTimeout(() => {
            page.props.flash.error = null
            page.props.flash.success = null
        }, 4000) 
    }
})
const pedidosFiltrados = computed(() => {
    if (filtroEstado.value === 'todos') return props.pedidos
    return props.pedidos.filter(p => p.estado === filtroEstado.value)
})

const cambiarEstado = (pedido, estado) => {
    router.post(`/dashboard/preparacion/${pedido.id}/estado`, { estado }, {
        preserveScroll: true,

        onSuccess: () => {
            router.reload({ only: ['pedidos'] })
        },

        onError: (errors) => {

            const mensajeError = errors.message || errors.error || 'Ocurrió un error con el stock';
            
        
            page.props.flash.error = mensajeError;
            page.props.flash.success = null;
        }
    })
}
</script>

<template>
    <div v-if="$page.props.flash?.error || $page.props.flash?.success" 
         class="toast-fixed"
         :class="$page.props.flash?.error ? 'bg-red-600' : 'bg-green-600'">
        {{ $page.props.flash.error || $page.props.flash.success }}
    </div>

    <navSito />

    <main class="flex min-h-screen bg-zinc-100/90">
        <AppLayout />

        <section class="flex-1 min-w-0">
            <div class="mb-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3 bg-zinc-950/90 px-4 py-2">
                <div class="flex items-center gap-1 overflow-x-auto whitespace-nowrap pb-1">
                    <button v-for="estado in ['todos', 'pendiente', 'en_preparacion', 'listo', 'entregado']" 
                        :key="estado"
                        @click="filtroEstado = estado"
                        class="px-3 py-3 text-[11px] font-bold rounded-lg capitalize"
                        :class="filtroEstado === estado ? 'bg-zinc-900 text-white' : 'text-zinc-300 hover:bg-zinc-800'">
                        {{ estado.replace('_', ' ') }}
                    </button>
                </div>
            </div>

            <div v-if="pedidosFiltrados.length === 0" class="bg-white border border-zinc-200 rounded-xl p-8 text-center text-sm text-zinc-400 max-w-md mx-auto mt-12 shadow-sm">
                <i class="fa-solid fa-inbox block text-zinc-300 text-3xl mb-3"></i>
                No hay pedidos en esta sección.
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 px-4 sm:px-6 py-2">
                <div v-for="pedido in pedidosFiltrados" :key="pedido.id" class="relative overflow-hidden bg-white rounded-3xl border border-zinc-200 p-4 flex flex-col justify-between hover:shadow-xl transition">
                    
                    <div :class="{
                        'bg-orange-400': pedido.estado === 'pendiente',
                        'bg-sky-500': pedido.estado === 'en_preparacion',
                        'bg-emerald-500': pedido.estado === 'listo',
                        'bg-green-800': pedido.estado === 'entregado'
                    }" class="absolute top-0 left-0 w-full h-7 rounded-t-3xl flex items-center px-4">
                        <p class="text-[10px] font-black uppercase text-white">{{ pedido.estado.replace('_', ' ') }}</p>
                    </div>

                    <div class="p-2">
                        <p class="text-[11px] font-bold text-zinc-500 pt-5">Pedido #{{ pedido.id }}</p>
                        <h2 class="text-2xl font-black text-zinc-950">Mesa {{ pedido.mesa?.numero ?? '--' }}</h2>
                        <div class="flex items-center gap-2 mt-2 text-[13px] text-zinc-500">
                            <i class="fa-regular fa-clock"></i>
                            {{ new Date(pedido.created_at).toLocaleTimeString('es-CL', { hour: '2-digit', minute: '2-digit' }) }} hrs
                        </div>
                    </div>

                    <div class="flex justify-between px-2 mt-2">
                        <p class="text-[10px] text-zinc-400 font-black uppercase">Cliente</p>
                        <p class="text-sm font-black text-zinc-900">{{ pedido.nombre_cliente || 'Sin nombre' }}</p>
                    </div>

                    <div class="mt-3 space-y-1 px-2">
                        <p class="text-[10px] text-zinc-400 font-black uppercase mb-1">Productos</p>
                        <div v-for="detalle in pedido.detalles.slice(0, 3)" :key="detalle.id">
                            <p class="text-sm font-black text-zinc-800">{{ detalle.producto.nombre }}</p>
                        </div>
                        <p v-if="pedido.detalles.length > 3" class="text-xs text-zinc-400">+{{ pedido.detalles.length - 3 }} más</p>
                    </div>

                    <div class="mt-4 px-2">
                        <button v-if="pedido.estado === 'pendiente'" @click="cambiarEstado(pedido, 'en_preparacion')" class="w-full bg-orange-500 text-white font-black py-3 rounded-xl hover:opacity-90">Iniciar preparación</button>
                        <button v-if="pedido.estado === 'en_preparacion'" @click="cambiarEstado(pedido, 'listo')" class="w-full bg-sky-500 text-white font-black py-3 rounded-xl hover:opacity-90">Marcar listo</button>
                        <button v-if="pedido.estado === 'listo'" @click="cambiarEstado(pedido, 'entregado')" class="w-full bg-green-700 text-white font-black py-3 rounded-xl hover:opacity-90">Marcar entregado</button>
                        <div v-if="pedido.estado === 'entregado'" class="w-full bg-green-900 text-white text-center font-black py-3 rounded-xl">Entregado</div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>

<style scoped>
.toast-fixed {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    color: white;
    font-weight: 800;
    font-size: 14px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
    animation: slideIn 0.3s ease-out;
}
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>