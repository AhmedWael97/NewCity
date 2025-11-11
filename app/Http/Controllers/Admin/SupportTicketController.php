<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of support tickets
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'city', 'assignedTo']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Search by ticket number or subject
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tickets = $query->latest()->paginate(20);

        $stats = [
            'total_tickets' => SupportTicket::count(),
            'open_tickets' => SupportTicket::where('status', 'open')->count(),
            'in_progress_tickets' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved_tickets' => SupportTicket::where('status', 'resolved')->count(),
            'closed_tickets' => SupportTicket::where('status', 'closed')->count(),
            'avg_response_time' => $this->calculateAverageResponseTime()
        ];

        $cities = City::orderBy('name_ar')->get();
        $admins = User::where('user_type', 'admin')->get();

        return view('admin.tickets.index', compact('tickets', 'stats', 'cities', 'admins'));
    }

    /**
     * Display the specified ticket
     */
    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'city', 'assignedTo', 'replies.user']);
        
        // Mark as read if viewing for first time
        if (!$ticket->admin_read_at) {
            $ticket->update(['admin_read_at' => now()]);
        }

        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Update ticket status or assignment
     */
    public function update(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:open,in_progress,resolved,closed',
            'priority' => 'sometimes|in:low,normal,high,urgent',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
            'admin_notes' => 'sometimes|nullable|string'
        ]);

        // Track status changes
        $oldStatus = $ticket->status;
        
        $ticket->update($validated);

        // Log status change
        if (isset($validated['status']) && $oldStatus !== $validated['status']) {
            TicketReply::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'message' => "تم تغيير حالة التذكرة من {$oldStatus} إلى {$validated['status']}",
                'is_internal' => true
            ]);

            // Send notification to user if ticket is resolved/closed
            if (in_array($validated['status'], ['resolved', 'closed'])) {
                // Here you would send an email notification
                // Mail::to($ticket->user->email)->send(new TicketStatusChanged($ticket));
            }
        }

        return redirect()->back()->with('success', 'تم تحديث التذكرة بنجاح');
    }

    /**
     * Add a reply to the ticket
     */
    public function reply(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal' => 'boolean'
        ]);

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'is_internal' => $validated['is_internal'] ?? false
        ]);

        // Update ticket status to in_progress if it was open
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'in_progress']);
        }

        // Update last reply time
        $ticket->touch('updated_at');

        // Send email notification to user if not internal
        if (!$reply->is_internal) {
            // Mail::to($ticket->user->email)->send(new TicketReplied($ticket, $reply));
        }

        return redirect()->back()->with('success', 'تم إضافة الرد بنجاح');
    }

    /**
     * Assign ticket to admin
     */
    public function assign(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $ticket->update($validated);

        // Log assignment
        $admin = User::find($validated['assigned_to']);
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => "تم تعيين التذكرة للمشرف: {$admin->name}",
            'is_internal' => true
        ]);

        return redirect()->back()->with('success', 'تم تعيين التذكرة بنجاح');
    }

    /**
     * Bulk actions on tickets
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'tickets' => 'required|array',
            'tickets.*' => 'exists:support_tickets,id',
            'action' => 'required|in:assign,status_change,priority_change',
            'assigned_to' => 'required_if:action,assign|exists:users,id',
            'status' => 'required_if:action,status_change|in:open,in_progress,resolved,closed',
            'priority' => 'required_if:action,priority_change|in:low,normal,high,urgent'
        ]);

        $tickets = SupportTicket::whereIn('id', $validated['tickets']);

        switch ($validated['action']) {
            case 'assign':
                $tickets->update(['assigned_to' => $validated['assigned_to']]);
                $message = 'تم تعيين التذاكر المحددة بنجاح';
                break;
            
            case 'status_change':
                $tickets->update(['status' => $validated['status']]);
                $message = 'تم تحديث حالة التذاكر المحددة بنجاح';
                break;
            
            case 'priority_change':
                $tickets->update(['priority' => $validated['priority']]);
                $message = 'تم تحديث أولوية التذاكر المحددة بنجاح';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Show ticket analytics
     */
    public function analytics()
    {
        // Ticket volume over time (last 30 days)
        $ticketVolume = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subDays($i);
            $ticketVolume[] = [
                'date' => $date->format('Y-m-d'),
                'count' => SupportTicket::whereDate('created_at', $date)->count()
            ];
        }

        // Resolution time statistics
        $resolvedTickets = SupportTicket::where('status', 'resolved')
            ->whereNotNull('resolved_at')
            ->get();

        $avgResolutionTime = $resolvedTickets->avg(function($ticket) {
            return $ticket->created_at->diffInHours($ticket->resolved_at);
        });

        // Category distribution
        $categoryStats = SupportTicket::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->get();

        // Priority distribution
        $priorityStats = SupportTicket::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get();

        // City-wise ticket distribution
        $cityStats = SupportTicket::select('city_id', DB::raw('count(*) as count'))
            ->whereNotNull('city_id')
            ->groupBy('city_id')
            ->with('city')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Admin performance
        $adminPerformance = User::where('user_type', 'admin')
            ->withCount(['assignedTickets as total_assigned'])
            ->withCount(['assignedTickets as resolved_tickets' => function($query) {
                $query->where('status', 'resolved');
            }])
            ->get();

        return view('admin.tickets.analytics', compact(
            'ticketVolume',
            'avgResolutionTime',
            'categoryStats',
            'priorityStats',
            'cityStats',
            'adminPerformance'
        ));
    }

    /**
     * Calculate average response time
     */
    private function calculateAverageResponseTime()
    {
        $tickets = SupportTicket::whereHas('replies')
            ->with('replies')
            ->get();

        if ($tickets->isEmpty()) {
            return 0;
        }

        $totalTime = 0;
        $count = 0;

        foreach ($tickets as $ticket) {
            $firstReply = $ticket->replies->first();
            if ($firstReply) {
                $totalTime += $ticket->created_at->diffInHours($firstReply->created_at);
                $count++;
            }
        }

        return $count > 0 ? round($totalTime / $count, 2) : 0;
    }

    /**
     * Export tickets to CSV
     */
    public function export(Request $request)
    {
        $query = SupportTicket::with(['user', 'city', 'assignedTo']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $tickets = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="support_tickets_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($tickets) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'رقم التذكرة',
                'الموضوع',
                'الحالة',
                'الأولوية',
                'الفئة',
                'المستخدم',
                'المدينة',
                'تاريخ الإنشاء',
                'آخر تحديث'
            ]);

            // Data
            foreach ($tickets as $ticket) {
                fputcsv($file, [
                    $ticket->ticket_number,
                    $ticket->subject,
                    $ticket->status,
                    $ticket->priority,
                    $ticket->category,
                    $ticket->user->name ?? 'غير محدد',
                    $ticket->city->name_ar ?? 'غير محدد',
                    $ticket->created_at->format('Y-m-d H:i'),
                    $ticket->updated_at->format('Y-m-d H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}