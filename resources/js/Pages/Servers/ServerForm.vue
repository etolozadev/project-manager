<template>
  <div class="mx-auto max-w-3xl space-y-6">
    <form @submit.prevent="$emit('submit')">

      <!-- ── Info general ─────────────────────────────────── -->
      <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
        <h2 class="mb-5 text-sm font-semibold text-gray-900">Información del servidor</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Proyecto <span class="text-red-500">*</span></label>
            <select v-model="form.project_id" :class="inputClass">
              <option value="">Seleccionar proyecto...</option>
              <option v-for="p in projects" :key="p.id" :value="p.id">
                {{ p.name }} — {{ p.client_name }}
              </option>
            </select>
            <p v-if="errors.project_id" class="mt-1 text-xs text-red-600">{{ errors.project_id }}</p>
          </div>

          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre del servidor <span class="text-red-500">*</span></label>
            <input type="text" v-model="form.name" :class="inputClass"
                   placeholder="Ej: Servidor de producción"/>
            <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo</label>
            <select v-model="form.type" :class="inputClass">
              <option value="vps">VPS</option>
              <option value="shared">Hosting compartido</option>
              <option value="managed">Hosting administrado</option>
              <option value="dedicated">Servidor dedicado</option>
              <option value="cloud">Cloud (AWS/GCP/Azure)</option>
              <option value="other">Otro</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Proveedor</label>
            <input type="text" v-model="form.provider" :class="inputClass"
                   placeholder="Ej: DigitalOcean, Hetzner, AWS"/>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Dirección IP</label>
            <input type="text" v-model="form.ip_address" :class="inputClass"
                   placeholder="Ej: 192.168.1.1" autocomplete="off"/>
            <p v-if="errors.ip_address" class="mt-1 text-xs text-red-600">{{ errors.ip_address }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Hostname</label>
            <input type="text" v-model="form.hostname" :class="inputClass"
                   placeholder="Ej: server1.example.com"/>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Dominio</label>
            <input type="text" v-model="form.domain" :class="inputClass"
                   placeholder="Ej: mi-app.cl"/>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">URL del proyecto</label>
            <input type="url" v-model="form.url" :class="inputClass"
                   placeholder="https://mi-app.cl"/>
            <p v-if="errors.url" class="mt-1 text-xs text-red-600">{{ errors.url }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado</label>
            <select v-model="form.status" :class="inputClass">
              <option value="active">Activo</option>
              <option value="inactive">Inactivo</option>
              <option value="expired">Vencido</option>
            </select>
          </div>
        </div>
      </div>

      <!-- ── Stack técnico ─────────────────────────────────── -->
      <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
        <h2 class="mb-5 text-sm font-semibold text-gray-900">Stack técnico</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Sistema operativo</label>
            <input type="text" v-model="form.os" :class="inputClass"
                   placeholder="Ej: Ubuntu 22.04"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">PHP</label>
            <input type="text" v-model="form.php_version" :class="inputClass"
                   placeholder="Ej: 8.3"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Web server</label>
            <input type="text" v-model="form.web_server" :class="inputClass"
                   placeholder="nginx / apache / caddy"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Base de datos</label>
            <select v-model="form.db_type" :class="inputClass">
              <option value="">Sin especificar</option>
              <option value="mysql">MySQL</option>
              <option value="postgresql">PostgreSQL</option>
              <option value="mariadb">MariaDB</option>
              <option value="sqlite">SQLite</option>
              <option value="other">Otro</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Versión BD</label>
            <input type="text" v-model="form.db_version" :class="inputClass" placeholder="Ej: 8.0"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Panel de control</label>
            <select v-model="form.panel" :class="inputClass">
              <option value="none">Sin panel</option>
              <option value="cpanel">cPanel</option>
              <option value="plesk">Plesk</option>
              <option value="forge">Laravel Forge</option>
              <option value="ploi">Ploi</option>
              <option value="runcloud">RunCloud</option>
              <option value="other">Otro</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Usuario SSH</label>
            <input type="text" v-model="form.ssh_user" :class="inputClass" autocomplete="off"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Puerto SSH</label>
            <input type="number" v-model="form.ssh_port" :class="inputClass" min="1" max="65535"/>
          </div>
        </div>
      </div>

      <!-- ── Vencimientos ──────────────────────────────────── -->
      <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
        <h2 class="mb-2 text-sm font-semibold text-gray-900">Fechas de vencimiento</h2>
        <p class="mb-4 text-xs text-gray-400">Recibirás alertas 30 días antes del vencimiento.</p>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Vencimiento hosting</label>
            <input type="date" v-model="form.hosting_expires_at" :class="inputClass"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Vencimiento dominio</label>
            <input type="date" v-model="form.domain_expires_at" :class="inputClass"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Vencimiento SSL</label>
            <input type="date" v-model="form.ssl_expires_at" :class="inputClass"/>
          </div>
        </div>
      </div>

      <!-- ── Credenciales cifradas ─────────────────────────── -->
      <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-yellow-200 border border-yellow-100">
        <div class="flex items-center gap-2 mb-2">
          <svg class="h-4 w-4 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
          </svg>
          <h2 class="text-sm font-semibold text-gray-900">Credenciales de acceso</h2>
        </div>
        <p class="mb-4 text-xs text-gray-400">
          Estos datos se cifran con AES-256 antes de guardarse en la base de datos.
          Solo tú puedes verlos al acceder a este servidor.
        </p>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña SSH</label>
            <input type="password" v-model="form.cred_ssh_password" :class="inputClass" autocomplete="new-password"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">URL del panel</label>
            <input type="url" v-model="form.cred_panel_url" :class="inputClass"
                   placeholder="https://panel.proveedor.com"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Usuario del panel</label>
            <input type="text" v-model="form.cred_panel_user" :class="inputClass" autocomplete="off"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña del panel</label>
            <input type="password" v-model="form.cred_panel_password" :class="inputClass" autocomplete="new-password"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Usuario BD</label>
            <input type="text" v-model="form.cred_db_user" :class="inputClass" autocomplete="off"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña BD</label>
            <input type="password" v-model="form.cred_db_password" :class="inputClass" autocomplete="new-password"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre de la BD</label>
            <input type="text" v-model="form.cred_db_name" :class="inputClass" autocomplete="off"/>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Notas adicionales</label>
            <input type="text" v-model="form.cred_extra" :class="inputClass"
                   placeholder="Otras credenciales o tokens..."/>
          </div>
        </div>
      </div>

      <!-- ── Notas ─────────────────────────────────────────── -->
      <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Notas técnicas</label>
        <textarea v-model="form.notes" rows="4" :class="inputClass + ' resize-none'"
                  placeholder="Configuraciones especiales, comandos útiles, historial de cambios..."/>
      </div>

      <!-- ── Acciones ──────────────────────────────────────── -->
      <div class="flex items-center justify-end gap-3">
        <Link :href="route('servers.index')"
              class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
          Cancelar
        </Link>
        <button type="submit" :disabled="processing"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors disabled:opacity-60">
          {{ processing ? 'Guardando...' : 'Guardar servidor' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  projects:   Array,
  form:       Object,
  errors:     Object,
  processing: Boolean,
  preselectedProject: [String, Number],
});

defineEmits(['submit']);

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';
</script>
