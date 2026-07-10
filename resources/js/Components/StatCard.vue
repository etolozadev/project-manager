<template>
  <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-500">{{ label }}</p>
        <p class="mt-1 text-3xl font-bold text-gray-900">{{ value }}</p>
      </div>
      <div :class="['flex h-12 w-12 items-center justify-center rounded-xl', lightClass]">
        <div :class="textClass">
          <slot name="icon" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  label: String,
  value: [String, Number],
  color: { type: String, default: 'indigo' },
});

const colors = {
  indigo: { light: 'bg-indigo-50', text: 'text-indigo-600' },
  green:  { light: 'bg-green-50',  text: 'text-green-600'  },
  yellow: { light: 'bg-yellow-50', text: 'text-yellow-600' },
  red:    { light: 'bg-red-50',    text: 'text-red-600'    },
  blue:   { light: 'bg-blue-50',   text: 'text-blue-600'   },
};

const c = computed(() => colors[props.color] ?? colors.indigo);
const lightClass = computed(() => c.value.light);
const textClass  = computed(() => c.value.text);
</script>
