<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Benefit;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        Banner::create([
            'title' => 'Tecnología de última generación',
            'subtitle' => 'NUEVO INGRESO',
            'button_text' => 'Ver productos',
            'button_url' => '/productos',
            'gradient_from' => 'from-rar-700',
            'gradient_to' => 'to-rar-500',
            'text_color' => 'text-white',
            'sort_order' => 0,
            'active' => true,
        ]);

        Banner::create([
            'title' => 'Hasta 30% de descuento',
            'subtitle' => 'OFERTA ESPECIAL',
            'button_text' => 'Ver ofertas',
            'button_url' => '/productos',
            'gradient_from' => 'from-blue-700',
            'gradient_to' => 'to-blue-500',
            'text_color' => 'text-white',
            'sort_order' => 1,
            'active' => true,
        ]);

        Banner::create([
            'title' => 'En compras mayores a S/ 200',
            'subtitle' => 'ENVÍO GRATIS',
            'button_text' => 'Crear cuenta',
            'button_url' => '/registro',
            'gradient_from' => 'from-emerald-700',
            'gradient_to' => 'to-emerald-500',
            'text_color' => 'text-white',
            'sort_order' => 2,
            'active' => true,
        ]);

        Benefit::create(['icon' => '🚚', 'title' => 'Envío a todo el país', 'sort_order' => 0]);
        Benefit::create(['icon' => '🔒', 'title' => 'Pago 100% seguro', 'sort_order' => 1]);
        Benefit::create(['icon' => '↩️', 'title' => 'Devoluciones fáciles', 'sort_order' => 2]);
        Benefit::create(['icon' => '💬', 'title' => 'Soporte al cliente', 'sort_order' => 3]);
    }
}
