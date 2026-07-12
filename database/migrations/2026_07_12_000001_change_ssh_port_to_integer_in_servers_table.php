<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            // smallint tiene max 32767 en PostgreSQL, los puertos SSH pueden llegar a 65535
            $table->unsignedInteger('ssh_port')->default(22)->change();
        });
    }

    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            $table->unsignedSmallInteger('ssh_port')->default(22)->change();
        });
    }
};
