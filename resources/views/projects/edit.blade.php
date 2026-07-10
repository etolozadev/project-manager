@php
$inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';
@endphp

<x-layouts.sidebar :title="$project->name">
    <x-slot name="breadcrumbs">
        @php $breadcrumbs = ['Proyectos' => route('projects.index'), $project->name => route('projects.show', $project), 'Editar' => '#']; @endphp
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="mb-5 text-sm font-semibold text-gray-900">Información del proyecto</h2>
                <div class="space-y-4">
                    <x-form-field label="Cliente" :required="true" :error="$errors->first('client_id')">
                        <select name="client_id" class="{{ $inputClass }}">
                            <option value="">Seleccionar cliente...</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                    {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }} — {{ $client->rut }}
                                </option>
                            @endforeach
                        </select>
                    </x-form-field>
                    <x-form-field label="Nombre del proyecto" :required="true" :error="$errors->first('name')">
                        <input type="text" name="name" value="{{ old('name', $project->name) }}"
                               class="{{ $inputClass }}">
                    </x-form-field>
                    <x-form-field label="Descripción" :error="$errors->first('description')">
                        <textarea name="description" rows="3"
                                  class="{{ $inputClass }} resize-none">{{ old('description', $project->description) }}</textarea>
                    </x-form-field>
                    <x-form-field label="Estado" :required="true" :error="$errors->first('status')">
                        <select name="status" class="{{ $inputClass }}">
                            @foreach(['draft'=>'Borrador','active'=>'En progreso','paused'=>'Pausado','completed'=>'Completado','cancelled'=>'Cancelado'] as $val => $label)
                                <option value="{{ $val }}" {{ old('status', $project->status) === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </x-form-field>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="mb-5 text-sm font-semibold text-gray-900">Fechas</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-form-field label="Fecha de inicio" :error="$errors->first('start_date')">
                        <input type="date" name="start_date"
                               value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}"
                               class="{{ $inputClass }}">
                    </x-form-field>
                    <x-form-field label="Fecha de término" :error="$errors->first('end_date')">
                        <input type="date" name="end_date"
                               value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}"
                               class="{{ $inputClass }}">
                    </x-form-field>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="mb-5 text-sm font-semibold text-gray-900">Presupuesto</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <x-form-field label="Monto" :required="true" :error="$errors->first('budget_amount')">
                            <input type="text" name="budget_amount"
                                   value="{{ old('budget_amount', $project->budget_amount) }}"
                                   class="{{ $inputClass }}">
                            <p class="mt-1 text-xs text-gray-400">Sin puntos ni símbolos. Ej: 1500000</p>
                        </x-form-field>
                    </div>
                    <x-form-field label="Moneda" :required="true" :error="$errors->first('currency')">
                        <select name="currency" class="{{ $inputClass }}">
                            <option value="CLP" {{ old('currency', $project->currency) === 'CLP' ? 'selected' : '' }}>CLP — Peso chileno</option>
                            <option value="USD" {{ old('currency', $project->currency) === 'USD' ? 'selected' : '' }}>USD — Dólar</option>
                        </select>
                    </x-form-field>
                    <div class="sm:col-span-3">
                        <label class="flex cursor-pointer items-center gap-3">
                            <input type="hidden" name="budget_includes_vat" value="0">
                            <input type="checkbox" name="budget_includes_vat" value="1"
                                   {{ old('budget_includes_vat', $project->budget_includes_vat) ? 'checked' : '' }}
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <p class="text-sm font-medium text-gray-900">El monto incluye IVA (19%)</p>
                        </label>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <x-form-field label="Notas internas" :error="$errors->first('notes')">
                    <textarea name="notes" rows="3"
                              class="{{ $inputClass }} resize-none">{{ old('notes', $project->notes) }}</textarea>
                </x-form-field>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('projects.show', $project) }}"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</x-layouts.sidebar>
