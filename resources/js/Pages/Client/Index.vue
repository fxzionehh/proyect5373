<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'

const props = defineProps({
    productos: Array,
    mesaActual: Object,
    puedePedir: Boolean,
})

const mostrarResumen = ref(false)
const productoSeleccionado = ref(null)

const form = useForm({
    tipo_entrega: 'presencial',
    mesa_id: props.mesaActual?.id ?? '',
    nombre_cliente: '',
    productos: []
})

const seleccionarProducto = (producto) => {
    if (!props.puedePedir) {
        return
    }

    productoSeleccionado.value = producto

    form.productos = [
        {
            id: producto.id,
            cantidad: 1
        }
    ]

    mostrarResumen.value = true
}

const cancelarResumen = () => {
    mostrarResumen.value = false

    setTimeout(() => {
        productoSeleccionado.value = null
        form.productos = []
        form.nombre_cliente = ''
        form.clearErrors()
    }, 200)
}

const confirmarPedido = () => {
    console.log(form.data())

    form.post('/pedidos', {
        onSuccess: () => {
            form.reset()
            cancelarResumen()
            alert('¡Pedido recibido! Lo estamos preparando.')
        },
        onError: (errors) => {
            console.log(errors)
        }
    })
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 text-gray-800 pb-16">
        <navSito />

        <main class="max-w-4xl mx-auto px-4 py-8">
            <header class="mb-8">
                <h1 class="text-3xl font-bold mt-3">
                    Menú
                </h1>

                <div class="text-gray-500 mt-1">
                    {{ mesaActual ? `Mesa ${mesaActual.numero}` : 'Catálogo de productos' }}
                </div>

                <div
                    v-if="!puedePedir"
                    class="mt-4 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-lg px-4 py-3 text-sm"
                >
                    Este catálogo es solo para visualizar productos. Para realizar un pedido debes escanear el QR de una mesa.
                </div>
            </header>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div
                    v-for="p in productos"
                    :key="p.id"
                    class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm"
                >
                    <div class="flex justify-between gap-4">
                        <div>
                            <h2 class="font-bold text-lg">
                                {{ p.nombre }}
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Stock: {{ p.stock }}
                            </p>
                        </div>

                        <p class="font-bold text-yellow-700">
                            ${{ Number(p.precio).toLocaleString('es-CL') }}
                        </p>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button
                            v-if="puedePedir"
                            @click="seleccionarProducto(p)"
                            :disabled="p.stock <= 0"
                            class="px-4 py-2 rounded-lg text-sm font-bold transition"
                            :class="p.stock > 0
                                ? 'bg-yellow-400 text-gray-900 hover:bg-yellow-500'
                                : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                        >
                            {{ p.stock > 0 ? 'Pedir' : 'Agotado' }}
                        </button>

                        <span
                            v-else
                            class="text-sm text-gray-400 font-semibold"
                        >
                            Solo visualización
                        </span>
                    </div>
                </div>
            </div>
        </main>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <section
                v-if="mostrarResumen"
                class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center px-4"
            >
                <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center border-b pb-3 mb-4">
                        <h2 class="text-xl font-bold">
                            Confirmar pedido
                        </h2>

                        <button
                            @click="cancelarResumen"
                            class="text-gray-400 hover:text-gray-700 text-2xl leading-none"
                        >
                            &times;
                        </button>
                    </div>

                    <div class="border rounded-lg p-4 mb-4 bg-gray-50">
                        <div class="flex justify-between">
                            <span class="font-semibold">
                                {{ productoSeleccionado?.nombre }}
                            </span>

                            <span class="font-bold text-yellow-700">
                                ${{ Number(productoSeleccionado?.precio).toLocaleString('es-CL') }}
                            </span>
                        </div>

                        <p class="text-sm text-gray-500 mt-1">
                            Cantidad: 1
                        </p>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-gray-100 rounded-lg px-3 py-2 text-sm">
                            Pedido asociado a Mesa {{ mesaActual?.numero }}
                        </div>

                        <p v-if="form.errors.mesa_id" class="text-red-500 text-xs mt-1">
                            {{ form.errors.mesa_id }}
                        </p>

                        <div>
                            <label class="block text-sm font-semibold mb-1">
                                Nombre
                            </label>

                            <input
                                v-model="form.nombre_cliente"
                                type="text"
                                placeholder="Ej: Sebastián"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-yellow-400"
                            >

                            <p v-if="form.errors.nombre_cliente" class="text-red-500 text-xs mt-1">
                                {{ form.errors.nombre_cliente }}
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button
                            @click="cancelarResumen"
                            class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200"
                        >
                            Cancelar
                        </button>

                        <button
                            @click="confirmarPedido"
                            :disabled="form.processing"
                            class="px-4 py-2 rounded-lg bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-500 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Procesando...' : 'Confirmar' }}
                        </button>
                    </div>
                </div>
            </section>
        </Transition>
    </div>
</template>