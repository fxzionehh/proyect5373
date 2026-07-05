<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Mesa;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PedidosController extends Controller
{
    public function index()
    {
      return Inertia::render('Admin/Pedidos/Index', [
    'pedidos' => Pedido::with('detalles.producto')->latest()->get(),
    'productos' => Producto::orderBy('nombre')->get(),
    'mesas' => Mesa::orderBy('numero')->get(),
    'appUrl' => config('app.url'),
]);
    }

    public function show($codigo)
{
    $pedido = Pedido::with('detalles.producto')
        ->where('codigo', $codigo)
        ->firstOrFail();

    return Inertia::render('Admin/Pedidos/Show', [
        'pedido' => $pedido
    ]);
}

public function store(Request $request)
{
    $validated = $request->validate([
        'mesa_id' => 'nullable|integer',
        'nombre_cliente' => 'required|string|max:100',
        'estado' => 'required|string',
        'tipo_pedido' => 'required|string',
        'producto_id' => 'required|exists:productos,id',
        'tamano' => 'required|in:nano,mini,normal,max',
    ]);

    $producto = Producto::findOrFail($validated['producto_id']);
    $precio = $producto->{'precio_' . $validated['tamano']};

    $pedido = new Pedido();

    // 🔥 CÓDIGO ÚNICO SEGURO
    do {
        $codigo = Str::upper(Str::random(10));
    } while (Pedido::where('codigo', $codigo)->exists());

    $pedido->codigo = $codigo;

    $pedido->fill([
        'mesa_id' => $validated['mesa_id'],
        'nombre_cliente' => $validated['nombre_cliente'],
        'estado' => $validated['estado'],
        'tipo_pedido' => $validated['tipo_pedido'],
        'total' => $precio,
    ]);

    $pedido->save();

    $pedido->detalles()->create([
        'producto_id' => $producto->id,
        'cantidad' => 1,
        'tamano' => $validated['tamano'],
        'precio_unitario' => $precio,
        'subtotal' => $precio,
    ]);

    return back();
}
}