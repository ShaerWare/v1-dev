<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class TeamLeadControllerWeb extends Controller
{
    public function indexBuyers()
    {
        $users = User::role('buyer')->get();
        return view('teamlead.buyers.index', compact('users'));
    }

    public function assignBuyerRole($userId)
    {
        $user = User::findOrFail($userId);
        $user->assignRole('buyer');

        return redirect()->route('teamlead.buyers.index')->with('success', 'Баер успешно назначен.');
    }

    public function removeBuyerRole($userId)
    {
        $user = User::findOrFail($userId);
        $user->removeRole('buyer');

        return redirect()->route('teamlead.buyers.index')->with('success', 'Роль баера успешно удалена.');
    }
}
