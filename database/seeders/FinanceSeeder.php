<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $user     = User::first();
        $projects = Project::all();

        // ── Proyecto 1: Sistema de gestión de obras (active) ────
        $p1 = $projects[0];

        Payment::create([
            'project_id'   => $p1->id,
            'created_by'   => $user->id,
            'amount'       => 2250000, // 50% del presupuesto
            'currency'     => 'CLP',
            'payment_date' => now()->subDays(20),
            'method'       => 'transfer',
            'reference'    => '20240701-001',
            'notes'        => 'Anticipo 50% aprobación del proyecto',
        ]);

        Expense::create([
            'project_id'   => $p1->id,
            'created_by'   => $user->id,
            'category'     => 'hosting',
            'description'  => 'DigitalOcean Droplet 2GB RAM',
            'amount'       => 42000,
            'currency'     => 'CLP',
            'expense_date' => now()->subDays(15),
            'notes'        => '$6 USD aprox. servidor de desarrollo',
        ]);

        Expense::create([
            'project_id'   => $p1->id,
            'created_by'   => $user->id,
            'category'     => 'domain',
            'description'  => 'Registro dominio constructoradelvalle.cl',
            'amount'       => 15000,
            'currency'     => 'CLP',
            'expense_date' => now()->subDays(18),
        ]);

        Expense::create([
            'project_id'   => $p1->id,
            'created_by'   => $user->id,
            'category'     => 'license',
            'description'  => 'Licencia Bootstrap UI Kit',
            'amount'       => 89000,
            'currency'     => 'CLP',
            'expense_date' => now()->subDays(12),
        ]);

        // ── Proyecto 2: Portal de pacientes (active) ────────────
        $p2 = $projects[1];

        Payment::create([
            'project_id'   => $p2->id,
            'created_by'   => $user->id,
            'amount'       => 3100000, // 50%
            'currency'     => 'CLP',
            'payment_date' => now()->subDays(8),
            'method'       => 'transfer',
            'reference'    => '20240709-002',
            'notes'        => 'Anticipo inicial',
        ]);

        Expense::create([
            'project_id'   => $p2->id,
            'created_by'   => $user->id,
            'category'     => 'subcontract',
            'description'  => 'Diseño UI/UX por diseñadora freelance',
            'amount'       => 450000,
            'currency'     => 'CLP',
            'expense_date' => now()->subDays(5),
            'notes'        => 'María Paz Rojas — diseño de 6 pantallas',
        ]);

        Expense::create([
            'project_id'   => $p2->id,
            'created_by'   => $user->id,
            'category'     => 'tools',
            'description'  => 'Twilio SMS — mensajes de confirmación',
            'amount'       => 35000,
            'currency'     => 'CLP',
            'expense_date' => now()->subDays(3),
        ]);

        // ── Proyecto 3: Landing page (completed) ────────────────
        $p3 = $projects[2];

        Payment::create([
            'project_id'   => $p3->id,
            'created_by'   => $user->id,
            'amount'       => 900000,
            'currency'     => 'CLP',
            'payment_date' => now()->subDays(45),
            'method'       => 'transfer',
            'reference'    => '20240525-001',
            'notes'        => 'Primer cuota 50%',
        ]);

        Payment::create([
            'project_id'   => $p3->id,
            'created_by'   => $user->id,
            'amount'       => 900000,
            'currency'     => 'CLP',
            'payment_date' => now()->subDays(6),
            'method'       => 'transfer',
            'reference'    => '20240704-001',
            'notes'        => 'Saldo final contra entrega',
        ]);

        Expense::create([
            'project_id'   => $p3->id,
            'created_by'   => $user->id,
            'category'     => 'hosting',
            'description'  => 'Hosting compartido Hostinger 1 año',
            'amount'       => 48000,
            'currency'     => 'CLP',
            'expense_date' => now()->subDays(7),
        ]);
    }
}
