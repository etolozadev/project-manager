<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->date('delivery_date')->nullable()->after('total_amount');
            $table->dropColumn('delivery_time');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('delivery_time', 100)->nullable()->after('total_amount');
            $table->dropColumn('delivery_date');
        });
    }
};
