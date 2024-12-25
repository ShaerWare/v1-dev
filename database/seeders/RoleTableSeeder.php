<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Определяем роли
        $roles = [
            'admin',      // Администратор
            'team_lead',  // Тимлид
            'buyer'       // Байер
        ];

        // Создаём роли, если их ещё нет
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Назначаем привилегии для каждой роли
            switch ($roleName) {
                case 'admin':
                    // Администратор получает все привилегии
                    $role->givePermissionTo(Permission::all());
                    break;

                case 'team_lead':
                    // Тимлид получает доступ к ролям и продуктам, но ограничен
                    $role->givePermissionTo([
                        'role-list',
                        'product-list',
                        'product-create',
                        'product-edit'
                    ]);
                    break;

                case 'buyer':
                    // Байер получает доступ к управлению своими продуктами и просмотр остальных
                    $role->givePermissionTo([
                        'product-list',
                        'product-create-own',
                        'product-edit-own',
                        'product-delete-own'
                    ]);
                    break;
            }
        }

        // Вывод результата в консоль для отладки (опционально)
        $this->command->info('Роли успешно созданы и привилегии назначены.');
    }
}
