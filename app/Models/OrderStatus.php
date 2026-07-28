<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $fillable = ['order_id', 'status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pendiente' => 'Pedido recibido',
            'pagado' => 'Pago confirmado',
            'enviado' => 'En camino',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
            default => ucfirst($this->status),
        };
    }

    public function icon(): string
    {
        return match ($this->status) {
            'pendiente' => '📋',
            'pagado' => '💳',
            'enviado' => '🚚',
            'entregado' => '✅',
            'cancelado' => '❌',
            default => '📌',
        };
    }
}
