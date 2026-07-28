<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->latest()->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
            'description' => 'nullable|string',
        ]);
        $data['slug'] = Str::slug($request->name);
        $data['active'] = true;

        Category::create($data);

        return back()->with('success', 'Categoría creada.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:10',
            'description' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);
        $data['active'] = $request->boolean('active');

        $category->update($data);

        return back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return back()->with('success', 'Categoría eliminada.');
    }
}
