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
    public function show(Pedido $pedido)
    {
        return response()->json(
            $pedido->load('detalles.producto', 'mesa')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mesa_id'        => ['required', 'integer', 'exists:mesas,id'],
            'nombre_cliente' => ['required', 'string', 'max:100'],
            'producto_id'    => ['required', 'integer', 'exists:productos,id'],
            'tamano'         => ['required', 'in:nano,mini,normal,max'],
        ]);

        // Evitar pedidos duplicados activos
        $pedidoActivo = Pedido::where('mesa_id', $data['mesa_id'])
            ->whereIn('estado', ['pendiente', 'en_preparacion', 'listo'])
            ->exists();

        if ($pedidoActivo) {
            return back()->withErrors([
                'pedido' => 'Esta mesa ya tiene un pedido activo.'
            ]);
        }

        try {

            DB::transaction(function () use ($data) {

                // ----------------------------
                // MAPA SEGURO DE INSUMOS
                // ----------------------------
                $mapaInsumos = [
                    'nano'   => 'Vaso Nano',
                    'mini'   => 'Vaso Mini',
                    'normal' => 'Vaso Normal',
                    'max'    => 'Vaso Max',
                ];

                if (!isset($mapaInsumos[$data['tamano']])) {
                    throw new \Exception("Tamaño inválido.");
                }

                $insumo = Insumo::where('nombre', $mapaInsumos[$data['tamano']])->first();

                if (!$insumo) {
                    throw new \Exception("No existe insumo configurado para: {$data['tamano']}");
                }

                if ($insumo->stock <= 0) {
                    throw new \Exception("Stock insuficiente para: {$data['tamano']}");
                }

                $insumo->decrement('stock', 1);

                // ----------------------------
                // PRODUCTO Y PRECIO
                // ----------------------------
                $producto = Producto::findOrFail($data['producto_id']);

                $campoPrecio = 'precio_' . $data['tamano'];

                if (!isset($producto->$campoPrecio)) {
                    throw new \Exception("El producto no tiene precio para: {$data['tamano']}");
                }

                $precioFinal = $producto->$campoPrecio;

                // ----------------------------
                // CREAR PEDIDO
                // ----------------------------
                $pedido = Pedido::create([
                    'mesa_id'        => $data['mesa_id'],
                    'nombre_cliente' => $data['nombre_cliente'],
                    'estado'         => 'pendiente',
                    'total'          => $precioFinal,
                ]);

                // ----------------------------
                // DETALLE
                // ----------------------------
                $pedido->detalles()->create([
                    'producto_id'     => $producto->id,
                    'cantidad'        => 1,
                    'precio_unitario' => $precioFinal,
                    'subtotal'        => $precioFinal,
                    'tamano'          => $data['tamano'],
                ]);

                // ----------------------------
                // MESA OCUPADA
                // ----------------------------
                Mesa::where('id', $data['mesa_id'])
                    ->update(['estado' => 'ocupada']);
            });

        } catch (\Throwable $e) {

            \Log::error('Error creando pedido: ' . $e->getMessage());

            return back()->withErrors([
                'pedido' => 'Error interno al crear el pedido.'
            ]);
        }

        return back()->with('success', 'Pedido creado correctamente');
    }
}