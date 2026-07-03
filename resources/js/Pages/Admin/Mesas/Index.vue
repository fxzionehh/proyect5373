<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import QrcodeVue from 'qrcode.vue'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({ mesas: Array })

const modalAbierto = ref(false)
const modalQrAbierto = ref(false)
const mesaSeleccionada = ref(null)

const urlBase = window.location.origin


const form = useForm({
    id: null,
    numero: '',
})


const limpiar = () => {
    form.reset()
    form.clearErrors()
}

const abrirCrear = () => {
    limpiar()
    modalAbierto.value = true
}

const abrirQr = (mesa) => {
    mesaSeleccionada.value = mesa
    modalQrAbierto.value = true
}

const cerrarModal = () => {
    modalAbierto.value = false
    limpiar()
}

const guardar = () => {
    form.post('/dashboard/mesas', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => cerrarModal()
    })
}

const eliminar = (id) => {
    if (confirm('¿Eliminar esta mesa?')) {
        router.delete(`/dashboard/mesas/${id}`, { preserveScroll: true })
    }
}
</script>

<template>
    <navSito />

    <div class="min-h-screen bg-zinc-100/90 md:flex">
        <AppLayout />

        <main class="flex-1 w-full p-8">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
                <h1 class="text-3xl font-black text-zinc-900">Mesas</h1>
                <button @click="abrirCrear" class="w-full sm:w-auto bg-amber-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-amber-600 transition">
                    + Nueva Mesa
                </button>
            </div>

            <section class="w-full bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <table class="w-full text-sm">
                    <thead class="bg-zinc-900 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left">Mesa</th>
                            <th class="px-6 py-4 text-left">Estado</th>
                            <th class="px-6 py-4 text-left">Código QR</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        <tr v-for="mesa in mesas" :key="mesa.id" class="hover:bg-zinc-50 transition">
                            <td class="px-6 py-4 font-semibold">Mesa {{ mesa.numero }}</td>
                            <td class="px-6 py-4">
                                <span class="font-semibold" :class="mesa.estado === 'libre' ? 'text-green-600' : 'text-red-600'">
                                    {{ mesa.estado === 'libre' ? 'Libre' : 'Ocupada' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button @click="abrirQr(mesa)" class="bg-zinc-800 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-zinc-700">Ver QR</button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <button @click="eliminar(mesa.id)" class="bg-red-500 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-red-600">Eliminar</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <div v-if="modalAbierto" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-md rounded-xl shadow-2xl overflow-hidden">
            <div class="bg-zinc-900 text-white px-5 py-4 flex justify-between items-center">
                <h2 class="font-bold text-lg">Crear Mesa</h2>
                <button @click="cerrarModal" class="text-white/70 hover:text-white">✕</button>
            </div>

            <form @submit.prevent="guardar" class="p-6 space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-zinc-500 uppercase">Número de mesa</label>
                    <input v-model="form.numero" type="number" class="w-full mt-1 border rounded-lg p-2.5 text-sm outline-none focus:ring-1 focus:ring-amber-500" :class="{'border-red-500': form.errors.numero}" />
                    <p v-if="form.errors.numero" class="text-xs text-red-500 mt-1">{{ form.errors.numero }}</p>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" @click="cerrarModal" class="px-4 py-2 border rounded-lg text-sm hover:bg-gray-100">Cancelar</button>
                    <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-amber-500 text-white rounded-lg text-sm font-semibold hover:bg-amber-600 disabled:opacity-50">
                        {{ form.processing ? 'Guardando...' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div v-if="modalQrAbierto" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
        <div class="bg-white p-8 rounded-xl text-center w-full max-w-sm">
            <h2 class="font-bold text-xl mb-6">Mesa {{ mesaSeleccionada?.numero }}</h2>
            <div class="flex justify-center p-4 border rounded-lg">
                <qrcode-vue v-if="mesaSeleccionada" :value="`${urlBase}/mesa/${mesaSeleccionada.id}?token=${mesaSeleccionada.qr_token}`" :size="200" level="H" />
            </div>
            <button @click="modalQrAbierto = false" class="mt-6 px-6 py-2 bg-zinc-200 rounded-lg w-full font-semibold hover:bg-zinc-300">Cerrar</button>
        </div>
    </div>
</template>