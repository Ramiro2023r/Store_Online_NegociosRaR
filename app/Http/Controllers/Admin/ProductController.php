<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        if ($request->filled('q')) {
            $query->where('name', 'ilike', '%'.$request->q.'%');
        }
        $products = $query->latest()->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['slug'] = Str::slug($request->name).'-'.Str::random(5);

        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product = Product::create($data);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $i => $file) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $file->store('products', 'public'),
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateData($request, $product->id);

        if ($request->hasFile('main_image')) {
            $data['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Producto eliminado.');
    }

    protected function validateData(Request $request, $ignoreId = null): array
    {
        return $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|unique:products,sku,'.$ignoreId,
            'stock' => 'required|integer|min:0',
            'brand' => 'nullable|string|max:100',
            'main_image' => 'nullable|image|max:4096',
            'gallery.*' => 'nullable|image|max:4096',
            'featured' => 'nullable|boolean',
            'active' => 'nullable|boolean',
        ]) + [
            'featured' => $request->boolean('featured'),
            'active' => $request->boolean('active', true),
        ];
    }
}
