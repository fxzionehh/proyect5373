<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validar los datos de entrada
        $data = $request->validate([
            'mesa_id'        => ['required', 'integer', 'exists:mesas,id'],
            'nombre_cliente' => ['required', 'string', 'max:100'],
            'producto_id'    => ['required', 'integer', 'exists:productos,id'],
            'tamano'         => ['required', 'in:nano,mini,normal,max'],
        ]);

        // 2. Verificar si la mesa ya tiene un pedido activo
        $pedidoActivo = Pedido::where('mesa_id', $data['mesa_id'])
            ->whereIn('estado', ['pendiente', 'en_preparacion'])
            ->exists();

        if ($pedidoActivo) {
            return back()->withErrors([
                'pedido' => 'Esta mesa ya tiene un pedido activo'
            ]);
        }

        // 3. Procesar el pedido dentro de una transacción
        DB::transaction(function () use ($data) {
            
            // Obtener el producto
            $producto = Producto::findOrFail($data['producto_id']);

            // Determinar dinámicamente el campo de precio a usar
            // Ej: si tamano es 'nano', busca 'precio_nano' en el modelo Producto
            $campoPrecio = 'precio_' . $data['tamano'];
            $precioFinal = $producto->$campoPrecio;

            // Crear el pedido principal
            $pedido = Pedido::create([
                'mesa_id'        => $data['mesa_id'],
                'nombre_cliente' => $data['nombre_cliente'],
                'estado'         => 'pendiente',
                'total'          => $precioFinal, // Ahora garantizamos que no sea null
            ]);

            // Crear el detalle del pedido
            $pedido->detalles()->create([
                'producto_id'     => $producto->id,
                'cantidad'        => 1,
                'precio_unitario' => $precioFinal,
                'subtotal'        => $precioFinal,
                'tamano'          => $data['tamano'],
            ]);

            // Marcar mesa como ocupada
            Mesa::where('id', $data['mesa_id'])->update([
                'estado' => 'ocupada'
            ]);
        });

        return back()->with('success', 'Pedido creado correctamente');
    }
}