<template>
  <AppLayout
    title="Nuevo proyecto"
    :breadcrumbs="[{ label: 'Proyectos', href: route('projects.index') }, { label: 'Nuevo proyecto' }]"
  >
    <div class="mx-auto max-w-2xl">
      <form @submit.prevent="submit" class="space-y-6">

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <h2 class="mb-5 text-sm font-semibold text-gray-900">Información del proyecto</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Cliente <span class="text-red-500">*</span></label>
              <select v-model="form.client_id" :class="inputClass">
                <option value="">Seleccionar cliente...</option>
                <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }} — {{ c.rut }}</option>
              </select>
              <p v-if="form.errors.client_id" class="mt-1.5 text-xs text-red-600">{{ form.errors.client_id }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Nombre del proyecto <span class="text-red-500">*</span></label>
              <input type="text" v-model="form.name" :class="inputClass"
                     placeholder="Ej: Sistema de gestión de inventario"/>
              <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-600">{{ form.errors.name }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
              <textarea v-model="form.description" rows="3" :class="inputClass + ' resize-none'"
                        placeholder="Describe brevemente el alcance del proyecto..."/>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado <span class="text-red-500">*</span></label>
              <select v-model="form.status" :class="inputClass">
                <option value="draft">Borrador</option>
                <option value="active">En progreso</option>
                <option value="paused">Pausado</option>
              </select>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <h2 class="mb-5 text-sm font-semibold text-gray-900">Fechas</h2>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de inicio</label>
              <input type="date" v-model="form.start_date" :class="inputClass"/>
              <p v-if="form.errors.start_date" class="mt-1.5 text-xs text-red-600">{{ form.errors.start_date }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha de término</label>
              <input type="date" v-model="form.end_date" :class="inputClass"/>
              <p v-if="form.errors.end_date" class="mt-1.5 text-xs text-red-600">{{ form.errors.end_date }}</p>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <h2 class="mb-5 text-sm font-semibold text-gray-900">Presupuesto</h2>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Monto <span class="text-red-500">*</span></label>
              <input type="text" v-model="form.budget_amount" :class="inputClass" placeholder="1500000"/>
              <p class="mt-1 text-xs text-gray-400">Sin puntos ni símbolos. Ej: 1500000</p>
              <p v-if="form.errors.budget_amount" class="mt-1 text-xs text-red-600">{{ form.errors.budget_amount }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Moneda</label>
              <select v-model="form.currency" :class="inputClass">
                <option value="CLP">CLP — Peso chileno</option>
                <option value="USD">USD — Dólar</option>
              </select>
            </div>
            <div class="sm:col-span-3">
              <label class="flex cursor-pointer items-center gap-3">
                <input type="checkbox" v-model="form.budget_includes_vat"
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"/>
                <div>
                  <p class="text-sm font-medium text-gray-900">El monto incluye IVA (19%)</p>
                  <p class="text-xs text-gray-500">Si está desmarcado, el IVA se calcula aparte al generar la cotización.</p>
                </div>
              </label>
            </div>
          </div>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <label class="block text-sm font-medium text-gray-700 mb-1.5">Notas internas</label>
          <textarea v-model="form.notes" rows="3" :class="inputClass + ' resize-none'"
                    placeholder="Observaciones técnicas, contexto del cliente, etc."/>
        </div>

        <div class="flex items-center justify-end gap-3">
          <Link :href="route('projects.index')"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            Cancelar
          </Link>
          <button type="submit" :disabled="form.processing"
                  class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors disabled:opacity-60">
            {{ form.processing ? 'Creando...' : 'Crear proyecto' }}
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
  clients:           Array,
  preselectedClient: [String, Number],
});

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';

const form = useForm({
  client_id:          props.preselectedClient ?? '',
  name:               '',
  description:        '',
  status:             'draft',
  start_date:         '',
  end_date:           '',
  budget_amount:      '',
  currency:           'CLP',
  budget_includes_vat: false,
  notes:              '',
});

function submit() {
  form.post(route('projects.store'));
}
</script>
