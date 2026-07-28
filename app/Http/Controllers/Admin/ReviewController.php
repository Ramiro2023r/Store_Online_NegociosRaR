<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['product', 'user'])
            ->latest()
            ->paginate(20);

        $pendingCount = Review::where('approved', false)->count();

        return view('admin.reviews.index', compact('reviews', 'pendingCount'));
    }

    public function approve(Review $review)
    {
        $review->update(['approved' => true]);

        return back()->with('success', 'Reseña aprobada correctamente.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Reseña eliminada.');
    }
}
