<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $user    = User::first();
        $clients = Client::all();

        $projects = [
            [
                'client_id'     => $clients[0]->id,
                'user_id'       => $user->id,
                'name'          => 'Sistema de gestión de obras',
                'description'   => 'Plataforma web para gestionar proyectos de construcción, personal y materiales.',
                'status'        => 'active',
                'start_date'    => now()->subDays(30),
                'end_date'      => now()->addDays(60),
                'budget_amount' => 4500000,
                'currency'      => 'CLP',
                'tasks'         => [
                    ['title' => 'Levantamiento de requerimientos',  'category' => 'meeting',       'status' => 'done',        'priority' => 'high',   'estimated_hours' => 8],
                    ['title' => 'Diseño de base de datos',          'category' => 'development',   'status' => 'done',        'priority' => 'high',   'estimated_hours' => 12],
                    ['title' => 'Módulo de autenticación',          'category' => 'development',   'status' => 'done',        'priority' => 'high',   'estimated_hours' => 10],
                    ['title' => 'Módulo de obras',                  'category' => 'development',   'status' => 'in_progress', 'priority' => 'high',   'estimated_hours' => 24],
                    ['title' => 'Módulo de personal',               'category' => 'development',   'status' => 'in_progress', 'priority' => 'medium', 'estimated_hours' => 20],
                    ['title' => 'Diseño UI/UX',                     'category' => 'design',        'status' => 'review',      'priority' => 'medium', 'estimated_hours' => 16],
                    ['title' => 'Configurar servidor producción',   'category' => 'server',        'status' => 'backlog',     'priority' => 'medium', 'estimated_hours' => 6],
                    ['title' => 'Testing y QA',                     'category' => 'testing',       'status' => 'backlog',     'priority' => 'low',    'estimated_hours' => 20],
                ],
            ],
            [
                'client_id'     => $clients[1]->id,
                'user_id'       => $user->id,
                'name'          => 'Portal de pacientes online',
                'description'   => 'Sistema de agendamiento de horas médicas y gestión de fichas de pacientes.',
                'status'        => 'active',
                'start_date'    => now()->subDays(10),
                'end_date'      => now()->addDays(90),
                'budget_amount' => 6200000,
                'currency'      => 'CLP',
                'tasks'         => [
                    ['title' => 'Análisis de requerimientos',       'category' => 'meeting',       'status' => 'done',        'priority' => 'high',   'estimated_hours' => 10],
                    ['title' => 'Módulo de agenda médica',          'category' => 'development',   'status' => 'in_progress', 'priority' => 'high',   'estimated_hours' => 30],
                    ['title' => 'Integración con Fonasa/Isapre',    'category' => 'development',   'status' => 'backlog',     'priority' => 'high',   'estimated_hours' => 20],
                    ['title' => 'Diseño de portal pacientes',       'category' => 'design',        'status' => 'backlog',     'priority' => 'medium', 'estimated_hours' => 18],
                    ['title' => 'Documentación técnica',            'category' => 'documentation', 'status' => 'backlog',     'priority' => 'low',    'estimated_hours' => 12],
                ],
            ],
            [
                'client_id'     => $clients[2]->id,
                'user_id'       => $user->id,
                'name'          => 'Landing page y tienda online',
                'description'   => 'Sitio web con catálogo de productos y pasarela de pago Webpay.',
                'status'        => 'completed',
                'start_date'    => now()->subDays(60),
                'end_date'      => now()->subDays(5),
                'budget_amount' => 1800000,
                'currency'      => 'CLP',
                'tasks'         => [
                    ['title' => 'Diseño landing page',              'category' => 'design',        'status' => 'done',        'priority' => 'high',   'estimated_hours' => 14],
                    ['title' => 'Desarrollo frontend',              'category' => 'development',   'status' => 'done',        'priority' => 'high',   'estimated_hours' => 20],
                    ['title' => 'Integración Webpay',               'category' => 'development',   'status' => 'done',        'priority' => 'high',   'estimated_hours' => 8],
                    ['title' => 'Deploy en servidor',               'category' => 'server',        'status' => 'done',        'priority' => 'medium', 'estimated_hours' => 4],
                ],
            ],
        ];

        foreach ($projects as $data) {
            $tasks = $data['tasks'];
            unset($data['tasks']);

            $project = Project::create($data);

            foreach ($tasks as $i => $taskData) {
                Task::create([
                    ...$taskData,
                    'project_id' => $project->id,
                    'order'      => $i,
                    'due_date'   => now()->addDays(rand(5, 45)),
                ]);
            }

            $project->recalculateProgress();
        }
    }
}
