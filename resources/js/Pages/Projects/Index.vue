<template>
  <AppLayout title="Proyectos">
    <template #actions>
      <Link :href="route('projects.create')"
            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Nuevo proyecto
      </Link>
    </template>

    <!-- Filtros -->
    <div class="mb-6 flex flex-wrap items-center gap-2 rounded-xl bg-white px-4 py-3 shadow-sm ring-1 ring-gray-100">
      <div class="flex flex-1 items-center gap-2.5 rounded-lg bg-gray-50 px-3 py-2 min-w-48 ring-1 ring-gray-200 transition-all focus-within:bg-white focus-within:ring-indigo-400 focus-within:shadow-sm">
        <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
        </svg>
        <input type="text" v-model="search" @input="applyFilters"
               placeholder="Buscar por nombre, código o cliente..."
               class="w-full bg-transparent text-sm text-gray-900 placeholder-gray-400 outline-none"/>
      </div>
      <div class="hidden h-5 w-px bg-gray-200 sm:block"></div>
      <select v-model="status" @change="applyFilters"
              class="cursor-pointer rounded-lg bg-gray-50 px-3 py-2 text-sm
               text-gray-600 ring-1 ring-gray-200 transition-all hover:ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white">
        <option value="">Todos los estados</option>
        <option value="draft">Borrador</option>
        <option value="active">En progreso</option>
        <option value="paused">Pausado</option>
        <option value="completed">Completado</option>
        <option value="cancelled">Cancelado</option>
      </select>
      <select v-model="clientId" @change="applyFilters"
              class="cursor-pointer rounded-lg bg-gray-50 
              px-3 py-2 text-sm text-gray-600 ring-1
               ring-gray-200 transition-all hover:ring-gray-300
                focus:outline-none focus:ring-2 focus:ring-indigo-400
                focus:bg-white">
        <option value="">Todos los clientes</option>
        <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <button v-if="search || status || clientId" @click="clearFilters"
              class="flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-gray-400 transition-colors hover:bg-red-50 hover:text-red-500">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Limpiar
      </button>
    </div>

    <!-- Grid de proyectos -->
    <div v-if="projects.data.length === 0"
         class="flex flex-col items-center justify-center rounded-xl bg-white py-16 text-center shadow-sm ring-1 ring-gray-100">
      <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100">
        <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/>
        </svg>
      </div>
      <p class="text-sm font-medium text-gray-900">No hay proyectos</p>
      <Link :href="route('projects.create')"
            class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
        Crear proyecto
      </Link>
    </div>

    <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
      <Link v-for="project in projects.data" :key="project.id"
            :href="route('projects.show', project.id)"
            class="group flex flex-col rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hover:shadow-md hover:ring-indigo-200 transition-all duration-200">
        <div class="flex items-start justify-between gap-2">
          <div class="min-w-0">
            <p class="font-mono text-xs text-gray-400">{{ project.code }}</p>
            <h3 class="mt-0.5 truncate text-sm font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
              {{ project.name }}
            </h3>
            <p class="mt-0.5 truncate text-xs text-gray-500">{{ project.client_name }}</p>
          </div>
          <Badge :color="project.status_color">{{ project.status_name }}</Badge>
        </div>
        <div class="mt-4">
          <ProgressBar :value="project.progress"
            :color="project.progress >= 75 ? 'green' : project.progress >= 40 ? 'blue' : 'yellow'"/>
        </div>
        <div class="mt-4 flex items-center justify-between text-xs text-gray-400">
          <span class="font-medium text-gray-700">{{ project.formatted_budget }}</span>
          <span>{{ project.end_date ? `Hasta ${project.end_date}` : 'Sin fecha fin' }}</span>
        </div>
      </Link>
    </div>

    <!-- Paginación -->
    <div v-if="projects.links?.length > 3" class="mt-5 flex items-center gap-1 flex-wrap">
      <template v-for="link in projects.links" :key="link.label">
        <Link v-if="link.url" :href="link.url"
              class="rounded-md px-3 py-1.5 text-xs font-medium transition-colors"
              :class="link.active ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
              v-html="link.label" preserve-scroll/>
        <span v-else class="rounded-md px-3 py-1.5 text-xs text-gray-400 cursor-not-allowed" v-html="link.label"/>
      </template>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import ProgressBar from '@/Components/ProgressBar.vue';

const props = defineProps({
  projects: Object,
  clients:  Array,
  filters:  Object,
});

const search   = ref(props.filters?.search    ?? '');
const status   = ref(props.filters?.status    ?? '');
const clientId = ref(props.filters?.client_id ?? '');

let searchTimeout = null;
function applyFilters() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get(route('projects.index'), {
      search:    search.value    || undefined,
      status:    status.value    || undefined,
      client_id: clientId.value || undefined,
    }, { preserveState: true, replace: true });
  }, 300);
}

function clearFilters() {
  search.value = ''; status.value = ''; clientId.value = '';
  router.get(route('projects.index'), {}, { preserveState: false });
}
</script>
