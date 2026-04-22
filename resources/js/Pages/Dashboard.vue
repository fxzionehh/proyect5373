<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

/* 🏪 LOCAL FIJO */
const LOCAL = {
    lat: -36.830846573088095,
    lng: -73.05892420433075
}

/* 📍 CLIENTE */
const lat = ref(null)
const lng = ref(null)
const accuracy = ref(null)
const direccion = ref(null)

/* 📡 GPS */
let watchId = null

onMounted(() => {

    if (!navigator.geolocation) {
        alert("GPS no soportado")
        return
    }

    watchId = navigator.geolocation.watchPosition(
        async (position) => {

            lat.value = position.coords.latitude
            lng.value = position.coords.longitude
            accuracy.value = position.coords.accuracy

            await obtenerDireccion(lat.value, lng.value)
        },
        (error) => {
            console.log("GPS ERROR:", error)
        },
        {
            enableHighAccuracy: true,
            maximumAge: 0,
            timeout: 10000
        }
    )
})

onBeforeUnmount(() => {
    if (watchId) navigator.geolocation.clearWatch(watchId)
})

/* 🌍 Reverse Geocoding */
async function obtenerDireccion(lat, lng) {
    try {
        const res = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
        )
        const data = await res.json()
        direccion.value = data.display_name
    } catch (e) {
        console.log("Error dirección", e)
    }
}

/* 📏 DISTANCIA (opcional útil) */
function calcularDistancia() {
    if (!lat.value || !lng.value) return null

    const R = 6371 // km
    const dLat = (LOCAL.lat - lat.value) * Math.PI / 180
    const dLon = (LOCAL.lng - lng.value) * Math.PI / 180

    const a =
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat.value * Math.PI/180) *
        Math.cos(LOCAL.lat * Math.PI/180) *
        Math.sin(dLon/2) * Math.sin(dLon/2)

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a))

    return (R * c).toFixed(2)
}
</script>

<template>
<Head title="Seguimiento" />

<AuthenticatedLayout>

    <template #header>
        <h2 class="text-xl font-semibold">
            📍 Seguimiento en tiempo real
        </h2>
    </template>

    <div class="py-10 max-w-6xl mx-auto space-y-6">

        <!-- 📊 INFO -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div class="bg-white p-4 rounded-xl shadow">
                <h3 class="font-bold">📍 Dirección</h3>
                <p class="text-sm text-gray-600">
                    {{ direccion || 'Cargando...' }}
                </p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow">
                <h3 class="font-bold">🌍 Coordenadas</h3>
                <p class="text-sm">
                    {{ lat }}, {{ lng }}
                </p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow">
                <h3 class="font-bold">🎯 Precisión</h3>
                <p class="text-sm">
                    {{ accuracy ? Math.round(accuracy) + ' m' : '...' }}
                </p>
            </div>

        </div>

        <!-- 🚚 DISTANCIA -->
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="font-bold">📦 Distancia al local</h3>
            <p class="text-lg">
                {{ calcularDistancia() ? calcularDistancia() + ' km' : 'Calculando...' }}
            </p>
        </div>

    </div>

</AuthenticatedLayout>
</template>