<script setup>
import { ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'

import logoUrl from '../../img/logos/logo_steamcup.jpg'

const page = usePage()
const mobileOpen = ref(false)

const logout = () => {
  router.post('/logout')
}
</script>

<template>
  <div>
    <nav class="sticky top-0 z-50 bg-zinc-950 shadow-2xl">

      <!-- TOP BAR -->
      <div class="flex items-center justify-between px-4 sm:px-6 lg:px-10 py-3">

        <!-- LEFT -->
        <div class="flex items-center gap-3">
          <!-- Logo opcional -->
          <!--
          <img :src="logoUrl" class="h-8 w-auto rounded" />
          -->

          <h1 class="text-sm sm:text-base font-black uppercase text-white leading-none">
            Gestión de pedidos
          </h1>
        </div>

        <!-- DESKTOP USER -->
        <div class="hidden sm:flex items-center gap-4">

          <div v-if="page.props.auth?.user" class="flex items-center gap-2 px-3 py-1.5 rounded-lg">
            <i class="fa-solid fa-user text-xs text-zinc-400"></i>
            <span class="text-xs font-medium text-zinc-200">
              {{ page.props.auth.user.name }}
            </span>
          </div>

          <button
            v-if="page.props.auth?.user"
            @click="logout"
            class="px-2 py-1 text-zinc-400 hover:text-red-400 hover:bg-zinc-900 rounded-lg transition border border-zinc-700"
          >
            <i class="fa-solid fa-right-from-bracket text-sm"></i>
          </button>

        </div>

        <!-- MOBILE BUTTON -->
        <button
          class="sm:hidden text-zinc-300"
          @click="mobileOpen = !mobileOpen"
        >
          <i class="fa-solid fa-bars text-lg"></i>
        </button>

      </div>

      <!-- MOBILE MENU -->
      <div
        v-if="mobileOpen"
        class="sm:hidden px-4 pb-4 space-y-3 border-t border-zinc-800"
      >

        <div v-if="page.props.auth?.user" class="flex items-center gap-2 pt-3">
          <i class="fa-solid fa-user text-xs text-zinc-400"></i>
          <span class="text-xs text-zinc-200">
            {{ page.props.auth.user.name }}
          </span>
        </div>

        <button
          v-if="page.props.auth?.user"
          @click="logout"
          class="w-full flex items-center justify-center gap-2 px-3 py-2 text-red-400 border border-zinc-700 rounded-lg"
        >
          <i class="fa-solid fa-right-from-bracket"></i>
          Cerrar sesión
        </button>

      </div>

    </nav>

    <main>
      <slot />
    </main>
  </div>
</template>