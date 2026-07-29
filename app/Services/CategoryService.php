<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryService
{
    public function search(array $filters = []): array
    {
        $query = Category::withCount('products');

        if (!empty($filters['query'])) {
            $query->where('name', 'ilike', '%'.$filters['query'].'%');
        }
        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        return $query->latest()->get()->toArray();
    }

    public function find(int $id): ?Category
    {
        return Category::withCount('products')->find($id);
    }

    public function create(array $data): Category
    {
        $data['slug'] = Str::slug($data['name']);
        $data['active'] = $data['active'] ?? true;
        return Category::create($data);
    }

    public function update(int $id, array $data): Category
    {
        $category = Category::findOrFail($id);
        $category->update($data);
        return $category->fresh();
    }

    public function delete(int $id): void
    {
        Category::findOrFail($id)->delete();
    }

    public function changeStatus(int $id, bool $active): Category
    {
        $category = Category::findOrFail($id);
        $category->update(['active' => $active]);
        return $category->fresh();
    }
}
