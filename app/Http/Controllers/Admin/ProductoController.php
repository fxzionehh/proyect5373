<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductoController extends Controller
{
    // 📋 Lista de productos
    public function index()
    {
        return Inertia::render('Admin/Productos/Index', [
            'productos' => Producto::orderBy('nombre')->get(),
            'insumos' => Insumo::orderBy('nombre')->get(),
        ]);
    }

    // 🔎 Edit (API simple)
    public function edit($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json([
                'error' => 'No encontrado',
            ], 404);
        }

        return response()->json($producto);
    }

    // 💾 Crear / actualizar producto
    public function store(Request $request)
    {
        $id = $request->id;

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'precio_nano' => 'required|numeric|min:0',
            'precio_mini' => 'required|numeric|min:0',
            'precio_normal' => 'required|numeric|min:0',
            'precio_max' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        if ($id) {
            // ✏️ ACTUALIZAR
            $producto = Producto::findOrFail($id);

            $producto->nombre = $validated['nombre'];
            $producto->precio_nano = $validated['precio_nano'];
            $producto->precio_mini = $validated['precio_mini'];
            $producto->precio_normal = $validated['precio_normal'];
            $producto->precio_max = $validated['precio_max'];
            $producto->stock = $validated['stock'];

            $producto->save();
        } else {
            // ➕ CREAR
            $producto = new Producto();

            $producto->nombre = $validated['nombre'];
            $producto->precio_nano = $validated['precio_nano'];
            $producto->precio_mini = $validated['precio_mini'];
            $producto->precio_normal = $validated['precio_normal'];
            $producto->precio_max = $validated['precio_max'];
            $producto->stock = $validated['stock'];

            $producto->save();
        }

        return back();
    }

    // 🗑️ Eliminar producto
    public function destroy(Producto $producto)
    {
        $producto->delete();

        return back();
    }
}