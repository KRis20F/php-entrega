<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('correo')->unique();
            $table->timestamp('correo_verificado_el')->nullable();
            $table->string('contrasena');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('tokens_reinicio_contrasena', function (Blueprint $table) {
            $table->string('correo')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sesiones', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('usuario_id')->nullable()->index();
            $table->string('direccion_ip', 45)->nullable();
            $table->text('agente_usuario')->nullable();
            $table->longText('payload');
            $table->integer('ultima_actividad')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('tokens_reinicio_contrasena');
        Schema::dropIfExists('sesiones');
    }
};
