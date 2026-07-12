<template>
  <AppLayout title="Servidores">
    <template #actions>
      <Link :href="route('servers.create')"
            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Nuevo servidor
      </Link>
    </template>

    <!-- Alerta de vencimientos -->
    <div v-if="expiringCount > 0"
         class="mb-5 flex items-center gap-3 rounded-xl bg-yellow-50 px-4 py-3 text-sm text-yellow-800 ring-1 ring-yellow-200">
      <svg class="h-5 w-5 shrink-0 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
      </svg>
      <span>
        <strong>{{ expiringCount }} servidor(es)</strong> tienen hosting o dominio próximo a vencer (30 días).
      </span>
    </div>

    <!-- Filtros -->
    <div class="mb-6 flex flex-wrap items-center gap-2 rounded-xl bg-white px-4 py-3 shadow-sm ring-1 ring-gray-100">
      <div class="flex flex-1 items-center gap-2.5 rounded-lg bg-gray-50 px-3 py-2 min-w-52 ring-1 ring-gray-200 transition-all focus-within:bg-white focus-within:ring-indigo-400 focus-within:shadow-sm">
        <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
        </svg>
        <input type="text" v-model="filters.search" @input="applyFilters"
               placeholder="Buscar por nombre, dominio, IP o proveedor..."
               class="w-full bg-transparent text-sm text-gray-900 placeholder-gray-400 outline-none"/>
      </div>
      <div class="hidden h-5 w-px bg-gray-200 sm:block"></div>
      <select v-model="filters.status" @change="applyFilters"
              class="cursor-pointer rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-600 ring-1 ring-gray-200 transition-all hover:ring-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white">
        <option value="">Todos los estados</option>
        <option value="active">Activo</option>
        <option value="inactive">Inactivo</option>
        <option value="expired">Vencido</option>
      </select>
      <button v-if="filters.search || filters.status" @click="clearFilters"
              class="flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium text-gray-400 transition-colors hover:bg-red-50 hover:text-red-500">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Limpiar
      </button>
    </div>

    <!-- Lista -->
    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
      <div v-if="servers.data.length === 0"
           class="flex flex-col items-center justify-center py-16 text-center">
        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100">
          <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z"/>
          </svg>
        </div>
        <p class="text-sm font-medium text-gray-900">No hay servidores</p>
        <Link :href="route('servers.create')"
              class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
          Agregar servidor
        </Link>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-100 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
              <th class="px-5 py-3">Servidor</th>
              <th class="px-5 py-3">Proyecto</th>
              <th class="px-5 py-3">Stack</th>
              <th class="px-5 py-3">Vencimientos</th>
              <th class="px-5 py-3 text-center">Estado</th>
              <th class="px-5 py-3 text-right">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="s in servers.data" :key="s.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3.5">
                <Link :href="route('servers.show', s.id)"
                      class="font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                  {{ s.name }}
                </Link>
                <div class="flex items-center gap-2 mt-0.5">
                  <Badge :color="s.type_color">{{ s.type_name }}</Badge>
                  <span v-if="s.provider" class="text-xs text-gray-400">{{ s.provider }}</span>
                </div>
              </td>
              <td class="px-5 py-3.5">
                <p class="text-sm text-gray-700">{{ s.project_name }}</p>
                <p class="text-xs text-gray-400">{{ s.client_name }}</p>
              </td>
              <td class="px-5 py-3.5">
                <div class="space-y-0.5 text-xs text-gray-500">
                  <p v-if="s.ip_address" class="font-mono">{{ s.ip_address }}</p>
                  <p v-if="s.domain">{{ s.domain }}</p>
                  <p v-if="s.os">{{ s.os }}</p>
                  <p v-if="s.php_version">PHP {{ s.php_version }}</p>
                </div>
              </td>
              <td class="px-5 py-3.5">
                <div class="space-y-1">
                  <ExpiryBadge v-if="s.hosting_expires_at" label="Hosting" :date="s.hosting_expires_at" :alert="s.hosting_alert"/>
                  <ExpiryBadge v-if="s.domain_expires_at"  label="Dominio" :date="s.domain_expires_at"  :alert="s.domain_alert"/>
                  <ExpiryBadge v-if="s.ssl_expires_at"     label="SSL"     :date="s.ssl_expires_at"     :alert="s.ssl_alert"/>
                  <span v-if="!s.hosting_expires_at && !s.domain_expires_at && !s.ssl_expires_at"
                        class="text-xs text-gray-300">—</span>
                </div>
              </td>
              <td class="px-5 py-3.5 text-center">
                <Badge :color="s.status_color">{{ s.status_name }}</Badge>
              </td>
              <td class="px-5 py-3.5 text-right">
                <div class="flex items-center justify-end gap-2">
                  <Link :href="route('servers.show', s.id)"
                        class="rounded-md px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-100 transition-colors">
                    Ver
                  </Link>
                  <Link :href="route('servers.edit', s.id)"
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
      <div v-if="servers.links?.length > 3" class="border-t border-gray-100 px-5 py-4 flex items-center gap-1 flex-wrap">
        <template v-for="link in servers.links" :key="link.label">
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
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import ExpiryBadge from '@/Components/ExpiryBadge.vue';

const props = defineProps({
  servers:        Object,
  filters:        Object,
  expiringCount:  Number,
});

const filters = ref({
  search: props.filters?.search ?? '',
  status: props.filters?.status ?? '',
});

let searchTimeout = null;
function applyFilters() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get(route('servers.index'), {
      search: filters.value.search || undefined,
      status: filters.value.status || undefined,
    }, { preserveState: true, replace: true });
  }, 300);
}

function clearFilters() {
  filters.value = { search: '', status: '' };
  router.get(route('servers.index'), {}, { preserveState: false });
}
</script>
