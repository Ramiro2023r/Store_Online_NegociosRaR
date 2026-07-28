<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Benefit;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Product::with('category')->where('active', true)->where('featured', true)->latest()->take(8)->get();
        $categories = Category::where('active', true)->withCount('products')->get();
        $newest = Product::with('category')->where('active', true)->latest()->take(8)->get();
        $banners = Banner::active()->ordered()->get();
        $benefits = Benefit::active()->ordered()->get();

        return view('home', compact('featured', 'categories', 'newest', 'banners', 'benefits'));
    }

    public function about()
    {
        return view('about');
    }
}
