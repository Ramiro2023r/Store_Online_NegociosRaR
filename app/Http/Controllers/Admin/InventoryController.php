<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Filtro de alertas
        if ($request->filled('alert')) {
            if ($request->alert === 'low') {
                $query->where('stock', '>', 0)->whereRaw('stock <= min_stock');
            } elseif ($request->alert === 'out') {
                $query->where('stock', '<=', 0)->where('active', true);
            }
        }

        // Búsqueda
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ilike', "%{$request->search}%")
                  ->orWhere('sku', 'ilike', "%{$request->search}%");
            });
        }

        $products = $query->withCount([
            'variants as active_variants_count' => fn ($q) => $q->where('active', true),
            'variants as variants_low_stock' => fn ($q) => $q->where('stock', '>', 0)->whereRaw('stock <= products.min_stock'),
        ])->orderByRaw('stock ASC')->paginate(20)->withQueryString();

        $stats = [
            'total' => Product::count(),
            'low_stock' => Product::where('stock', '>', 0)->whereRaw('stock <= min_stock')->count(),
            'out_of_stock' => Product::where('stock', '<=', 0)->where('active', true)->count(),
            'in_stock' => Product::where('stock', '>', 0)->count(),
            'needs_restock' => Product::where('stock', '>', 0)->whereRaw('stock <= min_stock')->count(),
        ];

        return view('admin.inventory.index', compact('products', 'stats'));
    }

    public function history(Request $request, ?Product $product = null)
    {
        $query = StockMovement::with('product', 'variant', 'user');

        if ($product && $product->exists) {
            $query->where('product_id', $product->id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->latest()->paginate(30)->withQueryString();

        $products = Product::orderBy('name')->get(['id', 'name']);

        return view('admin.inventory.history', compact('movements', 'products', 'product'));
    }

    public function restockForm(Product $product, ?ProductVariant $variant = null)
    {
        $product->load('activeVariants');
        return view('admin.inventory.restock', compact('product', 'variant'));
    }

    public function restockStore(Request $request, Product $product, ?ProductVariant $variant = null)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        app(StockService::class)->recordRestock(
            $product,
            (int) $request->quantity,
            $variant,
            $request->notes
        );

        $label = $variant ? "variante {$variant->size}/{$variant->color}" : $product->name;
        return redirect()->route('admin.inventory.index')
            ->with('success', "Reabastecimiento de {$label} registrado (+{$request->quantity}).");
    }
}
