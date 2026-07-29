<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductService
{
    public function search(array $filters = [], int $perPage = 20): array
    {
        $query = Product::with('category');

        if (!empty($filters['query'])) {
            $query->where('name', 'ilike', '%'.$filters['query'].'%');
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (!empty($filters['brand'])) {
            $query->where('brand', $filters['brand']);
        }
        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
        if (!empty($filters['status'])) {
            $query->where('active', $filters['status'] === 'active');
        }

        return $query->latest()->paginate($perPage)->toArray();
    }

    public function find(int $id): ?Product
    {
        return Product::with('category')->find($id);
    }

    public function create(array $data): Product
    {
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(5);
        return Product::create($data);
    }

    public function update(int $id, array $data): Product
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product->fresh();
    }

    public function delete(int $id): void
    {
        Product::findOrFail($id)->delete();
    }

    public function duplicate(int $id): Product
    {
        $original = Product::findOrFail($id);
        $data = $original->toArray();
        unset($data['id'], $data['created_at'], $data['updated_at']);
        $data['name'] = $original->name.' (copia)';
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(5);
        $data['sku'] = $original->sku ? $original->sku.'-COPY' : null;
        $data['stock'] = 0;
        return Product::create($data);
    }

    public function updatePrice(int $id, float $price, ?float $comparePrice = null): Product
    {
        $product = Product::findOrFail($id);
        $product->update([
            'price' => $price,
            'compare_price' => $comparePrice ?? $product->compare_price,
        ]);
        return $product->fresh();
    }

    public function updateStock(int $id, int $stock): Product
    {
        $product = Product::findOrFail($id);
        $product->update(['stock' => max(0, $stock)]);
        return $product->fresh();
    }

    public function changeStatus(int $id, bool $active): Product
    {
        $product = Product::findOrFail($id);
        $product->update(['active' => $active]);
        return $product->fresh();
    }
}
