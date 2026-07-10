<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── clients ────────────────────────────────────────────
        Schema::table('clients', function (Blueprint $table) {
            // Búsqueda por nombre y filtros frecuentes
            $table->index('name');
            $table->index('active');
            $table->index('type');
            // rut_raw ya tiene unique (implica índice)
        });

        // ── projects ───────────────────────────────────────────
        Schema::table('projects', function (Blueprint $table) {
            // Filtros del listado
            $table->index('status');
            $table->index('client_id');
            $table->index('user_id');
            // Ordenamiento por fecha
            $table->index('created_at');
            // Índice compuesto para el dashboard (activos recientes)
            $table->index(['status', 'updated_at']);
        });

        // ── tasks ──────────────────────────────────────────────
        Schema::table('tasks', function (Blueprint $table) {
            // Kanban: cargar tareas de un proyecto ordenadas
            $table->index(['project_id', 'status', 'order']);
            // Dashboard: tareas vencidas
            $table->index(['due_date', 'status']);
            // Conteo por estado
            $table->index('status');
            // Asignaciones
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['active']);
            $table->dropIndex(['type']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['client_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status', 'updated_at']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'status', 'order']);
            $table->dropIndex(['due_date', 'status']);
            $table->dropIndex(['status']);
            $table->dropIndex(['assigned_to']);
        });
    }
};
