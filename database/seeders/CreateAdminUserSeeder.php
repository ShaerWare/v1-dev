<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Запустить начальные данные базы данных.
     *
     * @return void
     */
    public function run()
    {
        // Создаем роль admin для guard web
        $roleWeb = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $permissionsWeb = Permission::where('guard_name', 'web')->get();
        $roleWeb->syncPermissions($permissionsWeb);

        // Создаем роль admin для guard api
        $roleApi = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $permissionsApi = Permission::where('guard_name', 'api')->get();
        $roleApi->syncPermissions($permissionsApi);

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

        // Назначаем пользователю роли для обоих guard'ов
        $user->assignRole('admin', 'web');
        $user->assignRole('admin', 'api');

        // Назначаем роль пользователю
        // $user->assignRole('admin'); // Присваиваем роль пользователю

        // Вывод сообщения для отладки
        $this->command->info('Администратор успешно создан с максимальными привилегиями с ролями для web и api.');
    }
}
