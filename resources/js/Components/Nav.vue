<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
// Importación corregida con la ruta exacta y la extensión .jpg correcta
import logoUrl from '../../img/logos/logo_steamcup.jpg'

const page = usePage()

const logout = () => {
  router.post('/logout')
}
</script>

<template>
  <div>
    <nav
      class="flex  justify-between items-center px-10  py-3 sticky top-0 z-50 bg-zinc-950/90 backdrop-blur-xl border-b border-zinc-800 shadow-2xl">

      <div class="flex items-center gap-3 font-bold">
        <Link href="/">
          <img :src="logoUrl" alt="SteamCup Logo" class="h-8 w-auto object-contain rounded" />
        </Link>

        <div class="flex flex-col">
          <h1 class="text-sm font-black  uppercase text-white leading-none">
            SteamCup
          </h1>

          <p class="text-[9px] text-zinc-400 mt-1 tracking-wider uppercase">
            Cafetería
          </p>
        </div>

      </div>

      <div class="flex gap-4 items-center">

        <div v-if="page.props.auth?.user" class="flex items-center gap-4">

          <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg">
            <i class="fa-solid fa-user text-xs text-zinc-400"></i>
            <span class="text-xs font-medium text-zinc-200">
              {{ page.props.auth.user.name }}
            </span>
          </div>

          <button @click="logout" title="Cerrar sesión"
            class="px-2 text-zinc-400 hover:text-red-400 hover:bg-zinc-900 rounded-lg transition-all duration-200  border border-zinc-700 ">
            <i class="fa-solid fa-right-from-bracket text-sm"></i>
          </button>

        </div>

       <div v-else>

    <Link
        href="/login"
        class="flex items-center gap-2 text-xs font-semibold text-white bg-zinc-800 hover:bg-zinc-700 px-3 py-1.5 rounded-lg transition-all duration-200"
    >

        <i class="fa-solid fa-user"></i>

        <span>
            Login
        </span>

    </Link>

</div>

      </div>
    </nav>

    <main>
      <slot />
    </main>
  </div>
</template>