<x-layouts.sidebar :title="$client->name">
    <x-slot name="breadcrumbs">
        @php $breadcrumbs = ['Clientes' => route('clients.index'), $client->name => '#']; @endphp
    </x-slot>
    <x-slot name="headerActions">
        <a href="{{ route('clients.edit', $client) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
            </svg>
            Editar
        </a>
    </x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Info del cliente --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100">
                        <span class="text-lg font-bold text-indigo-600">
                            {{ strtoupper(substr($client->name, 0, 2)) }}
                        </span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <x-badge color="{{ $client->type === 'company' ? 'blue' : 'purple' }}">
                                {{ $client->type_name }}
                            </x-badge>
                            <x-badge color="{{ $client->active ? 'green' : 'red' }}">
                                {{ $client->active ? 'Activo' : 'Inactivo' }}
                            </x-badge>
                        </div>
                        <p class="mt-1 font-mono text-xs text-gray-400">{{ $client->rut }}</p>
                    </div>
                </div>

                <dl class="space-y-3">
                    @if($client->contact_person)
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Contacto</dt>
                            <dd class="mt-0.5 text-sm text-gray-900">{{ $client->contact_person }}</dd>
                        </div>
                    @endif
                    @if($client->email)
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Email</dt>
                            <dd class="mt-0.5 text-sm text-gray-900">
                                <a href="mailto:{{ $client->email }}" class="text-indigo-600 hover:underline">{{ $client->email }}</a>
                            </dd>
                        </div>
                    @endif
                    @if($client->phone)
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Teléfono</dt>
                            <dd class="mt-0.5 text-sm text-gray-900">{{ $client->phone }}</dd>
                        </div>
                    @endif
                    @if($client->address || $client->city)
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Dirección</dt>
                            <dd class="mt-0.5 text-sm text-gray-900">
                                {{ $client->address ? $client->address . ', ' : '' }}{{ $client->city }}
                                @if($client->region), {{ $client->region }}@endif
                            </dd>
                        </div>
                    @endif
                    @if($client->website)
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Web</dt>
                            <dd class="mt-0.5 text-sm">
                                <a href="{{ $client->website }}" target="_blank"
                                   class="text-indigo-600 hover:underline truncate block">
                                    {{ $client->website }}
                                </a>
                            </dd>
                        </div>
                    @endif
                    @if($client->notes)
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-400">Notas</dt>
                            <dd class="mt-0.5 text-sm text-gray-600">{{ $client->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Proyectos --}}
        <div class="lg:col-span-2">
            <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-100">
                <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Proyectos
                        <span class="ml-1.5 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                            {{ $client->projects->count() }}
                        </span>
                    </h2>
                    <a href="{{ route('projects.create', ['client_id' => $client->id]) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Nuevo proyecto
                    </a>
                </div>

                @if($client->projects->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <p class="text-sm text-gray-500">Este cliente aún no tiene proyectos.</p>
                        <a href="{{ route('projects.create', ['client_id' => $client->id]) }}"
                           class="mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            Crear primer proyecto →
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($client->projects as $project)
                            @php
                                $statusColors = ['draft'=>'gray','active'=>'blue','paused'=>'yellow','completed'=>'green','cancelled'=>'red'];
                            @endphp
                            <div class="flex items-center gap-4 px-5 py-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs text-gray-400">{{ $project->code }}</span>
                                        <x-badge color="{{ $statusColors[$project->status] ?? 'gray' }}">
                                            {{ $project->status_name }}
                                        </x-badge>
                                    </div>
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="mt-0.5 block text-sm font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                                        {{ $project->name }}
                                    </a>
                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ $project->formatted_budget }}
                                        @if($project->end_date)
                                            · Hasta {{ $project->end_date->format('d/m/Y') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="w-28 shrink-0">
                                    <x-progress-bar :value="$project->progress"
                                        :color="$project->progress >= 75 ? 'green' : ($project->progress >= 40 ? 'blue' : 'yellow')"/>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

</x-layouts.sidebar>
