<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller{
  
    public function create(){
        return Inertia::render('Login');
    }


   public function store(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {

        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->role?->nombre === 'administrador') {
            return redirect('/dashboard');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

    }


}

    // 🔵 logout
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}