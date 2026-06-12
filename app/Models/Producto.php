<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
    'nombre',
    'stock',
    'precio_nano',
    'precio_mini',
    'precio_normal',
    'precio_max',
];




    public function preparaciones()
{
    return $this->hasMany(Preparacion::class);
}

   public function insumos()
    {
        return $this->belongsToMany(
            Insumo::class,
            'producto_insumo'
        )
        ->withPivot('cantidad', 'tamano')
        ->withTimestamps();
    }
}

