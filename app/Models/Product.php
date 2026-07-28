<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'compare_price',
        'sku', 'stock', 'min_stock', 'restock_quantity', 'brand', 'attributes', 'main_image', 'video_url', 'featured', 'active', 'rating',
        'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'featured' => 'boolean',
            'active' => 'boolean',
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('size')->orderBy('color');
    }

    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('active', true)->orderBy('size')->orderBy('color');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('approved', true);
    }

    public function averageRating(): float
    {
        return round((float) $this->approvedReviews()->avg('rating'), 1);
    }

    public function reviewsCount(): int
    {
        return $this->approvedReviews()->count();
    }

    public function hasDiscount(): bool
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    public function discountPercent(): int
    {
        if (! $this->hasDiscount()) {
            return 0;
        }

        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function hasVariants(): bool
    {
        return $this->activeVariants()->exists();
    }

    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= $this->min_stock;
    }

    public function needsRestock(): bool
    {
        return $this->stock <= $this->min_stock;
    }
}
