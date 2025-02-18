<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuthBanner;
use Illuminate\Support\Facades\Validator;


class AuthBannerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/auth-banners",
     *     summary="Получить заставки для авторизации",
     *     description="Получить заставки для авторизации в зависимости от типа доступа и индекса",
     *     operationId="getAuthBanners",
     *     tags={"3аставки для авторизации"},
     *     @OA\Parameter(
     *         name="index",
     *         in="query",
     *         description="Индекс для фильтрации",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список заставок",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
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
     *     @OA\Response(
     *         response=400,
     *         description="Неверный запрос"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка сервера"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $index = $request->query('index');

        // Проверка, если индекс передан, валидируем
        $validator = Validator::make($request->all(), [
            'index' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid index format'], 400);
        }

        // Получаем заставки в зависимости от типа доступа и индекса
        $banners = AuthBanner::query()
            ->when(!$user, fn($query) => $query->where('access_type', 'all'))
            ->when($index, fn($query) => $query->where('index_code', $index))
            ->get();

        return response()->json($banners);
    }
}
