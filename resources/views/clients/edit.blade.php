@php
$regiones = [
    'Arica y Parinacota','Tarapacá','Antofagasta','Atacama','Coquimbo',
    'Valparaíso','Metropolitana','O\'Higgins','Maule','Ñuble','Biobío',
    'La Araucanía','Los Ríos','Los Lagos','Aysén','Magallanes',
];
$inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';
@endphp

<x-layouts.sidebar title="Editar cliente">
    <x-slot name="breadcrumbs">
        @php $breadcrumbs = ['Clientes' => route('clients.index'), $client->name => route('clients.show', $client), 'Editar' => '#']; @endphp
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="mb-5 text-sm font-semibold text-gray-900">Información del cliente</h2>

                <x-form-field label="Tipo de cliente" :required="true" class="mb-5">
                    <div class="flex gap-4">
                        @foreach(['company' => 'Empresa', 'person' => 'Persona Natural'] as $val => $label)
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="radio" name="type" value="{{ $val }}"
                                       {{ old('type', $client->type) === $val ? 'checked' : '' }}
                                       class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </x-form-field>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-form-field label="Nombre / Razón social" :required="true" :error="$errors->first('name')">
                            <input type="text" name="name" value="{{ old('name', $client->name) }}"
                                   class="{{ $inputClass }}">
                        </x-form-field>
                    </div>
                    <x-form-field label="RUT" :required="true" :error="$errors->first('rut')">
                        <input type="text" name="rut" value="{{ old('rut', $client->rut) }}"
                               class="{{ $inputClass }}">
                        <p class="mt-1 text-xs text-gray-400">Formato: XX.XXX.XXX-Y</p>
                    </x-form-field>
                    <x-form-field label="Persona de contacto" :error="$errors->first('contact_person')">
                        <input type="text" name="contact_person" value="{{ old('contact_person', $client->contact_person) }}"
                               class="{{ $inputClass }}">
                    </x-form-field>
                    <x-form-field label="Email" :error="$errors->first('email')">
                        <input type="email" name="email" value="{{ old('email', $client->email) }}"
                               class="{{ $inputClass }}">
                    </x-form-field>
                    <x-form-field label="Teléfono" :error="$errors->first('phone')">
                        <input type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                               class="{{ $inputClass }}">
                    </x-form-field>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="mb-5 text-sm font-semibold text-gray-900">Dirección</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-form-field label="Dirección" :error="$errors->first('address')">
                            <input type="text" name="address" value="{{ old('address', $client->address) }}"
                                   class="{{ $inputClass }}">
                        </x-form-field>
                    </div>
                    <x-form-field label="Ciudad" :error="$errors->first('city')">
                        <input type="text" name="city" value="{{ old('city', $client->city) }}"
                               class="{{ $inputClass }}">
                    </x-form-field>
                    <x-form-field label="Región" :error="$errors->first('region')">
                        <select name="region" class="{{ $inputClass }}">
                            <option value="">Seleccionar región</option>
                            @foreach($regiones as $r)
                                <option value="{{ $r }}" {{ old('region', $client->region) === $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                    </x-form-field>
                    <div class="sm:col-span-2">
                        <x-form-field label="Sitio web" :error="$errors->first('website')">
                            <input type="url" name="website" value="{{ old('website', $client->website) }}"
                                   class="{{ $inputClass }}">
                        </x-form-field>
                    </div>
                    <div class="sm:col-span-2">
                        <x-form-field label="Notas internas" :error="$errors->first('notes')">
                            <textarea name="notes" rows="3" class="{{ $inputClass }} resize-none">{{ old('notes', $client->notes) }}</textarea>
                        </x-form-field>
                    </div>
                </div>
            </div>

            {{-- Estado --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <label class="flex cursor-pointer items-center gap-3">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" name="active" value="1"
                           {{ old('active', $client->active) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Cliente activo</p>
                        <p class="text-xs text-gray-500">Los clientes inactivos no aparecen en los selectores de nuevos proyectos.</p>
                    </div>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('clients.show', $client) }}"
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
