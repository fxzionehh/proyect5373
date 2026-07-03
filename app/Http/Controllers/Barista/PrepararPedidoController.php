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

    try {
        DB::transaction(function () use ($request, $pedido) {
            $estadoAnterior = $pedido->estado;

          
            if ($request->estado === 'listo' && $estadoAnterior !== 'listo') {
                $pedido->load('detalles');

                foreach ($pedido->detalles as $detalle) {
                    $producto = Producto::lockForUpdate()->findOrFail($detalle->producto_id);

                    if ($producto->stock < $detalle->cantidad) {
                   
                        throw new \Illuminate\Validation\ValidationException(
                            \Illuminate\Support\Facades\Validator::make([], []),
                            \Illuminate\Support\Facades\Response::json(['error' => "Stock insuficiente para {$producto->nombre}"], 422)
                        );
                    }

                    $producto->decrement('stock', $detalle->cantidad);
                }
            }

      
            $pedido->update(['estado' => $request->estado]);

        
            if ($request->estado === 'entregado' && $pedido->mesa_id) {
                Mesa::where('id', $pedido->mesa_id)->update(['estado' => 'libre']);
            }
        });

        return back()->with('success', 'Pedido actualizado correctamente');

    } catch (\Illuminate\Validation\ValidationException $e) {
       
        return back()->withErrors(['stock' => $e->getResponse()->getData()->error]);
    } catch (\Exception $e) {
        return back()->with('error', 'Ocurrió un error: ' . $e->getMessage());
    }
}
}