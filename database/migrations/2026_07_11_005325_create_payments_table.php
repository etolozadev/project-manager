<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quote_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // Monto
            $table->unsignedBigInteger('amount');   // en entero (CLP sin decimales)
            $table->char('currency', 3)->default('CLP');

            // Detalle
            $table->date('payment_date');
            $table->enum('method', [
                'transfer',   // Transferencia bancaria
                'cash',       // Efectivo
                'check',      // Cheque
                'card',       // Tarjeta débito/crédito
                'other',      // Otro
            ])->default('transfer');

            $table->string('reference')->nullable(); // N° transferencia, folio, etc.
            $table->text('notes')->nullable();

            $table->index(['project_id', 'payment_date']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
