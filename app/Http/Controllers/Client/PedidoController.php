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
        $data = $request->validate([
            'mesa_id'        => ['required', 'integer', 'exists:mesas,id'],
            'nombre_cliente' => ['required', 'string', 'max:100'],
            'producto_id'    => ['required', 'integer', 'exists:productos,id'],
            'tamano'         => ['required', 'in:nano,mini,normal,max'],
        ]);

        try {

            DB::transaction(function () use ($data) {

                // Bloquea la mesa mientras se crea el pedido
                $mesa = Mesa::where('id', $data['mesa_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // Si ya está ocupada, no permite otro pedido
                if ($mesa->estado === 'ocupada') {
                    throw new \Exception('Esta mesa ya tiene un pedido activo.');
                }

                // Obtener el vaso correspondiente
                $nombreVaso = match ($data['tamano']) {
                    'nano'   => 'Vaso Nano 120ml',
                    'mini'   => 'Vaso Mini 240ml',
                    'normal' => 'Vaso Normal 360ml',
                    'max'    => 'Vaso Max 480ml',
                };

                $insumo = Insumo::where('nombre', $nombreVaso)->first();

                if (!$insumo) {
                    throw new \Exception("No existe el insumo {$nombreVaso}");
                }

                if ($insumo->stock <= 0) {
                    throw new \Exception("No hay stock de {$nombreVaso}");
                }

                // Descontar un vaso
                $insumo->decrement('stock', 1);

                // Obtener producto
                $producto = Producto::findOrFail($data['producto_id']);

                $campoPrecio = 'precio_' . $data['tamano'];

                $precioFinal = $producto->$campoPrecio;

                // Crear pedido
                $pedido = Pedido::create([
                    'mesa_id'        => $mesa->id,
                    'nombre_cliente' => $data['nombre_cliente'],
                    'estado'         => 'pendiente',
                    'total'          => $precioFinal,
                ]);

                // Crear detalle
                $pedido->detalles()->create([
                    'producto_id'     => $producto->id,
                    'cantidad'        => 1,
                    'precio_unitario' => $precioFinal,
                    'subtotal'        => $precioFinal,
                    'tamano'          => $data['tamano'],
                ]);

                // Marcar mesa ocupada
                $mesa->estado = 'ocupada';
                $mesa->save();
            });

            return back()->with('success', 'Pedido creado correctamente.');

        } catch (\Throwable $e) {

            return back()->withErrors([
                'pedido' => $e->getMessage()
            ]);
        }
    }
}