<template>
  <AppLayout
    title="Mi perfil"
    :breadcrumbs="[{ label: 'Mi perfil' }]"
  >
    <div class="mx-auto max-w-2xl space-y-6">

      <!-- Alerta de éxito -->
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="status === 'profile-updated'"
             class="flex items-center gap-3 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-800 ring-1 ring-green-200">
          <svg class="h-5 w-5 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Perfil actualizado correctamente.
        </div>
        <div v-else-if="status === 'password-updated'"
             class="flex items-center gap-3 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-800 ring-1 ring-green-200">
          <svg class="h-5 w-5 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Contraseña actualizada correctamente.
        </div>
      </Transition>

      <!-- ── Información del perfil ─────────────────────── -->
      <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
        <div class="mb-5">
          <h2 class="text-sm font-semibold text-gray-900">Información del perfil</h2>
          <p class="mt-1 text-xs text-gray-500">Actualiza tu nombre y correo electrónico.</p>
        </div>

        <form @submit.prevent="submitProfile" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
              Nombre <span class="text-red-500">*</span>
            </label>
            <input type="text" v-model="profileForm.name" :class="inputClass" autocomplete="name"/>
            <p v-if="profileForm.errors.name" class="mt-1.5 text-xs text-red-600">
              {{ profileForm.errors.name }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
              Correo electrónico <span class="text-red-500">*</span>
            </label>
            <input type="email" v-model="profileForm.email" :class="inputClass" autocomplete="username"/>
            <p v-if="profileForm.errors.email" class="mt-1.5 text-xs text-red-600">
              {{ profileForm.errors.email }}
            </p>
          </div>

          <!-- Aviso si el email no está verificado -->
          <div v-if="user.email_verified_at === null"
               class="rounded-lg bg-yellow-50 px-4 py-3 text-xs text-yellow-700 ring-1 ring-yellow-200">
            Tu correo electrónico no está verificado.
            <Link :href="route('verification.send')" method="post" as="button"
                  class="ml-1 underline hover:text-yellow-900 transition-colors">
              Reenviar verificación
            </Link>
          </div>

          <div class="flex justify-end">
            <button type="submit" :disabled="profileForm.processing"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors disabled:opacity-60">
              {{ profileForm.processing ? 'Guardando...' : 'Guardar cambios' }}
            </button>
          </div>
        </form>
      </div>

      <!-- ── Cambiar contraseña ──────────────────────────── -->
      <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
        <div class="mb-5">
          <h2 class="text-sm font-semibold text-gray-900">Cambiar contraseña</h2>
          <p class="mt-1 text-xs text-gray-500">
            Usa una contraseña larga y segura. Mínimo 8 caracteres con letras y números.
          </p>
        </div>

        <form @submit.prevent="submitPassword" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña actual</label>
            <input type="password" v-model="passwordForm.current_password" :class="inputClass" autocomplete="current-password"/>
            <p v-if="passwordForm.errors.current_password" class="mt-1.5 text-xs text-red-600">
              {{ passwordForm.errors.current_password }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nueva contraseña</label>
            <input type="password" v-model="passwordForm.password" :class="inputClass" autocomplete="new-password"/>
            <p v-if="passwordForm.errors.password" class="mt-1.5 text-xs text-red-600">
              {{ passwordForm.errors.password }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Confirmar nueva contraseña</label>
            <input type="password" v-model="passwordForm.password_confirmation" :class="inputClass" autocomplete="new-password"/>
            <p v-if="passwordForm.errors.password_confirmation" class="mt-1.5 text-xs text-red-600">
              {{ passwordForm.errors.password_confirmation }}
            </p>
          </div>

          <div class="flex justify-end">
            <button type="submit" :disabled="passwordForm.processing"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors disabled:opacity-60">
              {{ passwordForm.processing ? 'Actualizando...' : 'Actualizar contraseña' }}
            </button>
          </div>
        </form>
      </div>

      <!-- ── Eliminar cuenta ────────────────────────────── -->
      <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-red-100">
        <div class="mb-5">
          <h2 class="text-sm font-semibold text-red-700">Eliminar cuenta</h2>
          <p class="mt-1 text-xs text-gray-500">
            Una vez eliminada, todos los datos serán borrados permanentemente. Esta acción no se puede deshacer.
          </p>
        </div>

        <div v-if="!showDeleteForm">
          <button @click="showDeleteForm = true"
                  class="rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
            Eliminar mi cuenta
          </button>
        </div>

        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 -translate-y-1" enter-to-class="opacity-100 translate-y-0">
          <form v-if="showDeleteForm" @submit.prevent="submitDelete" class="space-y-4">
            <div class="rounded-lg bg-red-50 px-4 py-3 text-xs text-red-700 ring-1 ring-red-200">
              Confirma tu contraseña para eliminar la cuenta permanentemente.
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña</label>
              <input type="password" v-model="deleteForm.password" :class="inputClass"
                     placeholder="Ingresa tu contraseña para confirmar" autocomplete="current-password"/>
              <p v-if="deleteForm.errors.password" class="mt-1.5 text-xs text-red-600">
                {{ deleteForm.errors.password }}
              </p>
            </div>
            <div class="flex items-center gap-3">
              <button type="button" @click="showDeleteForm = false; deleteForm.reset()"
                      class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
              </button>
              <button type="submit" :disabled="deleteForm.processing"
                      class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors disabled:opacity-60">
                {{ deleteForm.processing ? 'Eliminando...' : 'Sí, eliminar cuenta' }}
              </button>
            </div>
          </form>
        </Transition>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  user:   Object,
  status: String,
});

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';

// ── Formulario de perfil ─────────────────────────────────
const profileForm = useForm({
  name:  props.user.name,
  email: props.user.email,
});

function submitProfile() {
  profileForm.patch(route('profile.update'));
}

// ── Formulario de contraseña ─────────────────────────────
const passwordForm = useForm({
  current_password:      '',
  password:              '',
  password_confirmation: '',
});

function submitPassword() {
  passwordForm.put(route('password.update'), {
    onSuccess: () => passwordForm.reset(),
  });
}

// ── Eliminar cuenta ──────────────────────────────────────
const showDeleteForm = ref(false);

const deleteForm = useForm({
  password: '',
});

function submitDelete() {
  deleteForm.delete(route('profile.destroy'), {
    onSuccess: () => deleteForm.reset(),
  });
}
</script>
