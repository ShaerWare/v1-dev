<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegionIndex;
use OpenApi\Attributes as OA;

class RegionIndexController extends Controller
{
    /**
     * Получить список всех регионов с индексами.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    #[OA\Get(
        path: '/api/region-indices',
        operationId: 'getRegionIndices',
        tags: ['Regions'],
        summary: 'Получить список регионов с индексами',
        description: 'Возвращает ассоциативный массив регионов, где ключ - числовой индекс, а значение - полное название региона'
    )]
    #[OA\Response(
        response: 200,
        description: 'Успешный ответ',
        content: new OA\JsonContent(
            type: 'object',
            example: [
                '1' => 'Ярославль и обл.',
                '2' => 'Казань и Татарстан',
                '3' => 'Екатеринбург и обл.',
                '4' => 'Москва и Московская обл.',
                '5' => 'Владимир и обл.',
                '6' => 'Тверь и обл.',
                '7' => 'Калуга и обл.',
                '8' => 'Тула и обл.',
                '9' => 'Санкт-Петербург и обл.',
                '10' => 'Великий Новгород и обл.',
            ]
        )
    )]
    public function index()
    {
        $regions = RegionIndex::all()->mapWithKeys(function ($region) {
            return [$region->index_code => "{$region->index_code}. {$region->name}"];
        })->all();

        return response()->json($regions);
    }
}
