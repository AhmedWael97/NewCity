<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceReview;
use App\Models\Shop;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    /**
     * Display a listing of reviews.
     */
    public function index(Request $request)
    {
        $query = ServiceReview::with(['user:id,name,email', 'reviewable'])
            ->where('reviewable_type', Shop::class);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Rating filter
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Shop filter
        if ($request->filled('shop_id')) {
            $query->where('reviewable_id', $request->shop_id);
        }

        // Status filter (if you want to add approval status)
        if ($request->filled('is_approved')) {
            $query->where('is_approved', $request->is_approved);
        }

        $reviews = $query->latest()->paginate(20);
        $shops = Shop::select('id', 'name')->where('status', 'approved')->get();

        return view('admin.reviews.index', compact('reviews', 'shops'));
    }

    /**
     * Display the specified review.
     */
    public function show(ServiceReview $review)
    {
        $review->load(['user', 'reviewable']);
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve a review.
     */
    public function approve(ServiceReview $review)
    {
        $review->update(['is_approved' => true]);
        return back()->with('success', 'Review approved successfully.');
    }

    /**
     * Reject a review.
     */
    public function reject(ServiceReview $review)
    {
        $review->update(['is_approved' => false]);
        return back()->with('success', 'Review rejected successfully.');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(ServiceReview $review)
    {
        $shopId = $review->reviewable_id;
        $review->delete();

        // Recalculate shop rating
        $this->updateShopRating($shopId);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }

    /**
     * Update shop rating and review count after review changes.
     */
    private function updateShopRating($shopId)
    {
        $shop = Shop::find($shopId);
        if ($shop) {
            $reviews = ServiceReview::where('reviewable_type', Shop::class)
                ->where('reviewable_id', $shopId)
                ->get();

            $shop->review_count = $reviews->count();
            $shop->rating = $reviews->count() > 0 ? round($reviews->avg('rating'), 2) : 0;
            $shop->save();
        }
    }
}
