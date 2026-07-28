<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Order;
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
        $order->update(['status' => $request->status]);

        try {
            Mail::to($order->user->email)->queue(new OrderStatusUpdatedMail($order));
        } catch (\Throwable $e) {
            Log::error('Error al enviar notificación de cambio de estado: '.$e->getMessage());
        }

        return back()->with('success', 'Estado del pedido actualizado.');
    }
}
