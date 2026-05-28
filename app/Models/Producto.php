<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'precio',
        'stock'
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

