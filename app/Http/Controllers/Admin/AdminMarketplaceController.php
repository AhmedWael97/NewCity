<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceItem;
use App\Models\Category;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminMarketplaceController extends Controller
{
    /**
     * Display a listing of marketplace items
     */
    public function index(Request $request)
    {
        $query = MarketplaceItem::with(['user', 'category', 'city']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // City filter
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sponsored filter
        if ($request->filled('sponsored')) {
            if ($request->sponsored === 'yes') {
                $query->where('is_sponsored', true)->where('sponsored_until', '>', now());
            } elseif ($request->sponsored === 'no') {
                $query->where(function ($q) {
                    $q->where('is_sponsored', false)
                      ->orWhere('sponsored_until', '<=', now())
                      ->orWhereNull('sponsored_until');
                });
            }
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $items = $query->paginate(20)->withQueryString();

        // Get filter options
        $cities = City::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $statuses = ['active', 'sold', 'pending', 'rejected'];

        return view('admin.marketplace.index', compact('items', 'cities', 'categories', 'statuses'));
    }

    /**
     * Display the specified item
     */
    public function show(MarketplaceItem $item)
    {
        $item->load(['user', 'category', 'city', 'sponsorships' => function ($query) {
            $query->orderByDesc('created_at');
        }]);

        return view('admin.marketplace.show', compact('item'));
    }

    /**
     * Approve an item
     */
    public function approve(MarketplaceItem $item)
    {
        $item->approve();

        return redirect()->back()->with('success', 'Marketplace item approved successfully');
    }

    /**
     * Reject an item
     */
    public function reject(Request $request, MarketplaceItem $item)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $item->reject($request->rejection_reason);

        return redirect()->back()->with('success', 'Marketplace item rejected successfully');
    }

    /**
     * Delete an item
     */
    public function destroy(MarketplaceItem $item)
    {
        // Delete images from storage
        if ($item->images) {
            foreach ($item->images as $image) {
                if (str_contains($image, 'storage/marketplace/')) {
                    $path = str_replace(url('storage/'), '', $image);
                    Storage::disk('public')->delete($path);
                }
            }
        }

        $item->delete();

        return redirect()->route('admin.marketplace.index')
            ->with('success', 'Marketplace item deleted successfully');
    }

    /**
     * Bulk action handler
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:marketplace_items,id',
            'rejection_reason' => 'required_if:action,reject|string|max:500',
        ]);

        $items = MarketplaceItem::whereIn('id', $request->item_ids)->get();

        foreach ($items as $item) {
            switch ($request->action) {
                case 'approve':
                    $item->approve();
                    break;
                case 'reject':
                    $item->reject($request->rejection_reason);
                    break;
                case 'delete':
                    // Delete images
                    if ($item->images) {
                        foreach ($item->images as $image) {
                            if (str_contains($image, 'storage/marketplace/')) {
                                $path = str_replace(url('storage/'), '', $image);
                                Storage::disk('public')->delete($path);
                            }
                        }
                    }
                    $item->delete();
                    break;
            }
        }

        return redirect()->back()->with('success', 'Bulk action completed successfully');
    }

    /**
     * Statistics page
     */
    public function statistics()
    {
        $stats = [
            'total_items' => MarketplaceItem::count(),
            'active_items' => MarketplaceItem::where('status', 'active')->count(),
            'sold_items' => MarketplaceItem::where('status', 'sold')->count(),
            'pending_items' => MarketplaceItem::where('status', 'pending')->count(),
            'rejected_items' => MarketplaceItem::where('status', 'rejected')->count(),
            'sponsored_items' => MarketplaceItem::where('is_sponsored', true)
                ->where('sponsored_until', '>', now())
                ->count(),
            'total_views' => MarketplaceItem::sum('view_count'),
            'total_contacts' => MarketplaceItem::sum('contact_count'),
        ];

        // Top performing items
        $topItems = MarketplaceItem::with(['user', 'category'])
            ->orderByDesc('contact_count')
            ->limit(10)
            ->get();

        // Items by city
        $itemsByCity = MarketplaceItem::select('city_id', \DB::raw('count(*) as count'))
            ->with('city:id,name')
            ->groupBy('city_id')
            ->orderByDesc('count')
            ->get();

        // Items by category
        $itemsByCategory = MarketplaceItem::select('category_id', \DB::raw('count(*) as count'))
            ->with('category:id,name')
            ->groupBy('category_id')
            ->orderByDesc('count')
            ->get();

        return view('admin.marketplace.statistics', compact('stats', 'topItems', 'itemsByCity', 'itemsByCategory'));
    }
}
