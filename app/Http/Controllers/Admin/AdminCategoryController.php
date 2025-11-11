<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $query = Category::withCount(['shops']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $categories = $query->paginate(15)->withQueryString();

        // Get parent categories for filter dropdown
        $parentCategories = Category::whereNull('parent_id')->get();

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category = new Category();
        $category->fill($request->except(['image']));
        
        // Set default sort order if not provided
        if (!$request->filled('sort_order')) {
            $category->sort_order = Category::max('sort_order') + 1;
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
            $category->image = $imagePath;
        }

        $category->save();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم إنشاء التصنيف بنجاح');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $category->load(['shops.city']);
        
        // Get category statistics
        $stats = [
            'total_shops' => $category->shops()->count(),
            'active_shops' => $category->shops()->where('status', 'active')->count(),
            'pending_shops' => $category->shops()->where('status', 'pending')->count(),
            'verified_shops' => $category->shops()->where('is_verified', true)->count(),
            'featured_shops' => $category->shops()->where('is_featured', true)->count(),
            'average_rating' => $category->shops()->withAvg('ratings', 'rating')->get()->avg('ratings_avg_rating') ?? 0,
        ];
        
        return view('admin.categories.show', compact('category', 'stats'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category->fill($request->except(['image']));
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            
            $imagePath = $request->file('image')->store('categories', 'public');
            $category->image = $imagePath;
        }

        $category->save();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم تحديث التصنيف بنجاح');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category has shops
        if ($category->shops()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'لا يمكن حذف التصنيف لأنه يحتوي على متاجر');
        }

        // Delete associated image
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح');
    }

    /**
     * Toggle category status (active/inactive).
     */
    public function toggleStatus(Category $category)
    {
        $newStatus = $category->status === 'active' ? 'inactive' : 'active';
        
        $category->update([
            'status' => $newStatus
        ]);

        $statusText = $newStatus === 'active' ? 'تم تفعيل التصنيف' : 'تم إلغاء تفعيل التصنيف';

        return redirect()
            ->back()
            ->with('success', $statusText);
    }

    /**
     * Toggle category featured status.
     */
    public function toggleFeatured(Category $category)
    {
        $category->update([
            'is_featured' => !$category->is_featured
        ]);

        $status = $category->is_featured ? 'تم إضافة التصنيف للمميزة' : 'تم إزالة التصنيف من المميزة';

        return redirect()
            ->back()
            ->with('success', $status);
    }

    /**
     * Update categories sort order.
     */
    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->categories as $categoryData) {
            Category::where('id', $categoryData['id'])
                   ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث ترتيب التصنيفات بنجاح'
        ]);
    }

    /**
     * Bulk actions for categories.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'action' => 'required|in:delete,activate,deactivate,feature,unfeature'
        ]);

        $categories = Category::whereIn('id', $request->categories);
        $count = $categories->count();

        switch ($request->action) {
            case 'delete':
                // Check if any category has shops
                foreach ($categories->get() as $category) {
                    if ($category->shops()->count() > 0) {
                        return redirect()
                            ->back()
                            ->with('error', 'لا يمكن حذف بعض التصنيفات لأنها تحتوي على متاجر');
                    }
                }
                
                // Delete associated images
                foreach ($categories->get() as $category) {
                    if ($category->image) {
                        Storage::disk('public')->delete($category->image);
                    }
                }
                $categories->delete();
                $message = "تم حذف {$count} تصنيف";
                break;
                
            case 'activate':
                $categories->update(['status' => 'active']);
                $message = "تم تفعيل {$count} تصنيف";
                break;
                
            case 'deactivate':
                $categories->update(['status' => 'inactive']);
                $message = "تم إلغاء تفعيل {$count} تصنيف";
                break;
                
            case 'feature':
                $categories->update(['is_featured' => true]);
                $message = "تم إضافة {$count} تصنيف للمميزة";
                break;
                
            case 'unfeature':
                $categories->update(['is_featured' => false]);
                $message = "تم إزالة {$count} تصنيف من المميزة";
                break;
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Get categories statistics for dashboard.
     */
    public function getStats()
    {
        $stats = [
            'total' => Category::count(),
            'active' => Category::where('status', 'active')->count(),
            'inactive' => Category::where('status', 'inactive')->count(),
            'featured' => Category::where('is_featured', true)->count(),
            'with_shops' => Category::has('shops')->count(),
            'without_shops' => Category::doesntHave('shops')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get category tree for hierarchical display.
     */
    public function getTree()
    {
        $categories = Category::where('status', 'active')
                            ->orderBy('sort_order')
                            ->withCount('shops')
                            ->get();

        return response()->json($categories);
    }
}