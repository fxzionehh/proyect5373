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
    // 📋 Lista
    public function index()
    {
        return Inertia::render('Admin/Pedidos/Index', [
            'pedidos' => Pedido::with('detalles.producto')->latest()->get(),
            'productos' => Producto::orderBy('nombre')->get(),
            'mesas' => Mesa::orderBy('numero')->get(),
        ]);
    }

    // 🔎 Edit simple (API)
    public function edit($id)
    {
        $pedido = Pedido::with('detalles.producto')->find($id);

        if (!$pedido) {
            return response()->json([
                'error' => 'Pedido no encontrado',
            ], 404);
        }

        return response()->json($pedido);
    }

    // 💾 CREAR / ACTUALIZAR (SIMPLE COMO USUARIOCONTROLLER)
    public function store(Request $request)
    {
        $id = $request->id;

        $validated = $request->validate([
            'mesa_id' => 'required|integer',
            'nombre_cliente' => 'required|string|max:100',
            'estado' => 'required|string',
            'tipo_pedido' => 'required|string',
            'producto_id' => 'required|exists:productos,id',
        ]);

        $producto = Producto::findOrFail($validated['producto_id']);

        if ($id) {
            // ✏️ ACTUALIZAR
            $pedido = Pedido::findOrFail($id);

            $pedido->mesa_id = $validated['mesa_id'];
            $pedido->nombre_cliente = $validated['nombre_cliente'];
            $pedido->estado = $validated['estado'];
            $pedido->tipo_pedido = $validated['tipo_pedido'];
            $pedido->total = $producto->precio_normal;

            $pedido->save();

            // actualizar detalle simple (1 producto)
            $detalle = $pedido->detalles()->first();

            if ($detalle) {
                $detalle->producto_id = $producto->id;
                $detalle->cantidad = 1;
                $detalle->precio_unitario = $producto->precio_normal;
                $detalle->subtotal = $producto->precio_normal;
                $detalle->save();
            }

        } else {
            // ➕ CREAR
            $pedido = new Pedido();

            $pedido->mesa_id = $validated['mesa_id'];
            $pedido->nombre_cliente = $validated['nombre_cliente'];
            $pedido->estado = $validated['estado'];
            $pedido->tipo_pedido = $validated['tipo_pedido'];
            $pedido->total = $producto->precio_normal;

            $pedido->save();

            $pedido->detalles()->create([
                'producto_id' => $producto->id,
                'cantidad' => 1,
                'precio_unitario' => $producto->precio_normal,
                'subtotal' => $producto->precio_normal,
            ]);
        }

        return back();
    }

    // 🗑️ Eliminar
    public function destroy(Pedido $pedido)
    {
        $pedido->detalles()->delete();
        $pedido->delete();

        return back();
    }
}