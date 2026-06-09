<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
  use HasFactory;

    protected $fillable = ['numero', 'estado', 'activa', 'qr_token'];

   
    public function getRouteKeyName()
    {
        return 'qr_token';
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}