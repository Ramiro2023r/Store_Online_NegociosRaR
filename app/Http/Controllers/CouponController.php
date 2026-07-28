<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $coupon = Coupon::findByCode($request->code);

        if (! $coupon) {
            return response()->json(['error' => 'El cupón ingresado no existe.'], 422);
        }

        if (! $coupon->isValidForUser($request->user())) {
            if (! $coupon->isValid()) {
                if (! $coupon->active) {
                    return response()->json(['error' => 'Este cupón está desactivado.'], 422);
                }
                if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
                    return response()->json(['error' => 'Este cupón aún no está vigente.'], 422);
                }
                if ($coupon->expires_at && $coupon->expires_at->isPast()) {
                    return response()->json(['error' => 'Este cupón ha expirado.'], 422);
                }
                if ($coupon->usage_limit !== null && $coupon->usage_count >= $coupon->usage_limit) {
                    return response()->json(['error' => 'Este cupón ya alcanzó su límite de usos.'], 422);
                }
            }
            return response()->json(['error' => 'Ya has usado este cupón el máximo de veces permitido.'], 422);
        }

        $cart = Cart::where('user_id', $request->user()->id)->with('items.product')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Tu carrito está vacío.'], 422);
        }

        $subtotal = $cart->items->sum(fn ($i) => $i->unit_price * $i->quantity);

        if ($subtotal < $coupon->min_purchase) {
            $min = number_format($coupon->min_purchase, 2);
            return response()->json(['error' => "El monto mínimo de compra para este cupón es S/ {$min}."], 422);
        }

        $discount = $coupon->calculateDiscount($subtotal, $cart->items);
        $shipping = $subtotal >= 200 ? 0 : 15;
        $newTotal = $subtotal + $shipping - $discount;

        session(['applied_coupon' => $coupon->id]);

        return response()->json([
            'success' => 'Cupón aplicado correctamente.',
            'coupon_code' => $coupon->code,
            'discount' => $discount,
            'discount_formatted' => 'S/ ' . number_format($discount, 2),
            'new_total' => $newTotal,
            'new_total_formatted' => 'S/ ' . number_format(max($newTotal, 0), 2),
        ]);
    }

    public function remove(Request $request)
    {
        session()->forget('applied_coupon');

        $cart = Cart::where('user_id', $request->user()->id)->with('items.product')->first();
        $subtotal = $cart ? $cart->items->sum(fn ($i) => $i->unit_price * $i->quantity) : 0;
        $shipping = $subtotal >= 200 ? 0 : 15;
        $total = $subtotal + $shipping;

        return response()->json([
            'success' => 'Cupón eliminado.',
            'total' => $total,
            'total_formatted' => 'S/ ' . number_format($total, 2),
        ]);
    }
}
