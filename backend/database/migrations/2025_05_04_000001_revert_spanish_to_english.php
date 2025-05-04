<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing tables
        Schema::dropIfExists('tokens_reinicio_contrasena');
        Schema::dropIfExists('sesiones');
        Schema::dropIfExists('products');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');

        // Recreate roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Recreate users table with English column names
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Recreate password reset tokens table
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Recreate sessions table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Recreate products table with English column names
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->integer('stock');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Drop English tables
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('products');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');

        // Recreate roles table (unchanged)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Recreate users table with Spanish column names
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->constrained('roles');
            $table->string('nombre');
            $table->string('correo')->unique();
            $table->timestamp('correo_verificado_el')->nullable();
            $table->string('contrasena');
            $table->rememberToken();
            $table->timestamps();
        });

        // Recreate Spanish tables
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

        // Recreate products table with Spanish column names
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_producto');
            $table->text('descripcion_producto');
            $table->decimal('precio_producto', 8, 2);
            $table->integer('stock_producto');
            $table->timestamps();
        });
    }
}; 