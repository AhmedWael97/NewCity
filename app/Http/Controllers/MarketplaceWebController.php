<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceItem;
use App\Models\Category;
use App\Models\City;
use App\Services\AdminEmailQueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarketplaceWebController extends Controller
{
    /**
     * Display marketplace listings
     */
    public function index(Request $request)
    {
        $query = MarketplaceItem::with(['user', 'category', 'city'])
            ->where('status', 'active')
            ->availableToView();

        // Apply filters
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // Sort - Sponsored items first
        $query->orderByRaw('
            CASE 
                WHEN is_sponsored = 1 AND sponsored_until > NOW() THEN 0 
                ELSE 1 
            END
        ')
        ->orderByDesc('sponsored_priority')
        ->orderByDesc('created_at');

        $items = $query->paginate(20)->withQueryString();

        // Get filter options
        $cities = City::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('marketplace.index', compact('items', 'cities', 'categories'));
    }

    /**
     * Show item details
     */
    public function show(MarketplaceItem $marketplace_item)
    {
        $item = $marketplace_item->load(['user', 'category', 'city', 'activeSponsorship']);

        // Check if item can be viewed
        if (!$item->canBeViewed() && (!Auth::check() || !$item->isOwnedBy(Auth::user()))) {
            return view('marketplace.view-limit-reached', compact('item'));
        }

        // Increment view count if not the owner
        if (!Auth::check() || !$item->isOwnedBy(Auth::user())) {
            $item->incrementViewCount();
        }

        // Get related items
        $relatedItems = MarketplaceItem::where('category_id', $item->category_id)
            ->where('id', '!=', $item->id)
            ->where('status', 'active')
            ->availableToView()
            ->limit(4)
            ->get();

        return view('marketplace.show', compact('item', 'relatedItems'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $cities = City::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('marketplace.create', compact('cities', 'categories'));
    }

    /**
     * Store new item
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:cities,id',
            'condition' => 'required|in:new,like_new,good,fair',
            'is_negotiable' => 'boolean',
            'contact_phone' => 'nullable|string|max:20',
            'contact_whatsapp' => 'nullable|string|max:20',
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('marketplace', 'public');
                $imagePaths[] = $path;
            }
        }

        $item = MarketplaceItem::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'city_id' => $request->city_id,
            'condition' => $request->condition,
            'is_negotiable' => $request->boolean('is_negotiable', true),
            'contact_phone' => $request->contact_phone ?? Auth::user()->phone,
            'contact_whatsapp' => $request->contact_whatsapp,
            'images' => $imagePaths,
            'status' => 'pending', // Requires admin approval
            'max_views' => 50,
        ]);

        // Queue email notification to admins
        AdminEmailQueueService::queueNewMarketplaceItem($item->load(['user', 'city']));

        return redirect()->route('marketplace.my-items')
            ->with('success', 'تم إنشاء الإعلان بنجاح وهو الآن قيد المراجعة من قبل الإدارة');
    }

    /**
     * Show user's items
     */
    public function myItems()
    {
        $items = MarketplaceItem::with(['category', 'city', 'activeSponsorship'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('marketplace.my-items', compact('items'));
    }

    /**
     * Show edit form
     */
    public function edit(MarketplaceItem $marketplace_item)
    {
        $item = $marketplace_item;

        if (!$item->isOwnedBy(Auth::user())) {
            abort(403, 'You do not have permission to edit this item');
        }

        $cities = City::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('marketplace.edit', compact('item', 'cities', 'categories'));
    }

    /**
     * Update item
     */
    public function update(Request $request, MarketplaceItem $marketplace_item)
    {
        $item = $marketplace_item;

        if (!$item->isOwnedBy(Auth::user())) {
            abort(403, 'You do not have permission to edit this item');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'price' => 'required|numeric|min:0',
            'city_id' => 'required|exists:cities,id',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|in:new,like_new,good,fair',
            'is_negotiable' => 'boolean',
            'contact_phone' => 'nullable|string|max:20',
            'contact_whatsapp' => 'nullable|string|max:20',
            'images.*' => 'image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'delete_images' => 'array',
            'delete_images.*' => 'integer',
        ]);

        // Update basic fields
        $item->update($request->only([
            'title',
            'description',
            'price',
            'city_id',
            'category_id',
            'condition',
            'is_negotiable',
            'contact_phone',
            'contact_whatsapp',
        ]));

        // Handle image deletion
        $currentImages = $item->images ?? [];
        if ($request->filled('delete_images')) {
            foreach ($request->delete_images as $index) {
                if (isset($currentImages[$index])) {
                    // Delete from storage
                    if (str_contains($currentImages[$index], 'storage/marketplace/')) {
                        $path = str_replace(url('storage/'), '', $currentImages[$index]);
                        Storage::disk('public')->delete($path);
                    }
                    unset($currentImages[$index]);
                }
            }
            $currentImages = array_values($currentImages); // Re-index array
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $newImages = [];
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('marketplace', $filename, 'public');
                $newImages[] = url('storage/' . $path);
            }
            $currentImages = array_merge($currentImages, $newImages);
        }

        // Validate total image count
        if (count($currentImages) < 1 || count($currentImages) > 5) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['images' => 'يجب أن يحتوي الإعلان على 1-5 صور']);
        }

        $item->update(['images' => $currentImages]);

        return redirect()->route('marketplace.my-items')
            ->with('success', 'تم تحديث الإعلان بنجاح');
    }

    /**
     * Delete item
     */
    public function destroy(MarketplaceItem $marketplace_item)
    {
        $item = $marketplace_item;

        if (!$item->isOwnedBy(Auth::user())) {
            abort(403, 'You do not have permission to delete this item');
        }

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

        return redirect()->route('marketplace.my-items')
            ->with('success', 'تم حذف الإعلان بنجاح');
    }

    /**
     * Mark item as sold
     */
    public function markAsSold(MarketplaceItem $marketplace_item)
    {
        $item = $marketplace_item;

        if (!$item->isOwnedBy(Auth::user())) {
            abort(403);
        }

        $item->markAsSold();

        return redirect()->back()
            ->with('success', 'تم وضع علامة "مباع" على الإعلان');
    }

    /**
     * Record contact attempt
     */
    public function recordContact(MarketplaceItem $marketplace_item)
    {
        $item = $marketplace_item;
        $item->incrementContactCount();

        return response()->json([
            'success' => true,
            'contact_phone' => $item->contact_phone,
            'contact_whatsapp' => $item->contact_whatsapp,
        ]);
    }

    /**
     * Show sponsorship packages
     */
    public function sponsorshipPackages(MarketplaceItem $marketplace_item)
    {
        $item = $marketplace_item;

        if (!$item->isOwnedBy(Auth::user())) {
            abort(403);
        }

        $packages = \App\Models\MarketplaceSponsorship::packages();

        return view('marketplace.sponsor', compact('item', 'packages'));
    }

    /**
     * Purchase sponsorship
     */
    public function purchaseSponsorship(Request $request, MarketplaceItem $marketplace_item)
    {
        $item = $marketplace_item;

        if (!$item->isOwnedBy(Auth::user())) {
            abort(403);
        }

        $request->validate([
            'package_type' => 'required|in:basic,standard,premium',
            'payment_method' => 'required|string|max:50',
        ]);

        $packages = \App\Models\MarketplaceSponsorship::packages();
        $selectedPackage = $packages[$request->package_type];

        $sponsorship = \App\Models\MarketplaceSponsorship::create([
            'marketplace_item_id' => $item->id,
            'user_id' => Auth::id(),
            'package_type' => $request->package_type,
            'duration_days' => $selectedPackage['duration_days'],
            'price_paid' => $selectedPackage['price'],
            'views_boost' => $selectedPackage['views_boost'],
            'priority_level' => $selectedPackage['priority_level'],
            'starts_at' => now(),
            'ends_at' => now()->addDays($selectedPackage['duration_days']),
            'payment_method' => $request->payment_method,
            'payment_status' => 'completed', // In production, wait for payment confirmation
            'status' => 'active',
        ]);

        $sponsorship->activate();

        return redirect()->route('marketplace.my-items')
            ->with('success', 'تم تفعيل الرعاية بنجاح! إعلانك الآن مميز ويظهر في أعلى النتائج');
    }

    /**
     * Generate QR code for an item
     */
    public function generateQrCode(MarketplaceItem $item)
    {
        $qrCode = $item->generateQrCode(300);

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=86400'); // Cache for 24 hours
    }

    /**
     * Download QR code for an item
     */
    public function downloadQrCode(MarketplaceItem $marketplace_item)
    {
        $item = $marketplace_item;
        // Check if user owns this item
        if (!Auth::check() || !$item->isOwnedBy(Auth::user())) {
            abort(403, 'غير مصرح لك بتحميل رمز QR لهذا الإعلان');
        }

        $qrCode = $item->generateQrCode(500); // Larger size for download

        $filename = 'qr-code-' . Str::slug($item->title) . '-' . $item->id . '.svg';

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
