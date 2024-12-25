<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Генерация тестового пользователя
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Вызов дополнительных сидеров
        $this->call([
            CreateAdminUserSeeder::class,
            PermissionTableSeeder::class,
            RoleTableSeeder::class,
        ]);
    }
}
