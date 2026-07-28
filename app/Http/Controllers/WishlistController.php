<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $wishlists = $request->user()->wishlists()
            ->with('product.category')
            ->latest()
            ->get();

        return view('wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request, Product $product)
    {
        $existing = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = 'Eliminado de tu lista de deseos.';
        } else {
            Wishlist::create([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
            ]);
            $message = 'Agregado a tu lista de deseos.';
        }

        return back()->with('success', $message);
    }

    public function destroy(Request $request, Wishlist $wishlist)
    {
        abort_if($wishlist->user_id !== $request->user()->id, 403);

        $wishlist->delete();

        return back()->with('success', 'Eliminado de tu lista de deseos.');
    }
}
