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
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:technical_issue,shop_complaint,payment_issue,account_problem,feature_request,bug_report,content_issue,other',
            'priority' => 'required|in:low,medium,high,urgent',
            'description' => 'required|string',
            'city_id' => 'nullable|exists:cities,id',
            'shop_id' => 'nullable|exists:shops,id',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx'
        ]);

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
            'user_id' => Auth::id(),
            'city_id' => $validated['city_id'] ?? null,
            'shop_id' => $validated['shop_id'] ?? null,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'attachments' => !empty($attachments) ? $attachments : null,
            'status' => 'open'
        ]);

        return redirect()->route('contact')->with('success', 
            'تم إرسال تذكرة الدعم بنجاح! رقم التذكرة: ' . $ticket->ticket_number . 
            '. سيتم التواصل معك قريباً.');
    }
}
