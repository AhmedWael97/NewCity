<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceSponsorship;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Marketplace Sponsorships",
 *     description="Marketplace item sponsorship management endpoints"
 * )
 */
class MarketplaceSponsorshipController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/marketplace/sponsorship-packages",
     *     summary="Get available sponsorship packages",
     *     tags={"Marketplace Sponsorships"},
     *     @OA\Response(
     *         response=200,
     *         description="Packages retrieved successfully"
     *     )
     * )
     */
    public function packages(): JsonResponse
    {
        $packages = MarketplaceSponsorship::packages();

        return response()->json([
            'success' => true,
            'message' => 'Sponsorship packages retrieved successfully',
            'data' => $packages,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/marketplace/{itemId}/sponsor",
     *     summary="Purchase sponsorship for an item",
     *     tags={"Marketplace Sponsorships"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="itemId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"package_type", "payment_method"},
     *             @OA\Property(property="package_type", type="string", enum={"basic", "standard", "premium"}),
     *             @OA\Property(property="payment_method", type="string", example="cash")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sponsorship purchased successfully"
     *     )
     * )
     */
    public function purchase(Request $request, $itemId): JsonResponse
    {
        $item = MarketplaceItem::findOrFail($itemId);

        // Check ownership
        if (!$item->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You can only sponsor your own items',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'package_type' => 'required|in:basic,standard,premium',
            'payment_method' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $packages = MarketplaceSponsorship::packages();
        $selectedPackage = $packages[$request->package_type];

        DB::beginTransaction();
        try {
            // Create sponsorship record
            $sponsorship = MarketplaceSponsorship::create([
                'marketplace_item_id' => $item->id,
                'user_id' => Auth::id(),
                'package_type' => $request->package_type,
                'duration_days' => $selectedPackage['duration_days'],
                'price_paid' => $selectedPackage['price'],
                'views_boost' => $selectedPackage['views_boost'],
                'priority_level' => $selectedPackage['priority_level'],
                'starts_at' => now(),
                'ends_at' => now()->addDays($selectedPackage['duration_days']),
                'payment_method' => $request->payment_method,
                'payment_status' => 'completed', // In production, wait for payment gateway confirmation
                'status' => 'active',
            ]);

            // Activate sponsorship on item
            $sponsorship->activate();

            DB::commit();

            $sponsorship->load(['marketplaceItem', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Sponsorship purchased successfully',
                'data' => [
                    'sponsorship' => $sponsorship,
                    'item' => $item->fresh(),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to purchase sponsorship: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/my-marketplace-sponsorships",
     *     summary="Get current user's sponsorships",
     *     tags={"Marketplace Sponsorships"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sponsorships retrieved successfully"
     *     )
     * )
     */
    public function mySponsorships(Request $request): JsonResponse
    {
        $sponsorships = MarketplaceSponsorship::with(['marketplaceItem'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(15);

        // Add computed fields
        $sponsorships->getCollection()->transform(function ($sponsorship) {
            $sponsorship->days_remaining = $sponsorship->daysRemaining();
            $sponsorship->is_active = $sponsorship->isActive();
            $sponsorship->is_expired = $sponsorship->isExpired();
            $sponsorship->roi = $sponsorship->getRoi();
            return $sponsorship;
        });

        return response()->json([
            'success' => true,
            'message' => 'Your sponsorships retrieved successfully',
            'data' => $sponsorships,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/marketplace/{itemId}/sponsorships",
     *     summary="Get sponsorship history for an item",
     *     tags={"Marketplace Sponsorships"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sponsorships retrieved successfully"
     *     )
     * )
     */
    public function itemSponsorships($itemId): JsonResponse
    {
        $item = MarketplaceItem::findOrFail($itemId);

        // Check ownership
        if (!$item->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You can only view sponsorships for your own items',
            ], 403);
        }

        $sponsorships = MarketplaceSponsorship::where('marketplace_item_id', $itemId)
            ->orderByDesc('created_at')
            ->get();

        // Add computed fields
        $sponsorships->transform(function ($sponsorship) {
            $sponsorship->days_remaining = $sponsorship->daysRemaining();
            $sponsorship->is_active = $sponsorship->isActive();
            $sponsorship->is_expired = $sponsorship->isExpired();
            $sponsorship->roi = $sponsorship->getRoi();
            return $sponsorship;
        });

        return response()->json([
            'success' => true,
            'message' => 'Item sponsorships retrieved successfully',
            'data' => $sponsorships,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/marketplace/sponsorships/{id}",
     *     summary="Get sponsorship details",
     *     tags={"Marketplace Sponsorships"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sponsorship details retrieved successfully"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $sponsorship = MarketplaceSponsorship::with(['marketplaceItem', 'user'])
            ->findOrFail($id);

        // Check ownership
        if ($sponsorship->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this sponsorship',
            ], 403);
        }

        // Add computed fields
        $sponsorship->days_remaining = $sponsorship->daysRemaining();
        $sponsorship->is_active = $sponsorship->isActive();
        $sponsorship->is_expired = $sponsorship->isExpired();
        $sponsorship->roi = $sponsorship->getRoi();

        return response()->json([
            'success' => true,
            'message' => 'Sponsorship details retrieved successfully',
            'data' => $sponsorship,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/marketplace/sponsorships/{id}/renew",
     *     summary="Renew an expired sponsorship",
     *     tags={"Marketplace Sponsorships"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=201,
     *         description="Sponsorship renewed successfully"
     *     )
     * )
     */
    public function renew(Request $request, $id): JsonResponse
    {
        $oldSponsorship = MarketplaceSponsorship::findOrFail($id);

        // Check ownership
        if ($oldSponsorship->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to renew this sponsorship',
            ], 403);
        }

        $packages = MarketplaceSponsorship::packages();
        $package = $packages[$oldSponsorship->package_type];

        DB::beginTransaction();
        try {
            // Create new sponsorship with same package
            $newSponsorship = MarketplaceSponsorship::create([
                'marketplace_item_id' => $oldSponsorship->marketplace_item_id,
                'user_id' => Auth::id(),
                'package_type' => $oldSponsorship->package_type,
                'duration_days' => $package['duration_days'],
                'price_paid' => $package['price'],
                'views_boost' => $package['views_boost'],
                'priority_level' => $package['priority_level'],
                'starts_at' => now(),
                'ends_at' => now()->addDays($package['duration_days']),
                'payment_method' => $oldSponsorship->payment_method,
                'payment_status' => 'completed',
                'status' => 'active',
            ]);

            // Activate new sponsorship
            $newSponsorship->activate();

            DB::commit();

            $newSponsorship->load(['marketplaceItem', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Sponsorship renewed successfully',
                'data' => $newSponsorship,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to renew sponsorship: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/marketplace/sponsorships/{id}/cancel",
     *     summary="Cancel an active sponsorship",
     *     tags={"Marketplace Sponsorships"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="reason", type="string", example="Changed my mind")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sponsorship cancelled successfully"
     *     )
     * )
     */
    public function cancel(Request $request, $id): JsonResponse
    {
        $sponsorship = MarketplaceSponsorship::findOrFail($id);

        // Check ownership
        if ($sponsorship->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to cancel this sponsorship',
            ], 403);
        }

        if (!$sponsorship->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'This sponsorship is not active and cannot be cancelled',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $sponsorship->cancel($request->reason);

        return response()->json([
            'success' => true,
            'message' => 'Sponsorship cancelled successfully',
            'data' => $sponsorship,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/marketplace/sponsorships/stats",
     *     summary="Get sponsorship statistics for current user",
     *     tags={"Marketplace Sponsorships"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistics retrieved successfully"
     *     )
     * )
     */
    public function stats(): JsonResponse
    {
        $userId = Auth::id();

        $stats = [
            'total_sponsorships' => MarketplaceSponsorship::where('user_id', $userId)->count(),
            'active_sponsorships' => MarketplaceSponsorship::where('user_id', $userId)->active()->count(),
            'total_spent' => MarketplaceSponsorship::where('user_id', $userId)
                ->where('payment_status', 'completed')
                ->sum('price_paid'),
            'total_views_gained' => MarketplaceSponsorship::where('user_id', $userId)->sum('views_gained'),
            'total_contacts_gained' => MarketplaceSponsorship::where('user_id', $userId)->sum('contacts_gained'),
            'avg_roi' => 0,
        ];

        // Calculate average ROI
        $sponsorships = MarketplaceSponsorship::where('user_id', $userId)
            ->where('payment_status', 'completed')
            ->get();

        if ($sponsorships->count() > 0) {
            $totalRoi = $sponsorships->sum(function ($s) {
                return $s->getRoi();
            });
            $stats['avg_roi'] = round($totalRoi / $sponsorships->count(), 2);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sponsorship statistics retrieved successfully',
            'data' => $stats,
        ]);
    }
}
