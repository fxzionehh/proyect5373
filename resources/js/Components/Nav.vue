<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()

const logout = () => {
  router.post('/logout')
}
</script>

<template>
  <div>
    <nav class="bg-black text-white px-6 py-4 flex justify-between">
      
      <div class="font-bold">
        <h1>SteamCup <Link class="font-light" href="/">Cafetería</Link></h1>
      </div>

      <div class="flex gap-4 items-center">

     
        <div v-if="page.props.auth?.user">
  
       
          <i @click="logout" class="fa-solid fa-right-from-bracket"></i>
        </div>

        <div class="flex items-center gap-1" v-else>
            <i class="fa-solid fa-user"></i>
          <Link href="/login">Login</Link>
        </div>

      </div>
    </nav>

    <main class="">
      <slot />
    </main>
  </div>
</template>