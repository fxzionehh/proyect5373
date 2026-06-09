<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Mesa;
use App\Models\ProductoInsumo;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_entrega'   => ['required', 'in:presencial,delivery'],
            'mesa_id'        => ['nullable', 'integer', 'exists:mesas,id'],
            'nombre_cliente' => ['nullable', 'string', 'max:100'],

            'productos'            => ['required', 'array', 'min:1'],
            'productos.*.id'       => ['required', 'integer', 'exists:productos,id'],
            'productos.*.cantidad' => ['required', 'integer', 'min:1'],
            'productos.*.tamano'   => ['required', 'in:nano,mini,normal,max'],
        ]);

        // 🔐 usuario autenticado (ya protegido por middleware)
        $user = $request->user();

        $pedidoActivo = Pedido::where('user_id', $user->id)
            ->whereIn('estado', ['pendiente', 'en_preparacion'])
            ->exists();

        if ($pedidoActivo) {
            return back()->withErrors([
                'pedido' => 'Ya tienes un pedido activo.'
            ]);
        }

        try {

            $pedido = DB::transaction(function () use ($data, $user) {

                $total = 0;

                $pedido = Pedido::create([
                    'user_id'        => $user->id,
                    'tipo_pedido'    => $data['tipo_entrega'],
                    'mesa_id'        => $data['mesa_id'],
                    'nombre_cliente' => $data['nombre_cliente'],
                    'estado'         => 'pendiente',
                    'total'          => 0,
                ]);

                // mesa opcional
                if (!empty($data['mesa_id'])) {
                    Mesa::where('id', $data['mesa_id'])
                        ->update(['estado' => 'ocupada']);
                }

                foreach ($data['productos'] as $item) {

                    $producto = Producto::findOrFail($item['id']);

                    $subtotal = $producto->precio * $item['cantidad'];
                    $total += $subtotal;

                    $pedido->detalles()->create([
                        'producto_id'     => $producto->id,
                        'cantidad'        => $item['cantidad'],
                        'precio_unitario' => $producto->precio,
                        'subtotal'        => $subtotal,
                        'tamano'          => $item['tamano'],
                    ]);

                    $receta = ProductoInsumo::where('producto_id', $producto->id)
                        ->where('tamano', $item['tamano'])
                        ->get();

                    if ($receta->isEmpty()) {
                        throw new \Exception(
                            "No hay receta para {$producto->nombre} ({$item['tamano']})"
                        );
                    }

                    foreach ($receta as $r) {

                        $insumo = Insumo::findOrFail($r->insumo_id);

                        $cantidad = $r->cantidad * $item['cantidad'];

                        if ($insumo->stock < $cantidad) {
                            throw new \Exception(
                                "Stock insuficiente de {$insumo->nombre}"
                            );
                        }

                        $insumo->decrement('stock', $cantidad);
                    }
                }

                $pedido->update([
                    'total' => $total,
                ]);

                return $pedido;
            });

        } catch (Throwable $e) {

            return back()->withErrors([
                'pedido' => $e->getMessage()
            ]);
        }

        return back()->with('success', 'Pedido creado correctamente');
    }
}