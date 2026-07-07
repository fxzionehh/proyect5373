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
        dd($request->all());
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email' => [
    'required',
    'email',
    Rule::unique('users', 'email')->ignore($id),
],
            'role_id'  => 'required|exists:roles,id',
            'password' => $id ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
        ]);

    
        $usuario = $id ? User::findOrFail($id) : new User();

  
        $usuario->fill($request->only(['name', 'email', 'role_id']));

       
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

    
        $usuario->save();

        return back()->with('message', 'Usuario guardado correctamente');
    }

    public function destroy(User $usuario)
    {
        $usuario->delete();
        return back()->with('message', 'Usuario eliminado');
    }
}