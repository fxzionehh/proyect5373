<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductoController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Productos/Index', [

            'productos' => Producto::orderBy('nombre')->get(),
            'insumos' => Insumo::orderBy('nombre')->get(),

        ]);
    }

    public function store(Request $request)
    {
      $data = $request->validate([
    'nombre' => ['required', 'string', 'max:100'],
    'precio_nano' => ['required', 'numeric', 'min:0'],
    'precio_mini' => ['required', 'numeric', 'min:0'],
    'precio_normal' => ['required', 'numeric', 'min:0'],
    'precio_max' => ['required', 'numeric', 'min:0'],
    'stock' => ['required', 'integer', 'min:0'],
]);

        Producto::create($data);

        return back();
    }

    public function update(Request $request, Producto $producto)
    {
      $data = $request->validate([
    'nombre' => ['required', 'string', 'max:100'],
    'precio_nano' => ['required', 'numeric', 'min:0'],
    'precio_mini' => ['required', 'numeric', 'min:0'],
    'precio_normal' => ['required', 'numeric', 'min:0'],
    'precio_max' => ['required', 'numeric', 'min:0'],
    'stock' => ['required', 'integer', 'min:0'],
]);

        $producto->update($data);

        return back();
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return back();
    }
}