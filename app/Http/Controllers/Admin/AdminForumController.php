<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumPost;
use App\Models\ForumReport;
use Illuminate\Http\Request;

class AdminForumController extends Controller
{
    /**
     * Display forum management dashboard
     */
    public function index()
    {
        $stats = [
            'total_categories' => ForumCategory::count(),
            'total_threads' => ForumThread::count(),
            'total_posts' => ForumPost::count(),
            'pending_threads' => ForumThread::pending()->count(),
            'pending_posts' => ForumPost::pending()->count(),
            'pending_reports' => ForumReport::pending()->count(),
        ];

        $categories = ForumCategory::withCount(['threads'])->ordered()->get();

        return view('admin.forum.index', compact('stats', 'categories'));
    }

    /**
     * Categories management
     */
    public function categories()
    {
        $categories = ForumCategory::with('city')->ordered()->get();
        return view('admin.forum.categories', compact('categories'));
    }

    public function createCategory()
    {
        $cities = \App\Models\City::where('is_active', true)->orderBy('name')->get();
        return view('admin.forum.create-category', compact('cities'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer',
            'requires_approval' => 'boolean',
        ]);

        ForumCategory::create($request->all());

        return redirect()->route('admin.forum.categories')
            ->with('success', 'تم إنشاء التصنيف بنجاح');
    }

    public function editCategory(ForumCategory $category)
    {
        $cities = \App\Models\City::where('is_active', true)->orderBy('name')->get();
        return view('admin.forum.edit-category', compact('category', 'cities'));
    }

    public function updateCategory(Request $request, ForumCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
            'requires_approval' => 'boolean',
        ]);

        $category->update($request->all());

        return redirect()->route('admin.forum.categories')
            ->with('success', 'تم تحديث التصنيف بنجاح');
    }

    public function destroyCategory(ForumCategory $category)
    {
        if ($category->threads()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف تصنيف يحتوي على مواضيع');
        }

        $category->delete();

        return redirect()->route('admin.forum.categories')
            ->with('success', 'تم حذف التصنيف بنجاح');
    }

    /**
     * Moderation queue
     */
    public function moderation(Request $request)
    {
        $type = $request->get('type', 'threads');

        if ($type === 'threads') {
            $items = ForumThread::with(['user', 'category', 'city'])
                ->pending()
                ->orderByDesc('created_at')
                ->paginate(20);
        } else {
            $items = ForumPost::with(['user', 'thread.category'])
                ->pending()
                ->orderByDesc('created_at')
                ->paginate(20);
        }

        return view('admin.forum.moderation', compact('items', 'type'));
    }

    public function approveThread(ForumThread $thread)
    {
        $thread->approve();
        return back()->with('success', 'تم الموافقة على الموضوع');
    }

    public function rejectThread(ForumThread $thread)
    {
        $thread->reject();
        return back()->with('success', 'تم رفض الموضوع');
    }

    public function approvePost(ForumPost $post)
    {
        $post->approve();
        return back()->with('success', 'تم الموافقة على الرد');
    }

    public function rejectPost(ForumPost $post)
    {
        $post->reject();
        return back()->with('success', 'تم رفض الرد');
    }

    /**
     * Reports management
     */
    public function reports(Request $request)
    {
        $status = $request->get('status', 'pending');

        $reports = ForumReport::with(['user', 'reportable', 'reviewer'])
            ->when($status === 'pending', fn($q) => $q->pending())
            ->when($status !== 'pending', fn($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.forum.reports', compact('reports', 'status'));
    }

    public function showReport(ForumReport $report)
    {
        $report->load(['user', 'reportable', 'reviewer']);
        return view('admin.forum.show-report', compact('report'));
    }

    public function resolveReport(Request $request, ForumReport $report)
    {
        $request->validate([
            'action' => 'required|in:resolve,dismiss',
            'notes' => 'nullable|string',
        ]);

        if ($request->action === 'resolve') {
            $report->resolve(auth()->id(), $request->notes);
        } else {
            $report->dismiss(auth()->id(), $request->notes);
        }

        return back()->with('success', 'تم معالجة البلاغ بنجاح');
    }

    /**
     * Threads management
     */
    public function threads(Request $request)
    {
        $threads = ForumThread::with(['user', 'category', 'city'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('category'), fn($q) => $q->where('forum_category_id', $request->category))
            ->orderByDesc('created_at')
            ->paginate(20);

        $categories = ForumCategory::ordered()->get();

        return view('admin.forum.threads', compact('threads', 'categories'));
    }

    public function togglePinThread(ForumThread $thread)
    {
        $thread->togglePin();
        return back()->with('success', $thread->is_pinned ? 'تم تثبيت الموضوع' : 'تم إلغاء تثبيت الموضوع');
    }

    public function toggleLockThread(ForumThread $thread)
    {
        $thread->toggleLock();
        return back()->with('success', $thread->is_locked ? 'تم إغلاق الموضوع' : 'تم فتح الموضوع');
    }

    public function deleteThread(ForumThread $thread)
    {
        $thread->delete();
        return back()->with('success', 'تم حذف الموضوع بنجاح');
    }
}
