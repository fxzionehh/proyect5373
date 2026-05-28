<script setup>
import { defineProps, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import { Pie, Bar, Line } from 'vue-chartjs'

import { Link, useForm, router, usePage } from '@inertiajs/vue3'
const page = usePage()

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement
} from 'chart.js'

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    ArcElement,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    BarElement
)

const props = defineProps({
    totalPedidos: Number,
    ingresos: Number,
    porEstado: Array,
    porDia: Array,
    topProductos: Array
})

/* 📊 ESTADOS (PIE) */
const estadosChart = computed(() => ({
    labels: props.porEstado.map(e => e.estado),
    datasets: [{
        data: props.porEstado.map(e => e.total),
        backgroundColor: ['#ef4444', '#f59e0b', '#10b981', '#3b82f6']
    }]
}))

/* 📈 VENTAS POR DÍA (LINE) */
const diasChart = computed(() => ({
    labels: props.porDia.map(d => d.fecha),
    datasets: [{
        label: 'Pedidos por día',
        data: props.porDia.map(d => d.total),
        borderColor: '#3b82f6',
        tension: 0.3,
        fill: false
    }]
}))

/* 📦 TOP PRODUCTOS (BAR) */
const productosChart = computed(() => ({
    labels: props.topProductos.map(p => p.nombre),
    datasets: [{
        label: 'Vendidos',
        data: props.topProductos.map(p => p.total),
        backgroundColor: '#22c55e'
    }]
}))
</script>

<template>

    <Head title="Reportes" />

    <navSito />

    <div class="flex min-h-screen bg-zinc-100/90">

        <!-- SIDEBAR -->
        <aside class="w-52 min-h-screen bg-zinc-950 flex flex-col shadow-xl">

            <div class="px-6 py-7 border-zinc-900">
                <p class="text-lg font-bold text-white">
                    Panel Admin
                </p>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-3">

                <!-- PEDIDOS -->
                <Link href="/dashboard/pedidos" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg transition"
                    :class="page.url.startsWith('/dashboard/pedidos')
                        ? 'bg-zinc-900 text-white font-medium'
                        : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">

                <i class="fa-solid fa-receipt text-sm"></i>

                <span class="text-sm">
                    Pedidos
                </span>

                </Link>

                <!-- INVENTARIO -->
                <div>

                    <button @click="inventarioAbierto = !inventarioAbierto"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition" :class="page.url.startsWith('/dashboard/productos')
                            ? 'bg-zinc-900 text-white font-medium'
                            : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">

                        <div class="flex items-center gap-3">

                            <i class="fa-solid fa-boxes-stacked text-sm"></i>

                            <span class="text-sm">
                                Inventario
                            </span>

                        </div>

                        <i class="fa-solid text-xs transition-all duration-200" :class="inventarioAbierto
                            ? 'fa-chevron-down'
                            : 'fa-chevron-right'"></i>

                    </button>

                    <!-- SUBMENU -->
                    <div v-if="inventarioAbierto" class="ml-6 mt-2 flex flex-col gap-1">

                        <button @click="seccionActiva = 'productos'"
                            class="text-left px-3 py-2 rounded-md text-sm transition" :class="seccionActiva === 'productos'
                                ? 'bg-zinc-900/60 text-white'
                                : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                            Productos
                        </button>

                        <button @click="seccionActiva = 'insumos'"
                            class="text-left px-3 py-2 rounded-md text-sm transition" :class="seccionActiva === 'insumos'
                                ? 'bg-zinc-900/60 text-white'
                                : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                            Insumos
                        </button>

                        <button @click="seccionActiva = 'controlstock'"
                            class="text-left px-3 py-2 rounded-md text-sm transition" :class="seccionActiva === 'controlstock'
                                ? 'bg-zinc-900/60 text-white'
                                : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                            Control Stock
                        </button>

                    </div>

                </div>


                <div>

                    <button @click="roleAbierto = !roleAbierto"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg transition" :class="page.url.startsWith('/dashboard/roles')
                            ? 'bg-zinc-900 text-white font-medium'
                            : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">

                        <div class="flex items-center gap-3">

                            <i class="fa-solid fa-user-shield text-sm"></i>

                            <span class="text-sm">
                                Roles
                            </span>

                        </div>

                        <i class="fa-solid text-xs transition-all duration-200" :class="roleAbierto
                            ? 'fa-chevron-down'
                            : 'fa-chevron-right'"></i>

                    </button>

                    <div v-if="roleAbierto" class="ml-6 mt-2 flex flex-col gap-1">

                        <Link href="/dashboard/roles" class="text-left px-3 py-2 rounded-md text-sm transition" :class="page.url.startsWith('/dashboard/roles')
                            ? 'bg-zinc-900/60 text-white'
                            : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">
                        Roles
                        </Link>

                        <Link href="/dashboard/roles"
                            class="text-left px-3 py-2 rounded-md text-sm transition text-zinc-400 hover:bg-zinc-900 hover:text-white">
                        Usuarios
                        </Link>



                    </div>
                </div>

                 <Link href="/dashboard/reportes"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg transition" :class="page.url.startsWith('/dashboard/reportes')
                        ? 'bg-zinc-900 text-white font-medium'
                        : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">

                    <i class="fa-solid fa-chart-bar text-sm"></i>

                    <span class="text-sm">
                        Reportes
                    </span>

                </Link>
                
                <!-- CONFIG -->
                <Link href="/dashboard/configuracion"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg transition" :class="page.url.startsWith('/dashboard/configuracion')
                        ? 'bg-zinc-900 text-white font-medium'
                        : 'text-zinc-400 hover:bg-zinc-900 hover:text-white'">

                <i class="fa-solid fa-gear text-sm"></i>

                <span class="text-sm">
                    Configuración
                </span>

                </Link>

            </nav>

        </aside>

        <!-- CONTENIDO -->
       <!-- CONTENIDO -->
<main class="flex-1 bg-zinc-100 p-8 overflow-y-auto">

    <!-- HEADER -->
    <div class="mb-8 flex items-center justify-between">

        <div>
            <h1 class="text-3xl font-bold text-zinc-800">
                Reportes
            </h1>

            <p class="text-zinc-500 mt-1">
                Estadísticas generales del sistema
            </p>
        </div>

    </div>

    <!-- TARJETAS RESUMEN -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

        <div class="bg-white rounded-2xl shadow-sm p-6 border border-zinc-200">
            <p class="text-zinc-500 text-sm">
                Total pedidos
            </p>

            <h2 class="text-3xl font-bold mt-2 text-zinc-800">
                {{ totalPedidos }}
            </h2>
        </div>


        <div class="bg-white rounded-2xl shadow-sm p-6 border border-zinc-200">
            <p class="text-zinc-500 text-sm">
                Productos vendidos
            </p>

            <h2 class="text-3xl font-bold mt-2 text-blue-600">
                {{ topProductos.length }}
            </h2>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 border border-zinc-200">
            <p class="text-zinc-500 text-sm">
                Estados registrados
            </p>

            <h2 class="text-3xl font-bold mt-2 text-orange-500">
                {{ porEstado.length }}
            </h2>
        </div>

    </div>

    <!-- GRAFICOS -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        <!-- LINE -->
        <div class="bg-white rounded-2xl shadow-sm border border-zinc-200 p-6">

            <div class="mb-5">
                <h2 class="text-lg font-bold text-zinc-800">
                    Pedidos por día
                </h2>

       
            </div>

            <div class="h-[350px]">
                <Line
                    :data="diasChart"
                    :options="{
                        responsive: true,
                        maintainAspectRatio: false
                    }"
                />
            </div>

        </div>

        <!-- BAR -->
        <div class="bg-white rounded-2xl shadow-sm border border-zinc-200 p-6">

            <div class="mb-5">
              

                <p class="text-lg font-bold text-zinc-800">
                    Productos más vendidos
                </p>
            </div>

            <div class="h-[350px]">
                <Bar
                    :data="productosChart"
                    :options="{
                        responsive: true,
                        maintainAspectRatio: false
                    }"
                />
            </div>

        </div>

    </div>



</main>
   </div>
      
</template>