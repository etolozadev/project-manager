@php
$statusColors = ['draft'=>'gray','active'=>'blue','paused'=>'yellow','completed'=>'green','cancelled'=>'red'];
$categoryNames = ['development'=>'Desarrollo','design'=>'Diseño','server'=>'Servidor','testing'=>'Testing','documentation'=>'Documentación','meeting'=>'Reunión','other'=>'Otro'];
$categoryColors = ['development'=>'blue','design'=>'purple','server'=>'orange','testing'=>'green','documentation'=>'gray','meeting'=>'yellow','other'=>'gray'];
$priorityNames = ['low'=>'Baja','medium'=>'Media','high'=>'Alta','critical'=>'Crítica'];
$priorityColors = ['low'=>'green','medium'=>'yellow','high'=>'orange','critical'=>'red'];
$columns = [
    'backlog'     => ['label' => 'Por hacer',    'color' => 'bg-gray-400'],
    'in_progress' => ['label' => 'En progreso',  'color' => 'bg-blue-500'],
    'review'      => ['label' => 'En revisión',  'color' => 'bg-yellow-500'],
    'done'        => ['label' => 'Hecho',         'color' => 'bg-green-500'],
];
$inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';
@endphp

<x-layouts.sidebar :title="$project->name">
    <x-slot name="breadcrumbs">
        @php $breadcrumbs = ['Proyectos' => route('projects.index'), $project->name => '#']; @endphp
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('projects.edit', $project) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
            </svg>
            Editar
        </a>
        <form method="POST" action="{{ route('projects.destroy', $project) }}"
              onsubmit="return confirm('¿Eliminar proyecto {{ addslashes($project->name) }}? Esta acción no se puede deshacer.')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-white px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916"/>
                </svg>
                Eliminar
            </button>
        </form>
    </x-slot>

    {{-- Panel de info del proyecto --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Cliente</p>
            <a href="{{ route('clients.show', $project->client) }}"
               class="mt-1 block text-sm font-semibold text-gray-900 hover:text-indigo-600 transition-colors">
                {{ $project->client->name }}
            </a>
            <p class="font-mono text-xs text-gray-400">{{ $project->client->rut }}</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Presupuesto</p>
            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $project->formatted_budget }}</p>
            <p class="text-xs text-gray-400">
                {{ $project->budget_includes_vat ? 'IVA incluido' : 'Más IVA 19%' }}
            </p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Estado</p>
            <div class="mt-1">
                <x-badge color="{{ $statusColors[$project->status] ?? 'gray' }}" class="text-xs">
                    {{ $project->status_name }}
                </x-badge>
            </div>
            <p class="mt-1 text-xs text-gray-400">
                @if($project->end_date)
                    Hasta {{ $project->end_date->format('d/m/Y') }}
                @else
                    Sin fecha límite
                @endif
            </p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-400">Progreso general</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $project->progress }}%</p>
            <div class="mt-2">
                <x-progress-bar :value="$project->progress" :show-label="false"
                    :color="$project->progress >= 75 ? 'green' : ($project->progress >= 40 ? 'blue' : 'yellow')"/>
            </div>
        </div>
    </div>

    {{-- Kanban --}}
    <div x-data="{
        showNewTask: {{ $errors->any() && old('project_id') == $project->id ? 'true' : 'false' }},
        draggedTask: null,
        async moveTask(taskId, newStatus) {
            const res = await fetch(`/tasks/${taskId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status: newStatus })
            });
            if (res.ok) window.location.reload();
        }
    }">

        {{-- Toolbar Kanban --}}
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">
                Tablero de tareas
                <span class="ml-1.5 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                    {{ collect($tasksByStatus)->flatten()->count() }}
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

        {{-- Formulario nueva tarea --}}
        <div x-show="showNewTask" x-collapse
             class="mb-5 rounded-xl bg-white p-5 shadow-sm ring-1 ring-indigo-200">
            <h3 class="mb-4 text-sm font-semibold text-gray-900">Nueva tarea</h3>
            <form method="POST" action="{{ route('tasks.store') }}">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="hidden" name="status" value="backlog">

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-form-field label="Título" :required="true" :error="$errors->first('title')">
                            <input type="text" name="title" value="{{ old('title') }}"
                                   placeholder="Ej: Implementar módulo de facturación"
                                   class="{{ $inputClass }}" autofocus>
                        </x-form-field>
                    </div>
                    <x-form-field label="Categoría" :required="true" :error="$errors->first('category')">
                        <select name="category" class="{{ $inputClass }}">
                            @foreach($categoryNames as $val => $label)
                                <option value="{{ $val }}" {{ old('category','development') === $val ? 'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-form-field>
                    <x-form-field label="Prioridad" :required="true" :error="$errors->first('priority')">
                        <select name="priority" class="{{ $inputClass }}">
                            @foreach($priorityNames as $val => $label)
                                <option value="{{ $val }}" {{ old('priority','medium') === $val ? 'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-form-field>
                    <x-form-field label="Fecha límite" :error="$errors->first('due_date')">
                        <input type="date" name="due_date" value="{{ old('due_date') }}"
                               class="{{ $inputClass }}">
                    </x-form-field>
                    <x-form-field label="Horas estimadas" :error="$errors->first('estimated_hours')">
                        <input type="number" name="estimated_hours" value="{{ old('estimated_hours') }}"
                               min="1" max="9999" placeholder="Ej: 8"
                               class="{{ $inputClass }}">
                    </x-form-field>
                    <div class="sm:col-span-2">
                        <x-form-field label="Descripción" :error="$errors->first('description')">
                            <textarea name="description" rows="2"
                                      class="{{ $inputClass }} resize-none"
                                      placeholder="Detalle opcional...">{{ old('description') }}</textarea>
                        </x-form-field>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-end gap-3">
                    <button type="button" @click="showNewTask = false"
                            class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                        Crear tarea
                    </button>
                </div>
            </form>
        </div>

        {{-- Columnas Kanban --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($columns as $statusKey => $column)
                <div class="flex flex-col rounded-xl bg-gray-50 ring-1 ring-gray-200"
                     x-on:dragover.prevent
                     x-on:drop.prevent="moveTask(draggedTask, '{{ $statusKey }}')">

                    {{-- Header columna --}}
                    <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-200">
                        <div class="h-2 w-2 rounded-full {{ $column['color'] }}"></div>
                        <span class="text-xs font-semibold text-gray-700">{{ $column['label'] }}</span>
                        <span class="ml-auto rounded-full bg-white px-2 py-0.5 text-xs font-medium text-gray-500 ring-1 ring-gray-200">
                            {{ $tasksByStatus[$statusKey]->count() }}
                        </span>
                    </div>

                    {{-- Tareas --}}
                    <div class="flex flex-col gap-2 p-2 min-h-32">
                        @forelse($tasksByStatus[$statusKey] as $task)
                            <div class="group rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-100 cursor-grab active:cursor-grabbing hover:shadow-md transition-all"
                                 draggable="true"
                                 x-on:dragstart="draggedTask = {{ $task->id }}">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-sm font-medium text-gray-900 leading-snug">{{ $task->title }}</p>
                                    <a href="{{ route('tasks.edit', $task) }}"
                                       class="shrink-0 opacity-0 group-hover:opacity-100 rounded-md p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-all">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z"/>
                                        </svg>
                                    </a>
                                </div>

                                <div class="mt-2 flex flex-wrap gap-1.5">
                                    <x-badge color="{{ $categoryColors[$task->category] ?? 'gray' }}">
                                        {{ $categoryNames[$task->category] ?? $task->category }}
                                    </x-badge>
                                    <x-badge color="{{ $priorityColors[$task->priority] ?? 'gray' }}">
                                        {{ $priorityNames[$task->priority] ?? $task->priority }}
                                    </x-badge>
                                </div>

                                @if($task->due_date || $task->estimated_hours)
                                    <div class="mt-2 flex items-center gap-3 text-xs text-gray-400">
                                        @if($task->due_date)
                                            <span class="{{ $task->is_overdue ? 'text-red-600 font-medium' : '' }} flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                                                </svg>
                                                {{ $task->due_date->format('d/m/Y') }}
                                            </span>
                                        @endif
                                        @if($task->estimated_hours)
                                            <span class="flex items-center gap-1">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $task->estimated_hours }}h
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="flex flex-1 items-center justify-center py-6 text-xs text-gray-400">
                                Sin tareas
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</x-layouts.sidebar>
