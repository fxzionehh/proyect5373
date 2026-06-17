<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\DetallePedido;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PedidosController extends Controller
{
    // 📋 Lista de pedidos
    public function index()
    {
        return Inertia::render('Admin/Pedidos/Index', [
            'pedidos' => Pedido::with('detalles.producto')->latest()->get(),
            'productos' => Producto::all(),
            'mesas' => Mesa::all(),
        ]);
    }

    // 🔎 Edit (API simple)
    public function edit($id)
    {
        $pedido = Pedido::with('detalles.producto')->find($id);

        if (!$pedido) {
            return response()->json([
                'error' => 'No encontrado',
            ], 404);
        }

        return response()->json($pedido);
    }

 public function store(Request $request)
{
    $data = $request->validate([
        'mesa_id' => ['required', 'integer'],
        'nombre_cliente' => ['required', 'string'],
        'estado' => ['required', 'string'],
        'tipo_pedido' => ['required', 'string'],
        'producto_id' => ['required', 'exists:productos,id'],
    ]);

    return DB::transaction(function () use ($data) {

        $producto = Producto::findOrFail($data['producto_id']);

        $pedido = Pedido::create([
            'mesa_id' => $data['mesa_id'],
            'nombre_cliente' => $data['nombre_cliente'],
            'estado' => $data['estado'],
            'tipo_pedido' => $data['tipo_pedido'],
            'total' => $producto->precio_normal,
        ]);

        DetallePedido::create([
            'pedido_id' => $pedido->id,
            'producto_id' => $producto->id,
            'cantidad' => 1,
            'precio_unitario' => $producto->precio_normal,
            'subtotal' => $producto->precio_normal,
        ]);

        return back();
    });
}
    // 🗑️ Eliminar pedido
    public function destroy(Pedido $pedido)
    {
        $pedido->detalles()->delete();
        $pedido->delete();

        return back();
    }
}