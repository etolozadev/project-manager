<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(1)->create([
            'name' => 'Admin User',
            'email' => 'estebantoloza1998@gmail.com',
            'password' => bcrypt('19981121'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'two_factor_secret' => Str::random(10),
            'two_factor_recovery_codes' => Str::random(10),
            'two_factor_confirmed_at' => now(),
        ]);
        $roles = ['Admin', 'User'];
        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);
        }

        $users->each(function ($user) {
            $user->assignRole('Admin');
        });

        $this->call(DemoSeeder::class);

       
    }
}
