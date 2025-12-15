<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceReview;
use App\Models\UserService;
use App\Services\AdminEmailQueueService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceReviewController extends Controller
{
    /**
     * Display reviews for a service
     */
    public function index(UserService $service)
    {
        $reviews = $service->reviews()
            ->with('reviewer')
            ->approved()
            ->latest()
            ->paginate(10);

        return view('services.reviews.index', compact('service', 'reviews'));
    }

    /**
     * Show the form for creating a new review
     */
    public function create(UserService $service)
    {
        // Check if user already reviewed this service
        $existingReview = ServiceReview::where('user_service_id', $service->id)
            ->where('reviewer_id', Auth::id())
            ->first();

        if ($existingReview) {
            return back()->with('error', 'لقد قمت بتقييم هذه الخدمة من قبل');
        }

        return view('services.reviews.create', compact('service'));
    }

    /**
     * Store a newly created review
     */
    public function store(Request $request, UserService $service)
    {
        // Check if user already reviewed this service
        $existingReview = ServiceReview::where('user_service_id', $service->id)
            ->where('reviewer_id', Auth::id())
            ->first();

        if ($existingReview) {
            return back()->with('error', 'لقد قمت بتقييم هذه الخدمة من قبل');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'service_date' => 'nullable|date',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('service-reviews', 'public');
                $imagePaths[] = $path;
            }
        }

        $review = ServiceReview::create([
            'user_service_id' => $service->id,
            'reviewer_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'service_date' => $request->service_date,
            'images' => $imagePaths,
            'is_approved' => true, // Auto-approve for now
            'is_verified' => false,
        ]);

        // Queue email notification to admins
        AdminEmailQueueService::queueServiceRating($review->load('service'));

        // Update service rating
        $this->updateServiceRating($service);

        return redirect()->route('services.show', $service)
            ->with('success', 'تم إضافة تقييمك بنجاح');
    }

    /**
     * Show the form for editing a review
     */
    public function edit(ServiceReview $review)
    {
        // Check if user owns this review
        if ($review->reviewer_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا التقييم');
        }

        return view('services.reviews.edit', compact('review'));
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, ServiceReview $review)
    {
        // Check if user owns this review
        if ($review->reviewer_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا التقييم');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'service_date' => 'nullable|date',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle image uploads
        $imagePaths = $review->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('service-reviews', 'public');
                $imagePaths[] = $path;
            }
        }

        // Remove deleted images
        if ($request->has('deleted_images')) {
            $deletedImages = json_decode($request->deleted_images, true) ?? [];
            foreach ($deletedImages as $deletedImage) {
                Storage::disk('public')->delete($deletedImage);
                $imagePaths = array_filter($imagePaths, function($path) use ($deletedImage) {
                    return $path !== $deletedImage;
                });
            }
        }

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'service_date' => $request->service_date,
            'images' => array_values($imagePaths),
        ]);

        // Update service rating
        $this->updateServiceRating($review->userService);

        return redirect()->route('services.show', $review->userService)
            ->with('success', 'تم تحديث تقييمك بنجاح');
    }

    /**
     * Remove the specified review
     */
    public function destroy(ServiceReview $review)
    {
        // Check if user owns this review
        if ($review->reviewer_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بحذف هذا التقييم');
        }

        $service = $review->userService;

        // Delete images
        if ($review->images) {
            foreach ($review->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $review->delete();

        // Update service rating
        $this->updateServiceRating($service);

        return back()->with('success', 'تم حذف التقييم بنجاح');
    }

    /**
     * Update service rating based on reviews
     */
    private function updateServiceRating(UserService $service)
    {
        $avgRating = $service->reviews()->approved()->avg('rating') ?? 0;
        $totalReviews = $service->reviews()->approved()->count();

        $service->update([
            'rating' => round($avgRating, 2),
            'total_reviews' => $totalReviews,
        ]);
    }
}
