<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display feedback list
     */
    public function index(Request $request)
    {
        $query = Feedback::with('user');
        
        // Filter by rating
        if ($request->has('rating') && $request->rating !== '') {
            $query->where('rating', $request->rating);
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('message', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('page_url', 'LIKE', "%{$search}%");
            });
        }
        
        // Sort
        $sortBy = $request->get('sort', 'latest');
        if ($sortBy === 'oldest') {
            $query->oldest('submitted_at');
        } elseif ($sortBy === 'highest') {
            $query->orderBy('rating', 'desc');
        } elseif ($sortBy === 'lowest') {
            $query->orderBy('rating', 'asc');
        } else {
            $query->latest('submitted_at');
        }
        
        $feedbacks = $query->paginate(50);
        
        $stats = [
            'total' => Feedback::count(),
            'average_rating' => round(Feedback::avg('rating'), 2),
            'positive' => Feedback::positive()->count(),
            'negative' => Feedback::negative()->count(),
            'today' => Feedback::whereDate('submitted_at', today())->count(),
            'this_week' => Feedback::whereBetween('submitted_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'rating_distribution' => [
                5 => Feedback::where('rating', 5)->count(),
                4 => Feedback::where('rating', 4)->count(),
                3 => Feedback::where('rating', 3)->count(),
                2 => Feedback::where('rating', 2)->count(),
                1 => Feedback::where('rating', 1)->count(),
            ]
        ];
        
        return view('admin.feedback.index', compact('feedbacks', 'stats'));
    }
    
    /**
     * Show single feedback
     */
    public function show(Feedback $feedback)
    {
        $feedback->load('user');
        return view('admin.feedback.show', compact('feedback'));
    }
    
    /**
     * Delete feedback
     */
    public function destroy(Feedback $feedback)
    {
        $feedback->delete();
        
        return redirect()->route('admin.feedback.index')
            ->with('success', 'تم حذف التقييم بنجاح');
    }
}
