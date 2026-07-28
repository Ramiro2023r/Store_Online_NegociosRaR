<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class SitemapController extends Controller
{
    public function index()
    {
        $static = [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('products.index'), 'priority' => '0.9'],
            ['loc' => route('about'), 'priority' => '0.7'],
            ['loc' => route('shipping-returns'), 'priority' => '0.6'],
            ['loc' => route('contact.index'), 'priority' => '0.6'],
            ['loc' => route('compare.index'), 'priority' => '0.5'],
            ['loc' => route('privacy-policy'), 'priority' => '0.4'],
            ['loc' => route('terms-conditions'), 'priority' => '0.4'],
        ];

        $products = Product::where('active', true)->latest('updated_at')->get(['slug', 'updated_at']);
        $categories = Category::has('products')->get(['slug', 'updated_at']);

        return response()->view('sitemap', compact('static', 'products', 'categories'))
            ->header('Content-Type', 'application/xml');
    }
}
