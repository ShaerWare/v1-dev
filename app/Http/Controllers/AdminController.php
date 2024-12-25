<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

/**
 * @OA\Info(
 *     title="Role and Permission Management API",
 *     version="1.0.0",
 *     description="API для управления ролями и привилегиями"
 * )
 * @OA\Tag(name="Admin", description="Управление ролями и привилегиями")
 */
class AdminController extends Controller
{
    /*public function __construct()
    {
      //  $this->middleware('role:admin');
    }

    /**
     * @OA\Post(
     *     path="/api/roles",
     *     summary="Создать роль",
     *     tags={"Admin"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="editor"),
     *             @OA\Property(property="permissions", type="array", @OA\Items(type="string", example="create-post"))
     *         )
     *     ),
     *     @OA\Response(response=201, description="Роль успешно создана"),
     *     @OA\Response(response=400, description="Ошибка валидации")
     * )
     */
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->permissions) {
            $role->givePermissionTo($request->permissions);
        }

        return response()->json($role, 201);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{userId}/roles",
     *     summary="Назначить роль пользователю",
     *     tags={"Admin"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID пользователя",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="role", type="string", example="editor")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Роль успешно назначена"),
     *     @OA\Response(response=404, description="Пользователь не найден")
     * )
     */
    public function assignRoleToUser(Request $request, $userId)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::findOrFail($userId);
        $user->assignRole($request->role);

        return response()->json($user, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/roles/{roleId}/permissions",
     *     summary="Назначить привилегии роли",
     *     tags={"Admin"},
     *     @OA\Parameter(
     *         name="roleId",
     *         in="path",
     *         required=true,
     *         description="ID роли",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="permissions", type="array", @OA\Items(type="string", example="create-post"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Привилегии успешно назначены"),
     *     @OA\Response(response=404, description="Роль не найдена")
     * )
     */
    public function assignPermissionsToRole(Request $request, $roleId)
    {
        $request->validate([
            'permissions' => 'array',
        ]);

        $role = Role::findOrFail($roleId);
        $role->syncPermissions($request->permissions);

        return response()->json($role, 200);
    }
}
