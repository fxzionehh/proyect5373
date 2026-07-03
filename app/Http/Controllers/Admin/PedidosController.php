<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PedidosController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Pedidos/Index', [
            'pedidos' => Pedido::with('detalles.producto')->latest()->get(),
            'productos' => Producto::orderBy('nombre')->get(),
            'mesas' => Mesa::orderBy('numero')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mesa_id' => 'nullable|integer', 
            'nombre_cliente' => 'required|string|max:100',
            'estado' => 'required|string',
            'tipo_pedido' => 'required|string',
            'producto_id' => 'required|exists:productos,id',
        ]);

        $producto = Producto::findOrFail($validated['producto_id']);

     
        $pedido = $request->id ? Pedido::findOrFail($request->id) : new Pedido();

        $pedido->fill([
            'mesa_id' => $validated['mesa_id'],
            'nombre_cliente' => $validated['nombre_cliente'],
            'estado' => $validated['estado'],
            'tipo_pedido' => $validated['tipo_pedido'],
            'total' => $producto->precio_normal,
        ])->save();

      
        $pedido->detalles()->updateOrCreate(
            ['pedido_id' => $pedido->id],
            [
                'producto_id' => $producto->id,
                'cantidad' => 1,
                'precio_unitario' => $producto->precio_normal,
                'subtotal' => $producto->precio_normal,
            ]
        );

        return back();
    }
}