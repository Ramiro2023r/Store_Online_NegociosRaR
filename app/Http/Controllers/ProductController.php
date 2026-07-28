<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Product::with('category')->where('active', true);

        if ($request->filled('q')) {
            $query->where('name', 'ilike', '%'.$request->q.'%');
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($c) => $c->where('slug', $request->category));
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->withAvg('approvedReviews as reviews_avg_rating', 'rating')
            ->withCount('approvedReviews as reviews_count')
            ->paginate(12)->withQueryString();
        $categories = Category::where('active', true)->get();
        $brands = Product::where('active', true)->whereNotNull('brand')->distinct()->pluck('brand');

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    public function show(Product $product)
    {
        abort_unless($product->active, 404);
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('active', true)
            ->withAvg('approvedReviews as reviews_avg_rating', 'rating')
            ->withCount('approvedReviews as reviews_count')
            ->take(4)->get();

        $reviews = $product->approvedReviews()->with('user')->latest()->get();

        return view('products.show', compact('product', 'related', 'reviews'));
    }
}
