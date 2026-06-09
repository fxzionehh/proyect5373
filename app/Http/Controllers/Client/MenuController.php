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
    $mesaActual = Mesa::where('id', $mesa)->where('activa', true)->firstOrFail();
    
    // DETECCIÓN: ¿Es usuario logueado o cliente anónimo?
    $tokenCliente = null;
    $userId = null;

    if (auth()->check()) {
        // Si está logueado, usamos su ID
        $userId = auth()->id();
    } else {
        // Si no, intentamos sacar el token de la sesión o del header
        $tokenCliente = session('token_cliente');
    }

    // Buscar pedido activo basado en lo que tengamos
    $query = Pedido::with('detalles.producto')->where('mesa_id', $mesaActual->id);
    
    if ($userId) {
        $query->where('user_id', $userId);
    } elseif ($tokenCliente) {
        $query->where('token_cliente', $tokenCliente);
    } else {
        $query = null; // No hay forma de identificar al cliente
    }

    $pedidoActual = $query ? $query->whereIn('estado', ['pendiente','en_preparacion', 'listo'])->latest()->first() : null;

    return Inertia::render('Client/Index', [
        'productos' => Producto::where('stock', '>', 0)->orderBy('nombre')->get(),
        'mesaActual' => [
            'id' => $mesaActual->id,
            'numero' => $mesaActual->numero,
        ],
        'puedePedir' => true,
        'pedidoActual' => $pedidoActual,
    ]);
}
}