<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'size', 'color', 'sku', 'stock',
        'price', 'compare_price', 'image', 'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function hasDiscount(): bool
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    public function discountPercent(): int
    {
        if (! $this->hasDiscount()) return 0;
        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->product->min_stock;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }
}
