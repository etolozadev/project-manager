<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')
            ->latest()
            ->get()
            ->map(fn ($u) => $this->userResource($u));

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $projects = Project::with('client')
            ->whereIn('status', ['active', 'paused', 'draft'])
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'code'        => $p->code,
                'client_name' => $p->client->name,
            ]);

        return Inertia::render('Users/Create', [
            'projects' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'role'     => ['required', 'in:admin,developer'],
            'projects' => ['array'],
            'projects.*' => ['exists:projects,id'],
        ], [
            'name.required'     => 'El nombre es obligatorio.',
            'email.required'    => 'El email es obligatorio.',
            'email.unique'      => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'role.required'     => 'Selecciona un rol.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        // Asignar proyectos si es developer
        if ($validated['role'] === 'developer' && ! empty($validated['projects'])) {
            $user->projects()->sync($validated['projects']);
        }

        return redirect()->route('users.index')
            ->with('success', "Usuario {$user->name} creado correctamente.");
    }

    public function edit(User $user)
    {
        $projects = Project::with('client')
            ->whereIn('status', ['active', 'paused', 'draft'])
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'id'          => $p->id,
                'name'        => $p->name,
                'code'        => $p->code,
                'client_name' => $p->client->name,
            ]);

        return Inertia::render('Users/Edit', [
            'user'     => $this->userResource($user, withProjects: true),
            'projects' => $projects,
        ]);
    }

    public function update(Request $request, User $user)
    {
        // Impedir que el admin se quite su propio rol
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            abort(403, 'No puedes cambiar tu propio rol de administrador.');
        }

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', "unique:users,email,{$user->id}"],
            'password' => ['nullable', Password::min(8)->letters()->numbers()],
            'role'     => ['required', 'in:admin,developer'],
            'projects' => ['array'],
            'projects.*' => ['exists:projects,id'],
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            ...($validated['password'] ? ['password' => Hash::make($validated['password'])] : []),
        ]);

        $user->syncRoles([$validated['role']]);

        $user->projects()->sync($validated['role'] === 'developer'
            ? ($validated['projects'] ?? [])
            : []
        );

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            abort(403, 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    private function userResource(User $u, bool $withProjects = false): array
    {
        $data = [
            'id'         => $u->id,
            'name'       => $u->name,
            'email'      => $u->email,
            'initials'   => $u->initials,
            'role'       => $u->roles->first()?->name ?? 'sin rol',
            'role_label' => match ($u->roles->first()?->name) {
                'admin'     => 'Administrador',
                'developer' => 'Desarrollador',
                default     => 'Sin rol',
            },
            'is_admin'   => $u->isAdmin(),
            'created_at' => $u->created_at->format('d/m/Y'),
        ];

        if ($withProjects) {
            $data['project_ids'] = $u->projects()->pluck('projects.id')->toArray();
        }

        return $data;
    }
}
