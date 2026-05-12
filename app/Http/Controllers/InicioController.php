<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\User;
use Inertia\Inertia;

class InicioController extends Controller
{
    public function index()
    {
        $productos = Producto::all();
        $usuarios = User::all();

        return Inertia::render('Inicio', [
            'productos' => $productos,
            'usuarios' => $usuarios,
        ]);
    }
}