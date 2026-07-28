<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function recordSale(Product $product, int $quantity, ?ProductVariant $variant = null, ?int $orderId = null, ?int $userId = null): void
    {
        DB::transaction(function () use ($product, $quantity, $variant, $orderId, $userId) {
            if ($variant) {
                $prev = $variant->stock;
                $new = $prev - $quantity;
                $variant->decrement('stock', $quantity);
                $product->decrement('stock', $quantity);

                StockMovement::create([
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'type' => 'sale',
                    'quantity' => -$quantity,
                    'reference_type' => 'order',
                    'reference_id' => $orderId,
                    'previous_stock' => $prev,
                    'new_stock' => $new,
                    'user_id' => $userId ?? Auth::id(),
                    'notes' => "Venta #{$orderId} — {$variant->size}/{$variant->color}",
                ]);
            } else {
                $prev = $product->stock;
                $new = $prev - $quantity;
                $product->decrement('stock', $quantity);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity' => -$quantity,
                    'reference_type' => 'order',
                    'reference_id' => $orderId,
                    'previous_stock' => $prev,
                    'new_stock' => $new,
                    'user_id' => $userId ?? Auth::id(),
                    'notes' => "Venta #{$orderId}",
                ]);
            }
        });
    }

    public function recordRestock(Product $product, int $quantity, ?ProductVariant $variant = null, ?string $notes = null): void
    {
        DB::transaction(function () use ($product, $quantity, $variant, $notes) {
            if ($variant) {
                $prev = $variant->stock;
                $new = $prev + $quantity;
                $variant->increment('stock', $quantity);
                $product->increment('stock', $quantity);

                StockMovement::create([
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'type' => 'restock',
                    'quantity' => $quantity,
                    'reference_type' => 'manual',
                    'previous_stock' => $prev,
                    'new_stock' => $new,
                    'user_id' => Auth::id(),
                    'notes' => $notes ?? "Reabastecimiento — {$variant->size}/{$variant->color}",
                ]);
            } else {
                $prev = $product->stock;
                $new = $prev + $quantity;
                $product->increment('stock', $quantity);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'restock',
                    'quantity' => $quantity,
                    'reference_type' => 'manual',
                    'previous_stock' => $prev,
                    'new_stock' => $new,
                    'user_id' => Auth::id(),
                    'notes' => $notes ?? 'Reabastecimiento manual',
                ]);
            }
        });
    }

    public function recordAdjustment(Product $product, int $newStock, ?ProductVariant $variant = null, ?string $notes = null): void
    {
        DB::transaction(function () use ($product, $newStock, $variant, $notes) {
            if ($variant) {
                $prev = $variant->stock;
                $diff = $newStock - $prev;

                $variant->update(['stock' => $newStock]);
                $product->decrement('stock', -$diff); // ajuste inverso al total

                StockMovement::create([
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'type' => 'adjustment',
                    'quantity' => $diff,
                    'reference_type' => 'manual',
                    'previous_stock' => $prev,
                    'new_stock' => $newStock,
                    'user_id' => Auth::id(),
                    'notes' => $notes ?? "Ajuste manual — {$variant->size}/{$variant->color}",
                ]);
            } else {
                $prev = $product->stock;
                $diff = $newStock - $prev;

                $product->update(['stock' => $newStock]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'adjustment',
                    'quantity' => $diff,
                    'reference_type' => 'manual',
                    'previous_stock' => $prev,
                    'new_stock' => $newStock,
                    'user_id' => Auth::id(),
                    'notes' => $notes ?? 'Ajuste manual',
                ]);
            }
        });
    }
}
