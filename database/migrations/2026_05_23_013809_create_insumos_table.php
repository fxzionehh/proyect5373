<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            
            // El nombre del insumo (ej: "Vaso Nano 120ml")
            $table->string('nombre');
            
            // Usamos unsignedInteger para garantizar que el stock nunca sea menor a 0
            $table->unsignedInteger('stock')->default(0);
            
            // Unidad de medida (ej: "unidad", "ml", "gr")
            $table->string('unidad')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos');
    }
};