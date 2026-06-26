<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UsuarioController extends Controller
{

    public function index()
    {
        return Inertia::render('Admin/Usuarios/Index', [
            'usuarios' => User::orderBy('name')->get(),
        ]);
    }

    // 🔎 Edit (API simple)
    public function edit($id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json([
                'error' => ' usuario no encontrado',
            ], 404);
        }

        return response()->json($usuario);
    }

    // 💾 Crear / actualizar usuario
    public function store(Request $request){
        $id = $request->id;

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($id) {
            // ✏️ ACTUALIZAR
            $usuario = User::findOrFail($id);

            $usuario->name = $validated['name'];
            $usuario->email = $validated['email'];
            $usuario->password = bcrypt($validated['password']);
        } else {
            // ➕ CREAR
            $usuario = new User();

            $usuario->name = $validated['name'];
            $usuario->email = $validated['email'];
            $usuario->password = bcrypt($validated['password']);
        }

        $usuario->save();

        return back();
    }

    // 🗑️ Eliminar usuario
    public function destroy(User $usuario){
        $usuario->delete();

        return back();
    }
}
