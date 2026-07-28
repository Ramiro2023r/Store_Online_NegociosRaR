<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items', 'user');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pendiente,pagado,enviado,entregado,cancelado']);

        $awardPoints = $request->status === 'entregado' && $order->status !== 'entregado';

        $order->update(['status' => $request->status]);
        $order->statuses()->create(['status' => $request->status]);

        if ($awardPoints) {
            $rate = (float) Setting::getValue('points_earning_rate', 1);
            $points = (int) floor($order->subtotal * $rate);

            if ($points > 0) {
                $order->user->increment('loyalty_points', $points);
                $order->user->increment('lifetime_points', $points);

                LoyaltyTransaction::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'type' => 'earned',
                    'points' => $points,
                    'description' => "Compra #{$order->order_number} — S/ {$order->subtotal}",
                ]);
            }
        }

        try {
            Mail::to($order->user->email)->queue(new OrderStatusUpdatedMail($order));
        } catch (\Throwable $e) {
            Log::error('Error al enviar notificación de cambio de estado: '.$e->getMessage());
        }

        return back()->with('success', 'Estado del pedido actualizado.');
    }
}
