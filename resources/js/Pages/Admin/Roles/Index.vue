<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    roles: Array,
    allPermissions: Array
})

// Estados para el Modal estilo Usuario
const modalAbierto = ref(false)
const editando = ref(false)

const form = useForm({
    id: null,
    nombre: '',
    permissions: []
})

const limpiar = () => {
    editando.value = false
    form.reset()
    form.clearErrors()
}

const abrirCrear = () => {
    limpiar()
    modalAbierto.value = true
}

const abrirEditar = (rol) => {
    limpiar()
    editando.value = true
    form.id = rol.id
    form.nombre = rol.nombre
    form.permissions = rol.permissions.map(p => p.id)
    modalAbierto.value = true
}

const cerrarModal = () => {
    modalAbierto.value = false
    limpiar()
}

const guardar = () => {
    form.post('/dashboard/roles/store', {
        preserveScroll: true,
        onSuccess: () => cerrarModal()
    })
}

const eliminar = (id) => {
    console.log("Intentando eliminar el rol con ID:", id); // ESTO DEBE APARECER
    
    if (!confirm('¿Seguro que deseas eliminar este rol?')) return
    
    router.delete(`/dashboard/roles/${id}`, { 
        preserveScroll: true,
        onSuccess: () => console.log("Eliminado con éxito"),
        onError: (errors) => console.error("Error al eliminar:", errors),
        onFinish: () => console.log("Proceso de eliminación finalizado")
    })
}
</script>

<template>
    <navSito />

    <div class="flex min-h-screen bg-zinc-100/90">
        <AppLayout />

        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-x-hidden">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
                <h1 class="text-3xl font-black text-zinc-900">Roles</h1>
                <button @click="abrirCrear" class="flex items-center gap-2 bg-amber-500 text-white px-4 py-1.5 rounded-lg font-semibold hover:bg-amber-600 transition-all shadow-sm text-sm">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Crear Rol</span>
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px]">
                        <thead class="bg-zinc-900 text-white">
                            <tr>
                                <th class="px-4 py-4 text-left text-sm">Rol</th>
                                <th class="px-4 py-4 text-left text-sm">Permisos</th>
                                <th class="px-4 py-4 text-right text-sm">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            <tr v-for="rol in roles" :key="rol.id" class="hover:bg-zinc-50 transition">
                                <td class="px-4 py-4 text-sm font-medium">{{ rol.nombre }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="permiso in rol.permissions" :key="permiso.id" class="bg-zinc-100 px-2 py-1 text-[10px] rounded font-bold text-zinc-600 uppercase">
                                            {{ permiso.nombre }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right flex justify-end gap-2">
                                    <button @click="abrirEditar(rol)" class="px-3 py-1.5 rounded-lg bg-sky-500 text-white text-xs font-semibold hover:bg-sky-600 transition">
                                        Editar
                                    </button>
                                    <button @click="eliminar(rol.id)" class="px-3 py-1.5 rounded-lg bg-red-500 text-white text-xs font-semibold hover:bg-red-600 transition">
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div v-if="modalAbierto" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl overflow-hidden">
            <div class="bg-zinc-900 text-white px-4 py-3 flex justify-between items-center">
                <h2 class="font-bold text-sm uppercase tracking-wide">{{ editando ? 'Editar Rol' : 'Nuevo Rol' }}</h2>
                <button @click="cerrarModal" class="text-white/70 hover:text-white text-lg">✕</button>
            </div>

            <form @submit.prevent="guardar" class="p-5 space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-zinc-500 uppercase">Nombre del rol</label>
                    <input v-model="form.nombre" type="text" class="w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-amber-500 outline-none" />
                </div>

                <div>
                    <label class="text-[10px] font-bold text-zinc-500 uppercase">Permisos</label>
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-60 overflow-y-auto border rounded-xl p-3 bg-zinc-50">
                        <label v-for="perm in allPermissions" :key="perm.id" class="flex items-center gap-2 text-sm bg-white px-3 py-2 rounded-lg border hover:bg-amber-50 cursor-pointer transition">
                            <input type="checkbox" :value="perm.id" v-model="form.permissions" class="accent-amber-500" />
                            <span class="text-zinc-700">{{ perm.nombre }}</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t mt-3">
                    <button type="button" @click="cerrarModal" class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-100 transition">Cancelar</button>
                    <button type="submit" :disabled="form.processing" class="px-5 py-2 bg-amber-500 text-white rounded-lg text-sm font-semibold hover:bg-amber-600 transition disabled:opacity-50">
                        {{ form.processing ? 'Guardando...' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>