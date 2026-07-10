<template>
  <AppLayout
    :title="`Editar tarea — ${task.title}`"
    :breadcrumbs="[
      { label: 'Proyectos', href: route('projects.index') },
      { label: task.project_name, href: route('projects.show', task.project_id) },
      { label: 'Editar tarea' }
    ]"
  >
    <div class="mx-auto max-w-xl">
      <form @submit.prevent="submit" class="space-y-5">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100 space-y-4">

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Título <span class="text-red-500">*</span></label>
            <input type="text" v-model="form.title" :class="inputClass"/>
            <p v-if="form.errors.title" class="mt-1.5 text-xs text-red-600">{{ form.errors.title }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
            <textarea v-model="form.description" rows="3" :class="inputClass + ' resize-none'"/>
          </div>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Categoría</label>
              <select v-model="form.category" :class="inputClass">
                <option v-for="(lbl, val) in categoryNames" :key="val" :value="val">{{ lbl }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Estado</label>
              <select v-model="form.status" :class="inputClass">
                <option value="backlog">Por hacer</option>
                <option value="in_progress">En progreso</option>
                <option value="review">En revisión</option>
                <option value="done">Hecho</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Prioridad</label>
              <select v-model="form.priority" :class="inputClass">
                <option value="low">Baja</option>
                <option value="medium">Media</option>
                <option value="high">Alta</option>
                <option value="critical">Crítica</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha límite</label>
              <input type="date" v-model="form.due_date" :class="inputClass"/>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Horas estimadas</label>
              <input type="number" v-model="form.estimated_hours" :class="inputClass" min="1" max="9999"/>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Horas reales</label>
              <input type="number" v-model="form.actual_hours" :class="inputClass" min="0" max="9999"/>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Notas</label>
            <textarea v-model="form.notes" rows="2" :class="inputClass + ' resize-none'"
                      placeholder="Observaciones adicionales..."/>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <button type="button" @click="deleteTask"
                  class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
            Eliminar tarea
          </button>
          <div class="flex items-center gap-3">
            <Link :href="route('projects.show', task.project_id)"
                  class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
              Cancelar
            </Link>
            <button type="submit" :disabled="form.processing"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors disabled:opacity-60">
              {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ task: Object });

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';

const categoryNames = {
  development: 'Desarrollo', design: 'Diseño', server: 'Servidor',
  testing: 'Testing', documentation: 'Documentación', meeting: 'Reunión', other: 'Otro',
};

const form = useForm({
  title:           props.task.title,
  description:     props.task.description     ?? '',
  category:        props.task.category,
  status:          props.task.status,
  priority:        props.task.priority,
  due_date:        props.task.due_date         ?? '',
  estimated_hours: props.task.estimated_hours  ?? '',
  actual_hours:    props.task.actual_hours     ?? '',
  notes:           props.task.notes            ?? '',
});

function submit() {
  form.patch(route('tasks.update', props.task.id));
}

function deleteTask() {
  if (confirm('¿Eliminar esta tarea? No se puede deshacer.')) {
    router.delete(route('tasks.destroy', props.task.id));
  }
}
</script>
