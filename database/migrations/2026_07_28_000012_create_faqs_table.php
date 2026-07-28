<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->default('general');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        DB::table('faqs')->insert([
            ['question' => '¿Cuánto tiempo tarda mi pedido?', 'answer' => 'El tiempo de entrega depende de tu ubicación. Para Lima Metropolitana, el plazo es de 1 a 3 días hábiles. Para provincias, de 3 a 7 días hábiles.', 'category' => 'envio', 'sort_order' => 0],
            ['question' => '¿Cuáles son los costos de envío?', 'answer' => 'El envío es gratis para compras mayores a S/ 200. Para compras menores, el costo es de S/ 15 a nivel nacional.', 'category' => 'envio', 'sort_order' => 1],
            ['question' => '¿Qué métodos de pago aceptan?', 'answer' => 'Aceptamos pago contraentrega, transferencia bancaria y tarjeta de crédito/débito (Visa, Mastercard) a través de Culqi.', 'category' => 'pago', 'sort_order' => 2],
            ['question' => '¿Cómo puedo devolver un producto?', 'answer' => 'Tienes hasta 7 días calendario desde la recepción para solicitar una devolución. El producto debe estar sin uso, en su empaque original y con todas las etiquetas. Contáctanos a través de nuestro chat de soporte para iniciar el proceso.', 'category' => 'devolucion', 'sort_order' => 3],
            ['question' => '¿Cuánto tiempo tengo para realizar un cambio?', 'answer' => 'El plazo para cambios es de 15 días calendario desde la recepción del producto. Los costos de envío por cambio corren por cuenta del cliente, excepto si el producto llegó dañado o incorrecto.', 'category' => 'devolucion', 'sort_order' => 4],
            ['question' => '¿Cómo sé si mi compra fue exitosa?', 'answer' => 'Recibirás un correo de confirmación con el número de pedido y los detalles de tu compra. También puedes revisar el estado en "Mis pedidos" dentro de tu cuenta.', 'category' => 'general', 'sort_order' => 5],
        ]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'shipping_info'],
            ['value' => 'Realizamos envíos a todo el Perú a través de agencias de transporte locales. El tiempo de entrega estimado es de 1 a 3 días hábiles para Lima Metropolitana y de 3 a 7 días hábiles para provincias. El envío es gratuito para compras mayores a S/ 200.', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'returns_policy'],
            ['value' => 'Aceptamos devoluciones dentro de los 7 días calendario posteriores a la recepción del producto. Requisitos: producto sin uso, empaque original y etiquetas intactas. Para iniciar una devolución, contáctanos vía chat de soporte con tu número de pedido.', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
        DB::table('settings')->whereIn('key', ['shipping_info', 'returns_policy'])->delete();
    }
};
