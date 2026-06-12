<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'

const props = defineProps({
    productos: Array,
    mesaActual: Object,
    pedidoActual: Object,
    puedePedir: Boolean,
})

const paso = ref(1)
const productoSeleccionado = ref(null)

const estadoTexto = {
    pendiente: 'Pedido recibido',
    en_preparacion: 'Preparando tu pedido',
    listo: 'Listo para retiro',
}

const mesaId = computed(() => props.mesaActual?.id ?? null)

const precioSeleccionado = computed(() => {
    if (!productoSeleccionado.value) return 0
    return productoSeleccionado.value[`precio_${form.tamano}`] || 0
})

const form = useForm({
    mesa_id: mesaId.value,
    nombre_cliente: '',
    producto_id: null,
    tamano: 'normal',
})

const seleccionarProducto = (p) => {
    if (!props.puedePedir || !mesaId.value) return
    productoSeleccionado.value = p
    form.producto_id = p.id
    paso.value = 2
}

const irNombre = () => paso.value = 3
const volver = () => paso.value--
const confirmar = () => {
    if (!mesaId.value || !form.nombre_cliente) return
    form.post('/pedidos', {
        preserveScroll: true,
        onSuccess: () => {
            paso.value = 1
            productoSeleccionado.value = null
            form.reset('nombre_cliente', 'producto_id', 'tamano')
        }
    })
}
</script>

<template>
<div class="min-h-screen bg-black text-white font-sans selection:bg-amber-400 selection:text-black">
    <navSito />

    <div v-if="!mesaActual" class="flex flex-col items-center justify-center min-h-[80vh] text-center px-8">
        <h2 class="text-3xl font-black uppercase tracking-tighter">Mesa no encontrada</h2>
    </div>

    <div v-else class="max-w-2xl mx-auto pb-10">
        <header class="p-6 border-b border-zinc-800 flex justify-between items-center bg-black sticky top-0 z-20">
            <div>
                <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Estás en</p>
                <h1 class="text-2xl font-black">MESA {{ mesaActual.numero }}</h1>
            </div>
            <div v-if="pedidoActual" class="px-4 py-1.5 bg-amber-500 text-black rounded-full text-[10px] font-black uppercase animate-pulse">
                {{ estadoTexto[pedidoActual.estado] }}
            </div>
        </header>

        <div v-if="paso === 1" class="p-6 animate-in slide-in-from-bottom-10">
    <div class="flex justify-between items-end mb-6">
        <h2 class="text-3xl font-black tracking-tighter uppercase italic text-zinc-500">¿Qué café quieres?</h2>
        <div class="flex gap-2">
            <div class="w-8 h-8 rounded-full bg-zinc-900 flex items-center justify-center text-zinc-500">←</div>
            <div class="w-8 h-8 rounded-full bg-zinc-900 flex items-center justify-center text-white">→</div>
        </div>
    </div>
    
    <div class="flex gap-4 overflow-x-auto pb-8 snap-x scrollbar-hide">
        <button v-for="p in productos" :key="p.id" 
            @click="seleccionarProducto(p)"
            class="min-w-[70%] snap-center group bg-zinc-900 p-8 rounded-[2rem] text-left transition-all border-2 border-transparent hover:border-amber-500 active:scale-95 relative overflow-hidden">
            
            <div class="relative z-10 w-20 h-20 bg-black/50 rounded-full flex items-center justify-center mb-8 mx-auto shadow-inner border border-zinc-800">
                <i class="fa-solid fa-mug-hot text-3xl text-amber-500"></i>
            </div>
            
            <div class="text-center">
                <h3 class="font-black text-2xl uppercase leading-tight">{{ p.nombre }}</h3>
                <span class="inline-block mt-4 px-4 py-1 bg-amber-500/10 text-amber-500 rounded-full text-[10px] font-bold uppercase tracking-widest">
                    Seleccionar
                </span>
            </div>
        </button>
    </div>
</div>

        <div v-else-if="paso === 2" class="p-6 animate-in slide-in-from-right-10">
            <button @click="volver" class="mb-8 text-zinc-500 font-bold hover:text-white flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> VOLVER
            </button>
            
            <div class="mb-8 bg-zinc-900 p-6 rounded-3xl flex items-center gap-4">
                <div class="w-16 h-16 bg-black rounded-2xl flex items-center justify-center text-2xl">☕</div>
                <h2 class="text-3xl font-black uppercase italic tracking-tighter">{{ productoSeleccionado.nombre }}</h2>
            </div>
            
           <div class="grid grid-cols-2 gap-3">
    <button 
        v-for="t in ['nano', 'mini', 'normal', 'max']" 
        :key="t"
        @click="form.tamano = t"
        :class="form.tamano === t 
            ? 'bg-amber-500 text-black border-amber-500' 
            : 'bg-zinc-900 text-white border-zinc-800'"
        class=" flex flex-col items-center justify-center rounded-3xl border-2 transition-all active:scale-95 hover:border-zinc-500"
    >
        <span class="font-black text-xl uppercase">{{ t }}</span>
        
        <span class="text-xs font-bold opacity-70 mt-1">
            ${{ Number(productoSeleccionado[`precio_${t}`]).toLocaleString('es-CL') }}
        </span>
    </button>
</div>

            <div class="mt-8 p-6 bg-zinc-900 rounded-3xl flex justify-between items-center">
                <span class="text-zinc-400 font-bold uppercase">Total</span>
                <span class="text-4xl font-black tracking-tight">${{ Number(precioSeleccionado).toLocaleString('es-CL') }}</span>
            </div>

            <button @click="irNombre" class="w-full mt-6 bg-white text-black py-6 rounded-2xl font-black text-xl uppercase tracking-widest active:scale-95">
                Continuar
            </button>
        </div>

        <div v-else class="p-6 animate-in slide-in-from-right-10">
            <button @click="volver" class="mb-8 text-zinc-500 font-bold hover:text-white">← VOLVER</button>
            <h2 class="text-4xl font-black mb-8 uppercase italic tracking-tighter">¿Quién pide?</h2>
            <div class="bg-zinc-900 p-8 rounded-3xl">
                <input v-model="form.nombre_cliente" 
                    class="w-full bg-black border-2 border-zinc-800 rounded-2xl p-6 text-xl font-bold text-white outline-none focus:border-amber-500"
                    placeholder="Escribe tu nombre..." />
                
                <button @click="confirmar" :disabled="form.processing"
                    class="w-full mt-6 bg-amber-500 text-black py-6 rounded-2xl font-black text-xl uppercase tracking-widest disabled:opacity-50">
                    {{ form.processing ? 'ENVIANDO...' : 'Pedir' }}
                </button>
            </div>
        </div>
    </div>
</div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
</style>