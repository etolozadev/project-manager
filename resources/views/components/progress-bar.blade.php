@props(['value', 'color' => 'blue', 'showLabel' => true])

@php
$colors = [
    'blue'   => 'bg-blue-500',
    'green'  => 'bg-green-500',
    'yellow' => 'bg-yellow-500',
    'red'    => 'bg-red-500',
    'indigo' => 'bg-indigo-500',
];
$bar = $colors[$color] ?? $colors['blue'];
@endphp

<div class="flex items-center gap-2">
    <div class="flex-1 overflow-hidden rounded-full bg-gray-100 h-2">
        <div class="{{ $bar }} h-2 rounded-full transition-all duration-500"
             style="width: {{ min(100, max(0, $value)) }}%">
        </div>
    </div>
    @if($showLabel)
        <span class="w-9 shrink-0 text-right text-xs font-medium text-gray-600">{{ $value }}%</span>
    @endif
</div>
