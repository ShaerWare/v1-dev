<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/content",
     *     summary="Получить список баннеров",
     *     description="Возвращает список баннеров в зависимости от типа доступа и индекса",
     *     operationId="getBanners",
     *     tags={"Новости/Контент"},
     *
     *     @OA\Parameter(
     *         name="index",
     *         in="query",
     *         description="Индекс для фильтрации",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Список баннеров",
     *
     *         @OA\JsonContent(type="array",
     *
     *             @OA\Items(type="object",
     *
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="access_type", type="string"),
     *                 @OA\Property(property="index_code", type="string"),
     *                 @OA\Property(property="image_url", type="string"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=400, description="Неверный запрос"),
     *     @OA\Response(response=500, description="Ошибка сервера")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $index = $request->query('index');

        $validator = Validator::make($request->all(), [
            'index' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid index format'], 400);
        }

        $banners = Content::query()
            ->when(!$user, fn ($query) => $query->where('access_type', 'all'))
            ->when(isset($index) && $index !== '', fn ($query) => $query->where('index_code', $index))
            ->get();

        return response()->json($banners);
    }
}
