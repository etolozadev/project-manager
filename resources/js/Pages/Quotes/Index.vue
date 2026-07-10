<template>
  <AppLayout title="Cotizaciones">
    <template #actions>
      <Link :href="route('quotes.create')"
            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Nueva cotización
      </Link>
    </template>

    <!-- Resumen de estados -->
    <div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
      <button
        v-for="item in statusSummary" :key="item.value"
        @click="filterByStatus(item.value)"
        class="rounded-xl border bg-white p-4 text-left transition-all hover:shadow-sm"
        :class="filters.status === item.value ? 'border-indigo-400 ring-2 ring-indigo-200' : 'border-gray-100'"
      >
        <p class="text-2xl font-bold text-gray-900">{{ summary[item.key] ?? 0 }}</p>
        <p class="mt-0.5 text-xs font-medium" :class="item.textColor">{{ item.label }}</p>
      </button>
    </div>

    <!-- Filtros -->
    <div class="mb-5 flex flex-wrap gap-3">
      <div class="flex flex-1 items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 min-w-48">
        <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
        </svg>
        <input type="text" v-model="filters.search" @input="applyFilters"
               placeholder="Buscar por número, título o cliente..."
               class="w-full bg-transparent text-sm text-gray-900 placeholder-gray-400 outline-none"/>
      </div>
      <select v-model="filters.status" @change="applyFilters"
              class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Todos los estados</option>
        <option value="draft">Borrador</option>
        <option value="sent">Enviada</option>
        <option value="approved">Aprobada</option>
        <option value="rejected">Rechazada</option>
      </select>
      <select v-model="filters.client_id" @change="applyFilters"
              class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Todos los clientes</option>
        <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <button v-if="hasActiveFilters" @click="clearFilters"
              class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Limpiar
      </button>
    </div>

    <!-- Tabla -->
    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
      <div v-if="quotes.data.length === 0"
           class="flex flex-col items-center justify-center py-16 text-center">
        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100">
          <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
        </div>
        <p class="text-sm font-medium text-gray-900">No hay cotizaciones</p>
        <p class="mt-1 text-sm text-gray-500">Crea tu primera cotización para un cliente.</p>
        <Link :href="route('quotes.create')"
              class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
          Crear cotización
        </Link>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
              <th class="px-5 py-3">Número / Título</th>
              <th class="px-5 py-3">Cliente</th>
              <th class="px-5 py-3 text-center">Estado</th>
              <th class="px-5 py-3 text-right">Total</th>
              <th class="px-5 py-3">Vigencia</th>
              <th class="px-5 py-3 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="quote in quotes.data" :key="quote.id"
                class="hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3.5">
                <Link :href="route('quotes.show', quote.id)"
                      class="font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                  {{ quote.title }}
                </Link>
                <p class="font-mono text-xs text-gray-400">{{ quote.quote_number }}</p>
              </td>
              <td class="px-5 py-3.5 text-gray-600">{{ quote.client_name }}</td>
              <td class="px-5 py-3.5 text-center">
                <Badge :color="quote.status_color">{{ quote.status_name }}</Badge>
              </td>
              <td class="px-5 py-3.5 text-right font-mono font-semibold text-gray-900">
                {{ quote.formatted_total }}
              </td>
              <td class="px-5 py-3.5 text-gray-500 text-xs">
                {{ quote.valid_until ?? '—' }}
              </td>
              <td class="px-5 py-3.5 text-right">
                <div class="flex items-center justify-end gap-2">
                  <Link :href="route('quotes.show', quote.id)"
                        class="rounded-md px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-100 transition-colors">
                    Ver
                  </Link>
                  <Link v-if="quote.is_editable" :href="route('quotes.edit', quote.id)"
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
      <div v-if="quotes.links?.length > 3" class="border-t border-gray-100 px-5 py-4 flex items-center gap-1 flex-wrap">
        <template v-for="link in quotes.links" :key="link.label">
          <Link v-if="link.url" :href="link.url"
                class="rounded-md px-3 py-1.5 text-xs font-medium transition-colors"
                :class="link.active ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                v-html="link.label" preserve-scroll/>
          <span v-else class="rounded-md px-3 py-1.5 text-xs text-gray-400 cursor-not-allowed" v-html="link.label"/>
        </template>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
  quotes:  Object,
  clients: Array,
  filters: Object,
  summary: Object,
});

const statusSummary = [
  { value: 'draft',    key: 'draft',    label: 'Borrador',  textColor: 'text-gray-500' },
  { value: 'sent',     key: 'sent',     label: 'Enviadas',  textColor: 'text-blue-600' },
  { value: 'approved', key: 'approved', label: 'Aprobadas', textColor: 'text-green-600' },
  { value: 'rejected', key: 'rejected', label: 'Rechazadas',textColor: 'text-red-500' },
];

const filters = ref({
  search:    props.filters?.search    ?? '',
  status:    props.filters?.status    ?? '',
  client_id: props.filters?.client_id ?? '',
});

const hasActiveFilters = computed(() =>
  Object.values(filters.value).some(v => v !== '')
);

let searchTimeout = null;
function applyFilters() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get(route('quotes.index'), {
      search:    filters.value.search    || undefined,
      status:    filters.value.status    || undefined,
      client_id: filters.value.client_id || undefined,
    }, { preserveState: true, replace: true });
  }, 300);
}

function filterByStatus(status) {
  filters.value.status = filters.value.status === status ? '' : status;
  applyFilters();
}

function clearFilters() {
  filters.value = { search: '', status: '', client_id: '' };
  router.get(route('quotes.index'), {}, { preserveState: false });
}
</script>
