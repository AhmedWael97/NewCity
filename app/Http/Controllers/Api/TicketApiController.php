<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Support Tickets",
 *     description="Support ticket management endpoints"
 * )
 */
class TicketApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/tickets",
     *     summary="Get user's tickets",
     *     tags={"Support Tickets"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"open", "in_progress", "waiting_user", "resolved", "closed"})
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"low", "medium", "high", "urgent"})
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="List of tickets"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = SupportTicket::where('user_id', $user->id)
            ->with(['city:id,name', 'shop:id,name', 'assignedAdmin:id,name'])
            ->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        $tickets = $query->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $tickets->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'subject' => $ticket->subject,
                    'description' => $ticket->description,
                    'category' => $ticket->category,
                    'category_label' => $this->getCategoryLabel($ticket->category),
                    'priority' => $ticket->priority,
                    'priority_label' => $this->getPriorityLabel($ticket->priority),
                    'status' => $ticket->status,
                    'status_label' => $this->getStatusLabel($ticket->status),
                    'city' => $ticket->city ? [
                        'id' => $ticket->city->id,
                        'name' => $ticket->city->name
                    ] : null,
                    'shop' => $ticket->shop ? [
                        'id' => $ticket->shop->id,
                        'name' => $ticket->shop->name
                    ] : null,
                    'assigned_admin' => $ticket->assignedAdmin ? [
                        'id' => $ticket->assignedAdmin->id,
                        'name' => $ticket->assignedAdmin->name
                    ] : null,
                    'attachments_count' => is_array($ticket->attachments) ? count($ticket->attachments) : 0,
                    'replies_count' => $ticket->replies()->count(),
                    'unread_replies_count' => $ticket->replies()
                        ->where('is_admin_reply', true)
                        ->whereNull('read_at')
                        ->count(),
                    'created_at' => $ticket->created_at->toIso8601String(),
                    'updated_at' => $ticket->updated_at->toIso8601String(),
                    'created_at_human' => $ticket->created_at->diffForHumans(),
                ];
            }),
            'pagination' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tickets",
     *     summary="Create a new support ticket (authenticated or guest)",
     *     description="Create a support ticket. For authenticated users, user_id is automatically set. For guests, guest fields (guest_name, guest_phone) are required.",
     *     tags={"Support Tickets"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"subject", "category", "priority", "description"},
     *                 @OA\Property(property="subject", type="string", maxLength=255, example="Unable to login to my account"),
     *                 @OA\Property(property="category", type="string", enum={"technical_issue", "shop_complaint", "payment_issue", "account_problem", "feature_request", "bug_report", "content_issue", "other"}, example="technical_issue"),
     *                 @OA\Property(property="priority", type="string", enum={"low", "medium", "high", "urgent"}, example="medium"),
     *                 @OA\Property(property="description", type="string", example="I am unable to login to my account. The error message says invalid credentials."),
     *                 @OA\Property(property="city_id", type="integer", nullable=true, example=1),
     *                 @OA\Property(property="shop_id", type="integer", nullable=true, example=5),
     *                 @OA\Property(property="guest_name", type="string", maxLength=255, nullable=true, description="Required for non-authenticated users", example="أحمد محمد"),
     *                 @OA\Property(property="guest_phone", type="string", maxLength=20, nullable=true, description="Required for non-authenticated users", example="01012345678"),
     *                 @OA\Property(property="guest_email", type="string", maxLength=255, nullable=true, description="Optional for non-authenticated users", example="guest@example.com"),
     *                 @OA\Property(property="guest_address", type="string", maxLength=500, nullable=true, description="Optional for non-authenticated users", example="القاهرة، مصر الجديدة"),
     *                 @OA\Property(property="attachments[]", type="array", @OA\Items(type="string", format="binary"), description="Optional attachments (max 5 files, 10MB each)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ticket created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ticket created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="ticket_number", type="string", example="TKT-20250101-0001"),
     *                 @OA\Property(property="subject", type="string"),
     *                 @OA\Property(property="status", type="string", example="open"),
     *                 @OA\Property(property="guest_name", type="string", nullable=true),
     *                 @OA\Property(property="guest_phone", type="string", nullable=true),
     *                 @OA\Property(property="guest_email", type="string", nullable=true),
     *                 @OA\Property(property="guest_address", type="string", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
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

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support_tickets', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'url' => Storage::url($path),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ];
            }
        }

        // Generate ticket number
        $ticketNumber = SupportTicket::generateTicketNumber();

        // Create ticket
        $ticket = SupportTicket::create([
            'ticket_number' => $ticketNumber,
            'user_id' => Auth::id(),
            'guest_name' => $request->guest_name,
            'guest_phone' => $request->guest_phone,
            'guest_email' => $request->guest_email,
            'guest_address' => $request->guest_address,
            'city_id' => $request->city_id,
            'shop_id' => $request->shop_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => $request->priority,
            'attachments' => !empty($attachments) ? $attachments : null,
            'status' => 'open'
        ]);

        // Load relationships
        $ticket->load(['city:id,name', 'shop:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Ticket created successfully',
            'data' => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'description' => $ticket->description,
                'category' => $ticket->category,
                'category_label' => $this->getCategoryLabel($ticket->category),
                'priority' => $ticket->priority,
                'priority_label' => $this->getPriorityLabel($ticket->priority),
                'status' => $ticket->status,
                'status_label' => $this->getStatusLabel($ticket->status),
                'city' => $ticket->city ? [
                    'id' => $ticket->city->id,
                    'name' => $ticket->city->name
                ] : null,
                'shop' => $ticket->shop ? [
                    'id' => $ticket->shop->id,
                    'name' => $ticket->shop->name
                ] : null,
                'guest_name' => $ticket->guest_name,
                'guest_phone' => $ticket->guest_phone,
                'guest_email' => $ticket->guest_email,
                'guest_address' => $ticket->guest_address,
                'attachments' => $ticket->attachments,
                'created_at' => $ticket->created_at->toIso8601String(),
                'created_at_human' => $ticket->created_at->diffForHumans(),
            ]
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tickets/{id}",
     *     summary="Get ticket details",
     *     description="Get detailed information about a specific ticket including guest fields if submitted by a guest",
     *     tags={"Support Tickets"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ticket details",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="ticket_number", type="string"),
     *                 @OA\Property(property="subject", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="guest_name", type="string", nullable=true, description="Guest name if ticket was submitted by a guest"),
     *                 @OA\Property(property="guest_phone", type="string", nullable=true, description="Guest phone if ticket was submitted by a guest"),
     *                 @OA\Property(property="guest_email", type="string", nullable=true, description="Guest email if provided"),
     *                 @OA\Property(property="guest_address", type="string", nullable=true, description="Guest address if provided"),
     *                 @OA\Property(property="replies", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Forbidden - User doesn't own this ticket"),
     *     @OA\Response(response=404, description="Ticket not found")
     * )
     */
    public function show($id)
    {
        $ticket = SupportTicket::with([
            'city:id,name',
            'shop:id,name',
            'assignedAdmin:id,name,email',
            'replies' => function ($query) {
                $query->where('is_internal_note', false)
                    ->with('user:id,name,email')
                    ->orderBy('created_at', 'asc');
            }
        ])->find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        // Check authorization
        if ($ticket->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this ticket'
            ], 403);
        }

        // Mark admin replies as read
        $ticket->replies()
            ->where('is_admin_reply', true)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'description' => $ticket->description,
                'category' => $ticket->category,
                'category_label' => $this->getCategoryLabel($ticket->category),
                'priority' => $ticket->priority,
                'priority_label' => $this->getPriorityLabel($ticket->priority),
                'status' => $ticket->status,
                'status_label' => $this->getStatusLabel($ticket->status),
                'city' => $ticket->city ? [
                    'id' => $ticket->city->id,
                    'name' => $ticket->city->name
                ] : null,
                'shop' => $ticket->shop ? [
                    'id' => $ticket->shop->id,
                    'name' => $ticket->shop->name
                ] : null,
                'assigned_admin' => $ticket->assignedAdmin ? [
                    'id' => $ticket->assignedAdmin->id,
                    'name' => $ticket->assignedAdmin->name,
                    'email' => $ticket->assignedAdmin->email
                ] : null,
                'guest_name' => $ticket->guest_name,
                'guest_phone' => $ticket->guest_phone,
                'guest_email' => $ticket->guest_email,
                'guest_address' => $ticket->guest_address,
                'attachments' => $ticket->attachments,
                'replies' => $ticket->replies->map(function ($reply) {
                    return [
                        'id' => $reply->id,
                        'message' => $reply->message,
                        'is_admin_reply' => $reply->is_admin_reply,
                        'attachments' => $reply->attachments,
                        'user' => [
                            'id' => $reply->user->id,
                            'name' => $reply->user->name,
                            'email' => $reply->user->email
                        ],
                        'read_at' => $reply->read_at ? $reply->read_at->toIso8601String() : null,
                        'created_at' => $reply->created_at->toIso8601String(),
                        'created_at_human' => $reply->created_at->diffForHumans(),
                    ];
                }),
                'resolved_at' => $ticket->resolved_at ? $ticket->resolved_at->toIso8601String() : null,
                'closed_at' => $ticket->closed_at ? $ticket->closed_at->toIso8601String() : null,
                'resolution_notes' => $ticket->resolution_notes,
                'satisfaction_rating' => $ticket->satisfaction_rating,
                'satisfaction_feedback' => $ticket->satisfaction_feedback,
                'created_at' => $ticket->created_at->toIso8601String(),
                'updated_at' => $ticket->updated_at->toIso8601String(),
                'created_at_human' => $ticket->created_at->diffForHumans(),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tickets/{id}/reply",
     *     summary="Reply to a ticket",
     *     tags={"Support Tickets"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"message"},
     *                 @OA\Property(property="message", type="string"),
     *                 @OA\Property(property="attachments[]", type="array", @OA\Items(type="string", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Reply added successfully"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Ticket not found")
     * )
     */
    public function reply(Request $request, $id)
    {
        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        // Check authorization
        if ($ticket->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this ticket'
            ], 403);
        }

        // Check if ticket is closed
        if ($ticket->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot reply to a closed ticket'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('ticket_replies', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'url' => Storage::url($path),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ];
            }
        }

        // Create reply
        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachments' => !empty($attachments) ? $attachments : null,
            'is_admin_reply' => false,
            'is_internal_note' => false
        ]);

        // Update ticket status if it was waiting for user
        if ($ticket->status === 'waiting_user') {
            $ticket->update(['status' => 'in_progress']);
        }

        // Load user relationship
        $reply->load('user:id,name,email');

        return response()->json([
            'success' => true,
            'message' => 'Reply added successfully',
            'data' => [
                'id' => $reply->id,
                'message' => $reply->message,
                'is_admin_reply' => $reply->is_admin_reply,
                'attachments' => $reply->attachments,
                'user' => [
                    'id' => $reply->user->id,
                    'name' => $reply->user->name,
                    'email' => $reply->user->email
                ],
                'created_at' => $reply->created_at->toIso8601String(),
                'created_at_human' => $reply->created_at->diffForHumans(),
            ]
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tickets/{id}/rate",
     *     summary="Rate a resolved/closed ticket",
     *     tags={"Support Tickets"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rating"},
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5),
     *             @OA\Property(property="feedback", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Rating submitted successfully"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Ticket not found")
     * )
     */
    public function rate(Request $request, $id)
    {
        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        // Check authorization
        if ($ticket->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this ticket'
            ], 403);
        }

        // Check if ticket is resolved or closed
        if (!in_array($ticket->status, ['resolved', 'closed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Can only rate resolved or closed tickets'
            ], 400);
        }

        // Check if already rated
        if ($ticket->satisfaction_rating !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket has already been rated'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $ticket->update([
            'satisfaction_rating' => $request->rating,
            'satisfaction_feedback' => $request->feedback
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!',
            'data' => [
                'ticket_number' => $ticket->ticket_number,
                'rating' => $ticket->satisfaction_rating,
                'feedback' => $ticket->satisfaction_feedback
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tickets/statistics",
     *     summary="Get user's ticket statistics",
     *     tags={"Support Tickets"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Ticket statistics")
     * )
     */
    public function statistics()
    {
        $user = Auth::user();
        
        $stats = [
            'total' => SupportTicket::where('user_id', $user->id)->count(),
            'open' => SupportTicket::where('user_id', $user->id)->where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'waiting_user' => SupportTicket::where('user_id', $user->id)->where('status', 'waiting_user')->count(),
            'resolved' => SupportTicket::where('user_id', $user->id)->where('status', 'resolved')->count(),
            'closed' => SupportTicket::where('user_id', $user->id)->where('status', 'closed')->count(),
            'unread_replies' => TicketReply::whereHas('ticket', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('is_admin_reply', true)->whereNull('read_at')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tickets/categories",
     *     summary="Get ticket categories with labels",
     *     tags={"Support Tickets"},
     *     @OA\Response(response=200, description="List of ticket categories")
     * )
     */
    public function categories()
    {
        $categories = [
            ['value' => 'technical_issue', 'label_en' => 'Technical Issue', 'label_ar' => 'مشكلة تقنية'],
            ['value' => 'shop_complaint', 'label_en' => 'Shop Complaint', 'label_ar' => 'شكوى متجر'],
            ['value' => 'payment_issue', 'label_en' => 'Payment Issue', 'label_ar' => 'مشكلة دفع'],
            ['value' => 'account_problem', 'label_en' => 'Account Problem', 'label_ar' => 'مشكلة حساب'],
            ['value' => 'feature_request', 'label_en' => 'Feature Request', 'label_ar' => 'طلب ميزة'],
            ['value' => 'bug_report', 'label_en' => 'Bug Report', 'label_ar' => 'بلاغ خطأ'],
            ['value' => 'content_issue', 'label_en' => 'Content Issue', 'label_ar' => 'مشكلة محتوى'],
            ['value' => 'other', 'label_en' => 'Other', 'label_ar' => 'أخرى'],
        ];

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get category label in Arabic
     */
    private function getCategoryLabel($category)
    {
        $labels = [
            'technical_issue' => 'مشكلة تقنية',
            'shop_complaint' => 'شكوى متجر',
            'payment_issue' => 'مشكلة دفع',
            'account_problem' => 'مشكلة حساب',
            'feature_request' => 'طلب ميزة',
            'bug_report' => 'بلاغ خطأ',
            'content_issue' => 'مشكلة محتوى',
            'other' => 'أخرى',
        ];

        return $labels[$category] ?? $category;
    }

    /**
     * Get priority label in Arabic
     */
    private function getPriorityLabel($priority)
    {
        $labels = [
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
            'urgent' => 'عاجلة',
        ];

        return $labels[$priority] ?? $priority;
    }

    /**
     * Get status label in Arabic
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'open' => 'مفتوح',
            'in_progress' => 'قيد المعالجة',
            'waiting_user' => 'في انتظار المستخدم',
            'resolved' => 'تم الحل',
            'closed' => 'مغلق',
        ];

        return $labels[$status] ?? $status;
    }
}
