<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserServiceResource;
use App\Http\Resources\ServiceCategoryResource;
use App\Http\Resources\ServiceReviewResource;
use App\Models\UserService;
use App\Models\ServiceCategory;
use App\Models\ServiceReview;
use App\Models\ServiceAnalytics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="User Services",
 *     description="User services management endpoints"
 * )
 */
class UserServiceApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/user-services",
     *     summary="Get user services list",
     *     tags={"User Services"},
     *     @OA\Parameter(
     *         name="city_id",
     *         in="query",
     *         description="Filter by city ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by service category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="pricing_type",
     *         in="query",
     *         description="Filter by pricing type",
     *         @OA\Schema(type="string", enum={"fixed", "hourly", "per_km", "negotiable"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User services list",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/UserService"))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = UserService::query()
            ->with(['user', 'city', 'serviceCategory'])
            ->where('is_active', true)
            ->where('is_verified', true);

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('service_category_id', $request->category_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by pricing type
        if ($request->filled('pricing_type')) {
            $query->where('pricing_type', $request->pricing_type);
        }

        // Sort by featured first, then by rating
        $services = $query->orderBy('is_featured', 'desc')
            ->orderBy('rating', 'desc')
            ->paginate(15);

        return response()->json([
            'data' => UserServiceResource::collection($services),
            'meta' => [
                'total' => $services->total(),
                'per_page' => $services->perPage(),
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/user-services/{id}",
     *     summary="Get service details",
     *     tags={"User Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Service ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service details",
     *         @OA\JsonContent(ref="#/components/schemas/UserService")
     *     )
     * )
     */
    public function show(UserService $userService): JsonResponse
    {
        $userService->load(['user', 'city', 'serviceCategory', 'reviews.reviewer']);
        
        // Record view
        $this->recordAnalytics($userService, 'view');

        return response()->json([
            'data' => new UserServiceResource($userService)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/my-services",
     *     summary="Get current user's services",
     *     tags={"User Services"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User's services",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/UserService"))
     *         )
     *     )
     * )
     */
    public function myServices(Request $request): JsonResponse
    {
        $services = UserService::where('user_id', $request->user()->id)
            ->with(['city', 'serviceCategory'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'data' => UserServiceResource::collection($services),
            'meta' => [
                'total' => $services->total(),
                'per_page' => $services->perPage(),
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/my-services",
     *     summary="Create a new service",
     *     tags={"User Services"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","description","service_category_id","city_id","pricing_type","phone"},
     *             @OA\Property(property="title", type="string", example="Professional Plumbing Services"),
     *             @OA\Property(property="description", type="string", example="Expert plumbing services for homes and offices"),
     *             @OA\Property(property="service_category_id", type="integer", example=1),
     *             @OA\Property(property="city_id", type="integer", example=1),
     *             @OA\Property(property="pricing_type", type="string", enum={"fixed", "hourly", "per_km", "negotiable"}),
     *             @OA\Property(property="price_from", type="number", format="float", example=100.00),
     *             @OA\Property(property="price_to", type="number", format="float", example=500.00),
     *             @OA\Property(property="phone", type="string", example="01234567890"),
     *             @OA\Property(property="whatsapp", type="string", example="01234567890"),
     *             @OA\Property(property="address", type="string", example="123 Main Street")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service created successfully"),
     *             @OA\Property(property="service", ref="#/components/schemas/UserService")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'service_category_id' => ['required', 'exists:service_categories,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'pricing_type' => ['required', 'in:fixed,hourly,per_km,negotiable'],
            'price_from' => ['nullable', 'numeric', 'min:0'],
            'price_to' => ['nullable', 'numeric', 'min:0'],
            'phone' => ['required', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'location' => ['nullable', 'string'],
            'availability' => ['nullable', 'array'],
            'service_areas' => ['nullable', 'array'],
            'requirements' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->except('images');
        $data['user_id'] = $request->user()->id;

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('user-services', 'public');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }

        $service = UserService::create($data);
        $service->load(['user', 'city', 'serviceCategory']);

        return response()->json([
            'message' => 'Service created successfully',
            'service' => new UserServiceResource($service)
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/my-services/{id}",
     *     summary="Update a service",
     *     tags={"User Services"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Service ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service updated successfully"
     *     )
     * )
     */
    public function update(Request $request, UserService $userService): JsonResponse
    {
        // Check if user owns the service
        if ($userService->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'service_category_id' => ['sometimes', 'exists:service_categories,id'],
            'city_id' => ['sometimes', 'exists:cities,id'],
            'pricing_type' => ['sometimes', 'in:fixed,hourly,per_km,negotiable'],
            'price_from' => ['nullable', 'numeric', 'min:0'],
            'price_to' => ['nullable', 'numeric', 'min:0'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $userService->update($request->only([
            'title', 'description', 'service_category_id', 'city_id',
            'pricing_type', 'price_from', 'price_to', 'phone',
            'whatsapp', 'address', 'is_active'
        ]));

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => new UserServiceResource($userService->load(['user', 'city', 'serviceCategory']))
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/my-services/{id}",
     *     summary="Delete a service",
     *     tags={"User Services"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Service ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service deleted successfully"
     *     )
     * )
     */
    public function destroy(Request $request, UserService $userService): JsonResponse
    {
        // Check if user owns the service
        if ($userService->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete associated images
        if ($userService->images) {
            foreach ($userService->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $userService->delete();

        return response()->json([
            'message' => 'Service deleted successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/user-services/{id}/contact",
     *     summary="Record contact interaction",
     *     tags={"User Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Service ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type"},
     *             @OA\Property(property="type", type="string", enum={"phone", "whatsapp"}, example="phone")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact recorded successfully"
     *     )
     * )
     */
    public function recordContact(Request $request, UserService $userService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:phone,whatsapp'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $this->recordAnalytics($userService, 'contact', $request->type);
        
        $userService->increment('total_contacts');

        return response()->json([
            'message' => 'Contact recorded successfully'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/service-categories",
     *     summary="Get service categories",
     *     tags={"User Services"},
     *     @OA\Response(
     *         response=200,
     *         description="Service categories list",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ServiceCategory"))
     *         )
     *     )
     * )
     */
    public function categories(): JsonResponse
    {
        $categories = ServiceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => ServiceCategoryResource::collection($categories)
        ]);
    }

    /**
     * Record analytics data
     */
    private function recordAnalytics(UserService $service, string $type, string $value = null): void
    {
        ServiceAnalytics::create([
            'user_service_id' => $service->id,
            'metric_type' => $type,
            'metric_value' => $value,
            'date' => now()->toDateString(),
            'hour' => now()->hour,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}