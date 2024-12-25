<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Список привилегий для ролей и продуктов
        $permissions = [
            // Привилегии для работы с ролями
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            // Привилегии для работы с продуктами
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',

            //Привелегии для работы с личным продуктом
            'product-create-own',
            'product-edit-own',
            'product-delete-own'
        ];

        // Создание привилегий в базе данных
        foreach ($permissions as $permission) {
            // Проверка, существует ли привилегия, чтобы не создавать дубли
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        // Вывод результата в консоль для отладки (опционально)
        $this->command->info('Привилегии успешно созданы.');
    }
}
