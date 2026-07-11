<template>
  <AppLayout title="Usuarios">
    <template #actions>
      <Link :href="route('users.create')"
            class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Nuevo usuario
      </Link>
    </template>

    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
      <div class="border-b border-gray-100 px-5 py-4">
        <h2 class="text-sm font-semibold text-gray-900">Equipo
          <span class="ml-1.5 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
            {{ users.length }}
          </span>
        </h2>
      </div>

      <ul class="divide-y divide-gray-50">
        <li v-for="u in users" :key="u.id" class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors">
          <!-- Avatar -->
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-sm font-bold text-white"
               :class="u.is_admin ? 'bg-indigo-600' : 'bg-slate-500'">
            {{ u.initials }}
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
              <p class="text-sm font-semibold text-gray-900">{{ u.name }}</p>
              <span v-if="u.id === currentUserId"
                    class="rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 ring-1 ring-green-200">
                Tú
              </span>
            </div>
            <p class="text-xs text-gray-500">{{ u.email }}</p>
          </div>

          <!-- Rol -->
          <div class="shrink-0">
            <Badge :color="u.is_admin ? 'indigo' : 'gray'">{{ u.role_label }}</Badge>
          </div>

          <!-- Fecha -->
          <p class="shrink-0 text-xs text-gray-400 hidden sm:block">Desde {{ u.created_at }}</p>

          <!-- Acciones -->
          <div class="flex shrink-0 items-center gap-2">
            <Link :href="route('users.edit', u.id)"
                  class="rounded-md px-2.5 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-50 transition-colors">
              Editar
            </Link>
            <button
              v-if="u.id !== currentUserId"
              @click="deleteUser(u)"
              class="rounded-md px-2.5 py-1.5 text-xs font-medium text-red-500 hover:bg-red-50 transition-colors">
              Eliminar
            </button>
          </div>
        </li>
      </ul>

      <div v-if="users.length === 0" class="py-12 text-center text-sm text-gray-400">
        No hay usuarios registrados.
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';

defineProps({ users: Array });

const page          = usePage();
const currentUserId = computed(() => page.props.auth.user?.id);

function deleteUser(u) {
  if (!confirm(`¿Eliminar a ${u.name}? Esta acción no se puede deshacer.`)) return;
  router.delete(route('users.destroy', u.id));
}
</script>
