<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_entrega' => ['required', 'in:presencial'],

            'mesa_id' => [
                'required',
                'integer',
                Rule::exists('mesas', 'id')->where(function ($query) {
                    $query->where('activa', true);
                }),
            ],

            'nombre_cliente' => ['nullable', 'string', 'max:100'],

            'productos' => ['required', 'array', 'min:1'],
            'productos.*.id' => ['required', 'integer', 'exists:productos,id'],
            'productos.*.cantidad' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        DB::transaction(function () use ($data) {
            $total = 0;
            $productosValidados = [];

            foreach ($data['productos'] as $item) {
                $producto = Producto::where('id', $item['id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($producto->stock < $item['cantidad']) {
                    abort(422, "No hay stock suficiente para {$producto->nombre}");
                }

                $subtotal = $producto->precio * $item['cantidad'];
                $total += $subtotal;

                $productosValidados[] = [
                    'producto' => $producto,
                    'cantidad' => $item['cantidad'],
                    'subtotal' => $subtotal,
                ];
            }
if (!session('mesa_id')) {
    abort(403, 'Debes escanear el QR de una mesa.');
}

if (now()->greaterThan(session('mesa_expira'))) {
    session()->forget(['mesa_id', 'mesa_expira']);
    abort(403, 'La sesión de la mesa expiró. Escanea nuevamente el QR.');
}
            $pedido = Pedido::create([
                'tipo_pedido' => 'presencial',
                'mesa_id' => $data['mesa_id'],
                'nombre_cliente' => $data['nombre_cliente'] ?? null,
                'estado' => 'pendiente',
                'total' => $total,
            ]);

            foreach ($productosValidados as $item) {
                $producto = $item['producto'];

                $pedido->detalles()->create([
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $item['subtotal'],
                ]);

                $producto->decrement('stock', $item['cantidad']);
            }
        });

        return back()->with('success', 'Pedido creado correctamente');
    }
}