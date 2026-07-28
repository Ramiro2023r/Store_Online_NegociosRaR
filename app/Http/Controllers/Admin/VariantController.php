<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VariantController extends Controller
{
    public function index(Product $product)
    {
        $product->load('variants');
        return view('admin.variants.index', compact('product'));
    }

    public function edit(Product $product, ProductVariant $variant)
    {
        return view('admin.variants.edit', compact('product', 'variant'));
    }

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'sku' => 'nullable|string|max:100|unique:product_variants,sku',
            'stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:4096',
        ]);

        $data = $request->only(['size', 'color', 'sku', 'stock', 'price', 'compare_price']);
        $data['product_id'] = $product->id;

        $productTotalStock = $product->variants()->sum('stock');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('variants', 'public');
        }

        $variant = ProductVariant::create($data);

        // Sincronizar stock total del producto
        $product->update(['stock' => $productTotalStock + $variant->stock]);

        if ($variant->stock > 0) {
            app(StockService::class)->recordRestock($product, $variant->stock, $variant, 'Stock inicial de variante');
        }

        return redirect()->route('admin.products.variants.index', $product)
            ->with('success', 'Variante agregada.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        $request->validate([
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'sku' => 'nullable|string|max:100|unique:product_variants,sku,' . $variant->id,
            'stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:4096',
            'active' => 'nullable|boolean',
        ]);

        $data = $request->only(['size', 'color', 'sku', 'price', 'compare_price', 'active']);
        $data['active'] = $request->boolean('active', true);

        if ($request->hasFile('image')) {
            if ($variant->image) {
                Storage::disk('public')->delete($variant->image);
            }
            $data['image'] = $request->file('image')->store('variants', 'public');
        }

        $prevStock = $variant->stock;

        // Si el stock cambió, registrar movimiento
        if ((int) $request->stock !== $prevStock) {
            $diff = (int) $request->stock - $prevStock;
            app(StockService::class)->recordAdjustment($product, (int) $request->stock, $variant,
                "Ajuste de {$prevStock} a {$request->stock}");
        }

        $variant->update($data);

        // Actualizar stock total del producto
        $totalStock = $product->variants()->sum('stock');
        $product->update(['stock' => $totalStock]);

        return redirect()->route('admin.products.variants.index', $product)
            ->with('success', 'Variante actualizada.');
    }

    public function destroy(Product $product, ProductVariant $variant)
    {
        if ($variant->image) {
            Storage::disk('public')->delete($variant->image);
        }

        $prevStock = $variant->stock;
        $variant->delete();

        $product->decrement('stock', $prevStock);

        return redirect()->route('admin.products.variants.index', $product)
            ->with('success', 'Variante eliminada.');
    }
}
