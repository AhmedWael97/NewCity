<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopSuggestion;
use Illuminate\Http\Request;

class AdminShopSuggestionController extends Controller
{
    public function index(Request $request)
    {
        $query = ShopSuggestion::with(['city', 'category', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('shop_name', 'like', "%{$search}%")
                  ->orWhere('suggested_by_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $suggestions = $query->paginate(20);

        // Get counts by status
        $statusCounts = [
            'all' => ShopSuggestion::count(),
            'pending' => ShopSuggestion::where('status', 'pending')->count(),
            'approved' => ShopSuggestion::where('status', 'approved')->count(),
            'rejected' => ShopSuggestion::where('status', 'rejected')->count(),
            'completed' => ShopSuggestion::where('status', 'completed')->count(),
        ];

        return view('admin.shop-suggestions.index', compact('suggestions', 'statusCounts'));
    }

    public function show(ShopSuggestion $suggestion)
    {
        $suggestion->load(['city', 'category', 'user', 'reviewer']);
        return view('admin.shop-suggestions.show', compact('suggestion'));
    }

    public function updateStatus(Request $request, ShopSuggestion $suggestion)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $suggestion->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $suggestion->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->guard('web')->id()
        ]);

        return redirect()
            ->back()
            ->with('success', 'تم تحديث حالة الاقتراح بنجاح');
    }

    public function destroy(ShopSuggestion $suggestion)
    {
        $suggestion->delete();

        return redirect()
            ->route('admin.shop-suggestions.index')
            ->with('success', 'تم حذف الاقتراح بنجاح');
    }
}
