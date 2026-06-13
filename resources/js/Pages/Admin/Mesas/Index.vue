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
    activa: true
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

const cambiarEstado = (mesa) => {
    router.post('/dashboard/mesas', {
        id: mesa.id,
        numero: mesa.numero,
        estado: mesa.estado === 'libre' ? 'ocupada' : 'libre'
    }, {
        preserveScroll: true
    })
}

const guardar = () => {
    form.post('/dashboard/mesas', {
        onSuccess: () => {
            modalAbierto.value = false
            form.reset()
        },
        onError: (errors) => {
            console.log('ERRORES:', errors)
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

<div class="flex min-h-screen bg-zinc-100/90">
<AppLayout />

<main class="p-10 w-full">

<!-- HEADER -->
<div class="flex justify-between mb-8">
    <h1 class="text-3xl font-black text-zinc-900">Mesas</h1>

    <button
        @click="abrirCrear"
        class="bg-amber-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-amber-600"
    >
        + Nueva Mesa
    </button>
</div>

<!-- GRID -->
<div class="grid grid-cols-4 gap-4">

    <div
        v-for="mesa in mesas"
        :key="mesa.id"
        class="bg-white rounded-2xl border shadow-sm p-5 hover:shadow-lg transition"
    >

        <!-- TITULO -->
        <h2 class="font-black text-lg">
            Mesa {{ mesa.numero }}
        </h2>

        <!-- ESTADO -->
        <p
            class="text-sm font-bold mt-2 mb-4"
            :class="mesa.estado === 'libre' ? 'text-green-600' : 'text-red-600'"
        >
            {{ mesa.estado === 'libre' ? '● Libre' : '● Ocupada' }}
        </p>

        <!-- BOTONES -->
        <div class="flex gap-2">

            
            <button
                @click="abrirQr(mesa)"
                class="bg-zinc-800 text-white px-3 py-1 rounded-lg text-sm"
            >
                QR
            </button>

            <button
                @click="eliminar(mesa.id)"
                class="text-red-500 text-sm font-semibold"
            >
                Eliminar
            </button>

        </div>

        <button
            @click="abrirMesa(mesa)"
            class="mt-4 w-full bg-black text-white py-2 rounded-lg font-semibold"
        >
            Ir a la mesa
        </button>

    </div>
</div>

<!-- MODAL QR -->
<div v-if="modalQrAbierto"
class="fixed inset-0 bg-black/50 flex items-center justify-center">

<div class="bg-white p-8 rounded-2xl text-center w-[350px]">

    <h2 class="font-black text-xl mb-4">
        Mesa {{ mesaSeleccionada?.numero }}
    </h2>

    <qrcode-vue
        :value="`${urlBase}/mesa/${mesaSeleccionada.id}?token=${mesaSeleccionada.qr_token}`"
        :size="220"
        level="H"
    />

    <button
        @click="modalQrAbierto = false"
        class="mt-6 px-6 py-2 bg-zinc-200 rounded-lg w-full font-semibold"
    >
        Cerrar
    </button>

</div>
</div>

<!-- MODAL CREAR (MEJORADO) -->
<div v-if="modalAbierto"
class="fixed inset-0 bg-black/50 flex items-center justify-center p-4">

<div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6">

    <h2 class="text-xl font-black mb-6">
        Nueva Mesa
    </h2>

    <form @submit.prevent="guardar" class="space-y-4">

        <div>
            <label class="text-xs font-bold text-zinc-500">Número de mesa</label>
            <input
                v-model="form.numero"
                type="number"
                class="w-full border rounded-xl p-3 mt-1 focus:ring-2 focus:ring-amber-500"
                placeholder="Ej: 5"
            />
        </div>

        <div class="flex justify-end gap-2 pt-4">

            <button
                type="button"
                @click="modalAbierto = false"
                class="px-5 py-2 rounded-xl text-sm font-semibold text-zinc-600 hover:bg-zinc-100"
            >
                Cancelar
            </button>

            <button
                type="submit"
                class="px-5 py-2 bg-amber-500 text-white rounded-xl font-semibold hover:bg-amber-600"
            >
                Guardar
            </button>

        </div>

    </form>

</div>
</div>

</main>
</div>
</template>