<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
                $role = Role::firstOrCreate([
                    'name'       => $roleName,
                    'guard_name' => $guard,
                ]);

                // Получаем только те привилегии, которые принадлежат нужному guard'у
                $permissions = Permission::where('guard_name', $guard)->get();

                switch ($roleName) {
                    case 'admin':
                        $role->syncPermissions($permissions);
                        break;

                    case 'team_lead':
                        $teamLeadPermissions = [
                            'role-list',
                            'product-list',
                            'product-create',
                            'product-edit',
                        ];
                        $role->syncPermissions(
                            $permissions->whereIn('name', $teamLeadPermissions)
                        );
                        break;

                    case 'buyer':
                        $buyerPermissions = [
                            'product-list',
                            'product-create-own',
                            'product-edit-own',
                            'product-delete-own',
                        ];
                        $role->syncPermissions(
                            $permissions->whereIn('name', $buyerPermissions)
                        );
                        break;
                }
            }
        }

        $this->command->info('Роли успешно созданы для web и api, и привилегии назначены.');
    }
}
