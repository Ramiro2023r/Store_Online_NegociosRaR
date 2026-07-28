<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            ['cat' => 'Tecnología', 'name' => 'Smartphone Galaxy X200', 'price' => 1899.90, 'compare' => 2199.90, 'brand' => 'Samsung', 'featured' => true],
            ['cat' => 'Tecnología', 'name' => 'Laptop UltraBook 15"', 'price' => 3299.00, 'compare' => null, 'brand' => 'HP', 'featured' => true],
            ['cat' => 'Tecnología', 'name' => 'Audífonos Inalámbricos Pro', 'price' => 249.90, 'compare' => 329.90, 'brand' => 'Sony', 'featured' => true],
            ['cat' => 'Tecnología', 'name' => 'Smartwatch Fit 5', 'price' => 459.00, 'compare' => null, 'brand' => 'Xiaomi', 'featured' => false],
            ['cat' => 'Ropa y Moda', 'name' => 'Casaca Impermeable Hombre', 'price' => 159.90, 'compare' => 199.90, 'brand' => 'Northland', 'featured' => true],
            ['cat' => 'Ropa y Moda', 'name' => 'Zapatillas Urbanas Running', 'price' => 219.00, 'compare' => null, 'brand' => 'Adidas', 'featured' => true],
            ['cat' => 'Ropa y Moda', 'name' => 'Vestido Casual Verano', 'price' => 89.90, 'compare' => 119.90, 'brand' => 'Zara', 'featured' => false],
            ['cat' => 'Hogar', 'name' => 'Juego de Sábanas Queen', 'price' => 129.90, 'compare' => null, 'brand' => 'Casa & Confort', 'featured' => false],
            ['cat' => 'Hogar', 'name' => 'Licuadora Multifunción 1200W', 'price' => 189.00, 'compare' => 239.00, 'brand' => 'Oster', 'featured' => true],
            ['cat' => 'Deportes', 'name' => 'Bicicleta Montañera Aro 29', 'price' => 1299.00, 'compare' => 1599.00, 'brand' => 'GW', 'featured' => true],
            ['cat' => 'Deportes', 'name' => 'Set de Pesas Ajustables 20kg', 'price' => 349.90, 'compare' => null, 'brand' => 'Everlast', 'featured' => false],
            ['cat' => 'Belleza', 'name' => 'Set de Maquillaje Profesional', 'price' => 149.90, 'compare' => 189.90, 'brand' => 'Maybelline', 'featured' => false],
            ['cat' => 'Belleza', 'name' => 'Perfume Eau de Parfum 100ml', 'price' => 259.00, 'compare' => null, 'brand' => 'Carolina Herrera', 'featured' => true],
            ['cat' => 'Juguetes', 'name' => 'Set de Bloques de Construcción', 'price' => 99.90, 'compare' => 129.90, 'brand' => 'Lego', 'featured' => false],
            ['cat' => 'Juguetes', 'name' => 'Drone con Cámara HD', 'price' => 399.00, 'compare' => 499.00, 'brand' => 'DJI Mini', 'featured' => true],
        ];

        foreach ($productos as $p) {
            $category = Category::where('name', $p['cat'])->first();
            Product::create([
                'category_id' => $category->id,
                'name' => $p['name'],
                'slug' => Str::slug($p['name']).'-'.Str::random(5),
                'description' => "Producto {$p['name']} de la marca {$p['brand']}. Calidad garantizada, disponible en Negocios RaR con envío a todo el país.",
                'price' => $p['price'],
                'compare_price' => $p['compare'],
                'sku' => strtoupper(Str::random(8)),
                'stock' => rand(5, 60),
                'brand' => $p['brand'],
                'attributes' => json_encode(['garantia' => '12 meses', 'origen' => 'Importado']),
                'featured' => $p['featured'],
                'active' => true,
                'rating' => rand(35, 50) / 10,
            ]);
        }
    }
}
