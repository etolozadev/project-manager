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
        $user = auth()->user();

        $projects = Project::with('client')
            // Developer solo ve proyectos donde es miembro
            ->when($user->isDeveloper(), fn ($q) => $q->whereHas('members', fn ($m) => $m->where('user_id', $user->id)))
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

        $clients = $user->isAdmin()
            ? Client::active()->orderBy('name')->get(['id', 'name'])
            : collect();

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

        // Datos financieros del proyecto
        $payments = $project->payments()->get()->map(fn ($p) => [
            'id'               => $p->id,
            'formatted_amount' => $p->formatted_amount,
            'amount'           => $p->amount,
            'method'           => $p->method,
            'method_name'      => $p->method_name,
            'method_color'     => $p->method_color,
            'payment_date'     => $p->payment_date->format('d/m/Y'),
            'reference'        => $p->reference,
            'notes'            => $p->notes,
        ]);

        $expenses = $project->expenses()->get()->map(fn ($e) => [
            'id'               => $e->id,
            'description'      => $e->description,
            'formatted_amount' => $e->formatted_amount,
            'amount'           => $e->amount,
            'category'         => $e->category,
            'category_name'    => $e->category_name,
            'category_color'   => $e->category_color,
            'expense_date'     => $e->expense_date->format('d/m/Y'),
            'notes'            => $e->notes,
        ]);

        $totalPaid     = $payments->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $budget        = $project->budget_amount;

        $finance = [
            'budget'              => $budget,
            'total_paid'          => $totalPaid,
            'total_expenses'      => $totalExpenses,
            'pending'             => max(0, $budget - $totalPaid),
            'net_profit'          => $totalPaid - $totalExpenses,
            'payment_pct'         => $budget > 0 ? (int) min(100, round(($totalPaid / $budget) * 100)) : 0,
            'formatted_budget'    => $project->formatted_budget,
            'formatted_paid'      => '$' . number_format($totalPaid, 0, ',', '.'),
            'formatted_expenses'  => '$' . number_format($totalExpenses, 0, ',', '.'),
            'formatted_pending'   => '$' . number_format(max(0, $budget - $totalPaid), 0, ',', '.'),
            'formatted_profit'    => '$' . number_format($totalPaid - $totalExpenses, 0, ',', '.'),
        ];

        // Cotizaciones del proyecto para vincular pagos
        $quotes = \App\Models\Quote::where('client_id', $project->client_id)
            ->whereIn('status', ['approved', 'sent'])
            ->get(['id', 'quote_number', 'title']);

        // Miembros del proyecto (para asignar tareas)
        $members = $project->members()->get()->map(fn ($u) => [
            'id'       => $u->id,
            'name'     => $u->name,
            'initials' => $u->initials,
        ]);

        // Todos los developers disponibles (para admin)
        $availableDevs = auth()->user()->isAdmin()
            ? \App\Models\User::role('developer')->get()->map(fn ($u) => [
                'id'       => $u->id,
                'name'     => $u->name,
                'initials' => $u->initials,
              ])
            : collect();

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
                'budget_amount'       => $project->budget_amount,
                'currency'            => $project->currency,
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
            'tasksByStatus'  => $tasksByStatus,
            'payments'       => $payments,
            'expenses'       => $expenses,
            'finance'        => $finance,
            'quotes'         => $quotes,
            'members'        => $members,
            'availableDevs'  => $availableDevs,
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

    public function syncMembers(\Illuminate\Http\Request $request, Project $project)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $request->validate([
            'user_ids'   => ['array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $project->members()->sync($request->user_ids ?? []);

        return back()->with('success', 'Equipo del proyecto actualizado.');
    }
}
