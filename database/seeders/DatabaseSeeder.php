<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Генерация тестового пользователя
        /*
        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        */
        // Вызов дополнительных сидеров
        $this->call([
            PermissionTableSeeder::class,   // Сначала создаём разрешения
            RoleTableSeeder::class,         // Затем создаём роли и привязываем к ним разрешения
            CreateAdminUserSeeder::class,   // Потом создаём администратора и назначаем роли
        ]);
        $this->command->info('Все сидеры успешно загружены.');

        $parameters = [
            '--personal' => true,
            '--name' => 'Central Panel Personal Access Client', // You can customize the client name here
        ];

        // Run the command
        Artisan::call('passport:client', $parameters);
    }
}
