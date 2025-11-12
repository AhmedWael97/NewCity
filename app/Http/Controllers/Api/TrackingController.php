<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserTrackingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TrackingController extends Controller
{
    protected $tracking;

    public function __construct(UserTrackingService $tracking)
    {
        $this->tracking = $tracking;
    }

    /**
     * Track user activity
     */
    public function track(Request $request): JsonResponse
    {
        try {
            $eventType = $request->input('event_type');
            $eventCategory = $request->input('event_category');
            $eventAction = $request->input('event_action');
            $eventLabel = $request->input('event_label');
            $eventData = $request->input('event_data', []);
            $scrollDepth = $request->input('scroll_depth');
            $timeOnPage = $request->input('time_on_page');

            $this->tracking->track($eventType, [
                'event_category' => $eventCategory,
                'event_action' => $eventAction,
                'event_label' => $eventLabel,
                'event_data' => $eventData,
                'scroll_depth' => $scrollDepth,
                'time_on_page' => $timeOnPage,
            ]);

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            // Return success even on error to not disrupt user experience
            return response()->json(['success' => true], 200);
        }
    }
}
