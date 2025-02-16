<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Запускает наполнение базы данных.
     */
    public function run()
    {
        // Проверяем, существует ли администратор
        $user = User::where('email', 'hr@gmail.com')->first();

        if (!$user) {
            $user = User::create([
                'name' => 'Kapul Nagant',
                'email' => 'hr@gmail.com',
                'password' => bcrypt('hr@gmav5%il.co7m!hfr56azl_c4m'),
            ]);
        }

        // Проверяем, созданы ли роли для обоих guard'ов
        $adminRoleWeb = Role::where(['name' => 'admin', 'guard_name' => 'web'])->first();
        $adminRoleApi = Role::where(['name' => 'admin', 'guard_name' => 'api'])->first();

        if (!$adminRoleWeb || !$adminRoleApi) {
            $this->command->error('Роли "admin" для web и api не найдены. Убедитесь, что RoleTableSeeder был запущен.');

            return;
        }

        // Получаем все разрешения для обоих guard'ов
        $permissionsWeb = Permission::where('guard_name', 'web')->pluck('name')->toArray();
        $permissionsApi = Permission::where('guard_name', 'api')->pluck('name')->toArray();

        // Назначаем все разрешения ролям
        $adminRoleWeb->syncPermissions($permissionsWeb);
        $adminRoleApi->syncPermissions($permissionsApi);

        // Удаляем все предыдущие роли пользователя (если есть)
        $user->roles()->detach();

        // Назначаем пользователю роли администратора для обоих guard'ов и орхидеи
        $user->assignRole($adminRoleWeb);
        $user->assignRole($adminRoleApi);

        $this->command->info('Администратор успешно создан и получил роли "admin" для web и api.');
    }
}
