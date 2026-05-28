<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PedidosController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::query();

        // 📅 FILTRO POR FECHA
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        $pedidos = $query->latest()->get();

        return Inertia::render('Admin/Pedidos/Index', [
            'pedidos' => $pedidos,
            'filtroFecha' => $request->fecha
        ]);
    }
}