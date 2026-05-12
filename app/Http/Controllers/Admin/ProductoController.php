<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductoController extends Controller
{
    // 📄 LISTAR PRODUCTOS (vista principal)
    public function index()
    {
        return Inertia::render('Admin/Productos/Index', [
            'productos' => Producto::orderBy('nombre')->get()
        ]);
    }

    // 💾 CREAR PRODUCTO
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        Producto::create($data);

        return back(); // vuelve a la misma página
    }

    // 🔄 ACTUALIZAR PRODUCTO
    public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'precio' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $producto->update($data);

        return back();
    }

    // ❌ ELIMINAR PRODUCTO
    public function destroy(Producto $producto)
    {
        $producto->delete();

        return back();
    }
}