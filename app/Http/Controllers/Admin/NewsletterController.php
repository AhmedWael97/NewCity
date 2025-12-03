<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Display newsletter subscribers
     */
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query();
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('email', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%");
            });
        }
        
        $subscribers = $query->latest('subscribed_at')->paginate(50);
        
        $stats = [
            'total' => NewsletterSubscriber::count(),
            'active' => NewsletterSubscriber::where('is_active', true)->count(),
            'inactive' => NewsletterSubscriber::where('is_active', false)->count(),
            'today' => NewsletterSubscriber::whereDate('subscribed_at', today())->count(),
            'this_week' => NewsletterSubscriber::whereBetween('subscribed_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => NewsletterSubscriber::whereMonth('subscribed_at', now()->month)->count(),
        ];
        
        return view('admin.newsletter.index', compact('subscribers', 'stats'));
    }
    
    /**
     * Export subscribers
     */
    public function export(Request $request)
    {
        $subscribers = NewsletterSubscriber::active()
            ->select('email', 'name', 'subscribed_at')
            ->get();
        
        $filename = 'newsletter-subscribers-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($subscribers) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, ['Email', 'Name', 'Subscribed Date']);
            
            // Data
            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->email,
                    $subscriber->name,
                    $subscriber->subscribed_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Delete subscriber
     */
    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        
        return redirect()->route('admin.newsletter.index')
            ->with('success', 'تم حذف المشترك بنجاح');
    }
}
