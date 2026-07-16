<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-white">
    <!-- Logo -->
    <div class="mb-6 flex flex-col items-center">
      <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white shadow-md ring-1 ring-gray-100">
        <img src="/logo.png" alt="FlowDesk" class="h-14 w-14 object-contain" @error="logoError = true" v-if="!logoError" />
        <span v-else class="text-2xl font-bold text-indigo-600">FD</span>
      </div>
      <span class="mt-2 text-sm font-semibold tracking-wide text-gray-500">FlowDesk</span>
    </div>

    <!-- Card -->
    <div class="w-full max-w-sm rounded-2xl bg-white px-8 py-8 shadow-lg ring-1 ring-gray-100">
      <!-- Session status -->
      <div v-if="status" class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
        {{ status }}
      </div>

      <form @submit.prevent="submit" novalidate>
        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">
            Correo electrónico
          </label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            autofocus
            autocomplete="username"
            class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm shadow-sm placeholder-gray-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
            :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400': form.errors.email }"
          />
          <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
        </div>

        <!-- Password -->
        <div class="mt-4">
          <label for="password" class="block text-sm font-medium text-gray-700">
            Contraseña
          </label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            autocomplete="current-password"
            class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm shadow-sm placeholder-gray-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-indigo-500"
            :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-400': form.errors.password }"
          />
          <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
        </div>

        <!-- Remember me -->
        <div class="mt-4 flex items-center">
          <input
            id="remember_me"
            v-model="form.remember"
            type="checkbox"
            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
          />
          <label for="remember_me" class="ml-2 block text-sm text-gray-600">
            Recordarme
          </label>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex items-center justify-between">
          <a
            v-if="canResetPassword"
            :href="route('password.request')"
            class="text-sm text-gray-500 underline hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded"
          >
            ¿Olvidaste tu contraseña?
          </a>
          <span v-else />

          <button
            type="submit"
            :disabled="form.processing"
            class="rounded-lg bg-gray-900 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Iniciar sesión
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

defineOptions({ layout: null });

const props = defineProps({
  canResetPassword: Boolean,
  status: String,
});

const logoError = ref(false);

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

function submit() {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
}
</script>
