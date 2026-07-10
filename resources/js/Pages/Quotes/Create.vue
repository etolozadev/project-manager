<template>
  <AppLayout
    title="Nueva cotización"
    :breadcrumbs="[{ label: 'Cotizaciones', href: route('quotes.index') }, { label: 'Nueva cotización' }]"
  >
    <div class="mx-auto max-w-4xl">
      <form @submit.prevent="submit" class="space-y-6">

        <!-- Cabecera -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <h2 class="mb-5 text-sm font-semibold text-gray-900">Información general</h2>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Cliente <span class="text-red-500">*</span>
              </label>
              <select v-model="form.client_id" :class="inputClass">
                <option value="">Seleccionar cliente...</option>
                <option v-for="c in clients" :key="c.id" :value="c.id">
                  {{ c.name }} — {{ c.rut }}
                </option>
              </select>
              <p v-if="form.errors.client_id" class="mt-1 text-xs text-red-600">{{ form.errors.client_id }}</p>
            </div>

            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Título del proyecto/servicio <span class="text-red-500">*</span>
              </label>
              <input type="text" v-model="form.title" :class="inputClass"
                     placeholder="Ej: Desarrollo de sistema de gestión de inventario"/>
              <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Moneda</label>
              <select v-model="form.currency" :class="inputClass">
                <option value="CLP">CLP — Peso chileno</option>
                <option value="USD">USD — Dólar</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Válida hasta</label>
              <input type="date" v-model="form.valid_until" :class="inputClass" :min="today"/>
              <p v-if="form.errors.valid_until" class="mt-1 text-xs text-red-600">{{ form.errors.valid_until }}</p>
            </div>
          </div>
        </div>

        <!-- Ítems -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Ítems de la cotización</h2>
            <button type="button" @click="addItem"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-100 transition-colors">
              <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
              </svg>
              Agregar ítem
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full">
              <thead>
                <tr class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">
                  <th class="px-4 py-3" style="width:40%;">Descripción</th>
                  <th class="px-4 py-3" style="width:25%;">Detalle (opcional)</th>
                  <th class="px-4 py-3 text-center" style="width:10%;">Cant.</th>
                  <th class="px-4 py-3 text-right" style="width:15%;">Precio Unit.</th>
                  <th class="px-4 py-3 text-right" style="width:10%;">Subtotal</th>
                  <th class="px-4 py-3" style="width:5%;"></th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="(item, i) in form.items" :key="i" class="group">
                  <td class="px-4 py-2.5">
                    <input v-model="item.description" type="text" :class="inputClass"
                           placeholder="Ej: Módulo de autenticación"/>
                    <p v-if="form.errors[`items.${i}.description`]" class="mt-1 text-xs text-red-600">
                      {{ form.errors[`items.${i}.description`] }}
                    </p>
                  </td>
                  <td class="px-4 py-2.5">
                    <input v-model="item.detail" type="text" :class="inputClass"
                           placeholder="Especificaciones adicionales"/>
                  </td>
                  <td class="px-4 py-2.5">
                    <input v-model.number="item.quantity" type="number" min="1" :class="inputClass + ' text-center'"/>
                  </td>
                  <td class="px-4 py-2.5">
                    <input v-model.number="item.unit_price" type="number" min="0" :class="inputClass + ' text-right font-mono'"
                           :placeholder="form.currency === 'CLP' ? '500000' : '1000'"/>
                  </td>
                  <td class="px-4 py-2.5 text-right font-mono text-sm font-semibold text-gray-700">
                    {{ formatAmount(item.quantity * item.unit_price) }}
                  </td>
                  <td class="px-4 py-2.5">
                    <button type="button" @click="removeItem(i)"
                            :disabled="form.items.length === 1"
                            class="rounded-md p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                      <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Totales -->
          <div class="flex justify-end border-t border-gray-100 px-6 py-4">
            <div class="w-64 space-y-2">
              <div class="flex justify-between text-sm text-gray-600">
                <span>Subtotal neto</span>
                <span class="font-mono font-semibold">{{ formatAmount(subtotal) }}</span>
              </div>
              <div class="flex justify-between text-sm text-gray-600">
                <span>IVA ({{ form.tax_rate }}%)</span>
                <span class="font-mono font-semibold">{{ formatAmount(taxAmount) }}</span>
              </div>
              <div class="flex justify-between rounded-lg bg-indigo-600 px-3 py-2 text-white">
                <span class="font-semibold">Total</span>
                <span class="font-mono font-bold">{{ formatAmount(total) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Notas y términos -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <h2 class="mb-5 text-sm font-semibold text-gray-900">Notas y términos</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Notas para el cliente
              </label>
              <textarea v-model="form.notes" rows="3" :class="inputClass + ' resize-none'"
                        placeholder="Información adicional que quieras compartir con el cliente..."/>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Términos y condiciones
              </label>
              <textarea v-model="form.terms" rows="4" :class="inputClass + ' resize-none'"
                        placeholder="Condiciones de pago, plazos, garantías..."/>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-end gap-3">
          <Link :href="route('quotes.index')"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            Cancelar
          </Link>
          <button type="submit" :disabled="form.processing"
                  class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors disabled:opacity-60">
            {{ form.processing ? 'Guardando...' : 'Crear cotización' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  clients:      Array,
  defaultTerms: String,
});

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';
const today = new Date().toISOString().split('T')[0];

const form = useForm({
  client_id:   '',
  title:       '',
  currency:    'CLP',
  tax_rate:    19,
  tax_included: false,
  valid_until: '',
  notes:       '',
  terms:       props.defaultTerms ?? '',
  items: [
    { description: '', detail: '', quantity: 1, unit_price: 0 },
  ],
});

const subtotal = computed(() =>
  form.items.reduce((sum, item) => sum + (item.quantity || 0) * (item.unit_price || 0), 0)
);

const taxAmount = computed(() =>
  Math.round(subtotal.value * (form.tax_rate / 100))
);

const total = computed(() => subtotal.value + taxAmount.value);

function formatAmount(amount) {
  if (form.currency === 'USD') {
    return 'USD ' + (amount / 100).toFixed(2);
  }
  return '$' + new Intl.NumberFormat('es-CL').format(amount);
}

function addItem() {
  form.items.push({ description: '', detail: '', quantity: 1, unit_price: 0 });
}

function removeItem(index) {
  if (form.items.length > 1) form.items.splice(index, 1);
}

function submit() {
  form.post(route('quotes.store'));
}
</script>
