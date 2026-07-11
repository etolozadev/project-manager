<template>
  <AppLayout title="Finanzas">
    <template #actions>
      <span class="text-sm text-gray-500">Año {{ currentYear }}</span>
    </template>

    <!-- ── Stats principales ──────────────────────────────── -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 mb-6">

      <!-- Ingresos del mes -->
      <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Ingresos este mes</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ stats.formatted_month_income }}</p>
            <p class="mt-1 text-xs text-gray-400">Gastos: {{ stats.formatted_month_expenses }}</p>
          </div>
          <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50">
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Ingresos anuales -->
      <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Ingresos {{ currentYear }}</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ stats.formatted_annual_income }}</p>
            <p class="mt-1 text-xs font-medium" :class="stats.annual_profit >= 0 ? 'text-green-600' : 'text-red-500'">
              Ganancia neta: {{ stats.formatted_annual_profit }}
            </p>
          </div>
          <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50">
            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Por cobrar -->
      <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500">Por cobrar</p>
            <p class="mt-1 text-3xl font-bold" :class="stats.total_pending > 0 ? 'text-yellow-600' : 'text-gray-400'">
              {{ stats.formatted_total_pending }}
            </p>
            <p class="mt-1 text-xs text-gray-400">En {{ pendingProjects.length }} proyecto(s) activo(s)</p>
          </div>
          <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-50">
            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

      <!-- ── Proyectos por cobrar ────────────────────────── -->
      <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
        <div class="border-b border-gray-100 px-5 py-4">
          <h2 class="text-sm font-semibold text-gray-900">Proyectos por cobrar</h2>
        </div>

        <div v-if="pendingProjects.length === 0" class="px-5 py-10 text-center text-sm text-gray-400">
          ¡Todo cobrado! No hay proyectos con saldo pendiente.
        </div>

        <ul v-else class="divide-y divide-gray-50">
          <li v-for="p in pendingProjects" :key="p.id" class="px-5 py-4 hover:bg-gray-50 transition-colors">
            <div class="flex items-start justify-between gap-3">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                  <Link :href="route('projects.show', p.id)"
                        class="font-medium text-gray-900 hover:text-indigo-600 transition-colors truncate">
                    {{ p.name }}
                  </Link>
                  <Badge :color="p.status_color" class="shrink-0">{{ p.status_name }}</Badge>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">{{ p.client_name }} · {{ p.code }}</p>
              </div>
              <div class="shrink-0 text-right">
                <p class="text-sm font-bold text-yellow-600">{{ p.formatted_pending }}</p>
                <p class="text-xs text-gray-400">de {{ p.formatted_budget }}</p>
              </div>
            </div>
            <div class="mt-2">
              <div class="flex items-center gap-2">
                <div class="flex-1 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                  <div class="h-1.5 rounded-full bg-green-500 transition-all" :style="{ width: `${p.payment_pct}%` }"/>
                </div>
                <span class="text-xs text-gray-500 w-8 text-right">{{ p.payment_pct }}%</span>
              </div>
            </div>
          </li>
        </ul>
      </div>

      <!-- ── Ingresos últimos 6 meses ────────────────────── -->
      <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
        <div class="border-b border-gray-100 px-5 py-4">
          <h2 class="text-sm font-semibold text-gray-900">Ingresos últimos 6 meses</h2>
        </div>

        <div v-if="monthlyIncome.length === 0" class="px-5 py-10 text-center text-sm text-gray-400">
          Sin pagos registrados aún.
        </div>

        <div v-else class="px-5 py-4">
          <!-- Barras manuales con CSS -->
          <div class="flex items-end gap-3 h-36">
            <div
              v-for="m in normalizedMonths" :key="m.label"
              class="flex flex-1 flex-col items-center gap-1"
            >
              <span class="text-xs font-mono text-gray-600 truncate max-w-full text-center">
                {{ m.formatted }}
              </span>
              <div class="w-full rounded-t-md bg-indigo-500 transition-all duration-500 min-h-1"
                   :style="{ height: `${m.height}%` }"
                   :title="m.formatted"
              />
              <span class="text-xs font-semibold text-gray-500 capitalize">{{ m.label }}</span>
            </div>
          </div>
        </div>

        <!-- Gastos por categoría -->
        <div v-if="expensesByCategory.length > 0" class="border-t border-gray-100 px-5 py-4">
          <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-3">
            Gastos por categoría ({{ currentYear }})
          </p>
          <div class="space-y-2">
            <div v-for="cat in expensesByCategory" :key="cat.category" class="flex items-center gap-3">
              <Badge :color="cat.color" class="shrink-0 w-24 justify-center text-center">{{ cat.label }}</Badge>
              <div class="flex-1 h-2 rounded-full bg-gray-100 overflow-hidden">
                <div class="h-2 rounded-full bg-red-400 transition-all"
                     :style="{ width: `${cat.pct}%` }"/>
              </div>
              <span class="text-xs font-mono text-gray-600 w-24 text-right shrink-0">{{ cat.formatted }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ── Últimos pagos ───────────────────────────────── -->
      <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
        <div class="border-b border-gray-100 px-5 py-4">
          <h2 class="text-sm font-semibold text-gray-900">Últimos pagos recibidos</h2>
        </div>
        <div v-if="recentPayments.length === 0" class="px-5 py-8 text-center text-sm text-gray-400">
          Sin pagos registrados aún.
        </div>
        <ul v-else class="divide-y divide-gray-50">
          <li v-for="p in recentPayments" :key="p.id"
              class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-50">
              <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <Link :href="route('projects.show', p.project_id)"
                    class="text-sm font-medium text-gray-900 hover:text-indigo-600 transition-colors truncate block">
                {{ p.project_name }}
              </Link>
              <p class="text-xs text-gray-400">{{ p.client_name }} · {{ p.payment_date }}</p>
            </div>
            <div class="shrink-0 text-right">
              <p class="text-sm font-bold text-green-600">{{ p.formatted_amount }}</p>
              <Badge :color="p.method_color">{{ p.method_name }}</Badge>
            </div>
          </li>
        </ul>
      </div>

      <!-- ── Últimos gastos ──────────────────────────────── -->
      <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
        <div class="border-b border-gray-100 px-5 py-4">
          <h2 class="text-sm font-semibold text-gray-900">Últimos gastos</h2>
        </div>
        <div v-if="recentExpenses.length === 0" class="px-5 py-8 text-center text-sm text-gray-400">
          Sin gastos registrados aún.
        </div>
        <ul v-else class="divide-y divide-gray-50">
          <li v-for="e in recentExpenses" :key="e.id"
              class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-50">
              <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <Link :href="route('projects.show', e.project_id)"
                    class="text-sm font-medium text-gray-900 hover:text-indigo-600 transition-colors truncate block">
                {{ e.description }}
              </Link>
              <p class="text-xs text-gray-400">{{ e.project_name }} · {{ e.expense_date }}</p>
            </div>
            <div class="shrink-0 text-right">
              <p class="text-sm font-bold text-red-600">{{ e.formatted_amount }}</p>
              <Badge :color="e.category_color">{{ e.category_name }}</Badge>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
  stats:               Object,
  pendingProjects:     Array,
  monthlyIncome:       Array,
  expensesByCategory:  Array,
  recentPayments:      Array,
  recentExpenses:      Array,
  currentYear:         Number,
});

// Normalizar alturas del gráfico de barras (max = 100%)
const normalizedMonths = computed(() => {
  const maxVal = Math.max(...props.monthlyIncome.map(m => m.total), 1);
  return props.monthlyIncome.map(m => ({
    ...m,
    height: Math.max(4, Math.round((m.total / maxVal) * 100)),
  }));
});

// Calcular % de cada categoría de gastos
const expensesByCategory = computed(() => {
  const total = props.expensesByCategory.reduce((s, c) => s + c.total, 0) || 1;
  return props.expensesByCategory.map(c => ({
    ...c,
    pct: Math.round((c.total / total) * 100),
  }));
});
</script>
