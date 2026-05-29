<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    protected $fillable = [
        'numero',
        'estado',
        'activa',
        'qr_token',
    ];

    // 🔽 AGREGA ESTE BLOQUE AQUÍ ABAJO 🔽
    protected $casts = [
        'activa' => 'boolean',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}