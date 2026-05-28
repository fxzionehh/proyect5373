<?php

namespace App\Http\Controllers\Barista;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MesaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO MESAS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $mesas = Mesa::orderBy('numero')->get();

        return Inertia::render('Barista/Mesas/Index', [
            'mesas' => $mesas
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ACTIVAR / DESACTIVAR
    |--------------------------------------------------------------------------
    */

    public function toggleEstado(Mesa $mesa)
    {
        $mesa->activa = !$mesa->activa;

        $mesa->save();

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | GENERAR QR
    |--------------------------------------------------------------------------
    */

    public function generarQr(Mesa $mesa)
    {
        $mesa->qr_token = (string) Str::uuid();

        $mesa->save();

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | LIBERAR MESA
    |--------------------------------------------------------------------------
    */

    public function liberarMesa(Mesa $mesa)
    {
        $mesa->estado = 'libre';

        $mesa->save();

        return redirect()->back();
    }
}