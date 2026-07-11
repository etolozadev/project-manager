<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // Identificación
            $table->string('name');               // Ej: "Servidor producción"
            $table->enum('type', [
                'vps',        // VPS (DigitalOcean, Hetzner, etc.)
                'shared',     // Hosting compartido
                'managed',    // Hosting administrado
                'dedicated',  // Servidor dedicado
                'cloud',      // Cloud (AWS, GCP, Azure)
                'other',
            ])->default('vps');

            // Proveedor y conexión
            $table->string('provider')->nullable();     // Ej: "DigitalOcean", "Hetzner"
            $table->string('ip_address', 45)->nullable();
            $table->string('hostname')->nullable();

            // Stack técnico
            $table->string('os')->nullable();           // Ej: "Ubuntu 22.04"
            $table->string('php_version', 10)->nullable();
            $table->enum('db_type', ['mysql', 'postgresql', 'mariadb', 'sqlite', 'other'])->nullable();
            $table->string('db_version', 10)->nullable();
            $table->enum('panel', ['cpanel', 'plesk', 'forge', 'ploi', 'runcloud', 'none', 'other'])->default('none');
            $table->string('web_server', 30)->nullable(); // nginx, apache, caddy

            // Acceso SSH
            $table->string('ssh_user', 50)->nullable()->default('root');
            $table->unsignedSmallInteger('ssh_port')->default(22);

            // Dominio y URLs
            $table->string('domain')->nullable();
            $table->string('url')->nullable();          // URL de acceso del proyecto

            // Estado
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');

            // Vencimientos (para alertas)
            $table->date('hosting_expires_at')->nullable();
            $table->date('domain_expires_at')->nullable();
            $table->date('ssl_expires_at')->nullable();

            // Credenciales cifradas (JSON encrypt)
            // Ej: {"ssh_password":"...", "db_password":"...", "panel_url":"...", "panel_user":"...", "panel_password":"..."}
            $table->text('credentials')->nullable();

            $table->text('notes')->nullable();

            $table->index(['project_id', 'status']);
            $table->index('hosting_expires_at');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
