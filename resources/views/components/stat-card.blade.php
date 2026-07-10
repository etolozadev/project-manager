@props(['label', 'value', 'color' => 'indigo', 'icon'])

@php
$colors = [
    'indigo' => ['bg' => 'bg-indigo-600', 'light' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
    'green'  => ['bg' => 'bg-green-600',  'light' => 'bg-green-50',  'text' => 'text-green-600'],
    'yellow' => ['bg' => 'bg-yellow-500', 'light' => 'bg-yellow-50', 'text' => 'text-yellow-600'],
    'red'    => ['bg' => 'bg-red-600',    'light' => 'bg-red-50',    'text' => 'text-red-600'],
    'blue'   => ['bg' => 'bg-blue-600',   'light' => 'bg-blue-50',   'text' => 'text-blue-600'],
];
$c = $colors[$color] ?? $colors['indigo'];
@endphp

<div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $value }}</p>
        </div>
        <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $c['light'] }}">
            <div class="{{ $c['text'] }}">
                {{ $icon }}
            </div>
        </div>
    </div>
</div>
