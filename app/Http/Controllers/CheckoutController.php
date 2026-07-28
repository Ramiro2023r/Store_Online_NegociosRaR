<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUse;
use App\Models\Address;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Services\StockService;
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
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()])->load('items.product', 'items.variant');
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

        $addresses = Address::where('user_id', Auth::id())->latest()->get();
        $user = Auth::user();
        $pointsBalance = $user->loyalty_points;
        $redeemRate = (float) Setting::getValue('points_redeem_rate', 0.10);
        $minPoints = (int) Setting::getValue('min_points_to_redeem', 100);
        $pointsDiscount = session('points_discount', 0);

        return view('checkout.index', compact('cart', 'coupon', 'discount', 'addresses', 'pointsBalance', 'redeemRate', 'minPoints', 'pointsDiscount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'nullable|exists:addresses,id',
            'new_address' => 'nullable|string|max:255',
            'new_address_label' => 'nullable|string|max:50',
            'new_city' => 'nullable|string|max:100',
            'new_phone' => 'nullable|string|max:30',
            'save_address' => 'nullable|boolean',
            'shipping_address' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_phone' => 'nullable|string|max:30',
            'payment_method' => 'required|in:contraentrega,transferencia,tarjeta',
            'notes' => 'nullable|string',
            'culqi_token' => 'nullable|string',
            'redeem_points' => 'nullable|boolean',
        ]);

        $cart = Cart::where('user_id', Auth::id())->with('items.product')->firstOrFail();
        abort_if($cart->items->isEmpty(), 400);

        $subtotal = $cart->items->sum(fn ($i) => $i->unit_price * $i->quantity);
        $minFree = (float) Setting::getValue('shipping_min_amount', 200);
        $shipCost = (float) Setting::getValue('shipping_cost', 15);
        $shipping = $subtotal >= $minFree ? 0 : $shipCost;

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

        // Puntos fidelización
        $user = Auth::user();
        $redeemRate = (float) Setting::getValue('points_redeem_rate', 0.10);
        $minPoints = (int) Setting::getValue('min_points_to_redeem', 100);
        $pointsDiscount = 0;
        $pointsUsed = 0;

        if ($request->boolean('redeem_points') && $user->loyalty_points >= $minPoints) {
            $maxRedeem = (int) floor(($subtotal + $shipping - $discount) / $redeemRate);
            $pointsToUse = min($user->loyalty_points, $maxRedeem);
            if ($pointsToUse >= $minPoints) {
                $pointsDiscount = round($pointsToUse * $redeemRate, 2);
                $pointsUsed = $pointsToUse;
            }
        }

        $total = $subtotal + $shipping - $discount - $pointsDiscount;

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
            // Resolver dirección de envío
            if ($request->filled('address_id')) {
                $addr = Address::findOrFail($request->address_id);
                $shipAddr = $addr->address;
                $shipCity = $addr->city;
                $shipPhone = $addr->phone;
            } else {
                $shipAddr = $request->new_address ?? $request->shipping_address;
                $shipCity = $request->new_city ?? $request->shipping_city;
                $shipPhone = $request->new_phone ?? $request->shipping_phone;

                if ($request->boolean('save_address') && $request->filled('new_address')) {
                    Address::create([
                        'user_id' => Auth::id(),
                        'label' => $request->new_address_label ?? 'Casa',
                        'address' => $shipAddr,
                        'city' => $shipCity,
                        'phone' => $shipPhone,
                    ]);
                }
            }

            $orderData = [
                'order_number' => 'RAR-' . strtoupper(Str::random(8)),
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'shipping_cost' => $shipping,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'shipping_address' => $shipAddr,
                'shipping_city' => $shipCity,
                'shipping_phone' => $shipPhone,
                'notes' => $request->notes,
            ];

            if ($pointsDiscount > 0) {
                $orderData['discount_amount'] = ($orderData['discount_amount'] ?? 0) + $pointsDiscount;
            }

            if ($coupon && $discount > 0) {
                $orderData['coupon_id'] = $coupon->id;
                $orderData['coupon_code'] = $coupon->code;
                $orderData['discount_amount'] = ($orderData['discount_amount'] ?? 0) + $discount;
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
                    'variant_id' => $item->variant_id,
                    'product_name' => $item->product->name,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'total' => $item->unit_price * $item->quantity,
                ]);

                if ($item->product) {
                    $variant = $item->variant;
                    app(StockService::class)->recordSale(
                        $item->product,
                        $item->quantity,
                        $variant,
                        $order->id,
                        $order->user_id
                    );
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

            if ($pointsUsed > 0) {
                $user->decrement('loyalty_points', $pointsUsed);
                LoyaltyTransaction::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'type' => 'redeemed',
                    'points' => -$pointsUsed,
                    'description' => "Canje en pedido #{$order->order_number} — S/ {$pointsDiscount}",
                ]);
            }

            $order->statuses()->create(['status' => $order->status]);

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

    public function show(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        $order->load('items', 'statuses');

        return view('checkout.show', compact('order'));
    }
}
