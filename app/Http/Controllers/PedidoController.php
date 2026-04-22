<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string',
            'lat' => 'required',
            'lng' => 'required',
        ]);

        Pedido::create([
            'tipo' => $request->tipo,
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);

        return back();
    }
}