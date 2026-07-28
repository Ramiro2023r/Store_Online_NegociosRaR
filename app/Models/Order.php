<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'subtotal', 'shipping_cost', 'total',
        'status', 'payment_method', 'shipping_address', 'shipping_city',
        'shipping_phone', 'notes', 'culqi_charge_id', 'payment_status', 'paid_at',
        'coupon_id', 'coupon_code', 'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statuses()
    {
        return $this->hasMany(OrderStatus::class)->latest();
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pendiente' => 'Pendiente',
            'pagado' => 'Pagado',
            'enviado' => 'Enviado',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
            default => ucfirst($this->status),
        };
    }
}
