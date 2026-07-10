<template>
  <div class="flex h-full flex-col">

    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center gap-3 border-b border-slate-700/50 px-6">
      <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-600">
        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
        </svg>
      </div>
      <span class="text-base font-semibold text-white">Project Manager</span>
    </div>

    <!-- Nav -->
    <nav class="flex flex-1 flex-col overflow-y-auto px-4 py-4">
      <ul class="flex flex-col gap-0.5">

        <!-- Dashboard -->
        <li>
          <Link
            :href="route('dashboard')"
            @click="$emit('navigate')"
            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
            :class="isActive('dashboard')
              ? 'bg-indigo-600 text-white'
              : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
            </svg>
            Dashboard
          </Link>
        </li>

        <!-- Separador -->
        <li class="mt-5 mb-2 px-3">
          <p class="text-xs font-semibold uppercase tracking-widest text-slate-500">Gestión</p>
        </li>

        <!-- Clientes -->
        <li>
          <Link
            :href="route('clients.index')"
            @click="$emit('navigate')"
            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
            :class="isActive('clients')
              ? 'bg-indigo-600 text-white'
              : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
            </svg>
            Clientes
          </Link>
        </li>

        <!-- Proyectos -->
        <li>
          <Link
            :href="route('projects.index')"
            @click="$emit('navigate')"
            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
            :class="isActive('projects')
              ? 'bg-indigo-600 text-white'
              : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/>
            </svg>
            Proyectos
          </Link>
        </li>

        <!-- Cotizaciones -->
        <li>
          <Link
            :href="route('quotes.index')"
            @click="$emit('navigate')"
            class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors"
            :class="isActive('quotes')
              ? 'bg-indigo-600 text-white'
              : 'text-slate-300 hover:bg-slate-800 hover:text-white'"
          >
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            Cotizaciones
          </Link>
        </li>

      </ul>

      <!-- Usuario (bottom) -->
      <div class="mt-auto border-t border-slate-700/50 pt-4">
        <div class="relative" ref="userMenuRef">
          <button
            @click="userMenuOpen = !userMenuOpen"
            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-colors hover:bg-slate-800"
          >
            <!-- Avatar -->
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-indigo-600 text-xs font-bold text-white">
              {{ userInitials }}
            </div>
            <!-- Info -->
            <div class="flex-1 min-w-0 text-left">
              <p class="truncate text-sm font-medium text-white">{{ auth?.user?.name }}</p>
              <p class="truncate text-xs text-slate-400">{{ auth?.user?.email }}</p>
            </div>
            <!-- Chevron -->
            <svg
              class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200"
              :class="userMenuOpen ? 'rotate-180' : ''"
              fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
            </svg>
          </button>

          <!-- Dropdown -->
          <Transition
            enter-active-class="transition ease-out duration-150"
            enter-from-class="opacity-0 scale-95 translate-y-1"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
          >
            <div
              v-if="userMenuOpen"
              class="absolute bottom-full left-0 mb-2 w-full origin-bottom rounded-xl bg-slate-800 py-1 shadow-xl ring-1 ring-slate-700"
            >
              <Link
                :href="route('profile.edit')"
                @click="userMenuOpen = false; $emit('navigate')"
                class="flex items-center gap-2.5 px-3 py-2.5 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition-colors rounded-lg mx-1"
              >
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Mi perfil
              </Link>
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                @click="userMenuOpen = false"
                class="flex w-full items-center gap-2.5 px-3 py-2.5 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition-colors rounded-lg mx-1"
              >
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                </svg>
                Cerrar sesión
              </Link>
            </div>
          </Transition>
        </div>
      </div>
    </nav>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

defineEmits(['navigate']);

const page    = usePage();
const auth    = computed(() => page.props.auth);

const userInitials = computed(() => {
  const name = auth.value?.user?.name ?? '';
  return name.slice(0, 2).toUpperCase();
});

const userMenuOpen = ref(false);
const userMenuRef  = ref(null);

function isActive(section) {
  try {
    return route().current(`${section}.*`) || route().current(section);
  } catch {
    return false;
  }
}

function onClickOutside(e) {
  if (userMenuRef.value && !userMenuRef.value.contains(e.target)) {
    userMenuOpen.value = false;
  }
}

onMounted(()  => document.addEventListener('mousedown', onClickOutside));
onUnmounted(() => document.removeEventListener('mousedown', onClickOutside));
</script>
