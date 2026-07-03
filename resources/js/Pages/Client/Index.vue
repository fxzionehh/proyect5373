<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { useForm, usePage } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'

const page = usePage()

let intervalo = null
let pollingActivo = false

const props = defineProps({
    productos: Array,
    mesaActual: Object,
    pedidoActual: Object,
    puedePedir: Boolean,
})

const paso = ref(1)
const productoSeleccionado = ref(null)

// estado local del pedido
const pedidoActualLocal = ref(props.pedidoActual || null)

const estadoTexto = {
    pendiente: 'Pedido solicitado',
    en_preparacion: 'Preparando tu café',
    listo: 'Listo',
    entregado: 'Entregado',
}

// -------------------------
// POLLING
// -------------------------
const iniciarPolling = (id) => {
    if (!id) return

    if (intervalo) clearInterval(intervalo)

    intervalo = setInterval(async () => {
        try {
            const res = await axios.get(`/pedidos/${id}`)

            pedidoActualLocal.value = {
                ...pedidoActualLocal.value,
                ...res.data
            }

        } catch (err) {
            console.log('Error polling:', err.response?.status, err.response?.data)
        }
    }, 3000)
}

// -------------------------
// WATCH SIMPLE Y SEGURO
// -------------------------
onMounted(() => {
    if (pedidoActualLocal.value?.id && !pollingActivo) {
        pollingActivo = true
        iniciarPolling(pedidoActualLocal.value.id)
    }
})

onUnmounted(() => {
    if (intervalo) clearInterval(intervalo)
})

// -------------------------
// FORM
// -------------------------
const mesaId = computed(() => props.mesaActual?.id ?? null)

const form = useForm({
    mesa_id: mesaId.value,
    nombre_cliente: '',
    producto_id: null,
    tamano: null,
})

const precioSeleccionado = computed(() => {
    if (!productoSeleccionado.value || !form.tamano) return 0
    return productoSeleccionado.value[`precio_${form.tamano}`] || 0
})

// -------------------------
// ACCIONES UI
// -------------------------
const seleccionarProducto = (p) => {
    if (pedidoActualLocal.value || !props.puedePedir || p.stock <= 0) return

    productoSeleccionado.value = p
    form.producto_id = p.id
    paso.value = 2
}

const irNombre = () => {
    paso.value = 3
}

const volver = () => {
    paso.value = Math.max(1, paso.value - 1)
}

// -------------------------
// ENVIAR PEDIDO
// -------------------------
const confirmar = () => {
    if (!form.nombre_cliente || !form.tamano) return

    form.post('/pedidos', {
        preserveScroll: true,
        onSuccess: () => {

            // FIX IMPORTANTE: usar page
            pedidoActualLocal.value = page.props.pedidoActual

            // reinicio UI
            paso.value = 1
            productoSeleccionado.value = null
            form.reset()
            form.tamano = null
            form.producto_id = null

            // iniciar polling si hay pedido
            if (pedidoActualLocal.value?.id && !pollingActivo) {
                pollingActivo = true
                iniciarPolling(pedidoActualLocal.value.id)
            }
        }
    })
}
</script>

<template>
    <div class="min-h-screen bg-black text-white font-sans">
        <navSito />

        <!-- SIN MESA -->
        <div v-if="!mesaActual" class="flex items-center justify-center min-h-[80vh]">
            <h2 class="text-2xl font-black">Mesa no encontrada</h2>
        </div>

        <div v-else class="max-w-2xl mx-auto pb-10">

            <!-- HEADER -->
            <header class="p-6 border-b border-zinc-800 flex justify-between sticky top-0 bg-black z-20">
                <div>
                    <p class="text-xs text-zinc-500 uppercase">Mesa</p>
                    <h1 class="text-2xl font-black">{{ mesaActual.numero }}</h1>
                </div>

                <div v-if="pedidoActualLocal"
                    class="px-3 py-1 bg-amber-500 text-black text-xs font-black rounded-full animate-pulse">
                    {{ estadoTexto[pedidoActualLocal.estado] }}
                </div>
            </header>

            <!-- PASO 1 -->
            <div v-if="paso === 1" class="p-6">
                <h2 class="text-xl font-black mb-4">
                    {{ pedidoActualLocal ? 'Esperando pedido...' : 'Selecciona café' }}
                </h2>

                <div class="grid grid-cols-2 gap-4">
                    <button
                        v-for="p in productos"
                        :key="p.id"
                        @click="seleccionarProducto(p)"
                        :disabled="pedidoActualLocal || p.stock <= 0"
                        class="p-4 rounded-2xl border bg-zinc-900 disabled:opacity-40"
                    >
                        <h3 class="font-black uppercase">{{ p.nombre }}</h3>
                        <p class="text-xs text-zinc-400">
                            {{ p.stock > 0 ? 'Disponible' : 'Agotado' }}
                        </p>
                    </button>
                </div>
            </div>

            <!-- PASO 2 -->
            <div v-else-if="paso === 2" class="p-6">
                <button @click="volver" class="mb-4 text-zinc-400">← Volver</button>

                <h2 class="text-xl font-black mb-4">
                    {{ productoSeleccionado.nombre }}
                </h2>

                <div class="grid grid-cols-2 gap-3">
                    <button
                        v-for="t in ['nano','mini','normal','max']"
                        :key="t"
                        @click="form.tamano = t"
                        :class="form.tamano === t ? 'bg-amber-500 text-black' : 'bg-zinc-900'"
                        class="p-3 rounded-xl font-black uppercase"
                    >
                        {{ t }}
                        <div class="text-xs">
                            ${{ Number(productoSeleccionado[`precio_${t}`]).toLocaleString('es-CL') }}
                        </div>
                    </button>
                </div>

                <div class="mt-6 flex justify-between font-black">
                    <span>Total</span>
                    <span>${{ precioSeleccionado.toLocaleString('es-CL') }}</span>
                </div>

                <button
                    @click="irNombre"
                    class="w-full mt-6 bg-white text-black py-4 font-black"
                >
                    Continuar
                </button>
            </div>

            <!-- PASO 3 -->
            <div v-else class="p-6">
                <button @click="volver" class="mb-4 text-zinc-400">← Volver</button>

                <h2 class="text-2xl font-black mb-4">Tu nombre</h2>

                <input
                    v-model="form.nombre_cliente"
                    class="w-full p-4 bg-zinc-900 rounded-xl mb-4"
                    placeholder="Nombre..."
                />

                <button
                    @click="confirmar"
                    :disabled="form.processing"
                    class="w-full bg-amber-500 text-black py-4 font-black"
                >
                    {{ form.processing ? 'Enviando...' : 'Confirmar pedido' }}
                </button>
            </div>

            <!-- RESUMEN -->
            <div v-if="pedidoActualLocal" class="p-6">
                <div class="bg-zinc-900 p-4 rounded-xl">
                    <h3 class="font-black mb-2">Resumen</h3>

                    <p class="text-sm text-zinc-400">
                        Cliente: {{ pedidoActualLocal.nombre_cliente }}
                    </p>

                    <div v-for="d in pedidoActualLocal.detalles" :key="d.id" class="mt-2 text-sm">
                        {{ d.producto?.nombre }} - {{ d.tamano }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>