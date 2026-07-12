<template>
  <AppLayout title="Clientes">
    <template #actions>
      <Link :href="route('clients.create')"
            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Nuevo cliente
      </Link>
    </template>

    <!-- Filtros -->
    <div class="mb-6 flex flex-wrap items-center gap-2 rounded-xl bg-white px-4 py-3 shadow-sm ring-1 ring-gray-100">
      <div class="flex flex-1 items-center gap-2.5 rounded-lg bg-gray-50 px-3 py-2 min-w-52 ring-1 ring-gray-200 transition-all focus-within:bg-white focus-within:ring-indigo-400 focus-within:shadow-sm">
        <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
        </svg>
        <input
          type="text"
          v-model="search"
          @input="applyFilters"
          placeholder="Buscar por nombre, RUT o email..."
          class="w-full bg-transparent text-sm text-gray-900 placeholder-gray-400 outline-none"
        />
      </div>
      <div class="hidden h-5 w-px bg-gray-200 sm:block"></div>
      <select
        v-model="type"
        @change="applyFilters"
        class="cursor-pointer rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-600 ring-1 ring-gray-200 transition-all hover:ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white"
      >
        <option value="">Todos los tipos</option>
        <option value="company">Empresa</option>
        <option value="person">Persona Natural</option>
      </select>
      <button
        v-if="search || type"
        @click="clearFilters"
        class="flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-gray-400 transition-colors hover:bg-red-50 hover:text-red-500"
      >
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Limpiar
      </button>
    </div>

    <!-- Tabla -->
    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
      <div v-if="clients.data.length === 0" class="flex flex-col items-center justify-center py-16 text-center">
        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100">
          <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
          </svg>
        </div>
        <p class="text-sm font-medium text-gray-900">No hay clientes</p>
        <p class="mt-1 text-sm text-gray-500">Comienza creando tu primer cliente.</p>
        <Link :href="route('clients.create')"
              class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
          Crear cliente
        </Link>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
              <th class="px-5 py-3">Cliente</th>
              <th class="px-5 py-3">Tipo</th>
              <th class="px-5 py-3">Email</th>
              <th class="px-5 py-3">Ciudad</th>
              <th class="px-5 py-3 text-center">Proyectos</th>
              <th class="px-5 py-3 text-center">Estado</th>
              <th class="px-5 py-3 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="client in clients.data" :key="client.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3.5">
                <Link :href="route('clients.show', client.id)"
                      class="font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                  {{ client.name }}
                </Link>
                <p class="font-mono text-xs text-gray-400">{{ client.rut }}</p>
              </td>
              <td class="px-5 py-3.5">
                <Badge :color="client.type === 'company' ? 'blue' : 'purple'">
                  {{ client.type_name }}
                </Badge>
              </td>
              <td class="px-5 py-3.5 text-gray-500">{{ client.email || '—' }}</td>
              <td class="px-5 py-3.5 text-gray-500">{{ client.city || '—' }}</td>
              <td class="px-5 py-3.5 text-center font-medium text-gray-700">{{ client.projects_count }}</td>
              <td class="px-5 py-3.5 text-center">
                <Badge :color="client.active ? 'green' : 'red'">
                  {{ client.active ? 'Activo' : 'Inactivo' }}
                </Badge>
              </td>
              <td class="px-5 py-3.5 text-right">
                <div class="flex items-center justify-end gap-2">
                  <Link :href="route('clients.show', client.id)"
                        class="rounded-md px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-100 transition-colors">
                    Ver
                  </Link>
                  <Link :href="route('clients.edit', client.id)"
                        class="rounded-md px-2.5 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-50 transition-colors">
                    Editar
                  </Link>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <div v-if="clients.links?.length > 3" class="border-t border-gray-100 px-5 py-4 flex items-center gap-1 flex-wrap">
        <template v-for="link in clients.links" :key="link.label">
          <Link
            v-if="link.url"
            :href="link.url"
            class="rounded-md px-3 py-1.5 text-xs font-medium transition-colors"
            :class="link.active ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
            v-html="link.label"
            preserve-scroll
          />
          <span v-else class="rounded-md px-3 py-1.5 text-xs text-gray-400 cursor-not-allowed" v-html="link.label" />
        </template>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
  clients: Object,
  filters: Object,
});

const search = ref(props.filters?.search ?? '');
const type   = ref(props.filters?.type ?? '');

let searchTimeout = null;
function applyFilters() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get(route('clients.index'), {
      search: search.value || undefined,
      type:   type.value   || undefined,
    }, { preserveState: true, replace: true });
  }, 300);
}

function clearFilters() {
  search.value = '';
  type.value   = '';
  router.get(route('clients.index'), {}, { preserveState: false });
}
</script>
