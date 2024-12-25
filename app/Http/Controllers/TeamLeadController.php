<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

/**
 * @OA\Tag(name="TeamLead", description="Управление баерами")
 */
class TeamLeadController extends Controller
{
   /* public function __construct()
    {
      //  $this->middleware('role:team_lead');
    }

    /**
     * @OA\Post(
     *     path="/api/users/{userId}/assign-buyer",
     *     summary="Назначить баера",
     *     tags={"TeamLead"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID пользователя",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(response=200, description="Баер успешно назначен"),
     *     @OA\Response(response=404, description="Пользователь не найден")
     * )
     */
    public function assignBuyerRole($userId)
    {
        $user = User::findOrFail($userId);
        $user->assignRole('buyer');

        return response()->json(['message' => 'Баер успешно назначен'], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{userId}/remove-buyer",
     *     summary="Удалить роль баера",
     *     tags={"TeamLead"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID пользователя",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(response=200, description="Роль баера успешно удалена"),
     *     @OA\Response(response=404, description="Пользователь не найден")
     * )
     */
    public function removeBuyerRole($userId)
    {
        $user = User::findOrFail($userId);
        $user->removeRole('buyer');

        return response()->json(['message' => 'Роль баера успешно удалена'], 200);
    }
}
