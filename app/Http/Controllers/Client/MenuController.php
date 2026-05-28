<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Mesa;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Client/Index', [
            'productos' => Producto::where('stock', '>', 0)
                ->orderBy('nombre')
                ->get(),

            'mesaActual' => null,
            'puedePedir' => false,
            'pedidoActual' => null,
        ]);
    }

    public function mesa(Request $request, $mesa)
    {
        $mesaActual = Mesa::where('id', $mesa)
            ->where('activa', true)
            ->firstOrFail();

        $pedidoActual = null;

       $tokenCliente = session('token_cliente');

        if ($tokenCliente) {
            $pedidoActual = Pedido::with('detalles.producto')
                ->where('mesa_id', $mesaActual->id)
                ->where('token_cliente', $tokenCliente)
                ->whereIn('estado', ['pendiente','en_preparacion', 'listo'])
                ->latest()
                ->first();
        }

        return Inertia::render('Client/Index', [
            'productos' => Producto::where('stock', '>', 0)
                ->orderBy('nombre')
                ->get(),

            'mesaActual' => [
                'id' => $mesaActual->id,
                'numero' => $mesaActual->numero,
            ],

            'puedePedir' => true,
            'pedidoActual' => $pedidoActual,
        ]);
    }
}