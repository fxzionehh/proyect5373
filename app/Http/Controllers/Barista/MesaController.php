<?php

namespace App\Http\Controllers\Barista;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MesaController extends Controller
{
    // Muestra la lista de mesas
    public function index() {
        return Inertia::render('Admin/Mesas/Index', [
            'mesas' => Mesa::orderBy('numero')->get()
        ]);
    }

  public function edit($id) {

    $mesa = Mesa::find($id);
    
    if (!$mesa) {
        return response()->json([
            'error' => 'No encontrada',
        ], 404);
    }
    
    return response()->json($mesa);
}


// Dentro de tu clase MesaController

public function show($id) 
{
    $mesa = Mesa::findOrFail($id);

    // Si quieres que el admin vea la mesa en un dashboard, 
    // pero el cliente vea el menú, puedes diferenciar por el usuario:
    
    return Inertia::render('Client/Index', [
        'mesa' => $mesa
    ]);
}

  public function store(Request $request) 
{
    $id = $request->id;

    $validated = $request->validate([
        'numero' => 'required|unique:mesas,numero,' . ($id ?? 'NULL'),
        'activa' => 'required|integer|in:1,2'
    ]);

    if ($id) {
        // ACTUALIZACIÓN EXPLÍCITA
        $mesa = Mesa::findOrFail($id);
        $mesa->numero = $validated['numero'];
        $mesa->activa = $validated['activa'];
        $mesa->save();
    } else {
        // CREACIÓN EXPLÍCITA
        $mesa = new Mesa();
        $mesa->numero = $validated['numero'];
        $mesa->activa = $validated['activa'];
        // Si tienes más campos, los pones aquí abajo:
        // $mesa->capacidad = $request->capacidad;
        $mesa->save();
    }

    return back();
}
    // Elimina la mesa
    public function destroy(Mesa $mesa) {
        $mesa->delete();
        return back();
    }
}