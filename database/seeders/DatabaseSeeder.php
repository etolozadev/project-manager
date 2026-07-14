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
        // Limpiar tablas respetando la sintaxis de cada motor
        $tables = ['servers', 'expenses', 'payments', 'quote_items', 'quotes', 'tasks', 'projects', 'clients', 'users'];

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('TRUNCATE TABLE ' . implode(', ', $tables) . ' RESTART IDENTITY CASCADE');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

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
