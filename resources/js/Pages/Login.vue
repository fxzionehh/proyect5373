<script setup>
import { useForm } from '@inertiajs/vue3'
import navSito from '@/Components/Nav.vue'

const form = useForm({
  email: '',
  password: '',
})

const submit = () => {
  form.post('/login', {
    onStart: () => {
      console.log('Enviando...')
    },
    onSuccess: () => {
      console.log('Login correcto')
    },
    onError: () => {
      console.log('Error en login')
    }
  })
}
</script>

<template>
  <navSito>

    <form @submit.prevent="submit" class="max-w-md mx-auto mt-10">

      <div class="mb-4">
        <label class="block mb-1 font-medium">Correo</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full border rounded px-3 py-2"
          required
        />
        <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">
          {{ form.errors.email }}
        </p>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Contraseña</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full border rounded px-3 py-2"
          required
        />
        <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">
          {{ form.errors.password }}
        </p>
      </div>

      <button
        type="submit"
        class="w-full bg-black text-white py-2 rounded flex justify-center items-center gap-2"
        :disabled="form.processing"
      >
        <span v-if="form.processing">Cargando...</span>
        <span v-else>Entrar</span>
      </button>

    </form>

  </navSito>
</template>