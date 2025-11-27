<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1) Primero crear roles
        $this->call(RoleSeeder::class);

        // 2) Luego crear usuario de prueba
        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
            'role_id' => 1, // si quieres que sea admin
        ]);
    }
}
