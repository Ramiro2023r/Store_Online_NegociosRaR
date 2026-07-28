<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tecnología', 'icon' => '💻'],
            ['name' => 'Ropa y Moda', 'icon' => '👕'],
            ['name' => 'Hogar', 'icon' => '🏠'],
            ['name' => 'Deportes', 'icon' => '⚽'],
            ['name' => 'Belleza', 'icon' => '💄'],
            ['name' => 'Juguetes', 'icon' => '🧸'],
        ];

        foreach ($categories as $c) {
            Category::create([
                'name' => $c['name'],
                'slug' => Str::slug($c['name']),
                'icon' => $c['icon'],
                'active' => true,
            ]);
        }
    }
}
