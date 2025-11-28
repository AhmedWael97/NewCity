<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::with(['category', 'city'])
            ->active()
            ->latest();

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by city
        if ($request->has('city')) {
            $query->byCity($request->city);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $news = $query->paginate(12);
        $categories = NewsCategory::where('is_active', true)->withCount('activeNews')->orderBy('order')->get();

        return view('news.index', compact('news', 'categories'));
    }

    public function show($slug)
    {
        $news = News::with(['category', 'city'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        // Increment views
        $news->incrementViews();

        // Get related news
        $relatedNews = News::with(['category'])
            ->active()
            ->where('id', '!=', $news->id)
            ->when($news->category_id, function ($query) use ($news) {
                $query->where('category_id', $news->category_id);
            })
            ->latest()
            ->limit(3)
            ->get();

        return view('news.show', compact('news', 'relatedNews'));
    }
}
