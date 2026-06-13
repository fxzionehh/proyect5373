<?php

namespace App\Http\Controllers\Barista;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MesaController extends Controller
{
    // 📋 Lista de mesas
    public function index() {
        return Inertia::render('Admin/Mesas/Index', [
            'mesas' => Mesa::orderBy('numero')->get()
        ]);
    }

    // 🔎 Edit (API simple)
    public function edit($id) {
        $mesa = Mesa::find($id);

        if (!$mesa) {
            return response()->json([
                'error' => 'No encontrada',
            ], 404);
        }

        return response()->json($mesa);
    }

    // 🍽️ Entrada cliente a mesa (PROTEGIDA POR QR)
    public function show($id, Request $request)
    {
       
        $mesa = Mesa::findOrFail($id);

        // 🔐 VALIDACIÓN QR
        if (!$request->has('token') || $request->token !== $mesa->qr_token) {
            abort(403, 'Acceso no autorizado a la mesa');
        }

        $productos = Producto::all();

        $pedidoActual = Pedido::with('detalles.producto')
            ->where('mesa_id', $mesa->id)
            ->whereIn('estado', ['pendiente', 'en_preparacion'])
            ->latest()
            ->first();

        return Inertia::render('Client/Index', [
            'mesaActual' => $mesa,
            'productos' => $productos,
            'pedidoActual' => $pedidoActual,
            'puedePedir' => $pedidoActual ? false : true,
        ]);
    }

    // 💾 Crear / actualizar mesa
    public function store(Request $request)
    {
        $id = $request->id;

  $request->merge([
    'numero' => strval($request->numero)
]);

$validated = $request->validate([
    'numero' => 'required|string|max:20|unique:mesas,numero,' . ($id ?? 'NULL'),
    'activa' => 'required|boolean'
]);
        if ($id) {
    // ✏️ ACTUALIZAR
    $mesa = Mesa::findOrFail($id);
    $mesa->numero = $validated['numero'];
    $mesa->activa = $validated['activa'];

    // 🔐 ASEGURAR TOKEN SI NO EXISTE
    if (!$mesa->qr_token) {
        $mesa->qr_token = Str::random(20);
    }

    $mesa->save();
} else {
    // ➕ CREAR
    $mesa = new Mesa();
    $mesa->numero = $validated['numero'];
    $mesa->activa = $validated['activa'];
    $mesa->qr_token = Str::random(20);
    $mesa->save();
}
        return back();
    }

    // 🗑️ Eliminar mesa
    public function destroy(Mesa $mesa) {
        $mesa->delete();
        return back();
    }
}