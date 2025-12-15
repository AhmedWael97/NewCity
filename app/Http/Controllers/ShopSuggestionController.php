<?php

namespace App\Http\Controllers;

use App\Models\ShopSuggestion;
use App\Models\City;
use App\Models\Category;
use App\Services\AdminEmailQueueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopSuggestionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'category_id' => 'nullable|exists:categories,id',
            'shop_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'google_maps_url' => 'nullable|url|max:2000',
            'website' => 'nullable|url|max:500',
            'facebook' => 'nullable|url|max:500',
            'instagram' => 'nullable|url|max:500',
            'opening_hours' => 'nullable|string|max:500',
            'suggested_by_name' => 'required|string|max:255',
            'suggested_by_phone' => 'required|string|max:20',
            'suggested_by_email' => 'nullable|email|max:255',
        ]);

        // Prepare social media data
        $socialMedia = [];
        if ($request->facebook) {
            $socialMedia['facebook'] = $request->facebook;
        }
        if ($request->instagram) {
            $socialMedia['instagram'] = $request->instagram;
        }

        // Create the suggestion
        $suggestion = ShopSuggestion::create([
            'user_id' => Auth::id(),
            'city_id' => $validated['city_id'],
            'category_id' => $validated['category_id'] ?? null,
            'shop_name' => $validated['shop_name'],
            'description' => $validated['description'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'whatsapp' => $validated['whatsapp'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'google_maps_url' => $validated['google_maps_url'] ?? null,
            'website' => $validated['website'] ?? null,
            'social_media' => !empty($socialMedia) ? $socialMedia : null,
            'opening_hours' => $validated['opening_hours'] ?? null,
            'suggested_by_name' => $validated['suggested_by_name'],
            'suggested_by_phone' => $validated['suggested_by_phone'],
            'suggested_by_email' => $validated['suggested_by_email'] ?? null,
            'status' => 'pending',
        ]);

        // Queue email notification to admins
        AdminEmailQueueService::queueShopSuggestion($suggestion);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'شكراً لاقتراحك! سيتم مراجعته من قبل الإدارة قريباً.',
                'suggestion_id' => $suggestion->id
            ]);
        }

        return redirect()->back()->with('success', 'شكراً لاقتراحك! سيتم مراجعته من قبل الإدارة قريباً.');
    }

    public function getCitiesAndCategories()
    {
        $cities = City::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id', 'name', 'icon']);

        return response()->json([
            'cities' => $cities,
            'categories' => $categories
        ]);
    }
}
