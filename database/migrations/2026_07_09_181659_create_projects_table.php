<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // responsable

            $table->string('name');
            $table->string('code')->unique()->nullable();   // Ej: PM-2024-001
            $table->text('description')->nullable();

            // Estado
            $table->enum('status', [
                'draft',        // Borrador
                'active',       // En progreso
                'paused',       // Pausado
                'completed',    // Completado
                'cancelled',    // Cancelado
            ])->default('draft');

            // Fechas
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Financiero (preparado para multi-moneda)
            $table->unsignedBigInteger('budget_amount')->default(0); // en centavos/entero segun moneda
            $table->char('currency', 3)->default('CLP');             // ISO 4217
            $table->boolean('budget_includes_vat')->default(false);  // si el presupuesto incluye IVA

            $table->integer('progress')->default(0);  // 0-100 %

            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
