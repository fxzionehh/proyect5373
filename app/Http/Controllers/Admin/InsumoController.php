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

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        Insumo::create($data);

        return back(); // Inertia recarga los props automáticamente
    }

    public function update(Request $request, Insumo $insumo)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $insumo->update($data);

        return back();
    }

    public function destroy(Insumo $insumo)
    {
        $insumo->delete();

        return back();
    }
}