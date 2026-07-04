<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Mesa;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function show($pedido)
    {
        $pedido = Pedido::with('detalles.producto', 'mesa')->find($pedido);

        if (!$pedido) {
            return response()->json([
                'error' => 'Pedido no encontrado'
            ], 404);
        }

        return response()->json($pedido);
    }


    public function store(Request $request)
{
    try {

        $data = $request->validate([
            'mesa_id'        => ['required', 'integer', 'exists:mesas,id'],
            'nombre_cliente' => ['required', 'string', 'max:100'],
            'producto_id'    => ['required', 'integer', 'exists:productos,id'],
            'tamano'         => ['required', 'in:nano,mini,normal,max'],
        ]);

        $pedidoActivo = Pedido::where('mesa_id', $data['mesa_id'])
            ->whereIn('estado', ['pendiente', 'en_preparacion', 'listo'])
            ->exists();

        if ($pedidoActivo) {
            return back()->withErrors([
                'pedido' => 'Esta mesa ya tiene un pedido activo.'
            ]);
        }

        DB::transaction(function () use ($data) {

            $insumo = \App\Models\Insumo::where('nombre', 'LIKE', "%{$data['tamano']}%")->first();

            if (!$insumo) {
                throw new \Exception("No se encontró el insumo para el tamaño: " . $data['tamano']);
            }

            if ($insumo->stock <= 0) {
                throw new \Exception("No hay stock del insumo.");
            }

            $insumo->decrement('stock', 1);

            $producto = Producto::findOrFail($data['producto_id']);

            $campoPrecio = 'precio_' . $data['tamano'];

            $precioFinal = $producto->$campoPrecio;

            $pedido = Pedido::create([
                'mesa_id'        => $data['mesa_id'],
                'nombre_cliente' => $data['nombre_cliente'],
                'estado'         => 'pendiente',
                'total'          => $precioFinal,
            ]);

            $pedido->detalles()->create([
                'producto_id'     => $producto->id,
                'cantidad'        => 1,
                'precio_unitario' => $precioFinal,
                'subtotal'        => $precioFinal,
                'tamano'          => $data['tamano'],
            ]);

            Mesa::where('id', $data['mesa_id'])->update([
                'estado' => 'ocupada'
            ]);
        });

        return back()->with('success', 'Pedido creado correctamente.');

    } catch (\Throwable $e) {

        dd([
            'mensaje' => $e->getMessage(),
            'archivo' => $e->getFile(),
            'linea'   => $e->getLine(),
            'trace'   => $e->getTraceAsString(),
        ]);
    }
}
}