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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('activo')->default(true);
            $table->text('app_authentication_secret')->nullable();
            $table->text('app_authentication_recovery_codes')->nullable();
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->boolean('visible')->default(true);
            $table->boolean('gestionado')->default(false);
        });

        Schema::create('ajustes', function (Blueprint $table) {
            $table->string('clave', 60)->primary();
            $table->text('valor')->nullable();
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('estilo', 20)->default('s-dark');
            $table->string('etiqueta', 60);
            $table->string('titulo_inicio', 60);
            $table->string('titulo_resaltado', 60)->nullable();
            $table->string('titulo_fin', 60)->nullable();
            $table->text('texto')->nullable();
            $table->string('boton_texto', 40);
            $table->string('boton_enlace', 200);
            $table->json('productos')->nullable();
            $table->string('imagen', 160)->nullable();
            $table->string('imagen_movil', 160)->nullable();
            $table->string('texto_alternativo', 300)->nullable();
            $table->unsignedSmallInteger('posicion')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('dropbox_ajustes', function (Blueprint $table) {
            $table->string('item_id', 80)->primary();
            $table->string('titulo', 200)->nullable();
            $table->boolean('oculto')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dropbox_ajustes');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('ajustes');

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['visible', 'gestionado']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['activo', 'app_authentication_secret', 'app_authentication_recovery_codes']);
        });
    }
};
