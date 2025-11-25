<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Shop;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    /**
     * Display a listing of reviews.
     */
    public function index(Request $request)
    {
        $query = Rating::with(['user:id,name,email', 'shop:id,name']);

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
            $query->where('shop_id', $request->shop_id);
        }

        // Verified filter
        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        $reviews = $query->latest()->paginate(20);
        $shops = Shop::select('id', 'name')->where('status', 'approved')->get();

        return view('admin.reviews.index', compact('reviews', 'shops'));
    }

    /**
     * Display the specified review.
     */
    public function show(Rating $review)
    {
        $review->load(['user', 'shop']);
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Verify a review.
     */
    public function verify(Rating $review)
    {
        $review->update(['is_verified' => true]);
        return back()->with('success', 'Review verified successfully.');
    }

    /**
     * Unverify a review.
     */
    public function unverify(Rating $review)
    {
        $review->update(['is_verified' => false]);
        return back()->with('success', 'Review unverified successfully.');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Rating $review)
    {
        $shopId = $review->shop_id;
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
            $reviews = Rating::where('shop_id', $shopId)->get();

            $shop->review_count = $reviews->count();
            $shop->rating = $reviews->count() > 0 ? round($reviews->avg('rating'), 2) : 0;
            $shop->save();
        }
    }
}
