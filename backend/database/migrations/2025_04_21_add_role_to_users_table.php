<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration to add role field to users table
// Migración para añadir el campo rol a la tabla de usuarios
return new class extends Migration
{
    public function up()
    {
        // Eliminado: esta migración ya no es necesaria porque solo se usará role_id
        // Puedes eliminar esta migración o dejarla vacía.
    }

    public function down()
    {
        // Nada que revertir
    }
}; 