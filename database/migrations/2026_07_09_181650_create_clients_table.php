<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Tipo de cliente
            $table->enum('type', ['person', 'company'])->default('company');

            // Datos principales
            $table->string('name');                        // Nombre o razon social
            $table->string('rut', 12)->unique();           // XX.XXX.XXX-Y (formato formateado)
            $table->string('rut_raw', 9)->unique();        // sin formato, para busquedas

            // Contacto
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('contact_person')->nullable();  // Persona de contacto (en empresas)

            // Direccion
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();

            // Extras
            $table->string('website')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
