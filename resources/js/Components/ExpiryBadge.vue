<template>
  <div class="flex items-center gap-1.5">
    <span class="text-xs font-medium text-gray-400 w-12">{{ label }}</span>
    <span
      class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset"
      :class="badgeClass"
    >
      <span v-if="alert" class="h-1.5 w-1.5 rounded-full" :class="dotClass"/>
      {{ date }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  label: String,
  date:  String,
  alert: String, // null | 'warning' | 'critical' | 'expired'
});

const badgeClass = computed(() => {
  switch (props.alert) {
    case 'expired':  return 'bg-red-50 text-red-700 ring-red-200';
    case 'critical': return 'bg-orange-50 text-orange-700 ring-orange-200';
    case 'warning':  return 'bg-yellow-50 text-yellow-700 ring-yellow-200';
    default:         return 'bg-gray-50 text-gray-600 ring-gray-200';
  }
});

const dotClass = computed(() => {
  switch (props.alert) {
    case 'expired':  return 'bg-red-500 animate-pulse';
    case 'critical': return 'bg-orange-500 animate-pulse';
    case 'warning':  return 'bg-yellow-500';
    default:         return 'hidden';
  }
});
</script>
