<script setup>
import { onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
let intervalo = null
const props = defineProps({
    productos: Array,
    mesaActual: Object,
    pedidoActual: Object,
    puedePedir: Boolean,
})

const paso = ref(1)
const productoSeleccionado = ref(null)

const estadoTexto = {
    pendiente: 'Pedido solicitado',
    en_preparacion: 'Preparando tu cafe',
    listo: 'Listo ',
}
onMounted(() => {
    if (!props.pedidoActual) return

    intervalo = setInterval(async () => {
        try {
            const res = await axios.get(`/pedidos/${props.pedidoActual.id}`)

            // actualizar SOLO el estado
            props.pedidoActual.estado = res.data.estado

            // opcional: actualizar todo el pedido
            props.pedidoActual.detalles = res.data.detalles

        } catch (err) {
            console.log('error actualizando pedido', err)
        }
    }, 3000) // cada 3 segundos
})
const mesaId = computed(() => props.mesaActual?.id ?? null)

const form = useForm({
    mesa_id: mesaId.value,
    nombre_cliente: '',
    producto_id: null,
    tamano: 'normal',
})

const precioSeleccionado = computed(() => {
    if (!productoSeleccionado.value) return 0
    return productoSeleccionado.value[`precio_${form.tamano}`] || 0
})

const seleccionarProducto = (p) => {
    if (!props.puedePedir || !mesaId.value) return
    productoSeleccionado.value = p
    form.producto_id = p.id
    paso.value = 2
}

const irNombre = () => paso.value = 3
const volver = () => paso.value--

const confirmar = () => {
    if (!mesaId.value || !form.nombre_cliente) return

    form.post('/pedidos', {
        preserveScroll: true,
        onSuccess: () => {
            paso.value = 1
            productoSeleccionado.value = null
            form.reset('nombre_cliente', 'producto_id', 'tamano')
        }
    })
}
</script>

<template>
    <div class="min-h-screen bg-black text-white font-sans selection:bg-amber-400 selection:text-black">
        <navSito />

        <div v-if="!mesaActual" class="flex flex-col items-center justify-center min-h-[80vh] text-center px-8">
            <h2 class="text-3xl font-black uppercase tracking-tighter">Mesa no encontrada</h2>
        </div>

        <div v-else class="max-w-2xl mx-auto pb-10">
            <!-- HEADER -->
            <header class="p-6 border-b border-zinc-800 flex justify-between items-center bg-black sticky top-0 z-20">
                <div>
                    <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Estás en</p>
                    <h1 class="text-2xl font-black">MESA {{ mesaActual.numero }}</h1>
                </div>

                <div v-if="pedidoActual"
                    class="px-4 py-1.5 bg-amber-500 text-black rounded-full text-[10px] font-black uppercase animate-pulse">
                    {{ estadoTexto[pedidoActual.estado] }}
                </div>
            </header>

            <!-- PASO 1 -->
            <div v-if="paso === 1" class="p-6 animate-in slide-in-from-bottom-10">
                <div class="flex justify-between items-end mb-6">
                    <h2 class="text-3xl font-black tracking-tighter uppercase italic text-zinc-500">
                        ¿Qué café quieres?
                    </h2>
                </div>

                <div class="flex gap-4 overflow-x-auto pb-8 snap-x scrollbar-hide">
                    <button
                        v-for="p in productos"
                        :key="p.id"
                        @click="seleccionarProducto(p)"
                        class="min-w-[70%] snap-center group bg-zinc-900 p-8 rounded-[2rem] text-left border-2 border-transparent hover:border-amber-500 active:scale-95"
                    >
                        <div class="w-20 h-20 bg-black/50 rounded-full flex items-center justify-center mb-8 mx-auto border border-zinc-800">
                            <i class="fa-solid fa-mug-hot text-3xl text-amber-500"></i>
                        </div>

                        <div class="text-center">
                            <h3 class="font-black text-2xl uppercase">{{ p.nombre }}</h3>
                            <span class="inline-block mt-4 px-4 py-1 bg-amber-500/10 text-amber-500 rounded-full text-[10px] font-bold uppercase">
                                Seleccionar
                            </span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- PASO 2 -->
            <div v-else-if="paso === 2" class="p-6 animate-in slide-in-from-right-10">
                <button @click="volver" class="mb-8 text-zinc-500 font-bold hover:text-white flex items-center gap-2">
                    ← VOLVER
                </button>

                <div class="mb-8 bg-zinc-900 p-6 rounded-3xl flex items-center gap-4">
                    <h2 class="text-3xl font-black uppercase italic">
                        {{ productoSeleccionado.nombre }}
                    </h2>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button
                        v-for="t in ['nano', 'mini', 'normal', 'max']"
                        :key="t"
                        @click="form.tamano = t"
                        :class="form.tamano === t
                            ? 'bg-amber-500 text-black'
                            : 'bg-zinc-900 text-white'"
                        class="rounded-3xl border-2 p-4 font-black uppercase"
                    >
                        {{ t }}

                        <div class="text-xs mt-1 opacity-70">
                            ${{ Number(productoSeleccionado[`precio_${t}`]).toLocaleString('es-CL') }}
                        </div>
                    </button>
                </div>

                <div class="mt-8 p-6 bg-zinc-900 rounded-3xl flex justify-between">
                    <span>Total</span>
                    <span class="text-2xl font-black">
                        ${{ Number(precioSeleccionado).toLocaleString('es-CL') }}
                    </span>
                </div>

                <button
                    @click="irNombre"
                    class="w-full mt-6 bg-white text-black py-6 rounded-2xl font-black uppercase"
                >
                    Continuar
                </button>
            </div>

            <!-- PASO 3 -->
            <div v-else class="p-6 animate-in slide-in-from-right-10">
                <button @click="volver" class="mb-8 text-zinc-500 font-bold">
                    ← VOLVER
                </button>

                <h2 class="text-4xl font-black mb-8 uppercase italic">
                    ¿Quién pide?
                </h2>

                <div class="bg-zinc-900 p-8 rounded-3xl">
                    <input
                        v-model="form.nombre_cliente"
                        class="w-full bg-black border-2 border-zinc-800 rounded-2xl p-6 text-xl font-bold"
                        placeholder="Escribe tu nombre..."
                    />

                    <button
                        @click="confirmar"
                        :disabled="form.processing"
                        class="w-full mt-6 bg-amber-500 text-black py-6 rounded-2xl font-black uppercase disabled:opacity-50"
                    >
                        {{ form.processing ? 'ENVIANDO...' : 'Pedir' }}
                    </button>
                </div>
            </div>

            <!-- RESUMEN SIMPLE RESTAURADO -->
            <div v-if="pedidoActual" class="p-6">
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">

                    <h2 class="text-xl font-black uppercase mb-4">
                        Resumen del pedido
                    </h2>

                    <p class="text-zinc-400 text-sm uppercase">Cliente</p>
                    <p class="text-lg font-bold mb-4">
                        {{ pedidoActual.nombre_cliente }}
                    </p>

                    <p class="text-zinc-400 text-sm uppercase mb-2">Productos</p>

                    <div
                        v-for="detalle in pedidoActual.detalles"
                        :key="detalle.id"
                        class="py-3 border-b border-zinc-800 last:border-none"
                    >
                        <div class="flex justify-between">
                            <div>
                                <p class="font-bold uppercase">
                                    {{ detalle.producto?.nombre }}
                                </p>
                                <p class="text-xs text-zinc-400 uppercase">
                                    Tamaño: {{ detalle.tamano }} · Cant: {{ detalle.cantidad }}
                                </p>
                            </div>

                            <p class="font-black text-amber-400">
                                ${{ Number(detalle.subtotal).toLocaleString('es-CL') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 p-3 rounded-xl bg-amber-500/10 border border-amber-500/30">
                        <p class="text-amber-400 font-bold uppercase text-sm">
                            {{ estadoTexto[pedidoActual.estado] }}
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>