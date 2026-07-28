<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected function currentCart(Request $request): Cart
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            $cart->touchLastActive();
            return $cart;
        }

        if (! $request->session()->has('cart_session_id')) {
            $request->session()->put('cart_session_id', (string) \Illuminate\Support\Str::uuid());
        }

        $cart = Cart::firstOrCreate(['session_id' => $request->session()->get('cart_session_id'), 'user_id' => null]);
        $cart->touchLastActive();
        return $cart;
    }

    public function index(Request $request)
    {
        $cart = $this->currentCart($request)->load('items.product');

        return view('cart.index', compact('cart'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,id',
        ]);
        $cart = $this->currentCart($request);
        $qty = $request->get('quantity', 1);
        $variantId = $request->get('variant_id');

        $variant = $variantId ? ProductVariant::findOrFail($variantId) : null;

        $price = $variant && $variant->price ? $variant->price : $product->price;

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('variant_id', $variantId)
            ->first();

        if ($item) {
            $item->update(['quantity' => $item->quantity + $qty]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'variant_id' => $variantId,
                'quantity' => $qty,
                'unit_price' => $price,
            ]);
        }

        return back()->with('success', 'Producto agregado al carrito.');
    }

    public function update(Request $request, CartItem $item)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $item->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Carrito actualizado.');
    }

    public function remove(CartItem $item)
    {
        $item->delete();

        return back()->with('success', 'Producto eliminado del carrito.');
    }
}
