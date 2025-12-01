<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\City;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    /**
     * Display forum categories
     */
    public function index(Request $request)
    {
        $cityId = session('selected_city_id');
        
        $categories = ForumCategory::with(['latestThread.user', 'city'])
            ->active()
            ->when($cityId, function($query) use ($cityId) {
                $query->inCity($cityId);
            })
            ->ordered()
            ->get();

        $cities = City::where('is_active', true)->orderBy('name')->get();

        return view('forum.index', compact('categories', 'cities'));
    }

    /**
     * Display threads in a category
     */
    public function category(ForumCategory $category, Request $request)
    {
        $threads = $category->threads()
            ->with(['user', 'lastPostUser', 'city'])
            ->active()
            ->when($request->filled('sort'), function($query) use ($request) {
                switch($request->sort) {
                    case 'latest':
                        $query->recent();
                        break;
                    case 'popular':
                        $query->orderByDesc('views_count');
                        break;
                    case 'replies':
                        $query->orderByDesc('replies_count');
                        break;
                    default:
                        $query->recent();
                }
            }, function($query) {
                $query->orderByDesc('is_pinned')->recent();
            })
            ->paginate(20);

        return view('forum.category', compact('category', 'threads'));
    }

    /**
     * Show create thread form
     */
    public function createThread(ForumCategory $category)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول لإنشاء موضوع جديد');
        }

        $cities = City::where('is_active', true)->orderBy('name')->get();

        return view('forum.create-thread', compact('category', 'cities'));
    }

    /**
     * Store a new thread
     */
    public function storeThread(Request $request, ForumCategory $category)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:10',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        $thread = $category->threads()->create([
            'user_id' => auth()->id(),
            'city_id' => $request->city_id ?? session('selected_city_id'),
            'title' => $request->title,
            'body' => $request->body,
            'is_approved' => !$category->requires_approval,
            'status' => $category->requires_approval ? 'pending' : 'active',
            'approved_at' => $category->requires_approval ? null : now(),
        ]);

        if ($category->requires_approval) {
            return redirect()->route('forum.category', $category)
                ->with('success', 'تم إنشاء الموضوع بنجاح وهو قيد المراجعة من قبل الإدارة');
        }

        return redirect()->route('forum.thread', $thread)
            ->with('success', 'تم إنشاء الموضوع بنجاح');
    }
}
