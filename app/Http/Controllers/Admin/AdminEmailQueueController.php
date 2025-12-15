<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminEmailQueue;
use App\Models\AdminEmailPreference;
use Illuminate\Http\Request;

class AdminEmailQueueController extends Controller
{
    /**
     * Display email queue
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $eventType = $request->get('event_type', 'all');

        $query = AdminEmailQueue::query()->orderBy('created_at', 'desc');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($eventType !== 'all') {
            $query->where('event_type', $eventType);
        }

        $emails = $query->paginate(20);

        $stats = [
            'pending' => AdminEmailQueue::where('status', AdminEmailQueue::STATUS_PENDING)->count(),
            'sent' => AdminEmailQueue::where('status', AdminEmailQueue::STATUS_SENT)->count(),
            'failed' => AdminEmailQueue::where('status', AdminEmailQueue::STATUS_FAILED)->count(),
        ];

        return view('admin.email-queue.index', compact('emails', 'stats', 'status', 'eventType'));
    }

    /**
     * Show email details
     */
    public function show($id)
    {
        $email = AdminEmailQueue::findOrFail($id);
        return view('admin.email-queue.show', compact('email'));
    }

    /**
     * Retry failed email
     */
    public function retry($id)
    {
        $email = AdminEmailQueue::findOrFail($id);
        
        if ($email->status === AdminEmailQueue::STATUS_FAILED) {
            $email->update([
                'status' => AdminEmailQueue::STATUS_PENDING,
                'error_message' => null,
            ]);

            return redirect()->back()->with('success', 'Email has been queued for retry.');
        }

        return redirect()->back()->with('error', 'Only failed emails can be retried.');
    }

    /**
     * Delete email from queue
     */
    public function destroy($id)
    {
        $email = AdminEmailQueue::findOrFail($id);
        $email->delete();

        return redirect()->route('admin.email-queue.index')
            ->with('success', 'Email deleted from queue.');
    }

    /**
     * Clear all sent emails
     */
    public function clearSent()
    {
        AdminEmailQueue::where('status', AdminEmailQueue::STATUS_SENT)->delete();

        return redirect()->route('admin.email-queue.index')
            ->with('success', 'All sent emails have been cleared.');
    }

    /**
     * Show email preferences
     */
    public function preferences()
    {
        $user = auth()->user();
        $preference = AdminEmailPreference::firstOrCreate(
            ['user_id' => $user->id],
            [
                'shop_suggestion' => true,
                'city_suggestion' => true,
                'shop_rate' => true,
                'service_rate' => true,
                'new_service' => true,
                'new_marketplace' => true,
                'new_user' => true,
            ]
        );

        return view('admin.email-queue.preferences', compact('preference'));
    }

    /**
     * Update email preferences
     */
    public function updatePreferences(Request $request)
    {
        $user = auth()->user();
        $preference = AdminEmailPreference::firstOrCreate(['user_id' => $user->id]);

        $preference->update([
            'shop_suggestion' => $request->has('shop_suggestion'),
            'city_suggestion' => $request->has('city_suggestion'),
            'shop_rate' => $request->has('shop_rate'),
            'service_rate' => $request->has('service_rate'),
            'new_service' => $request->has('new_service'),
            'new_marketplace' => $request->has('new_marketplace'),
            'new_user' => $request->has('new_user'),
        ]);

        return redirect()->route('admin.email-queue.preferences')
            ->with('success', 'Email preferences updated successfully!');
    }
}
