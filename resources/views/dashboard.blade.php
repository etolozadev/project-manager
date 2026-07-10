<x-layouts.sidebar title="Dashboard">

    {{-- Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <x-stat-card label="Proyectos activos" :value="$stats['active_projects']" color="indigo">
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/>
                </svg>
            </x-slot>
        </x-stat-card>

        <x-stat-card label="Clientes activos" :value="$stats['total_clients']" color="green">
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                </svg>
            </x-slot>
        </x-stat-card>

        <x-stat-card label="Tareas pendientes" :value="$stats['pending_tasks']" color="blue">
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 002.25 2.25h.75M9 12H6"/>
                </svg>
            </x-slot>
        </x-stat-card>

        <x-stat-card label="Tareas vencidas" :value="$stats['overdue_tasks']" color="red">
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </x-slot>
        </x-stat-card>

    </div>

    {{-- Proyectos activos + Tareas vencidas --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Proyectos activos --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-900">Proyectos activos</h2>
                <a href="{{ route('projects.index', ['status' => 'active']) }}"
                   class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                    Ver todos →
                </a>
            </div>
            <ul class="divide-y divide-gray-50 px-2 py-2">
                @forelse($activeProjects as $project)
                    <li>
                        <a href="{{ route('projects.show', $project) }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-gray-50 transition-colors">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50">
                                <span class="text-xs font-bold text-indigo-600">
                                    {{ strtoupper(substr($project->name, 0, 2)) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900">{{ $project->name }}</p>
                                <p class="text-xs text-gray-500">{{ $project->client->name }} · {{ $project->code }}</p>
                            </div>
                            <div class="w-20 shrink-0">
                                <x-progress-bar :value="$project->progress"
                                    :color="$project->progress >= 75 ? 'green' : ($project->progress >= 40 ? 'blue' : 'yellow')"/>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="px-3 py-8 text-center text-sm text-gray-400">
                        No hay proyectos activos.
                    </li>
                @endforelse
            </ul>
        </div>

        {{-- Tareas vencidas --}}
        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h2 class="text-sm font-semibold text-gray-900">Tareas vencidas</h2>
                @if($stats['overdue_tasks'] > 0)
                    <x-badge color="red">{{ $stats['overdue_tasks'] }} pendientes</x-badge>
                @endif
            </div>
            <ul class="divide-y divide-gray-50 px-2 py-2">
                @forelse($overdueTasks as $task)
                    <li>
                        <a href="{{ route('projects.show', $task->project) }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-3 hover:bg-gray-50 transition-colors">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-50">
                                <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900">{{ $task->title }}</p>
                                <p class="text-xs text-gray-500">{{ $task->project->name }}</p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-xs font-medium text-red-600">
                                    {{ $task->due_date->format('d/m/Y') }}
                                </p>
                                <x-badge color="{{ $task->priority_color }}">{{ $task->priority_name }}</x-badge>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="px-3 py-8 text-center text-sm text-gray-400">
                        Sin tareas vencidas. ¡Todo al día!
                    </li>
                @endforelse
            </ul>
        </div>

    </div>

    {{-- Clientes recientes --}}
    <div class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h2 class="text-sm font-semibold text-gray-900">Clientes recientes</h2>
            <a href="{{ route('clients.index') }}"
               class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                Ver todos →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 text-left text-xs font-medium uppercase tracking-wider text-gray-400">
                        <th class="px-5 py-3">Cliente</th>
                        <th class="px-5 py-3">RUT</th>
                        <th class="px-5 py-3">Ciudad</th>
                        <th class="px-5 py-3">Tipo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentClients as $client)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3">
                                <a href="{{ route('clients.show', $client) }}"
                                   class="font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                                    {{ $client->name }}
                                </a>
                                @if($client->contact_person)
                                    <p class="text-xs text-gray-400">{{ $client->contact_person }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $client->rut }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $client->city ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <x-badge color="{{ $client->type === 'company' ? 'blue' : 'purple' }}">
                                    {{ $client->type_name }}
                                </x-badge>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.sidebar>
