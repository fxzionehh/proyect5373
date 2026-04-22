<script setup>
import { ref } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
})

const lat = ref(null)
const lng = ref(null)
const showModal = ref(true)
const loading = ref(false)

function pedirUbicacion() {

    if (!navigator.geolocation) {
        alert("Tu navegador no soporta ubicación")
        return
    }

    loading.value = true

    navigator.geolocation.getCurrentPosition(
        (pos) => {
            lat.value = pos.coords.latitude
            lng.value = pos.coords.longitude
            showModal.value = false
            loading.value = false
        },
        (err) => {
            loading.value = false
            alert(err.message)
        },
        {
            enableHighAccuracy: true,
            timeout: 30000,
            maximumAge: 0
        }
    )
}

function hacerPedido() {
    if (!lat.value || !lng.value) {
        alert("Esperando ubicación...")
        return
    }

    router.post('/pedido', {
        tipo: 'llevar',
        lat: lat.value,
        lng: lng.value
    })
}
</script>

<template>

<Head title="Welcome" />

<AuthenticatedLayout>

    <template #header>
        <h2 class="text-xl font-semibold">
            🛵 Crear pedido
        </h2>
    </template>

    <div class="py-10 flex flex-col items-center">

        <!-- MODAL -->
        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black/60 z-50">
            <div class="bg-white p-6 rounded-xl text-center w-80 shadow-lg">

                <h2 class="text-lg font-bold mb-2">📍 Ubicación requerida</h2>

                <p class="text-sm text-gray-600">
                    Necesitamos tu ubicación para crear el pedido
                </p>

                <button
                    @click="pedirUbicacion"
                    class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg"
                    :disabled="loading"
                >
                    {{ loading ? 'Obteniendo...' : 'Continuar' }}
                </button>

            </div>
        </div>

        <!-- INFO -->
        <div v-if="lat && lng" class="text-center mb-4">
            📍 {{ lat }}, {{ lng }}
        </div>

        <!-- BOTÓN -->
        <button
            @click="hacerPedido"
            class="px-6 py-3 bg-green-600 text-white rounded-lg"
        >
            Hacer pedido
        </button>

    </div>

</AuthenticatedLayout>

</template>