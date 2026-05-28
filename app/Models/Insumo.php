<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected $table = 'insumos';

    protected $fillable = [
        'nombre',
        'stock',
        'unidad'
    ];

    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'producto_insumo'
        )
        ->withPivot('cantidad', 'tamano')
        ->withTimestamps();
    }
}