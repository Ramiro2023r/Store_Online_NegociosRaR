<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;

class VariantService
{
    public function search(int $productId): array
    {
        return ProductVariant::where('product_id', $productId)->orderBy('size')->orderBy('color')->get()->toArray();
    }

    public function find(int $id): ?ProductVariant
    {
        return ProductVariant::find($id);
    }

    public function create(int $productId, array $data): ProductVariant
    {
        $data['product_id'] = $productId;
        return ProductVariant::create($data);
    }

    public function update(int $id, array $data): ProductVariant
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->update($data);
        return $variant->fresh();
    }

    public function updateStock(int $id, int $stock): ProductVariant
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->update(['stock' => max(0, $stock)]);
        return $variant->fresh();
    }

    public function delete(int $id): void
    {
        ProductVariant::findOrFail($id)->delete();
    }
}
