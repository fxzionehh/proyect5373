<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import QrcodeVue from 'qrcode.vue'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ mesas: Array })

const modalAbierto = ref(false)
const modalQrAbierto = ref(false)
const editando = ref(false)
const mesaSeleccionada = ref(null)
const urlBase = window.location.origin

const form = useForm({
    id: null,
    numero: '',
    activa: 1
})

const abrirCrear = () => {
    editando.value = false
    form.reset()
    modalAbierto.value = true
}

const abrirQr = (mesa) => {
    mesaSeleccionada.value = mesa
    modalQrAbierto.value = true
}

// Lógica para cambiar estado rápidamente
const cambiarEstado = (mesa) => {
    const formEstado = useForm({
        id: mesa.id,
        numero: mesa.numero,
        activa: mesa.activa == 1 ? 2 : 1 // Alterna entre 1 y 2
    });
    formEstado.post('/dashboard/mesas', { preserveScroll: true });
}

const guardar = () => {
    form.post('/dashboard/mesas', {
        onSuccess: () => modalAbierto.value = false
    })
}

const eliminar = (id) => {
    if (confirm('¿Seguro que deseas eliminar esta mesa?')) router.delete(`/dashboard/mesas/${id}`)
}
</script>

<template>
    <navSito />
    <div class="flex min-h-screen bg-zinc-100/90">
        <AppLayout />
        <main class="p-10 w-full">
            <div class="flex justify-between mb-8">
                <h1 class="text-2xl font-bold">Gestión de Mesas</h1>
                <button @click="abrirCrear" class="bg-amber-500 text-white px-4 py-2 rounded-lg">+ Nueva Mesa</button>
            </div>

            <div class="grid grid-cols-4 gap-4">
                <div v-for="mesa in mesas" :key="mesa.id" class="p-4 bg-white rounded-xl border shadow-sm">
                    <h2 class="font-bold text-lg">Mesa {{ mesa.numero }}</h2>
                    <p class="text-sm mb-4 font-semibold" :class="mesa.activa == 1 ? 'text-green-600' : 'text-red-600'">
                        {{ mesa.activa == 1 ? '● Libre' : '● Ocupada' }}
                    </p>
                    
                    <div class="flex gap-2">
                        <button @click="cambiarEstado(mesa)" class="px-3 py-1 rounded text-sm bg-zinc-200 hover:bg-zinc-300">
                            {{ mesa.activa == 1 ? 'Ocupar' : 'Liberar' }}
                        </button>
                        <button @click="abrirQr(mesa)" class="bg-zinc-800 text-white px-3 py-1 rounded text-sm">QR</button>
                        <button @click="eliminar(mesa.id)" class="text-red-500 text-sm">X</button>
                    </div>
                </div>
            </div>

            <div v-if="modalQrAbierto" class="fixed inset-0 bg-black/50 flex items-center justify-center">
                <div class="bg-white p-8 rounded-2xl text-center">
                    <h2 class="font-bold text-xl mb-4">Mesa {{ mesaSeleccionada.numero }}</h2>
                    <qrcode-vue 
                        :value="`${urlBase}/pedido/mesa/${mesaSeleccionada.id}`" 
                        :size="250" 
                        level="H" 
                    />
                    <button @click="modalQrAbierto = false" class="mt-6 px-6 py-2 bg-zinc-200 rounded w-full">Cerrar</button>
                </div>
            </div>
        </main>
    </div>
</template>