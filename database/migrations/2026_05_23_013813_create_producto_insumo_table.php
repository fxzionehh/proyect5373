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
        Schema::create('producto_insumo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->cascadeOnDelete();

            $table->foreignId('insumo_id')
                ->constrained('insumos')
                ->cascadeOnDelete();

            // Sincronizado con tus tamaños reales: nano, mini, normal, max
            $table->enum('tamano', ['nano', 'mini', 'normal', 'max']);

            // Usamos unsignedInteger porque una receta nunca usará cantidades negativas
            $table->unsignedInteger('cantidad')->default(1);

            $table->timestamps();

            // Evita que metas el mismo insumo dos veces para el mismo tamaño de un producto
            $table->unique(['producto_id', 'insumo_id', 'tamano'], 'prod_ins_tam_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_insumo');
    }
};