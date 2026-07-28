<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('settings')->insert([
            ['key' => 'top_bar_text', 'value' => '📦 Envío gratis en compras mayores a S/ 200  |  🎉 Bienvenido a Negocios RaR'],
            ['key' => 'footer_description', 'value' => 'Tu tienda online de confianza. Todo lo que necesitas, en un solo lugar.'],
            ['key' => 'footer_address', 'value' => 'Lima, Perú'],
            ['key' => 'footer_phone', 'value' => '(01) 555-0100'],
            ['key' => 'footer_email', 'value' => 'ventas@negociosrar.com'],
            ['key' => 'shipping_min_amount', 'value' => '200'],
            ['key' => 'shipping_cost', 'value' => '15'],
            ['key' => 'about_mission', 'value' => 'Facilitar el acceso a productos de calidad para todos los peruanos, con precios justos y un servicio excepcional.'],
            ['key' => 'about_vision', 'value' => 'Ser la tienda online líder en Perú, reconocida por la confianza de nuestros clientes y la calidad de nuestros productos.'],
            ['key' => 'about_values', 'value' => 'Honestidad, transparencia, calidad y compromiso con la satisfacción total de nuestros clientes.'],
            ['key' => 'about_clients_count', 'value' => '+15,000'],
            ['key' => 'about_products_count', 'value' => '+500'],
            ['key' => 'about_regions_count', 'value' => '24'],
            ['key' => 'about_rating', 'value' => '4.8★'],
            ['key' => 'home_title_categories', 'value' => 'Compra por categoría'],
            ['key' => 'home_title_featured', 'value' => '⭐ Productos destacados'],
            ['key' => 'home_title_newest', 'value' => '🆕 Recién llegados'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
