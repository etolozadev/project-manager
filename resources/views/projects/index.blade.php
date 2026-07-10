@php
$statusColors = ['draft'=>'gray','active'=>'blue','paused'=>'yellow','completed'=>'green','cancelled'=>'red'];
$statusLabels = ['draft'=>'Borrador','active'=>'En progreso','paused'=>'Pausado','completed'=>'Completado','cancelled'=>'Cancelado'];
@endphp

<x-layouts.sidebar title="Proyectos">
    <x-slot name="headerActions">
        <a href="{{ route('projects.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nuevo proyecto
        </a>
    </x-slot>

    {{-- Filtros --}}
    <form method="GET" class="mb-5 flex flex-wrap gap-3">
        <div class="flex flex-1 items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 min-w-48">
            <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar por nombre, código o cliente..."
                   class="w-full bg-transparent text-sm text-gray-900 placeholder-gray-400 outline-none">
        </div>
        <select name="status"
                class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                onchange="this.form.submit()">
            <option value="">Todos los estados</option>
            @foreach($statusLabels as $val => $label)
                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="client_id"
                class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                onchange="this.form.submit()">
            <option value="">Todos los clientes</option>
            @foreach($clients as $c)
                <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        @if(request()->hasAny(['search','status','client_id']))
            <a href="{{ route('projects.index') }}"
               class="flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar
            </a>
        @endif
        <button type="submit"
                class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
            Buscar
        </button>
    </form>

    @if($projects->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-xl bg-white py-16 text-center shadow-sm ring-1 ring-gray-100">
            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100">
                <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-900">No hay proyectos</p>
            <p class="mt-1 text-sm text-gray-500">Crea tu primer proyecto para comenzar.</p>
            <a href="{{ route('projects.create') }}"
               class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                Crear proyecto
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($projects as $project)
                <a href="{{ route('projects.show', $project) }}"
                   class="group flex flex-col rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100 hover:shadow-md hover:ring-indigo-200 transition-all duration-200">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-mono text-xs text-gray-400">{{ $project->code }}</p>
                            <h3 class="mt-0.5 truncate text-sm font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                {{ $project->name }}
                            </h3>
                            <p class="mt-0.5 truncate text-xs text-gray-500">{{ $project->client->name }}</p>
                        </div>
                        <x-badge color="{{ $statusColors[$project->status] ?? 'gray' }}">
                            {{ $project->status_name }}
                        </x-badge>
                    </div>

                    <div class="mt-4">
                        <x-progress-bar :value="$project->progress"
                            :color="$project->progress >= 75 ? 'green' : ($project->progress >= 40 ? 'blue' : 'yellow')"/>
                    </div>

                    <div class="mt-4 flex items-center justify-between text-xs text-gray-400">
                        <span class="font-medium text-gray-700">{{ $project->formatted_budget }}</span>
                        <span>
                            @if($project->end_date)
                                Hasta {{ $project->end_date->format('d/m/Y') }}
                            @else
                                Sin fecha fin
                            @endif
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        @if($projects->hasPages())
            <div class="mt-5">
                {{ $projects->links() }}
            </div>
        @endif
    @endif

</x-layouts.sidebar>
