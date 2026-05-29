<?php

namespace App\Http\Controllers\Admin; // <-- DEBE DECIR ADMIN

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ConfiguracionController extends Controller // <-- DEBE SER CONFIGURACIONCONTROLLER
{
    public function index()
    {
        $mesas = Mesa::orderBy('numero')->get()->map(function ($mesa) {
            if ($mesa->qr_token) {
                $url = url('/mesa/' . $mesa->qr_token);
                $mesa->qr = base64_encode(
                    QrCode::format('svg')->size(250)->margin(1)->generate($url)
                );
            } else {
                $mesa->qr = null;
            }
            return $mesa;
        });

        return Inertia::render('Admin/Configuracion/Index', [
            'mesas' => $mesas
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|integer|unique:mesas,numero|min:1',
        ]);

        Mesa::create([
            'numero' => $request->numero,
            'estado' => 'libre',
            'activa' => true,
            'qr_token' => Str::random(32),
        ]);

        return redirect()->back();
    }
}