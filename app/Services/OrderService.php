<?php

namespace App\Services;

use App\Models\LoyaltyTransaction;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusUpdatedMail;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function search(array $filters = [], int $perPage = 20): array
    {
        $query = Order::with('user');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['customer_id'])) {
            $query->where('user_id', $filters['customer_id']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        if (!empty($filters['query'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('order_number', 'ilike', '%'.$filters['query'].'%')
                  ->orWhere('id', $filters['query']);
            });
        }

        return $query->latest()->paginate($perPage)->toArray();
    }

    public function find(int $id): ?Order
    {
        return Order::with('user', 'items', 'statuses')->find($id);
    }

    public function updateStatus(int $id, string $status): Order
    {
        $order = Order::with('user')->findOrFail($id);

        $awardPoints = $status === 'entregado' && $order->status !== 'entregado';

        $order->update(['status' => $status]);
        $order->statuses()->create(['status' => $status]);

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
            Log::error('Error al enviar notificación: '.$e->getMessage());
        }

        return $order->fresh();
    }

    public function timeline(int $id): array
    {
        $order = Order::findOrFail($id);
        return $order->statuses()->get(['id', 'status', 'created_at'])->toArray();
    }

    public function cancel(int $id): Order
    {
        return $this->updateStatus($id, 'cancelado');
    }
}
