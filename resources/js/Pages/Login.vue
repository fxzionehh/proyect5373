<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'
import Toast from 'primevue/toast'
import { useToast } from "primevue/usetoast"

const toast = useToast()
const showPassword = ref(false)

const form = useForm({
  email: '',
  password: '',
})

const submit = () => {
  form.post('/login', {
    onSuccess: () => {
      toast.add({ severity: 'success', summary: '¡Bienvenido!', detail: 'Inicio de sesión exitoso', life: 3000 })
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0]
      toast.add({ severity: 'error', summary: 'Error', detail: firstError || 'Verifica tus datos', life: 4000 })
    },
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <Toast />

  <div class="min-h-screen flex flex-col bg-gradient-to-br from-gray-100 to-gray-200">
    <navSito />

    <div class="flex flex-1 items-center justify-center px-4 py-12">
      
      <!-- CARD -->
      <form
        @submit.prevent="submit"
        class="w-full max-w-md p-8 bg-white rounded-3xl shadow-2xl border border-gray-100"
      >
        <!-- Título DENTRO de la tarjeta -->
        <div class="text-center mb-8">
          <h1 class="text-2xl font-black text-gray-900 tracking-tight">
            Gestión de Pedidos
          </h1>
          <p class="text-sm text-gray-500 mt-1">Ingresa tus credenciales para acceder</p>
        </div>

        <!-- Campo Correo -->
        <div class="mb-4">
          <label class="block mb-1.5 text-xs font-bold text-gray-500 uppercase">Correo electrónico</label>
          <input
            v-model="form.email"
            type="email"
            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black focus:ring-2 focus:ring-black/10 outline-none transition"
            :class="{ 'border-red-500': form.errors.email }"
          />
          <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
        </div>

        <!-- Campo Contraseña -->
        <div class="mb-6">
          <label class="block mb-1.5 text-xs font-bold text-gray-500 uppercase">Contraseña</label>
          <div class="relative">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-black focus:ring-2 focus:ring-black/10 outline-none transition pr-16"
              :class="{ 'border-red-500': form.errors.password }"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black font-bold text-[10px] uppercase tracking-wider"
            >
              {{ showPassword ? 'Ocultar' : 'Ver' }}
            </button>
          </div>
          <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
        </div>

        <button
          type="submit"
          class="w-full bg-zinc-900 hover:bg-black text-white py-3 rounded-xl font-bold transition-transform active:scale-[0.98]"
          :disabled="form.processing"
        >
          {{ form.processing ? 'Procesando...' : 'Iniciar Sesión' }}
        </button>

      </form>
    </div>
  </div>
</template>