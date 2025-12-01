<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumPostVote;
use App\Models\ForumReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumPostController extends Controller
{
    /**
     * Edit post
     */
    public function edit(ForumPost $post)
    {
        if (!Auth::check() || !$post->isOwnedBy(Auth::id())) {
            abort(403, 'غير مصرح لك بتعديل هذا الرد');
        }

        return view('forum.edit-post', compact('post'));
    }

    /**
     * Update post
     */
    public function update(Request $request, ForumPost $post)
    {
        if (!Auth::check() || !$post->isOwnedBy(Auth::id())) {
            abort(403);
        }

        $request->validate([
            'body' => 'required|string|min:5|max:5000',
        ]);

        $post->update([
            'body' => $request->body,
            'status' => 'edited',
        ]);

        return redirect()->route('forum.thread', $post->thread)
            ->with('success', 'تم تحديث الرد بنجاح');
    }

    /**
     * Delete post
     */
    public function destroy(ForumPost $post)
    {
        if (!Auth::check() || !$post->isOwnedBy(Auth::id())) {
            abort(403);
        }

        $thread = $post->thread;
        $post->delete();

        return redirect()->route('forum.thread', $thread)
            ->with('success', 'تم حذف الرد بنجاح');
    }

    /**
     * Vote post as helpful
     */
    public function vote(ForumPost $post)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'يجب تسجيل الدخول'], 401);
        }

        // Check if already voted
        $existingVote = ForumPostVote::where('forum_post_id', $post->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingVote) {
            // Remove vote
            $existingVote->delete();
            
            return response()->json([
                'success' => true,
                'voted' => false,
                'count' => $post->helpful_count,
                'message' => 'تم إلغاء التصويت',
            ]);
        }

        // Add vote
        ForumPostVote::create([
            'forum_post_id' => $post->id,
            'user_id' => Auth::id(),
            'is_helpful' => true,
        ]);

        return response()->json([
            'success' => true,
            'voted' => true,
            'count' => $post->helpful_count,
            'message' => 'شكراً لتصويتك',
        ]);
    }

    /**
     * Report post
     */
    public function report(Request $request, ForumPost $post)
    {
        if (!Auth::check()) {
            return back()->with('error', 'يجب تسجيل الدخول للإبلاغ');
        }

        $request->validate([
            'reason' => 'required|in:spam,inappropriate,offensive,off_topic,duplicate,other',
            'description' => 'nullable|string|max:500',
        ]);

        // Check if already reported by this user
        $existingReport = ForumReport::where('reportable_type', ForumPost::class)
            ->where('reportable_id', $post->id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($existingReport) {
            return back()->with('error', 'لقد قمت بالإبلاغ عن هذا المحتوى مسبقاً');
        }

        ForumReport::create([
            'user_id' => Auth::id(),
            'reportable_type' => ForumPost::class,
            'reportable_id' => $post->id,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return back()->with('success', 'تم إرسال البلاغ بنجاح. سيتم مراجعته من قبل الإدارة');
    }
}
