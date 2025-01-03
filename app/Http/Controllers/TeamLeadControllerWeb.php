<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class TeamLeadControllerWeb extends Controller
{
    /**
     * Отобразить список пользователей с ролью "buyer".
     */
    public function indexBuyers()
    {
        // Получаем пользователей с ролью "buyer"
        $buyers = User::role('buyer')->get();

        // Отображаем пользователей без роли "buyer" (для назначения роли)
        $usersWithoutBuyerRole = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'buyer');
        })->get();

        return view('teamlead.buyers.index', compact('buyers', 'usersWithoutBuyerRole'));
    }

    /**
     * Назначить роль "buyer" пользователю.
     */
    public function assignBuyerRole($userId)
    {
        $user = User::findOrFail($userId);
        $user->assignRole('buyer');

        return redirect()->route('teamlead.buyers.index')->with('success', 'Роль "Байер" успешно назначена.');
    }

    /**
     * Удалить роль "buyer" у пользователя.
     */
    public function removeBuyerRole($userId)
    {
        $user = User::findOrFail($userId);
        $user->removeRole('buyer');

        return redirect()->route('teamlead.buyers.index')->with('success', 'Роль "Байер" успешно снята.');
    }
}
