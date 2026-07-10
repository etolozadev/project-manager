<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete(); // se llena al aprobar
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // Identificación
            $table->string('quote_number', 20)->unique(); // COT-2026-001
            $table->string('title');                      // Título descriptivo

            // Estado del flujo
            $table->enum('status', [
                'draft',     // Borrador
                'sent',      // Enviada al cliente
                'approved',  // Aprobada por el cliente
                'rejected',  // Rechazada
            ])->default('draft');

            // Financiero
            $table->char('currency', 3)->default('CLP');
            $table->unsignedTinyInteger('tax_rate')->default(19); // IVA %
            $table->boolean('tax_included')->default(false);      // ¿El precio ya incluye IVA?

            // Totales (calculados y almacenados para historial)
            $table->unsignedBigInteger('subtotal')->default(0);   // Neto
            $table->unsignedBigInteger('tax_amount')->default(0); // IVA
            $table->unsignedBigInteger('total')->default(0);      // Total final

            // Metadata
            $table->date('valid_until')->nullable();               // Vigencia
            $table->text('notes')->nullable();                     // Notas para el cliente
            $table->text('terms')->nullable();                     // Términos y condiciones

            // Timestamps de cambios de estado
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->index(['status', 'client_id']);
            $table->index('created_at');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
