<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'category_id', 'min_purchase',
        'max_discount', 'usage_limit', 'usage_count', 'usage_limit_per_user',
        'starts_at', 'expires_at', 'active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_purchase' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function uses()
    {
        return $this->hasMany(CouponUse::class);
    }

    public function isValid(): bool
    {
        if (! $this->active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function isValidForUser(User $user): bool
    {
        if (! $this->isValid()) {
            return false;
        }

        if ($this->usage_limit_per_user === null) {
            return true;
        }

        $userUsage = $this->uses()->where('user_id', $user->id)->count();

        return $userUsage < $this->usage_limit_per_user;
    }

    public function calculateDiscount(float $subtotal, $cartItems): float
    {
        $applicableSubtotal = $subtotal;

        if ($this->category_id) {
            $categoryProductIds = Product::where('category_id', $this->category_id)->pluck('id')->toArray();
            $applicableSubtotal = $cartItems->filter(fn ($item) => in_array($item->product_id, $categoryProductIds))
                ->sum(fn ($item) => $item->unit_price * $item->quantity);
        }

        if ($applicableSubtotal <= 0) {
            return 0;
        }

        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = $applicableSubtotal * ($this->value / 100);
            if ($this->max_discount !== null && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } else {
            $discount = min($this->value, $applicableSubtotal);
        }

        return round($discount, 2);
    }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', strtoupper($code))->first();
    }
}
