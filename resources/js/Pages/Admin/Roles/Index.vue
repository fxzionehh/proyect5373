<script setup>
import { ref } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
const page = usePage()

const props = defineProps({
    roles: Array
})

console.log(props.roles)

const form = useForm({

})
const inventarioAbierto = ref(false)
const seccionActiva = ref('productos')


const roleAbierto = ref(true)
const openMenu = ref({
    inventario: false,
    pedidos: false,
    roles: false,
    usuarios: false,
})

const toggleMenu = (menu) => {
    openMenu.value[menu] = !openMenu.value[menu]
}
</script>

<template>
    <navSito />

    <div class="flex min-h-screen bg-zinc-100/90">

        <AppLayout />


        <main class="flex-1 p-8">

            <div class="flex justify-between items-center mb-4">

                <div>
                    <h1 class="text-3xl font-black text-zinc-900">
                        Roles
                    </h1>

                    <p class="text-gray-500 mt-1">
                        Listados de roles.
                    </p>
                </div>

            </div>


         <section class="bg-white rounded-xl overflow-hidden border border-gray-200">

    <table class="w-full">

        <thead class="bg-zinc-900 text-white text-sm uppercase tracking-wide">
            <tr>
                <th class="px-6 py-4 text-left">Nombre Rol</th>
                <th class="px-6 py-4 text-left">Fecha creación</th>
                <th class="px-6 py-4 text-right">Acción</th>
            </tr>
        </thead>

        <tbody>

            <tr
                v-for="rol in roles"
                :key="rol.id"
                class="border-t hover:bg-gray-50 transition"
            >

                <td class="px-6 py-4 font-medium text-zinc-800">
                    {{ rol.nombre }}
                </td>

                <td class="px-6 py-4 font-semibold text-gray-700">
                    {{ rol.created_at }}
                </td>

                <td class="px-6 py-4">
                    <div class="flex justify-end">

                        <Link
                            :href="`/dashboard/roles/${rol.id}/edit`"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 transition"
                        >
                            <i class="fa-solid fa-pen text-xs"></i>
                        </Link>

                    </div>
                </td>

            </tr>

            <tr v-if="roles.length === 0">
                <td colspan="3" class="px-6 py-10 text-center text-gray-500">
                    No hay roles registrados.
                </td>
            </tr>

        </tbody>

    </table>

</section>

        </main>

    </div>
</template>