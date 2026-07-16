<template>
  <div class="min-h-full">

    <!-- =====================================================
         SIDEBAR MÓVIL — overlay, se monta sobre todo
         ===================================================== -->
    <Transition
      enter-active-class="transition-opacity ease-linear duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity ease-linear duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/80" @click="sidebarOpen = false" />

        <!-- Panel deslizable -->
        <div class="fixed inset-0 flex">
          <Transition
            enter-active-class="transition ease-in-out duration-300 transform"
            enter-from-class="-translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition ease-in-out duration-300 transform"
            leave-from-class="translate-x-0"
            leave-to-class="-translate-x-full"
            appear
          >
            <div class="relative flex w-64 max-w-xs flex-col bg-slate-900">

              <!-- Botón cerrar (fuera del panel, a la derecha) -->
              <div class="absolute left-full top-0 flex w-14 justify-center pt-4">
                <button
                  @click="sidebarOpen = false"
                  class="-m-2.5 p-2.5 rounded-md text-white/70 hover:text-white"
                >
                  <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>

              <SidebarContent @navigate="sidebarOpen = false" />
            </div>
          </Transition>
        </div>
      </div>
    </Transition>

    <!-- =====================================================
         SIDEBAR DESKTOP — fijo, siempre visible en lg+
         ===================================================== -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-64 lg:flex-col">
      <div class="flex grow flex-col overflow-y-auto bg-slate-900">
        <SidebarContent />
      </div>
    </div>

    <!-- =====================================================
         CONTENIDO PRINCIPAL
         ===================================================== -->
    <div class="flex flex-col min-h-screen lg:pl-64">

      <!-- Top bar -->
      <header class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-4 border-b border-gray-200 bg-white px-4 sm:px-6">

        <!-- Hamburguesa móvil -->
        <button
          @click="sidebarOpen = true"
          class="lg:hidden -m-2.5 p-2.5 text-gray-600 hover:text-gray-900 transition-colors"
        >
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
          </svg>
        </button>

        <!-- Separador vertical (solo desktop) -->
        <div class="h-6 w-px bg-gray-200 lg:hidden" />

        <!-- Breadcrumbs / Título -->
        <nav v-if="breadcrumbs?.length" class="flex flex-1 items-center gap-1.5 text-sm min-w-0">
          <template v-for="(crumb, i) in breadcrumbs" :key="i">
            <Link
              v-if="crumb.href"
              :href="crumb.href"
              class="shrink-0 text-gray-500 hover:text-gray-700 transition-colors truncate max-w-32 sm:max-w-none"
            >
              {{ crumb.label }}
            </Link>
            <span v-else class="font-medium text-gray-900 truncate">{{ crumb.label }}</span>
            <svg
              v-if="i < breadcrumbs.length - 1"
              class="h-4 w-4 shrink-0 text-gray-300"
              fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
          </template>
        </nav>
        <span v-else class="flex-1 text-sm font-semibold text-gray-900 truncate">{{ title }}</span>

        <!-- Acciones del header -->
        <div v-if="$slots.actions" class="flex shrink-0 items-center gap-2">
          <slot name="actions" />
        </div>
      </header>

      <!-- Flash message -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
      >
        <div
          v-if="flashMessage"
          class="mx-4 sm:mx-6 mt-4 flex items-start gap-3 rounded-lg px-4 py-3 text-sm ring-1"
          :class="flashType === 'success'
            ? 'bg-green-50 text-green-800 ring-green-200'
            : 'bg-red-50 text-red-800 ring-red-200'"
        >
          <svg v-if="flashType === 'success'" class="mt-0.5 h-4 w-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <svg v-else class="mt-0.5 h-4 w-4 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
          </svg>
          <span class="flex-1">{{ flashMessage }}</span>
          <button @click="flashMessage = null" class="shrink-0 opacity-60 hover:opacity-100 transition-opacity">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </Transition>

      <!-- Contenido de la página -->
      <main class="flex-1 relative">
        <div class="px-4 sm:px-6 py-6">
          <slot />
        </div>

        <!-- Skeleton overlay — cubre el contenido durante la navegación
             sin desmontar el slot (Inertia necesita el componente montado) -->
        <Transition
          enter-active-class="transition-opacity duration-150 ease-out"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="transition-opacity duration-200 ease-in"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div
            v-if="navigating"
            class="absolute inset-0 z-10 bg-gray-50 px-4 sm:px-6 py-6"
          >
            <PageSkeleton />
          </div>
        </Transition>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import SidebarContent from './SidebarContent.vue';
import PageSkeleton from '@/Components/PageSkeleton.vue';

defineProps({
  title:       String,
  breadcrumbs: Array,
});

const page         = usePage();
const sidebarOpen  = ref(false);
const userMenuOpen = ref(false);
const userMenuRef  = ref(null);
const auth         = computed(() => page.props.auth);
const navigating   = ref(false);

// Flash
const flashMessage = ref(null);
const flashType    = ref('success');

watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.success) { flashMessage.value = flash.success; flashType.value = 'success'; }
    if (flash?.error)   { flashMessage.value = flash.error;   flashType.value = 'error';   }
    if (flashMessage.value) setTimeout(() => (flashMessage.value = null), 4000);
  },
  { immediate: true, deep: true },
);

// Bloquear scroll del body cuando sidebar móvil está abierto
watch(sidebarOpen, (val) => {
  document.body.style.overflow = val ? 'hidden' : '';
});

// Cerrar user menu al click fuera
function onClickOutside(e) {
  if (userMenuRef.value && !userMenuRef.value.contains(e.target)) {
    userMenuOpen.value = false;
  }
}
let removeStart, removeFinish;
onMounted(() => {
  document.addEventListener('mousedown', onClickOutside);
  removeStart  = router.on('start',  () => { navigating.value = true; });
  removeFinish = router.on('finish', () => { navigating.value = false; });
});
onUnmounted(() => {
  document.removeEventListener('mousedown', onClickOutside);
  document.body.style.overflow = '';
  removeStart?.();
  removeFinish?.();
});
</script>
