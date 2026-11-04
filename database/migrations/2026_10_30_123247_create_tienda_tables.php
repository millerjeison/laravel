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
        Schema::create('categorias', function (Blueprint $table) {
            $table->string('id', 40)->primary();
            $table->string('nombre', 80);
            $table->unsignedSmallInteger('posicion')->default(0);
            $table->string('portada_producto_id', 40)->nullable();
            $table->timestamps();
        });

        Schema::create('productos', function (Blueprint $table) {
            $table->string('id', 40)->primary();
            $table->string('nombre', 200);
            $table->string('categoria_id', 40)->index();
            $table->foreign('categoria_id')->references('id')->on('categorias');
            $table->unsignedInteger('precio_referencia')->nullable();
            $table->string('unidad', 20)->nullable();
            $table->json('referencias');
            $table->json('imagenes');
            $table->string('miniatura', 120)->nullable();
            $table->date('novedad_en')->nullable();
            $table->unsignedSmallInteger('destacado_posicion')->nullable();
            $table->timestamps();
        });

        Schema::create('saint_productos', function (Blueprint $table) {
            $table->string('codigo', 15)->primary();
            $table->string('huella', 64);
            $table->string('descripcion', 40)->nullable();
            $table->integer('instancia')->nullable();
            $table->string('unidad', 3)->nullable();
            $table->decimal('precio1', 18, 4)->default(0);
            $table->decimal('precio2', 18, 4)->default(0);
            $table->decimal('precio3', 18, 4)->default(0);
            $table->decimal('iva', 6, 2)->nullable();
            $table->decimal('existencia', 18, 4)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('puente_estados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('secuencia')->default(0);
            $table->string('modo', 20)->nullable();
            $table->unsignedInteger('filas')->default(0);
            $table->string('error', 500)->nullable();
            $table->timestamp('ultimo_latido_en')->nullable();
            $table->timestamp('ultimo_dato_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puente_estados');
        Schema::dropIfExists('saint_productos');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('categorias');
    }
};
