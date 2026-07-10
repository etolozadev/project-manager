<template>
  <div class="flex items-center gap-2">
    <div class="flex-1 overflow-hidden rounded-full bg-gray-100 h-2">
      <div
        :class="['h-2 rounded-full transition-all duration-500', barColor]"
        :style="{ width: `${Math.min(100, Math.max(0, value))}%` }"
      />
    </div>
    <span v-if="showLabel" class="w-9 shrink-0 text-right text-xs font-medium text-gray-600">
      {{ value }}%
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  value:     { type: Number, default: 0 },
  color:     { type: String, default: 'blue' },
  showLabel: { type: Boolean, default: true },
});

const colors = {
  blue:   'bg-blue-500',
  green:  'bg-green-500',
  yellow: 'bg-yellow-500',
  red:    'bg-red-500',
  indigo: 'bg-indigo-500',
};

const barColor = computed(() => colors[props.color] ?? colors.blue);
</script>
