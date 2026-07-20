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
  
    public function index()
    {
        return Inertia::render('Admin/Mesas/Index', [
            'mesas' => Mesa::orderBy('numero')->get()
        ]);
    }

    
    public function show($id, Request $request)
{
    $mesa = Mesa::findOrFail($id);


    if(!$request->token || $request->token !== $mesa->qr_token){
        abort(403,'QR inválido');
    }


    $pedidoActual = Pedido::where('mesa_id',$mesa->id)
        ->whereIn('estado',[
            'pendiente',
            'en_preparacion',
            'listo'
        ])
        ->first();



    return Inertia::render('Client/Index',[

        'mesaActual'=>$mesa,

        'productos'=>Producto::all(),

        'pedidoActual'=>$pedidoActual,

        // puede pedir solo si no hay pedido
        'puedePedir'=>$pedidoActual === null,

        'mesaOcupada'=>$pedidoActual !== null

    ]);
}
    }

    public function store(Request $request)
    {
        $id = $request->id;

        $request->merge([
            'numero' => strval($request->numero)
        ]);

        $validated = $request->validate([
            'numero' => 'required|string|max:20|unique:mesas,numero,' . ($id ?? 'NULL'),
        ]);

        if ($id) {
            $mesa = Mesa::findOrFail($id);
            $mesa->numero = $validated['numero'];

            if (!$mesa->qr_token) {
                $mesa->qr_token = Str::random(20);
            }

            $mesa->save();
        } else {
            $mesa = new Mesa();
            $mesa->numero = $validated['numero'];
            $mesa->qr_token = Str::random(20);
            $mesa->save();
        }

        return back();
    }


   public function destroy($id)
{
    $mesa = Mesa::findOrFail($id);
    $mesa->delete();

    return back();
}
}