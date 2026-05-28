<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ConfiguracionController extends Controller
{
  
    public function index()
    {
        $mesas = Mesa::orderBy('numero')->get()->map(function ($mesa) {
            
            if ($mesa->qr_token) {
                $url = url('/mesa/' . $mesa->qr_token);
                
                $mesa->qr = base64_encode(
                    QrCode::format('svg')
                        ->size(250)
                        ->margin(1)
                        ->generate($url)
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
}