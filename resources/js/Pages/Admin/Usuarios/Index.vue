<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    usuarios: Array,
    roles: Array, 
})

const modalAbierto = ref(false)
const editando = ref(false)


const form = useForm({
    id: null,
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '', 
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

const abrirEditar = async (id) => {
    if (!id) return;
    
    limpiar()
    editando.value = true

    try {
        const response = await fetch(`/usuarios/edit/${id}`)
        const usuario = await response.json()

        if (usuario.error) {
            alert(usuario.error)
            return
        }

        form.id = usuario.id
        form.name = usuario.name
        form.email = usuario.email
        form.role_id = usuario.role_id 
        modalAbierto.value = true
    } catch (error) {
        console.error(error)
        alert('Error al cargar el usuario')
    }
}

const cerrarModal = () => {
    modalAbierto.value = false
    limpiar()
}

const guardar = () => {
    form.post('/dashboard/usuarios', {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            cerrarModal()
        }
    })
}

const eliminar = (id) => {
    if (!confirm('¿Seguro que deseas eliminar este usuario?')) return
    router.delete(`/dashboard/usuarios/${id}`, { preserveScroll: true })
}
</script>

<template>
    <navSito />
    <div class="min-h-screen bg-zinc-100/90 md:flex">
        <AppLayout />

        <main class="flex-1 p-4 md:p-8">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
                <h1 class="text-3xl font-black text-zinc-900">Usuarios</h1>
                <button @click="abrirCrear" class="flex items-center gap-2 bg-amber-500 text-white px-4 py-1.5 rounded-lg font-semibold hover:bg-amber-600 transition-all shadow-sm text-sm">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Crear usuario</span>
                </button>
            </div>

            <section class="bg-white rounded-xl overflow-hidden border shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[850px] text-sm">
                        <thead class="bg-zinc-900 text-white">
                            <tr>
                                <th class="px-4 py-4 text-left">ID</th>
                                <th class="px-4 py-4 text-left">Nombre</th>
                                <th class="px-4 py-4 text-left">Email</th>
                                <th class="px-4 py-4 text-left">Rol</th> <th class="px-4 py-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100">
                            <tr v-for="usuario in usuarios" :key="usuario.id" class="hover:bg-zinc-50 transition">
                                <td class="px-4 py-4">{{ usuario.id }}</td>
                                <td class="px-4 py-4 font-medium">{{ usuario.name }}</td>
                                <td class="px-4 py-4">{{ usuario.email }}</td>
                                <td class="px-4 py-4">
                                    <span class="px-2 py-1 bg-zinc-100 rounded text-[10px] font-bold text-zinc-600 uppercase">
                                        {{ usuario.role ? usuario.role.nombre : 'Sin rol' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button @click="abrirEditar(usuario.id)" class="px-3 py-1.5 rounded-lg bg-sky-500 text-white text-xs font-semibold hover:bg-sky-600 transition">Editar</button>
                                        <button @click="eliminar(usuario.id)" class="px-3 py-1.5 rounded-lg bg-red-500 text-white text-xs font-semibold hover:bg-red-600 transition">Eliminar</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="usuarios.length === 0">
                                <td colspan="5" class="px-4 py-10 text-center text-zinc-500">No hay usuarios registrados.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <div v-if="modalAbierto" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-md rounded-xl shadow-2xl overflow-hidden">
            <div class="bg-zinc-900 text-white px-4 py-3 flex justify-between items-center">
                <h2 class="font-bold text-sm uppercase tracking-wide">{{ editando ? 'Editar usuario' : 'Nuevo usuario' }}</h2>
                <button @click="cerrarModal" class="text-white/70 hover:text-white">✕</button>
            </div>

          <form @submit.prevent="guardar" autocomplete="off" class="p-4 space-y-3">
    <div>
        <label class="text-[10px] font-bold text-zinc-500 uppercase">Nombre</label>
        <input v-model="form.name" type="text" class="w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-amber-500 outline-none" :class="{'border-red-500': form.errors.name}" />
        <div v-if="form.errors.name" class="text-red-500 text-[10px] font-bold mt-1">{{ form.errors.name }}</div>
    </div>

    <div>
        <label class="text-[10px] font-bold text-zinc-500 uppercase">Email</label>
        <input v-model="form.email" type="email" class="w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-amber-500 outline-none" :class="{'border-red-500': form.errors.email}" />
        <div v-if="form.errors.email" class="text-red-500 text-[10px] font-bold mt-1">{{ form.errors.email }}</div>
    </div>

    <div>
        <label class="text-[10px] font-bold text-zinc-500 uppercase">Rol</label>
        <select v-model="form.role_id" class="w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-amber-500 outline-none" :class="{'border-red-500': form.errors.role_id}">
            <option value="" disabled>Seleccione un rol</option>
            <option v-for="rol in roles" :key="rol.id" :value="rol.id">{{ rol.nombre }}</option>
        </select>
        <div v-if="form.errors.role_id" class="text-red-500 text-[10px] font-bold mt-1">{{ form.errors.role_id }}</div>
    </div>

    <div>
        <label class="text-[10px] font-bold text-zinc-500 uppercase">Contraseña</label>
        <input v-model="form.password" type="password" autocomplete="new-password" class="w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-amber-500 outline-none" :class="{'border-red-500': form.errors.password}" />
        <div v-if="form.errors.password" class="text-red-500 text-[10px] font-bold mt-1">{{ form.errors.password }}</div>
    </div>

    <div>
        <label class="text-[10px] font-bold text-zinc-500 uppercase">Confirmar contraseña</label>
        <input v-model="form.password_confirmation" type="password" autocomplete="new-password" class="w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:ring-1 focus:ring-amber-500 outline-none" />
    </div>

    <div class="flex justify-end gap-2 pt-2 border-t mt-3">
        <button type="button" @click="cerrarModal" class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-100">Cancelar</button>
        <button type="submit" :disabled="form.processing" class="px-4 py-1.5 text-sm bg-amber-500 text-white rounded-lg hover:bg-amber-600 font-semibold disabled:opacity-50">
            {{ form.processing ? 'Guardando...' : 'Guardar' }}
        </button>
    </div>
</form>
        </div>
    </div>
</template>