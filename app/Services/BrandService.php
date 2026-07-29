<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class BrandService
{
    public function search(?string $query = null): array
    {
        $brands = Product::select('brand', DB::raw('count(*) as products_count'))
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->when($query, fn ($q) => $q->where('brand', 'ilike', "%{$query}%"))
            ->groupBy('brand')
            ->orderBy('products_count', 'desc')
            ->get()
            ->toArray();

        return $brands;
    }
}
