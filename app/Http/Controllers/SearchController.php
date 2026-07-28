<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggestions(Request $request)
    {
        $q = $request->get('q', '');

        if (mb_strlen($q) < 2) {
            return response()->json(['products' => [], 'categories' => [], 'brands' => []]);
        }

        $products = Product::where('active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'ilike', '%' . $q . '%')
                    ->orWhereRaw('similarity(name, ?) > 0.25', [$q])
                    ->orWhereRaw('similarity(COALESCE(brand, \'\'), ?) > 0.3', [$q]);
            })
            ->orderByRaw('CASE WHEN name ilike ? THEN 0 ELSE 1 END', [$q . '%'])
            ->orderByRaw('similarity(name, ?) DESC', [$q])
            ->take(6)
            ->get(['id', 'name', 'slug', 'price', 'main_image', 'brand']);

        $categories = Category::where('active', true)
            ->where('name', 'ilike', '%' . $q . '%')
            ->take(3)
            ->get(['id', 'name', 'slug', 'icon']);

        $brands = Product::where('active', true)
            ->whereNotNull('brand')
            ->where('brand', 'ilike', '%' . $q . '%')
            ->distinct()
            ->take(3)
            ->pluck('brand');

        return response()->json([
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
