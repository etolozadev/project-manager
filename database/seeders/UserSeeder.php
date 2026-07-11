<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'admin',     'guard_name' => 'web']);
        $devRole   = Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);

        // Admin ya existe (del DatabaseSeeder), asignarle rol
        $admin = User::where('email', 'estebantoloza1998@gmail.com')->first();
        if ($admin) {
            $admin->assignRole('admin');
        }

        // Developer de prueba
        $dev = User::firstOrCreate(
            ['email' => 'developer@project-manager.cl'],
            [
                'name'     => 'Carlos Rodríguez',
                'password' => bcrypt('password123'),
            ]
        );
        $dev->assignRole('developer');

        // Asignar al developer los primeros 2 proyectos
        $projects = Project::take(2)->pluck('id')->toArray();
        $dev->projects()->sync($projects);
    }
}
