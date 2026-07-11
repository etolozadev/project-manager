<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Server;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServerController extends Controller
{
    // ── Helper para serializar un servidor ──────────────────────
    private function serverResource(Server $s, bool $withCredentials = false): array
    {
        $data = [
            'id'                  => $s->id,
            'project_id'          => $s->project_id,
            'name'                => $s->name,
            'type'                => $s->type,
            'type_name'           => $s->type_name,
            'type_color'          => $s->type_color,
            'provider'            => $s->provider,
            'ip_address'          => $s->ip_address,
            'hostname'            => $s->hostname,
            'os'                  => $s->os,
            'php_version'         => $s->php_version,
            'db_type'             => $s->db_type,
            'db_type_name'        => $s->db_type_name,
            'db_version'          => $s->db_version,
            'panel'               => $s->panel,
            'panel_name'          => $s->panel_name,
            'web_server'          => $s->web_server,
            'ssh_user'            => $s->ssh_user,
            'ssh_port'            => $s->ssh_port,
            'domain'              => $s->domain,
            'url'                 => $s->url,
            'status'              => $s->status,
            'status_name'         => $s->status_name,
            'status_color'        => $s->status_color,
            'hosting_expires_at'  => $s->hosting_expires_at?->format('d/m/Y'),
            'hosting_expires_raw' => $s->hosting_expires_at?->format('Y-m-d'),
            'domain_expires_at'   => $s->domain_expires_at?->format('d/m/Y'),
            'domain_expires_raw'  => $s->domain_expires_at?->format('Y-m-d'),
            'ssl_expires_at'      => $s->ssl_expires_at?->format('d/m/Y'),
            'ssl_expires_raw'     => $s->ssl_expires_at?->format('Y-m-d'),
            'hosting_alert'       => $s->hosting_expiry_alert,
            'domain_alert'        => $s->domain_expiry_alert,
            'ssl_alert'           => $s->ssl_expiry_alert,
            'notes'               => $s->notes,
            'project_name'        => $s->project->name ?? null,
            'client_name'         => $s->project->client->name ?? null,
        ];

        if ($withCredentials) {
            $data['credentials'] = $s->credentials; // array descifrado
        }

        return $data;
    }

    public function index()
    {
        $servers = Server::with('project.client')
            ->when(request('search'), fn ($q) => $q->where(function ($w) {
                $w->where('name', 'like', '%' . request('search') . '%')
                  ->orWhere('domain', 'like', '%' . request('search') . '%')
                  ->orWhere('ip_address', 'like', '%' . request('search') . '%')
                  ->orWhere('provider', 'like', '%' . request('search') . '%');
            }))
            ->when(request('status'), fn ($q) => $q->where('status', request('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($s) => $this->serverResource($s));

        // Alertas de vencimiento próximo (próximos 30 días)
        $expiringCount = Server::where('status', 'active')
            ->where(function ($q) {
                $q->expiringHosting(30)
                  ->orWhere(fn ($q2) => $q2->expiringDomain(30));
            })
            ->count();

        return Inertia::render('Servers/Index', [
            'servers'       => $servers,
            'filters'       => request()->only(['search', 'status']),
            'expiringCount' => $expiringCount,
        ]);
    }

    public function create()
    {
        $projects = \App\Models\Project::with('client')
            ->whereIn('status', ['active', 'paused', 'draft'])
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'code'        => $p->code,
                'client_name' => $p->client->name,
            ]);

        return Inertia::render('Servers/Create', [
            'projects'           => $projects,
            'preselectedProject' => request('project_id'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateServer($request);
        $credentials = $this->extractCredentials($request);

        $server = Server::create([
            ...$validated,
            'credentials' => $credentials,
            'created_by'  => auth()->id(),
        ]);

        return redirect()->route('servers.show', $server)
            ->with('success', "Servidor \"{$server->name}\" creado correctamente.");
    }

    public function show(Server $server)
    {
        $server->load('project.client');

        return Inertia::render('Servers/Show', [
            'server' => $this->serverResource($server, withCredentials: true),
        ]);
    }

    public function edit(Server $server)
    {
        $projects = \App\Models\Project::with('client')
            ->whereIn('status', ['active', 'paused', 'draft'])
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'code'        => $p->code,
                'client_name' => $p->client->name,
            ]);

        return Inertia::render('Servers/Edit', [
            'server'   => $this->serverResource($server, withCredentials: true),
            'projects' => $projects,
        ]);
    }

    public function update(Request $request, Server $server)
    {
        $validated = $this->validateServer($request, $server);
        $credentials = $this->extractCredentials($request);

        // Si no se envían nuevas credenciales, mantener las existentes
        if (! empty($credentials)) {
            $validated['credentials'] = $credentials;
        }

        $server->update($validated);

        return redirect()->route('servers.show', $server)
            ->with('success', 'Servidor actualizado correctamente.');
    }

    public function destroy(Server $server)
    {
        $server->delete();

        return redirect()->route('servers.index')
            ->with('success', 'Servidor eliminado correctamente.');
    }

    // ── Helpers privados ─────────────────────────────────────────

    private function validateServer(Request $request, ?Server $server = null): array
    {
        return $request->validate([
            'project_id'         => ['required', 'exists:projects,id'],
            'name'               => ['required', 'string', 'max:255'],
            'type'               => ['required', 'in:vps,shared,managed,dedicated,cloud,other'],
            'provider'           => ['nullable', 'string', 'max:100'],
            'ip_address'         => ['nullable', 'ip'],
            'hostname'           => ['nullable', 'string', 'max:255'],
            'os'                 => ['nullable', 'string', 'max:100'],
            'php_version'        => ['nullable', 'string', 'max:10'],
            'db_type'            => ['nullable', 'in:mysql,postgresql,mariadb,sqlite,other'],
            'db_version'         => ['nullable', 'string', 'max:10'],
            'panel'              => ['nullable', 'in:cpanel,plesk,forge,ploi,runcloud,none,other'],
            'web_server'         => ['nullable', 'string', 'max:30'],
            'ssh_user'           => ['nullable', 'string', 'max:50'],
            'ssh_port'           => ['nullable', 'integer', 'min:1', 'max:65535'],
            'domain'             => ['nullable', 'string', 'max:255'],
            'url'                => ['nullable', 'url', 'max:255'],
            'status'             => ['required', 'in:active,inactive,expired'],
            'hosting_expires_at' => ['nullable', 'date'],
            'domain_expires_at'  => ['nullable', 'date'],
            'ssl_expires_at'     => ['nullable', 'date'],
            'notes'              => ['nullable', 'string'],
        ], [
            'project_id.required' => 'Selecciona un proyecto.',
            'name.required'       => 'El nombre del servidor es obligatorio.',
            'ip_address.ip'       => 'La dirección IP no tiene un formato válido.',
            'url.url'             => 'La URL debe ser válida (ej: https://mi-app.cl).',
        ]);
    }

    private function extractCredentials(Request $request): array
    {
        $creds = [
            'ssh_password'   => $request->input('cred_ssh_password'),
            'db_user'        => $request->input('cred_db_user'),
            'db_password'    => $request->input('cred_db_password'),
            'db_name'        => $request->input('cred_db_name'),
            'panel_url'      => $request->input('cred_panel_url'),
            'panel_user'     => $request->input('cred_panel_user'),
            'panel_password' => $request->input('cred_panel_password'),
            'extra'          => $request->input('cred_extra'),
        ];

        return array_filter($creds, fn ($v) => $v !== null && $v !== '');
    }
}
