<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AdminControllerWeb extends Controller
{
    public function indexRoles()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }

    public function createRole()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
        ]);

        // Создаём роль
    $role = Role::create(['name' => $request->name]);

    // Назначаем привилегии, если они указаны
    if ($request->has('permissions')) {
        $role->givePermissionTo($request->permissions);
    }

    // Редирект на список ролей с уведомлением
    return redirect()->route('admin.roles.index')->with('success', 'Роль успешно создана.');
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'array',
        ]);

        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Роль успешно обновлена.');
    }

    public function destroyRole($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Роль успешно удалена.');
    }
}
