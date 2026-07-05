<script setup>
import NavSito from '@/Components/Nav.vue'
import QrcodeVue from 'qrcode.vue'

defineProps({
    pedido: Object
})

const baseUrl = window.location.origin
</script>

<template>
    <div class="min-h-screen bg-black text-white font-sans">

        <!-- NAV -->
        <NavSito />

        <div class="p-6 max-w-xl mx-auto">

            <!-- HEADER -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 mb-6 text-center">

                <h1 class="text-2xl font-black uppercase">
                    Pedido #{{ pedido.codigo }}
                </h1>

                <p class="text-zinc-400 text-sm mt-2 uppercase">
                    Cliente: {{ pedido.nombre_cliente }}
                </p>

                <div class="mt-3 inline-block px-3 py-1 bg-amber-500/10 border border-amber-500/30 rounded-full">
                    <p class="text-amber-400 text-xs font-bold uppercase">
                        {{ pedido.estado }}
                    </p>
                </div>

            </div>

            <!-- QR -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 flex flex-col items-center text-center mb-6">

                <h2 class="text-sm font-black uppercase text-zinc-400 mb-4">
                    Escanea este código en la vitrina
                </h2>

                <div class="bg-white p-4 rounded-2xl shadow-lg">
                   <QrcodeVue
    :value="`${baseUrl}/pedido/${pedido.codigo}`"
    :size="180"
    level="H"
/>
                </div>

                <p class="text-[10px] text-zinc-500 mt-4 uppercase">
                    SteamCup Cafetería
                </p>

            </div>

            <!-- DETALLES -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6">

                <h2 class="text-lg font-black uppercase mb-4">
                    Detalle del pedido
                </h2>

                <div class="space-y-3">
                    <div
                        v-for="d in pedido.detalles"
                        :key="d.id"
                        class="flex justify-between items-center border-b border-zinc-800 pb-3 last:border-none"
                    >
                        <div>
                            <p class="font-bold uppercase text-sm">
                                {{ d.producto.nombre }}
                            </p>
                            <p class="text-xs text-zinc-500 uppercase">
                                Tamaño: {{ d.tamano }}
                            </p>
                        </div>

                        <p class="text-amber-400 font-black">
                            ${{ Number(d.subtotal || 0).toLocaleString('es-CL') }}
                        </p>
                    </div>
                </div>

                <!-- TOTAL -->
                <div class="mt-6 flex justify-between items-center bg-amber-500/10 border border-amber-500/30 p-4 rounded-xl">
                    <span class="font-bold uppercase text-sm text-amber-400">
                        Total
                    </span>

                    <span class="text-xl font-black text-amber-400">
                        ${{ Number(pedido.total || 0).toLocaleString('es-CL') }}
                    </span>
                </div>

            </div>

        </div>
    </div>
</template>