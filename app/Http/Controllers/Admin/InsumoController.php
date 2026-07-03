<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InsumoController extends Controller
{

    public function index()
    {
        return Inertia::render('Admin/Insumos/Index', [
            'insumos' => Insumo::orderBy('nombre')->get(),
        ]);
    }


    public function edit($id)
    {
        $insumo = Insumo::find($id);

        if (!$insumo) {
            return response()->json([
                'error' => 'No encontrado',
            ], 404);
        }

        return response()->json($insumo);
    }

 
    public function store(Request $request)
    {
        $id = $request->id;

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'stock' => 'required|integer|min:0',
        ]);

        if ($id) {
         
            $insumo = Insumo::findOrFail($id);

            $insumo->nombre = $validated['nombre'];
            $insumo->stock = $validated['stock'];

            $insumo->save();
        } else {
       
            $insumo = new Insumo();

            $insumo->nombre = $validated['nombre'];
            $insumo->stock = $validated['stock'];

            $insumo->save();
        }

        return back();
    }


    public function destroy(Insumo $insumo)
    {
        $insumo->delete();

        return back();
    }
}