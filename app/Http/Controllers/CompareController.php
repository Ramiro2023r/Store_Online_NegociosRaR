<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index()
    {
        $ids = session('comparison', []);
        $products = Product::with('category')->whereIn('id', $ids)->get();

        $attributes = collect();
        if ($products->isNotEmpty()) {
            $allKeys = $products->flatMap(fn ($p) => array_keys($p->attributes ?? []))->unique();
            $attributes = $allKeys;
        }

        return view('compare.index', compact('products', 'attributes'));
    }

    public function toggle(Product $product)
    {
        $ids = session('comparison', []);

        if (in_array($product->id, $ids)) {
            $ids = array_filter($ids, fn ($id) => $id != $product->id);
            session(['comparison' => array_values($ids)]);
            return back()->with('success', 'Producto eliminado de la comparación.');
        }

        if (count($ids) >= 4) {
            return back()->withErrors(['max' => 'Máximo 4 productos para comparar.']);
        }

        if (!empty($ids)) {
            $first = Product::find(head($ids));
            if ($first && $first->category_id !== $product->category_id) {
                return back()->withErrors(['category' => 'Solo puedes comparar productos de la misma categoría.']);
            }
        }

        $ids[] = $product->id;
        session(['comparison' => $ids]);

        $count = count($ids);
        $message = $count === 4
            ? 'Producto agregado. Ya tienes 4 productos para comparar.'
            : 'Producto agregado a la comparación.';

        return back()->with('success', $message);
    }

    public function clear()
    {
        session()->forget('comparison');
        return redirect()->route('compare.index')->with('success', 'Comparación limpiada.');
    }

    public function count(): int
    {
        return count(session('comparison', []));
    }
}
