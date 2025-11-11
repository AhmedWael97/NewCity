<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\City;
use App\Models\Category;
use App\Services\AdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdvertisementController extends Controller
{
    protected $adService;

    public function __construct(AdService $adService)
    {
        $this->adService = $adService;
    }

    public function index(Request $request)
    {
        $query = Advertisement::with(['city']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        $advertisements = $query->orderBy('created_at', 'desc')->paginate(15);
        $cities = City::all();
        
        // Get statistics
        $stats = $this->adService->getAdStatistics();
        
        return view('admin.advertisements.index', compact('advertisements', 'cities', 'stats'));
    }

    public function create()
    {
        $cities = City::all();
        $categories = Category::all();
        $pricingTiers = Advertisement::getPricingTiers();
        
        return view('admin.advertisements.create', compact('cities', 'categories', 'pricingTiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'click_url' => 'required|url',
            'type' => 'required|in:banner,hero,sponsored_listing,sidebar',
            'scope' => 'required|in:global,city_specific',
            'city_id' => 'nullable|exists:cities,id',
            'target_categories' => 'nullable|array',
            'target_categories.*' => 'exists:categories,id',
            'pricing_model' => 'required|in:cpm,cpc,cpa',
            'price_amount' => 'required|numeric|min:0.01',
            'budget_limit' => 'nullable|numeric|min:0',
            'daily_budget_limit' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,paused,pending_review,rejected,completed',
        ]);

        // Handle city requirement for city_specific scope
        if ($validated['scope'] === 'city_specific' && !$validated['city_id']) {
            return back()->withErrors(['city_id' => 'City is required for city-specific advertisements.']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'ad_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('advertisements', $imageName, 'public');
            $validated['image_path'] = $imagePath;
        }

        Advertisement::create($validated);

        return redirect()->route('admin.advertisements.index')
                        ->with('success', 'Advertisement created successfully.');
    }

    public function show(Advertisement $advertisement)
    {
        $advertisement->load(['city']);
        
        // Get performance metrics
        $metrics = [
            'ctr' => $advertisement->ctr,
            'conversion_rate' => $advertisement->conversion_rate,
            'cost_per_click' => $advertisement->cost_per_click,
            'cost_per_conversion' => $advertisement->cost_per_conversion,
        ];
        
        return view('admin.advertisements.show', compact('advertisement', 'metrics'));
    }

    public function edit(Advertisement $advertisement)
    {
        $cities = City::all();
        $categories = Category::all();
        $pricingTiers = Advertisement::getPricingTiers();
        
        return view('admin.advertisements.edit', compact('advertisement', 'cities', 'categories', 'pricingTiers'));
    }

    public function update(Request $request, Advertisement $advertisement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'click_url' => 'required|url',
            'type' => 'required|in:banner,hero,sponsored_listing,sidebar',
            'scope' => 'required|in:global,city_specific',
            'city_id' => 'nullable|exists:cities,id',
            'target_categories' => 'nullable|array',
            'target_categories.*' => 'exists:categories,id',
            'pricing_model' => 'required|in:cpm,cpc,cpa',
            'price_amount' => 'required|numeric|min:0.01',
            'budget_limit' => 'nullable|numeric|min:0',
            'daily_budget_limit' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,paused,pending_review,rejected,completed',
        ]);

        // Handle city requirement for city_specific scope
        if ($validated['scope'] === 'city_specific' && !$validated['city_id']) {
            return back()->withErrors(['city_id' => 'City is required for city-specific advertisements.']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($advertisement->image_path) {
                Storage::disk('public')->delete($advertisement->image_path);
            }
            
            $image = $request->file('image');
            $imageName = 'ad_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('advertisements', $imageName, 'public');
            $validated['image_path'] = $imagePath;
        }

        $advertisement->update($validated);

        return redirect()->route('admin.advertisements.index')
                        ->with('success', 'Advertisement updated successfully.');
    }

    public function destroy(Advertisement $advertisement)
    {
        // Delete image if exists
        if ($advertisement->image_path) {
            Storage::disk('public')->delete($advertisement->image_path);
        }
        
        $advertisement->delete();

        return redirect()->route('admin.advertisements.index')
                        ->with('success', 'Advertisement deleted successfully.');
    }

    public function approve(Advertisement $advertisement)
    {
        $advertisement->update(['status' => 'active']);
        
        return redirect()->route('admin.advertisements.index')
                        ->with('success', 'Advertisement approved and activated.');
    }

    public function reject(Advertisement $advertisement)
    {
        $advertisement->update(['status' => 'rejected']);
        
        return redirect()->route('admin.advertisements.index')
                        ->with('success', 'Advertisement rejected.');
    }

    public function pause(Advertisement $advertisement)
    {
        $advertisement->update(['status' => 'paused']);
        
        return redirect()->route('admin.advertisements.index')
                        ->with('success', 'Advertisement paused.');
    }

    public function activate(Advertisement $advertisement)
    {
        $advertisement->update(['status' => 'active']);
        
        return redirect()->route('admin.advertisements.index')
                        ->with('success', 'Advertisement activated.');
    }

    public function analytics(Request $request)
    {
        $cityId = $request->get('city_id');
        $dateRange = $request->get('date_range', '30'); // Default 30 days
        
        $startDate = now()->subDays($dateRange)->startOfDay();
        $endDate = now()->endOfDay();
        
        $report = $this->adService->getRevenueReport(
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
            $cityId
        );
        
        $cities = City::all();
        
        return view('admin.advertisements.analytics', compact('report', 'cities', 'cityId', 'dateRange'));
    }

    public function recordImpression(Request $request)
    {
        $validated = $request->validate([
            'ad_id' => 'required|exists:advertisements,id'
        ]);
        
        $this->adService->recordImpression($validated['ad_id']);
        
        return response()->json(['success' => true]);
    }

    public function recordClick(Request $request)
    {
        $validated = $request->validate([
            'ad_id' => 'required|exists:advertisements,id'
        ]);
        
        $clickUrl = $this->adService->recordClick($validated['ad_id']);
        
        return response()->json([
            'success' => true,
            'redirect_url' => $clickUrl
        ]);
    }
}