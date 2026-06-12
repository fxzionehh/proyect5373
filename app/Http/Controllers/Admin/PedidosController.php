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
    public function index()
    {
        return Inertia::render('Admin/Pedidos/Index', [
            'pedidos' => Pedido::with('detalles.producto')->latest()->get(),
            'productos' => Producto::all(),
            'mesas' => Mesa::all(),
        ]);
    }

   public function store(Request $request)
{
   
    $data = $request->validate([
        'mesa_id' => ['required', 'integer'],
        'nombre_cliente' => ['required', 'string'],
        'estado' => ['required', 'string'],
        'tipo_pedido' => ['required', 'string'],
        'productos' => ['required', 'array', 'min:1'],
        'productos.*.id' => ['required', 'exists:productos,id'],
        'productos.*.cantidad' => ['required', 'integer', 'min:1'],
    ]);
 //dd($data);
    return DB::transaction(function () use ($data) {
        // 1. Crear el Pedido
        $pedido = Pedido::create([
            'mesa_id' => $data['mesa_id'],
            'nombre_cliente' => $data['nombre_cliente'],
            'estado' => $data['estado'], // Corregido aquí
            'tipo_pedido' => $data['tipo_pedido'],
            'total' => 0, 
        ]);

        $totalPedido = 0;

        // 2. Crear los Detalles
        foreach ($data['productos'] as $item) {
            $producto = Producto::findOrFail($item['id']);
            $subtotal = $producto->precio * $item['cantidad'];
            $totalPedido += $subtotal;

            DetallePedido::create([
                'pedido_id' => $pedido->id,
                'producto_id' => $producto->id,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $producto->precio,
                'subtotal' => $subtotal,
            ]);
        }

        // 3. Guardar el total calculado
        $pedido->update(['total' => $totalPedido]);

        return back();
    });
}
}