<?php

namespace Database\Seeders;

use App\Models\RegionIndex;
use Illuminate\Database\Seeder;

class RegionIndexSeeder extends Seeder
{
    public function run()
    {
        $regions = [
            ['index_code' => 1, 'name' => 'Ярославль и обл.'],
            ['index_code' => 2, 'name' => 'Казань и Татарстан'],
            ['index_code' => 3, 'name' => 'Екатеринбург и обл.'],
            ['index_code' => 4, 'name' => 'Москва и Московская обл.'],
            ['index_code' => 5, 'name' => 'Владимир и обл.'],
            ['index_code' => 6, 'name' => 'Тверь и обл.'],
            ['index_code' => 7, 'name' => 'Калуга и обл.'],
            ['index_code' => 8, 'name' => 'Тула и обл.'],
            ['index_code' => 9, 'name' => 'Санкт-Петербург и обл.'],
            ['index_code' => 10, 'name' => 'Великий Новгород и обл.'],
        ];

        // Очищаем таблицу перед вставкой (опционально)
        RegionIndex::truncate();

        // Вставляем все регионы
        foreach ($regions as $region) {
            RegionIndex::updateOrCreate(
                ['index_code' => $region['index_code']],
                ['name' => $region['name']]
            );
        }
    }
}
