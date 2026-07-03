<script setup>
import { ref } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'

const page = usePage()
const mobileOpen = ref(false)
const dropdownOpen = ref(false)

const logout = () => {
  router.post('/logout')
}
</script>

<template>
  <div>
    <nav class="sticky top-0 z-50 bg-zinc-950 shadow-2xl">
      
  
      <div class="flex items-center justify-between px-4 sm:px-6 lg:px-10 py-3">

      
        <div class="flex items-center gap-3">
          <h1 class="text-sm sm:text-base font-black uppercase text-white leading-none">
            Gestión de pedidos
          </h1>
        </div>

      
        <div class="flex items-center gap-4">
          
      
          <div v-if="page.props.auth?.user" class="relative hidden sm:block">
            <button
              @click="dropdownOpen = !dropdownOpen"
              class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-zinc-900 transition border border-transparent hover:border-zinc-700"
            >
              <i class="fa-solid fa-user text-xs text-zinc-400"></i>
              <span class="text-xs font-medium text-zinc-200">
                {{ page.props.auth.user.name }}
              </span>
              <i class="fa-solid fa-chevron-down text-[10px] text-zinc-500"></i>
            </button>

          
            <div v-if="dropdownOpen" class="absolute right-0 top-full mt-2 w-56 bg-zinc-900 border border-zinc-800 rounded-xl shadow-2xl py-2 overflow-hidden">
              
            
              <Link
                href="/dashboard/preparacion"
                class="block px-4 py-2 text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white transition"
              >
                <i class="fa-solid fa-clipboard-list mr-2"></i> Preparación
              </Link>

            
              <div class="border-t border-zinc-800 my-1"></div>

                <Link
                href="/dashboard/pedidos"
                class="block px-4 py-2 text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white transition"
              >
                <i class="fa-solid fa-boxes mr-2"></i> Pedidos
              </Link>

           
              <div class="border-t border-zinc-800 my-1"></div>

             
                <Link
                href="/dashboard/productos"
                class="block px-4 py-2 text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white transition"
              >
                <i class="fa-solid fa-boxes mr-2"></i> Inventario
              </Link>

           
              <div class="border-t border-zinc-800 my-1"></div>

                <Link
                href="/dashboard/roles"
                class="block px-4 py-2 text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white transition"
              >
                <i class="fa-solid fa-user-group mr-2"></i> Roles
              </Link>

             
              <div class="border-t border-zinc-800 my-1"></div>

                <Link
                href="/dashboard/reportes"
                class="block px-4 py-2 text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white transition"
              >
                <i class="fa-solid fa-chart-bar mr-2"></i> Reportes
              </Link>

            
              <div class="border-t border-zinc-800 my-1"></div>

                <Link
                href="/dashboard/mesas"
                class="block px-4 py-2 text-xs text-zinc-300 hover:bg-zinc-800 hover:text-white transition"
              >
                <i class="fa-solid fa-table mr-2"></i> Mesas
              </Link>

             
              <div class="border-t border-zinc-800 my-1"></div>

              <button
                @click="logout"
                class="w-full text-left px-4 py-2 text-xs text-red-400 hover:bg-zinc-800 transition"
              >
                <i class="fa-solid fa-right-from-bracket mr-2"></i> Cerrar sesión
              </button>
            </div>
          </div>

        
          <button
            class="sm:hidden text-zinc-300"
            @click="mobileOpen = !mobileOpen"
          >
            <i class="fa-solid fa-bars text-lg"></i>
          </button>
        </div>
      </div>

     
      <div
        v-if="mobileOpen"
        class="sm:hidden px-4 pb-4 space-y-3 border-t border-zinc-800 bg-zinc-950"
      >
        <div v-if="page.props.auth?.user" class="pt-3 space-y-2">
          
          <div class="flex items-center gap-2 mb-2 px-2">
            <i class="fa-solid fa-user text-xs text-zinc-400"></i>
            <span class="text-xs text-zinc-200">{{ page.props.auth.user.name }}</span>
          </div>

       
          <Link
            href="/dashboard/preparacion"
            class="block w-full px-3 py-2 text-xs text-zinc-200 bg-zinc-900 rounded-lg border border-zinc-800"
          >
            Preparación de pedidos
          </Link>

          
          <button
            @click="logout"
            class="w-full flex items-center justify-center gap-2 px-3 py-2 text-red-400 border border-zinc-700 rounded-lg"
          >
            <i class="fa-solid fa-right-from-bracket"></i>
            Cerrar sesión
          </button>
        </div>
      </div>
    </nav>

    <main>
      <slot />
    </main>
  </div>
</template>