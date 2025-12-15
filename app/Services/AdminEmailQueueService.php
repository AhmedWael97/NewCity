<?php

namespace App\Services;

use App\Models\AdminEmailQueue;

class AdminEmailQueueService
{
    /**
     * Add email to queue for admin notification
     */
    public static function queueEmail(string $eventType, string $subject, string $body, array $eventData = [], string $source = null)
    {
        // Get notification emails from SMTP settings
        $smtpSettings = \App\Models\SmtpSettings::getActive();
        
        if (!$smtpSettings || empty($smtpSettings->notification_emails)) {
            return false;
        }

        $recipients = $smtpSettings->notification_emails;

        if (empty($recipients)) {
            return false;
        }

        // Auto-detect source if not provided
        if ($source === null) {
            $source = request()->is('api/*') ? 'api' : 'web';
        }

        // Create email queue entry
        AdminEmailQueue::create([
            'event_type' => $eventType,
            'source' => $source,
            'subject' => $subject,
            'body' => $body,
            'recipients' => $recipients,
            'event_data' => $eventData,
            'status' => AdminEmailQueue::STATUS_PENDING,
        ]);

        return true;
    }

    /**
     * Queue email for new shop suggestion
     */
    public static function queueShopSuggestion($suggestion)
    {
        $subject = 'New Shop Suggestion Received';
        $body = "A new shop suggestion has been submitted:\n\n" .
                "Shop Name: {$suggestion->shop_name}\n" .
                "Address: {$suggestion->address}\n" .
                "City: {$suggestion->city->name}\n" .
                "Submitted at: " . $suggestion->created_at->format('Y-m-d H:i:s');

        return self::queueEmail(
            AdminEmailQueue::EVENT_SHOP_SUGGESTION,
            $subject,
            $body,
            ['suggestion_id' => $suggestion->id]
        );
    }

    /**
     * Queue email for new city suggestion
     */
    public static function queueCitySuggestion($suggestion)
    {
        $subject = 'New City Suggestion Received';
        $body = "A new city suggestion has been submitted:\n\n" .
                "City Name (Arabic): {$suggestion->name_ar}\n" .
                "City Name (English): {$suggestion->name_en}\n" .
                "Governorate: {$suggestion->governorate}\n" .
                "Submitted at: " . $suggestion->created_at->format('Y-m-d H:i:s');

        return self::queueEmail(
            AdminEmailQueue::EVENT_CITY_SUGGESTION,
            $subject,
            $body,
            ['suggestion_id' => $suggestion->id]
        );
    }

    /**
     * Queue email for new shop rating
     */
    public static function queueShopRating($rating)
    {
        $subject = 'New Shop Rating Received';
        $body = "A new shop rating has been submitted:\n\n" .
                "Shop: {$rating->shop->name}\n" .
                "Rating: {$rating->rating}/5\n" .
                "Comment: {$rating->comment}\n" .
                "Submitted at: " . $rating->created_at->format('Y-m-d H:i:s');

        return self::queueEmail(
            AdminEmailQueue::EVENT_SHOP_RATE,
            $subject,
            $body,
            ['rating_id' => $rating->id, 'shop_id' => $rating->shop_id]
        );
    }

    /**
     * Queue email for new service rating
     */
    public static function queueServiceRating($review)
    {
        $subject = 'New Service Rating Received';
        $body = "A new service rating has been submitted:\n\n" .
                "Service: {$review->service->title}\n" .
                "Rating: {$review->rating}/5\n" .
                "Comment: {$review->comment}\n" .
                "Submitted at: " . $review->created_at->format('Y-m-d H:i:s');

        return self::queueEmail(
            AdminEmailQueue::EVENT_SERVICE_RATE,
            $subject,
            $body,
            ['review_id' => $review->id, 'service_id' => $review->service_id]
        );
    }

    /**
     * Queue email for new service
     */
    public static function queueNewService($service)
    {
        $subject = 'New Service Created';
        $body = "A new service has been created:\n\n" .
                "Service: {$service->title}\n" .
                "Category: {$service->category->name}\n" .
                "Provider: {$service->user->name}\n" .
                "Created at: " . $service->created_at->format('Y-m-d H:i:s');

        return self::queueEmail(
            AdminEmailQueue::EVENT_NEW_SERVICE,
            $subject,
            $body,
            ['service_id' => $service->id]
        );
    }

    /**
     * Queue email for new marketplace item
     */
    public static function queueNewMarketplaceItem($item)
    {
        $subject = 'New Marketplace Item Listed';
        $body = "A new marketplace item has been listed:\n\n" .
                "Item: {$item->title}\n" .
                "Price: {$item->price} EGP\n" .
                "Seller: {$item->user->name}\n" .
                "City: {$item->city->name}\n" .
                "Listed at: " . $item->created_at->format('Y-m-d H:i:s');

        return self::queueEmail(
            AdminEmailQueue::EVENT_NEW_MARKETPLACE,
            $subject,
            $body,
            ['item_id' => $item->id]
        );
    }

    /**
     * Queue email for new user registration
     */
    public static function queueNewUser($user)
    {
        $subject = 'New User Registered';
        $body = "A new user has registered:\n\n" .
                "Name: {$user->name}\n" .
                "Email: {$user->email}\n" .
                "Phone: {$user->phone}\n" .
                "Registered at: " . $user->created_at->format('Y-m-d H:i:s');

        return self::queueEmail(
            AdminEmailQueue::EVENT_NEW_USER,
            $subject,
            $body,
            ['user_id' => $user->id]
        );
    }

    /**
     * Get default preferences for new admins
     */
    private static function getDefaultPreferences()
    {
        return [
            'shop_suggestion' => true,
            'city_suggestion' => true,
            'shop_rate' => true,
            'service_rate' => true,
            'new_service' => true,
            'new_marketplace' => true,
            'new_user' => true,
        ];
    }
}
