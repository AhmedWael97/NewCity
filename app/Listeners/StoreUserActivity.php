<?php

namespace App\Listeners;

use App\Events\UserActivityTracked;
use App\Models\UserEvent;
use Illuminate\Support\Facades\Log;

class StoreUserActivity
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserActivityTracked $event): void
    {
        try {
            UserEvent::create($event->eventData);
        } catch (\Exception $e) {
            // Log error but don't throw to prevent disrupting user experience
            Log::error('Failed to store user event: ' . $e->getMessage());
        }
    }

}
