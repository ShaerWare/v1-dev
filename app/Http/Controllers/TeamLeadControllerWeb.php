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
    if (auth()->user()->hasRole('admin')) {
        // Администратор видит всех байеров
        $buyers = User::role('buyer')->with('assignedBy')->paginate(10);
    } else {
        // Тимлид видит только своих байеров
        $buyers = User::role('buyer')
            ->where('assigned_by', auth()->id())
            ->with('assignedBy')
            ->paginate(10);
    }

    // Отображаем пользователей без роли "buyer" только для тимлида
    $usersWithoutBuyerRole = [];
    if (auth()->user()->hasRole('team_lead')) {
        $usersWithoutBuyerRole = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'buyer');
        })->get();
    }

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
