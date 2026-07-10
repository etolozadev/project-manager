<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::withCount('projects')
            ->when(request('search'), fn ($q) => $q->search(request('search')))
            ->when(request('type'),   fn ($q) => $q->where('type', request('type')))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($c) => [
                'id'             => $c->id,
                'name'           => $c->name,
                'rut'            => $c->rut,
                'type'           => $c->type,
                'type_name'      => $c->type_name,
                'email'          => $c->email,
                'city'           => $c->city,
                'active'         => $c->active,
                'contact_person' => $c->contact_person,
                'projects_count' => $c->projects_count,
            ]);

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => request()->only(['search', 'type']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Clients/Create');
    }

    public function store(StoreClientRequest $request)
    {
        Client::create($request->validated());

        return redirect()->route('clients.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function show(Client $client)
    {
        $projects = $client->projects()->latest()->get()->map(fn ($p) => [
            'id'              => $p->id,
            'code'            => $p->code,
            'name'            => $p->name,
            'status'          => $p->status,
            'status_name'     => $p->status_name,
            'status_color'    => $p->status_color,
            'formatted_budget'=> $p->formatted_budget,
            'progress'        => $p->progress,
            'end_date'        => $p->end_date?->format('d/m/Y'),
            'budget_includes_vat' => $p->budget_includes_vat,
        ]);

        return Inertia::render('Clients/Show', [
            'client'   => [
                'id'             => $client->id,
                'name'           => $client->name,
                'rut'            => $client->rut,
                'type'           => $client->type,
                'type_name'      => $client->type_name,
                'email'          => $client->email,
                'phone'          => $client->phone,
                'contact_person' => $client->contact_person,
                'address'        => $client->address,
                'city'           => $client->city,
                'region'         => $client->region,
                'website'        => $client->website,
                'notes'          => $client->notes,
                'active'         => $client->active,
            ],
            'projects' => $projects,
        ]);
    }

    public function edit(Client $client)
    {
        return Inertia::render('Clients/Edit', [
            'client' => [
                'id'             => $client->id,
                'name'           => $client->name,
                'rut'            => $client->rut,
                'type'           => $client->type,
                'email'          => $client->email,
                'phone'          => $client->phone,
                'contact_person' => $client->contact_person,
                'address'        => $client->address,
                'city'           => $client->city,
                'region'         => $client->region,
                'website'        => $client->website,
                'notes'          => $client->notes,
                'active'         => $client->active,
            ],
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        return redirect()->route('clients.show', $client)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
