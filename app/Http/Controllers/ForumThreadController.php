<?php

namespace App\Http\Controllers;

use App\Models\ForumThread;
use App\Models\ForumPost;
use App\Models\ForumSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumThreadController extends Controller
{
    /**
     * Display a thread with its posts
     */
    public function show(ForumThread $thread)
    {
        // Increment view count
        $thread->incrementViews();

        // Load relationships
        $thread->load(['category', 'user', 'city', 'lastPostUser']);

        // Get posts with pagination
        $posts = $thread->posts()
            ->with(['user', 'votes'])
            ->active()
            ->topLevel()
            ->orderBy('created_at')
            ->paginate(20);

        // Check if user is subscribed
        $isSubscribed = false;
        if (Auth::check()) {
            $isSubscribed = $thread->isSubscribedBy(Auth::id());
        }

        return view('forum.thread', compact('thread', 'posts', 'isSubscribed'));
    }

    /**
     * Store a new post/reply
     */
    public function storePost(Request $request, ForumThread $thread)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول للرد على المواضيع');
        }

        if ($thread->is_locked) {
            return back()->with('error', 'هذا الموضوع مغلق ولا يمكن الرد عليه');
        }

        $request->validate([
            'body' => 'required|string|min:5|max:5000',
            'parent_id' => 'nullable|exists:forum_posts,id',
        ]);

        $post = $thread->posts()->create([
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'body' => $request->body,
            'is_approved' => !$thread->category->requires_approval,
            'status' => $thread->category->requires_approval ? 'pending' : 'active',
            'approved_at' => $thread->category->requires_approval ? null : now(),
        ]);

        // Subscribe user to thread automatically
        if (!$thread->isSubscribedBy(Auth::id())) {
            ForumSubscription::create([
                'user_id' => Auth::id(),
                'forum_thread_id' => $thread->id,
            ]);
        }

        if ($thread->category->requires_approval) {
            return back()->with('success', 'تم إضافة ردك بنجاح وهو قيد المراجعة');
        }

        return back()->with('success', 'تم إضافة ردك بنجاح');
    }

    /**
     * Subscribe to thread
     */
    public function subscribe(ForumThread $thread)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'يجب تسجيل الدخول'], 401);
        }

        $subscription = ForumSubscription::firstOrCreate([
            'user_id' => Auth::id(),
            'forum_thread_id' => $thread->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم الاشتراك في الموضوع بنجاح',
            'subscribed' => true,
        ]);
    }

    /**
     * Unsubscribe from thread
     */
    public function unsubscribe(ForumThread $thread)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'يجب تسجيل الدخول'], 401);
        }

        ForumSubscription::where('user_id', Auth::id())
            ->where('forum_thread_id', $thread->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الاشتراك بنجاح',
            'subscribed' => false,
        ]);
    }

    /**
     * Edit thread
     */
    public function edit(ForumThread $thread)
    {
        if (!Auth::check() || !$thread->isOwnedBy(Auth::id())) {
            abort(403, 'غير مصرح لك بتعديل هذا الموضوع');
        }

        return view('forum.edit-thread', compact('thread'));
    }

    /**
     * Update thread
     */
    public function update(Request $request, ForumThread $thread)
    {
        if (!Auth::check() || !$thread->isOwnedBy(Auth::id())) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|min:10',
        ]);

        $thread->update([
            'title' => $request->title,
            'body' => $request->body,
            'status' => 'edited',
        ]);

        return redirect()->route('forum.thread', $thread)
            ->with('success', 'تم تحديث الموضوع بنجاح');
    }

    /**
     * Delete thread
     */
    public function destroy(ForumThread $thread)
    {
        if (!Auth::check() || !$thread->isOwnedBy(Auth::id())) {
            abort(403);
        }

        $category = $thread->category;
        $thread->delete();

        return redirect()->route('forum.category', $category)
            ->with('success', 'تم حذف الموضوع بنجاح');
    }
}
