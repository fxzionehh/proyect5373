<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{

    public function index()
    {
        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::with('permissions')->latest()->get(),
            'allPermissions' => Permission::all(),
        ]);
    }


    public function edit($id)
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return response()->json([
                'error' => 'No encontrado',
            ], 404);
        }

        return response()->json($role);
    }

  public function store(Request $request)
{
    $validated = $request->validate([
        'nombre' => 'required|string|max:100',
        'permissions' => 'nullable|array',
    ]);

    $role = Role::updateOrCreate(
        ['id' => $request->id],
        ['nombre' => $validated['nombre']]
    );

    $role->permissions()->sync($request->permissions ?? []);

    return back();
}

public function destroy($id)
{
    $role = Role::findOrFail($id);
    $role->permissions()->detach();
    $role->delete();

    return back();
}
}