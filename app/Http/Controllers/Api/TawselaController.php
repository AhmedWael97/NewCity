<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TawselaRide;
use App\Models\TawselaRequest;
use App\Models\TawselaMessage;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Tawsela",
 *     description="نظام توصيلة - مشاركة الرحلات"
 * )
 */
class TawselaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/tawsela/rides",
     *     summary="Get all available rides",
     *     tags={"Tawsela"},
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="Filter by city",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="start_lat",
     *         in="query",
     *         description="Start location latitude",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="start_lng",
     *         in="query",
     *         description="Start location longitude",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="dest_lat",
     *         in="query",
     *         description="Destination latitude",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="dest_lng",
     *         in="query",
     *         description="Destination longitude",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="max_distance",
     *         in="query",
     *         description="Maximum distance in km",
     *         @OA\Schema(type="number", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rides retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = TawselaRide::query()
            ->with(['user:id,name,phone,avatar', 'city:id,name'])
            ->active()
            ->upcoming();

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Search by location proximity
        if ($request->filled('start_lat') && $request->filled('start_lng')) {
            $maxDistance = $request->get('max_distance', 10);
            
            $query->selectRaw("
                *,
                (
                    6371 * acos(
                        cos(radians(?)) * cos(radians(start_latitude)) *
                        cos(radians(start_longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(start_latitude))
                    )
                ) AS start_distance
            ", [
                $request->start_lat,
                $request->start_lng,
                $request->start_lat
            ])
            ->having('start_distance', '<=', $maxDistance);
        }

        // Search by destination proximity
        if ($request->filled('dest_lat') && $request->filled('dest_lng')) {
            $maxDistance = $request->get('max_distance', 10);
            
            $query->selectRaw("
                *,
                (
                    6371 * acos(
                        cos(radians(?)) * cos(radians(destination_latitude)) *
                        cos(radians(destination_longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(destination_latitude))
                    )
                ) AS dest_distance
            ", [
                $request->dest_lat,
                $request->dest_lng,
                $request->dest_lat
            ])
            ->having('dest_distance', '<=', $maxDistance);
        }

        // Order by departure time
        $query->orderBy('departure_time', 'asc');

        $rides = $query->paginate(20);

        // Add remaining seats to each ride
        $rides->getCollection()->transform(function ($ride) {
            $ride->remaining_seats = $ride->getRemainingSeats();
            return $ride;
        });

        return response()->json([
            'success' => true,
            'data' => $rides
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tawsela/rides/{id}",
     *     summary="Get ride details",
     *     tags={"Tawsela"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ride details retrieved successfully"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $isAuthenticated = Auth::check();
        
        if ($isAuthenticated) {
            // Full details for authenticated users
            $ride = TawselaRide::with([
                'user:id,name,phone,avatar',
                'city:id,name',
                'requests' => function($query) {
                    $query->where('status', 'accepted')
                          ->with('user:id,name,avatar');
                }
            ])->findOrFail($id);
        } else {
            // Limited details for guests
            $ride = TawselaRide::with([
                'city:id,name'
            ])->findOrFail($id);
            
            // Remove sensitive data
            $ride->makeHidden(['user_id', 'car_model', 'car_year', 'car_color', 'notes', 'views']);
        }

        // Increment views
        $ride->incrementViews();

        $ride->remaining_seats = $ride->getRemainingSeats();
        $ride->is_authenticated = $isAuthenticated;

        return response()->json([
            'success' => true,
            'data' => $ride
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tawsela/rides",
     *     summary="Create a new ride",
     *     tags={"Tawsela"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="Ride created successfully"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required|exists:cities,id',
            'car_model' => 'required|string|max:255',
            'car_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'car_color' => 'required|string|max:50',
            'available_seats' => 'required|integer|min:1|max:10',
            'start_latitude' => 'required|numeric|between:-90,90',
            'start_longitude' => 'required|numeric|between:-180,180',
            'start_address' => 'required|string|max:500',
            'destination_latitude' => 'required|numeric|between:-90,90',
            'destination_longitude' => 'required|numeric|between:-180,180',
            'destination_address' => 'required|string|max:500',
            'stop_points' => 'nullable|array',
            'stop_points.*.latitude' => 'required|numeric|between:-90,90',
            'stop_points.*.longitude' => 'required|numeric|between:-180,180',
            'stop_points.*.address' => 'required|string|max:500',
            'price' => 'required|numeric|min:0',
            'price_type' => 'required|in:fixed,negotiable',
            'price_unit' => 'required|in:per_person,per_trip',
            'departure_time' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
        ], [
            'city_id.required' => 'يجب اختيار المدينة',
            'car_model.required' => 'يجب إدخال موديل السيارة',
            'car_year.required' => 'يجب إدخال سنة السيارة',
            'car_color.required' => 'يجب إدخال لون السيارة',
            'available_seats.required' => 'يجب تحديد عدد المقاعد المتاحة',
            'available_seats.min' => 'يجب أن يكون عدد المقاعد على الأقل 1',
            'start_latitude.required' => 'يجب تحديد نقطة البداية',
            'start_address.required' => 'يجب إدخال عنوان البداية',
            'destination_latitude.required' => 'يجب تحديد الوجهة',
            'destination_address.required' => 'يجب إدخال عنوان الوجهة',
            'price.required' => 'يجب تحديد السعر',
            'price_type.required' => 'يجب تحديد نوع السعر',
            'price_unit.required' => 'يجب تحديد وحدة السعر',
            'departure_time.required' => 'يجب تحديد وقت المغادرة',
            'departure_time.after' => 'يجب أن يكون وقت المغادرة في المستقبل',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $ride = TawselaRide::create([
            'user_id' => Auth::id(),
            'city_id' => $request->city_id,
            'car_model' => $request->car_model,
            'car_year' => $request->car_year,
            'car_color' => $request->car_color,
            'available_seats' => $request->available_seats,
            'start_latitude' => $request->start_latitude,
            'start_longitude' => $request->start_longitude,
            'start_address' => $request->start_address,
            'destination_latitude' => $request->destination_latitude,
            'destination_longitude' => $request->destination_longitude,
            'destination_address' => $request->destination_address,
            'stop_points' => $request->stop_points,
            'price' => $request->price,
            'price_type' => $request->price_type,
            'price_unit' => $request->price_unit,
            'departure_time' => $request->departure_time,
            'notes' => $request->notes,
            'status' => 'active',
        ]);

        $ride->load(['user:id,name,phone,avatar', 'city:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الرحلة بنجاح',
            'data' => $ride
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/tawsela/rides/{id}",
     *     summary="Update a ride",
     *     tags={"Tawsela"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ride updated successfully"
     *     )
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $ride = TawselaRide::findOrFail($id);

        // Check if user owns this ride
        if ($ride->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بتعديل هذه الرحلة'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'car_model' => 'sometimes|string|max:255',
            'car_year' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
            'car_color' => 'sometimes|string|max:50',
            'available_seats' => 'sometimes|integer|min:1|max:10',
            'price' => 'sometimes|numeric|min:0',
            'price_type' => 'sometimes|in:fixed,negotiable',
            'price_unit' => 'sometimes|in:per_person,per_trip',
            'departure_time' => 'sometimes|date|after:now',
            'notes' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:active,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        $ride->update($request->only([
            'car_model', 'car_year', 'car_color', 'available_seats',
            'price', 'price_type', 'price_unit', 'departure_time',
            'notes', 'status'
        ]));

        $ride->load(['user:id,name,phone,avatar', 'city:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الرحلة بنجاح',
            'data' => $ride
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/tawsela/rides/{id}",
     *     summary="Delete a ride",
     *     tags={"Tawsela"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ride deleted successfully"
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $ride = TawselaRide::findOrFail($id);

        // Check if user owns this ride
        if ($ride->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بحذف هذه الرحلة'
            ], 403);
        }

        $ride->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الرحلة بنجاح'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tawsela/my-rides",
     *     summary="Get current user's rides",
     *     tags={"Tawsela"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Rides retrieved successfully"
     *     )
     * )
     */
    public function myRides(): JsonResponse
    {
        $rides = TawselaRide::with(['city:id,name', 'requests'])
            ->where('user_id', Auth::id())
            ->orderBy('departure_time', 'desc')
            ->paginate(20);

        $rides->getCollection()->transform(function ($ride) {
            $ride->remaining_seats = $ride->getRemainingSeats();
            return $ride;
        });

        return response()->json([
            'success' => true,
            'data' => $rides
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tawsela/rides/{id}/request",
     *     summary="Request to join a ride",
     *     tags={"Tawsela"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Request sent successfully"
     *     )
     * )
     */
    public function requestRide(Request $request, $id): JsonResponse
    {
        $ride = TawselaRide::findOrFail($id);

        // Check if user owns this ride
        if ($ride->user_id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك طلب الانضمام لرحلتك الخاصة'
            ], 422);
        }

        // Check if user already requested this ride
        $existingRequest = TawselaRequest::where('ride_id', $id)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'لديك طلب قائم بالفعل على هذه الرحلة'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'pickup_latitude' => 'required|numeric|between:-90,90',
            'pickup_longitude' => 'required|numeric|between:-180,180',
            'pickup_address' => 'required|string|max:500',
            'dropoff_latitude' => 'nullable|numeric|between:-90,90',
            'dropoff_longitude' => 'nullable|numeric|between:-180,180',
            'dropoff_address' => 'nullable|string|max:500',
            'passengers_count' => 'required|integer|min:1',
            'offered_price' => 'nullable|numeric|min:0',
            'message' => 'required|string|max:1000',
        ], [
            'pickup_latitude.required' => 'يجب تحديد نقطة الصعود',
            'pickup_address.required' => 'يجب إدخال عنوان نقطة الصعود',
            'passengers_count.required' => 'يجب تحديد عدد الركاب',
            'message.required' => 'يجب إدخال رسالة',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if ride has enough seats
        if ($ride->getRemainingSeats() < $request->passengers_count) {
            return response()->json([
                'success' => false,
                'message' => 'عدد المقاعد المتاحة غير كافٍ'
            ], 422);
        }

        $rideRequest = TawselaRequest::create([
            'ride_id' => $id,
            'user_id' => Auth::id(),
            'pickup_latitude' => $request->pickup_latitude,
            'pickup_longitude' => $request->pickup_longitude,
            'pickup_address' => $request->pickup_address,
            'dropoff_latitude' => $request->dropoff_latitude,
            'dropoff_longitude' => $request->dropoff_longitude,
            'dropoff_address' => $request->dropoff_address,
            'passengers_count' => $request->passengers_count,
            'offered_price' => $request->offered_price,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        $rideRequest->load(['user:id,name,phone,avatar']);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال طلبك بنجاح',
            'data' => $rideRequest
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tawsela/my-requests",
     *     summary="Get current user's ride requests",
     *     tags={"Tawsela"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Requests retrieved successfully"
     *     )
     * )
     */
    public function myRequests(): JsonResponse
    {
        $requests = TawselaRequest::with([
            'ride' => function($query) {
                $query->with('user:id,name,phone,avatar');
            }
        ])
        ->where('user_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tawsela/rides/{id}/requests",
     *     summary="Get requests for a ride",
     *     tags={"Tawsela"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Requests retrieved successfully"
     *     )
     * )
     */
    public function rideRequests($id): JsonResponse
    {
        $ride = TawselaRide::findOrFail($id);

        // Check if user owns this ride
        if ($ride->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بعرض طلبات هذه الرحلة'
            ], 403);
        }

        $requests = $ride->requests()
            ->with('user:id,name,phone,avatar')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tawsela/requests/{id}/accept",
     *     summary="Accept a ride request",
     *     tags={"Tawsela"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request accepted successfully"
     *     )
     * )
     */
    public function acceptRequest($id): JsonResponse
    {
        $request = TawselaRequest::with('ride')->findOrFail($id);

        // Check if user owns the ride
        if ($request->ride->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بقبول هذا الطلب'
            ], 403);
        }

        // Check if ride has enough seats
        if ($request->ride->getRemainingSeats() < $request->passengers_count) {
            return response()->json([
                'success' => false,
                'message' => 'عدد المقاعد المتاحة غير كافٍ'
            ], 422);
        }

        $request->accept();

        return response()->json([
            'success' => true,
            'message' => 'تم قبول الطلب بنجاح'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tawsela/requests/{id}/reject",
     *     summary="Reject a ride request",
     *     tags={"Tawsela"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request rejected successfully"
     *     )
     * )
     */
    public function rejectRequest($id): JsonResponse
    {
        $request = TawselaRequest::with('ride')->findOrFail($id);

        // Check if user owns the ride
        if ($request->ride->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك برفض هذا الطلب'
            ], 403);
        }

        $request->reject();

        return response()->json([
            'success' => true,
            'message' => 'تم رفض الطلب'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tawsela/requests/{id}/cancel",
     *     summary="Cancel a ride request",
     *     tags={"Tawsela"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Request cancelled successfully"
     *     )
     * )
     */
    public function cancelRequest($id): JsonResponse
    {
        $request = TawselaRequest::findOrFail($id);

        // Check if user owns the request
        if ($request->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بإلغاء هذا الطلب'
            ], 403);
        }

        $request->cancel();

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الطلب'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tawsela/messages",
     *     summary="Get user's messages",
     *     tags={"Tawsela"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="ride_id",
     *         in="query",
     *         description="Filter by ride",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filter by conversation with user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Messages retrieved successfully"
     *     )
     * )
     */
    public function messages(Request $request): JsonResponse
    {
        $query = TawselaMessage::query()
            ->with(['sender:id,name,avatar', 'receiver:id,name,avatar', 'ride'])
            ->forUser(Auth::id());

        if ($request->filled('ride_id')) {
            $query->where('ride_id', $request->ride_id);
        }

        if ($request->filled('user_id')) {
            $query->where(function($q) use ($request) {
                $q->where(function($subq) use ($request) {
                    $subq->where('sender_id', Auth::id())
                         ->where('receiver_id', $request->user_id);
                })->orWhere(function($subq) use ($request) {
                    $subq->where('sender_id', $request->user_id)
                         ->where('receiver_id', Auth::id());
                });
            });
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(50);

        // Mark messages as read
        TawselaMessage::where('receiver_id', Auth::id())
            ->unread()
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tawsela/messages",
     *     summary="Send a message",
     *     tags={"Tawsela"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="Message sent successfully"
     *     )
     * )
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:tawsela_rides,id',
            'receiver_id' => 'required|exists:users,id',
            'request_id' => 'nullable|exists:tawsela_requests,id',
            'message' => 'required|string|max:1000',
        ], [
            'ride_id.required' => 'يجب تحديد الرحلة',
            'receiver_id.required' => 'يجب تحديد المستقبل',
            'message.required' => 'يجب إدخال الرسالة',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user is sender or receiver ID matches auth user
        if ($request->receiver_id == Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك إرسال رسالة لنفسك'
            ], 422);
        }

        $message = TawselaMessage::create([
            'ride_id' => $request->ride_id,
            'request_id' => $request->request_id,
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        $message->load(['sender:id,name,avatar', 'receiver:id,name,avatar']);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الرسالة بنجاح',
            'data' => $message
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/tawsela/conversations",
     *     summary="Get user's conversations",
     *     tags={"Tawsela"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Conversations retrieved successfully"
     *     )
     * )
     */
    public function conversations(): JsonResponse
    {
        $userId = Auth::id();

        // Get unique conversations
        $conversations = TawselaMessage::query()
            ->select([
                \DB::raw('CASE 
                    WHEN sender_id = ' . $userId . ' THEN receiver_id 
                    ELSE sender_id 
                END as other_user_id'),
                'ride_id',
                \DB::raw('MAX(created_at) as last_message_at'),
                \DB::raw('MAX(id) as last_message_id')
            ])
            ->where(function($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->groupBy('other_user_id', 'ride_id')
            ->orderBy('last_message_at', 'desc')
            ->get();

        // Load relationships
        $conversations->each(function($conv) {
            $conv->other_user = \App\Models\User::select('id', 'name', 'avatar')
                ->find($conv->other_user_id);
            $conv->ride = TawselaRide::select('id', 'start_address', 'destination_address', 'departure_time')
                ->find($conv->ride_id);
            $conv->last_message = TawselaMessage::find($conv->last_message_id);
            $conv->unread_count = TawselaMessage::where('ride_id', $conv->ride_id)
                ->where('receiver_id', Auth::id())
                ->where(function($q) use ($conv) {
                    $q->where('sender_id', $conv->other_user_id);
                })
                ->unread()
                ->count();
        });

        return response()->json([
            'success' => true,
            'data' => $conversations
        ]);
    }
}
