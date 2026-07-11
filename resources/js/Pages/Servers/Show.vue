<template>
  <AppLayout
    :title="server.name"
    :breadcrumbs="[{ label: 'Servidores', href: route('servers.index') }, { label: server.name }]"
  >
    <template #actions>
      <Link :href="route('servers.edit', server.id)"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
        </svg>
        Editar
      </Link>
      <button @click="confirmDelete"
              class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916"/>
        </svg>
        Eliminar
      </button>
    </template>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

      <!-- ── Panel izquierdo ────────────────────────────── -->
      <div class="space-y-4">

        <!-- Estado y tipo -->
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h2 class="text-base font-bold text-gray-900">{{ server.name }}</h2>
              <p v-if="server.provider" class="text-sm text-gray-500 mt-0.5">{{ server.provider }}</p>
            </div>
            <Badge :color="server.status_color">{{ server.status_name }}</Badge>
          </div>
          <div class="flex flex-wrap gap-2">
            <Badge :color="server.type_color">{{ server.type_name }}</Badge>
            <Badge v-if="server.panel !== 'none'" color="indigo">{{ server.panel_name }}</Badge>
          </div>
        </div>

        <!-- Proyecto -->
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Proyecto</p>
          <Link :href="route('projects.show', server.project_id)"
                class="font-medium text-gray-900 hover:text-indigo-600 transition-colors">
            {{ server.project_name }}
          </Link>
          <p class="text-xs text-gray-400 mt-0.5">{{ server.client_name }}</p>
        </div>

        <!-- Vencimientos -->
        <div v-if="server.hosting_expires_at || server.domain_expires_at || server.ssl_expires_at"
             class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">Vencimientos</p>
          <div class="space-y-2">
            <ExpiryBadge v-if="server.hosting_expires_at" label="Hosting" :date="server.hosting_expires_at" :alert="server.hosting_alert"/>
            <ExpiryBadge v-if="server.domain_expires_at"  label="Dominio" :date="server.domain_expires_at"  :alert="server.domain_alert"/>
            <ExpiryBadge v-if="server.ssl_expires_at"     label="SSL"     :date="server.ssl_expires_at"     :alert="server.ssl_alert"/>
          </div>
        </div>

        <!-- URLs de acceso rápido -->
        <div v-if="server.url || server.domain" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">Acceso rápido</p>
          <div class="space-y-2">
            <a v-if="server.url" :href="server.url" target="_blank"
               class="flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 transition-colors">
              <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
              </svg>
              <span class="truncate">{{ server.url }}</span>
            </a>
            <div v-if="server.credentials?.panel_url" class="flex items-center gap-2">
              <a :href="server.credentials.panel_url" target="_blank"
                 class="flex items-center gap-2 text-sm text-purple-600 hover:text-purple-800 transition-colors">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/>
                </svg>
                <span class="truncate">Panel de control</span>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- ── Panel derecho ──────────────────────────────── -->
      <div class="lg:col-span-2 space-y-5">

        <!-- Información técnica -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <h3 class="text-sm font-semibold text-gray-900 mb-4">Configuración del servidor</h3>
          <dl class="grid grid-cols-2 gap-x-6 gap-y-3 sm:grid-cols-3">
            <div v-if="server.ip_address">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">IP</dt>
              <dd class="mt-0.5 font-mono text-sm text-gray-900">{{ server.ip_address }}</dd>
            </div>
            <div v-if="server.hostname">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Hostname</dt>
              <dd class="mt-0.5 text-sm text-gray-900 truncate">{{ server.hostname }}</dd>
            </div>
            <div v-if="server.domain">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Dominio</dt>
              <dd class="mt-0.5 text-sm text-gray-900">{{ server.domain }}</dd>
            </div>
            <div v-if="server.os">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Sistema OS</dt>
              <dd class="mt-0.5 text-sm text-gray-900">{{ server.os }}</dd>
            </div>
            <div v-if="server.php_version">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">PHP</dt>
              <dd class="mt-0.5 text-sm text-gray-900">{{ server.php_version }}</dd>
            </div>
            <div v-if="server.web_server">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Web server</dt>
              <dd class="mt-0.5 text-sm text-gray-900 capitalize">{{ server.web_server }}</dd>
            </div>
            <div v-if="server.db_type">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Base de datos</dt>
              <dd class="mt-0.5 text-sm text-gray-900">
                {{ server.db_type_name }}
                <span v-if="server.db_version" class="text-gray-400"> {{ server.db_version }}</span>
              </dd>
            </div>
            <div v-if="server.ssh_user">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">SSH</dt>
              <dd class="mt-0.5 font-mono text-sm text-gray-900">
                {{ server.ssh_user }}@{{ server.ip_address ?? server.hostname ?? '?' }}:{{ server.ssh_port }}
              </dd>
            </div>
          </dl>
        </div>

        <!-- Credenciales -->
        <div v-if="hasCredentials" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-yellow-200">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
              <svg class="h-4 w-4 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
              </svg>
              <h3 class="text-sm font-semibold text-gray-900">Credenciales</h3>
            </div>
            <button @click="showCredentials = !showCredentials"
                    class="rounded-md px-2.5 py-1 text-xs font-medium transition-colors"
                    :class="showCredentials ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
              {{ showCredentials ? 'Ocultar' : 'Mostrar' }}
            </button>
          </div>

          <div v-if="showCredentials" class="space-y-3">
            <CredentialRow v-if="server.credentials.ssh_password"   label="Contraseña SSH"    :value="server.credentials.ssh_password"/>
            <CredentialRow v-if="server.credentials.panel_user"     label="Usuario panel"     :value="server.credentials.panel_user"/>
            <CredentialRow v-if="server.credentials.panel_password" label="Contraseña panel"  :value="server.credentials.panel_password"/>
            <CredentialRow v-if="server.credentials.db_user"        label="Usuario BD"        :value="server.credentials.db_user"/>
            <CredentialRow v-if="server.credentials.db_password"    label="Contraseña BD"     :value="server.credentials.db_password"/>
            <CredentialRow v-if="server.credentials.db_name"        label="Nombre BD"         :value="server.credentials.db_name" :copyable="false"/>
            <CredentialRow v-if="server.credentials.extra"          label="Notas extra"       :value="server.credentials.extra" :copyable="false"/>
          </div>

          <p v-if="!showCredentials" class="text-xs text-gray-400 mt-1">
            Haz clic en "Mostrar" para ver las credenciales cifradas.
          </p>
        </div>

        <!-- Notas técnicas -->
        <div v-if="server.notes" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <h3 class="text-sm font-semibold text-gray-900 mb-3">Notas técnicas</h3>
          <p class="text-sm text-gray-700 whitespace-pre-line">{{ server.notes }}</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import ExpiryBadge from '@/Components/ExpiryBadge.vue';
import CredentialRow from './CredentialRow.vue';

const props = defineProps({ server: Object });

const showCredentials = ref(false);

const hasCredentials = computed(() =>
  props.server.credentials && Object.keys(props.server.credentials).length > 0
);

function confirmDelete() {
  if (confirm(`¿Eliminar el servidor "${props.server.name}"?`)) {
    router.delete(route('servers.destroy', props.server.id));
  }
}
</script>
