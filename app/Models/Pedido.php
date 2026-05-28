<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
   protected $fillable = [
    'mesa_id',
    'nombre_cliente',
    'telefono',
    'tipo_pedido',
    'estado',
    'token_cliente',
    'total',
];


//1n  Un pedido tiene muchos detalles de pedido y n detalles de pedido pertenecen a un pedido
public function detalles(){
    return $this->hasMany(DetallePedido::class);
}


public function mesa()
{
    return $this->belongsTo(Mesa::class);
}
}


