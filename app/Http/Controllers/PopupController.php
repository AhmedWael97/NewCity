<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber;
use App\Models\Feedback;

class PopupController extends Controller
{
    /**
     * Subscribe to newsletter
     */
    public function subscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email',
            'name' => 'nullable|string|max:255',
        ]);

        $subscriber = NewsletterSubscriber::create([
            'email' => $request->email,
            'name' => $request->name,
            'subscribed_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم الاشتراك بنجاح! سنرسل لك أحدث العروض والمتاجر'
        ]);
    }

    /**
     * Submit feedback
     */
    public function submitFeedback(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'nullable|string|max:1000',
            'email' => 'nullable|email',
            'page_url' => 'required|string',
        ]);

        $feedback = Feedback::create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'message' => $request->message,
            'email' => $request->email,
            'page_url' => $request->page_url,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'شكراً لك! تقييمك يساعدنا على التحسين'
        ]);
    }

    /**
     * Track popup interaction
     */
    public function trackPopupInteraction(Request $request)
    {
        $request->validate([
            'popup_type' => 'required|string',
            'action' => 'required|string', // shown, closed, clicked, converted
        ]);

        // Log to database or analytics
        \Log::info('Popup Interaction', [
            'type' => $request->popup_type,
            'action' => $request->action,
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
        ]);

        return response()->json(['success' => true]);
    }
}
