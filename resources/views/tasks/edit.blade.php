@php
$inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';
@endphp

<x-layouts.sidebar title="Editar tarea">
    <x-slot name="breadcrumbs">
        @php
        $breadcrumbs = [
            'Proyectos'          => route('projects.index'),
            $task->project->name => route('projects.show', $task->project),
            'Editar tarea'       => '#',
        ];
        @endphp
    </x-slot>

    <div class="mx-auto max-w-xl">
        <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100 space-y-4">
                <x-form-field label="Título" :required="true" :error="$errors->first('title')">
                    <input type="text" name="title" value="{{ old('title', $task->title) }}"
                           class="{{ $inputClass }}">
                </x-form-field>

                <x-form-field label="Descripción" :error="$errors->first('description')">
                    <textarea name="description" rows="3"
                              class="{{ $inputClass }} resize-none">{{ old('description', $task->description) }}</textarea>
                </x-form-field>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-form-field label="Categoría" :required="true" :error="$errors->first('category')">
                        <select name="category" class="{{ $inputClass }}">
                            @foreach(['development'=>'Desarrollo','design'=>'Diseño','server'=>'Servidor','testing'=>'Testing','documentation'=>'Documentación','meeting'=>'Reunión','other'=>'Otro'] as $val => $label)
                                <option value="{{ $val }}" {{ old('category', $task->category) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-form-field>

                    <x-form-field label="Estado" :required="true" :error="$errors->first('status')">
                        <select name="status" class="{{ $inputClass }}">
                            @foreach(['backlog'=>'Por hacer','in_progress'=>'En progreso','review'=>'En revisión','done'=>'Hecho'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $task->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-form-field>

                    <x-form-field label="Prioridad" :required="true" :error="$errors->first('priority')">
                        <select name="priority" class="{{ $inputClass }}">
                            @foreach(['low'=>'Baja','medium'=>'Media','high'=>'Alta','critical'=>'Crítica'] as $val => $label)
                                <option value="{{ $val }}" {{ old('priority', $task->priority) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </x-form-field>

                    <x-form-field label="Fecha límite" :error="$errors->first('due_date')">
                        <input type="date" name="due_date"
                               value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                               class="{{ $inputClass }}">
                    </x-form-field>

                    <x-form-field label="Horas estimadas" :error="$errors->first('estimated_hours')">
                        <input type="number" name="estimated_hours" min="1" max="9999"
                               value="{{ old('estimated_hours', $task->estimated_hours) }}"
                               class="{{ $inputClass }}">
                    </x-form-field>

                    <x-form-field label="Horas reales" :error="$errors->first('actual_hours')">
                        <input type="number" name="actual_hours" min="0" max="9999"
                               value="{{ old('actual_hours', $task->actual_hours) }}"
                               class="{{ $inputClass }}">
                    </x-form-field>
                </div>

                <x-form-field label="Notas" :error="$errors->first('notes')">
                    <textarea name="notes" rows="2"
                              class="{{ $inputClass }} resize-none"
                              placeholder="Observaciones adicionales...">{{ old('notes', $task->notes) }}</textarea>
                </x-form-field>
            </div>

            <div class="flex items-center justify-between">
                <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                      onsubmit="return confirm('¿Eliminar esta tarea? No se puede deshacer.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                        Eliminar tarea
                    </button>
                </form>

                <div class="flex items-center gap-3">
                    <a href="{{ route('projects.show', $task->project) }}"
                       class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                        Guardar cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.sidebar>
