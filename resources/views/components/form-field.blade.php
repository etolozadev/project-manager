{{-- Componente de campo de formulario con label y error --}}
@props(['label', 'error' => null, 'required' => false])

<div>
    @isset($label)
        <label class="block text-sm font-medium text-gray-700 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-red-500 ml-0.5">*</span>
            @endif
        </label>
    @endisset

    {{ $slot }}

    @if($error)
        <p class="mt-1.5 text-xs text-red-600">{{ $error }}</p>
    @endif
</div>
