<template>
  <AppLayout
    :title="`Editar — ${user.name}`"
    :breadcrumbs="[{ label: 'Usuarios', href: route('users.index') }, { label: user.name }]"
  >
    <div class="mx-auto max-w-xl">
      <form @submit.prevent="submit" class="space-y-6">

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <h2 class="mb-5 text-sm font-semibold text-gray-900">Datos del usuario</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre completo <span class="text-red-500">*</span></label>
              <input type="text" v-model="form.name" :class="inputClass"/>
              <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
              <input type="email" v-model="form.email" :class="inputClass"/>
              <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Nueva contraseña</label>
              <input type="password" v-model="form.password" :class="inputClass"
                     placeholder="Dejar vacío para mantener la actual" autocomplete="new-password"/>
              <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Rol <span class="text-red-500">*</span></label>
              <div class="grid grid-cols-2 gap-3">
                <label v-for="opt in roleOptions" :key="opt.value"
                       class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition-all"
                       :class="form.role === opt.value ? 'border-indigo-400 bg-indigo-50 ring-2 ring-indigo-200' : 'border-gray-200 hover:border-gray-300'">
                  <input type="radio" :value="opt.value" v-model="form.role"
                         class="mt-0.5 h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500"/>
                  <div>
                    <p class="text-sm font-semibold text-gray-900">{{ opt.label }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ opt.description }}</p>
                  </div>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Proyectos asignados (developer) -->
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
          <div v-if="form.role === 'developer'" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
            <h2 class="mb-1 text-sm font-semibold text-gray-900">Proyectos asignados</h2>
            <p class="mb-4 text-xs text-gray-400">El desarrollador solo podrá ver los proyectos seleccionados.</p>
            <div class="space-y-2 max-h-64 overflow-y-auto">
              <label v-for="p in projects" :key="p.id"
                     class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50 transition-colors"
                     :class="form.projects.includes(p.id) ? 'border-indigo-300 bg-indigo-50' : ''">
                <input type="checkbox" :value="p.id" v-model="form.projects"
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"/>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900">{{ p.name }}</p>
                  <p class="text-xs text-gray-400">{{ p.client_name }} · {{ p.code }}</p>
                </div>
              </label>
            </div>
          </div>
        </Transition>

        <div class="flex items-center justify-end gap-3">
          <Link :href="route('users.index')"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            Cancelar
          </Link>
          <button type="submit" :disabled="form.processing"
                  class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors disabled:opacity-60">
            {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  user:     Object,
  projects: Array,
});

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';

const roleOptions = [
  { value: 'admin',     label: 'Administrador', description: 'Acceso total al sistema.' },
  { value: 'developer', label: 'Desarrollador',  description: 'Solo ve proyectos asignados.' },
];

const form = useForm({
  name:     props.user.name,
  email:    props.user.email,
  password: '',
  role:     props.user.role,
  projects: props.user.project_ids ?? [],
});

function submit() {
  form.patch(route('users.update', props.user.id));
}
</script>
