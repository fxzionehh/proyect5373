<script setup>
import { useForm } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'

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
      toast.add({
        severity: 'success',
        summary: '¡Bienvenido!',
        detail: 'Inicio de sesión exitoso',
        life: 3000
      })
    },
    onError: (errors) => {
      const firstError = Object.values(errors)[0]

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
  <Toast />

 
  <div class="min-h-screen overflow-hidden flex flex-col bg-gradient-to-br from-gray-100 to-gray-200">

    <navSito />

   
    <div class="flex flex-1 items-center justify-center px-4">

      <form
        @submit.prevent="submit"
        class="w-full max-w-md p-8 bg-white rounded-2xl shadow-xl border border-gray-100"
      >

        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">
          Iniciar Sesión
        </h2>

        <div class="mb-4">
          <label class="block mb-1 font-medium text-gray-700">Correo</label>

          <input
            v-model="form.email"
            type="email"
            class="w-full px-4 py-2 rounded-lg border border-gray-200
                   focus:border-black focus:ring-2 focus:ring-black/10
                   outline-none transition"
            :class="{ 'border-red-500': form.errors.email }"
            required
          />

          <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">
            {{ form.errors.email }}
          </p>
        </div>

      
        <div class="mb-6">
          <label class="block mb-1 font-medium text-gray-700">Contraseña</label>

          <input
            v-model="form.password"
            type="password"
            class="w-full px-4 py-2 rounded-lg border border-gray-200
                   focus:border-black focus:ring-2 focus:ring-black/10
                   outline-none transition"
            :class="{ 'border-red-500': form.errors.password }"
            required
          />

          <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">
            {{ form.errors.password }}
          </p>
        </div>

       
        <button
          type="submit"
          class="w-full bg-black hover:bg-gray-800 text-white
                 py-3 rounded-lg font-semibold transition"
          :disabled="form.processing"
        >
          <span v-if="form.processing">Procesando...</span>
          <span v-else>Entrar</span>
        </button>

      </form>

    </div>
  </div>
</template>