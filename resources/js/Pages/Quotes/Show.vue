<template>
  <AppLayout
    :title="quote.quote_number"
    :breadcrumbs="[{ label: 'Cotizaciones', href: route('quotes.index') }, { label: quote.quote_number }]"
  >
    <template #actions>
      <!-- PDF -->
      <a :href="route('quotes.pdf', quote.id)" target="_blank"
         class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
        <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
        </svg>
        Descargar PDF
      </a>

      <!-- Editar (solo borrador) -->
      <Link v-if="quote.is_editable" :href="route('quotes.edit', quote.id)"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
        </svg>
        Editar
      </Link>
    </template>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

      <!-- Panel izquierdo: acciones y estado -->
      <div class="space-y-4">

        <!-- Estado y acciones de flujo -->
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <div class="mb-4 flex items-center justify-between">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Estado</p>
            <Badge :color="quote.status_color" class="text-xs">{{ quote.status_name }}</Badge>
          </div>

          <!-- Acciones según estado -->
          <div class="space-y-2">
            <!-- Borrador → Enviada -->
            <button v-if="quote.status === 'draft'" @click="send"
                    :disabled="sending"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors disabled:opacity-60">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/>
              </svg>
              {{ sending ? 'Marcando...' : 'Marcar como enviada' }}
            </button>

            <!-- Enviada → Aprobada -->
            <button v-if="quote.status === 'sent'" @click="approve"
                    :disabled="approving"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-700 transition-colors disabled:opacity-60">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              {{ approving ? 'Aprobando...' : '¡Aprobada! Crear proyecto' }}
            </button>

            <!-- Rechazar -->
            <button v-if="['draft','sent'].includes(quote.status)" @click="reject"
                    :disabled="rejecting"
                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors disabled:opacity-60">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              {{ rejecting ? 'Rechazando...' : 'Marcar como rechazada' }}
            </button>

            <!-- Duplicar -->
            <button @click="duplicate"
                    :disabled="duplicating"
                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-60">
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/>
              </svg>
              {{ duplicating ? 'Duplicando...' : 'Duplicar cotización' }}
            </button>

            <!-- Eliminar -->
            <button v-if="quote.status !== 'approved'" @click="destroy"
                    class="flex w-full items-center justify-center gap-2 rounded-lg border border-red-100 px-4 py-2 text-xs font-medium text-red-500 hover:bg-red-50 transition-colors">
              <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916"/>
              </svg>
              Eliminar cotización
            </button>
          </div>

          <!-- Timestamps -->
          <div v-if="quote.sent_at || quote.approved_at || quote.rejected_at" class="mt-4 space-y-1.5 border-t border-gray-100 pt-4">
            <div v-if="quote.sent_at" class="flex justify-between text-xs">
              <span class="text-gray-400">Enviada</span>
              <span class="text-gray-600">{{ quote.sent_at }}</span>
            </div>
            <div v-if="quote.approved_at" class="flex justify-between text-xs">
              <span class="text-gray-400">Aprobada</span>
              <span class="text-green-600 font-medium">{{ quote.approved_at }}</span>
            </div>
            <div v-if="quote.rejected_at" class="flex justify-between text-xs">
              <span class="text-gray-400">Rechazada</span>
              <span class="text-red-500">{{ quote.rejected_at }}</span>
            </div>
          </div>
        </div>

        <!-- Info cliente -->
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-400">Cliente</p>
          <Link :href="route('clients.show', quote.client.id)"
                class="font-semibold text-gray-900 hover:text-indigo-600 transition-colors">
            {{ quote.client.name }}
          </Link>
          <p class="font-mono text-xs text-gray-400 mt-0.5">{{ quote.client.rut }}</p>
          <p v-if="quote.client.contact_person" class="text-xs text-gray-500 mt-1">
            {{ quote.client.contact_person }}
          </p>
          <p v-if="quote.client.email" class="text-xs text-gray-500">{{ quote.client.email }}</p>
        </div>

        <!-- Proyecto generado -->
        <div v-if="quote.project" class="rounded-xl bg-green-50 p-5 ring-1 ring-green-200">
          <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-green-600">Proyecto generado</p>
          <Link :href="route('projects.show', quote.project.id)"
                class="font-semibold text-green-800 hover:text-green-900 transition-colors">
            {{ quote.project.name }}
          </Link>
          <p class="font-mono text-xs text-green-600 mt-0.5">{{ quote.project.code }}</p>
        </div>

        <!-- Vigencia -->
        <div v-if="quote.valid_until_display" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
          <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Válida hasta</p>
          <p class="font-medium text-gray-900">{{ quote.valid_until_display }}</p>
        </div>
      </div>

      <!-- Panel derecho: detalle -->
      <div class="lg:col-span-2 space-y-5">

        <!-- Resumen financiero -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <div class="mb-4 flex items-start justify-between">
            <div>
              <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Cotización</p>
              <h2 class="mt-1 text-lg font-bold text-gray-900">{{ quote.title }}</h2>
              <p class="font-mono text-xs text-gray-400">{{ quote.quote_number }} · {{ quote.created_at }}</p>
            </div>
            <div class="text-right">
              <p class="text-2xl font-bold text-gray-900">{{ quote.formatted_total }}</p>
              <p class="text-xs text-gray-400 mt-0.5">Total con IVA</p>
            </div>
          </div>

          <!-- Mini totales -->
          <div class="flex gap-6 pt-4 border-t border-gray-100">
            <div>
              <p class="text-xs text-gray-400">Neto</p>
              <p class="font-mono text-sm font-semibold text-gray-700">{{ quote.formatted_subtotal }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400">IVA {{ quote.tax_rate }}%</p>
              <p class="font-mono text-sm font-semibold text-gray-700">{{ quote.formatted_tax }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-400">Moneda</p>
              <p class="text-sm font-semibold text-gray-700">{{ quote.currency }}</p>
            </div>
          </div>
        </div>

        <!-- Tabla de ítems -->
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">
              Detalle de ítems
              <span class="ml-1.5 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                {{ quote.items.length }}
              </span>
            </h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wider text-gray-400">
                  <th class="px-5 py-3">Descripción</th>
                  <th class="px-5 py-3 text-center">Cant.</th>
                  <th class="px-5 py-3 text-right">Precio Unit.</th>
                  <th class="px-5 py-3 text-right">Subtotal</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="item in quote.items" :key="item.id" class="hover:bg-gray-50">
                  <td class="px-5 py-3.5">
                    <p class="font-medium text-gray-900">{{ item.description }}</p>
                    <p v-if="item.detail" class="text-xs text-gray-500 mt-0.5">{{ item.detail }}</p>
                  </td>
                  <td class="px-5 py-3.5 text-center font-mono text-gray-600">{{ item.quantity }}</td>
                  <td class="px-5 py-3.5 text-right font-mono text-gray-700">
                    {{ formatAmount(item.unit_price) }}
                  </td>
                  <td class="px-5 py-3.5 text-right font-mono font-semibold text-gray-900">
                    {{ formatAmount(item.subtotal) }}
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="border-t-2 border-gray-200">
                  <td colspan="3" class="px-5 py-3 text-right text-sm font-medium text-gray-500">Subtotal neto</td>
                  <td class="px-5 py-3 text-right font-mono font-semibold text-gray-900">{{ quote.formatted_subtotal }}</td>
                </tr>
                <tr>
                  <td colspan="3" class="px-5 py-2 text-right text-sm text-gray-500">IVA {{ quote.tax_rate }}%</td>
                  <td class="px-5 py-2 text-right font-mono text-gray-700">{{ quote.formatted_tax }}</td>
                </tr>
                <tr class="bg-indigo-600">
                  <td colspan="3" class="px-5 py-3 text-right text-sm font-bold text-white">TOTAL</td>
                  <td class="px-5 py-3 text-right font-mono font-bold text-white">{{ quote.formatted_total }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <!-- Notas -->
        <div v-if="quote.notes" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Notas</h3>
          <p class="text-sm text-gray-700 whitespace-pre-line">{{ quote.notes }}</p>
        </div>

        <!-- Términos -->
        <div v-if="quote.terms" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
          <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Términos y condiciones</h3>
          <p class="text-sm text-gray-600 whitespace-pre-line">{{ quote.terms }}</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({ quote: Object });

const sending    = ref(false);
const approving  = ref(false);
const rejecting  = ref(false);
const duplicating = ref(false);

function formatAmount(amount) {
  if (props.quote.currency === 'USD') return 'USD ' + Number(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  return '$' + new Intl.NumberFormat('es-CL').format(amount);
}

function send() {
  if (!confirm('¿Marcar esta cotización como enviada al cliente?')) return;
  sending.value = true;
  router.post(route('quotes.send', props.quote.id), {}, {
    onFinish: () => (sending.value = false),
  });
}

function approve() {
  if (!confirm('¿Aprobar esta cotización? Se creará el proyecto automáticamente.')) return;
  approving.value = true;
  router.post(route('quotes.approve', props.quote.id), {}, {
    onFinish: () => (approving.value = false),
  });
}

function reject() {
  if (!confirm('¿Marcar como rechazada?')) return;
  rejecting.value = true;
  router.post(route('quotes.reject', props.quote.id), {}, {
    onFinish: () => (rejecting.value = false),
  });
}

function duplicate() {
  duplicating.value = true;
  router.post(route('quotes.duplicate', props.quote.id), {}, {
    onFinish: () => (duplicating.value = false),
  });
}

function destroy() {
  if (!confirm(`¿Eliminar la cotización ${props.quote.quote_number}? Esta acción no se puede deshacer.`)) return;
  router.delete(route('quotes.destroy', props.quote.id));
}
</script>
