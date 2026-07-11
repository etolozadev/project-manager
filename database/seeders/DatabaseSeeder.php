<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
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
