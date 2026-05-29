<script setup>
import { ref } from 'vue'
import { Link, useForm, router, usePage } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
const page = usePage()

defineProps({
    mesas: Array,
})

// Formulario reactivo para la nueva mesa
const form = useForm({
    numero: ''
})

const crearMesa = () => {
    form.post('/dashboard/configuracion/mesas', {
        onSuccess: () => {
            form.reset() // Limpia el input si todo sale bien
        }
    })
}

const inventarioAbierto = ref(false)
const seccionActiva = ref('productos')
const roleAbierto = ref(false)

const toggleEstado = (mesaId) => {
    if (confirm('¿Cambiar estado de esta mesa?')) {
        router.patch(`/dashboard/configuracion/mesas/${mesaId}/toggle`, {}, {
            preserveScroll: true
        })
    }
}

const generarQr = (mesaId) => {
    router.post(`/dashboard/configuracion/mesas/${mesaId}/generar-qr`, {}, {
        preserveScroll: true
    })
}
</script>

<template>
    <navSito />

    <div class="flex min-h-screen bg-zinc-100/90">
        <aside class="w-52 min-h-screen bg-zinc-950 flex flex-col shadow-xl">
            </aside>

        <main class="flex-1 ">
            <div class="min-h-screen bg-zinc-100 p-8">
                
                <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-black text-zinc-900">Cartas QR</h1>
                    </div>
                    
                    <form @submit.prevent="crearMesa" class="flex items-center gap-2 bg-white p-2 rounded-xl border border-zinc-200 shadow-sm">
                        <input 
                            v-model="form.numero" 
                            type="number" 
                            placeholder="N° de Mesa" 
                            min="1"
                            required
                            class="w-28 text-sm px-3 py-2 rounded-lg border border-zinc-300 focus:outline-none focus:ring-2 focus:ring-zinc-950"
                        />
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="bg-zinc-950 text-white text-sm px-4 py-2 rounded-lg font-medium hover:bg-zinc-800 transition disabled:opacity-50"
                        >
                            + Agregar Mesa
                        </button>
                    </form>
                </div>

                <div v-if="form.errors.numero" class="mb-4 text-red-600 text-sm font-medium">
                    {{ form.errors.numero }}
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div v-for="mesa in mesas" :key="mesa.id" class="bg-white border border-zinc-200 rounded-2xl p-6">
                        <h2 class="text-2xl font-black text-zinc-900 mb-5">
                            Mesa {{ mesa.numero }}
                            <span class="text-sm font-normal text-gray-500">
                                ({{ mesa.activa ? 'Activa' : 'Inactiva' }})
                            </span>
                        </h2>

                        <div class="flex justify-center">
                            <img v-if="mesa.qr && mesa.activa" :src="`data:image/svg+xml;base64,${mesa.qr}`" alt="QR" class="w-52 h-52">
                            <div v-else class="w-52 h-52 flex items-center justify-center border bg-gray-50 text-gray-400 text-sm text-center p-4">
                                {{ !mesa.activa ? 'Mesa Desactivada' : 'Sin código QR' }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-4">
                            <button @click="generarQr(mesa.id)" :disabled="!mesa.activa" class="bg-white border border-zinc-300 text-zinc-700 text-xs px-3 py-2 rounded-lg font-medium hover:bg-zinc-50 disabled:opacity-50">
                                Generar QR
                            </button>
                            <button @click="toggleEstado(mesa.id)" class="text-white text-xs px-3 py-2 rounded-lg font-medium" :class="mesa.activa ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'">
                                {{ mesa.activa ? 'Desactivar' : 'Activar' }}
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</template>