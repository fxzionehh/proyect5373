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
      Schema::create('pedidos', function (Blueprint $table) {
    $table->id();
    $table->string('mesa')->nullable();
    $table->string('nombre_cliente')->nullable();
    $table->string('telefono')->nullable();
    $table->enum('tipo_pedido', ['presencial', 'delivery'])->default('presencial');
    $table->enum('estado', ['pendiente', 'pagado', 'en_preparacion', 'listo'])->default('pendiente');
    $table->decimal('total', 10, 2);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
