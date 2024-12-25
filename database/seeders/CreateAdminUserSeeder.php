<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Запустить начальные данные базы данных.
     *
     * @return void
     */
    public function run()
    {
        // Создание пользователя
        $user = User::create([
            'name' => 'Kapul Nagant',
            'email' => 'hr@gmail.com',
            'password' => bcrypt('hr@gmav5%il.co7m!hfr56azl_c4m'),
        ]);

        // Создание роли администратора, если она ещё не существует
        $role = Role::firstOrCreate(['name' => 'admin']); // Используем 'admin' с маленькой буквы

        // Получение всех разрешений
        $permissions = Permission::all();

        // Назначаем все разрешения роли
        $role->syncPermissions($permissions); // Роль получает все привилегии

        // Назначаем роль пользователю
        $user->assignRole('admin'); // Присваиваем роль пользователю

        // Вывод сообщения для отладки
        $this->command->info('Администратор успешно создан с максимальными привилегиями.');
    }
}
