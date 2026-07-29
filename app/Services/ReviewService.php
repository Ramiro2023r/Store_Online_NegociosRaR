<?php

namespace App\Services;

use App\Models\Review;

class ReviewService
{
    public function search(?string $status = null): array
    {
        $q = Review::with('product:id,name', 'user:id,name');
        if ($status === 'pending') $q->where('approved', false);
        elseif ($status === 'approved') $q->where('approved', true);
        return $q->latest()->get()->toArray();
    }

    public function approve(int $id): Review
    {
        $review = Review::findOrFail($id);
        $review->update(['approved' => true]);
        return $review->fresh();
    }

    public function reject(int $id): Review
    {
        $review = Review::findOrFail($id);
        $review->update(['approved' => false]);
        return $review->fresh();
    }

    public function delete(int $id): void
    {
        Review::findOrFail($id)->delete();
    }

    public function summary(): array
    {
        return [
            'total' => Review::count(),
            'approved' => Review::where('approved', true)->count(),
            'pending' => Review::where('approved', false)->count(),
        ];
    }
}
