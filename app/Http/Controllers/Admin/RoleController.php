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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:roles,nombre,' . ($request->id ?? 'NULL'),
            'permissions' => 'nullable|array',
        ]);

        $role = Role::updateOrCreate(
            ['id' => $request->id],
            ['nombre' => $validated['nombre']]
        );

        $role->permissions()->sync($request->permissions ?? []);

        $mensaje = $request->id ? 'Rol actualizado exitosamente' : 'Rol creado exitosamente';
        return back()->with('success', $mensaje);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->users()->count() > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar este rol porque tiene usuarios asignados.']);
        }

        $role->permissions()->detach();
        $role->delete();

        return back()->with('success', 'Rol eliminado correctamente');
    }
}