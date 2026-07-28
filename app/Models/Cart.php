<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id', 'last_active_at'];

    protected function casts(): array
    {
        return [
            'last_active_at' => 'datetime',
        ];
    }

    public function touchLastActive(): void
    {
        $this->update(['last_active_at' => now()]);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function total()
    {
        return $this->items->sum(fn ($item) => $item->unit_price * $item->quantity);
    }
}
