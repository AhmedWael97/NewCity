<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories
     */
    public function index()
    {
        // Get main categories (no parent) with their children and shop counts
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        // Add shops count for each category
        $categories->each(function ($category) {
            $category->shops_count = $category->activeShops()->count();
        });

        return view('categories.index', compact('categories'));
    }

    /**
     * Display the specified category and its shops
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->firstOrFail();

        // Get all cities for filter
        $cities = City::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Build shops query
        $query = $category->activeShops()
            ->with(['city', 'category']);

        // Apply filters
        if (request()->has('q') && !empty(request('q'))) {
            $search = request('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if (request()->has('city') && !empty(request('city'))) {
            $query->whereHas('city', function ($q) {
                $q->where('slug', request('city'));
            });
        }

        if (request()->has('rating') && !empty(request('rating'))) {
            $query->where('rating', '>=', request('rating'));
        }

        // Apply sorting
        $sortBy = request('sort', 'rating');
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name');
                break;
            case 'rating':
                $query->orderByDesc('rating');
                break;
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            default:
                $query->orderByDesc('is_featured')
                      ->orderByDesc('rating');
        }

        $shops = $query->paginate(20)->withQueryString();

        return view('categories.show', compact('category', 'shops', 'cities'));
    }
}
