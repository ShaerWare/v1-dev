<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Запускает наполнение базы данных ролями и привилегиями.
     */
    public function run()
    {
        // Определяем роли
        $roles = [
            'admin',     // Администратор
            'team_lead', // Тимлид
            'buyer',     // Байер
        ];

        // Гварды, для которых создаём роли
        $guards = ['web', 'api'];

        foreach ($roles as $roleName) {
            foreach ($guards as $guard) {
                // Создаём или обновляем роль
                $role = Role::firstOrCreate(
                    ['name' => $roleName, 'guard_name' => $guard]
                );

                // Получаем привилегии для данного guard'а
                $permissions = Permission::where('guard_name', $guard)->pluck('name')->toArray();

                if (!empty($permissions)) {
                    $role->syncPermissions($permissions);
                }
            }
        }

        $this->command->info('Роли успешно созданы для web и api, и привилегии назначены.');
    }
}
