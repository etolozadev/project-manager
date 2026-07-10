<template>
  <AppLayout
    :title="project.name"
    :breadcrumbs="[{ label: 'Proyectos', href: route('projects.index') }, { label: project.name }]"
  >
    <template #actions>
      <Link :href="route('projects.edit', project.id)"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
        </svg>
        Editar
      </Link>
      <button @click="confirmDelete"
              class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916"/>
        </svg>
        Eliminar
      </button>
    </template>

    <!-- Info del proyecto -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
        <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Cliente</p>
        <Link :href="route('clients.show', project.client.id)"
              class="mt-1 block text-sm font-semibold text-gray-900 hover:text-indigo-600 transition-colors">
          {{ project.client.name }}
        </Link>
        <p class="font-mono text-xs text-gray-400">{{ project.client.rut }}</p>
      </div>
      <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
        <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Presupuesto</p>
        <p class="mt-1 text-sm font-semibold text-gray-900">{{ project.formatted_budget }}</p>
        <p class="text-xs text-gray-400">{{ project.budget_includes_vat ? 'IVA incluido' : 'Más IVA 19%' }}</p>
      </div>
      <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
        <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Estado</p>
        <div class="mt-1">
          <Badge :color="project.status_color">{{ project.status_name }}</Badge>
        </div>
        <p class="mt-1 text-xs text-gray-400">
          {{ project.end_date ? `Hasta ${project.end_date}` : 'Sin fecha límite' }}
        </p>
      </div>
      <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
        <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Progreso general</p>
        <p class="mt-1 text-2xl font-bold text-gray-900">{{ currentProgress }}%</p>
        <div class="mt-2">
          <ProgressBar :value="currentProgress" :show-label="false"
            :color="currentProgress >= 75 ? 'green' : currentProgress >= 40 ? 'blue' : 'yellow'"/>
        </div>
      </div>
    </div>

    <!-- Kanban -->
    <div>
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">
          Tablero de tareas
          <span class="ml-1.5 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
            {{ totalTasks }}
          </span>
        </h2>
        <button @click="showNewTask = !showNewTask"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
          </svg>
          Nueva tarea
        </button>
      </div>

      <!-- Form nueva tarea -->
      <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="showNewTask" class="mb-5 rounded-xl bg-white p-5 shadow-sm ring-1 ring-indigo-200">
          <h3 class="mb-4 text-sm font-semibold text-gray-900">Nueva tarea</h3>
          <form @submit.prevent="submitTask">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
              <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Título <span class="text-red-500">*</span></label>
                <input type="text" v-model="taskForm.title" :class="inputClass" autofocus
                       placeholder="Ej: Implementar módulo de facturación"/>
                <p v-if="taskForm.errors.title" class="mt-1 text-xs text-red-600">{{ taskForm.errors.title }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Categoría</label>
                <select v-model="taskForm.category" :class="inputClass">
                  <option v-for="(lbl, val) in categoryNames" :key="val" :value="val">{{ lbl }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Prioridad</label>
                <select v-model="taskForm.priority" :class="inputClass">
                  <option v-for="(lbl, val) in priorityNames" :key="val" :value="val">{{ lbl }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha límite</label>
                <input type="date" v-model="taskForm.due_date" :class="inputClass"/>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Horas estimadas</label>
                <input type="number" v-model="taskForm.estimated_hours" :class="inputClass" min="1" placeholder="Ej: 8"/>
              </div>
              <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
                <textarea v-model="taskForm.description" rows="2" :class="inputClass + ' resize-none'"
                          placeholder="Detalle opcional..."/>
              </div>
            </div>
            <div class="mt-4 flex items-center justify-end gap-3">
              <button type="button" @click="showNewTask = false"
                      class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Cancelar
              </button>
              <button type="submit" :disabled="taskForm.processing"
                      class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors disabled:opacity-60">
                {{ taskForm.processing ? 'Creando...' : 'Crear tarea' }}
              </button>
            </div>
          </form>
        </div>
      </Transition>

      <!-- Columnas Kanban -->
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div
          v-for="(col, colKey) in columns" :key="colKey"
          class="flex flex-col rounded-xl bg-gray-50 ring-1 ring-gray-200"
          @dragover.prevent
          @drop.prevent="onDrop($event, colKey)"
        >
          <!-- Header columna -->
          <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-200">
            <div class="h-2 w-2 rounded-full" :class="col.dot"/>
            <span class="text-xs font-semibold text-gray-700">{{ col.label }}</span>
            <span class="ml-auto rounded-full bg-white px-2 py-0.5 text-xs font-medium text-gray-500 ring-1 ring-gray-200">
              {{ (localTasks[colKey] || []).length }}
            </span>
          </div>

          <!-- Cards -->
          <div class="flex flex-col gap-2 p-2 min-h-32">
            <div
              v-for="task in (localTasks[colKey] || [])" :key="task.id"
              draggable="true"
              @dragstart="onDragStart($event, task.id)"
              class="group rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-100 cursor-grab active:cursor-grabbing hover:shadow-md transition-all"
            >
              <div class="flex items-start justify-between gap-2">
                <p class="text-sm font-medium text-gray-900 leading-snug">{{ task.title }}</p>
                <Link :href="route('tasks.edit', task.id)"
                      class="shrink-0 opacity-0 group-hover:opacity-100 rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all">
                  <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/>
                  </svg>
                </Link>
              </div>
              <div class="mt-2 flex flex-wrap gap-1.5">
                <Badge :color="task.category_color">{{ task.category_name }}</Badge>
                <Badge :color="task.priority_color">{{ task.priority_name }}</Badge>
              </div>
              <div v-if="task.due_date || task.estimated_hours" class="mt-2 flex items-center gap-3 text-xs text-gray-400">
                <span v-if="task.due_date" :class="task.is_overdue ? 'text-red-600 font-medium' : ''" class="flex items-center gap-1">
                  <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                  </svg>
                  {{ task.due_date }}
                </span>
                <span v-if="task.estimated_hours" class="flex items-center gap-1">
                  <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ task.estimated_hours }}h
                </span>
              </div>
            </div>

            <div v-if="!(localTasks[colKey] || []).length"
                 class="flex flex-1 items-center justify-center py-6 text-xs text-gray-400">
              Sin tareas
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, reactive, watch } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Badge from '@/Components/Badge.vue';
import ProgressBar from '@/Components/ProgressBar.vue';

const props = defineProps({
  project:       Object,
  tasksByStatus: Object,
});

const inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';

const columns = {
  backlog:     { label: 'Por hacer',   dot: 'bg-gray-400'   },
  in_progress: { label: 'En progreso', dot: 'bg-blue-500'   },
  review:      { label: 'En revisión', dot: 'bg-yellow-500' },
  done:        { label: 'Hecho',       dot: 'bg-green-500'  },
};

const categoryNames = {
  development: 'Desarrollo', design: 'Diseño', server: 'Servidor',
  testing: 'Testing', documentation: 'Documentación', meeting: 'Reunión', other: 'Otro',
};

const priorityNames = { low: 'Baja', medium: 'Media', high: 'Alta', critical: 'Crítica' };

// Estado local del Kanban
const localTasks = reactive({
  backlog:     [...(props.tasksByStatus?.backlog     ?? [])],
  in_progress: [...(props.tasksByStatus?.in_progress ?? [])],
  review:      [...(props.tasksByStatus?.review      ?? [])],
  done:        [...(props.tasksByStatus?.done        ?? [])],
});

const currentProgress = ref(props.project.progress);

// ──────────────────────────────────────────────────────────────────────
// Sincronizar localTasks cuando los props cambian (ej: tras crear tarea)
// Inertia reutiliza el componente sin remontarlo, por eso es necesario.
// ──────────────────────────────────────────────────────────────────────
watch(
  () => props.tasksByStatus,
  (newVal) => {
    localTasks.backlog     = [...(newVal?.backlog     ?? [])];
    localTasks.in_progress = [...(newVal?.in_progress ?? [])];
    localTasks.review      = [...(newVal?.review      ?? [])];
    localTasks.done        = [...(newVal?.done        ?? [])];
  },
  { deep: true }
);

watch(() => props.project.progress, (val) => {
  currentProgress.value = val;
});

const totalTasks = computed(() =>
  Object.values(localTasks).reduce((sum, col) => sum + col.length, 0)
);

// Drag & Drop
let draggingTaskId = null;

function onDragStart(e, taskId) {
  draggingTaskId = taskId;
  e.dataTransfer.effectAllowed = 'move';
}

async function onDrop(e, targetStatus) {
  if (!draggingTaskId) return;

  let task = null;
  let sourceStatus = null;
  for (const [status, tasks] of Object.entries(localTasks)) {
    const idx = tasks.findIndex(t => t.id === draggingTaskId);
    if (idx !== -1) {
      task = tasks[idx];
      sourceStatus = status;
      break;
    }
  }

  if (!task || sourceStatus === targetStatus) return;

  // Optimistic update
  localTasks[sourceStatus] = localTasks[sourceStatus].filter(t => t.id !== draggingTaskId);
  localTasks[targetStatus] = [...localTasks[targetStatus], { ...task, status: targetStatus }];
  draggingTaskId = null;

  try {
    const res = await fetch(route('tasks.update-status', task.id), {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        'Accept': 'application/json',
      },
      body: JSON.stringify({ status: targetStatus }),
    });
    if (res.ok) {
      const data = await res.json();
      currentProgress.value = data.progress;
    }
  } catch (err) {
    console.error('Error al actualizar estado de tarea:', err);
  }
}

// Nueva tarea
const showNewTask = ref(false);
const taskForm = useForm({
  project_id:      props.project.id,
  title:           '',
  description:     '',
  category:        'development',
  status:          'backlog',
  priority:        'medium',
  due_date:        '',
  estimated_hours: '',
});

function submitTask() {
  taskForm.post(route('tasks.store'), {
    // preserveScroll evita que la página suba al inicio
    // preserveState: false para que los props se actualicen
    preserveScroll: true,
    onSuccess: () => {
      showNewTask.value = false;
      taskForm.reset('title', 'description', 'due_date', 'estimated_hours');
      // Los props ya se actualizaron via Inertia → el watch sincroniza localTasks
    },
  });
}

// Eliminar proyecto
function confirmDelete() {
  if (confirm(`¿Eliminar el proyecto "${props.project.name}"? Esta acción no se puede deshacer.`)) {
    router.delete(route('projects.destroy', props.project.id));
  }
}
</script>
