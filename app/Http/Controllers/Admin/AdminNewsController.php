<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\City;
use App\Models\PushNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminNewsController extends Controller
{
    public function index()
    {
        $news = News::with(['category', 'city'])
            ->latest('created_at')
            ->paginate(20);

        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        $categories = NewsCategory::where('is_active', true)->orderBy('order')->get();
        $cities = City::orderBy('name')->get();
        
        return view('admin.news.create', compact('categories', 'cities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug',
            'description' => 'required|string|max:500',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:news_categories,id',
            'city_id' => 'nullable|exists:cities,id',
            'is_active' => 'nullable|boolean',
            'published_at' => 'nullable|date',
            'send_notification' => 'nullable|boolean',
        ]);

        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('news/thumbnails', 'public');
        }

        // Handle multiple images upload
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('news/images', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $sendNotification = $request->has('send_notification') ? true : false;
        
        if (!isset($validated['published_at']) && $validated['is_active']) {
            $validated['published_at'] = now();
        }

        $news = News::create($validated);

        // Send notification if checkbox is checked
        if ($sendNotification && $validated['is_active']) {
            $this->sendNewsNotification($news);
        }

        return redirect()->route('admin.news.index')
            ->with('success', 'News article created successfully.');
    }

    public function edit(News $news)
    {
        $categories = NewsCategory::where('is_active', true)->orderBy('order')->get();
        $cities = City::orderBy('name')->get();
        
        return view('admin.news.edit', compact('news', 'categories', 'cities'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug,' . $news->id,
            'description' => 'required|string|max:500',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:news_categories,id',
            'city_id' => 'nullable|exists:cities,id',
            'is_active' => 'nullable|boolean',
            'published_at' => 'nullable|date',
            'remove_thumbnail' => 'nullable|boolean',
            'remove_images' => 'nullable|array',
            'send_notification' => 'nullable|boolean',
        ]);

        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle thumbnail removal
        if ($request->has('remove_thumbnail') && $news->thumbnail) {
            Storage::disk('public')->delete($news->thumbnail);
            $validated['thumbnail'] = null;
        }

        // Handle new thumbnail upload
        if ($request->hasFile('thumbnail')) {
            if ($news->thumbnail) {
                Storage::disk('public')->delete($news->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('news/thumbnails', 'public');
        }

        // Handle image removal
        if ($request->has('remove_images') && $news->images) {
            $removeImages = $request->input('remove_images');
            $currentImages = $news->images;
            
            foreach ($removeImages as $imageToRemove) {
                if (in_array($imageToRemove, $currentImages)) {
                    Storage::disk('public')->delete($imageToRemove);
                    $currentImages = array_diff($currentImages, [$imageToRemove]);
                }
            }
            
            $validated['images'] = array_values($currentImages);
        } else {
            $validated['images'] = $news->images;
        }

        // Handle new images upload
        if ($request->hasFile('images')) {
            $imagePaths = $validated['images'] ?? [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('news/images', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $sendNotification = $request->has('send_notification') ? true : false;
        
        if (!isset($validated['published_at']) && $validated['is_active'] && !$news->published_at) {
            $validated['published_at'] = now();
        }

        $news->update($validated);

        // Send notification if checkbox is checked
        if ($sendNotification && $validated['is_active']) {
            $this->sendNewsNotification($news);
        }

        return redirect()->route('admin.news.index')
            ->with('success', 'News article updated successfully.');
    }

    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'News article deleted successfully.');
    }

    public function categories()
    {
        $categories = NewsCategory::withCount('news')->orderBy('order')->paginate(20);
        
        return view('admin.news.categories', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.news.create-category');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news_categories,slug',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? 0;

        NewsCategory::create($validated);

        return redirect()->route('admin.news.categories')
            ->with('success', 'Category created successfully.');
    }

    public function editCategory(NewsCategory $category)
    {
        return view('admin.news.edit-category', compact('category'));
    }

    public function updateCategory(Request $request, NewsCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news_categories,slug,' . $category->id,
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        if (!isset($validated['slug']) || empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('admin.news.categories')
            ->with('success', 'Category updated successfully.');
    }

    public function destroyCategory(NewsCategory $category)
    {
        $category->delete();

        return redirect()->route('admin.news.categories')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Send notification to all users about new news
     */
    protected function sendNewsNotification(News $news)
    {
        try {
            // Create push notification
            $notification = PushNotification::create([
                'title' => $news->title,
                'body' => $news->description,
                'target' => 'all',
                'data' => [
                    'type' => 'news',
                    'news_id' => $news->id,
                    'news_slug' => $news->slug,
                ],
                'action_url' => route('news.show', $news->slug),
                'scheduled_at' => now(),
                'status' => 'pending',
            ]);

            // Send notification immediately
            $notificationService = app(NotificationService::class);
            $result = $notificationService->sendPushNotification($notification);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send news notification: ' . $e->getMessage());
            return false;
        }
    }
}
