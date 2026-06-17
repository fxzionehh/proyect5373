<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    // 📋 Lista de roles
    public function index()
    {
        return Inertia::render('Admin/Roles/Index', [
            'roles' => Role::with('permissions')->latest()->get(),
            'allPermissions' => Permission::all(),
        ]);
    }

    // 🔎 Edit (API simple)
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

    // 💾 Crear / actualizar rol
    public function store(Request $request)
    {
        $id = $request->id;

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'permissions' => 'nullable|array',
        ]);

        if ($id) {
            // ✏️ ACTUALIZAR
            $role = Role::findOrFail($id);

            $role->nombre = $validated['nombre'];
            $role->save();
        } else {
            // ➕ CREAR
            $role = new Role();

            $role->nombre = $validated['nombre'];
            $role->save();
        }

        // 🔐 Sincronizar permisos
        $role->permissions()->sync(
            $request->permissions ?? []
        );

        return back();
    }

    // 🗑️ Eliminar rol
    public function destroy(Role $role)
    {
        $role->permissions()->detach();
        $role->delete();

        return back();
    }
}