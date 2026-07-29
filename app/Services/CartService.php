<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;

class CartService
{
    public function get(int $userId): ?Cart
    {
        $cart = Cart::where('user_id', $userId)->with('items.product', 'items.variant')->first();
        return $cart;
    }

    public function addItem(int $userId, int $productId, int $quantity = 1, ?int $variantId = null): CartItem
    {
        $product = Product::findOrFail($productId);
        $cart = Cart::firstOrCreate(['user_id' => $userId], ['last_active_at' => now()]);

        $price = $product->price;
        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            $price = $variant->price ?? $product->price;
        }

        $existing = $cart->items()->where('product_id', $productId)->where('variant_id', $variantId)->first();
        if ($existing) {
            $existing->increment('quantity', $quantity);
            $cart->touchLastActive();
            return $existing->fresh();
        }

        $item = $cart->items()->create([
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity' => $quantity,
            'unit_price' => $price,
        ]);
        $cart->touchLastActive();
        return $item;
    }

    public function updateQuantity(int $userId, int $itemId, int $quantity): CartItem
    {
        $cart = Cart::where('user_id', $userId)->firstOrFail();
        $item = $cart->items()->findOrFail($itemId);
        $item->update(['quantity' => max(1, $quantity)]);
        $cart->touchLastActive();
        return $item->fresh();
    }

    public function removeItem(int $userId, int $itemId): void
    {
        $cart = Cart::where('user_id', $userId)->firstOrFail();
        $cart->items()->findOrFail($itemId)->delete();
        $cart->touchLastActive();
    }

    public function estimateTotals(int $userId): array
    {
        $cart = $this->get($userId);
        if (!$cart) return ['subtotal' => 0, 'items_count' => 0];
        $subtotal = $cart->items->sum(fn ($i) => $i->unit_price * $i->quantity);
        return ['subtotal' => $subtotal, 'items_count' => $cart->items->count()];
    }
}
