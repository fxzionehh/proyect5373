<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\DetallePedido;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReporteController extends Controller
{
    public function index()
{
    $totalPedidos = Pedido::count();

    $ingresos = Pedido::where('estado', 'pagado')->sum('total');

    $porEstado = Pedido::select('estado', DB::raw('count(*) as total'))
        ->groupBy('estado')
        ->get();

    $porDia = Pedido::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('count(*) as total')
        )
        ->groupBy('fecha')
        ->orderBy('fecha', 'asc')
        ->limit(7)
        ->get();

    $topProductos = DB::table('detalle_pedidos')
        ->join('productos', 'detalle_pedidos.producto_id', '=', 'productos.id')
        ->select('productos.nombre', DB::raw('SUM(detalle_pedidos.cantidad) as total'))
        ->groupBy('productos.nombre')
        ->orderByDesc('total')
        ->limit(5)
        ->get();

    return Inertia::render('Admin/Reportes/Index', [
        'totalPedidos' => $totalPedidos,
        'ingresos' => $ingresos,
        'porEstado' => $porEstado,
        'porDia' => $porDia,
        'topProductos' => $topProductos,
    ]);
}
}