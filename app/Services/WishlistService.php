<?php

namespace App\Services;

use App\Models\Wishlist;

class WishlistService
{
    public function get(int $userId): array
    {
        return Wishlist::where('user_id', $userId)->with('product')->latest()->get()->toArray();
    }

    public function add(int $userId, int $productId): Wishlist
    {
        return Wishlist::firstOrCreate(['user_id' => $userId, 'product_id' => $productId]);
    }

    public function remove(int $userId, int $productId): void
    {
        Wishlist::where('user_id', $userId)->where('product_id', $productId)->delete();
    }
}
