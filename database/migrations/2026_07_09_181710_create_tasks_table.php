<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            // Categoria de la tarea
            $table->enum('category', [
                'development',    // Desarrollo
                'design',         // Diseño
                'server',         // Servidor / Infraestructura
                'testing',        // Testing / QA
                'documentation',  // Documentacion
                'meeting',        // Reunion / Gestion
                'other',          // Otro
            ])->default('development');

            // Estado (Kanban)
            $table->enum('status', [
                'backlog',       // Por hacer
                'in_progress',   // En progreso
                'review',        // En revision
                'done',          // Hecho
            ])->default('backlog');

            // Prioridad
            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'critical',
            ])->default('medium');

            $table->date('due_date')->nullable();
            $table->integer('estimated_hours')->nullable();  // horas estimadas
            $table->integer('actual_hours')->nullable();     // horas reales (para mejorar cotizaciones futuras)

            $table->integer('order')->default(0);  // orden en el kanban
            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
