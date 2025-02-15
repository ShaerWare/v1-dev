<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/profile",
     *     tags={"Profile"},
     *     summary="Получение информации о текущем пользователе",
     *     description="Возвращает информацию о текущем пользователе, который авторизован в системе",
     *     security={
     *         {"BearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Информация о пользователе успешно получена",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Иван Иванов"),
     *             @OA\Property(property="email", type="string", example="ivan@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function show(Request $request)
    {
        // Получаем залогинившегося пользователя
        $user = Auth::user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/profile",
     *     tags={"Profile"},
     *     summary="Обновление информации о текущем пользователе",
     *     description="Позволяет обновить информацию о текущем пользователе, который авторизован в системе",
     *     security={
     *         {"BearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Новый Имя Администратора"),
     *             @OA\Property(property="email", type="string", example="new.admin@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация о пользователе успешно обновлена",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Новый Имя Администратора"),
     *             @OA\Property(property="email", type="string", example="new.admin@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Неверный запрос",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function update(Request $request)
    {
        // Получаем залогинившегося пользователя
        $user = Auth::user();

        // Валидация данных
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        // Обновляем пользователя
        $user->update($validatedData);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/profile",
     *     tags={"Profile"},
     *     summary="Удаление текущего пользователя",
     *     description="Позволяет удалить текущего пользователя, который авторизован в системе",
     *     security={
     *         {"BearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=204,
     *         description="Пользователь успешно удален"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        // Получаем залогинившегося пользователя
        $user = Auth::user();

        // Удаляем пользователя
        $user->delete();

        return response()->json(null, 204);
    }
}
