<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CitySuggestion;
use Illuminate\Http\Request;

class AdminCitySuggestionController extends Controller
{
    /**
     * Display a listing of city suggestions
     */
    public function index(Request $request)
    {
        $query = CitySuggestion::query()->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('city_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('group_url', 'like', "%{$search}%");
            });
        }

        $suggestions = $query->paginate(20);

        $stats = [
            'total' => CitySuggestion::count(),
            'pending' => CitySuggestion::where('status', 'pending')->count(),
            'approved' => CitySuggestion::where('status', 'approved')->count(),
            'rejected' => CitySuggestion::where('status', 'rejected')->count(),
        ];

        return view('admin.city-suggestions.index', compact('suggestions', 'stats'));
    }

    /**
     * Display the specified city suggestion
     */
    public function show(CitySuggestion $suggestion)
    {
        return view('admin.city-suggestions.show', compact('suggestion'));
    }

    /**
     * Update the status of a city suggestion
     */
    public function updateStatus(Request $request, CitySuggestion $suggestion)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $suggestion->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()
            ->route('admin.city-suggestions.index')
            ->with('success', 'تم تحديث حالة الاقتراح بنجاح');
    }

    /**
     * Remove the specified city suggestion
     */
    public function destroy(CitySuggestion $suggestion)
    {
        $suggestion->delete();

        return redirect()
            ->route('admin.city-suggestions.index')
            ->with('success', 'تم حذف الاقتراح بنجاح');
    }
}
