<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id', 'variant_id', 'type', 'quantity',
        'reference_type', 'reference_id',
        'previous_stock', 'new_stock', 'user_id', 'notes',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeSales($query)
    {
        return $query->where('type', 'sale');
    }

    public function scopeRestocks($query)
    {
        return $query->where('type', 'restock');
    }
}
