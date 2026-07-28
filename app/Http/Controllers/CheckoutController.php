<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUse;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()])->load('items.product');
        abort_if($cart->items->isEmpty(), 302, redirect()->route('cart.index'));

        $coupon = null;
        $discount = 0;

        if (session()->has('applied_coupon')) {
            $coupon = Coupon::find(session('applied_coupon'));
            if ($coupon && $coupon->isValidForUser(Auth::user())) {
                $subtotal = $cart->items->sum(fn ($i) => $i->unit_price * $i->quantity);
                $discount = $coupon->calculateDiscount($subtotal, $cart->items);
            } else {
                session()->forget('applied_coupon');
            }
        }

        return view('checkout.index', compact('cart', 'coupon', 'discount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_phone' => 'required|string|max:30',
            'payment_method' => 'required|in:contraentrega,transferencia,tarjeta',
            'notes' => 'nullable|string',
            'culqi_token' => 'nullable|string',
        ]);

        $cart = Cart::where('user_id', Auth::id())->with('items.product')->firstOrFail();
        abort_if($cart->items->isEmpty(), 400);

        $subtotal = $cart->items->sum(fn ($i) => $i->unit_price * $i->quantity);
        $shipping = $subtotal >= 200 ? 0 : 15;

        $coupon = null;
        $discount = 0;

        if (session()->has('applied_coupon')) {
            $coupon = Coupon::find(session('applied_coupon'));
            if ($coupon && $coupon->isValidForUser(Auth::user())) {
                $discount = $coupon->calculateDiscount($subtotal, $cart->items);
            } else {
                session()->forget('applied_coupon');
            }
        }

        $total = $subtotal + $shipping - $discount;

        $culqiCharge = null;

        if ($request->payment_method === 'tarjeta') {
            if (!$request->filled('culqi_token')) {
                return back()->withErrors(['culqi_token' => 'Token de pago no recibido. Intenta nuevamente.'])->withInput();
            }

            try {
                $response = Http::withToken(config('services.culqi.secret_key'))
                    ->post('https://api.culqi.com/v2/charges', [
                        'amount' => (int) round($total * 100),
                        'currency_code' => 'PEN',
                        'email' => Auth::user()->email,
                        'source_id' => $request->culqi_token,
                        'description' => 'Pedido Negocios RaR',
                    ]);

                if ($response->failed()) {
                    $errorBody = $response->json();
                    $userMessage = $errorBody['user_message'] ?? 'Tu pago fue rechazado. Verifica los datos de tu tarjeta o intenta con otro método.';
                    return back()->with('culqi_error', $userMessage)->withInput();
                }

                $body = $response->json();

                if (($body['outcome']['type'] ?? '') !== 'venta_exitosa') {
                    return back()->with('culqi_error', 'Tu pago fue rechazado. Verifica los datos de tu tarjeta o intenta con otro método.')->withInput();
                }

                $culqiCharge = [
                    'id' => $body['id'],
                ];
            } catch (\Throwable $e) {
                Log::error('Error al conectar con Culqi: ' . $e->getMessage());
                return back()->with('culqi_error', 'Error al procesar el pago. Intenta nuevamente en unos minutos.')->withInput();
            }
        }

        $order = DB::transaction(function () use ($cart, $request, $subtotal, $shipping, $total, $culqiCharge, $coupon, $discount) {
            $orderData = [
                'order_number' => 'RAR-' . strtoupper(Str::random(8)),
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_phone' => $request->shipping_phone,
                'notes' => $request->notes,
            ];

            if ($coupon && $discount > 0) {
                $orderData['coupon_id'] = $coupon->id;
                $orderData['coupon_code'] = $coupon->code;
                $orderData['discount_amount'] = $discount;
            }

            if ($culqiCharge) {
                $orderData['status'] = 'pagado';
                $orderData['payment_status'] = 'pagado';
                $orderData['culqi_charge_id'] = $culqiCharge['id'];
                $orderData['paid_at'] = now();
            } else {
                $orderData['status'] = 'pendiente';
                $orderData['payment_status'] = 'pendiente';
            }

            $order = Order::create($orderData);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'total' => $item->unit_price * $item->quantity,
                ]);

                if ($item->product) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            $cart->items()->delete();

            if ($coupon && $discount > 0) {
                CouponUse::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'discount_amount' => $discount,
                ]);
                $coupon->increment('usage_count');
            }

            return $order;
        });

        session()->forget('applied_coupon');

        try {
            Mail::to($order->user->email)->queue(new OrderConfirmationMail($order));
        } catch (\Throwable $e) {
            Log::error('Error al enviar confirmación de pedido: ' . $e->getMessage());
        }

        $message = $discount > 0
            ? '¡Pedido realizado con éxito! Se aplicó un descuento de S/ ' . number_format($discount, 2) . ' por tu cupón.'
            : '¡Pedido realizado con éxito!';

        return redirect()->route('checkout.success', $order)->with('success', $message);
    }

    public function success(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        return view('checkout.success', compact('order'));
    }

    public function myOrders()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->paginate(10);

        return view('checkout.my-orders', compact('orders'));
    }
}
