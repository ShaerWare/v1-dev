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

     * )
     */
    public function assignBuyerRole($userId)
    {
        $user = User::findOrFail($userId);
        $user->assignRole('buyer');

        return response()->json(['message' => 'Баер успешно назначен'], 200);
    }

    /**

     */
    public function removeBuyerRole($userId)
    {
        $user = User::findOrFail($userId);
        $user->removeRole('buyer');

        return response()->json(['message' => 'Роль баера успешно удалена'], 200);
    }
}
