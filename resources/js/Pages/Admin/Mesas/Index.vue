<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import QrcodeVue from 'qrcode.vue'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ mesas: Array })

const modalAbierto = ref(false)
const modalQrAbierto = ref(false)
const mesaSeleccionada = ref(null)

const urlBase = window.location.origin

const form = useForm({
    id: null,
    numero: '',
})

const abrirCrear = () => {
    form.reset()
    modalAbierto.value = true
}

const abrirQr = (mesa) => {
    mesaSeleccionada.value = mesa
    modalQrAbierto.value = true
}

const abrirMesa = (mesa) => {
    window.location.href = `/mesa/${mesa.id}?token=${mesa.qr_token}`
}

const guardar = () => {
    form.post('/dashboard/mesas', {
        onSuccess: () => {
            modalAbierto.value = false
            form.reset()
        }
    })
}

const eliminar = (id) => {
    if (confirm('¿Eliminar mesa?')) {
        router.delete(`/dashboard/mesas/${id}`)
    }
}
</script>

<template>
<navSito />

<div class="min-h-screen bg-zinc-100/90 md:flex">
    <AppLayout />

    <main class="p-4 md:p-10 w-full">

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-3xl font-black text-zinc-900">Mesas</h1>

            <button
                @click="abrirCrear"
                class="w-full sm:w-auto bg-amber-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-amber-600 transition"
            >
                + Nueva Mesa
            </button>
        </div>

        <!-- GRID RESPONSIVO -->
        <!-- 1 col móvil, 2 col tablet, 3 col laptop, 4 col desktop -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

            <div
                v-for="mesa in mesas"
                :key="mesa.id"
                class="bg-white rounded-2xl border shadow-sm p-5 hover:shadow-lg transition"
            >
                <h2 class="font-black text-lg">Mesa {{ mesa.numero }}</h2>
                
                <p class="text-sm font-bold mt-2 mb-4" 
                   :class="mesa.estado === 'libre' ? 'text-green-600' : 'text-red-600'">
                    {{ mesa.estado === 'libre' ? '● Libre' : '● Ocupada' }}
                </p>

                <div class="flex gap-2">
                    <button @click="abrirQr(mesa)" class="bg-zinc-800 text-white px-3 py-1 rounded-lg text-sm hover:bg-zinc-700">QR</button>
                    <button @click="eliminar(mesa.id)" class="text-red-500 text-sm font-semibold hover:text-red-700">Eliminar</button>
                </div>

                <button
                    @click="abrirMesa(mesa)"
                    class="mt-4 w-full bg-black text-white py-2 rounded-lg font-semibold hover:bg-zinc-800 transition"
                >
                    Ir a la mesa
                </button>
            </div>
        </div>

        <!-- MODAL QR -->
        <div v-if="modalQrAbierto" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
            <div class="bg-white p-6 md:p-8 rounded-2xl text-center w-full max-w-sm">
                <h2 class="font-black text-xl mb-4">Mesa {{ mesaSeleccionada?.numero }}</h2>
                
                <div class="flex justify-center">
                    <qrcode-vue
                        :value="`${urlBase}/mesa/${mesaSeleccionada.id}?token=${mesaSeleccionada.qr_token}`"
                        :size="200"
                        level="H"
                        class="w-full h-auto"
                    />
                </div>

                <button
                    @click="modalQrAbierto = false"
                    class="mt-6 px-6 py-2 bg-zinc-200 rounded-lg w-full font-semibold"
                >
                    Cerrar
                </button>
            </div>
        </div>

        <!-- MODAL CREAR -->
        <div v-if="modalAbierto" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden">
                <div class="bg-zinc-900 text-white px-5 py-4 flex justify-between items-center">
                    <h2 class="font-bold text-lg">Nueva mesa</h2>
                    <button @click="modalAbierto = false" class="text-white/70 hover:text-white">✕</button>
                </div>

                <form @submit.prevent="guardar" class="p-6 space-y-5">
                    <div>
                        <label class="text-[10px] font-bold text-zinc-500 uppercase">Número de mesa</label>
                        <input
                            v-model="form.numero"
                            type="number"
                            placeholder="Ej: 5"
                            class="w-full mt-1 border rounded-xl p-3 text-sm focus:ring-2 focus:ring-amber-500 outline-none"
                        />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="modalAbierto = false" class="px-4 py-2 border rounded-xl text-sm hover:bg-gray-100">Cancelar</button>
                        <button type="submit" class="px-5 py-2 bg-amber-500 text-white rounded-xl text-sm font-semibold hover:bg-amber-600">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</template>