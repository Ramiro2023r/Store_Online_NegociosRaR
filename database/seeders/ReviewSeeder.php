<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $clientes = User::where('role', 'cliente')->get();

        if ($products->isEmpty() || $clientes->isEmpty()) {
            return;
        }

        $reviewsData = [
            ['rating' => 5, 'comment' => 'Excelente producto, muy recomendado. Llegó antes de lo esperado.'],
            ['rating' => 4, 'comment' => 'Muy buena calidad, cumple con lo descrito.'],
            ['rating' => 5, 'comment' => 'Perfecto, volvería a comprar sin dudas.'],
            ['rating' => 3, 'comment' => 'Está bien por el precio, pero esperaba un poco más.'],
            ['rating' => 4, 'comment' => 'Buen producto, relación calidad-precio adecuada.'],
            ['rating' => 2, 'comment' => 'No cumplió mis expectativas, pero el envío fue rápido.'],
            ['rating' => 5, 'comment' => 'Increíble, superó todas mis expectativas.'],
            ['rating' => 4, 'comment' => 'Bueno, lo recomiendo.'],
        ];

        foreach ($products as $product) {
            $numReviews = rand(1, 3);
            $usedUsers = [];

            for ($i = 0; $i < $numReviews; $i++) {
                $user = $clientes->random();

                if (in_array($user->id, $usedUsers)) {
                    continue;
                }

                $usedUsers[] = $user->id;
                $data = $reviewsData[array_rand($reviewsData)];

                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'rating' => $data['rating'],
                    'comment' => $data['comment'],
                    'approved' => true,
                ]);
            }
        }
    }
}
