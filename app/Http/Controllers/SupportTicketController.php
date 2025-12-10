<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupportTicketController extends Controller
{
    public function store(Request $request)
    {
        // Different validation rules based on authentication
        $rules = [
            'subject' => 'required|string|max:255',
            'category' => 'required|in:technical_issue,shop_complaint,payment_issue,account_problem,feature_request,bug_report,content_issue,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'description' => 'required|string|min:10',
            'city_id' => 'nullable|exists:cities,id',
            'shop_id' => 'nullable|exists:shops,id',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx'
        ];

        // Add guest fields validation if user is not authenticated
        if (!Auth::check()) {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_phone'] = 'required|string|max:20|regex:/^([0-9\s\-\+\(\)]*)$/';
            $rules['guest_email'] = 'nullable|email|max:255';
            $rules['guest_address'] = 'nullable|string|max:500';
        }

        $validated = $request->validate($rules);

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support_tickets', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ];
            }
        }

        // Create ticket
        $ticket = SupportTicket::create([
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'user_id' => Auth::id(),
            'guest_name' => $validated['guest_name'] ?? null,
            'guest_phone' => $validated['guest_phone'] ?? null,
            'guest_email' => $validated['guest_email'] ?? null,
            'guest_address' => $validated['guest_address'] ?? null,
            'city_id' => $validated['city_id'] ?? null,
            'shop_id' => $validated['shop_id'] ?? null,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'attachments' => !empty($attachments) ? $attachments : null,
            'status' => 'open'
        ]);

        // Return JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إرسال تذكرة الدعم بنجاح!',
                'ticket_number' => $ticket->ticket_number,
                'ticket' => $ticket
            ]);
        }

        return redirect()->route('contact')->with('success', 
            'تم إرسال تذكرة الدعم بنجاح! رقم التذكرة: ' . $ticket->ticket_number . 
            '. سيتم التواصل معك قريباً.');
    }
}
