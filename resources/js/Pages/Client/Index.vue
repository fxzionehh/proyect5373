<script setup>
import { ref, computed, watch, onUnmounted } from 'vue'
import axios from 'axios'
import { useForm } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'

const props = defineProps({
    productos: Array,
    mesaActual: Object,
    pedidoActual: Object,
    puedePedir: Boolean,
})

/**
 * =========================
 * ESTADO SIMPLE DEL PEDIDO
 * =========================
 */
const estado = ref(null)
const pedidoId = ref(null)

let intervalo = null

const estadoTexto = {
    pendiente: 'Pedido solicitado',
    en_preparacion: 'Preparando tu cafe',
    listo: 'Listo',
    entregado: 'Entregado',
}

/**
 * =========================
 * PASOS UI
 * =========================
 */
const paso = ref(1)
const productoSeleccionado = ref(null)

/**
 * =========================
 * FORM INERTIA
 * =========================
 */
const mesaId = computed(() => props.mesaActual?.id ?? null)

const form = useForm({
    mesa_id: mesaId.value,
    nombre_cliente: '',
    producto_id: null,
    tamano: null,
})

/**
 * =========================
 * PRECIO
 * =========================
 */
const precioSeleccionado = computed(() => {
    if (!productoSeleccionado.value) return 0
    return productoSeleccionado.value[`precio_${form.tamano}`] || 0
})

/**
 * =========================
 * POLLING SOLO ESTADO
 * =========================
 */
const iniciarPolling = (id) => {
    if (!id) return

    pedidoId.value = id

    if (intervalo) clearInterval(intervalo)

    intervalo = setInterval(async () => {
        try {
            const res = await axios.get(`/pedidos/${id}/estado`)
            estado.value = res.data.estado
        } catch (err) {
            console.log('error polling estado', err)
        }
    }, 3000)
}

/**
 * =========================
 * WATCH PEDIDO INICIAL
 * =========================
 */
watch(
    () => props.pedidoActual,
    (nuevo) => {
        if (nuevo?.id) {
            pedidoId.value = nuevo.id
            estado.value = nuevo.estado
            iniciarPolling(nuevo.id)
        } else {
            estado.value = null
            if (intervalo) clearInterval(intervalo)
        }
    },
    { immediate: true }
)

/**
 * =========================
 * CLEANUP
 * =========================
 */
onUnmounted(() => {
    if (intervalo) clearInterval(intervalo)
})

/**
 * =========================
 * ACCIONES UI
 * =========================
 */
const seleccionarProducto = (p) => {
    if (estado.value || !props.puedePedir || p.stock <= 0) return

    productoSeleccionado.value = p
    form.producto_id = p.id
    paso.value = 2
}

const irNombre = () => {
    paso.value = 3
}

const volver = () => {
    paso.value--
}

/**
 * =========================
 * CREAR PEDIDO
 * =========================
 */
const confirmar = () => {
    if (!form.nombre_cliente || !form.tamano) return

    form.post('/pedidos', {
        preserveScroll: true,
        onSuccess: (page) => {
            const nuevo = page.props?.pedidoActual

            if (nuevo?.id) {
                pedidoId.value = nuevo.id
                estado.value = nuevo.estado
                iniciarPolling(nuevo.id)
            }

            paso.value = 1
            productoSeleccionado.value = null
            form.reset()
            form.tamano = null
            form.producto_id = null
        }
    })
}
</script>

<template>
    <div class="min-h-screen bg-black text-white font-sans">
        <navSito />

        <!-- MESA NO ENCONTRADA -->
        <div v-if="!mesaActual" class="flex items-center justify-center min-h-[80vh]">
            <h2 class="text-3xl font-black">Mesa no encontrada</h2>
        </div>

        <div v-else class="max-w-2xl mx-auto pb-10">

            <!-- HEADER -->
            <header class="p-6 border-b border-zinc-800 flex justify-between items-center">
                <div>
                    <p class="text-[10px] text-zinc-500 uppercase">Estás en</p>
                    <h1 class="text-2xl font-black">MESA {{ mesaActual.numero }}</h1>
                </div>

                <div v-if="estado"
                    class="px-4 py-1 bg-amber-500 text-black rounded-full text-xs font-black uppercase">
                    {{ estadoTexto[estado] }}
                </div>
            </header>

            <!-- PASO 1 -->
            <div v-if="paso === 1" class="p-6">
                <h2 class="text-xl font-black mb-6">
                    {{ estado ? 'Esperando pedido...' : 'Selecciona tu café' }}
                </h2>

                <div class="grid grid-cols-2 gap-4">
                    <button
                        v-for="p in productos"
                        :key="p.id"
                        @click="seleccionarProducto(p)"
                        :disabled="estado !== null"
                        class="p-4 rounded-xl border bg-zinc-900 disabled:opacity-40"
                    >
                        <h3 class="font-bold uppercase">{{ p.nombre }}</h3>
                        <p class="text-xs text-zinc-500">
                            {{ p.stock > 0 ? 'Disponible' : 'Agotado' }}
                        </p>
                    </button>
                </div>
            </div>

            <!-- PASO 2 -->
            <div v-else-if="paso === 2" class="p-6">
                <button @click="volver" class="mb-4 text-zinc-400">← Volver</button>

                <h2 class="text-2xl font-black mb-4">
                    {{ productoSeleccionado.nombre }}
                </h2>

                <div class="grid grid-cols-2 gap-3">
                    <button
                        v-for="t in ['nano','mini','normal','max']"
                        :key="t"
                        @click="form.tamano = t"
                        :class="form.tamano === t ? 'bg-amber-500 text-black' : 'bg-zinc-900'"
                        class="p-4 rounded-xl font-bold uppercase"
                    >
                        {{ t }}
                        <div class="text-xs">
                            ${{ Number(productoSeleccionado[`precio_${t}`]).toLocaleString('es-CL') }}
                        </div>
                    </button>
                </div>

                <div class="mt-6 text-xl font-black">
                    Total: ${{ Number(precioSeleccionado).toLocaleString('es-CL') }}
                </div>

                <button
                    @click="irNombre"
                    class="mt-6 w-full bg-white text-black py-4 font-black"
                >
                    Continuar
                </button>
            </div>

            <!-- PASO 3 -->
            <div v-else class="p-6">
                <button @click="volver" class="text-zinc-400 mb-4">← Volver</button>

                <h2 class="text-3xl font-black mb-6">¿Quién pide?</h2>

                <input
                    v-model="form.nombre_cliente"
                    class="w-full p-4 bg-zinc-900 border rounded-xl mb-4"
                    placeholder="Nombre..."
                />

                <button
                    @click="confirmar"
                    :disabled="form.processing"
                    class="w-full bg-amber-500 text-black py-4 font-black disabled:opacity-50"
                >
                    {{ form.processing ? 'Enviando...' : 'Pedir' }}
                </button>
            </div>
        </div>
    </div>
</template>