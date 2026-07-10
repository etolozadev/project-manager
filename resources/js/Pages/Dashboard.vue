<template>
  <AppLayout title="Dashboard">
    <!-- Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <StatCard label="Proyectos activos" :value="stats.active_projects" color="indigo">
        <template #icon>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/>
          </svg>
        </template>
      </StatCard>

      <StatCard label="Clientes activos" :value="stats.total_clients" color="green">
        <template #icon>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
          </svg>
        </template>
      </StatCard>

      <StatCard label="Cotizaciones pendientes" :value="stats.pending_quotes" color="yellow">
        <template #icon>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
          </svg>
        </template>
      </StatCard>

      <StatCard label="Tareas vencidas" :value="stats.overdue_tasks" color="red">
        <template #icon>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
          </svg>
        </template>
      </StatCard>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
      <!-- Proyectos activos -->
      <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
          <h2 class="text-sm font-semibold text-gray-900">Proyectos activos</h2>
          <Link :href="route('projects.index', { status: 'active' })"
                class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
            Ver todos →
          </Link>
        </div>
        <ul class="divide-y divide-gray-50 px-2 py-2">
          <li v-for="project in activeProjects" :key="project.id">
            <Link :href="route('projects.show', project.id)"
                  class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-gray-50 transition-colors">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50">
                <span class="text-xs font-bold text-indigo-600">
                  {{ project.name.slice(0, 2).toUpperCase() }}
                </span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="truncate text-sm font-medium text-gray-900">{{ project.name }}</p>
                <p class="text-xs text-gray-500">{{ project.client_name }} · {{ project.code }}</p>
              </div>
              <div class="w-20 shrink-0">
                <ProgressBar :value="project.progress"
                  :color="project.progress >= 75 ? 'green' : project.progress >= 40 ? 'blue' : 'yellow'"/>
              </div>
            </Link>
          </li>
          <li v-if="!activeProjects.length" class="px-3 py-8 text-center text-sm text-gray-400">
            No hay proyectos activos.
          </li>
        </ul>
      </div>

      <!-- Tareas vencidas -->
      <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
          <h2 class="text-sm font-semibold text-gray-900">Tareas vencidas</h2>
          <Badge v-if="stats.overdue_tasks > 0" color="red">{{ stats.overdue_tasks }} pendientes</Badge>
        </div>
        <ul class="divide-y divide-gray-50 px-2 py-2">
          <li v-for="task in overdueTasks" :key="task.id">
            <Link :href="route('projects.show', task.project_id)"
                  class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-gray-50 transition-colors">
              <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-50">
                <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="truncate text-sm font-medium text-gray-900">{{ task.title }}</p>
                <p class="text-xs text-gray-500">{{ task.project_name }}</p>
              </div>
              <div class="shrink-0 text-right">
                <p class="text-xs font-medium text-red-600">{{ task.due_date }}</p>
                <Badge :color="task.priority_color">{{ task.priority_name }}</Badge>
              </div>
            </Link>
          </li>
          <li v-if="!overdueTasks.length" class="px-3 py-8 text-center text-sm text-gray-400">
            Sin tareas vencidas. ¡Todo al día!
          </li>
        </ul>
      </div>
    </div>

    <!-- Cotizaciones pendientes -->
    <div v-if="pendingQuotes.length" class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
      <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <h2 class="text-sm font-semibold text-gray-900">Cotizaciones pendientes de acción</h2>
        <Link :href="route('quotes.index')" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
          Ver todas →
        </Link>
      </div>
      <ul class="divide-y divide-gray-50 px-2 py-2">
        <li v-for="quote in pendingQuotes" :key="quote.id">
          <Link :href="route('quotes.show', quote.id)"
                class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-gray-50 transition-colors">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-yellow-50">
              <svg class="h-4 w-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="truncate text-sm font-medium text-gray-900">{{ quote.title }}</p>
              <p class="text-xs text-gray-500">{{ quote.client_name }} · {{ quote.quote_number }}</p>
            </div>
            <div class="shrink-0 text-right">
              <p class="font-mono text-sm font-semibold text-gray-900">{{ quote.formatted_total }}</p>
              <Badge :color="quote.status_color">{{ quote.status_name }}</Badge>
            </div>
          </Link>
        </li>
      </ul>
    </div>

    <!-- Clientes recientes -->
    <div class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
      <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <h2 class="text-sm font-semibold text-gray-900">Clientes recientes</h2>
        <Link :href="route('clients.index')" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
          Ver todos →
        </Link>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b border-gray-50 text-left text-xs font-medium uppercase tracking-wider text-gray-400">
              <th class="px-5 py-3">Cliente</th>
              <th class="px-5 py-3">RUT</th>
              <th class="px-5 py-3">Ciudad</th>
              <th class="px-5 py-3">Tipo</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="client in recentClients" :key="client.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-5 py-3">
                <Link :href="route('clients.show', client.id)"
                      class="font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                  {{ client.name }}
                </Link>
                <p v-if="client.contact_person" class="text-xs text-gray-400">{{ client.contact_person }}</p>
              </td>
              <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ client.rut }}</td>
              <td class="px-5 py-3 text-gray-500">{{ client.city || '—' }}</td>
              <td class="px-5 py-3">
                <Badge :color="client.type === 'company' ? 'blue' : 'purple'">{{ client.type_name }}</Badge>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import StatCard from '@/Components/StatCard.vue';

defineProps({
  stats:          Object,
  activeProjects: Array,
  overdueTasks:   Array,
  pendingQuotes:  Array,
  recentClients:  Array,
});
</script>
