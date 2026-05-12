<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Mesa;
use Inertia\Inertia;

class CatalogoController extends Controller
{
    public function index()
    {
        return Inertia::render('Client/Index', [
            'productos' => Producto::where('stock', '>', 0)
                ->orderBy('nombre')
                ->get(),

            'mesaActual' => null,
            'puedePedir' => false,
        ]);
    }

    public function mesa($token)
    {
        $mesaActual = Mesa::where('qr_token', $token)
            ->where('activa', true)
            ->firstOrFail();

        session([
            'mesa_id' => $mesaActual->id,
            'mesa_expira' => now()->addMinutes(45),
        ]);

        return Inertia::render('Client/Index', [
            'productos' => Producto::where('stock', '>', 0)
                ->orderBy('nombre')
                ->get(),

            'mesaActual' => [
                'id' => $mesaActual->id,
                'numero' => $mesaActual->numero,
            ],

            'puedePedir' => true,
        ]);
    }
}