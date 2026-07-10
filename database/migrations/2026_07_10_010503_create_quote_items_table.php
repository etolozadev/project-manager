<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();

            $table->string('description');                        // Descripción del ítem
            $table->text('detail')->nullable();                   // Detalle opcional
            $table->unsignedSmallInteger('quantity')->default(1); // Cantidad
            $table->unsignedBigInteger('unit_price')->default(0); // Precio unitario (sin IVA)
            $table->unsignedBigInteger('subtotal')->default(0);   // quantity * unit_price

            $table->unsignedSmallInteger('order')->default(0);    // Orden en la cotización

            $table->index(['quote_id', 'order']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_items');
    }
};
