@php
$regiones = [
    'Arica y Parinacota','Tarapacá','Antofagasta','Atacama','Coquimbo',
    'Valparaíso','Metropolitana','O\'Higgins','Maule','Ñuble','Biobío',
    'La Araucanía','Los Ríos','Los Lagos','Aysén','Magallanes',
];
$inputClass = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20';
@endphp

<x-layouts.sidebar title="Nuevo cliente">
    <x-slot name="breadcrumbs">
        @php $breadcrumbs = ['Clientes' => route('clients.index'), 'Nuevo cliente' => '#']; @endphp
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('clients.store') }}" class="space-y-6">
            @csrf

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="mb-5 text-sm font-semibold text-gray-900">Información del cliente</h2>

                {{-- Tipo --}}
                <x-form-field label="Tipo de cliente" :required="true" class="mb-5">
                    <div class="flex gap-4">
                        @foreach(['company' => 'Empresa', 'person' => 'Persona Natural'] as $val => $label)
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="radio" name="type" value="{{ $val }}" id="type_{{ $val }}"
                                       {{ old('type', 'company') === $val ? 'checked' : '' }}
                                       class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </x-form-field>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    {{-- Nombre --}}
                    <div class="sm:col-span-2">
                        <x-form-field label="Nombre / Razón social" :required="true" :error="$errors->first('name')">
                            <input type="text" name="name" value="{{ old('name') }}"
                                   placeholder="Ej: Constructora Del Valle SpA"
                                   class="{{ $inputClass }}">
                        </x-form-field>
                    </div>

                    {{-- RUT --}}
                    <x-form-field label="RUT" :required="true" :error="$errors->first('rut')">
                        <input type="text" name="rut" value="{{ old('rut') }}"
                               placeholder="12.345.678-9"
                               class="{{ $inputClass }}">
                        <p class="mt-1 text-xs text-gray-400">Formato: XX.XXX.XXX-Y (el dígito puede ser K)</p>
                    </x-form-field>

                    {{-- Persona de contacto --}}
                    <div id="contact-person-field">
                        <x-form-field label="Persona de contacto" :error="$errors->first('contact_person')">
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                                   placeholder="Ej: María González"
                                   class="{{ $inputClass }}">
                        </x-form-field>
                    </div>

                    {{-- Email --}}
                    <x-form-field label="Email" :error="$errors->first('email')">
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="contacto@empresa.cl"
                               class="{{ $inputClass }}">
                    </x-form-field>

                    {{-- Teléfono --}}
                    <x-form-field label="Teléfono" :error="$errors->first('phone')">
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               placeholder="+56 2 2345 6789"
                               class="{{ $inputClass }}">
                    </x-form-field>
                </div>
            </div>

            {{-- Dirección --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="mb-5 text-sm font-semibold text-gray-900">Dirección</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-form-field label="Dirección" :error="$errors->first('address')">
                            <input type="text" name="address" value="{{ old('address') }}"
                                   placeholder="Av. Providencia 1234, Oficina 501"
                                   class="{{ $inputClass }}">
                        </x-form-field>
                    </div>
                    <x-form-field label="Ciudad" :error="$errors->first('city')">
                        <input type="text" name="city" value="{{ old('city') }}"
                               placeholder="Santiago" class="{{ $inputClass }}">
                    </x-form-field>
                    <x-form-field label="Región" :error="$errors->first('region')">
                        <select name="region" class="{{ $inputClass }}">
                            <option value="">Seleccionar región</option>
                            @foreach($regiones as $r)
                                <option value="{{ $r }}" {{ old('region') === $r ? 'selected' : '' }}>{{ $r }}</option>
                            @endforeach
                        </select>
                    </x-form-field>
                    <div class="sm:col-span-2">
                        <x-form-field label="Sitio web" :error="$errors->first('website')">
                            <input type="url" name="website" value="{{ old('website') }}"
                                   placeholder="https://www.empresa.cl" class="{{ $inputClass }}">
                        </x-form-field>
                    </div>
                    <div class="sm:col-span-2">
                        <x-form-field label="Notas internas" :error="$errors->first('notes')">
                            <textarea name="notes" rows="3"
                                      placeholder="Información adicional sobre el cliente..."
                                      class="{{ $inputClass }} resize-none">{{ old('notes') }}</textarea>
                        </x-form-field>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('clients.index') }}"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    Guardar cliente
                </button>
            </div>
        </form>
    </div>
</x-layouts.sidebar>
