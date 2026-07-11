<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user      = auth()->user();
        $isDev     = $user->isDeveloper();

        // Developer scope: solo proyectos donde es miembro
        $projectScope = fn ($q) => $isDev
            ? $q->whereHas('members', fn ($m) => $m->where('user_id', $user->id))
            : $q;

        // Task scope: developer solo ve tareas asignadas a él
        $taskScope = fn ($q) => $isDev
            ? $q->where('assigned_to', $user->id)
            : $q;

        $stats = Cache::remember("dashboard.stats.{$user->id}", 60, fn () => [
            'active_projects'  => Project::where('status', 'active')->tap($projectScope)->count(),
            'total_clients'    => $isDev ? 0 : Client::where('active', true)->count(),
            'pending_quotes'   => $isDev ? 0 : Quote::whereIn('status', ['draft', 'sent'])->count(),
            'overdue_tasks'    => Task::overdue()->tap($taskScope)->count(),
            'approved_quotes'  => $isDev ? 0 : Quote::where('status', 'approved')->count(),
        ]);

        $activeProjects = Project::with('client')
            ->where('status', 'active')
            ->tap($projectScope)
            ->orderByDesc('updated_at')
            ->take(5)
            ->get()
            ->map(fn ($p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'code'        => $p->code,
                'progress'    => $p->progress,
                'client_name' => $p->client->name,
            ]);

        $overdueTasks = Task::with('project')
            ->overdue()
            ->tap($taskScope)
            ->orderBy('due_date')
            ->take(5)
            ->get()
            ->map(fn ($t) => [
                'id'             => $t->id,
                'title'          => $t->title,
                'due_date'       => $t->due_date->format('d/m/Y'),
                'priority'       => $t->priority,
                'priority_name'  => $t->priority_name,
                'priority_color' => $t->priority_color,
                'project_id'     => $t->project_id,
                'project_name'   => $t->project->name,
            ]);

        $pendingQuotes = $isDev ? collect() : Quote::with('client')
            ->whereIn('status', ['draft', 'sent'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($q) => [
                'id'              => $q->id,
                'quote_number'    => $q->quote_number,
                'title'           => $q->title,
                'status'          => $q->status,
                'status_name'     => $q->status_name,
                'status_color'    => $q->status_color,
                'formatted_total' => $q->formatted_total,
                'client_name'     => $q->client->name,
                'valid_until'     => $q->valid_until?->format('d/m/Y'),
            ]);

        $recentClients = $isDev ? collect() : Client::latest()->take(5)->get()
            ->map(fn ($c) => [
                'id'             => $c->id,
                'name'           => $c->name,
                'rut'            => $c->rut,
                'type'           => $c->type,
                'type_name'      => $c->type_name,
                'city'           => $c->city,
                'contact_person' => $c->contact_person,
                'active'         => $c->active,
            ]);

        return Inertia::render('Dashboard', compact(
            'stats', 'activeProjects', 'overdueTasks', 'pendingQuotes', 'recentClients'
        ));
    }
}
