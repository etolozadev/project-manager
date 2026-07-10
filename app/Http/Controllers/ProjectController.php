<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Client;
use App\Models\Project;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('client')
            ->when(request('search'), fn ($q) => $q->search(request('search')))
            ->when(request('status'), fn ($q) => $q->byStatus(request('status')))
            ->when(request('client_id'), fn ($q) => $q->where('client_id', request('client_id')))
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn ($p) => [
                'id'              => $p->id,
                'code'            => $p->code,
                'name'            => $p->name,
                'status'          => $p->status,
                'status_name'     => $p->status_name,
                'status_color'    => $p->status_color,
                'formatted_budget'=> $p->formatted_budget,
                'progress'        => $p->progress,
                'end_date'        => $p->end_date?->format('d/m/Y'),
                'client_name'     => $p->client->name,
            ]);

        $clients = Client::active()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'clients'  => $clients,
            'filters'  => request()->only(['search', 'status', 'client_id']),
        ]);
    }

    public function create()
    {
        $clients = Client::active()->orderBy('name')->get()->map(fn ($c) => [
            'id'   => $c->id,
            'name' => $c->name,
            'rut'  => $c->rut,
        ]);

        return Inertia::render('Projects/Create', [
            'clients'           => $clients,
            'preselectedClient' => request('client_id'),
        ]);
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', "Proyecto {$project->code} creado correctamente.");
    }

    public function show(Project $project)
    {
        $project->load('client');

        $tasksByStatus = $project->tasks()
            ->orderBy('order')
            ->get()
            ->groupBy('status')
            ->map(fn ($tasks) => $tasks->map(fn ($t) => [
                'id'              => $t->id,
                'title'           => $t->title,
                'category'        => $t->category,
                'category_name'   => $t->category_name,
                'category_color'  => $t->category_color,
                'status'          => $t->status,
                'priority'        => $t->priority,
                'priority_name'   => $t->priority_name,
                'priority_color'  => $t->priority_color,
                'due_date'        => $t->due_date?->format('d/m/Y'),
                'due_date_raw'    => $t->due_date?->format('Y-m-d'),
                'is_overdue'      => $t->is_overdue,
                'estimated_hours' => $t->estimated_hours,
            ]));

        return Inertia::render('Projects/Show', [
            'project' => [
                'id'                  => $project->id,
                'code'                => $project->code,
                'name'                => $project->name,
                'description'         => $project->description,
                'status'              => $project->status,
                'status_name'         => $project->status_name,
                'status_color'        => $project->status_color,
                'formatted_budget'    => $project->formatted_budget,
                'budget_includes_vat' => $project->budget_includes_vat,
                'progress'            => $project->progress,
                'start_date'          => $project->start_date?->format('d/m/Y'),
                'end_date'            => $project->end_date?->format('d/m/Y'),
                'notes'               => $project->notes,
                'client'              => [
                    'id'   => $project->client->id,
                    'name' => $project->client->name,
                    'rut'  => $project->client->rut,
                ],
            ],
            'tasksByStatus' => $tasksByStatus,
        ]);
    }

    public function edit(Project $project)
    {
        $clients = Client::active()->orderBy('name')->get()->map(fn ($c) => [
            'id'   => $c->id,
            'name' => $c->name,
            'rut'  => $c->rut,
        ]);

        return Inertia::render('Projects/Edit', [
            'project' => [
                'id'                  => $project->id,
                'client_id'           => $project->client_id,
                'name'                => $project->name,
                'description'         => $project->description,
                'status'              => $project->status,
                'start_date'          => $project->start_date?->format('Y-m-d'),
                'end_date'            => $project->end_date?->format('Y-m-d'),
                'budget_amount'       => $project->budget_amount,
                'currency'            => $project->currency,
                'budget_includes_vat' => $project->budget_includes_vat,
                'notes'               => $project->notes,
            ],
            'clients' => $clients,
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyecto eliminado correctamente.');
    }
}
