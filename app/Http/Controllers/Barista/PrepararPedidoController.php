<?php

namespace App\Http\Controllers\Barista;

use Illuminate\Support\Facades\DB;
use App\Models\Producto;
use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Mesa;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PrepararPedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with(['mesa', 'detalles.producto'])
            ->whereIn('estado', ['pendiente', 'en_preparacion', 'listo', 'entregado'])
            ->latest()
            ->get();

        $mesas = Mesa::orderBy('numero')->get();

        return Inertia::render('Barista/Index', [
            'pedidos' => $pedidos,
            'mesas' => $mesas,
        ]);
    }

    public function edit()
    {
        $pedidos = Pedido::with(['detalles.producto', 'mesa'])
            ->where('estado', 'listo')
            ->latest()
            ->get();

        return Inertia::render('Barista/Index', [
            'pedidos' => $pedidos
        ]);
    }

    public function store(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_preparacion,listo,entregado',
        ]);

        DB::transaction(function () use ($request, $pedido) {

            $estadoAnterior = $pedido->estado;

            $pedido->load('detalles');

            $pedido->update([
                'estado' => $request->estado,
            ]);

            
            /**
             * SOLO cuando pasa a LISTO por primera vez:
             * - descontar stock
             * - liberar mesa
             */
            if ($request->estado === 'listo' && $estadoAnterior !== 'listo') {

                foreach ($pedido->detalles as $detalle) {
                    $producto = Producto::lockForUpdate()->findOrFail($detalle->producto_id);

                    if ($producto->stock < $detalle->cantidad) {
                        abort(422, "No hay stock suficiente para {$producto->nombre}");
                    }

                    $producto->decrement('stock', $detalle->cantidad);
                }

                if ($pedido->mesa_id) {
                    Mesa::where('id', $pedido->mesa_id)->update([
                        'estado' => 'libre'
                    ]);
                }
            }
        });

        return back();
    }
}