<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    roles: Array,
    allPermissions: Array
})

const editingRole = ref(null)

const form = useForm({
    id: null,
    nombre: '',
    permissions: []
})

const openEdit = (rol) => {
    editingRole.value = rol

    form.id = rol.id
    form.nombre = rol.nombre
    form.permissions = rol.permissions.map(p => p.id)
}

const closeModal = () => {
    editingRole.value = null
    form.reset()
}

const update = () => {
    form.post('/dashboard/roles/store', {
        preserveScroll: true,
        onSuccess: () => {
            closeModal()
        }
    })
}
</script>

<template>
    <navSito />

    <div class="flex min-h-screen bg-zinc-100/90">
        <AppLayout />

        <main class="flex-1 p-8">
            <h1 class="text-3xl font-black mb-6">
                Roles
            </h1>

            <table class="w-full bg-white rounded-xl shadow-sm">
                <thead class="bg-zinc-900 text-white">
                    <tr>
                        <th class="p-4 text-left">
                            Rol
                        </th>

                        <th class="p-4 text-left">
                            Permisos
                        </th>

                        <th class="p-4 text-right">
                            Acción
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="rol in roles"
                        :key="rol.id"
                        class="border-t"
                    >
                        <td class="p-4">
                            {{ rol.nombre }}
                        </td>

                        <td class="p-4">
                            <div class="flex gap-1 flex-wrap">
                                <span
                                    v-for="permiso in rol.permissions"
                                    :key="permiso.id"
                                    class="bg-zinc-200 px-2 py-1 text-[10px] rounded"
                                >
                                    {{ permiso.nombre }}
                                </span>
                            </div>
                        </td>

                        <td class="p-4 text-right">
                            <button
                                @click="openEdit(rol)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-500 text-white text-xs font-semibold hover:bg-sky-600 transition">
                            <i class="fa-solid fa-pen text-[10px]"></i>
                                Editar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Modal -->
           <div
    v-if="editingRole"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
>
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden">

        <!-- HEADER -->
        <div class="bg-zinc-900 text-white px-5 py-4 flex justify-between items-center">
            <h2 class="font-bold text-lg">
                Editando: {{ editingRole.nombre }}
            </h2>

            <button
                @click="closeModal"
                class="text-white/70 hover:text-white"
            >
                ✕
            </button>
        </div>

        <!-- BODY -->
        <form @submit.prevent="update" class="p-6 space-y-5">

            <!-- NOMBRE -->
            <div>
                <label class="text-[10px] font-bold text-zinc-500 uppercase">
                    Nombre del rol
                </label>

                <input
                    v-model="form.nombre"
                    type="text"
                    class="w-full mt-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-1 focus:ring-amber-500"
                />
            </div>

            <!-- PERMISOS -->
            <div>
                <label class="text-[10px] font-bold text-zinc-500 uppercase">
                    Permisos
                </label>

                <div class="mt-2 grid grid-cols-2 gap-2 max-h-60 overflow-y-auto border rounded-xl p-3 bg-zinc-50">

                    <label
                        v-for="perm in allPermissions"
                        :key="perm.id"
                        class="flex items-center gap-2 text-sm bg-white px-2 py-1 rounded-lg border hover:bg-amber-50 transition"
                    >
                        <input
                            type="checkbox"
                            :value="perm.id"
                            v-model="form.permissions"
                            class="accent-amber-500"
                        />

                        <span class="text-zinc-700">
                            {{ perm.nombre }}
                        </span>
                    </label>

                </div>
            </div>

            <!-- FOOTER -->
            <div class="flex justify-end gap-2 pt-2">

                <button
                    type="button"
                    @click="closeModal"
                    class="px-4 py-2 border rounded-xl text-sm hover:bg-gray-100"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-5 py-2 bg-amber-500 text-white rounded-xl text-sm font-semibold hover:bg-amber-600"
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