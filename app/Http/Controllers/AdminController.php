<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Laravel\Passport\TokenRepository;
use Laravel\Passport\Passport;



class AdminController extends Controller
{


    public function __construct()
    {
        // Здесь можно добавить дополнительные проверки доступа или инициализацию,
        // если это необходимо для всех методов контроллера.
    }

    /**
     * @OA\Post(
     *     path="/api/admin",
     *     tags={"Admin"},
     *     summary="Создание нового администратора",
     *     description="Назначает роль администратора существующему пользователю по его ID",
     *     security={
     *         {"BearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"userId"},
     *             @OA\Property(property="userId", type="integer", example=1, description="ID пользователя, которому будет назначена роль администратора")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Роль администратора успешно назначена",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Роль администратора успешно назначена"),
     *             @OA\Property(property="admin", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                 @OA\Property(property="email", type="string", example="ivan@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Доступ запрещен",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Forbidden")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь не найден",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Пользователь не найден")
     *         )
     *     )
     * )
     */
    public function createAdmin(Request $request)
    {
        $authUser = $this->authenticateUser($request);

        if (!$authUser->hasRole('super_admin')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
            'userId' => 'required|integer|exists:users,id',
        ]);

        $user = User::findOrFail($request->userId);
        $adminRole = Role::where('name', 'admin')->firstOrFail();

        $user->assignRole($adminRole);

        return response()->json([
            'message' => 'Роль администратора успешно назначена',
            'admin' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/admin",
     *     tags={"Admin"},
     *     summary="Получение списка всех администраторов",
     *     description="Возвращает список всех существующих администраторов",
     *     security={
     *         {"BearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Успешно получен список всех администраторов",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Список администраторов успешно получен"),
     *             @OA\Property(property="admins", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                     @OA\Property(property="email", type="string", example="ivan@example.com")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Администраторы не найдены",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Администраторы не найдены")
     *         )
     *     )
     * )
     */
    public function getAdmins(Request $request)
    {
        $this->authenticateUser($request);

        $admins = User::role('admin')->get();

        if ($admins->isEmpty()) {
            return response()->json(['message' => 'Администраторы не найдены'], 404);
        }

        return response()->json([
            'message' => 'Список администраторов успешно получен',
            'admins' => $admins->map(function ($admin) {
                return [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'email' => $admin->email,
                ];
            })->toArray(),
        ]);
    }
    private function authenticateUser(Request $request)
    {
        // Получаем токен из заголовка запроса
        $token = $request->bearerToken();

        // Проверяем наличие токена
        if (!$token) {
            abort(401, 'Unauthorized');
        }

        // Находим валидный токен для текущего пользователя
        $personalAccessToken = app(TokenRepository::class)->findValidToken(
            // Используем пользователя из запроса вместо Passport::token()
            $request->user(),
            // Получаем клиента из токена
            $request->user()->defaultClient()
        );

        // Проверяем наличие валидного токена
        if (!$personalAccessToken) {
            abort(401, 'Unauthorized');
        }

        // Возвращаем пользователя, связанного с токеном
        return $personalAccessToken->user;
    }
    /**
     * @OA\Get(
     *     path="/api/admin/{id}",
     *     tags={"Admin"},
     *     summary="Получение информации о конкретном администраторе",
     *     description="Возвращает информацию о конкретном администраторе по его ID",
     *     security={
     *         {"BearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID администратора",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация о администраторе успешно получена",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Информация о администраторе успешно получена"),
     *             @OA\Property(property="admin", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                 @OA\Property(property="email", type="string", example="ivan@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Администратор не найден",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Администратор не найден")
     *         )
     *     )
     * )
     */
    public function getAdmin($id)
    {
        $admin = User::role('admin')->find($id);

        if (!$admin) {
            return response()->json(['message' => 'Администратор не найден'], 404);
        }

        return response()->json([
            'message' => 'Информация о администраторе успешно получена',
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
        ]);
    }

    /**
     * @OA\Patch(
     *     path="/api/admin/{id}",
     *     tags={"Admin"},
     *     summary="Обновление информации об администраторе",
     *     description="Обновляет информацию об администраторе по его ID",
     *     security={
     *         {"BearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID администратора",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Новый Имя Администратора"),
     *             @OA\Property(property="email", type="string", format="email", example="new.admin@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация об администраторе успешно обновлена",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Информация об администраторе успешно обновлена"),
     *             @OA\Property(property="admin", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Новый Имя Администратора"),
     *                 @OA\Property(property="email", type="string", example="new.admin@example.com")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Администратор не найден",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Администратор не найден")
     *         )
     *     )
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Admin"},
     *     summary="Получение списка всех зарегистрированных пользователей",
     *     description="Возвращает список всех существующих пользователей",
     *     security={
     *         {"BearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Успешно получен список всех пользователей",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Список пользователей успешно получен"),
     *             @OA\Property(property="users", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Иван Иванов"),
     *                     @OA\Property(property="email", type="string", example="ivan@example.com"),
     *                     @OA\Property(property="roles", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="name", type="string", example="user")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователи не найдены",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Пользователи не найдены")
     *         )
     *     )
     * )
     */
    public function getAllUsers()
    {
        $users = User::with('roles')->get();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'Пользователи не найдены'], 404);
        }

        return response()->json([
            'message' => 'Список пользователей успешно получен',
            'users' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name'),
                ];
            })->toArray(),
        ]);
    }

    public function updateAdmin(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json(['message' => 'Администратор не найден'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $admin->id,
        ]);

        $admin->update($validatedData);

        return response()->json([
            'message' => 'Информация об администраторе успешно обновлена',
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{id}",
     *     tags={"Admin"},
     *     summary="Удаление администратора",
     *     description="Удаляет администратора по его ID",
     *     security={
     *         {"BearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID администратора",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Администратор успешно удален"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Администратор не найден",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Администратор не найден")
     *         )
     *     )
     * )
     */
    public function deleteAdmin($id)
    {
        $admin = User::where('role', 'admin')->find($id);

        if (!$admin) {
            return response()->json(['message' => 'Администратор не найден'], 404);
        }

        $admin->delete();

        return response()->noContent();
    }
}
