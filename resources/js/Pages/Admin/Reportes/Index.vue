<script setup>
import { defineProps, computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import { Pie, Bar, Line } from 'vue-chartjs'
import AppLayout from '@/Layouts/AppLayout.vue'
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

const productosChart = computed(() => ({
    labels: props.topProductos.map(p => p.nombre),
    datasets: [{
        label: 'Solicitados',
        data: props.topProductos.map(p => p.total),

        backgroundColor: [
            '#3b82f6', 
            '#22c55e', 
            '#f59e0b', 
            '#ef4444', 
            '#a855f7', 
            '#14b8a6',
            '#f97316', 
            '#6366f1',
            '#ec4899', 
            '#84cc16' 
        ]
    }]
}))

</script>

<template>

    <Head title="Reportes" />

    <navSito />

    <div class="flex min-h-screen bg-zinc-100/90">

        <AppLayout />
    


        <main class="flex-1 bg-zinc-100 p-8 overflow-y-auto">


            <div class="mb-8 flex items-center justify-between">

                <div>
                    <h1 class="text-3xl font-bold text-zinc-800">
                        Reportes
                    </h1>


                </div>

            </div>

           
    

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">


                <div class="bg-white rounded-2xl shadow-sm border border-zinc-200 p-6">

                    <div class="mb-5">
                        <h2 class="text-lg font-bold text-zinc-800">
                            Pedidos por día
                        </h2>


                    </div>

                    <div class="h-[350px]">
                        <Line :data="diasChart" :options="{
                            responsive: true,
                            maintainAspectRatio: false
                        }" />
                    </div>

                </div>


                <div class="bg-white rounded-2xl shadow-sm border border-zinc-200 p-6">

                    <div class="mb-5">


                        <p class="text-lg font-bold text-zinc-800">
                            Productos mas solicitados
                        </p>
                    </div>

                    <div class="h-[350px]">
                        <Bar :data="productosChart" :options="{
                            responsive: true,
                            maintainAspectRatio: false
                        }" />
                    </div>

                </div>

            </div>



        </main>
    </div>

</template>