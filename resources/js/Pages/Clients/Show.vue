<template>
  <AppLayout
    :title="client.name"
    :breadcrumbs="[{ label: 'Clientes', href: route('clients.index') }, { label: client.name }]"
  >
    <template #actions>
      <Link :href="route('clients.edit', client.id)"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
        </svg>
        Editar
      </Link>
    </template>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

      <!-- Info -->
      <div class="lg:col-span-1">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <div class="mb-4 flex items-center gap-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100">
              <span class="text-lg font-bold text-indigo-600">
                {{ client.name.slice(0, 2).toUpperCase() }}
              </span>
            </div>
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <Badge :color="client.type === 'company' ? 'blue' : 'purple'">{{ client.type_name }}</Badge>
                <Badge :color="client.active ? 'green' : 'red'">{{ client.active ? 'Activo' : 'Inactivo' }}</Badge>
              </div>
              <p class="mt-1 font-mono text-xs text-gray-400">{{ client.rut }}</p>
            </div>
          </div>

          <dl class="space-y-3">
            <div v-if="client.contact_person">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Contacto</dt>
              <dd class="mt-0.5 text-sm text-gray-900">{{ client.contact_person }}</dd>
            </div>
            <div v-if="client.email">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Email</dt>
              <dd class="mt-0.5 text-sm">
                <a :href="`mailto:${client.email}`" class="text-indigo-600 hover:underline">{{ client.email }}</a>
              </dd>
            </div>
            <div v-if="client.phone">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Teléfono</dt>
              <dd class="mt-0.5 text-sm text-gray-900">{{ client.phone }}</dd>
            </div>
            <div v-if="client.city || client.address">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Dirección</dt>
              <dd class="mt-0.5 text-sm text-gray-900">
                {{ [client.address, client.city, client.region].filter(Boolean).join(', ') }}
              </dd>
            </div>
            <div v-if="client.website">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Web</dt>
              <dd class="mt-0.5 text-sm">
                <a :href="client.website" target="_blank" class="text-indigo-600 hover:underline truncate block">
                  {{ client.website }}
                </a>
              </dd>
            </div>
            <div v-if="client.notes">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Notas</dt>
              <dd class="mt-0.5 text-sm text-gray-600">{{ client.notes }}</dd>
            </div>
          </dl>
        </div>
      </div>

      <!-- Proyectos -->
      <div class="lg:col-span-2">
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
          <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-gray-900">
              Proyectos
              <span class="ml-1.5 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                {{ projects.length }}
              </span>
            </h2>
            <Link :href="route('projects.create', { client_id: client.id })"
                  class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700 transition-colors">
              <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
              </svg>
              Nuevo proyecto
            </Link>
          </div>

          <div v-if="projects.length === 0" class="flex flex-col items-center justify-center py-12 text-center">
            <p class="text-sm text-gray-500">Este cliente aún no tiene proyectos.</p>
            <Link :href="route('projects.create', { client_id: client.id })"
                  class="mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">
              Crear primer proyecto →
            </Link>
          </div>

          <div v-else class="divide-y divide-gray-50">
            <div v-for="project in projects" :key="project.id" class="flex items-center gap-4 px-5 py-4">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                  <span class="font-mono text-xs text-gray-400">{{ project.code }}</span>
                  <Badge :color="project.status_color">{{ project.status_name }}</Badge>
                </div>
                <Link :href="route('projects.show', project.id)"
                      class="mt-0.5 block text-sm font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                  {{ project.name }}
                </Link>
                <p class="mt-1 text-xs text-gray-400">
                  {{ project.formatted_budget }}
                  <template v-if="project.end_date"> · Hasta {{ project.end_date }}</template>
                </p>
              </div>
              <div class="w-28 shrink-0">
                <ProgressBar :value="project.progress"
                  :color="project.progress >= 75 ? 'green' : project.progress >= 40 ? 'blue' : 'yellow'"/>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import ProgressBar from '@/Components/ProgressBar.vue';

defineProps({
  client:   Object,
  projects: Array,
});
</script>
