<template>
  <!-- Wrapper con misma estructura que el contenido real -->
  <div class="animate-pulse">

    <!-- Stats row (4 tarjetas) — igual a Dashboard / Finances -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <div
        v-for="n in 4" :key="n"
        class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100 p-5 flex items-center gap-4"
      >
        <!-- Icono -->
        <div class="h-10 w-10 rounded-lg bg-gray-100 shrink-0" />
        <!-- Texto -->
        <div class="flex-1 space-y-2">
          <div class="h-3 w-24 rounded bg-gray-100" />
          <div class="h-6 w-12 rounded bg-gray-200" />
        </div>
      </div>
    </div>

    <!-- Barra de filtros / acciones -->
    <div class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-100 px-4 py-3 flex items-center gap-3">
      <div class="h-9 flex-1 rounded-lg bg-gray-100 min-w-48" />
      <div class="hidden sm:block h-5 w-px bg-gray-200" />
      <div class="h-9 w-36 rounded-lg bg-gray-100" />
      <div class="h-9 w-36 rounded-lg bg-gray-100" />
    </div>

    <!-- Grid de tarjetas (3 columnas en xl) -->
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
      <div
        v-for="n in 6" :key="n"
        class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100 p-5 space-y-4"
      >
        <!-- Header tarjeta -->
        <div class="flex items-start justify-between gap-2">
          <div class="space-y-2 flex-1 min-w-0">
            <div class="h-2.5 w-16 rounded bg-gray-100" />
            <div class="h-4 w-40 rounded bg-gray-200" />
            <div class="h-3 w-28 rounded bg-gray-100" />
          </div>
          <div class="h-5 w-16 rounded-full bg-gray-100 shrink-0" />
        </div>
        <!-- Barra de progreso -->
        <div class="h-1.5 w-full rounded-full bg-gray-100 overflow-hidden">
          <div
            class="h-full rounded-full bg-gray-200"
            :style="{ width: `${30 + (n * 13) % 60}%` }"
          />
        </div>
        <!-- Footer tarjeta -->
        <div class="flex justify-between">
          <div class="h-3 w-20 rounded bg-gray-100" />
          <div class="h-3 w-24 rounded bg-gray-100" />
        </div>
      </div>
    </div>

  </div>
</template>
