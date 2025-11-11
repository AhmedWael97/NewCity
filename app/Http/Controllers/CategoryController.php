<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Shop;
use App\Models\City;

class CategoryController extends Controller
{
    /**
     * Display all categories
     */
    public function index()
    {
        $categories = Category::active()
            ->roots()
            ->withChildren()
            ->withCount(['shops' => function($query) {
                $query->where('is_active', true);
            }])
            ->ordered()
            ->get();

        $seoData = [
            'title' => 'جميع فئات المتاجر - اكتشف المدن',
            'description' => 'استعرض جميع فئات المتاجر والخدمات المتاحة في مصر',
            'keywords' => 'فئات المتاجر، أنواع المتاجر، تصنيفات، دليل المتاجر',
            'canonical' => route('categories.index'),
        ];

        return view('categories.index', compact('categories', 'seoData'));
    }

    /**
     * Display shops for a specific category
     */
    public function show(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $query = Shop::where('is_active', true)
                     ->with(['category', 'city']);

        // Include shops from this category and its children
        if ($category->hasChildren()) {
            $categoryIds = $category->children()->pluck('id')->push($category->id);
            $query->whereHas('category', function($q) use ($categoryIds) {
                $q->whereIn('id', $categoryIds);
            });
        } else {
            $query->where('category_id', $category->id);
        }

        // City filter
        if ($request->filled('city')) {
            $citySlug = $request->input('city');
            $query->whereHas('city', function($q) use ($citySlug) {
                $q->where('slug', $citySlug);
            });
        }

        // Search filter
        if ($request->filled('q')) {
            $searchTerm = $request->input('q');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Rating filter
        if ($request->filled('rating')) {
            $rating = $request->input('rating');
            $query->where('rating', '>=', $rating);
        }

        $shops = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Get cities for filter
        $cities = City::active()->orderBy('name')->get();

        $seoData = [
            'title' => $category->name . ' - اكتشف المدن',
            'description' => "استعرض أفضل متاجر {$category->name} في مصر",
            'keywords' => $category->name . ', متاجر, مصر, ' . $category->description,
            'canonical' => route('category.shops', $category->slug),
        ];

        return view('categories.show', compact('category', 'shops', 'cities', 'seoData'));
    }
}
