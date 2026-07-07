<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UsuarioController extends Controller
{

    public function index()
    {

        $roles = Role::all();
   //dd($roles);
        return Inertia::render('Admin/Usuarios/Index', [
            'usuarios' => User::with('role')->orderBy('name')->get(),
            'roles'    => Role::all(),
        ]);
    }

  
    public function edit($id)
    {
        $usuario = User::find($id);
        return $usuario 
            ? response()->json($usuario) 
            : response()->json(['error' => 'Usuario no encontrado'], 404);
    }

  
  public function store(Request $request)
{
    $id = $request->id;

    $validated = $request->validate([
        'name' => 'required|string|max:100',
        'email' => [
            'required',
            'email',
            Rule::unique('users', 'email')->ignore($id),
        ],
        'role_id' => 'required|exists:roles,id',
        'password' => $id
            ? 'nullable|string|min:8|confirmed'
            : 'required|string|min:8|confirmed',
    ]);

    if ($id) {

        $usuario = User::findOrFail($id);

        $usuario->name = $validated['name'];
        $usuario->email = $validated['email'];
        $usuario->role_id = $validated['role_id'];

        if (!empty($validated['password'])) {
            $usuario->password = Hash::make($validated['password']);
        }

        $usuario->save();

    } else {

        $usuario = new User();

        $usuario->name = $validated['name'];
        $usuario->email = $validated['email'];
        $usuario->role_id = $validated['role_id'];
        $usuario->password = Hash::make($validated['password']);

        $usuario->save();
    }

    return back();
}
    public function destroy(User $usuario)
    {
        $usuario->delete();
        return back()->with('message', 'Usuario eliminado');
    }
}