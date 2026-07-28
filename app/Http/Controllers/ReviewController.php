<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        if (! $product->active) {
            return back()->with('error', 'Este producto no está disponible.');
        }

        $user = $request->user();

        // Must have email verified
        if (! $user->hasVerifiedEmail()) {
            return back()->with('error', 'Debes verificar tu correo electrónico antes de reseñar.');
        }

        // Must have at least one delivered order containing this product
        $hasDeliveredPurchase = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.user_id', $user->id)
            ->where('orders.status', 'entregado')
            ->where('order_items.product_id', $product->id)
            ->exists();

        if (! $hasDeliveredPurchase) {
            return back()->with('error', 'Solo puedes reseñar productos que hayas comprado y recibido.');
        }

        // Check if already reviewed
        $existing = Review::where('product_id', $product->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Ya has reseñado este producto.');
        }

        Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Tu reseña ha sido enviada y será publicada tras la revisión del administrador.');
    }

    public function destroy(Request $request, Review $review)
    {
        abort_if($review->user_id !== $request->user()->id && ! $request->user()->isAdmin(), 403);

        $review->delete();

        return back()->with('success', 'Reseña eliminada.');
    }
}
