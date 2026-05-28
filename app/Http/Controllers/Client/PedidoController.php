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
        // 1. Modificamos la validación para tus tamaños reales y la movemos al producto
        $data = $request->validate([
            'tipo_entrega'   => ['required', 'in:presencial'],
            'mesa_id'        => ['required', 'integer', 'exists:mesas,id'],
            'nombre_cliente' => ['nullable', 'string', 'max:100'],
            'token_cliente'  => ['required', 'string', 'max:255'],
            
            'productos'            => ['required', 'array', 'min:1'],
            'productos.*.id'       => ['required', 'integer', 'exists:productos,id'],
            'productos.*.cantidad' => ['required', 'integer', 'min:1'],
            // Cada producto define su propio tamaño (Los vasos se descuentan según esto)
            'productos.*.tamano'   => ['required', 'in:nano,mini,normal,max'], 
        ]);

        $pedidoActivo = Pedido::where('mesa_id', $data['mesa_id'])
            ->where('token_cliente', $data['token_cliente'])
            ->whereIn('estado', ['pendiente', 'en_preparacion'])
            ->exists();

        if ($pedidoActivo) {
            return back()->withErrors([
                'pedido' => 'Ya tienes un pedido activo.'
            ]);
        }

        try {
            $pedido = DB::transaction(function () use ($data) {
                $total = 0;

                $pedido = Pedido::create([
                    'tipo_pedido'    => 'presencial',
                    'mesa_id'        => $data['mesa_id'],
                    'token_cliente'  => $data['token_cliente'],
                    'nombre_cliente' => $data['nombre_cliente'],
                    'estado'         => 'pendiente',
                    'total'          => 0,
                ]);

                Mesa::where('id', $data['mesa_id'])->update([
                    'estado' => 'ocupada'
                ]);

                foreach ($data['productos'] as $item) {
                    $producto = Producto::findOrFail($item['id']);

                    $subtotal = $producto->precio * $item['cantidad'];
                    $total += $subtotal;

                    // Registramos el detalle con el tamaño específico de ESTE producto
                    $pedido->detalles()->create([
                        'producto_id'     => $producto->id,
                        'cantidad'        => $item['cantidad'],
                        'precio_unitario' => $producto->precio,
                        'subtotal'        => $subtotal,
                        'tamano'          => $item['tamano'], 
                    ]);

                    // Buscamos la receta que coincida con el Producto Y el Tamaño seleccionado
                    $receta = ProductoInsumo::where('producto_id', $producto->id)
                        ->where('tamano', $item['tamano'])
                        ->get();

                    if ($receta->isEmpty()) {
                        throw new \Exception("No existe una receta configurada para {$producto->nombre} en tamaño {$item['tamano']}.");
                    }

                    foreach ($receta as $itemReceta) {
                        $insumo = Insumo::findOrFail($itemReceta->insumo_id);

                        $cantidadADescontar = $itemReceta->cantidad * $item['cantidad'];

                        if ($insumo->stock < $cantidadADescontar) {
                            throw new \Exception("Stock insuficiente de {$insumo->nombre} para preparar el tamaño {$item['tamano']}");
                        }

                        $insumo->decrement('stock', $cantidadADescontar);
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

        session(['token_cliente' => $data['token_cliente']]);

        return back()->with('success', 'Pedido creado correctamente');
    }
}