<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Limpiar tablas en orden inverso de dependencias (CASCADE maneja las FK en PostgreSQL)
        DB::statement('TRUNCATE TABLE
            servers, expenses, payments, quote_items, quotes,
            tasks, projects, clients, users
            RESTART IDENTITY CASCADE'
        );

        User::factory()->create([
            'name'  => 'Esteban Toloza',
            'email' => 'estebantoloza1998@gmail.com',
        ]);

        $this->call([
            ClientSeeder::class,
            ProjectSeeder::class,
            QuoteSeeder::class,
            FinanceSeeder::class,
            ServerSeeder::class,
            UserSeeder::class,
        ]);
    }
}
