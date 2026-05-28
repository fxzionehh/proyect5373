<script setup>
import { useForm } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'

// 1. Imports de PrimeVue
import Toast from 'primevue/toast'
import { useToast } from "primevue/usetoast"

const toast = useToast()

const form = useForm({
  email: '',
  password: '',
})

const submit = () => {
  form.post('/login', {
    onSuccess: () => {
      // 2. Disparar Toast de éxito
      toast.add({ 
        severity: 'success', 
        summary: '¡Bienvenido!', 
        detail: 'Inicio de sesión exitoso', 
        life: 3000 
      })
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0]
      // 3. Disparar Toast de error
      toast.add({ 
        severity: 'error', 
        summary: 'Error', 
        detail: firstError || 'Verifica tus datos', 
        life: 4000 
      })
    },
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
  <!-- 4. El componente visual de PrimeVue -->
  <Toast />

  <navSito>
    <form @submit.prevent="submit" class="max-w-md mx-auto mt-10 p-6 bg-white shadow-lg rounded-lg border border-gray-100">
      
      <h2 class="text-2xl font-bold mb-6 text-center">Iniciar Sesión</h2>

      <div class="mb-4">
        <label class="block mb-1 font-medium text-gray-700">Correo</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-black outline-none transition"
          :class="{'border-red-500': form.errors.email}"
          required
        />
        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
      </div>

      <div class="mb-6">
        <label class="block mb-1 font-medium text-gray-700">Contraseña</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-black outline-none transition"
          :class="{'border-red-500': form.errors.password}"
          required
        />
        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
      </div>

      <button
        type="submit"
        class="w-full bg-black hover:bg-gray-800 text-white py-2 rounded-md font-semibold flex justify-center items-center gap-2 transition"
        :disabled="form.processing"
      >
        <span v-if="form.processing">Procesando...</span>
        <span v-else>Entrar</span>
      </button>

    </form>
  </navSito>
</template>