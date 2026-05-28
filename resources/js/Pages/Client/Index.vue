<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'

const props = defineProps({
    productos: Array,
    mesaActual: Object,
    puedePedir: Boolean,
    pedidoActual: Object,
})

console.log(props)
let tokenCliente = localStorage.getItem('token_cliente')

if (!tokenCliente) {
    tokenCliente = crypto.randomUUID()
    localStorage.setItem('token_cliente', tokenCliente)
}

const mostrarModal = ref(false)
const productoSeleccionado = ref(null)
const toast = ref(null)

const form = useForm({
    tipo_entrega: 'presencial',
    mesa_id: props.mesaActual?.id ?? '',
    nombre_cliente: '',
    token_cliente: tokenCliente,
    productos: [], // Aquí meteremos el objeto completo con su tamaño
    tamano: 'normal', // Valor por defecto inicial para el select de la interfaz
})

const estadoTexto = {
    pendiente: 'Pedido recibido',
    en_preparacion: 'Preparando tu pedido',
    listo: 'Listo para retiro',
}

const mostrarToast = (mensaje) => {
    toast.value = mensaje
    setTimeout(() => {
        toast.value = null
    }, 3000)
}

const abrirModalPedido = (producto) => {
    if (props.pedidoActual) {
        mostrarToast('Ya tienes un pedido activo')
        return
    }

    if (!props.puedePedir || producto.stock <= 0) return

    productoSeleccionado.value = producto
    form.tamano = 'normal' // Reseteamos al tamaño estándar cada vez que abre el modal
    mostrarModal.value = true
}

const cerrarModalPedido = () => {
    mostrarModal.value = false
    productoSeleccionado.value = null
    form.productos = []
}

const confirmarPedido = () => {
    // CAPA DE SEGURIDAD: Inyectamos el tamaño elegido dentro del array de productos
    form.productos = [
        {
            id: productoSeleccionado.value.id,
            cantidad: 1,
            tamano: form.tamano // Aquí acoplamos el tamaño real (nano, mini, normal, max)
        }
    ]

    console.log(form)

    form.post('/pedidos', {
        preserveScroll: true,
        onSuccess: () => {
            cerrarModalPedido()
            window.location.reload()
        },
        onError: (errors) => {
            if (errors.pedido) {
                mostrarToast(errors.pedido)
            } else {
                mostrarToast('No se pudo crear el pedido')
            }
            console.log(errors)
        },
    })
}
</script>
<template>
    <div class="h-auto bg-white w-full">

        <navSito />


        <section class="flex justify-between bg-gradient-to-br from-zinc-950 to-zinc-900 px-8 py-10">
            <h1 class="text-4xl font-black text-white">
                Menú
            </h1>


        </section>

        <main class="max-w-7xl mx-auto px-4 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8 items-start">


                <section>
                    <div class="mb-6">


                        <h1 class="text-3xl font-black text-zinc-950">

                            {{
                                props.mesaActual?.numero
                                    ? `Mesa ${props.mesaActual.numero}`
                                    : 'Catálogo'
                            }}

                        </h1>

                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        <article v-for="p in productos" :key="p.id"
                            class="bg-white rounded-3xl p-5 shadow-sm border border-zinc-200 hover:shadow-md transition-all">
                            <div>
                                <div class="flex justify-between items-start gap-3">
                                    <div>
                                        <h2 class="text-lg font-black text-zinc-900">
                                            {{ p.nombre }}
                                        </h2>

                                        <p class="text-sm font-black text-zinc-500 mt-1">
                                            ${{ Number(p.precio).toLocaleString('es-CL') }}
                                        </p>
                                    </div>

                                    <span class="text-[10px] font-black px-2 py-1 rounded-full" :class="p.stock > 0
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-red-100 text-red-700'">

                                        {{ p.stock > 0 ? 'Disponible' : 'Agotado' }}

                                    </span>
                                </div>

                                <button v-if="puedePedir && !props.pedidoActual" type="button"
                                    @click.stop="abrirModalPedido(p)" :disabled="p.stock <= 0"
                                    class="w-full mt-5 py-3 px-4 rounded-2xl text-sm font-black transition-all disabled:opacity-40"
                                    :class="p.stock > 0
                                        ? 'bg-zinc-950 text-white hover:bg-sky-500'
                                        : 'bg-zinc-200 text-zinc-400'">

                                    {{ p.stock > 0 ? 'Pedir' : 'Agotado' }}

                                </button>


                            </div>
                        </article>
                    </div>
                </section>


                <aside class="lg:sticky lg:top-6">
                    <div class="bg-white border border-zinc-200 rounded-1xl shadow-sm overflow-hidden">

                        <div class="bg-zinc-950 text-white p-5">


                            <h2 class="text-xl font-black">
                                Tu pedido
                            </h2>
                        </div>

                        <div v-if="props.pedidoActual" class="p-5">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-zinc-500">
                                        Estado actual
                                    </p>

                                    <p class="text-lg font-black text-zinc-950">
                                        {{ estadoTexto[props.pedidoActual.estado] }}
                                    </p>
                                </div>


                            </div>

                            <div class="mt-5 space-y-3">

                                <div v-for="detalle in props.pedidoActual.detalles" :key="detalle.id"
                                    class="flex justify-between items-start border-b border-zinc-100 pb-3">

                                    <div>

                                        <p class="text-sm font-black text-zinc-900">
                                            {{ detalle.producto.nombre }}
                                        </p>

                                        <p v-if="detalle.tamano" class="text-xs text-zinc-500 capitalize mt-1">
                                            {{ detalle.tamano }}
                                        </p>

                                        <p class="text-xs text-zinc-400 mt-1">
                                            Cantidad: x{{ detalle.cantidad }}
                                        </p>

                                    </div>

                                    <div class="text-right">

                                        <p class="text-sm font-black text-zinc-950">
                                            ${{ Number(detalle.subtotal).toLocaleString('es-CL') }}
                                        </p>

                                    </div>

                                </div>

                                <!-- TOTAL -->
                                <div class="flex justify-between items-center pt-4">

                                    <p class="text-sm font-black text-zinc-500">
                                        Total
                                    </p>

                                    <p class="text-xl font-black text-zinc-950">
                                        ${{
                                            Number(props.pedidoActual.total).toLocaleString('es-CL')
                                        }}
                                    </p>

                                </div>

                            </div>
                        </div>

                        <div v-else class="p-5">
                            <div class="bg-zinc-100 rounded-2xl p-5 text-center">
                                <p class="text-sm font-bold text-zinc-700">
                                    No hay pedidos registrados.
                                </p>
                            </div>
                        </div>

                    </div>
                </aside>

            </div>
        </main>



    </div>

    <div v-if="mostrarModal" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center px-4">
        <div class="bg-white w-full max-w-md rounded-3xl p-6 shadow-2xl">
            <h2 class="text-2xl font-black text-zinc-950">
                Crear Pedido
            </h2>

            <p class="mt-3 text-zinc-600">
                Producto:
                <strong>{{ productoSeleccionado?.nombre }}</strong>
            </p>

            <p class="mt-1 text-zinc-600">
                Precio:
                <strong>
                    ${{ Number(productoSeleccionado?.precio).toLocaleString('es-CL') }}
                </strong>
            </p>


          <p class="mt-3 font-bold text-zinc-700 text-sm">
    Selecciona el Tamaño:
</p>

<select v-model="form.tamano" class="mt-2 w-full rounded-2xl border border-zinc-300 px-4 py-3 bg-white text-zinc-900 font-medium focus:outline-none focus:ring-2 focus:ring-zinc-950">
    <option value="nano">Nano (120ml)</option>
    <option value="mini">Mini (240ml)</option>
    <option value="normal">Normal (360ml)</option>
    <option value="max">Max (480ml)</option>
</select>

            <div class="mt-5">
                <label class="text-sm font-bold text-zinc-700">
                    Nombre:
                </label>

                <input v-model="form.nombre_cliente" type="text"
                    class="mt-2 w-full rounded-2xl border border-zinc-300 px-4 py-3">
            </div>

            <div class="mt-6 flex gap-3">
                <button type="button" @click="cerrarModalPedido" class="w-full py-3 rounded-2xl bg-zinc-200 font-black">
                    Cancelar
                </button>

                <button type="button" @click="confirmarPedido" :disabled="form.processing"
                    class="w-full py-3 rounded-2xl bg-zinc-950 text-white font-black disabled:opacity-50">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
</template>