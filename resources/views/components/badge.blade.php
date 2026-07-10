@props(['color' => 'gray', 'size' => 'sm'])

@php
$colors = [
    'gray'   => 'bg-gray-100 text-gray-700 ring-gray-200',
    'blue'   => 'bg-blue-50 text-blue-700 ring-blue-200',
    'green'  => 'bg-green-50 text-green-700 ring-green-200',
    'yellow' => 'bg-yellow-50 text-yellow-700 ring-yellow-200',
    'red'    => 'bg-red-50 text-red-700 ring-red-200',
    'purple' => 'bg-purple-50 text-purple-700 ring-purple-200',
    'orange' => 'bg-orange-50 text-orange-700 ring-orange-200',
    'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
];
$cls = $colors[$color] ?? $colors['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset {$cls}"]) }}>
    {{ $slot }}
</span>
