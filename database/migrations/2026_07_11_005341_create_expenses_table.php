<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // Categoría del gasto
            $table->enum('category', [
                'hosting',      // Hosting / VPS
                'domain',       // Dominio
                'license',      // Licencias de software
                'subcontract',  // Subcontratación
                'tools',        // Herramientas / SaaS
                'travel',       // Viáticos / Transporte
                'other',        // Otro
            ])->default('other');

            $table->string('description');
            $table->unsignedBigInteger('amount');
            $table->char('currency', 3)->default('CLP');
            $table->date('expense_date');
            $table->text('notes')->nullable();

            $table->index(['project_id', 'expense_date']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
