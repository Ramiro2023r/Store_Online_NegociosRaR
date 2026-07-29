<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;

class InventoryService
{
    public function lowStock(int $threshold = null, int $limit = 20): array
    {
        $query = Product::where('stock', '>', 0)
            ->whereRaw('stock <= COALESCE(min_stock, 5)');

        if ($threshold !== null) {
            $query->where('stock', '<=', $threshold);
        }

        return $query->orderBy('stock')->limit($limit)->get(['id', 'name', 'sku', 'stock', 'min_stock'])->toArray();
    }

    public function outOfStock(int $limit = 20): array
    {
        return Product::where('stock', '<=', 0)
            ->where('active', true)
            ->orderBy('name')
            ->limit($limit)
            ->get(['id', 'name', 'sku', 'stock'])
            ->toArray();
    }

    public function movements(array $filters = [], int $perPage = 30): array
    {
        $query = StockMovement::with('product:id,name', 'variant:id,size,color', 'user:id,name');

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->limit($perPage)->get()->toArray();
    }

    public function adjustStock(int $productId, int $newStock, ?int $variantId = null, ?string $notes = null): void
    {
        $product = Product::findOrFail($productId);
        $variant = $variantId ? ProductVariant::findOrFail($variantId) : null;

        app(StockService::class)->recordAdjustment($product, $newStock, $variant, $notes);
    }

    public function setMinimumStock(int $productId, int $minStock): Product
    {
        $product = Product::findOrFail($productId);
        $product->update(['min_stock' => max(0, $minStock)]);
        return $product->fresh();
    }
}
