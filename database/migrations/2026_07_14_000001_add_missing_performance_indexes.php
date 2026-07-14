<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Índices para FinanceController (whereBetween en fechas)
        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_date');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('expense_date');
        });

        // Índices para ServerController (status standalone + ordenamiento + scopes)
        Schema::table('servers', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
            $table->index('domain_expires_at');
            $table->index('ssl_expires_at');
        });

        // Índices para ordenamiento latest() sin índice
        Schema::table('clients', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', fn (Blueprint $t) => $t->dropIndex(['payment_date']));
        Schema::table('expenses', fn (Blueprint $t) => $t->dropIndex(['expense_date']));
        Schema::table('servers',  fn (Blueprint $t) => $t->dropIndex(['status', 'created_at', 'domain_expires_at', 'ssl_expires_at']));
        Schema::table('clients',  fn (Blueprint $t) => $t->dropIndex(['created_at']));
        Schema::table('users',    fn (Blueprint $t) => $t->dropIndex(['created_at']));
    }
};
