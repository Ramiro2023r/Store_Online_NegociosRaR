<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('compare_price', 10, 2)->nullable(); // precio antes de descuento
            $table->string('sku')->unique()->nullable();
            $table->integer('stock')->default(0);
            $table->string('brand')->nullable();
            $table->json('attributes')->nullable(); // color, talla, material, etc (clave-valor libres)
            $table->string('main_image')->nullable();
            $table->boolean('featured')->default(false); // para carrusel / destacados
            $table->boolean('active')->default(true);
            $table->decimal('rating', 3, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
